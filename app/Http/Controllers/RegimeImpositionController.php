<?php
namespace App\Http\Controllers;

use App\Models\RegimeImposition;
use Illuminate\Http\Request;

class RegimeImpositionController extends Controller
{
    public function index()
    {
        $regimes = RegimeImposition::all();
        return view('regime-impositions.index', compact('regimes'));
    }

    public function create()
    {
        return view('regime-impositions.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:255','ref' => 'required|string|max:255', 'tva' => 'required|string|max:255']);

        RegimeImposition::create($request->all());
        return redirect()->route('regime-impositions.index')->with('success', 'Régime d’imposition ajouté avec succès.');
    }

    public function edit($id)
    {
        $regime = RegimeImposition::findOrFail($id);
        return view('regime-impositions.edit', compact('regime'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nom' => 'required|string|max:255','ref' => 'required|string|max:255', 'tva' => 'required|string|max:255']);

        $regime = RegimeImposition::findOrFail($id);
        $regime->update($request->all());
        return redirect()->route('regime-impositions.index')->with('success', 'Régime d’imposition mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $regime = RegimeImposition::findOrFail($id);
        $regime->delete();
        return redirect()->route('regime-impositions.index')->with('success', 'Régime d’imposition supprimé avec succès.');
    }
}
