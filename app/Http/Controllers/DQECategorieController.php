<?php

namespace App\Http\Controllers;

use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use App\Models\Rubrique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DQECategorieController extends Controller
{
    /**
     * Créer une nouvelle catégorie DQE manuelle
     */
    public function storeCategorie(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné.'
            ], 400);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'id_qe' => 'nullable|exists:dqes,id',
        ]);
        
        $categorie = CategorieRubrique::create([
            'nom' => $request->nom,
            'type' => 'dqe_manuel',
            'contrat_id' => $contratId,
            'id_qe' => $request->id_qe,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Catégorie créée avec succès.',
            'categorie' => [
                'id' => $categorie->id,
                'nom' => $categorie->nom
            ]
        ]);
    }
    
    /**
     * Créer une nouvelle sous-catégorie DQE manuelle
     */
    public function storeSousCategorie(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné.'
            ], 400);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categorierubriques,id',
            'id_qe' => 'nullable|exists:dqes,id',
        ]);
        
        $sousCategorie = SousCategorieRubrique::create([
            'nom' => $request->nom,
            'type' => 'dqe_manuel',
            'id_session' => $request->categorie_id,
            'contrat_id' => $contratId,
            'id_qe' => $request->id_qe,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Sous-catégorie créée avec succès.',
            'sousCategorie' => [
                'id' => $sousCategorie->id,
                'nom' => $sousCategorie->nom,
                'categorie_id' => $sousCategorie->id_session
            ]
        ]);
    }
    
    /**
     * Créer une nouvelle rubrique DQE manuelle
     */
    public function storeRubrique(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné.'
            ], 400);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'sous_categorie_id' => 'required|exists:souscategorierubriques,id',
            'id_qe' => 'nullable|exists:dqes,id',
        ]);
        
        $rubrique = Rubrique::create([
            'nom' => $request->nom,
            'type' => 'dqe_manuel',
            'id_soussession' => $request->sous_categorie_id,
            'contrat_id' => $contratId,
            'id_qe' => $request->id_qe,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Rubrique créée avec succès.',
            'rubrique' => [
                'id' => $rubrique->id,
                'nom' => $rubrique->nom,
                'sous_categorie_id' => $rubrique->id_soussession
            ]
        ]);
    }
    
    /**
     * Récupérer les catégories pour un contrat
     */
    public function getCategories()
    {
        $contratId = session('contrat_id');
        $dqeId = session('dqe_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné.'
            ], 400);
        }
        
        $query = CategorieRubrique::where('contrat_id', $contratId)
            ->where('type', 'dqe_manuel');
            
        if ($dqeId) {
            $query->where('id_qe', $dqeId);
        }
        
        $categories = $query->orderBy('nom')->get();
            
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
    
    /**
     * Récupérer les sous-catégories pour une catégorie
     */
    public function getSousCategories($contratId, $categorieId)
    {
        $dqeId = session('dqe_id');
        
        $query = SousCategorieRubrique::where('id_session', $categorieId)
            ->where('type', 'dqe_manuel');
            
        if ($dqeId) {
            $query->where('id_qe', $dqeId);
        }
        
        $sousCategories = $query->orderBy('nom')->get();
            
        return response()->json([
            'success' => true,
            'sousCategories' => $sousCategories
        ]);
    }
    
    /**
     * Récupérer les rubriques pour un contrat
     */
    public function getRubriques($sousCategorieId)
    {
       $dqeId = session('dqe_id');
        
        $query = Rubrique::where('id_soussession', $sousCategorieId)
            ->where('type', 'dqe_manuel');
            
        if ($dqeId) {
            $query->where('id_qe', $dqeId);
        }
        
        $Rubriques = $query->orderBy('nom')->get();
            
        return response()->json([
            'success' => true,
            'Rubriques' => $Rubriques
        ]);
    }


    
    
    /**
     * Modifier une catégorie DQE manuelle
     */
    public function updateCategorie(Request $request, $id)
    {
        $categorie = CategorieRubrique::find($id);
        
        if (!$categorie) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie non trouvée.'
            ], 404);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'id_qe' => 'nullable|exists:dqes,id',
        ]);
        
        $categorie->update([
            'nom' => $request->nom,
            'id_qe' => $request->id_qe,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Catégorie modifiée avec succès.',
            'categorie' => [
                'id' => $categorie->id,
                'nom' => $categorie->nom,
                'id_qe' => $categorie->id_qe
            ]
        ]);
    }
    
    /**
     * Supprimer une catégorie DQE manuelle
     */
    public function deleteCategorie($id)
    {
        $categorie = CategorieRubrique::find($id);
        
        if (!$categorie) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie non trouvée.'
            ], 404);
        }
        
        // Vérifier s'il y a des sous-catégories
        if ($categorie->sousCategories()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer la catégorie car elle contient des sous-catégories.'
            ], 400);
        }
        
        $categorie->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès.'
        ]);
    }
    
    /**
     * Modifier une sous-catégorie DQE manuelle
     */
    public function updateSousCategorie(Request $request, $id)
    {
        $sousCategorie = SousCategorieRubrique::find($id);
        
        if (!$sousCategorie) {
            return response()->json([
                'success' => false,
                'message' => 'Sous-catégorie non trouvée.'
            ], 404);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categorierubriques,id',
            'id_qe' => 'nullable|exists:dqes,id',
        ]);
        
        $sousCategorie->update([
            'nom' => $request->nom,
            'id_session' => $request->categorie_id,
            'id_qe' => $request->id_qe,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Sous-catégorie modifiée avec succès.',
            'sousCategorie' => [
                'id' => $sousCategorie->id,
                'nom' => $sousCategorie->nom,
                'categorie_id' => $sousCategorie->id_session,
                'id_qe' => $sousCategorie->id_qe
            ]
        ]);
    }
    
    /**
     * Supprimer une sous-catégorie DQE manuelle
     */
    public function deleteSousCategorie($id)
    {
        $sousCategorie = SousCategorieRubrique::find($id);
        
        if (!$sousCategorie) {
            return response()->json([
                'success' => false,
                'message' => 'Sous-catégorie non trouvée.'
            ], 404);
        }
        
        // Vérifier s'il y a des rubriques
        if ($sousCategorie->rubriques()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer la sous-catégorie car elle contient des rubriques.'
            ], 400);
        }
        
        $sousCategorie->delete();
        
        return response()->json([
            'success' => true,
                'message' => 'Sous-catégorie supprimée avec succès.'
        ]);
    }
    
    /**
     * Modifier une rubrique DQE manuelle
     */
    public function updateRubrique(Request $request, $id)
    {
        $rubrique = Rubrique::find($id);
        
        if (!$rubrique) {
            return response()->json([
                'success' => false,
                'message' => 'Rubrique non trouvée.'
            ], 404);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'sous_categorie_id' => 'required|exists:souscategorierubriques,id',
            'id_qe' => 'nullable|exists:dqes,id',
        ]);
        
        $rubrique->update([
            'nom' => $request->nom,
            'id_soussession' => $request->sous_categorie_id,
            'id_qe' => $request->id_qe,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Rubrique modifiée avec succès.',
            'rubrique' => [
                'id' => $rubrique->id,
                'nom' => $rubrique->nom,
                'sous_categorie_id' => $rubrique->id_soussession,
                'id_qe' => $rubrique->id_qe
            ]
        ]);
    }
    
    /**
     * Supprimer une rubrique DQE manuelle
     */
    public function deleteRubrique($id)
    {
        $rubrique = Rubrique::find($id);
        
        if (!$rubrique) {
            return response()->json([
                'success' => false,
                'message' => 'Rubrique non trouvée.'
            ], 404);
        }
        
        // Vérifier s'il y a des BPU
        if ($rubrique->bpus()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer la rubrique car elle contient des BPU.'
            ], 400);
        }
        
        $rubrique->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Rubrique supprimée avec succès.'
        ]);
    }

    /**
     * Récupérer les lignes DQE pour une rubrique
     */
    public function getDQELignes($rubriqueId)
    {
        $dqeId = session('dqe_id');
        
        if (!$dqeId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun DQE sélectionné.'
            ], 400);
        }
        
        // Récupérer les lignes DQE pour cette rubrique et ce DQE
        $lignes = \App\Models\DQELigne::with(['dqe'])
            ->where('id_rubrique', $rubriqueId)
            ->where('dqe_id', $dqeId)
            ->orderBy('code')
            ->get();
            
        return response()->json([
            'success' => true,
            'lignes' => $lignes
        ]);
    }

    /**
     * Créer une nouvelle ligne DQE
     */
    public function storeDQELigne(Request $request)
    {
        $dqeId = session('dqe_id');
        
        if (!$dqeId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun DQE sélectionné.'
            ], 400);
        }
        
        $request->validate([
            'id_rubrique' => 'required|exists:rubriques,id',
            'code' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'quantite' => 'required|numeric|min:0',
            'unite' => 'required|string|max:50',
            'pu_ht' => 'required|numeric|min:0',
        ]);
        
        // Vérifier que la rubrique existe
        $rubrique = \App\Models\Rubrique::find($request->id_rubrique);
        if (!$rubrique) {
            return response()->json([
                'success' => false,
                'message' => 'Rubrique non trouvée.'
            ], 404);
        }
        
        // Créer la ligne DQE
        $ligne = \App\Models\DQELigne::create([
            'dqe_id' => $dqeId,
            'id_rubrique' => $request->id_rubrique,
            'code' => $request->code,
            'section' => $request->section,
            'designation' => $request->designation,
            'quantite' => $request->quantite,
            'unite' => $request->unite,
            'pu_ht' => $request->pu_ht,
            'montant_ht' => $request->quantite * $request->pu_ht,
        ]);
        
        // Charger les relations pour la réponse
        $ligne->load(['dqe']);
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne DQE créée avec succès.',
            'ligne' => $ligne
        ]);
    }

    /**
     * Modifier une ligne DQE
     */
    public function updateDQELigne(Request $request, $id)
    {
        $ligne = \App\Models\DQELigne::find($id);
        
        if (!$ligne) {
            return response()->json([
                'success' => false,
                'message' => 'Ligne DQE non trouvée.'
            ], 404);
        }
        
        $request->validate([
            'code' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'designation' => 'required|string|max:255',
            'quantite' => 'required|numeric|min:0',
            'unite' => 'required|string|max:50',
            'pu_ht' => 'required|numeric|min:0',
        ]);
        
        // Mettre à jour la ligne
        $ligne->update([
            'code' => $request->code,
            'section' => $request->section,
            'designation' => $request->designation,
            'quantite' => $request->quantite,
            'unite' => $request->unite,
            'pu_ht' => $request->pu_ht,
            'montant_ht' => $request->quantite * $request->pu_ht,
        ]);
        
        // Recharger les relations pour la réponse
        $ligne->load(['dqe']);
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne DQE modifiée avec succès.',
            'ligne' => $ligne
        ]);
    }

    /**
     * Supprimer une ligne DQE
     */
    public function deleteDQELigne($id)
    {
        $ligne = \App\Models\DQELigne::find($id);
        
        if (!$ligne) {
            return response()->json([
                'success' => false,
                'message' => 'Ligne DQE non trouvée.'
            ], 404);
        }
        
        // Vérifier s'il y a des détails de déboursé
        // REMOVED: debourseDetails reference deleted
        
        // Aucune vérification nécessaire - les déboursés ont été supprimés
        
        $ligne->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne DQE supprimée avec succès.'
        ]);
    }
}