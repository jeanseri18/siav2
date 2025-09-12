<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Article;
use App\Models\ClientFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevisController extends Controller
{
    public function index()
    {
        $devis = Devis::with('client', 'articles')->get();
        return view('devis.index', compact('devis'));
    }

    public function create()
    {
        $clients = ClientFournisseur::where('type', 'Client')->get();
        $articles = Article::all();
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
                'utilise_pour_vente' => false
            ]);

            $totalHT = 0;

            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['id']);
                
                // Utiliser le coût moyen pondéré comme prix unitaire HT
                $prixUnitaireHT = $article->cout_moyen_pondere;
                $montantTotal = $prixUnitaireHT * $articleData['quantite'];

                $devis->articles()->attach($article->id, [
                    'quantite' => $articleData['quantite'],
                    'prix_unitaire_ht' => $prixUnitaireHT,
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

    public function show(Devis $devis)
    {
        return view('devis.show', compact('devis'));
    }

    public function edit(Devis $devis)
    {
        if ($devis->utilise_pour_vente) {
            return redirect()->route('devis.index')
                ->with('error', 'Ce devis a déjà été utilisé pour une vente et ne peut plus être modifié.');
        }

        $clients = ClientFournisseur::where('type', 'Client')->get();
        $articles = Article::all();
        return view('devis.edit', compact('devis', 'clients', 'articles'));
    }

    public function update(Request $request, Devis $devis)
    {
        if ($devis->utilise_pour_vente) {
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
        ]);

        DB::transaction(function () use ($request, $devis) {
            // Supprimer les anciens articles
            $devis->articles()->detach();

            $totalHT = 0;

            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['id']);
                
                $prixUnitaireHT = $article->cout_moyen_pondere;
                $montantTotal = $prixUnitaireHT * $articleData['quantite'];

                $devis->articles()->attach($article->id, [
                    'quantite' => $articleData['quantite'],
                    'prix_unitaire_ht' => $prixUnitaireHT,
                    'montant_total' => $montantTotal
                ]);

                $totalHT += $montantTotal;
            }

            $tva = $totalHT * 0.18;
            $totalTTC = $totalHT + $tva;

            $devis->update([
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

    public function destroy(Devis $devis)
    {
        if ($devis->utilise_pour_vente) {
            return redirect()->route('devis.index')
                ->with('error', 'Ce devis a déjà été utilisé pour une vente et ne peut plus être supprimé.');
        }

        $devis->delete();
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
}