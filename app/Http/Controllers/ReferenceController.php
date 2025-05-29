<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use Illuminate\Http\Request;

class ReferenceController extends Controller
{
    public function index()
    {
        $references = Reference::all();
        return view('references.index', compact('references'));
    }

    public function create()
    {
        return view('references.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ref' => 'required|string|max:255|unique:references'
        ]);

        Reference::create($request->all());

        return redirect()->route('references.index')->with('success', 'Référence ajoutée avec succès.');
    }

    public function edit(Reference $reference)
    {
        return view('references.edit', compact('reference'));
    }

    public function update(Request $request, Reference $reference)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'ref' => 'required|string|max:255|unique:references,ref,' . $reference->id
        ]);

        $reference->update($request->all());

        return redirect()->route('references.index')->with('success', 'Référence mise à jour.');
    }

    public function destroy(Reference $reference)
    {
        $reference->delete();
        return redirect()->route('references.index')->with('success', 'Référence supprimée.');
    }
}
