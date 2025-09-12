<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\Article;
use App\Models\StockProjet;
use App\Models\Reception;
use App\Models\LigneReception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    public function index()
    {
        // Récupérer tous les bons de commande confirmés avec leurs lignes et articles
        $bonCommandes = BonCommande::with(['lignes.article', 'fournisseur', 'projet'])
            ->whereIn('statut', ['confirmé', 'partiellement_reçu'])
            ->orderBy('date_commande', 'desc')
            ->paginate(15);
            
        // Récupérer les réceptions récentes
        $recentReceptions = Reception::with(['bonCommande.fournisseur', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('receptions.index', compact('bonCommandes', 'recentReceptions'));
    }

    public function show($id)
    {
        // Si l'ID correspond à une réception, afficher la réception
        $reception = Reception::with(['bonCommande.lignes.article', 'bonCommande.fournisseur', 'bonCommande.projet', 'lignes.article', 'user'])
            ->find($id);
            
        if ($reception) {
            return view('receptions.show_reception', compact('reception'));
        }
        
        // Sinon, traiter comme un bon de commande
        $bonCommande = BonCommande::with(['lignes.article', 'fournisseur', 'projet'])
            ->findOrFail($id);
        
        return view('receptions.show', compact('bonCommande'));
    }

    public function create(BonCommande $bonCommande = null)
    {
        if (!$bonCommande) {
            return redirect()->route('receptions.index')
                ->with('error', 'Veuillez sélectionner un bon de commande pour effectuer une réception.');
        }
        
        $bonCommande->load(['lignes.article', 'fournisseur', 'projet']);
        
        // Vérifier si toutes les lignes sont déjà complètement reçues
        $toutesLignesRecues = $bonCommande->lignes->every(function($ligne) {
            return $ligne->quantite_recue >= $ligne->quantite;
        });
        
        if ($toutesLignesRecues) {
            return redirect()->route('receptions.show', $bonCommande->id)
                ->with('info', 'Toutes les lignes de ce bon de commande ont déjà été reçues.');
        }
        
        return view('receptions.create', compact('bonCommande'));
    }

    public function store(Request $request, BonCommande $bonCommande)
    {
        $request->validate([
            'date_reception' => 'required|date',
            'numero_bon_livraison' => 'nullable|string|max:255',
            'transporteur' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'quantites' => 'required|array',
            'quantites.*' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        
        try {
            // Créer la réception
            $reception = Reception::create([
                'bon_commande_id' => $bonCommande->id,
                'numero_reception' => Reception::generateNumeroReception(),
                'date_reception' => $request->date_reception,
                'numero_bon_livraison' => $request->numero_bon_livraison,
                'transporteur' => $request->transporteur,
                'observations' => $request->observations,
                'user_id' => Auth::id(),
                'statut' => 'en_cours'
            ]);
            
            $totalQuantiteRecue = 0;
            $montantTotalRecu = 0;
            
            // Traiter chaque ligne de réception
            foreach ($request->quantites as $ligneId => $quantiteRecue) {
                if ($quantiteRecue <= 0) continue;
                
                $ligne = LigneBonCommande::findOrFail($ligneId);
                
                // Vérifier que la quantité reçue ne dépasse pas la quantité restante
                $quantiteRestante = $ligne->quantite - $ligne->quantite_recue;
                if ($quantiteRecue > $quantiteRestante) {
                    throw new \Exception("La quantité reçue pour {$ligne->article->nom} ne peut pas dépasser la quantité restante ({$quantiteRestante}).");
                }
                
                // Créer la ligne de réception
                LigneReception::create([
                    'reception_id' => $reception->id,
                    'ligne_bon_commande_id' => $ligne->id,
                    'article_id' => $ligne->article_id,
                    'quantite_recue' => $quantiteRecue,
                    'quantite_conforme' => $quantiteRecue, // Par défaut, tout est conforme
                    'quantite_non_conforme' => 0,
                    'prix_unitaire_recu' => $ligne->prix_unitaire,
                    'etat_article' => 'neuf'
                ]);
                
                // Mettre à jour la quantité reçue dans la ligne de bon de commande
                $ligne->quantite_recue += $quantiteRecue;
                $ligne->save();
                
                $totalQuantiteRecue += $quantiteRecue;
                $montantTotalRecu += $quantiteRecue * $ligne->prix_unitaire;
                
                // Mettre à jour le stock
                $this->updateStock($ligne->article, $bonCommande->projet_id, $quantiteRecue);
            }
            
            // Mettre à jour les totaux de la réception
            $reception->update([
                'quantite_totale_recue' => $totalQuantiteRecue,
                'montant_total_recu' => $montantTotalRecu
            ]);
            
            // Mettre à jour le statut du bon de commande et de la réception
            $this->updateBonCommandeStatus($bonCommande);
            $this->updateReceptionStatus($reception);
            
            DB::commit();
            
            return redirect()->route('receptions.show', $reception->id)
                           ->with('success', 'Réception effectuée avec succès.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Erreur lors de la réception : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour le statut de la réception
     */
    private function updateReceptionStatus(Reception $reception)
    {
        $bonCommande = $reception->bonCommande;
        $totalQuantiteCommande = $bonCommande->lignes->sum('quantite');
        $totalQuantiteRecue = $bonCommande->lignes->sum('quantite_recue');
        
        if ($totalQuantiteRecue >= $totalQuantiteCommande) {
            $reception->statut = 'complete';
        } elseif ($totalQuantiteRecue > 0) {
            $reception->statut = 'partielle';
        } else {
            $reception->statut = 'en_cours';
        }
        
        $reception->save();
    }
    
    /**
     * Met à jour le statut du bon de commande
     */
    private function updateBonCommandeStatus(BonCommande $bonCommande)
    {
        $totalQuantiteCommande = $bonCommande->lignes->sum('quantite');
        $totalQuantiteRecue = $bonCommande->lignes->sum('quantite_recue');
        
        if ($totalQuantiteRecue >= $totalQuantiteCommande) {
            $bonCommande->statut = 'reçu';
        } elseif ($totalQuantiteRecue > 0) {
            $bonCommande->statut = 'partiellement_reçu';
        }
        
        $bonCommande->save();
    }

    /**
     * Met à jour le stock après réception
     */
    private function updateStock(Article $article, $projetId, $quantite)
    {
        // Mettre à jour le stock général
        $article->stock_actuel += $quantite;
        $article->save();
        
        // Mettre à jour le stock projet si spécifié
        if ($projetId) {
            $stockProjet = StockProjet::firstOrCreate(
                [
                    'article_id' => $article->id,
                    'projet_id' => $projetId
                ],
                [
                    'quantite_stock' => 0,
                    'quantite_reservee' => 0
                ]
            );
            
            $stockProjet->quantite_stock += $quantite;
            $stockProjet->save();
        }
    }

    public function history($bonCommandeId)
    {
        $bonCommande = BonCommande::with(['fournisseur', 'lignes.article'])->findOrFail($bonCommandeId);
        
        return view('receptions.history', compact('bonCommande'));
    }
}