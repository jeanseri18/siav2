<?php

namespace App\Http\Controllers;

use App\Models\Monnaie;
use Illuminate\Http\Request;

class MonnaieController extends Controller
{
    // Afficher la liste des monnaies
    public function index()
    {
        $monnaies = Monnaie::all();
        return view('monnaies.index', compact('monnaies'));
    }

    // Afficher le formulaire pour ajouter une nouvelle monnaie
    public function create()
    {
        return view('monnaies.create');
    }

    // Enregistrer une nouvelle monnaie
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'sigle' => 'required',
        ]);

        Monnaie::create($request->all());

        return redirect()->route('monnaies.index')->with('success', 'Monnaie ajoutée avec succès');
    }

    // Afficher le formulaire pour modifier une monnaie existante
    public function edit(Monnaie $monnaie)
    {
        return view('monnaies.edit', compact('monnaie'));
    }

    // Mettre à jour les informations d'une monnaie existante
    public function update(Request $request, Monnaie $monnaie)
    {
        $request->validate([
            'nom' => 'required',
            'sigle' => 'required',
        ]);

        $monnaie->update($request->all());

        return redirect()->route('monnaies.index')->with('success', 'Monnaie mise à jour avec succès');
    }

    // Supprimer une monnaie
    public function destroy(Monnaie $monnaie)
    {
        $monnaie->delete();

        return redirect()->route('monnaies.index')->with('success', 'Monnaie supprimée avec succès');
    }
}
