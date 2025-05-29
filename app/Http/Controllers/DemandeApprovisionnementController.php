<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Article;
use App\Models\Reference;
use App\Models\DemandeApprovisionnement;
use App\Models\LigneDemandeApprovisionnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeApprovisionnementController extends Controller
{
    public function index()
    {
        $demandes = DemandeApprovisionnement::with(['user', 'projet', 'lignes.article'])->get();
        return view('demande_approvisionnements.index', compact('demandes'));
    }

    public function create()
    {
        $projets = Projet::all();
        $articles = Article::with('categorie')->get();
        return view('demande_approvisionnements.create', compact('projets', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_demande' => 'required|date',
            'projet_id' => 'nullable|exists:projets,id',
            'description' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite_demandee' => 'required|array',
            'quantite_demandee.*' => 'integer|min:1'
        ]);

        // Générer la référence
        $lastReference = Reference::where('nom', 'Code Demande Approvisionnement')
            ->latest('created_at')
            ->first();
        
        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'DA_0000';
        $newReference = 'DA_' . now()->format('YmdHis');

        // Créer la demande
        $demande = DemandeApprovisionnement::create([
            'reference' => $newReference,
            'date_demande' => $request->date_demande,
            'projet_id' => $request->projet_id,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'statut' => 'en attente'
        ]);

        // Ajouter les lignes de la demande
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite_demandee[$i] > 0) {
                LigneDemandeApprovisionnement::create([
                    'demande_approvisionnement_id' => $demande->id,
                    'article_id' => $request->article_id[$i],
                    'quantite_demandee' => $request->quantite_demandee[$i],
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        // Enregistrer la référence
        Reference::create([
            'nom' => 'Code Demande Approvisionnement',
            'ref' => $newReference
        ]);

        return redirect()->route('demande-approvisionnements.index')
            ->with('success', 'Demande d\'approvisionnement créée avec succès');
    }

    public function show(DemandeApprovisionnement $demandeApprovisionnement)
    {
        $demandeApprovisionnement->load(['user', 'projet', 'approbateur', 'lignes.article']);
        return view('demande_approvisionnements.show', compact('demandeApprovisionnement'));
    }

    public function edit(DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en attente');
        }

        $projets = Projet::all();
        $articles = Article::with('categorie')->get();
        $demandeApprovisionnement->load(['lignes.article']);
        
        return view('demande_approvisionnements.edit', compact('demandeApprovisionnement', 'projets', 'articles'));
    }

    public function update(Request $request, DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en attente');
        }

        $request->validate([
            'date_demande' => 'required|date',
            'projet_id' => 'nullable|exists:projets,id',
            'description' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite_demandee' => 'required|array',
            'quantite_demandee.*' => 'integer|min:1'
        ]);

        // Mettre à jour la demande
        $demandeApprovisionnement->update([
            'date_demande' => $request->date_demande,
            'projet_id' => $request->projet_id,
            'description' => $request->description
        ]);

        // Supprimer les anciennes lignes
        $demandeApprovisionnement->lignes()->delete();

        // Ajouter les nouvelles lignes
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite_demandee[$i] > 0) {
                LigneDemandeApprovisionnement::create([
                    'demande_approvisionnement_id' => $demandeApprovisionnement->id,
                    'article_id' => $request->article_id[$i],
                    'quantite_demandee' => $request->quantite_demandee[$i],
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
            ->with('success', 'Demande d\'approvisionnement mise à jour avec succès');
    }

    public function destroy(DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.index')
                ->with('error', 'Impossible de supprimer une demande qui n\'est pas en attente');
        }

        // Supprimer les lignes de la demande
        $demandeApprovisionnement->lignes()->delete();
        
        // Supprimer la demande
        $demandeApprovisionnement->delete();

        return redirect()->route('demande-approvisionnements.index')
            ->with('success', 'Demande d\'approvisionnement supprimée avec succès');
    }

    public function approve(Request $request, DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Cette demande ne peut pas être approuvée');
        }

        $request->validate([
            'quantite_approuvee' => 'required|array',
            'quantite_approuvee.*' => 'integer|min:0'
        ]);

        // Mettre à jour les quantités approuvées
        foreach ($demandeApprovisionnement->lignes as $index => $ligne) {
            $ligne->update([
                'quantite_approuvee' => $request->quantite_approuvee[$index]
            ]);
        }

        // Mettre à jour le statut de la demande
        $demandeApprovisionnement->update([
            'statut' => 'approuvée',
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
            ->with('success', 'Demande d\'approvisionnement approuvée avec succès');
    }

    public function reject(Request $request, DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Cette demande ne peut pas être rejetée');
        }

        $request->validate([
            'motif_rejet' => 'required|string'
        ]);

        // Mettre à jour le statut de la demande
        $demandeApprovisionnement->update([
            'statut' => 'rejetée',
            'motif_rejet' => $request->motif_rejet,
            'approved_by' => Auth::id()
        ]);

       return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
            ->with('success', 'Demande d\'approvisionnement rejetée');
    }
}