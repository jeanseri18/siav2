<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Devis;
use App\Models\Article;
use App\Models\ClientFournisseur;
use App\Http\Controllers\Concerns\ExportsListPdf;
use App\Support\PdfBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    use ExportsListPdf;

    public function index()
    {
        $ventes = Vente::with('client', 'articles', 'devis')->get();
        return view('ventes.index', compact('ventes'));
    }

    public function exportListePdf()
    {
        $ventes = Vente::with('client', 'articles')->orderByDesc('created_at')->get();

        $rows = [];
        foreach ($ventes as $vente) {
            $articles = $vente->articles->map(fn ($a) => $a->nom.' ('.$a->pivot->quantite.')')->implode(', ');
            $rows[] = [
                '#'.str_pad((string) $vente->id, 4, '0', STR_PAD_LEFT),
                $vente->numero_client ?? '—',
                $vente->nom_client ?? $vente->client?->nom_raison_sociale ?? '—',
                $vente->created_at?->format('d/m/Y H:i') ?? '—',
                $articles ?: '—',
                number_format((float) ($vente->total_ht ?? 0), 0, ',', ' ').' FCFA',
                number_format((float) ($vente->tva ?? 0), 0, ',', ' ').' FCFA',
                number_format((float) ($vente->total_ttc ?? $vente->total ?? 0), 0, ',', ' ').' FCFA',
                $vente->statut ?? '—',
            ];
        }

        return $this->streamListPdf(
            'Liste des ventes',
            ['ID', 'N° client', 'Nom client', 'Date', 'Articles', 'Total HT', 'TVA', 'Total TTC', 'Statut'],
            $rows,
            'liste-ventes'
        );
    }

    public function create()
    {
        $clients = ClientFournisseur::where('type', 'Client')->get();
        $articles = Article::with('uniteMesure')->get();
        $devis = Devis::nonUtilises()->with('client', 'articles')->get();
        return view('ventes.create', compact('clients', 'articles', 'devis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:client_fournisseurs,id',
            'numero_client' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'commentaire' => 'nullable|string',
            'devis_id' => 'nullable|exists:devis,id',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'prestations' => 'nullable|array',
            'prestations.*.nom' => 'required_with:prestations|string|max:255',
            'prestations.*.quantite' => 'required_with:prestations|integer|min:1',
            'prestations.*.montant' => 'required_with:prestations|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $totalHT = 0;
            $articles = [];

            // Si un devis est sélectionné, utiliser ses articles
            if ($request->devis_id) {
                $devis = Devis::with('articles')->findOrFail($request->devis_id);
                
                // Marquer le devis comme utilisé
                $devis->update(['utilise_pour_vente' => true]);
                
                foreach ($devis->articles as $article) {
                    $puHt = (float) $article->pivot->prix_unitaire_ht;
                    $montantLigne = (float) $article->pivot->montant_total;
                    $articles[] = [
                        'id' => $article->id,
                        'quantite' => $article->pivot->quantite,
                        'prix_unitaire' => $puHt,
                        'sous_total' => $montantLigne,
                    ];

                    $totalHT += $montantLigne;
                }
            } else {
                // Utiliser les articles sélectionnés manuellement
                foreach ($request->articles as $articleData) {
                    $article = Article::findOrFail($articleData['id']);

                    $prixUnitaireHT = $article->cout_moyen_pondere;
                    $montantTotal = $prixUnitaireHT * $articleData['quantite'];

                    $articles[] = [
                        'id' => $article->id,
                        'quantite' => $articleData['quantite'],
                        'prix_unitaire' => $prixUnitaireHT,
                        'sous_total' => $montantTotal
                    ];

                    $totalHT += $montantTotal;
                }
            }

            // Calculer le total des prestations
            $totalPrestations = 0;
            if ($request->has('prestations') && !empty($request->prestations)) {
                foreach ($request->prestations as $prestation) {
                    $totalPrestations += $prestation['quantite'] * $prestation['montant'];
                }
            }

            $totalHT = $totalHT + $totalPrestations;
            $tva = $totalHT * 0.18;
            $totalTTC = $totalHT + $tva;

            $vente = Vente::create([
                'client_id' => $request->client_id,
                'devis_id' => $request->devis_id,
                'numero_client' => $request->numero_client,
                'nom_client' => $request->nom_client,
                'commentaire' => $request->commentaire,
                'total' => $totalTTC,
                'total_ht' => $totalHT,
                'tva' => $tva,
                'total_ttc' => $totalTTC,
                'statut' => 'En attente'
            ]);

            // Attacher les articles à la vente et décrémenter le stock
            foreach ($articles as $articleData) {
                $vente->articles()->attach($articleData['id'], [
                    'quantite' => $articleData['quantite'],
                    'prix_unitaire' => $articleData['prix_unitaire'],
                    'sous_total' => $articleData['sous_total']
                ]);

                $article = Article::findOrFail($articleData['id']);
                $article->decrement('quantite_stock', $articleData['quantite']);
            }

            // Enregistrer les prestations
            if ($request->has('prestations') && !empty($request->prestations)) {
                foreach ($request->prestations as $prestation) {
                    $vente->prestations()->create([
                        'nom_prestation' => $prestation['nom'],
                        'quantite' => $prestation['quantite'],
                        'prix_unitaire' => $prestation['montant'],
                        'montant_total' => $prestation['quantite'] * $prestation['montant']
                    ]);
                }
            }
        });

     return redirect()->route('ventes.index')->with('success', 'Vente enregistrée avec succès.');
    }

    public function getDevisForClient($clientId)
    {
        $devis = Devis::where('client_id', $clientId)
                     ->where('utilise_pour_vente', false)
                     ->with('articles')
                     ->get();
        
        return response()->json($devis);
    }

    public function show(Vente $vente)
    {
        $vente->load('client', 'articles', 'devis', 'prestations');
        return view('ventes.show', compact('vente'));
    }

    public function facture(Vente $vente)
    {
        $vente->load(['client', 'articles.uniteMesure', 'devis', 'prestations']);

        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $pdfBranding = PdfBranding::forBu($buId);

        $pdf = Pdf::loadView('ventes.facture-proforma-pdf', [
            'vente' => $vente,
            'pdfBranding' => $pdfBranding,
            'configGlobal' => $pdfBranding['config'],
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        $numero = 'FP' . $vente->created_at->format('mY') . '-' . str_pad((string) $vente->id, 5, '0', STR_PAD_LEFT);

        return $pdf->stream('Facture_proforma_' . $numero . '.pdf');
    }

    /**
     * PDF — Bon de livraison client (vente)
     */
    public function exportBonLivraisonClientPdf(Vente $vente)
    {
        $vente->load(['client', 'articles.uniteMesure', 'devis', 'prestations']);

        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $pdfBranding = PdfBranding::forBu($buId);
        $bl = $this->buildBonLivraisonClientData($vente, $pdfBranding);

        $pdf = Pdf::loadView('ventes.bon-livraison-client-pdf', [
            'vente' => $vente,
            'pdfBranding' => $pdfBranding,
            'configGlobal' => $pdfBranding['config'],
            'bl' => $bl,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('Bon_livraison_client_' . $bl['numero'] . '.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBonLivraisonClientData(Vente $vente, array $pdfBranding): array
    {
        $client = $vente->client;
        $company = $pdfBranding['company'] ?? [];

        $numero = 'BLC' . str_pad((string) $vente->id, 6, '0', STR_PAD_LEFT);
        $refVente = 'VTE-' . str_pad((string) $vente->id, 5, '0', STR_PAD_LEFT);
        $refDevis = $vente->devis?->ref_devis
            ?? ($vente->devis_id ? 'DEV-' . $vente->devis_id : '');

        $lignes = [];
        $index = 1;

        foreach ($vente->articles as $article) {
            $qty = (float) ($article->pivot->quantite ?? 0);
            $lignes[] = [
                'numero_ligne' => str_pad((string) $index++, 3, '0', STR_PAD_LEFT),
                'ref_produit' => $article->reference ?? $article->reference_fournisseur ?? '—',
                'designation' => $article->nom ?? '—',
                'unite' => $article->uniteMesure?->ref
                    ?? $article->uniteMesure?->nom
                    ?? '—',
                'quantite_commandee' => $qty,
                'quantite_livree' => $qty,
            ];
        }

        foreach ($vente->prestations as $prestation) {
            $qty = (float) ($prestation->quantite ?? 0);
            $lignes[] = [
                'numero_ligne' => str_pad((string) $index++, 3, '0', STR_PAD_LEFT),
                'ref_produit' => 'PREST',
                'designation' => $prestation->nom_prestation ?? '—',
                'unite' => 'F',
                'quantite_commandee' => $qty,
                'quantite_livree' => $qty,
            ];
        }

        $livreA = $client?->nom_raison_sociale
            ?? $vente->nom_client
            ?? '—';

        $adresseRue = filled($client?->adresse_localisation)
            ? $client->adresse_localisation
            : null;

        return [
            'numero' => $numero,
            'date' => $vente->created_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'ref_vente' => $refVente,
            'ref_devis' => $refDevis,
            'emis_par' => $company['nom'] ?? ($pdfBranding['nom_entreprise'] ?? '—'),
            'livre_a' => $livreA,
            'adresse_rue' => $adresseRue,
            'adresse_bp' => filled($client?->boite_postale) ? $client->boite_postale : null,
            'telephone' => $client?->telephone ?? '',
            'email' => $client?->email ?? '',
            'rccm' => $client?->n_rccm ?? '',
            'cc' => $client?->n_cc ?? '',
            'lieu_livraison' => $adresseRue ?: '—',
            'lignes' => $lignes,
        ];
    }

    public function destroy(Vente $vente)
    {
        $vente->delete();
        return redirect()->route('ventes.index')->with('success', 'Vente supprimée.');
    }
    public function updateStatus($venteId)
    {
        // Trouver la vente par son ID
        $vente = Vente::findOrFail($venteId);

        // Vérifier si la vente n'est pas déjà validée
        if ($vente->statut !== 'Payée') {
            // Mettre à jour le statut
            $vente->statut = 'Payée';
            $vente->save();

            // Rediriger avec succès
            return redirect()->route('ventes.show', $venteId)->with('success', 'Vente validée avec succès');
        }

        // Rediriger si déjà validée
        return redirect()->route('ventes.show', $venteId)->with('error', 'La vente a déjà été validée');
    }

    public function showReportForm()
    {
        $clients = ClientFournisseur::where('type', 'Client')->get();
        $articles = Article::all();

        return view('ventes.report', compact('clients', 'articles'));
    }

    /**
     * Générer un rapport en fonction des critères sélectionnés.
     */
    public function generateReport(Request $request)
    {
        // Récupérer les filtres depuis le formulaire
        $query = Vente::query();

        if ($request->has('client_id') && $request->client_id != '') {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('article_id') && $request->article_id != '') {
            $query->whereHas('articles', function($q) use ($request) {
                $q->where('article_id', $request->article_id);
            });
        }

        if ($request->has('date_debut') && $request->has('date_fin')) {
            $query->whereBetween('created_at', [$request->date_debut, $request->date_fin]);
        }

        // Obtenir les résultats du rapport
        $ventes = $query->get();

        // Afficher les résultats dans la vue du rapport
        return view('ventes.report_result', compact('ventes'));
    }

    public function generatePDF(Request $request)
    {
        $orientation = config('dompdf.orientation');
        $paperSize = config('dompdf.paper_size');
        $ventes = Vente::all();  // Exemple pour récupérer toutes les ventes, ou filtrer selon les besoins

        // Charger la vue qui contient le tableau des ventes
        $pdf = Pdf::loadView('ventes.report_pdf', compact('ventes'))->setPaper($paperSize, $orientation);

        // Retourner le PDF au navigateur
        return $pdf->download('rapport_ventes.pdf');
    }
}
