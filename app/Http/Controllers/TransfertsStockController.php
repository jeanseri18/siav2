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
        $transferts = TransfertStock::with(['projetSource', 'projetDestination', 'article'])
            ->orderBy('date_transfert', 'desc')
            ->get();
            
        // Identifier les transferts déjà reçus
        $recuIds = \App\Models\MouvementStock::where('type_mouvement', 'transfert')
            ->where('reference_mouvement', 'like', 'TR-IN-%')
            ->get()
            ->map(function($mvt) {
                return str_replace('TR-IN-', '', $mvt->reference_mouvement);
            })
            ->toArray();
            
        return view('stock_projet.transferts', compact('transferts','projets','articles', 'recuIds'));
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
            
            $quantiteAvant = $stockSource->quantite;
            $stockSource->decrement('quantite', $item['quantite']);

            // Enregistrement du transfert
            $transfert = TransfertStock::create([
                'id_projet_source' => $projetSource,
                'id_projet_destination' => $projetDestination,
                'article_id' => $item['article_id'],
                'quantite' => $item['quantite'],
                'date_transfert' => $dateTransfert,
            ]);

            // Enregistrer le mouvement de sortie (Source)
            \App\Models\MouvementStock::create([
                'stock_projet_id' => $stockSource->id,
                'type_mouvement' => 'transfert', // Sortie
                'quantite' => -$item['quantite'],
                'quantite_avant' => $quantiteAvant,
                'quantite_apres' => $stockSource->quantite,
                'date_mouvement' => $dateTransfert,
                'reference_mouvement' => 'TR-OUT-' . $transfert->id,
                'commentaires' => 'Transfert vers projet ' . \App\Models\Projet::find($projetDestination)->nom_projet
            ]);
        }

        return redirect()->route('transferts.index')->with('success', 'Transfert(s) initié(s) avec succès. En attente de réception.');
    }

    public function receptionner(Request $request, TransfertStock $transfert)
    {
        // Vérifier si déjà reçu
        $dejaRecu = \App\Models\MouvementStock::where('reference_mouvement', 'TR-IN-' . $transfert->id)->exists();
        
        if ($dejaRecu) {
            return back()->with('error', 'Ce transfert a déjà été réceptionné.');
        }

        \DB::beginTransaction();

        try {
            // Ajout au stock du projet de destination
            $stockDestination = StockProjet::firstOrCreate(
                [
                    'id_projet' => $transfert->id_projet_destination,
                    'article_id' => $transfert->article_id
                ],
                ['quantite' => 0]
            );

            $quantiteAvant = $stockDestination->quantite;
            $stockDestination->increment('quantite', $transfert->quantite);

            // Enregistrer le mouvement d'entrée (Destination)
            \App\Models\MouvementStock::create([
                'stock_projet_id' => $stockDestination->id,
                'type_mouvement' => 'transfert', // Entrée
                'quantite' => $transfert->quantite,
                'quantite_avant' => $quantiteAvant,
                'quantite_apres' => $stockDestination->quantite,
                'date_mouvement' => now(),
                'reference_mouvement' => 'TR-IN-' . $transfert->id,
                'commentaires' => 'Réception transfert depuis projet ' . $transfert->projetSource->nom_projet
            ]);

            \DB::commit();

            return redirect()->route('transferts.index')->with('success', 'Réception confirmée avec succès.');

        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Erreur lors de la réception: ' . $e->getMessage());
        }
    }
}
