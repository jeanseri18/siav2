<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransfertStock;
use App\Models\StockProjet;
use App\Models\Projet;
use App\Models\Article;

class TransfertsStockController extends Controller
{
    public function index()
    {
        $projets = Projet::all();
        $articles = Article::all();
        $transferts = TransfertStock::with(['projetSource', 'projetDestination', 'article'])->get();
        return view('stock_projet.transferts', compact('transferts','projets','articles'));
    }

    public function create()
    {
        $projets = Projet::all();
        $articles = Article::all();
        return view('stock_projet.create_transfer', compact('projets','articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_source' => 'required|exists:projets,id',
            'projet_destination' => 'required|exists:projets,id',
            'date_transfert' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.article_id' => 'required|exists:articles,id',
            'items.*.quantite' => 'required|integer|min:1',
        ]);

        $projetSource = $request->projet_source;
        $projetDestination = $request->projet_destination;
        $dateTransfert = $request->date_transfert;

        // Vérification du stock disponible pour tous les articles
        foreach ($request->items as $item) {
            $stockSource = StockProjet::where('id_projet', $projetSource)
                                      ->where('article_id', $item['article_id'])
                                      ->first();

            if (!$stockSource || $stockSource->quantite < $item['quantite']) {
                return redirect()->back()->with('error', 'Quantité insuffisante dans le stock source pour l\'article: ' . Article::find($item['article_id'])->nom);
            }
        }

        // Effectuer les transferts pour chaque article
        foreach ($request->items as $item) {
            // Décrémentation du stock source
            $stockSource = StockProjet::where('id_projet', $projetSource)
                                      ->where('article_id', $item['article_id'])
                                      ->first();
            
            $stockSource->decrement('quantite', $item['quantite']);

            // Ajout au stock du projet de destination
            $stockDestination = StockProjet::firstOrCreate(
                [
                    'id_projet' => $projetDestination,
                    'article_id' => $item['article_id']
                ],
                ['quantite' => 0]
            );

            $stockDestination->increment('quantite', $item['quantite']);

            // Enregistrement du transfert
            TransfertStock::create([
                'id_projet_source' => $projetSource,
                'id_projet_destination' => $projetDestination,
                'article_id' => $item['article_id'],
                'quantite' => $item['quantite'],
                'date_transfert' => $dateTransfert,
            ]);
        }

        return redirect()->route('transferts.index')->with('success', 'Transfert(s) effectué(s) avec succès.');
    }
}
