<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artisan;
use App\Models\CorpMetier;

class ArtisanController extends Controller
{
    public function index() {
        $artisans = Artisan::all(); 
        return view('artisans.index', compact('artisans'));
    }

    public function create() {
        $corpsMetier = CorpMetier::all();
        return view('artisans.create', compact('corpsMetier'));
    }

    public function store(Request $request) {
        $request->validate([
            'nom' => 'required|string|max:255',
            // 'id_corpmetier' => 'required|exists:corp_metiers,id',
            'civilite' => 'required|in:Monsieur,Madame,Mademoiselle',
            'prenoms' => 'nullable|string|max:255',
            'type_piece' => 'required|in:CNI,Passeport,Permis',
            'numero_piece' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'nationalite' => 'nullable|string|max:100',
            'fonction' => 'required|string',
            'localisation' => 'required|string|max:255',
            'rcc' => 'nullable|string|max:100',
            'rccm' => 'nullable|string|max:100',
            'boite_postale' => 'nullable|string|max:100',
            'tel1' => 'required|string|max:20',
            'tel2' => 'nullable|string|max:20',
            'mail' => 'nullable|email|max:255',
            'ppsi' => 'nullable|boolean',
        ]);

         $lastReference = \App\Models\Reference::where('nom', 'Code Artisans')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'Ouv_0000';  // Si aucune référence, utiliser un modèle
$newReference = 'Ouv_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête
$request->merge([
'reference' => $newReference,
]);
        Artisan::create($request->all());
        return redirect()->route('artisans.index')->with('success', 'Artisan ajouté avec succès.');
    }

    public function edit($id) {
        $artisan = Artisan::findOrFail($id);
        $corpsMetier = CorpMetier::all();
        return view('artisans.edit', compact('artisan', 'corpsMetier'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nom' => 'required|string|max:255',
            'civilite' => 'required|in:Monsieur,Madame,Mademoiselle',
            'prenoms' => 'nullable|string|max:255',
            'type_piece' => 'required|in:CNI,Passeport,Permis',
            'numero_piece' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'nationalite' => 'nullable|string|max:100',
            'fonction' => 'required|string',
            'localisation' => 'required|string|max:255',
            'rcc' => 'nullable|string|max:100',
            'rccm' => 'nullable|string|max:100',
            'boite_postale' => 'nullable|string|max:100',
            'tel1' => 'required|string|max:20',
            'tel2' => 'nullable|string|max:20',
            'mail' => 'nullable|email|max:255',
            'ppsi' => 'nullable|boolean',
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
