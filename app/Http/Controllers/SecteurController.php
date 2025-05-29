<?php
namespace App\Http\Controllers;

use App\Models\Secteur;
use App\Models\Ville;
use Illuminate\Http\Request;

class SecteurController extends Controller
{
    public function index()
    {
        $secteurs = Secteur::with('ville')->get();
        return view('secteurs.index', compact('secteurs'));
    }

    public function create()
    {
        $villes = Ville::all();
        return view('secteurs.create', compact('villes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ville_id' => 'required'
        ]);

        Secteur::create($request->all());
        return redirect()->route('secteurs.index')->with('success', 'Secteur ajouté avec succès.');
    }

    public function edit($id)
    {
        $secteur = Secteur::findOrFail($id);
        $villes = Ville::all();
        return view('secteurs.edit', compact('secteur', 'villes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ville_id' => 'required'
        ]);

        $secteur = Secteur::findOrFail($id);
        $secteur->update($request->all());
        return redirect()->route('secteurs.index')->with('success', 'Secteur mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $secteur = Secteur::findOrFail($id);
        $secteur->delete();
        return redirect()->route('secteurs.index')->with('success', 'Secteur supprimé avec succès.');
    }
}
