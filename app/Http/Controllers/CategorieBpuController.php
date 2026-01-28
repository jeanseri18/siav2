<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategorieRubrique;

class CategorieBpuController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);
        
        $contratId = session('contrat_id');
        
        CategorieRubrique::create([
            'nom' => $request->nom,
            'type' => 'bpu',
            'contrat_id' => $contratId,
        ]);
        
        return back()->with('success', 'Catégorie créée avec succès.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);
        
        $categorie = CategorieRubrique::findOrFail($id);
        $categorie->update(['nom' => $request->nom]);
        
        return response()->json(['message' => 'Catégorie mise à jour !']);
    }
    
    public function destroy($id)
    {
        CategorieRubrique::findOrFail($id)->delete();
        
        return back()->with('success', 'Catégorie supprimée avec succès.');
    }
}