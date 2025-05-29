<?php

namespace App\Http\Controllers;

use App\Models\ModePaiement;
use Illuminate\Http\Request;

class ModeDePaiementController extends Controller
{
    // Afficher la liste des modes de paiement
    public function index()
    {
        $modesDePaiement = ModePaiement::all();
        return view('modes_de_paiement.index', compact('modesDePaiement'));
    }

    // Afficher le formulaire pour ajouter un nouveau mode de paiement
    public function create()
    {
        return view('modes_de_paiement.create');
    }

    // Enregistrer un nouveau mode de paiement
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
        ]);

        ModePaiement::create($request->all());

        return redirect()->route('modes_de_paiement.index')->with('success', 'Mode de paiement ajouté avec succès');
    }

    // Afficher le formulaire pour modifier un mode de paiement existant
    public function edit(ModePaiement $ModePaiement)
    {
        return view('modes_de_paiement.edit', compact('ModePaiement'));
    }

    // Mettre à jour les informations d'un mode de paiement existant
    public function update(Request $request, ModePaiement $ModePaiement)
    {
        $request->validate([
            'nom' => 'required',
        ]);

        $ModePaiement->update($request->all());

        return redirect()->route('modes_de_paiement.index')->with('success', 'Mode de paiement mis à jour avec succès');
    }

    // Supprimer un mode de paiement
    public function destroy(ModePaiement $ModePaiement)
    {
        $ModePaiement->delete();

        return redirect()->route('modes_de_paiement.index')->with('success', 'Mode de paiement supprimé avec succès');
    }
}
