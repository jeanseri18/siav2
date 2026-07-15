<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransfertStock;
use App\Models\StockProjet;
use App\Models\Projet;
use App\Models\Article;
use App\Models\MouvementStock;

class TransfertsStockController extends Controller
{
    /**
     * Lignes d'inventaire (stock projet + qté > 0) pour alimenter les listes d'articles des transferts.
     */
    public function lignesStockPourProjet(Projet $projet)
    {
        $lignes = StockProjet::query()
            ->where('id_projet', $projet->id)
            ->where('quantite', '>', 0)
            ->with(['article'])
            ->orderBy('article_id')
            ->get();

        return response()->json([
            'lignes' => $lignes->map(static function (StockProjet $s) {
                return [
                    'article_id' => $s->article_id,
                    'nom' => $s->article?->nom ?? ('Article #'.$s->article_id),
                    'quantite_disponible' => (int) $s->quantite,
                ];
            }),
        ]);
    }

    public function index()
    {
        $projets = Projet::all();
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
            
        return view('stock_projet.transferts', compact('transferts', 'projets', 'recuIds'));
    }

    public function create()
    {
        $projets = Projet::all();
        $stocksSource = collect();
        if (session('projet_id')) {
            $stocksSource = StockProjet::where('id_projet', session('projet_id'))
                ->where('quantite', '>', 0)
                ->with('article')
                ->orderBy('article_id')
                ->get();
        }

        return view('stock_projet.create_transfer', compact('projets', 'stocksSource'));
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

    public function edit(TransfertStock $transfert)
    {
        $pid = session('projet_id');
        if (! $pid || (int) $pid !== (int) $transfert->id_projet_source) {
            return redirect()->route('transferts.index')->with('error', 'Modification réservée au projet ayant émis le transfert.');
        }
        if (MouvementStock::where('reference_mouvement', 'TR-IN-' . $transfert->id)->exists()) {
            return redirect()->route('transferts.index')->with('error', 'Ce transfert est déjà réceptionné.');
        }

        $transfert->load(['article', 'projetSource', 'projetDestination']);
        $projets = Projet::orderBy('nom_projet')->get();

        return view('stock_projet.edit_transfer', compact('transfert', 'projets'));
    }

    public function update(Request $request, TransfertStock $transfert)
    {
        $pid = session('projet_id');
        if (! $pid || (int) $pid !== (int) $transfert->id_projet_source) {
            return redirect()->route('transferts.index')->with('error', 'Modification réservée au projet source.');
        }
        if (MouvementStock::where('reference_mouvement', 'TR-IN-' . $transfert->id)->exists()) {
            return redirect()->route('transferts.index')->with('error', 'Ce transfert est déjà réceptionné.');
        }

        $request->validate([
            'projet_destination' => 'required|exists:projets,id',
            'quantite' => 'required|integer|min:1',
            'date_transfert' => 'required|date',
        ]);

        $destId = (int) $request->projet_destination;
        if ($destId === (int) $transfert->id_projet_source) {
            return back()->withErrors(['projet_destination' => 'La destination doit être différente du projet source.'])->withInput();
        }

        DB::beginTransaction();

        try {
            $stockSource = StockProjet::where('id_projet', $transfert->id_projet_source)
                ->where('article_id', $transfert->article_id)
                ->firstOrFail();

            $stockSource->increment('quantite', $transfert->quantite);
            $quantiteAvant = $stockSource->quantite;

            if ($quantiteAvant < $request->quantite) {
                DB::rollBack();

                return back()->withErrors(['quantite' => 'Stock source insuffisant pour cette quantité.'])->withInput();
            }

            $stockSource->decrement('quantite', $request->quantite);
            $quantiteApres = $stockSource->quantite;

            MouvementStock::where('reference_mouvement', 'TR-OUT-' . $transfert->id)->delete();

            MouvementStock::create([
                'stock_projet_id' => $stockSource->id,
                'type_mouvement' => 'transfert',
                'quantite' => -$request->quantite,
                'quantite_avant' => $quantiteAvant,
                'quantite_apres' => $quantiteApres,
                'date_mouvement' => $request->date_transfert,
                'reference_mouvement' => 'TR-OUT-' . $transfert->id,
                'commentaires' => 'Transfert vers projet ' . Projet::find($destId)->nom_projet,
                'user_id' => auth()->id(),
            ]);

            $transfert->update([
                'id_projet_destination' => $destId,
                'quantite' => $request->quantite,
                'date_transfert' => $request->date_transfert,
            ]);

            DB::commit();

            return redirect()->route('transferts.index')->with('success', 'Transfert mis à jour.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function annuler(Request $request, TransfertStock $transfert)
    {
        $pid = session('projet_id');
        if (! $pid || (int) $pid !== (int) $transfert->id_projet_source) {
            return redirect()->route('transferts.index')->with('error', 'Annulation réservée au projet source.');
        }

        DB::beginTransaction();

        try {
            $this->executerRetraitTransfertEnTransit($transfert);
            DB::commit();

            return redirect()->route('transferts.index')->with('success', 'Transfert annulé. Le stock du projet source a été rétabli.');
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors de l\'annulation : ' . $e->getMessage());
        }
    }

    public function refuser(Request $request, TransfertStock $transfert)
    {
        $pid = session('projet_id');
        if (! $pid || (int) $pid !== (int) $transfert->id_projet_destination) {
            return redirect()->route('transferts.index')->with('error', 'Le refus doit être effectué depuis le projet destinataire.');
        }

        DB::beginTransaction();

        try {
            $this->executerRetraitTransfertEnTransit($transfert);
            DB::commit();

            return redirect()->route('transferts.index')->with('success', 'Transfert refusé. Le stock a été rétabli sur le projet source.');
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Erreur lors du refus : ' . $e->getMessage());
        }
    }

    public function receptionner(Request $request, TransfertStock $transfert)
    {
        $pid = session('projet_id');
        if ($pid && (int) $pid !== (int) $transfert->id_projet_destination) {
            return back()->with('error', 'La réception doit être effectuée depuis le projet destinataire.');
        }

        // Vérifier si déjà reçu
        $dejaRecu = MouvementStock::where('reference_mouvement', 'TR-IN-' . $transfert->id)->exists();
        
        if ($dejaRecu) {
            return back()->with('error', 'Ce transfert a déjà été réceptionné.');
        }

        DB::beginTransaction();

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
            MouvementStock::create([
                'stock_projet_id' => $stockDestination->id,
                'type_mouvement' => 'transfert', // Entrée
                'quantite' => $transfert->quantite,
                'quantite_avant' => $quantiteAvant,
                'quantite_apres' => $stockDestination->quantite,
                'date_mouvement' => now(),
                'reference_mouvement' => 'TR-IN-' . $transfert->id,
                'commentaires' => 'Réception transfert depuis projet ' . $transfert->projetSource->nom_projet,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('transferts.index')->with('success', 'Réception confirmée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la réception: ' . $e->getMessage());
        }
    }

    /**
     * Annule un transfert encore « en transit » : rétablit le stock source, supprime le mouvement TR-OUT et l'enregistrement.
     *
     * @throws \RuntimeException Si déjà réceptionné (TR-IN présent)
     */
    protected function executerRetraitTransfertEnTransit(TransfertStock $transfert): void
    {
        if (MouvementStock::where('reference_mouvement', 'TR-IN-' . $transfert->id)->exists()) {
            throw new \RuntimeException('Ce transfert est déjà réceptionné.');
        }

        $stockSource = StockProjet::where('id_projet', $transfert->id_projet_source)
            ->where('article_id', $transfert->article_id)
            ->firstOrFail();

        $stockSource->increment('quantite', $transfert->quantite);

        MouvementStock::where('reference_mouvement', 'TR-OUT-' . $transfert->id)->delete();

        $transfert->delete();
    }
}
