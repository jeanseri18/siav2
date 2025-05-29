<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\SousCategorie;
use Illuminate\Http\Request;

class SousCategorieController extends Controller
{
    // Afficher la liste des sous-catégories
    public function index()
    {
        $sousCategories = SousCategorie::with('categorie')->get();
        return view('sous_categories.index', compact('sousCategories'));
    }

    // Afficher le formulaire de création d'une sous-catégorie
    public function create()
    {
        $categories = Categorie::all(); // Obtenir toutes les catégories
        return view('sous_categories.create', compact('categories'));
    }

    // Sauvegarder une nouvelle sous-catégorie
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categories,id'
        ]);

        SousCategorie::create([
            'nom' => $request->nom,
            'categorie_id' => $request->categorie_id,
        ]);

        return redirect()->route('sous_categories.index')->with('success', 'Sous-catégorie créée avec succès.');
    }

    // Supprimer une sous-catégorie
    public function destroy($id)
    {
        $sousCategorie = SousCategorie::findOrFail($id);
        $sousCategorie->delete();

        return redirect()->route('sous_categories.index')->with('success', 'Sous-catégorie supprimée avec succès.');
    }
}
