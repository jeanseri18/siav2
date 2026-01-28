<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Afficher la liste des catégories
    public function index()
    {
        $categories = Categorie::all();
        return view('categories.index', compact('categories'));
    }

    // Afficher le formulaire de création d'une catégorie
    public function create()
    {
        return view('categories.create');
    }

    // Sauvegarder une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $categorie = Categorie::create([
            'nom' => $request->nom,
        ]);

        // Si c'est une requête AJAX, retourner une réponse JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'categorie' => $categorie,
                'message' => 'Catégorie créée avec succès.'
            ]);
        }

        // Sinon, redirection classique
        return redirect()->route('categories.index')->with('success', 'Categorie created successfully.');
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $category = Categorie::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Categorie deleted successfully.');
    }
}
