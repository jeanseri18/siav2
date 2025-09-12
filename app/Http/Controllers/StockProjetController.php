<?php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\StockProjet;
use App\Models\Projet;
use App\Models\Contrat;
use App\Models\UniteMesure;
use App\Models\DemandeRavitaillement;
use App\Models\MouvementStock;
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
    $stocks = StockProjet::where('id_projet', $projet_id)->with(['article.categorie', 'article.sousCategorie', 'article.fournisseur', 'article.uniteMesure', 'uniteMesure'])->get();
    $projets = Projet::all();
    $articles = Article::all();

    // Retourner la vue avec les stocks récupérés
    return view('stock_projet.index', compact('stocks','projets','articles','projet_id'));
}

    public function create()
    {
        // Récupérer les articles disponibles
        $projets = Projet::all();
        $articles = Article::with('uniteMesure')->get();
        $uniteMesures = UniteMesure::all();
        return view('stock_projet.create', compact('articles','projets','uniteMesures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|integer|min:1',
            'unite_mesure_id' => 'required|exists:unite_mesures,id',
        ]);

        $projet_id = session('projet_id');

        StockProjet::create([
            'id_projet' => $projet_id,
            'article_id' => $request->article_id,
            'quantite' => $request->quantite,
            'unite_mesure_id' => $request->unite_mesure_id,
        ]);

        return redirect()->route('stock.index')->with('success', 'Article ajouté au stock avec succès');
    }

    public function edit($id)
    {
        $stock = StockProjet::with(['article.uniteMesure', 'uniteMesure'])->findOrFail($id);
        $articles = Article::with('uniteMesure')->get();
        $uniteMesures = UniteMesure::all();
        return view('stock_projet.edit', compact('stock', 'articles', 'uniteMesures'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'quantite' => 'required|integer|min:1',
            'unite_mesure_id' => 'required|exists:unite_mesures,id',
        ]);

        $stock = StockProjet::findOrFail($id);
        $stock->update([
            'article_id' => $request->article_id,
            'quantite' => $request->quantite,
            'unite_mesure_id' => $request->unite_mesure_id,
        ]);

        return redirect()->route('stock.index')->with('success', 'Article mis à jour avec succès');
    }

    public function show($id)
    {
        $stock = StockProjet::with(['article.categorie', 'article.sousCategorie', 'article.fournisseur', 'article.uniteMesure', 'uniteMesure', 'projet'])->findOrFail($id);
        return view('stock_projet.show', compact('stock'));
    }

    public function destroy($id)
    {
        $stock = StockProjet::findOrFail($id);
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Article supprimé du stock avec succès');
    }


    public function index_contrat()
    {
        // Vérifier si l'id_contrat est défini dans la session
        $contrat_id = session('contrat_id');
        if (!$contrat_id) {
            return redirect()->route('contrats.index')->with('error', 'Aucun contrat sélectionné');
        }
    
        // Récupérer tous les stocks pour ce contrat
        $stocks = StockProjet::where('id_contrat', $contrat_id)->with(['article.categorie', 'article.sousCategorie', 'article.fournisseur', 'article.uniteMesure', 'uniteMesure', 'contrat'])->get();
        $projets = Projet::all();
        $articles = Article::all();
    
        // Retourner la vue avec les stocks récupérés
        return view('stock_contrat.index', compact('stocks','projets','articles','contrat_id'));
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
    
            $contrat_id = session('contrat_id');
            if (!$contrat_id) {
                return redirect()->route('contrats.index')->with('error', 'Aucun contrat sélectionné');
            }
    
            // Récupérer le contrat pour obtenir l'id_projet
             $contrat = Contrat::findOrFail($contrat_id);
    
            StockProjet::create([
                'id_projet' => $contrat->id_projet,
                'id_contrat' => $contrat_id,
                'article_id' => $request->article_id,
                'quantite' => $request->quantite,
            ]);
    
            return redirect()->route('stock_contrat.index')->with('success', 'Article ajouté au stock avec succès');
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
    
            return redirect()->route('stock_contrat.index')->with('success', 'Article mis à jour avec succès');
        }

        public function show_contrat($id)
        {
            $stock = StockProjet::with(['article.categorie', 'article.sousCategorie', 'article.fournisseur', 'article.uniteMesure', 'uniteMesure', 'projet'])->findOrFail($id);
            return view('stock_contrat.show', compact('stock'));
        }

        public function destroy_contrat($id)
        {
            $stock = StockProjet::findOrFail($id);
            $stock->delete();
            return redirect()->route('stock_contrat.index')->with('success', 'Article supprimé du stock avec succès');
        }
        
        /**
         * Afficher l'historique des mouvements de stock pour un contrat
         */
        public function historique_contrat()
        {
            $contrat_id = session('contrat_id');
            if (!$contrat_id) {
                return redirect()->route('contrats.index')->with('error', 'Aucun contrat sélectionné');
            }
            
            // Récupérer les demandes de ravitaillement approuvées pour ce contrat
            $demandesRavitaillement = \App\Models\DemandeRavitaillement::where('contrat_id', $contrat_id)
                ->where('statut', 'approuvee')
                ->with(['lignes.article', 'demandeur', 'approbateur'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('stock_contrat.historique', compact('demandesRavitaillement', 'contrat_id'));
        }

        /**
         * Livraison Chantier - Sortie du stock contrat
         */
        public function livraison(Request $request)
        {
            $request->validate([
            'stock_id' => 'required|exists:stock_projet,id',
                'quantite' => 'required|numeric|min:1',
                'date_livraison' => 'required|date',
                'commentaires' => 'nullable|string|max:500'
            ]);

            $stock = StockProjet::findOrFail($request->stock_id);
            
            // Vérifier que c'est bien un stock contrat (avec id_contrat)
            if (!$stock->id_contrat) {
                return redirect()->back()->with('error', 'Cette action n\'est possible que pour un stock contrat.');
            }

            // Vérifier la quantité disponible
            if ($stock->quantite < $request->quantite) {
                return redirect()->back()->with('error', 'Quantité insuffisante en stock.');
            }

            // Enregistrer le mouvement avant modification
            $quantiteAvant = $stock->quantite;
            
            // Réduire la quantité du stock contrat
            $stock->quantite -= $request->quantite;
            $stock->save();

            // Enregistrer le mouvement dans l'historique
            MouvementStock::creerMouvement(
                $stock->id,
                'livraison_chantier',
                $request->quantite,
                $quantiteAvant,
                $stock->quantite,
                $request->date_livraison,
                $request->commentaires,
                'LIV-' . now()->format('YmdHis')
            );

            return redirect()->route('stock_contrat.index')
                ->with('success', 'Livraison chantier effectuée avec succès. Quantité livrée: ' . $request->quantite);
        }

        /**
         * Retour Chantier - Entrée dans le stock contrat
         */
        public function retourChantier(Request $request)
        {
            $request->validate([
            'stock_id' => 'required|exists:stock_projet,id',
                'quantite' => 'required|numeric|min:1',
                'date_retour' => 'required|date',
                'commentaires' => 'nullable|string|max:500'
            ]);

            $stock = StockProjet::findOrFail($request->stock_id);
            
            // Vérifier que c'est bien un stock contrat (avec id_contrat)
            if (!$stock->id_contrat) {
                return redirect()->back()->with('error', 'Cette action n\'est possible que pour un stock contrat.');
            }

            // Enregistrer le mouvement avant modification
            $quantiteAvant = $stock->quantite;
            
            // Augmenter la quantité du stock contrat
            $stock->quantite += $request->quantite;
            $stock->save();

            // Enregistrer le mouvement dans l'historique
            MouvementStock::creerMouvement(
                $stock->id,
                'retour_chantier',
                $request->quantite,
                $quantiteAvant,
                $stock->quantite,
                $request->date_retour,
                $request->commentaires,
                'RET-' . now()->format('YmdHis')
            );

            return redirect()->route('stock_contrat.index')
                ->with('success', 'Retour chantier effectué avec succès. Quantité retournée: ' . $request->quantite);
        }

        /**
         * Retour Projet - Transfert du stock contrat vers le stock projet global
         */
        public function retourProjet(Request $request)
        {
            $request->validate([
            'stock_id' => 'required|exists:stock_projet,id',
                'quantite' => 'required|numeric|min:1',
                'date_retour' => 'required|date',
                'commentaires' => 'nullable|string|max:500'
            ]);

            $stockContrat = StockProjet::findOrFail($request->stock_id);
            
            // Vérifier que c'est bien un stock contrat (avec id_contrat)
            if (!$stockContrat->id_contrat) {
                return redirect()->back()->with('error', 'Cette action n\'est possible que pour un stock contrat.');
            }

            // Vérifier la quantité disponible
            if ($stockContrat->quantite < $request->quantite) {
                return redirect()->back()->with('error', 'Quantité insuffisante en stock.');
            }

            // Enregistrer le mouvement avant modification
            $quantiteAvantContrat = $stockContrat->quantite;
            
            // Diminuer la quantité dans le stock contrat
            $stockContrat->quantite -= $request->quantite;
            $stockContrat->save();

            // Enregistrer le mouvement de sortie du stock contrat
            MouvementStock::creerMouvement(
                $stockContrat->id,
                'retour_projet',
                $request->quantite,
                $quantiteAvantContrat,
                $stockContrat->quantite,
                $request->date_retour,
                $request->commentaires,
                'RP-' . now()->format('YmdHis')
            );

            // Chercher ou créer le stock projet principal (sans contrat)
            $stockProjet = StockProjet::where('id_projet', $stockContrat->id_projet)
                ->where('article_id', $stockContrat->article_id)
                ->whereNull('id_contrat')
                ->first();

            if ($stockProjet) {
                // Enregistrer le mouvement avant modification du stock projet
                $quantiteAvantProjet = $stockProjet->quantite;
                
                // Augmenter la quantité du stock projet existant
                $stockProjet->quantite += $request->quantite;
                $stockProjet->save();
                
                // Enregistrer le mouvement d'entrée dans le stock projet
                MouvementStock::creerMouvement(
                    $stockProjet->id,
                    'entree',
                    $request->quantite,
                    $quantiteAvantProjet,
                    $stockProjet->quantite,
                    $request->date_retour,
                    'Retour depuis contrat: ' . ($request->commentaires ?? ''),
                    'RP-' . now()->format('YmdHis')
                );
            } else {
                // Créer un nouveau stock projet principal
                $nouveauStockProjet = StockProjet::create([
                    'id_projet' => $stockContrat->id_projet,
                    'article_id' => $stockContrat->article_id,
                    'id_contrat' => null,
                    'quantite' => $request->quantite,
                    // 'cout_moyen_pondere' => $stockContrat->cout_moyen_pondere,
                    // 'valeur_totale' => $request->quantite * $stockContrat->cout_moyen_pondere
                ]);
                
                // Enregistrer le mouvement de création du stock projet
                MouvementStock::creerMouvement(
                    $nouveauStockProjet->id,
                    'entree',
                    $request->quantite,
                    0,
                    $request->quantite,
                    $request->date_retour,
                    'Création stock projet depuis contrat: ' . ($request->commentaires ?? ''),
                    'RP-' . now()->format('YmdHis')
                );
            }

            return redirect()->route('stock_contrat.index')
                ->with('success', 'Retour projet effectué avec succès. Quantité transférée: ' . $request->quantite);
        }

        /**
         * Afficher l'historique complet des mouvements de stock
         */
        public function historiqueComplet(Request $request)
        {
            $contrat_id = session('contrat_id');
            
            // Récupérer les mouvements de stock pour le contrat actuel
            $mouvements = MouvementStock::with(['stockProjet.article', 'stockProjet.contrat', 'user'])
                ->whereHas('stockProjet', function($query) use ($contrat_id) {
                    if ($contrat_id) {
                        $query->where('id_contrat', $contrat_id);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Récupérer aussi les demandes de ravitaillement pour l'historique complet
            $demandesRavitaillement = DemandeRavitaillement::with(['lignes.article', 'demandeur'])
                ->where('contrat_id', $contrat_id)
                ->where('statut', 'approuve')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('stock_contrat.historique_complet', compact('mouvements', 'demandesRavitaillement', 'contrat_id'));
        }
    
}
