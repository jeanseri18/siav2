<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\DQE;
use App\Models\DebourseChantierParent;
use App\Models\Rubrique;
use App\Models\DebourseChantier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebourseChantierController extends Controller
{
    const STATUT_BROUILLON = 'brouillon';
    const STATUT_VALIDE = 'valide';
    const STATUT_REFUSE = 'refuse';

    public function index(Contrat $contrat)
    {
        session(['contrat_id' => $contrat->id]);
        
        $debourseChantierParents = DebourseChantierParent::where('contrat_id', $contrat->id)
            ->with(['dqe', 'lignes'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('contrats.debourse-chantier.index', compact('contrat', 'debourseChantierParents'));
    }

    public function show(Contrat $contrat, DebourseChantierParent $debourseChantier)
    {
        session(['contrat_id' => $contrat->id]);
        
        $parent = $debourseChantier->load(['lignes.rubrique.sousCategorie.categorie', 'dqe', 'contrat']);
        
        return view('contrats.debourse-chantier.show', compact('contrat', 'parent'));
    }

    public function generate(Request $request, DQE $dqe)
    {
        $contrat = $dqe->contrat;
        
        try {
            DB::beginTransaction();

            // Charger explicitement les lignes du DQE
            $dqe->load('lignes');

            // Vérifier que le DQE a des lignes
            if (!$dqe->lignes || $dqe->lignes->isEmpty()) {
                throw new \Exception('Le DQE ne contient aucune ligne à générer');
            }

            // Créer le parent avec montant à 0 (sans générer les lignes)
            $parent = DebourseChantierParent::create([
                'ref' => 'DC-' . $contrat->reference . '-' . time(),
                'montant_total' => 0,
                'statut' => self::STATUT_BROUILLON,
                'dqe_id' => $dqe->id,
                'contrat_id' => $contrat->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Déboursé chantier parent créé avec succès (sans lignes)',
                'redirect_url' => route('contrats.debourse-chantier.show', [$contrat, $parent])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du déboursé chantier : ' . $e->getMessage()
            ], 422);
        }
    }

    public function showParent(Contrat $contrat, DebourseChantierParent $parent)
    {
        session(['contrat_id' => $contrat->id]);
        
        $parent = $parent->load(['lignes.rubrique.sousCategorie.categorie', 'dqe', 'contrat']);
        
        return view('contrats.debourse-chantier.show', compact('contrat', 'parent'));
    }

    public function storeLigne(Request $request, Contrat $contrat, DebourseChantierParent $parent)
    {
        try {
            $validated = $request->validate([
                'rubrique_id' => 'required|exists:rubriques,id',
                'designation' => 'required|string|max:255',
                'unite' => 'required|string|max:50',
                'quantite' => 'required|numeric|min:0',
                'pu_ht' => 'required|numeric|min:0',
                'montant_ht' => 'required|numeric|min:0',
            ]);

            // Créer la nouvelle ligne de déboursé
            $ligne = DebourseChantier::create([
                'parent_id' => $parent->id,
                'contrat_id' => $contrat->id,
                'rubrique_id' => $validated['rubrique_id'],
                'designation' => $validated['designation'],
                'unite' => $validated['unite'],
                'quantite' => $validated['quantite'],
                'pu_ht' => $validated['pu_ht'],
                'montant_ht' => $validated['montant_ht'],
            ]);

            // Recalculer le montant total du parent
            $montantTotal = DebourseChantier::where('parent_id', $parent->id)->sum('montant_ht');
            $parent->update(['montant_total' => $montantTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Ligne ajoutée avec succès!',
                'ligne' => $ligne
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la ligne : ' . $e->getMessage()
            ], 422);
        }
    }

    public function updateStatut(DebourseChantierParent $debourseChantier, Request $request)
    {
        $request->validate([
            'statut' => 'required|in:' . implode(',', [self::STATUT_BROUILLON, self::STATUT_VALIDE, self::STATUT_REFUSE])
        ]);

        $debourseChantier->update(['statut' => $request->statut]);

        return redirect()->back()->with('success', 'Statut mis à jour avec succès');
    }

    public function destroyLigne(Contrat $contrat, DebourseChantierParent $parent, DebourseChantier $ligne)
    {
        try {
            // Vérifier que la ligne appartient bien au parent et au contrat
            // if ($ligne->parent_id !== $parent->id || $ligne->contrat_id !== $contrat->id) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Ligne non trouvée ou accès non autorisé'
            //     ], 404);
            // }

            // Supprimer la ligne
            $ligne->delete();

            // Recalculer le montant total du parent
            $montantTotal = DebourseChantier::where('parent_id', $parent->id)->sum('montant_ht');
            $parent->update(['montant_total' => $montantTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Ligne supprimée avec succès!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la ligne : ' . $e->getMessage()
            ], 422);
        }
    }
}