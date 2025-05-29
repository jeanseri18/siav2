<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Article;
use App\Models\ClientFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with('client', 'articles')->get();
        return view('ventes.index', compact('ventes'));
    }

    public function create()
    {
        $clients = ClientFournisseur::where('type', 'Client')->get();
        $articles = Article::all();
        return view('ventes.create', compact('clients', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:client_fournisseurs,id',
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.quantite' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $vente = Vente::create([
                'client_id' => $request->client_id,
                'total' => 0,
                'statut' => 'En attente'
            ]);

            $total = 0;

            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['id']);

                if ($article->quantite_stock < $articleData['quantite']) {
                    throw new \Exception("Stock insuffisant pour {$article->nom}");
                }

                $sousTotal = $article->prix_unitaire * $articleData['quantite'];

                $vente->articles()->attach($article->id, [
                    'quantite' => $articleData['quantite'],
                    'prix_unitaire' => $article->prix_unitaire,
                    'sous_total' => $sousTotal
                ]);

                $article->decrement('quantite_stock', $articleData['quantite']);
                $total += $sousTotal;
            }

            $vente->update(['total' => $total]);
        });

        return redirect()->route('ventes.index')->with('success', 'Vente enregistrée avec succès.');
    }

    public function show(Vente $vente)
    {
        return view('ventes.show', compact('vente'));
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
