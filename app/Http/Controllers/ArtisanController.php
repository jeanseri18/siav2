<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artisan;
use App\Models\CorpMetier;

class ArtisanController extends Controller
{
    public function index() {
        $artisans = Artisan::with('corpMetier')->get(); 
        return view('artisans.index', compact('artisans'));
    }

    public function create() {
        $corpsMetiers = CorpMetier::all(); 
        return view('artisans.create', compact('corpsMetiers'));
    }

    public function store(Request $request) {
        $request->validate([
            'nom' => 'required|string|max:255',
            'id_corpmetier' => 'required|exists:corp_metiers,id',
            'type' => 'required|in:artisan,travailleur',
        ]);

        Artisan::create($request->all());
        return redirect()->route('artisans.index')->with('success', 'Artisan ajouté avec succès.');
    }

    public function edit($id) {
        $artisan = Artisan::findOrFail($id);
        $corpsMetiers = CorpMetier::all();
        return view('artisans.edit', compact('artisan', 'corpsMetiers'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nom' => 'required|string|max:255',
            'id_corpmetier' => 'required|exists:corp_metiers,id',
            'type' => 'required|in:artisan,travailleur',
        ]);

        $artisan = Artisan::findOrFail($id);
        $artisan->update($request->all());

        return redirect()->route('artisans.index')->with('success', 'Artisan mis à jour avec succès.');
    }

    public function destroy($id) {
        $artisan = Artisan::findOrFail($id);
        $artisan->delete();
        return redirect()->route('artisans.index')->with('success', 'Artisan supprimé avec succès.');
    }
}
