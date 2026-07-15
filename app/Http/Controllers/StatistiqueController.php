<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BU;
use App\Models\Contrat;
use App\Models\Projet;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StatistiqueController extends Controller
{
    public function __construct(
        private readonly DashboardStatsService $dashboardStats
    ) {}

    public function index(Request $request)
    {
        $id_bu = session('selected_bu');
        if (! $id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $bu = BU::find($id_bu);
        $filters = $this->dashboardStats->parseFilters($request, (int) $id_bu);

        if ($request->filled('granularite')) {
            $filters['granularite'] = $request->input('granularite', 'month');
        } else {
            $filters['granularite'] = match ($filters['periode']) {
                '1m', '3m' => 'month',
                'ytd', 'all' => 'month',
                default => 'month',
            };
        }

        $projets = Projet::where('bu_id', $id_bu)
            ->orderBy('nom_projet')
            ->get(['id', 'ref_projet', 'nom_projet']);

        $contrats = Contrat::query()
            ->whereHas('projet', fn ($q) => $q->where('bu_id', $id_bu))
            ->when($filters['projet_id'], fn ($q) => $q->where('id_projet', $filters['projet_id']))
            ->orderBy('ref_contrat')
            ->get(['id', 'ref_contrat', 'nom_contrat', 'id_projet']);

        $contratsParProjet = $contrats->groupBy('id_projet')->map(fn ($items) => $items->values());

        $summary = $this->dashboardStats->summaryKpis((int) $id_bu, $filters);
        $charts = $this->dashboardStats->chartPayload((int) $id_bu, $filters);

        $articlesAlerte = $this->countArticlesAlerte();
        $soldeCaisse = $bu ? (float) $bu->soldecaisse : ($summary['revenus_caisse'] - $summary['depenses_caisse']);

        return view('statistiques.index', compact(
            'bu',
            'filters',
            'projets',
            'contrats',
            'contratsParProjet',
            'summary',
            'charts',
            'articlesAlerte',
            'soldeCaisse'
        ));
    }

    public function getChartData(Request $request)
    {
        $id_bu = session('selected_bu');
        if (! $id_bu) {
            return response()->json(['error' => 'BU non sélectionnée'], 400);
        }

        $filters = $this->dashboardStats->parseFilters($request, (int) $id_bu);
        $filters['granularite'] = $request->input('granularite', 'month');

        return response()->json(
            $this->dashboardStats->chartPayload((int) $id_bu, $filters)
        );
    }

    public function getEvolutionData(Request $request)
    {
        $id_bu = session('selected_bu');
        if (! $id_bu) {
            return response()->json(['error' => 'BU non sélectionnée'], 400);
        }

        $filters = $this->dashboardStats->parseFilters($request, (int) $id_bu);
        $granularite = match ($request->input('period', 'month')) {
            'year' => 'year',
            'quarter' => 'quarter',
            default => 'month',
        };
        $filters['granularite'] = $granularite;

        $evolution = $this->dashboardStats->evolutionFinanciere((int) $id_bu, $filters, $granularite);

        return response()->json([
            'labels' => $evolution->pluck('label'),
            'entrees' => $evolution->pluck('entrees'),
            'sorties' => $evolution->pluck('sorties'),
        ]);
    }

    public function getRealtimeStats(Request $request)
    {
        $id_bu = session('selected_bu');
        if (! $id_bu) {
            return response()->json(['error' => 'BU non sélectionnée'], 400);
        }

        $filters = $this->dashboardStats->parseFilters($request, (int) $id_bu);
        $summary = $this->dashboardStats->summaryKpis((int) $id_bu, $filters);
        $bu = BU::find($id_bu);

        return response()->json([
            'total_bon_commandes' => $summary['total_bon_commandes'],
            'montant_bon_commandes' => $summary['montant_bon_commandes'],
            'total_contrats' => $summary['total_contrats'],
            'total_projets' => $summary['total_projets'],
            'revenus_totaux' => $summary['revenus_caisse'],
            'depenses_totales' => $summary['depenses_caisse'],
            'solde_caisse' => $bu ? (float) $bu->soldecaisse : ($summary['revenus_caisse'] - $summary['depenses_caisse']),
            'derniere_mise_a_jour' => now()->format('H:i:s'),
        ]);
    }

    public function exportPDF()
    {
        return response()->json(['message' => 'Export PDF non configuré.'], 501);
    }

    private function countArticlesAlerte(): int
    {
        if (Schema::hasColumn('articles', 'quantite_min')) {
            return (int) Article::whereColumn('quantite_stock', '<=', 'quantite_min')->count();
        }

        return (int) Article::where('quantite_stock', '<=', 0)->count();
    }
}
