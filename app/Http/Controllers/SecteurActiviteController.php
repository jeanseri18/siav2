<?php

namespace App\Http\Controllers;

use App\Models\SecteurActivite;
use Illuminate\Http\Request;

class SecteurActiviteController extends Controller
{
    // Afficher la liste des secteurs
    public function index()
    {
        $secteurs = SecteurActivite::all();
        return view('secteur_activites.index', compact('secteurs'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        return view('secteur_activites.create');
    }

    // Enregistrer un secteur d'activité
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        SecteurActivite::create($request->all());
        
        return redirect()->route('secteur_activites.index')->with('success', 'Secteur créé avec succès');
    }

    // Afficher le formulaire d'édition
    public function edit(SecteurActivite $secteur)
    {
        return view('secteur_activites.edit', compact('secteur'));
    }

    // Mettre à jour un secteur d'activité
    public function update(Request $request, SecteurActivite $secteur)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $secteur->update($request->all());

        return redirect()->route('secteur_activites.index')->with('success', 'Secteur mis à jour avec succès');
    }

    // Supprimer un secteur d'activité
    public function destroy(SecteurActivite $secteur)
    {
        $secteur->delete();
        return redirect()->route('secteur_activites.index')->with('success', 'Secteur supprimé avec succès');
    }
}
