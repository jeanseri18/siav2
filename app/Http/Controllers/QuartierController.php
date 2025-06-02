<?php

namespace App\Http\Controllers;

use App\Models\Quartier;
use App\Models\Commune;
use Illuminate\Http\Request;

class QuartierController extends Controller
{
    /**
     * Afficher la liste des quartiers
     */
    public function index()
    {
        $quartiers = Quartier::with('commune.ville')->get();
        return view('quartiers.index', compact('quartiers'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $communes = Commune::with('ville')->get();
        return view('quartiers.create', compact('communes'));
    }

    /**
     * Enregistrer un nouveau quartier
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'commune_id' => 'required|exists:communes,id',
            'code' => 'nullable|string|max:50',
        ]);

        Quartier::create($request->all());

        return redirect()->route('quartiers.index')
            ->with('success', 'Quartier ajouté avec succès.');
    }

    /**
     * Afficher les détails d'un quartier
     */
    public function show(Quartier $quartier)
    {
        return view('quartiers.show', compact('quartier'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Quartier $quartier)
    {
        $communes = Commune::with('ville')->get();
        return view('quartiers.edit', compact('quartier', 'communes'));
    }

    /**
     * Mettre à jour un quartier
     */
    public function update(Request $request, Quartier $quartier)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'commune_id' => 'required|exists:communes,id',
            'code' => 'nullable|string|max:50',
        ]);

        $quartier->update($request->all());

        return redirect()->route('quartiers.index')
            ->with('success', 'Quartier mis à jour avec succès.');
    }

    /**
     * Supprimer un quartier
     */
    public function destroy(Quartier $quartier)
    {
        $quartier->delete();

        return redirect()->route('quartiers.index')
            ->with('success', 'Quartier supprimé avec succès.');
    }
}