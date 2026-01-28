<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\ClientFournisseur;
use App\Models\DemandeApprovisionnement;
use App\Models\DemandeAchat;
use App\Models\DemandeCotation;
use App\Models\Article;
use App\Models\Reference;
use App\Models\Projet;
use App\Models\ModePaiement;
use App\Models\ConfigGlobal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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
        $demandesCotation = DemandeCotation::with('demandeAchat')->where('statut', 'terminée')->get();
        $projets = Projet::all();
        $modesPaiement = ModePaiement::all();
        $articles = Article::with('uniteMesure')->get();
        
        return view('bon_commandes.create', compact('fournisseurs', 'demandesAppro', 'demandesAchat', 'demandesCotation', 'projets', 'modesPaiement', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => 'required|exists:client_fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
            'mode_reglement' => 'nullable|string',
            'delai_reglement' => 'nullable|string',
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

        // Calculer le montant total avec remises
        $montantTotal = 0;
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                $montantBrut = $request->quantite[$i] * $request->prix_unitaire[$i];
                $remise = $request->remise[$i] ?? 0;
                $montantRemise = $montantBrut * ($remise / 100);
                $montantTotal += $montantBrut - $montantRemise;
            }
        }

        // Créer le bon de commande
        $bonCommande = BonCommande::create([
            'reference' => $newReference,
            'date_commande' => $request->date_commande,
            'fournisseur_id' => $request->fournisseur_id,
            'demande_approvisionnement_id' => $request->demande_approvisionnement_id,
            'demande_achat_id' => $request->demande_achat_id,
            'projet_id' => $request->projet_id,
            'demande_cotation_id' => $request->demande_cotation_id,
            'user_id' => Auth::id(),
            'montant_total' => $montantTotal,
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'conditions_paiement' => $request->conditions_paiement,
            'notes' => $request->notes,
            'mode_reglement' => $request->mode_reglement,
            'delai_reglement' => $request->delai_reglement,
            'lieu_livraison' => $request->lieu_livraison,
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
                    'remise' => $request->remise[$i] ?? 0,
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
        $demandesCotation = DemandeCotation::with('demandeAchat')->where('statut', 'terminée')->get();
        $projets = Projet::all();
        $modesPaiement = ModePaiement::all();
        $articles = Article::with('uniteMesure')->get();
        
        $bonCommande->load(['lignes.article']);
        
        return view('bon_commandes.edit', compact('bonCommande', 'fournisseurs', 'demandesAppro', 'demandesAchat', 'demandesCotation', 'projets', 'modesPaiement', 'articles'));
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
            'mode_reglement' => 'nullable|string',
            'delai_reglement' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'prix_unitaire' => 'required|array',
            'prix_unitaire.*' => 'numeric|min:0'
        ]);

        // Calculer le montant total avec remises
        $montantTotal = 0;
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                $montantBrut = $request->quantite[$i] * $request->prix_unitaire[$i];
                $remise = $request->remise[$i] ?? 0;
                $montantRemise = $montantBrut * ($remise / 100);
                $montantTotal += $montantBrut - $montantRemise;
            }
        }

        // Mettre à jour le bon de commande
        $bonCommande->update([
            'date_commande' => $request->date_commande,
            'fournisseur_id' => $request->fournisseur_id,
            'demande_approvisionnement_id' => $request->demande_approvisionnement_id,
            'demande_achat_id' => $request->demande_achat_id,
            'projet_id' => $request->projet_id,
            'demande_cotation_id' => $request->demande_cotation_id,
            'montant_total' => $montantTotal,
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'conditions_paiement' => $request->conditions_paiement,
            'notes' => $request->notes,
            'mode_reglement' => $request->mode_reglement,
            'delai_reglement' => $request->delai_reglement,
            'lieu_livraison' => $request->lieu_livraison
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
                    'remise' => $request->remise[$i] ?? 0,
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

    public function confirm(Request $request, BonCommande $bonCommande)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour confirmer ce bon de commande.');
        }

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
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour annuler ce bon de commande.');
        }

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
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['magasinier', 'chef_chantier', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour marquer ce bon de commande comme livré.');
        }

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

    /**
     * Récupérer la demande d'achat liée à une demande de cotation
     */
    public function getDemandeAchatFromCotation($demandeCotationId)
    {
        $demandeCotation = DemandeCotation::with([
            'demandeAchat.demandeApprovisionnement',
            'demandeAchat.projet',
            'fournisseurs.fournisseur'
        ])->find($demandeCotationId);
        
        $response = ['success' => false];
        
        if ($demandeCotation && $demandeCotation->demandeAchat) {
            $response = [
                'success' => true,
                'demande_achat_id' => $demandeCotation->demandeAchat->id,
                'demande_achat_reference' => $demandeCotation->demandeAchat->reference
            ];
            
            // Ajouter la demande d'approvisionnement si elle existe
            if ($demandeCotation->demandeAchat->demandeApprovisionnement) {
                $response['demande_approvisionnement_id'] = $demandeCotation->demandeAchat->demandeApprovisionnement->id;
                $response['demande_approvisionnement_reference'] = $demandeCotation->demandeAchat->demandeApprovisionnement->reference;
            }
            
            // Ajouter le projet si il existe
            if ($demandeCotation->demandeAchat->projet) {
                $response['projet_id'] = $demandeCotation->demandeAchat->projet->id;
                $response['projet_nom'] = $demandeCotation->demandeAchat->projet->nom_projet;
            }
            
            // Chercher le fournisseur retenu
            $fournisseurRetenu = $demandeCotation->fournisseurs->where('retenu', true)->first();
            
            if ($fournisseurRetenu && $fournisseurRetenu->fournisseur) {
                $response['fournisseur'] = [
                    'id' => $fournisseurRetenu->fournisseur->id,
                    'nom' => $fournisseurRetenu->fournisseur->nom_raison_sociale,
                    'prenoms' => $fournisseurRetenu->fournisseur->prenoms,
                    'mode_paiement' => $fournisseurRetenu->fournisseur->mode_paiement,
                    'delai_paiement' => $fournisseurRetenu->fournisseur->delai_paiement
                ];
            }
        } else {
            $response['message'] = 'Aucune demande d\'achat liée trouvée';
        }
        
        return response()->json($response);
    }

    /**
     * Exporter un bon de commande en PDF
     */
    public function exportPDF($id)
    {
        $bonCommande = BonCommande::with([
            'fournisseur', 
            'user.bus', 
            'lignes.article', 
            'demandeApprovisionnement', 
            'demandeAchat'
        ])->findOrFail($id);
        
        // Récupérer les configurations globales
        $configGlobal = ConfigGlobal::first();
        
        $pdf = PDF::loadView('bon_commandes.bon_commande_pdf', compact('bonCommande', 'configGlobal'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->download('Bon_Commande_' . $bonCommande->reference . '.pdf');
    }
}