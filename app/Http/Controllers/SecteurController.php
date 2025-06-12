<?php
namespace App\Http\Controllers;

use App\Models\Secteur;
use App\Models\Quartier;
use Illuminate\Http\Request;

class SecteurController extends Controller
{
    public function index()
    {
        $secteurs = Secteur::with('quartier.commune.ville')->get();
        return view('secteurs.index', compact('secteurs'));
    }

    public function create()
    {
        $quartiers = Quartier::with('commune.ville')->get();
        return view('secteurs.create', compact('quartiers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'quartier_id' => 'required'
        ]);

        Secteur::create($request->all());
        return redirect()->route('secteurs.index')->with('success', 'Secteur ajouté avec succès.');
    }

    public function edit($id)
    {
        $secteur = Secteur::findOrFail($id);
        $quartiers = Quartier::with('commune.ville')->get();
        return view('secteurs.edit', compact('secteur', 'quartiers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'quartier_id' => 'required'
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
