<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SousCategorieRubrique;

class SousCategorieBpuController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'id_session' => 'required|exists:categorierubriques,id',
        ]);
        
        SousCategorieRubrique::create([
            'nom' => $request->nom,
            'id_session' => $request->id_session,
            'type' => 'bpu',
        ]);
        
        return back()->with('success', 'Sous-catégorie créée avec succès.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);
        
        $sousCategorie = SousCategorieRubrique::findOrFail($id);
        $sousCategorie->update(['nom' => $request->nom]);
        
        return response()->json(['message' => 'Sous-catégorie mise à jour !']);
    }
    
    public function destroy($id)
    {
        SousCategorieRubrique::findOrFail($id)->delete();
        
        return back()->with('success', 'Sous-catégorie supprimée avec succès.');
    }
}