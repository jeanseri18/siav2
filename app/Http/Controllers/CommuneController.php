<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Ville;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    /**
     * Afficher la liste des communes
     */
    public function index()
    {
        $communes = Commune::with('ville')->get();
        return view('communes.index', compact('communes'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $villes = Ville::all();
        return view('communes.create', compact('villes'));
    }

    /**
     * Enregistrer une nouvelle commune
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ville_id' => 'required|exists:villes,id',
            'code' => 'nullable|string|max:50',
        ]);

        Commune::create($request->all());

        return redirect()->route('communes.index')
            ->with('success', 'Commune ajoutée avec succès.');
    }

    /**
     * Afficher les détails d'une commune
     */
    public function show(Commune $commune)
    {
        return view('communes.show', compact('commune'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Commune $commune)
    {
        $villes = Ville::all();
        return view('communes.edit', compact('commune', 'villes'));
    }

    /**
     * Mettre à jour une commune
     */
    public function update(Request $request, Commune $commune)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ville_id' => 'required|exists:villes,id',
            'code' => 'nullable|string|max:50',
        ]);

        $commune->update($request->all());

        return redirect()->route('communes.index')
            ->with('success', 'Commune mise à jour avec succès.');
    }

    /**
     * Supprimer une commune
     */
    public function destroy(Commune $commune)
    {
        $commune->delete();

        return redirect()->route('communes.index')
            ->with('success', 'Commune supprimée avec succès.');
    }
}