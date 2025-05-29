<?php
namespace App\Http\Controllers;

use App\Models\BU;
use App\Models\SecteurActivite;
use Illuminate\Http\Request;

class BUController extends Controller
{
    // Lister tous les BUs
    public function index()
    {
        $bus = BU::with('secteur')->get();
        return view('bu.index', compact('bus'));
    }

    // Afficher le formulaire de création
    public function create()
    {
        $secteurs = SecteurActivite::all();
        return view('bu.create', compact('secteurs'));
    }

    // Enregistrer un BU
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'secteur_activite_id' => 'required|exists:secteur_activites,id',
            'adresse' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'numero_rccm' => 'required|string|max:255',
            'numero_cc' => 'required|string|max:255',
            'soldecaisse'=> 'required|string|max:255',
            'statut' => 'required|in:actif,inactif',
        ]);

        $bu = new BU($request->except('logo'));

        if ($request->hasFile('logo')) {
            $bu->logo = $request->file('logo')->store('logos', 'public');
        }

        $bu->save();
        return redirect()->route('bu.index')->with('success', 'BU créé avec succès.');
    }

    // Afficher un BU spécifique
    public function show(BU $bu)
    {
        return view('bu.show', compact('bu'));
    }

    // Afficher le formulaire d'édition
    public function edit(BU $bu)
    {
        $secteurs = SecteurActivite::all();
        return view('bu.edit', compact('bu', 'secteurs'));
    }

    // Mettre à jour un BU
    public function update(Request $request, BU $bu)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'secteur_activite_id' => 'required|exists:secteur_activites,id',
            'adresse' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'numero_rccm' => 'required|string|max:255',
            'numero_cc' => 'required|string|max:255',
            'statut' => 'required|in:actif,inactif',
        ]);

        $bu->update($request->except('logo'));

        if ($request->hasFile('logo')) {
            $bu->logo = $request->file('logo')->store('logos', 'public');
        }

        return redirect()->route('bu.index')->with('success', 'BU mis à jour avec succès.');
    }

    // Supprimer un BU
    public function destroy(BU $bu)
    {
        $bu->delete();
        return redirect()->route('bu.index')->with('success', 'BU supprimé avec succès.');
    }
}
