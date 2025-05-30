<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\DQE;
use App\Models\Debourse;
use App\Models\DebourseDetail;
use Illuminate\Http\Request;

class DebourseController extends Controller
{
    /**
     * Afficher les déboursés d'un contrat
     */
 /**
 * Afficher les déboursés d'un contrat
 */
public function index()
{
    $contratId = session('contrat_id');
    
    if (!$contratId) {
        return redirect()->route('contrats.index')
            ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
    }
    
    $contrat = Contrat::findOrFail($contratId);
    $debourses = Debourse::where('contrat_id', $contratId)
        ->orderBy('type')
        ->get();
    
    return view('debourses.index', compact('contrat', 'debourses'));
}

    /**
     * Générer les déboursés à partir d'un DQE
     */
    public function generate(Request $request, $dqeId)
    {
        $dqe = DQE::with('lignes.bpu')->findOrFail($dqeId);
        $contratId = $dqe->contrat_id;

        // 1. Générer le déboursé sec
        $debourseSec = Debourse::create([
            'contrat_id' => $contratId,
            'dqe_id' => $dqe->id,
            'type' => 'sec',
            'montant_total' => 0,
            'statut' => 'brouillon',
        ]);

        // 2. Générer le déboursé main d'œuvre
        $debourseMO = Debourse::create([
            'contrat_id' => $contratId,
            'dqe_id' => $dqe->id,
            'type' => 'main_oeuvre',
            'montant_total' => 0,
            'statut' => 'brouillon',
        ]);

        // 3. Générer les frais de chantier
        $debourseFC = Debourse::create([
            'contrat_id' => $contratId,
            'dqe_id' => $dqe->id,
            'type' => 'frais_chantier',
            'montant_total' => 0,
            'statut' => 'brouillon',
        ]);

        // Remplir les détails pour chaque ligne du DQE
        foreach ($dqe->lignes as $ligne) {
            $bpu = $ligne->bpu;
            
            // Calculer les montants pour cette ligne
            $debourseSec_montant = ($bpu->materiaux + $bpu->main_oeuvre + $bpu->materiel) * $ligne->quantite;
            $debourseMO_montant = $bpu->main_oeuvre * $ligne->quantite;
            $debourseFC_montant = $bpu->frais_chantier * $ligne->quantite;
            
            // Créer les détails pour le déboursé sec
            DebourseDetail::create([
                'debourse_id' => $debourseSec->id,
                'dqe_ligne_id' => $ligne->id,
                'montant' => $debourseSec_montant,
            ]);
            
            // Créer les détails pour le déboursé main d'œuvre
            DebourseDetail::create([
                'debourse_id' => $debourseMO->id,
                'dqe_ligne_id' => $ligne->id,
                'montant' => $debourseMO_montant,
            ]);
            
            // Créer les détails pour les frais de chantier
            DebourseDetail::create([
                'debourse_id' => $debourseFC->id,
                'dqe_ligne_id' => $ligne->id,
                'montant' => $debourseFC_montant,
            ]);
        }

        // Mettre à jour les totaux
        $debourseSec->updateTotal();
        $debourseMO->updateTotal();
        $debourseFC->updateTotal();

        return redirect()->route('debourses.index', $contratId)
            ->with('success', 'Déboursés générés avec succès.');
    }

    /**
     * Afficher les détails d'un déboursé
     */
    public function details($id)
    {
        $debourse = Debourse::with(['details.dqeLigne.bpu', 'contrat', 'dqe'])->findOrFail($id);
        
        return view('debourses.details', compact('debourse'));
    }

    /**
     * Exporter un déboursé en PDF
     */
    public function export($id)
    {
        $debourse = Debourse::with(['details.dqeLigne.bpu', 'contrat', 'dqe'])->findOrFail($id);
        
        $typeLabel = [
            'sec' => 'Déboursé Sec',
            'main_oeuvre' => 'Déboursé Main d\'Œuvre',
            'frais_chantier' => 'Frais de Chantier'
        ][$debourse->type];
        
        $pdf = \PDF::loadView('debourses.export', compact('debourse', 'typeLabel'));
        
        return $pdf->download($typeLabel . '_' . $debourse->contrat->ref_contrat . '.pdf');
    }
}