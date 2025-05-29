<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CorpMetier;

class CorpsMetierController extends Controller
{
    // Affiche la liste des corps de métier
    public function index()
    {
        $corpsMetiers = CorpMetier::all();
        return view('corpmetiers.index', compact('corpsMetiers'));
    }

    // Affiche le formulaire de création
    public function create()
    {
        return view('corpmetiers.create');
    }

    // Enregistre un nouveau corps de métier
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:corp_metiers',
        ]);

        CorpMetier::create([
            'nom' => $request->nom,
        ]);

        return redirect()->route('corpsmetiers.index')->with('success', 'Corps de métier ajouté avec succès.');
    }

    // Affiche le formulaire d'édition
    public function edit($id)
    {
        $corpsMetier = CorpMetier::findOrFail($id);
        return view('corpmetiers.edit', compact('corpsMetier'));
    }

    // Met à jour un corps de métier
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:corp_metiers,nom,' . $id,
        ]);

        $corpMetier = CorpMetier::findOrFail($id);
        $corpMetier->update(['nom' => $request->nom]);

        return redirect()->route('corpsmetiers.index')->with('success', 'Corps de métier mis à jour avec succès.');
    }

    // Supprime un corps de métier
    public function destroy($id)
    {
        $corpMetier = CorpMetier::findOrFail($id);
        $corpMetier->delete();

        return redirect()->route('corpsmetiers.index')->with('success', 'Corps de métier supprimé.');
    }
}
