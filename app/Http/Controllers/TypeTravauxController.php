<?php
namespace App\Http\Controllers;

use App\Models\TypeTravaux;
use Illuminate\Http\Request;

class TypeTravauxController extends Controller
{
    public function index()
    {
        $types = TypeTravaux::all();
        return view('type-travaux.index', compact('types'));
    }

    public function create()
    {
        return view('type-travaux.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:255']);

        TypeTravaux::create($request->all());
        return redirect()->route('type-travaux.index')->with('success', 'Type de travaux ajouté avec succès.');
    }

    public function edit($id)
    {
        $type = TypeTravaux::findOrFail($id);
        return view('type-travaux.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nom' => 'required|string|max:255']);

        $type = TypeTravaux::findOrFail($id);
        $type->update($request->all());
        return redirect()->route('type-travaux.index')->with('success', 'Type de travaux mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $type = TypeTravaux::findOrFail($id);
        $type->delete();
        return redirect()->route('type-travaux.index')->with('success', 'Type de travaux supprimé avec succès.');
    }
}
