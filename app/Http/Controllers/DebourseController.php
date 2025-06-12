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
    
    $contrat = Contrat::with(['projet', 'dqes.lignes'])->findOrFail($contratId);
    $debourses = Debourse::where('contrat_id', $contratId)
        ->orderBy('type')
        ->get();
    
    return view('debourses.index', compact('contrat', 'debourses'));
}

    /**
     * Afficher les déboursés secs
     */
    public function debourseSec()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $contrat = Contrat::with(['projet', 'dqes.lignes'])->findOrFail($contratId);
        $debourses = Debourse::where('contrat_id', $contratId)
            ->where('type', 'sec')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('debourses.debourse_sec', compact('contrat', 'debourses'));
    }

    /**
     * Afficher les déboursés main d'œuvre
     */
    public function debourseMainOeuvre()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $contrat = Contrat::with(['projet', 'dqes.lignes'])->findOrFail($contratId);
        $debourses = Debourse::where('contrat_id', $contratId)
            ->where('type', 'main_oeuvre')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('debourses.debourse_main_oeuvre', compact('contrat', 'debourses'));
    }

    /**
     * Afficher les frais de chantier
     */
    public function fraisChantier()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $contrat = Contrat::with(['projet', 'dqes.lignes'])->findOrFail($contratId);
        $debourses = Debourse::where('contrat_id', $contratId)
            ->where('type', 'frais_chantier')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('debourses.frais_chantier', compact('contrat', 'debourses'));
    }

    /**
     * Générer les déboursés à partir d'un DQE
     */
    public function generate(Request $request, $dqeId)
    {
        $dqe = DQE::with(['lignes.bpu', 'contrat.projet'])->findOrFail($dqeId);
        $contratId = $dqe->contrat_id;
        $projetId = $dqe->contrat->projet_id;

        // Générer les références uniques
        $newReferenceds = 'DS_' . now()->format('YmdHis');
        $newReferencedsmo = 'DSM_' . now()->format('YmdHis');
        $newReferencefc = 'FC_' . now()->format('YmdHis');

        // 1. Générer le déboursé sec
        $debourseSec = Debourse::create([
            'reference' => $newReferenceds,
            'projet_id' => $projetId,
            'contrat_id' => $contratId,
            'dqe_id' => $dqe->id,
            'type' => 'sec',
            'montant_total' => 0,
            'statut' => 'brouillon',
        ]);

        // 2. Générer le déboursé main d'œuvre
        $debourseMO = Debourse::create([
            'reference' => $newReferencedsmo,
            'projet_id' => $projetId,
            'contrat_id' => $contratId,
            'dqe_id' => $dqe->id,
            'type' => 'main_oeuvre',
            'montant_total' => 0,
            'statut' => 'brouillon',
        ]);

        // 3. Générer les frais de chantier
        $debourseFC = Debourse::create([
            'reference' => $newReferencefc,
            'projet_id' => $projetId,
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

    /**
     * Mettre à jour un détail de déboursé
     */
    public function updateDetail(Request $request, $id)
    {
        $detail = DebourseDetail::findOrFail($id);
        $debourse = $detail->debourse;
        
        // Vérifier que le déboursé est en brouillon
        if ($debourse->statut !== 'brouillon') {
            return redirect()->back()->withErrors(['error' => 'Seuls les déboursés en brouillon peuvent être modifiés.']);
        }
        
        // Mettre à jour les champs
        if ($request->has('designation')) {
            // La désignation est stockée dans la ligne DQE, pas dans le détail
            $detail->dqeLigne->designation = $request->designation;
            $detail->dqeLigne->save();
        }
        
        if ($request->has('quantite')) {
            $detail->dqeLigne->quantite = $request->quantite;
            $detail->dqeLigne->save();
        }
        
        // Mettre à jour les coûts unitaires si présents
        if ($request->has('cout_unitaire_materiaux')) {
            $detail->cout_unitaire_materiaux = $request->cout_unitaire_materiaux;
        }
        
        if ($request->has('cout_unitaire_main_oeuvre')) {
            $detail->cout_unitaire_main_oeuvre = $request->cout_unitaire_main_oeuvre;
        }
        
        if ($request->has('cout_unitaire_materiel')) {
            $detail->cout_unitaire_materiel = $request->cout_unitaire_materiel;
        }
        
        // Calculer les totaux
        $quantite = $detail->dqeLigne->quantite;
        
        if ($detail->cout_unitaire_materiaux) {
            $detail->total_materiaux = $detail->cout_unitaire_materiaux * $quantite;
        }
        
        if ($detail->cout_unitaire_main_oeuvre) {
            $detail->total_main_oeuvre = $detail->cout_unitaire_main_oeuvre * $quantite;
        }
        
        if ($detail->cout_unitaire_materiel) {
            $detail->total_materiel = $detail->cout_unitaire_materiel * $quantite;
        }
        
        // Calculer le montant total du détail
        if ($debourse->type == 'sec') {
            $detail->montant = ($detail->total_materiaux ?? 0) + ($detail->total_main_oeuvre ?? 0) + ($detail->total_materiel ?? 0);
        } elseif ($debourse->type == 'main_oeuvre') {
            $detail->montant = $detail->total_main_oeuvre ?? ($detail->dqeLigne->bpu->main_oeuvre * $quantite);
        } else { // frais_chantier
            $detail->montant = $detail->dqeLigne->bpu->frais_chantier * $quantite;
        }
        
        $detail->save();
        
        // Mettre à jour le total du déboursé
        $debourse->updateTotal();
        
        return redirect()->back()->with('success', 'Détail mis à jour avec succès.');
    }
}