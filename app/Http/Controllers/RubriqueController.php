<?php

namespace App\Http\Controllers;

use App\Models\Rubrique;
use Illuminate\Http\Request;

class RubriqueController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'id_soussession' => 'required|exists:souscategorierubriques,id',
        ]);
        
        Rubrique::create([
            'nom' => $request->nom,
            'id_soussession' => $request->id_soussession,
            'type' => 'bpu',
        ]);
        
        return back()->with('success', 'Rubrique créée avec succès.');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);
        
        $rubrique = Rubrique::findOrFail($id);
        $rubrique->update(['nom' => $request->nom]);
        
        return response()->json(['success' => true, 'message' => 'Rubrique mise à jour !']);
    }
    
    public function destroy($id)
    {
        Rubrique::destroy($id);
        
        return back()->with('success', 'Rubrique supprimée avec succès.');
    }
}