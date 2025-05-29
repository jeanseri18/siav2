<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projet;
use App\Models\Article;
use App\Models\BrouillardCaisse;
use App\Models\BonCommande;
use App\Models\Contrat;
use App\Models\DemandeAchat;
use App\Models\DemandeApprovisionnement;
use App\Models\DemandeCotation;
use App\Models\DemandeDepense;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        // Récupérer l'ID du bus depuis la session
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        // ==================== STATISTIQUES PRINCIPALES ====================
        
        // Projets
        $projetsEnCours = Projet::where('statut', 'en cours')
            ->where('bu_id', $id_bu)
            ->count();
        
        $totalProjets = Projet::where('bu_id', $id_bu)->count();
        
        $projetsByStatus = Projet::where('bu_id', $id_bu)
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get();

        // Finances
        $revenusTotaux = BrouillardCaisse::where('type', 'Entrée')
            ->where('bus_id', $id_bu)
            ->sum('montant');

        $depensesTotales = BrouillardCaisse::where('type', 'Sortie')
            ->where('bus_id', $id_bu)
            ->sum('montant');

        $soldeCaisse = $revenusTotaux - $depensesTotales;

        // Articles et Stock
        $articlesEnStock = Article::sum('quantite_stock');
        $categoriesStock = Article::distinct('categorie_id')->count('categorie_id');
        $articlesAlerte = Article::where('quantite_stock', '<=', 'quantite_min')->count();

        // ==================== STATISTIQUES PAR MODULE ====================

        // Contrats
        $contratsActifs = Contrat::where('statut', 'actif')->count();
        $montantContratsTotal = Contrat::where('statut', 'actif')->sum('montant');
        
        $contratsByType = Contrat::select('type_travaux', DB::raw('count(*) as total'), DB::raw('sum(montant) as montant_total'))
            ->groupBy('type_travaux')
            ->get();

        // Bons de Commande
        $bonCommandesEnCours = BonCommande::where('statut', 'en cours')->count();
        $bonCommandesTotal = BonCommande::count();
        $montantBonCommandes = BonCommande::sum('montant_total');
        
        $bonCommandesByStatus = BonCommande::select('statut', DB::raw('count(*) as total'), DB::raw('sum(montant_total) as montant'))
            ->groupBy('statut')
            ->get();

        // Demandes d'Achat
        $demandesAchatEnAttente = DemandeAchat::where('statut', 'en attente')->count();
        $demandesAchatApprouvees = DemandeAchat::where('statut', 'approuvée')->count();
        $demandesAchatTotal = DemandeAchat::count();
        
        $demandesAchatByPriorite = DemandeAchat::select('priorite', DB::raw('count(*) as total'))
            ->groupBy('priorite')
            ->get();

        // Demandes d'Approvisionnement
        $demandesApproEnAttente = DemandeApprovisionnement::where('statut', 'en attente')->count();
        $demandesApproApprouvees = DemandeApprovisionnement::where('statut', 'approuvée')->count();
        $demandesApproTotal = DemandeApprovisionnement::count();

        // Demandes de Cotation
        $demandeCotationsEnCours = DemandeCotation::where('statut', 'en cours')->count();
        $demandeCotationsTerminees = DemandeCotation::where('statut', 'terminée')->count();
        $demandeCotationsTotal = DemandeCotation::count();

        // Demandes de Dépense
        $demandesDepenseEnAttente = DemandeDepense::where('statut', 'en attente')
            ->where('bus_id', $id_bu)
            ->count();
        
        $demandesDepenseValidees = DemandeDepense::where('statut', 'validée')
            ->where('bus_id', $id_bu)
            ->count();
        
        $montantDemandesDepense = DemandeDepense::where('bus_id', $id_bu)
            ->sum('montant');

        // ==================== ÉVOLUTIONS TEMPORELLES ====================

        // Évolution financière mensuelle
        $evolutionFinanciere = BrouillardCaisse::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mois"),
            DB::raw("SUM(CASE WHEN type = 'Entrée' THEN montant ELSE 0 END) as total_entrees"),
            DB::raw("SUM(CASE WHEN type = 'Sortie' THEN montant ELSE 0 END) as total_sorties")
        )
        ->where('bus_id', $id_bu)
        ->groupBy('mois')
        ->orderBy('mois', 'ASC')
        ->get();

        // Évolution des projets par mois
        $evolutionProjets = Projet::select(
            DB::raw("DATE_FORMAT(date_creation, '%Y-%m') as mois"),
            DB::raw("count(*) as total")
        )
        ->where('bu_id', $id_bu)
        ->groupBy('mois')
        ->orderBy('mois', 'ASC')
        ->get();

        // Évolution des commandes par mois
        $evolutionCommandes = BonCommande::select(
            DB::raw("DATE_FORMAT(date_commande, '%Y-%m') as mois"),
            DB::raw("count(*) as total"),
            DB::raw("sum(montant_total) as montant")
        )
        ->groupBy('mois')
        ->orderBy('mois', 'ASC')
        ->get();

        // ==================== STATISTIQUES AVANCÉES ====================

        // Top 5 des articles les plus utilisés
        $topArticles = Article::select('nom', 'quantite_stock')
            ->orderBy('quantite_stock', 'desc')
            ->limit(5)
            ->get();

        // Répartition des dépenses par catégorie (simulée)
        $repartitionDepenses = [
            ['categorie' => 'Matériel', 'montant' => $depensesTotales * 0.40],
            ['categorie' => 'Personnel', 'montant' => $depensesTotales * 0.30],
            ['categorie' => 'Transport', 'montant' => $depensesTotales * 0.20],
            ['categorie' => 'Autres', 'montant' => $depensesTotales * 0.10],
        ];

        // Performance par projet (avec contrats)
        $performanceProjets = DB::table('projets')
            ->leftJoin('contrats', 'projets.nom_projet', '=', 'contrats.nom_projet')
            ->select(
                'projets.nom_projet',
                'projets.statut',
                DB::raw('count(contrats.id) as nb_contrats'),
                DB::raw('sum(contrats.montant) as montant_total')
            )
            ->where('projets.bu_id', $id_bu)
            ->groupBy('projets.id', 'projets.nom_projet', 'projets.statut')
            ->limit(10)
            ->get();

        // Tendances mensuelles (3 derniers mois)
        $tendancesProjets = $this->calculateTrend($evolutionProjets->take(-3));
        $tendancesFinancieres = $this->calculateFinancialTrend($evolutionFinanciere->take(-3));

        // ==================== ALERTES ET NOTIFICATIONS ====================
        
        $alertes = [];
        
        // Alerte stock faible
        if ($articlesAlerte > 0) {
            $alertes[] = [
                'type' => 'warning',
                'titre' => 'Stock Faible Détecté',
                'message' => "$articlesAlerte articles nécessitent un réapprovisionnement urgent",
                'action' => 'Voir Stock'
            ];
        }

        // Alerte demandes en attente
        if ($demandesAchatEnAttente > 0) {
            $alertes[] = [
                'type' => 'info',
                'titre' => 'Demandes d\'Achat en Attente',
                'message' => "$demandesAchatEnAttente demandes nécessitent votre attention",
                'action' => 'Traiter'
            ];
        }

        // Alerte objectifs
        if ($revenusTotaux > 0 && $tendancesFinancieres['revenus'] > 10) {
            $alertes[] = [
                'type' => 'success',
                'titre' => 'Objectif Dépassé',
                'message' => "Revenus en hausse de {$tendancesFinancieres['revenus']}% ce mois",
                'action' => 'Félicitations'
            ];
        }

        // Alerte caisse
        if ($soldeCaisse < 0) {
            $alertes[] = [
                'type' => 'danger',
                'titre' => 'Solde Négatif',
                'message' => "Le solde de caisse est négatif : " . number_format($soldeCaisse, 0, ',', ' ') . " CFA",
                'action' => 'Approvisionner'
            ];
        }

        return view('statistiques.index', compact(
            // Statistiques principales
            'projetsEnCours', 'totalProjets', 'revenusTotaux', 'depensesTotales', 
            'articlesEnStock', 'categoriesStock', 'soldeCaisse',
            
            // Statistiques par module
            'contratsActifs', 'montantContratsTotal', 'bonCommandesEnCours', 
            'bonCommandesTotal', 'montantBonCommandes', 'demandesAchatEnAttente',
            'demandesAchatApprouvees', 'demandesAchatTotal', 'demandesApproEnAttente',
            'demandesApproApprouvees', 'demandesApproTotal', 'demandeCotationsEnCours',
            'demandeCotationsTerminees', 'demandeCotationsTotal', 'demandesDepenseEnAttente',
            'demandesDepenseValidees', 'montantDemandesDepense', 'articlesAlerte',
            
            // Évolutions
            'evolutionFinanciere', 'evolutionProjets', 'evolutionCommandes',
            
            // Répartitions et analyses
            'projetsByStatus', 'contratsByType', 'bonCommandesByStatus',
            'demandesAchatByPriorite', 'repartitionDepenses', 'performanceProjets',
            'topArticles', 'tendancesProjets', 'tendancesFinancieres',
            
            // Alertes
            'alertes'
        ));
    }

    /**
     * Calculer la tendance pour les projets
     */
    private function calculateTrend($data)
    {
        if ($data->count() < 2) return 0;
        
        $current = $data->last()->total ?? 0;
        $previous = $data->take(-2)->first()->total ?? 0;
        
        if ($previous == 0) return 0;
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Calculer la tendance financière
     */
    private function calculateFinancialTrend($data)
    {
        if ($data->count() < 2) return ['revenus' => 0, 'depenses' => 0];
        
        $current = $data->last();
        $previous = $data->take(-2)->first();
        
        $revenusActuels = $current->total_entrees ?? 0;
        $revenusPrecedents = $previous->total_entrees ?? 0;
        
        $depensesActuelles = $current->total_sorties ?? 0;
        $depensesPrecedentes = $previous->total_sorties ?? 0;
        
        $tendanceRevenus = $revenusPrecedents > 0 ? 
            round((($revenusActuels - $revenusPrecedents) / $revenusPrecedents) * 100, 1) : 0;
            
        $tendanceDepenses = $depensesPrecedentes > 0 ? 
            round((($depensesActuelles - $depensesPrecedentes) / $depensesPrecedentes) * 100, 1) : 0;
        
        return [
            'revenus' => $tendanceRevenus,
            'depenses' => $tendanceDepenses
        ];
    }
/**
 * Obtenir les données d'évolution filtrées par période
 */
public function getEvolutionData(Request $request)
{
    $id_bu = session('selected_bu');
    
    if (!$id_bu) {
        return response()->json(['error' => 'Bus non sélectionné'], 400);
    }
    
    // Récupérer la période demandée (month, quarter, year)
    $period = $request->input('period', 'month');
    
    // Format SQL pour le regroupement
    $format = '%Y-%m'; // Format par défaut (mensuel)
    
    // Ajuster le format selon la période
    switch ($period) {
        case 'quarter':
            $format = '%Y-%m'; // Trimestriel (vous pouvez ajuster pour regrouper par trimestre)
            break;
        case 'year':
            $format = '%Y'; // Annuel
            break;
    }
    
    // Récupérer les données filtrées
    $evolutionFinanciere = BrouillardCaisse::select(
        DB::raw("DATE_FORMAT(created_at, '$format') as mois"),
        DB::raw("SUM(CASE WHEN type = 'Entrée' THEN montant ELSE 0 END) as total_entrees"),
        DB::raw("SUM(CASE WHEN type = 'Sortie' THEN montant ELSE 0 END) as total_sorties")
    )
    ->where('bus_id', $id_bu)
    ->groupBy('mois')
    ->orderBy('mois', 'ASC')
    ->get();
    
    // Formater les données pour le graphique
    $labels = $evolutionFinanciere->pluck('mois');
    $entrees = $evolutionFinanciere->pluck('total_entrees');
    $sorties = $evolutionFinanciere->pluck('total_sorties');
    
    return response()->json([
        'labels' => $labels,
        'entrees' => $entrees,
        'sorties' => $sorties
    ]);
}

/**
 * Exporter le dashboard en PDF
 */
public function exportPDF()
{
    $id_bu = session('selected_bu');
    
    if (!$id_bu) {
        return response()->json(['error' => 'Bus non sélectionné'], 400);
    }
    
    // Récupérer toutes les données nécessaires pour le PDF
    // Vous pouvez réutiliser la même logique que dans la méthode index()
    
    // Projets
    $projetsEnCours = Projet::where('statut', 'en cours')
        ->where('bu_id', $id_bu)
        ->count();
    
    $totalProjets = Projet::where('bu_id', $id_bu)->count();
    
    // Finances
    $revenusTotaux = BrouillardCaisse::where('type', 'Entrée')
        ->where('bus_id', $id_bu)
        ->sum('montant');

    $depensesTotales = BrouillardCaisse::where('type', 'Sortie')
        ->where('bus_id', $id_bu)
        ->sum('montant');
    
    // Ajouter les autres données nécessaires...
    
    // Générer le PDF
    // Vous aurez besoin d'installer une bibliothèque comme DomPDF
    // composer require barryvdh/laravel-dompdf
    
    // $pdf = PDF::loadView('statistiques.export-pdf', compact(
    //     'projetsEnCours', 'totalProjets', 'revenusTotaux', 'depensesTotales'
    //     // Ajouter les autres variables nécessaires
    // ));
    
    // return $pdf->download('dashboard-' . now()->format('Y-m-d') . '.pdf');
    
    // Si vous n'avez pas encore configuré la génération PDF, retournez simplement un JSON pour tester
    // return response()->json(['success' => true, 'message' => 'Export PDF simulé avec succès']);
}

/**
 * API pour obtenir des statistiques en temps réel
 * Cette méthode existe déjà dans votre contrôleur, mais assurez-vous qu'elle retourne les bonnes données
 */
public function getRealtimeStats()
{
    $id_bu = session('selected_bu');
    
    if (!$id_bu) {
        return response()->json(['error' => 'Bus non sélectionné'], 400);
    }

    $stats = [
        'projets_en_cours' => Projet::where('statut', 'en cours')->where('bu_id', $id_bu)->count(),
        'revenus_totaux' => BrouillardCaisse::where('type', 'Entrée')->where('bus_id', $id_bu)->sum('montant'),
        'depenses_totales' => BrouillardCaisse::where('type', 'Sortie')->where('bus_id', $id_bu)->sum('montant'),
        'articles_en_stock' => Article::sum('quantite_stock'),
        'derniere_mise_a_jour' => now()->format('H:i:s')
    ];

    return response()->json($stats);

}
}