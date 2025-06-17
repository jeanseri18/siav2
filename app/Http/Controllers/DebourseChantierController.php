<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\DQE;
use App\Models\DebourseChantier;
use App\Models\DebourseChantierDetail;
use Illuminate\Http\Request;

class DebourseChantierController extends Controller
{
    /**
     * Afficher les déboursés chantier d'un contrat
     */
    public function index()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $contrat = Contrat::with(['dqes.lignes'])->findOrFail($contratId);
        $deboursesChantier = DebourseChantier::where('contrat_id', $contratId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('debourses_chantier.index', compact('contrat', 'deboursesChantier'));
    }

    /**
     * Générer un déboursé chantier à partir d'un DQE
     */
    public function generate(Request $request, $dqeId)
    {
        $dqe = DQE::with('lignes.bpu', 'contrat.projet')->findOrFail($dqeId);
        $contratId = $dqe->contrat_id;
        $projetId = $dqe->contrat->projet_id;

        // Générer une référence unique
        $reference = 'DC_' . now()->format('YmdHis');

        // Créer le déboursé chantier
        $debourseChantier = DebourseChantier::create([
            'reference' => $reference,
            'projet_id' => $projetId,
            'contrat_id' => $contratId,
            'dqe_id' => $dqe->id,
            'montant_total' => 0,
            'statut' => 'brouillon',
        ]);

        // Remplir les détails pour chaque ligne du DQE
        foreach ($dqe->lignes as $ligne) {
            $bpu = $ligne->bpu;
            $bpu->updateDerivedValues(); // S'assurer que les valeurs dérivées sont à jour
            
            DebourseChantierDetail::create([
                'debourse_chantier_id' => $debourseChantier->id,
                'dqe_ligne_id' => $ligne->id,
                'section' => $ligne->section,
                'designation' => $ligne->designation,
                'unite' => $ligne->unite,
                'quantite' => $ligne->quantite,
                'cout_unitaire_materiaux' => $bpu->materiaux ?? 0,
                'cout_unitaire_main_oeuvre' => $bpu->main_oeuvre ?? 0,
                'cout_unitaire_materiel' => $bpu->materiel ?? 0,
                'total_materiaux' => ($bpu->materiaux ?? 0) * $ligne->quantite,
                'total_main_oeuvre' => ($bpu->main_oeuvre ?? 0) * $ligne->quantite,
                'total_materiel' => ($bpu->materiel ?? 0) * $ligne->quantite,
                'montant_total' => (($bpu->materiaux ?? 0) + ($bpu->materiel ?? 0)) * $ligne->quantite, // Déboursé chantier : matériaux + matériel seulement
            ]);
        }

        // Mettre à jour le montant total
        $debourseChantier->updateTotal();

        return redirect()->route('debourses_chantier.index', $contratId)
            ->with('success', 'Déboursé chantier généré avec succès.');
    }

    /**
     * Afficher les détails d'un déboursé chantier
     */
    public function details($id)
    {
        $debourseChantier = DebourseChantier::with(['details.dqeLigne', 'contrat', 'dqe'])->findOrFail($id);
        
        return view('debourses_chantier.details', compact('debourseChantier'));
    }

    /**
     * Mettre à jour un détail de déboursé chantier
     */
    public function updateDetail(Request $request, $id)
    {
        $detail = DebourseChantierDetail::findOrFail($id);
        
        // Vérifier que le déboursé est en brouillon
        if ($detail->debourseChantier->statut !== 'brouillon') {
            return response()->json(['error' => 'Impossible de modifier une ligne d\'un déboursé validé.'], 403);
        }
        
        $request->validate([
            'designation' => 'required|string|max:255',
            'quantite' => 'required|numeric|min:0',
            'cout_unitaire_materiaux' => 'nullable|numeric|min:0',
            'cout_unitaire_main_oeuvre' => 'nullable|numeric|min:0',
            'cout_unitaire_materiel' => 'nullable|numeric|min:0',
        ]);
        
        $detail->update([
            'designation' => $request->designation,
            'quantite' => $request->quantite,
            'cout_unitaire_materiaux' => $request->cout_unitaire_materiaux ?? 0,
            'cout_unitaire_main_oeuvre' => $request->cout_unitaire_main_oeuvre ?? 0,
            'cout_unitaire_materiel' => $request->cout_unitaire_materiel ?? 0,
        ]);
        
        // Recalculer les totaux
        $detail->total_materiaux = $detail->quantite * $detail->cout_unitaire_materiaux;
        $detail->total_main_oeuvre = $detail->quantite * $detail->cout_unitaire_main_oeuvre;
        $detail->total_materiel = $detail->quantite * $detail->cout_unitaire_materiel;
        $detail->montant_total = $detail->total_materiaux + $detail->total_materiel; // Déboursé chantier : matériaux + matériel seulement
        $detail->save();
        
        $detail->debourseChantier->updateTotal();
        
        return response()->json([
            'success' => true,
            'message' => 'Détail mis à jour avec succès.',
            'detail' => $detail
        ]);
    }

    /**
     * Dupliquer un détail de déboursé chantier
     */
    public function duplicateDetail($id)
    {
        $detail = DebourseChantierDetail::findOrFail($id);
        
        // Vérifier que le déboursé est en brouillon
        if ($detail->debourseChantier->statut !== 'brouillon') {
            return response()->json(['error' => 'Impossible de dupliquer une ligne d\'un déboursé validé.'], 403);
        }
        
        // Créer une copie du détail
        $newDetail = $detail->replicate();
        $newDetail->designation = $detail->designation . ' (Copie)';
        $newDetail->save();
        
        // Recalculer le total du déboursé
        $detail->debourseChantier->updateTotal();
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne dupliquée avec succès.',
            'detail' => $newDetail
        ]);
    }

    /**
     * Supprimer un détail de déboursé chantier
     */
    public function deleteDetail($id)
    {
        $detail = DebourseChantierDetail::findOrFail($id);
        
        // Vérifier que le déboursé est en brouillon
        if ($detail->debourseChantier->statut !== 'brouillon') {
            return response()->json(['error' => 'Impossible de supprimer une ligne d\'un déboursé validé.'], 403);
        }
        
        $debourseChantier = $detail->debourseChantier;
        $detail->delete();
        
        // Recalculer le total du déboursé
        $debourseChantier->updateTotal();
        
        return response()->json([
            'success' => true,
            'message' => 'Ligne supprimée avec succès.'
        ]);
    }

    /**
     * Exporter un déboursé chantier en PDF
     */
    public function export($id)
    {
        $debourseChantier = DebourseChantier::with(['details.dqeLigne', 'contrat', 'dqe'])->findOrFail($id);
        
        $pdf = \PDF::loadView('debourses_chantier.export', compact('debourseChantier'))
            ->setPaper('a4', 'landscape');
        
        return $pdf->download('Debourse_Chantier_' . $debourseChantier->contrat->ref_contrat . '_' . $debourseChantier->reference . '.pdf');
    }
}