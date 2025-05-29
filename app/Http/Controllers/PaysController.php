<?php
namespace App\Http\Controllers;

use App\Models\Pays;
use Illuminate\Http\Request;

class PaysController extends Controller
{
    public function index()
    {
        $pays = Pays::all();
        return view('pays.index', compact('pays'));
    }

    public function create()
    {
        return view('pays.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:255']);
        Pays::create($request->all());
        return redirect()->route('pays.index')->with('success', 'Pays ajouté avec succès.');
    }

    public function edit($id)
    {
        $pays = Pays::findOrFail($id);
        return view('pays.edit', compact('pays'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nom' => 'required|string|max:255']);
        $pays = Pays::findOrFail($id);
        $pays->update($request->all());
        return redirect()->route('pays.index')->with('success', 'Pays mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $pays = Pays::findOrFail($id);
        $pays->delete();
        return redirect()->route('pays.index')->with('success', 'Pays supprimé avec succès.');
    }
}
