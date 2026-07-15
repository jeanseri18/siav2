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
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Support\PdfBranding;

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
        
        $articles = Article::with(['categorie', 'sousCategorie', 'fournisseur', 'uniteMesure'])->get();
        $stockIndicators = $this->stockIndicatorsByArticleId($projets_bu);

        return view('articles.index', compact('articles', 'projets_bu', 'stockIndicators'));
    }

    public function exportListePdf()
    {
        $bu_id = session('selected_bu');
        if (! $bu_id) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner une BU avant d\'accéder à cette page.']);
        }

        $projets_bu = Projet::where('bu_id', $bu_id)->pluck('id')->toArray();
        $articles = Article::with(['categorie', 'sousCategorie', 'fournisseur', 'uniteMesure'])
            ->orderBy('nom')
            ->orderBy('reference')
            ->get();
        $stockIndicators = $this->stockIndicatorsByArticleId($projets_bu);
        $stockQuantities = $this->stockQuantitiesByArticleId($projets_bu);
        $pdfBranding = PdfBranding::forBu((int) $bu_id);

        $pdf = Pdf::loadView('articles.liste-export', [
            'articles' => $articles,
            'stockIndicators' => $stockIndicators,
            'stockQuantities' => $stockQuantities,
            'pdfBranding' => $pdfBranding,
            'documentTitle' => 'Liste des articles',
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('liste-articles-'.now()->format('Y-m-d').'.pdf', ['Attachment' => false]);
    }

    protected function stockQuantitiesByArticleId(array $projetIds): array
    {
        if ($projetIds === []) {
            return [];
        }

        return DB::table('stock_projet')
            ->whereIn('id_projet', $projetIds)
            ->groupBy('article_id')
            ->selectRaw('article_id, SUM(quantite) as q')
            ->pluck('q', 'article_id')
            ->map(fn ($q) => (float) $q)
            ->all();
    }

    /**
     * Agrégats par article pour la liste (projets de la BU courante).
     * Les requêtes étaient auparavant dans la vue avec des statuts / filtres incorrects.
     *
     * @param  array<int>  $projetIds
     * @return array<int, array<string, float>>
     */
    protected function stockIndicatorsByArticleId(array $projetIds): array
    {
        $acc = [];

        $ensure = static function (int $articleId) use (&$acc): int {
            if (! isset($acc[$articleId])) {
                $acc[$articleId] = [
                    'demande_ravitaillement' => 0.0,
                    'ravitaillement_en_cours' => 0.0,
                    'retour_ravitaillement' => 0.0,
                    'approvisionnement_en_cours' => 0.0,
                    'retour_appro' => 0.0,
                    'transfert_in' => 0.0,
                    'transfert_out' => 0.0,
                ];
            }

            return $articleId;
        };

        $add = static function (int $articleId, string $key, float $value) use (&$acc, $ensure): void {
            $id = $ensure($articleId);
            $acc[$id][$key] += $value;
        };

        if ($projetIds === []) {
            return [];
        }

        // — Demande de ravitaillement : en attente d’approbation
        $rows = DB::table('lignes_demande_ravitaillement as l')
            ->join('demandes_ravitaillement as d', 'l.demande_ravitaillement_id', '=', 'd.id')
            ->join('contrats as c', 'd.contrat_id', '=', 'c.id')
            ->whereIn('c.id_projet', $projetIds)
            ->where('d.statut', 'en_attente')
            ->groupBy('l.article_id')
            ->selectRaw('l.article_id, SUM(l.quantite_demandee) as q')
            ->get();
        foreach ($rows as $r) {
            $add((int) $r->article_id, 'demande_ravitaillement', (float) $r->q);
        }

        $nonNegative = static function (string $expr): string {
            return "CASE WHEN ({$expr}) > 0 THEN ({$expr}) ELSE 0 END";
        };

        // — Ravitaillement en cours : approuvé mais pas encore tout livré depuis le magasin
        $exprApprouvee = 'COALESCE(l.quantite_approuvee, l.quantite_demandee) - COALESCE(l.quantite_livree, 0)';
        $rows = DB::table('lignes_demande_ravitaillement as l')
            ->join('demandes_ravitaillement as d', 'l.demande_ravitaillement_id', '=', 'd.id')
            ->join('contrats as c', 'd.contrat_id', '=', 'c.id')
            ->whereIn('c.id_projet', $projetIds)
            ->where('d.statut', 'approuvee')
            ->groupBy('l.article_id')
            ->selectRaw('l.article_id, SUM('.$nonNegative($exprApprouvee).') as q')
            ->get();
        foreach ($rows as $r) {
            $add((int) $r->article_id, 'ravitaillement_en_cours', (float) $r->q);
        }

        // — Ravitaillement en cours : livré vers le chantier, pas encore tout réceptionné
        if (Schema::hasColumn('lignes_demande_ravitaillement', 'quantite_recue')) {
            $exprLivree = 'COALESCE(l.quantite_livree, 0) - COALESCE(l.quantite_recue, 0)';
            $rows = DB::table('lignes_demande_ravitaillement as l')
                ->join('demandes_ravitaillement as d', 'l.demande_ravitaillement_id', '=', 'd.id')
                ->join('contrats as c', 'd.contrat_id', '=', 'c.id')
                ->whereIn('c.id_projet', $projetIds)
                ->whereIn('d.statut', ['en_cours', 'livree'])
                ->groupBy('l.article_id')
                ->selectRaw('l.article_id, SUM('.$nonNegative($exprLivree).') as q')
                ->get();
            foreach ($rows as $r) {
                $add((int) $r->article_id, 'ravitaillement_en_cours', (float) $r->q);
            }
        }

        // — Retour de ravitaillement : quantités retournées non validées côté gestionnaire
        if (Schema::hasColumn('lignes_demande_ravitaillement', 'quantite_retournee')
            && Schema::hasColumn('lignes_demande_ravitaillement', 'retour_valide')) {
            $rows = DB::table('lignes_demande_ravitaillement as l')
                ->join('demandes_ravitaillement as d', 'l.demande_ravitaillement_id', '=', 'd.id')
                ->join('contrats as c', 'd.contrat_id', '=', 'c.id')
                ->whereIn('c.id_projet', $projetIds)
                ->where('l.retour_valide', false)
                ->where('l.quantite_retournee', '>', 0)
                ->groupBy('l.article_id')
                ->selectRaw('l.article_id, SUM(l.quantite_retournee) as q')
                ->get();
            foreach ($rows as $r) {
                $add((int) $r->article_id, 'retour_ravitaillement', (float) $r->q);
            }
        }

        // — Approvisionnement en cours : lignes BC encore à recevoir (statuts réels de bon_commandes)
        $statutsBcEnCours = ['en attente', 'confirmée', 'partiellement_reçu', 'livrée'];
        $qAppro = DB::table('lignes_bon_commande as lb')
            ->join('bon_commandes as bc', 'lb.bon_commande_id', '=', 'bc.id')
            ->leftJoin('demande_approvisionnements as da', 'bc.demande_approvisionnement_id', '=', 'da.id')
            ->whereIn('bc.statut', $statutsBcEnCours)
            ->whereRaw('lb.quantite > COALESCE(lb.quantite_recue, 0)');

        if (Schema::hasColumn('bon_commandes', 'projet_id')) {
            $qAppro->where(function ($w) use ($projetIds) {
                $w->whereIn('bc.projet_id', $projetIds)
                    ->orWhereIn('da.projet_id', $projetIds);
            });
        } else {
            $qAppro->whereIn('da.projet_id', $projetIds);
        }

        $rows = $qAppro->groupBy('lb.article_id')
            ->selectRaw('lb.article_id, SUM(lb.quantite - COALESCE(lb.quantite_recue, 0)) as q')
            ->get();
        foreach ($rows as $r) {
            $add((int) $r->article_id, 'approvisionnement_en_cours', (float) $r->q);
        }

        // — Retour d’approvisionnement en attente
        if (Schema::hasTable('retour_approvisionnement')) {
            $rows = DB::table('retour_approvisionnement')
                ->whereIn('projet_id', $projetIds)
                ->where('statut', 'en_attente')
                ->groupBy('article_id')
                ->selectRaw('article_id, SUM(quantite_retournee) as q')
                ->get();
            foreach ($rows as $r) {
                $add((int) $r->article_id, 'retour_appro', (float) $r->q);
            }
        }

        // — Transferts : en route tant qu’aucun mouvement TR-IN n’existe pour ce transfert
        $driver = DB::connection()->getDriverName();
        $concatTrIn = ($driver === 'sqlite')
            ? "( 'TR-IN-' || ts.id )"
            : "CONCAT('TR-IN-', ts.id)";

        $in = implode(',', array_fill(0, count($projetIds), '?'));
        $bindingsIn = $projetIds;
        $sqlIn = "
            SELECT ts.article_id, SUM(ts.quantite) AS q
            FROM transfert_stock ts
            WHERE ts.id_projet_destination IN ({$in})
              AND NOT EXISTS (
                SELECT 1 FROM mouvements_stock ms
                WHERE ms.reference_mouvement = {$concatTrIn}
              )
            GROUP BY ts.article_id
        ";
        foreach (DB::select($sqlIn, $bindingsIn) as $r) {
            $add((int) $r->article_id, 'transfert_in', (float) $r->q);
        }

        $sqlOut = "
            SELECT ts.article_id, SUM(ts.quantite) AS q
            FROM transfert_stock ts
            WHERE ts.id_projet_source IN ({$in})
              AND NOT EXISTS (
                SELECT 1 FROM mouvements_stock ms
                WHERE ms.reference_mouvement = {$concatTrIn}
              )
            GROUP BY ts.article_id
        ";
        foreach (DB::select($sqlOut, $bindingsIn) as $r) {
            $add((int) $r->article_id, 'transfert_out', (float) $r->q);
        }

        return $acc;
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
            // 'cout_moyen_pondere' => 'required|numeric',
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
