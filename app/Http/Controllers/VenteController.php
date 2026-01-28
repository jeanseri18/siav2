<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Devis;
use App\Models\Article;
use App\Models\ClientFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with('client', 'articles', 'devis')->get();
        return view('ventes.index', compact('ventes'));
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
                    $articles[] = [
                        'id' => $article->id,
                        'quantite' => $article->pivot->quantite,
                        'prix_unitaire' => $article->pivot->prix_unitaire,
                        'sous_total' => $article->pivot->sous_total
                    ];
                    
                    $totalHT += $article->pivot->sous_total;
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
        $vente->load('client', 'articles', 'devis', 'prestations');
        return view('ventes.facture', compact('vente'));
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
        $pdf = PDF::loadView('ventes.report_pdf', compact('ventes'))->setPaper($paperSize, $orientation);

        // Retourner le PDF au navigateur
        return $pdf->download('rapport_ventes.pdf');
    }
}
