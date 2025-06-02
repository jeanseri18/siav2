<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\ClientFournisseur;
use App\Models\DemandeApprovisionnement;
use App\Models\DemandeAchat;
use App\Models\Article;
use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BonCommandeController extends Controller
{
    public function index()
    {
        $bonCommandes = BonCommande::with(['fournisseur', 'user', 'demandeApprovisionnement', 'demandeAchat'])->get();
        return view('bon_commandes.index', compact('bonCommandes'));
    }

    public function create()
    {
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $demandesAppro = DemandeApprovisionnement::where('statut', 'approuvée')->get();
        $demandesAchat = DemandeAchat::where('statut', 'approuvée')->get();
        $articles = Article::all();
        
        return view('bon_commandes.create', compact('fournisseurs', 'demandesAppro', 'demandesAchat', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => 'required|exists:client_fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'prix_unitaire' => 'required|array',
            'prix_unitaire.*' => 'numeric|min:0'
        ]);

        // Générer la référence
        $lastReference = Reference::where('nom', 'Code bon de commande')
            ->latest('created_at')
            ->first();
        
        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'PO_0000';
        $newReference = 'PO_' . now()->format('YmdHis');

        // Calculer le montant total
        $montantTotal = 0;
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                $montantTotal += $request->quantite[$i] * $request->prix_unitaire[$i];
            }
        }

        // Créer le bon de commande
        $bonCommande = BonCommande::create([
            'reference' => $newReference,
            'date_commande' => $request->date_commande,
            'fournisseur_id' => $request->fournisseur_id,
            'demande_approvisionnement_id' => $request->demande_approvisionnement_id,
            'demande_achat_id' => $request->demande_achat_id,
            'user_id' => Auth::id(),
            'montant_total' => $montantTotal,
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'conditions_paiement' => $request->conditions_paiement,
            'notes' => $request->notes,
            'statut' => 'en attente'
        ]);

        // Ajouter les lignes du bon de commande
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                LigneBonCommande::create([
                    'bon_commande_id' => $bonCommande->id,
                    'article_id' => $request->article_id[$i],
                    'quantite' => $request->quantite[$i],
                    'prix_unitaire' => $request->prix_unitaire[$i],
                    'quantite_livree' => 0,
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        // Enregistrer la référence
        Reference::create([
            'nom' => 'Code Bon Commande',
            'ref' => $newReference
        ]);

        return redirect()->route('bon-commandes.index')
            ->with('success', 'Bon de commande créé avec succès');
    }

    public function show(BonCommande $bonCommande)
    {
        $bonCommande->load(['fournisseur', 'user', 'demandeApprovisionnement', 'demandeAchat', 'lignes.article']);
        return view('bon_commandes.show', compact('bonCommande'));
    }

    public function edit(BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Impossible de modifier un bon de commande qui n\'est pas en attente');
        }

        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $demandesAppro = DemandeApprovisionnement::where('statut', 'approuvée')->get();
        $demandesAchat = DemandeAchat::where('statut', 'approuvée')->get();
        $articles = Article::all();
        
        $bonCommande->load(['lignes.article']);
        
        return view('bon_commandes.edit', compact('bonCommande', 'fournisseurs', 'demandesAppro', 'demandesAchat', 'articles'));
    }

    public function update(Request $request, BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Impossible de modifier un bon de commande qui n\'est pas en attente');
        }

        $request->validate([
            'fournisseur_id' => 'required|exists:client_fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'prix_unitaire' => 'required|array',
            'prix_unitaire.*' => 'numeric|min:0'
        ]);

        // Calculer le montant total
        $montantTotal = 0;
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                $montantTotal += $request->quantite[$i] * $request->prix_unitaire[$i];
            }
        }

        // Mettre à jour le bon de commande
        $bonCommande->update([
            'date_commande' => $request->date_commande,
            'fournisseur_id' => $request->fournisseur_id,
            'demande_approvisionnement_id' => $request->demande_approvisionnement_id,
            'demande_achat_id' => $request->demande_achat_id,
            'montant_total' => $montantTotal,
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'conditions_paiement' => $request->conditions_paiement,
            'notes' => $request->notes
        ]);

        // Supprimer les anciennes lignes
        $bonCommande->lignes()->delete();

        // Ajouter les nouvelles lignes
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                LigneBonCommande::create([
                    'bon_commande_id' => $bonCommande->id,
                    'article_id' => $request->article_id[$i],
                    'quantite' => $request->quantite[$i],
                    'prix_unitaire' => $request->prix_unitaire[$i],
                    'quantite_livree' => 0,
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande mis à jour avec succès');
    }

    public function destroy(BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.index')
                ->with('error', 'Impossible de supprimer un bon de commande qui n\'est pas en attente');
        }

        // Supprimer les lignes du bon de commande
        $bonCommande->lignes()->delete();
        
        // Supprimer le bon de commande
        $bonCommande->delete();

        return redirect()->route('bon-commandes.index')
            ->with('success', 'Bon de commande supprimé avec succès');
    }

    public function confirm(BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Ce bon de commande ne peut pas être confirmé');
        }

        // Mettre à jour le statut du bon de commande
        $bonCommande->update([
            'statut' => 'confirmée'
        ]);

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande confirmé avec succès');
    }

    public function cancel(Request $request, BonCommande $bonCommande)
    {
        if ($bonCommande->statut === 'livrée') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Impossible d\'annuler un bon de commande déjà livré');
        }

        // Mettre à jour le statut du bon de commande
        $bonCommande->update([
            'statut' => 'annulée'
        ]);

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande annulé avec succès');
    }

    public function livrer(Request $request, BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'confirmée') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Ce bon de commande ne peut pas être marqué comme livré');
        }

        $request->validate([
            'quantite_livree' => 'required|array',
            'quantite_livree.*' => 'integer|min:0'
        ]);

        // Mettre à jour les quantités livrées
        foreach ($bonCommande->lignes as $index => $ligne) {
            $ligne->update([
                'quantite_livree' => $request->quantite_livree[$index]
            ]);

            // Mettre à jour le stock
            $article = $ligne->article;
            $article->update([
                'quantite_stock' => $article->quantite_stock + $request->quantite_livree[$index]
            ]);
        }

        // Mettre à jour le statut du bon de commande
        $bonCommande->update([
            'statut' => 'livrée'
        ]);

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande marqué comme livré avec succès');
    }
}