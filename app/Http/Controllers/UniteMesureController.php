<?php
namespace App\Http\Controllers;

use App\Models\UniteMesure;
use Illuminate\Http\Request;

class UniteMesureController extends Controller
{
    public function index()
    {
        $unites = UniteMesure::all();
        return view('unite-mesures.index', compact('unites'));
    }

    public function create()
    {
        return view('unite-mesures.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:255']);

        UniteMesure::create($request->all());
        return redirect()->route('unite-mesures.index')->with('success', 'Unité de mesure ajoutée avec succès.');
    }

    public function edit($id)
    {
        $unite = UniteMesure::findOrFail($id);
        return view('unite-mesures.edit', compact('unite'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nom' => 'required|string|max:255']);

        $unite = UniteMesure::findOrFail($id);
        $unite->update($request->all());
        return redirect()->route('unite-mesures.index')->with('success', 'Unité de mesure mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $unite = UniteMesure::findOrFail($id);
        $unite->delete();
        return redirect()->route('unite-mesures.index')->with('success', 'Unité de mesure supprimée avec succès.');
    }
}
