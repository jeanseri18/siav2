<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use Illuminate\Http\Request;

class BanqueController extends Controller
{
    public function index()
    {
        $banques = Banque::all();
        return view('banques.index', compact('banques'));
    }

    public function create()
    {
        return view('banques.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:banques'
        ]);

        Banque::create($request->all());

        return redirect()->route('banques.index')->with('success', 'Banque ajoutée avec succès.');
    }

    public function edit(Banque $banque)
    {
        return view('banques.edit', compact('banque'));
    }

    public function update(Request $request, Banque $banque)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:banques,nom,' . $banque->id
        ]);

        $banque->update($request->all());

        return redirect()->route('banques.index')->with('success', 'Banque mise à jour.');
    }

    public function destroy(Banque $banque)
    {
        $banque->delete();
        return redirect()->route('banques.index')->with('success', 'Banque supprimée.');
    }
}
