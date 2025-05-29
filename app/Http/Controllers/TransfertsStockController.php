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

    public function store(Request $request)
    {
        $request->validate([
            'id_projet_source' => 'required|exists:projets,id',
            'id_projet_destination' => 'required|exists:projets,id',
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|integer|min:1',
            'date_transfert' => 'required|date',
        ]);

        // Vérification du stock disponible
        $stockSource = StockProjet::where('id_projet', $request->id_projet_source)
                                  ->where('article_id', $request->article_id)
                                  ->first();

        if (!$stockSource || $stockSource->quantite < $request->quantite) {
            return redirect()->back()->with('error', 'Quantité insuffisante dans le stock source.');
        }

        // Décrémentation du stock source
        $stockSource->decrement('quantite', $request->quantite);

        // Ajout au stock du projet de destination
        $stockDestination = StockProjet::firstOrCreate(
            [
                'id_projet' => $request->id_projet_destination,
                'article_id' => $request->article_id
            ],
            ['quantite' => 0]
        );

        $stockDestination->increment('quantite', $request->quantite);

        // Enregistrement du transfert
        TransfertStock::create([
            'id_projet_source' => $request->id_projet_source,
            'id_projet_destination' => $request->id_projet_destination,
            'article_id' => $request->article_id,
            'quantite' => $request->quantite,
            'date_transfert' => $request->date_transfert,
        ]);

        return redirect()->back()->with('success', 'Transfert effectué avec succès.');
    }
}
