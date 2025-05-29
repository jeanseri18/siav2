<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\UniteMesure;
use App\Models\SousCategorie;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['categorie', 'sousCategorie'])->get();
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = Categorie::all();
        $sousCategories = SousCategorie::all();
        $uniteMesures = UniteMesure::all();
        
        return view('articles.create', compact('categories', 'sousCategories','uniteMesures'));
    }

    public function store(Request $request)
    {
        // Set les champs à 0 s’ils sont vides
        $request->merge([
            'quantite_stock' => $request->input('quantite_stock') ?? 0,
            'prix_unitaire' => $request->input('prix_unitaire') ?? 0,
            'cout_moyen_pondere' => $request->input('cout_moyen_pondere') ?? 0,
        ]);
    
        $request->validate([
            'nom' => 'required',
            'quantite_stock' => 'required|integer',
            'prix_unitaire' => 'required|numeric',
            'unite_mesure' => 'required',
            'cout_moyen_pondere' => 'required|numeric',
            'categorie_id' => 'required|exists:categories,id',
            'sous_categorie_id' => 'nullable|exists:souscategories,id',
        ]);

        $lastReference = \App\Models\Reference::where('nom', 'Code Article')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'Art_0000';  // Si aucune référence, utiliser un modèle
$newReference = 'Art_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête
$request->merge([
'reference' => $newReference,
]);

    
        Article::create($request->all());
    
        return redirect()->route('articles.index')->with('success', 'Article ajouté avec succès.');
    }
    

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $categories = Categorie::all();
        $sousCategories = SousCategorie::all();
        $uniteMesures = UniteMesure::all();

        return view('articles.edit', compact('article', 'categories', 'sousCategories','uniteMesures'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom' => 'required',
            'quantite_stock' => 'required|integer',
            'prix_unitaire' => 'required|numeric',
            'unite_mesure' => 'required',
            'cout_moyen_pondere' => 'required|numeric',
            'categorie_id' => 'required|exists:categories,id',
            'sous_categorie_id' => 'nullable|exists:souscategories,id',
        ]);

        $article->update($request->all());

        return redirect()->route('articles.index')->with('success', 'Article mis à jour avec succès.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès.');
    }
}
