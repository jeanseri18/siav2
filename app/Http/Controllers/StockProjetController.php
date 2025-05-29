<?php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\StockProjet;
use App\Models\Projet;
use Illuminate\Http\Request;
 
class StockProjetController extends Controller
{
    public function index()
{
    // Vérifier si l'id_projet est défini dans la session
    $projet_id = session('projet_id');
    if (!$projet_id) {
        return redirect()->route('projets.index')->with('error', 'Aucun projet sélectionné');
    }

    // Récupérer tous les stocks pour ce projet
    $stocks = StockProjet::where('id_projet', $projet_id)->with('article')->get();
    $projets = Projet::all();
    $articles = Article::all();

    // Retourner la vue avec les stocks récupérés
    return view('stock_projet.index', compact('stocks','projets','articles'));
}

    public function create()
    {
        // Récupérer les articles disponibles
 $projets = Projet::all();
    $articles = Article::all();
            return view('stock_projet.create', compact('articles','projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $projet_id = session('projet_id');

        StockProjet::create([
            'id_projet' => $projet_id,
            'article_id' => $request->article_id,
            'quantite' => $request->quantite,
        ]);

        return redirect()->route('stock.index')->with('success', 'Produit ajouté au stock avec succès');
    }

    public function edit($id)
    {
        $stock = StockProjet::findOrFail($id);
        $articles = Article::all(); // Récupérer tous les articles
        return view('stock_projet.edit', compact('stock', 'articles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $stock = StockProjet::findOrFail($id);
        $stock->update([
            'article_id' => $request->article_id,
            'quantite' => $request->quantite,
        ]);

        return redirect()->route('stock.index')->with('success', 'Produit mis à jour avec succès');
    }


    public function index_contrat()
    {
        // Vérifier si l'id_projet est défini dans la session
        $projet_id = session('projet_id');
        if (!$projet_id) {
            return redirect()->route('contrats.index')->with('error', 'Aucun projet sélectionné');
        }
    
        // Récupérer tous les stocks pour ce projet
        $stocks = StockProjet::where('id_projet', $projet_id)->with('article')->get();
        $projets = Projet::all();
        $articles = Article::all();
    
        // Retourner la vue avec les stocks récupérés
        return view('stock_contrat.index', compact('stocks','projets','articles'));
    }
    
        public function create_contrat()
        {
            // Récupérer les articles disponibles
            $articles = Article::all();
            return view('stock_contrat.create', compact('articles'));
        }
    
        public function store_contrat(Request $request)
        {
            $request->validate([
                'article_id' => 'required|exists:articles,id',
                'quantite' => 'required|integer|min:1',
            ]);
    
            $projet_id = session('projet_id');
    
            StockProjet::create([
                'id_projet' => $projet_id,
                'article_id' => $request->article_id,
                'quantite' => $request->quantite,
            ]);
    
            return redirect()->route('stock_contrat.index')->with('success', 'Produit ajouté au stock avec succès');
        }
    
        public function edit_contrat($id)
        {
            $stock = StockProjet::findOrFail($id);
            $articles = Article::all(); // Récupérer tous les articles
            return view('stock_contrat.edit', compact('stock', 'articles'));
        }
    
        public function update_contrat(Request $request, $id)
        {
            $request->validate([
                'article_id' => 'required|exists:articles,id',
                'quantite' => 'required|integer|min:1',
            ]);
    
            $stock = StockProjet::findOrFail($id);
            $stock->update([
                'article_id' => $request->article_id,
                'quantite' => $request->quantite,
            ]);
    
            return redirect()->route('stock_contrat.index')->with('success', 'Produit mis à jour avec succès');
        }
    
}
