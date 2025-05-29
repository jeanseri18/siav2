<?php

namespace App\Http\Controllers;

use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use App\Models\Rubrique;
use App\Models\Bpu;
use App\Models\UniteMesure;
use Illuminate\Http\Request;

class BpuController extends Controller
{
    public function index()
    {
        $uniteMesures = UniteMesure::all();

        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus'
        ])->get();

        return view('bpu.index', compact('categories', 'uniteMesures'));
    }
    
    public function print()
    {
        $uniteMesures = UniteMesure::all();

        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus'
        ])->get();

        return view('bpu.print', compact('categories', 'uniteMesures'));
    }
    
    public function create()
    {
        $rubriques = Rubrique::all();
        $uniteMesures = UniteMesure::all();
        
        return view('bpu.create', compact('rubriques', 'uniteMesures'));
    }

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

        // 🔢 Calculs automatiques
        $ds = $request->materiaux + $request->main_oeuvre + $request->materiel;
        $fc = $ds * 0.30; // 30%
        $fg = ($ds + $fc) * 0.15; // 15%
        $mn = ($ds + $fc + $fg) * 0.15; // 15%
        $pu_ht = $ds + $fc + $fg + $mn;
        $pu_ttc = $pu_ht * 1.18; // TVA 18%

        // 💾 Création du BPU
        Bpu::create([
            'designation' => $request->designation,
            'qte' => $request->qte,
            'materiaux' => $request->materiaux,
            'main_oeuvre' => $request->main_oeuvre,
            'materiel' => $request->materiel,
            'unite' => $request->unite,
            'debourse_sec' => $ds,
            'frais_chantier' => $fc,
            'frais_general' => $fg,
            'marge_nette' => $mn,
            'pu_ht' => $pu_ht,
            'pu_ttc' => $pu_ttc,
            'id_rubrique' => $request->id_rubrique,
        ]);

        return redirect()->route('bpu.index')->with('success', 'BPU ajouté avec succès.');
    }

    public function edit(Bpu $bpu)
    {
        $uniteMesures = UniteMesure::all();
        $rubriques = Rubrique::all();
        
        return view('bpu.edit', compact('bpu', 'uniteMesures', 'rubriques'));
    }

    public function update(Request $request, $id)
    {
        $bpu = Bpu::findOrFail($id);
    
        // 🔍 Validation des données
        $request->validate([
            'designation' => 'required',
            'qte' => 'required|numeric',
            'materiaux' => 'required|numeric',
            'unite' => 'required',
            'main_oeuvre' => 'required|numeric',
            'materiel' => 'required|numeric',
        ]);
    
        // 🔢 Calculs auto
        $ds = $request->materiaux + $request->main_oeuvre + $request->materiel;
        $fc = $ds * 0.30;
        $fg = ($ds + $fc) * 0.15;
        $mn = ($ds + $fc + $fg) * 0.15;
        $pu_ht = $ds + $fc + $fg + $mn;
        $pu_ttc = $pu_ht * 1.18;
    
        // 🔁 Update de la ligne BPU
        $bpu->update([
            'designation' => $request->designation,
            'qte' => $request->qte,
            'materiaux' => $request->materiaux,
            'main_oeuvre' => $request->main_oeuvre,
            'materiel' => $request->materiel,
            'unite' => $request->unite,
            'debourse_sec' => $ds,
            'frais_chantier' => $fc,
            'frais_general' => $fg,
            'marge_nette' => $mn,
            'pu_ht' => $pu_ht,
            'pu_ttc' => $pu_ttc,
        ]);
    
        return redirect()->route('bpu.index')->with('success', 'BPU mis à jour avec succès.');
    }
    
    public function destroy(Bpu $bpu)
    {
        $bpu->delete();

        return redirect()->route('bpu.index')->with('success', 'BPU supprimé avec succès.');
    }
}