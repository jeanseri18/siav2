<?php

namespace App\Http\Controllers;

use App\Models\DemandeCotation;
use App\Models\LigneDemandeCotation;
use App\Models\FournisseurDemandeCotation;
use App\Models\DemandeAchat;
use App\Models\ClientFournisseur;
use App\Models\Article;
use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeCotationController extends Controller
{
    public function index()
    {
        $demandes = DemandeCotation::with(['user', 'demandeAchat', 'fournisseurs.fournisseur'])->get();
        return view('demande_cotations.index', compact('demandes'));
    }

    public function create()
    {
        $demandesAchat = DemandeAchat::where('statut', 'approuvée')->get();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $articles = Article::all();
        
        return view('demande_cotations.create', compact('demandesAchat', 'fournisseurs', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_demande' => 'required|date',
            'date_expiration' => 'required|date|after:date_demande',
            'demande_achat_id' => 'nullable|exists:demande_achats,id',
            'description' => 'nullable|string',
            'conditions_generales' => 'nullable|string',
            'fournisseur_id' => 'required|array',
            'fournisseur_id.*' => 'exists:client_fournisseurs,id',
            'designation' => 'required|array',
            'designation.*' => 'required|string',
            'article_id' => 'nullable|array',
            'article_id.*' => 'nullable|exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'unite_mesure' => 'required|array',
            'unite_mesure.*' => 'string',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

        // Générer la référence
        $lastReference = Reference::where('nom', 'Code Demande Cotation')
            ->latest('created_at')
            ->first();
        
        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'DC_0000';
        $newReference = 'DC_' . now()->format('YmdHis');

        // Créer la demande
        $demande = DemandeCotation::create([
            'reference' => $newReference,
            'date_demande' => $request->date_demande,
            'date_expiration' => $request->date_expiration,
            'demande_achat_id' => $request->demande_achat_id,
            'description' => $request->description,
            'conditions_generales' => $request->conditions_generales,
            'user_id' => Auth::id(),
            'statut' => 'en cours'
        ]);

        // Ajouter les fournisseurs
        foreach ($request->fournisseur_id as $fournisseurId) {
            FournisseurDemandeCotation::create([
                'demande_cotation_id' => $demande->id,
                'fournisseur_id' => $fournisseurId
            ]);
        }

        // Ajouter les lignes de la demande
        for ($i = 0; $i < count($request->designation); $i++) {
            if ($request->designation[$i] && $request->quantite[$i] > 0) {
                LigneDemandeCotation::create([
                    'demande_cotation_id' => $demande->id,
                    'article_id' => $request->article_id[$i] ?? null,
                    'designation' => $request->designation[$i],
                    'quantite' => $request->quantite[$i],
                    'unite_mesure' => $request->unite_mesure[$i],
                    'specifications' => $request->specifications[$i] ?? null
                ]);
            }
        }

        // Enregistrer la référence
        Reference::create([
            'nom' => 'Code Demande Cotation',
            'ref' => $newReference
        ]);

        return redirect()->route('demande-cotations.index')
            ->with('success', 'Demande de cotation créée avec succès');
    }

    public function show(DemandeCotation $demandeCotation)
    {
        $demandeCotation->load(['user', 'demandeAchat', 'fournisseurs.fournisseur', 'lignes.article']);
        return view('demande_cotations.show', compact('demandeCotation'));
    }

    public function edit(DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en cours');
        }

        $demandesAchat = DemandeAchat::where('statut', 'approuvée')->get();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $articles = Article::all();
        
        $demandeCotation->load(['fournisseurs.fournisseur', 'lignes.article']);
        
        return view('demande_cotations.edit', compact('demandeCotation', 'demandesAchat', 'fournisseurs', 'articles'));
    }

    public function update(Request $request, DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en cours');
        }

        $request->validate([
            'date_demande' => 'required|date',
            'date_expiration' => 'required|date|after:date_demande',
            'demande_achat_id' => 'nullable|exists:demande_achats,id',
            'description' => 'nullable|string',
            'conditions_generales' => 'nullable|string',
            'fournisseur_id' => 'required|array',
            'fournisseur_id.*' => 'exists:client_fournisseurs,id',
            'designation' => 'required|array',
            'designation.*' => 'required|string',
            'article_id' => 'nullable|array',
            'article_id.*' => 'nullable|exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'unite_mesure' => 'required|array',
            'unite_mesure.*' => 'string',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

       // Mettre à jour la demande
        $demandeCotation->update([
            'date_demande' => $request->date_demande,
            'date_expiration' => $request->date_expiration,
            'demande_achat_id' => $request->demande_achat_id,
            'description' => $request->description,
            'conditions_generales' => $request->conditions_generales
        ]);

        // Supprimer les anciennes lignes
        $demandeCotation->lignes()->delete();

        // Supprimer les anciens fournisseurs
        $demandeCotation->fournisseurs()->delete();

        // Ajouter les nouveaux fournisseurs
        foreach ($request->fournisseur_id as $fournisseurId) {
            FournisseurDemandeCotation::create([
                'demande_cotation_id' => $demandeCotation->id,
                'fournisseur_id' => $fournisseurId
            ]);
        }

        // Ajouter les nouvelles lignes
        for ($i = 0; $i < count($request->designation); $i++) {
            if ($request->designation[$i] && $request->quantite[$i] > 0) {
                LigneDemandeCotation::create([
                    'demande_cotation_id' => $demandeCotation->id,
                    'article_id' => $request->article_id[$i] ?? null,
                    'designation' => $request->designation[$i],
                    'quantite' => $request->quantite[$i],
                    'unite_mesure' => $request->unite_mesure[$i],
                    'specifications' => $request->specifications[$i] ?? null
                ]);
            }
        }

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Demande de cotation mise à jour avec succès');
    }

    public function destroy(DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.index')
                ->with('error', 'Impossible de supprimer une demande qui n\'est pas en cours');
        }

        // Supprimer les lignes de la demande
        $demandeCotation->lignes()->delete();
        
        // Supprimer les fournisseurs de la demande
        $demandeCotation->fournisseurs()->delete();
        
        // Supprimer la demande
        $demandeCotation->delete();

        return redirect()->route('demande-cotations.index')
            ->with('success', 'Demande de cotation supprimée avec succès');
    }

    public function terminate(DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Cette demande ne peut pas être terminée');
        }

        // Mettre à jour le statut de la demande
        $demandeCotation->update([
            'statut' => 'terminée'
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Demande de cotation terminée avec succès');
    }

    public function cancel(DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Cette demande ne peut pas être annulée');
        }

        // Mettre à jour le statut de la demande
        $demandeCotation->update([
            'statut' => 'annulée'
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Demande de cotation annulée avec succès');
    }

    public function saveFournisseurResponse(Request $request, DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible d\'enregistrer la réponse d\'un fournisseur pour une demande qui n\'est pas en cours');
        }

        $request->validate([
            'date_reponse' => 'required|date',
            'montant_total' => 'required|numeric|min:0',
            'commentaire' => 'nullable|string'
        ]);

        // Mettre à jour la réponse du fournisseur
        $fournisseurDemandeCotation->update([
            'repondu' => true,
            'date_reponse' => $request->date_reponse,
            'montant_total' => $request->montant_total,
            'commentaire' => $request->commentaire
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Réponse du fournisseur enregistrée avec succès');
    }

    public function selectFournisseur(Request $request, DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de sélectionner un fournisseur pour une demande qui n\'est pas en cours');
        }

        if (!$fournisseurDemandeCotation->repondu) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de sélectionner un fournisseur qui n\'a pas répondu');
        }

        // Mettre à jour tous les fournisseurs (désélectionner)
        $demandeCotation->fournisseurs()->update([
            'retenu' => false
        ]);

        // Sélectionner le fournisseur
        $fournisseurDemandeCotation->update([
            'retenu' => true
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Fournisseur sélectionné avec succès');
    }
}