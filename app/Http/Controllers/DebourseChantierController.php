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
        
        $contrat = Contrat::findOrFail($contratId);
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
            
            // Créer le détail du déboursé chantier
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
                'montant_total' => (($bpu->materiaux ?? 0) + ($bpu->main_oeuvre ?? 0) + ($bpu->materiel ?? 0)) * $ligne->quantite,
            ]);
        }

        // Mettre à jour le montant total
        $debourseChantier->updateTotal();

        return redirect()->route('debourses_chantier.details', $debourseChantier->id)
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
            'cout_unitaire_materiaux' => $request->cout_unitaire_materiaux,
            'cout_unitaire_main_oeuvre' => $request->cout_unitaire_main_oeuvre,
            'cout_unitaire_materiel' => $request->cout_unitaire_materiel,
        ]);
        
        // Recalculer les totaux
        $detail->calculerMontantTotal();
        $detail->debourseChantier->updateTotal();
        
        return redirect()->route('debourses_chantier.details', $detail->debourse_chantier_id)
            ->with('success', 'Détail mis à jour avec succès.');
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