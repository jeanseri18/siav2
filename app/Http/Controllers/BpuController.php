<?php

namespace App\Http\Controllers;

use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use App\Models\Rubrique;
use App\Models\Bpu;
use App\Models\UniteMesure;
use App\Models\CategoriesBpu;
use Illuminate\Http\Request;

class BpuController extends Controller
{
    public function index()
    {
        $contratId = session('contrat_id');
        
        $rubriques = Rubrique::all();
        $uniteMesures = UniteMesure::all();
        
        // Récupérer seulement les catégories qui ont des BPU utilitaires
        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus' => function($query) {
                $query->where('contrat_id', null); // BPU utilitaires
            }
        ])->whereHas('sousCategories.rubriques.bpus', function($query) {
            $query->where('contrat_id', null);
        })->get();

        if ($contratId) {
            // Mode contrat : récupérer seulement les catégories qui ont des BPU de contrat
            $categoriesContrat = CategorieRubrique::with([
                'sousCategories.rubriques.bpus' => function($query) use ($contratId) {
                    $query->where('contrat_id', $contratId);
                }
            ])->whereHas('sousCategories.rubriques.bpus', function($query) use ($contratId) {
                $query->where('contrat_id', $contratId);
            })->get();
            
            return view('bpu.index', compact('categories', 'categoriesContrat', 'rubriques', 'uniteMesures', 'contratId'));
        } else {
            // Mode utilitaires : afficher uniquement les BPU utilitaires
            return view('bpu.index', compact('categories', 'rubriques', 'uniteMesures', 'contratId'));
        }
    }

    public function indexuntil()
    {
        $uniteMesures = UniteMesure::all();

        // Récupérer seulement les catégories qui ont des BPU
        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus'
        ])->whereHas('sousCategories.rubriques.bpus')->get();

        return view('bpu.until', compact('categories', 'uniteMesures'));
    }
    
    
    public function print()
    {
        $uniteMesures = UniteMesure::all();

        // Récupérer seulement les catégories qui ont des BPU
        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus'
        ])->whereHas('sousCategories.rubriques.bpus')->get();

        return view('bpu.print', compact('categories', 'uniteMesures'));
    }
    
    public function create()
    {
        $rubriques = Rubrique::all();
        $uniteMesures = UniteMesure::all();
        
        return view('bpu.create', compact('rubriques', 'uniteMesures'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'designation' => 'required',
    //         'qte' => 'required|numeric',
    //         'materiaux' => 'required|numeric',
    //         'unite' => 'required',
    //         'main_oeuvre' => 'required|numeric',
    //         'materiel' => 'required|numeric',
    //         'id_rubrique' => 'required|exists:rubriques,id'
    //     ]);

    //     // 🔢 Calculs automatiques
    //     $ds = $request->materiaux + $request->main_oeuvre + $request->materiel;
    //     $fc = $ds * 0.30; // 30%
    //     $fg = ($ds + $fc) * 0.15; // 15%
    //     $mn = ($ds + $fc + $fg) * 0.15; // 15%
    //     $pu_ht = $ds + $fc + $fg + $mn;
    //     $pu_ttc = $pu_ht * 1.18; // TVA 18%

    //     // 💾 Création du BPU
    //  $bpu =     Bpu::create([
    //         'designation' => $request->designation,
    //         'qte' => $request->qte,
    //         'materiaux' => $request->materiaux,
    //         'main_oeuvre' => $request->main_oeuvre,
    //         'materiel' => $request->materiel,
    //         'unite' => $request->unite,
    //         'debourse_sec' => $ds,
    //         'frais_chantier' => $fc,
    //         'frais_general' => $fg,
    //         'marge_nette' => $mn,
    //         'pu_ht' => $pu_ht,
    //         'pu_ttc' => $pu_ttc,
    //         'id_rubrique' => $request->id_rubrique,
    //     ]);
    // $bpu->updateDerivedValues();

    //     return redirect()->route('bpu.index')->with('success', 'BPU ajouté avec succès.');
    // }

    public function edit(Bpu $bpu)
    {
        $uniteMesures = UniteMesure::all();
        $rubriques = Rubrique::all();
        
        return view('bpu.edit', compact('bpu', 'uniteMesures', 'rubriques'));
    }

    // public function update(Request $request, $id)
    // {
    //     $bpu = Bpu::findOrFail($id);
    
    //     // 🔍 Validation des données
    //     $request->validate([
    //         'designation' => 'required',
    //         'qte' => 'required|numeric',
    //         'materiaux' => 'required|numeric',
    //         'unite' => 'required',
    //         'main_oeuvre' => 'required|numeric',
    //         'materiel' => 'required|numeric',
    //     ]);
    
    //     // 🔢 Calculs auto
    //     $ds = $request->materiaux + $request->main_oeuvre + $request->materiel;
    //     $fc = $ds * 0.30;
    //     $fg = ($ds + $fc) * 0.15;
    //     $mn = ($ds + $fc + $fg) * 0.15;
    //     $pu_ht = $ds + $fc + $fg + $mn;
    //     $pu_ttc = $pu_ht * 1.18;
    
    //     // 🔁 Update de la ligne BPU
    //     $bpu->update([
    //         'designation' => $request->designation,
    //         'qte' => $request->qte,
    //         'materiaux' => $request->materiaux,
    //         'main_oeuvre' => $request->main_oeuvre,
    //         'materiel' => $request->materiel,
    //         'unite' => $request->unite,
    //         'debourse_sec' => $ds,
    //         'frais_chantier' => $fc,
    //         'frais_general' => $fg,
    //         'marge_nette' => $mn,
    //         'pu_ht' => $pu_ht,
    //         'pu_ttc' => $pu_ttc,
    //     ]);

    //     $bpu->updateDerivedValues();

    //     return redirect()->route('bpu.index')->with('success', 'BPU mis à jour avec succès.');
    // }
   
    


    public function store(Request $request)
{
    $request->validate([
        'designation' => 'required',
        'qte' => 'required|numeric',
        'materiaux' => 'required|numeric',
        'unite' => 'required',
        'main_oeuvre' => 'required|numeric',
        'materiel' => 'required|numeric',
        'id_rubrique' => 'required|exists:rubriques,id'
    ]);

    // Création du BPU avec uniquement les données de base
    $bpu = Bpu::create([
        'designation' => $request->designation,
        'qte' => $request->qte,
        'materiaux' => $request->materiaux,
        'main_oeuvre' => $request->main_oeuvre,
        'materiel' => $request->materiel,
        'unite' => $request->unite,
        'id_rubrique' => $request->id_rubrique,
        'contrat_id' => $request->has('contrat_id') && $request->contrat_id ? $request->contrat_id : null,
    ]);
    
    // La méthode updateDerivedValues s'occupe de tous les calculs
    $bpu->updateDerivedValues();

    return redirect()->route('bpu.index')->with('success', 'BPU ajouté avec succès.');
}

public function update(Request $request, $id)
{
    $bpu = Bpu::findOrFail($id);

    // Validation des données
    $request->validate([
        'designation' => 'required',
        'qte' => 'required|numeric',
        'materiaux' => 'required|numeric',
        'unite' => 'required',
        'main_oeuvre' => 'required|numeric',
        'materiel' => 'required|numeric',
    ]);

    // Mise à jour des données de base uniquement
    $bpu->update([
        'designation' => $request->designation,
        'qte' => $request->qte,
        'materiaux' => $request->materiaux,
        'main_oeuvre' => $request->main_oeuvre,
        'materiel' => $request->materiel,
        'unite' => $request->unite,
    ]);
    
    // La méthode updateDerivedValues s'occupe de tous les calculs
    $bpu->updateDerivedValues();

    return redirect()->route('bpu.index')->with('success', 'BPU mis à jour avec succès.');
}
    public function destroy(Bpu $bpu)
    {
        $bpu->delete();

        return redirect()->route('bpu.index')->with('success', 'BPU supprimé avec succès.');
    }

    /**
     * Dupliquer les BPU sélectionnés vers les utilitaires
     */
    public function duplicate(Request $request)
    {
        try {
            $bpuIds = $request->input('bpu_ids', []);
            
            if (empty($bpuIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun BPU sélectionné pour la duplication.'
                ]);
            }

            $duplicatedCount = 0;
            
            foreach ($bpuIds as $bpuId) {
                $originalBpu = Bpu::find($bpuId);
                
                if ($originalBpu && $originalBpu->contrat_id) {
                    // Créer une copie du BPU sans contrat_id (pour les utilitaires)
                    $duplicatedBpu = $originalBpu->replicate();
                    $duplicatedBpu->contrat_id = null;
                    $duplicatedBpu->save();
                    
                    $duplicatedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'duplicated_count' => $duplicatedCount,
                'message' => "$duplicatedCount BPU ont été dupliqués avec succès vers les utilitaires."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la duplication: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Copier les BPU utilitaires sélectionnés vers le contrat
     */
    public function copyToContract(Request $request)
    {
        try {
            $bpuIds = $request->input('bpu_ids', []);
            $contratId = $request->input('contrat_id');
            
            if (empty($bpuIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun BPU sélectionné pour la copie.'
                ]);
            }

            if (!$contratId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID du contrat manquant.'
                ]);
            }

            $copiedCount = 0;
            
            foreach ($bpuIds as $bpuId) {
                $originalBpu = Bpu::find($bpuId);
                
                if ($originalBpu && !$originalBpu->contrat_id) {
                    // Vérifier si ce BPU exact n'existe pas déjà dans le contrat
                    $existingBpu = Bpu::where('contrat_id', $contratId)
                        ->where('designation', $originalBpu->designation)
                        ->where('id_rubrique', $originalBpu->id_rubrique)
                        ->where('qte', $originalBpu->qte)
                        ->where('materiaux', $originalBpu->materiaux)
                        ->where('main_oeuvre', $originalBpu->main_oeuvre)
                        ->where('materiel', $originalBpu->materiel)
                        ->first();
                    
                    if (!$existingBpu) {
                        // Créer une copie du BPU avec contrat_id
                        $copiedBpu = $originalBpu->replicate();
                        $copiedBpu->contrat_id = $contratId;
                        $copiedBpu->save();
                        
                        $copiedCount++;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'copied_count' => $copiedCount,
                'message' => "$copiedCount BPU ont été copiés avec succès vers le contrat."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la copie: ' . $e->getMessage()
            ]);
        }
    }
}