<?php

namespace App\Http\Controllers;

use App\Models\DQELigne;
use Illuminate\Http\Request;

class DQELigneController extends Controller
{
    /**
     * Créer une nouvelle ligne DQE
     */
    public function store(Request $request)
    {
        $contratId = session('contrat_id');
        $dqeId = session('dqe_id');
        
        if (!$contratId || !$dqeId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat ou DQE sélectionné.'
            ], 400);
        }
        
        $request->validate([
            'rubrique_id' => 'required|exists:rubriques,id',
            'designation' => 'required|string|max:255',
            'unite' => 'required|string|max:50',
            'quantite' => 'required|numeric|min:0',
            'pu_ht' => 'required|numeric|min:0',
        ]);
        
        $ligne = DQELigne::create([
            'dqe_id' => $dqeId,
            'rubrique_id' => $request->rubrique_id,
            'designation' => $request->designation,
            'unite' => $request->unite,
            'quantite' => $request->quantite,
            'pu_ht' => $request->pu_ht,
            'montant_ht' => $request->quantite * $request->pu_ht,
        ]);
        
        // Mettre à jour les totaux du DQE
        $ligne->dqe->updateTotals();
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne créée avec succès.',
            'ligne' => $ligne->load(['rubrique', 'dqe'])
        ]);
    }
    
    /**
     * Modifier une ligne DQE
     */
    public function update(Request $request, $id)
    {
        $ligne = DQELigne::find($id);
        
        if (!$ligne) {
            return response()->json([
                'success' => false,
                'message' => 'Ligne non trouvée.'
            ], 404);
        }
        
        $request->validate([
            'rubrique_id' => 'nullable|exists:rubriques,id',
            'designation' => 'required|string|max:255',
            'unite' => 'required|string|max:50',
            'quantite' => 'required|numeric|min:0',
            'pu_ht' => 'required|numeric|min:0',
        ]);
        
        $updateData = [
            'designation' => $request->designation,
            'unite' => $request->unite,
            'quantite' => $request->quantite,
            'pu_ht' => $request->pu_ht,
            'montant_ht' => $request->quantite * $request->pu_ht,
        ];
        
        // Ne mettre à jour la rubrique que si elle est fournie
        if ($request->has('rubrique_id') && $request->rubrique_id) {
            $updateData['rubrique_id'] = $request->rubrique_id;
        }
        
        $ligne->update($updateData);
        
        // Mettre à jour les totaux du DQE
        $ligne->dqe->updateTotals();
        
        // Recharger le DQE avec les totaux mis à jour
        $dqe = $ligne->dqe->fresh();
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne modifiée avec succès.',
            'ligne' => $ligne->load(['rubrique', 'dqe']),
            'dqe' => [
                'montant_total_ht' => $dqe->montant_total_ht
            ]
        ]);
    }
    
    /**
     * Supprimer une ligne DQE
     */
    public function destroy($id)
    {
        $ligne = DQELigne::find($id);
        
        if (!$ligne) {
            return response()->json([
                'success' => false,
                'message' => 'Ligne non trouvée.'
            ], 404);
        }
        
        $dqe = $ligne->dqe;
        $ligne->delete();
        
        // Mettre à jour les totaux du DQE
        $dqe->updateTotals();
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne supprimée avec succès.'
        ]);
    }
    
    /**
     * Récupérer les lignes pour un DQE
     */
    public function getLignesByDqe($dqeId)
    {
        $lignes = DQELigne::where('dqe_id', $dqeId)
            ->with(['rubrique.sousCategorie.categorie'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'lignes' => $lignes
        ]);
    }
    
    /**
     * Récupérer une ligne spécifique
     */
    public function show($id)
    {
        $ligne = DQELigne::with(['rubrique.sousCategorie.categorie', 'dqe'])->find($id);
        
        if (!$ligne) {
            return response()->json([
                'success' => false,
                'message' => 'Ligne non trouvée.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'ligne' => $ligne
        ]);
    }
    
    /**
     * Afficher le formulaire d'édition d'une ligne DQE
     */
    public function edit($id)
    {
        $ligne = DQELigne::with(['rubrique.sousCategorie.categorie', 'dqe'])->find($id);
        
        if (!$ligne) {
            return redirect()->back()->with('error', 'Ligne non trouvée.');
        }
        
        // Récupérer les données nécessaires pour le formulaire
        $dqe = $ligne->dqe;
        $contrat = $dqe->contrat;
        
        return view('dqe.lignes.edit', compact('ligne', 'dqe', 'contrat'));
    }
}