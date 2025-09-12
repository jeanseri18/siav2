<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\UniteMesure;
use App\Models\SousCategorie;
use App\Models\ClientFournisseur;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index()
    {
        // Récupérer la BU actuelle depuis la session
        $bu_id = session('selected_bu');
        
        if (!$bu_id) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner une BU avant d\'accéder à cette page.']);
        }
        
        // Récupérer tous les projets de la BU
        $projets_bu = Projet::where('bu_id', $bu_id)->pluck('id')->toArray();
        
        $articles = Article::with(['categorie', 'sousCategorie', 'fournisseur','uniteMesure'])->get();
        return view('articles.index', compact('articles', 'projets_bu'));
    }

    public function create()
    {
        $categories = Categorie::all();
        $sousCategories = SousCategorie::all();
        $uniteMesures = UniteMesure::all();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->get();
        
        return view('articles.create', compact('categories', 'sousCategories','uniteMesures', 'fournisseurs'));
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
            'reference_fournisseur' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
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
        $article->load(['categorie', 'sousCategorie', 'uniteMesure', 'fournisseur']);
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $categories = Categorie::all();
        $sousCategories = SousCategorie::all();
        $uniteMesures = UniteMesure::all();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->get();

        return view('articles.edit', compact('article', 'categories', 'sousCategories','uniteMesures', 'fournisseurs'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom' => 'required',
            'reference_fournisseur' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
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

    public function stockDetails(Article $article)
    {
        // Récupérer la BU actuelle depuis la session
        $bu_id = session('selected_bu');
        
        if (!$bu_id) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner une BU avant d\'accéder à cette page.']);
        }
        
        // Récupérer tous les projets de la BU
        $projets_bu = \App\Models\Projet::where('bu_id', $bu_id)->pluck('id')->toArray();
        
        // Récupérer les stocks de l'article pour tous les projets de la BU
        $stocksProjet = \App\Models\StockProjet::with(['projet'])
            ->whereIn('id_projet', $projets_bu)
            ->where('article_id', $article->id)
            ->where('quantite', '>', 0)
            ->get();
            
        return response()->json([
            'article' => $article,
            'stocks' => $stocksProjet->map(function($stock) {
                return [
                    'projet_nom' => $stock->projet->nom_projet ?? 'Projet inconnu',
                    'quantite' => $stock->quantite,
                    'unite_mesure' => $stock->uniteMesure->ref ?? $stock->article->uniteMesure->ref ?? 'N/A'
                ];
            })
        ]);
    }
}
