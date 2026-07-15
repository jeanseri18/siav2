<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Article;
use App\Models\ClientFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Concerns\ExportsListPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Support\PdfBranding;

class DevisController extends Controller
{
    use ExportsListPdf;

    public function index()
    {
        $devis = Devis::with('client', 'articles', 'user')->get();
        return view('devis.index', compact('devis'));
    }

    public function exportListePdf()
    {
        $devis = Devis::with('client', 'user')->orderByDesc('created_at')->get();

        $rows = [];
        foreach ($devis as $item) {
            $rows[] = [
                $item->ref_devis ?? '#'.$item->id,
                $item->nom_client ?? $item->client?->nom_raison_sociale ?? '—',
                $item->created_at?->format('d/m/Y') ?? '—',
                $item->user?->nom ?? '—',
                number_format((float) ($item->total_ht ?? 0), 0, ',', ' ').' FCFA',
                number_format((float) ($item->tva ?? 0), 0, ',', ' ').' FCFA',
                number_format((float) ($item->total_ttc ?? 0), 0, ',', ' ').' FCFA',
                $item->statut ?? '—',
            ];
        }

        return $this->streamListPdf(
            'Liste des devis',
            ['Référence', 'Client', 'Date', 'Créé par', 'Total HT', 'TVA', 'Total TTC', 'Statut'],
            $rows,
            'liste-devis'
        );
    }

    public function create()
    {
        $clients = ClientFournisseur::where('type', 'Client')->get();
        $articles = Article::with('uniteMesure')->get();
        return view('devis.create', compact('clients', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:client_fournisseurs,id',
            'numero_client' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'commentaire' => 'nullable|string',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
            'articles.*.remise' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $devis = Devis::create([
                'client_id' => $request->client_id,
                'numero_client' => $request->numero_client,
                'nom_client' => $request->nom_client,
                'commentaire' => $request->commentaire,
                'total_ht' => 0,
                'tva' => 0,
                'total_ttc' => 0,
                'statut' => 'En attente',
                'utilise_pour_vente' => false,
                'user_id' => auth()->id(),
                'ref_devis' => Devis::generateRefDevis()
            ]);

            $totalHT = 0;

            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['id']);
                
                // Utiliser le prix unitaire saisi par l'utilisateur
                $prixUnitaireHT = $articleData['prix_unitaire'];
                $remise = $articleData['remise'] ?? 0;
                $montantBrut = $prixUnitaireHT * $articleData['quantite'];
                $montantRemise = $montantBrut * ($remise / 100);
                $montantTotal = $montantBrut - $montantRemise;

                $devis->articles()->attach($article->id, [
                    'quantite' => $articleData['quantite'],
                    'prix_unitaire_ht' => $prixUnitaireHT,
                    'remise' => $remise,
                    'montant_total' => $montantTotal
                ]);

                $totalHT += $montantTotal;
            }

            $tva = $totalHT * 0.18;
            $totalTTC = $totalHT + $tva;

            $devis->update([
                'total_ht' => $totalHT,
                'tva' => $tva,
                'total_ttc' => $totalTTC
            ]);
        });

        return redirect()->route('devis.index')->with('success', 'Devis créé avec succès.');
    }

    public function show(Devis $devi)
    {
        $devi->load('user');
        return view('devis.show', compact('devi'));
    }

    public function edit(Devis $devi)
    {
        if ($devi->utilise_pour_vente) {
            return redirect()->route('devis.index')
                ->with('error', 'Ce devis a déjà été utilisé pour une vente et ne peut plus être modifié.');
        }

        $clients = ClientFournisseur::where('type', 'Client')->get();
        $articles = Article::with('uniteMesure')->get();
        return view('devis.edit', compact('devi', 'clients', 'articles'));
    }

    public function update(Request $request, Devis $devi)
    {
        if ($devi->utilise_pour_vente) {
            return redirect()->route('devis.index')
                ->with('error', 'Ce devis a déjà été utilisé pour une vente et ne peut plus être modifié.');
        }

        $request->validate([
            'client_id' => 'required|exists:client_fournisseurs,id',
            'numero_client' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'commentaire' => 'nullable|string',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
            'articles.*.prix_unitaire' => 'required|numeric|min:0',
            'articles.*.remise' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $devi) {
            // Supprimer les anciens articles
            $devi->articles()->detach();

            $totalHT = 0;

            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['id']);
                
                // Utiliser le prix unitaire saisi par l'utilisateur
                $prixUnitaireHT = $articleData['prix_unitaire'];
                $remise = $articleData['remise'] ?? 0;
                $montantBrut = $prixUnitaireHT * $articleData['quantite'];
                $montantRemise = $montantBrut * ($remise / 100);
                $montantTotal = $montantBrut - $montantRemise;

                $devi->articles()->attach($article->id, [
                    'quantite' => $articleData['quantite'],
                    'prix_unitaire_ht' => $prixUnitaireHT,
                    'remise' => $remise,
                    'montant_total' => $montantTotal
                ]);

                $totalHT += $montantTotal;
            }

            $tva = $totalHT * 0.18;
            $totalTTC = $totalHT + $tva;

            $devi->update([
                'client_id' => $request->client_id,
                'numero_client' => $request->numero_client,
                'nom_client' => $request->nom_client,
                'commentaire' => $request->commentaire,
                'total_ht' => $totalHT,
                'tva' => $tva,
                'total_ttc' => $totalTTC
            ]);
        });

        return redirect()->route('devis.index')->with('success', 'Devis mis à jour avec succès.');
    }

    public function destroy(Devis $devi)
    {
        if ($devi->utilise_pour_vente) {
            return redirect()->route('devis.index')
                ->with('error', 'Ce devis a déjà été utilisé pour une vente et ne peut plus être supprimé.');
        }

        $devi->delete();
        return redirect()->route('devis.index')->with('success', 'Devis supprimé avec succès.');
    }

    /**
     * Récupérer les devis non utilisés pour un client
     */
    public function getDevisForClient($clientId)
    {
        $devis = Devis::where('client_id', $clientId)
            ->where('utilise_pour_vente', false)
            ->with('articles')
            ->get();

        return response()->json($devis);
    }

    /**
     * Approuver un devis
     */
    public function approve($id)
    {
        $devi = Devis::findOrFail($id);
        
        if ($devi->utilise_pour_vente) {
            return redirect()->route('devis.index')
                ->with('error', 'Ce devis a déjà été utilisé pour une vente et ne peut plus être modifié.');
        }

        $devi->update(['statut' => 'Approuvé']);
        
        return redirect()->route('devis.index')
            ->with('success', 'Devis approuvé avec succès.');
    }

    /**
     * Rejeter un devis
     */
    public function reject($id)
    {
        $devi = Devis::findOrFail($id);
        
        if ($devi->utilise_pour_vente) {
            return redirect()->route('devis.index')
                ->with('error', 'Ce devis a déjà été utilisé pour une vente et ne peut plus être modifié.');
        }

        $devi->update(['statut' => 'Rejeté']);
        
        return redirect()->route('devis.index')
            ->with('success', 'Devis rejeté avec succès.');
    }

    /**
     * Imprimer un devis en PDF
     */
    public function print(Devis $devis)
    {
        $devi = $devis->loadMissing(['client', 'articles.uniteMesure', 'user']);

        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $pdfBranding = PdfBranding::forBu($buId);
        $configGlobal = $pdfBranding['config'];

        $pdf = Pdf::loadView('devis.pdf', compact('devi', 'configGlobal', 'pdfBranding'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('Devis_' . ($devi->ref_devis ?? $devi->id) . '.pdf');
    }
}