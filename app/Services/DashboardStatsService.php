<?php

namespace App\Services;

use App\Models\Artisan;
use App\Models\BonCommande;
use App\Models\BrouillardCaisse;
use App\Models\Categorie;
use App\Models\Contrat;
use App\Models\Projet;
use App\Models\SousCategorie;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    private const BROUILLARD_ENTREE_SQL = "LOWER(TRIM(COALESCE(type, ''))) LIKE 'entr%'";

    /**
     * @return array{
     *     date_debut: ?Carbon,
     *     date_fin: ?Carbon,
     *     projet_id: ?int,
     *     contrat_id: ?int,
     *     periode: string
     * }
     */
    public function parseFilters(Request $request, int $buId): array
    {
        $periode = $request->input('periode', '12m');
        $projetId = $request->filled('projet_id') ? (int) $request->projet_id : null;
        $contratId = $request->filled('contrat_id') ? (int) $request->contrat_id : null;

        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $dateDebut = Carbon::parse($request->input('date_debut'))->startOfDay();
            $dateFin = Carbon::parse($request->input('date_fin'))->endOfDay();
            $periode = 'custom';
        } else {
            [$dateDebut, $dateFin] = $this->resolvePeriod($periode);
        }

        if ($contratId) {
            $contrat = Contrat::where('id', $contratId)
                ->whereHas('projet', fn ($q) => $q->where('bu_id', $buId))
                ->first();
            if ($contrat) {
                $projetId = (int) $contrat->id_projet;
            } else {
                $contratId = null;
            }
        }

        if ($projetId && ! Projet::where('id', $projetId)->where('bu_id', $buId)->exists()) {
            $projetId = null;
        }

        return [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'projet_id' => $projetId,
            'contrat_id' => $contratId,
            'periode' => $periode,
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolvePeriod(string $periode): array
    {
        $now = Carbon::now();

        return match ($periode) {
            '1m' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            '3m' => [$now->copy()->subMonths(2)->startOfMonth(), $now->copy()->endOfMonth()],
            '6m' => [$now->copy()->subMonths(5)->startOfMonth(), $now->copy()->endOfMonth()],
            'ytd' => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            '12m' => [$now->copy()->subMonths(11)->startOfMonth(), $now->copy()->endOfMonth()],
            'all' => [Carbon::parse('2000-01-01')->startOfDay(), $now->copy()->endOfDay()],
            default => [$now->copy()->subMonths(11)->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function summaryKpis(int $buId, array $filters): array
    {
        $qBc = $this->bonCommandesQuery($buId, $filters);
        $qContrats = $this->contratsQuery($buId, $filters);
        $qProjets = $this->projetsQuery($buId, $filters);

        $revenus = (float) $this->brouillardQuery($buId, $filters)
            ->whereRaw(self::BROUILLARD_ENTREE_SQL)
            ->sum('montant');

        $depenses = (float) $this->brouillardQuery($buId, $filters)
            ->whereRaw('NOT ('.self::BROUILLARD_ENTREE_SQL.')')
            ->sum('montant');

        return [
            'total_bon_commandes' => (clone $qBc)->count(),
            'montant_bon_commandes' => (float) (clone $qBc)->sum('montant_total'),
            'total_contrats' => (clone $qContrats)->count(),
            'montant_contrats' => (float) (clone $qContrats)->sum('montant'),
            'total_projets' => (clone $qProjets)->count(),
            'projets_en_cours' => (clone $qProjets)->where('statut', 'en cours')->count(),
            'total_artisans' => $this->artisansQuery($buId, $filters)->count(),
            'total_sous_categories' => (int) SousCategorie::count(),
            'revenus_caisse' => $revenus,
            'depenses_caisse' => $depenses,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function bonCommandesParFournisseur(int $buId, array $filters, int $limit = 12): Collection
    {
        return $this->bonCommandesQuery($buId, $filters)
            ->leftJoin('client_fournisseurs as cf', 'bon_commandes.fournisseur_id', '=', 'cf.id')
            ->select(
                DB::raw($this->clientLabelSql('cf.nom_raison_sociale', 'Fournisseur').' as label'),
                DB::raw('COUNT(bon_commandes.id) as total'),
                DB::raw('COALESCE(SUM(bon_commandes.montant_total), 0) as montant')
            )
            ->groupBy('cf.id', 'cf.nom_raison_sociale', 'bon_commandes.fournisseur_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function bonCommandesParStatut(int $buId, array $filters): Collection
    {
        return $this->bonCommandesQuery($buId, $filters)
            ->select('statut', DB::raw('COUNT(*) as total'))
            ->groupBy('statut')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => (object) [
                'label' => $this->humanizeStatut($row->statut),
                'total' => (int) $row->total,
            ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function projetsParClient(int $buId, array $filters, int $limit = 12): Collection
    {
        return $this->projetsQuery($buId, $filters)
            ->leftJoin('client_fournisseurs as cf', DB::raw('CAST(projets.client AS UNSIGNED)'), '=', 'cf.id')
            ->select(
                DB::raw($this->clientLabelSql('cf.nom_raison_sociale', 'Client').' as label'),
                DB::raw('COUNT(projets.id) as total')
            )
            ->groupBy('cf.id', 'cf.nom_raison_sociale', 'projets.client')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function contratsParClient(int $buId, array $filters, int $limit = 12): Collection
    {
        return $this->contratsQuery($buId, $filters)
            ->leftJoin('client_fournisseurs as cf', 'contrats.client_id', '=', 'cf.id')
            ->select(
                DB::raw($this->clientLabelSql('cf.nom_raison_sociale', 'Client').' as label'),
                DB::raw('COUNT(contrats.id) as total'),
                DB::raw('COALESCE(SUM(contrats.montant), 0) as montant')
            )
            ->groupBy('cf.id', 'cf.nom_raison_sociale', 'contrats.client_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function artisansParCorpsMetier(int $buId, array $filters): Collection
    {
        return $this->artisansQuery($buId, $filters)
            ->leftJoin('corp_metiers as cm', 'artisan.id_corpmetier', '=', 'cm.id')
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(cm.nom), ''), 'Corps de métier non renseigné') as label"),
                DB::raw('COUNT(DISTINCT artisan.id) as total')
            )
            ->groupBy('cm.id', 'cm.nom')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function sousCategoriesParCategorie(int $limit = 12): Collection
    {
        return SousCategorie::query()
            ->join('categories as c', 'souscategories.categorie_id', '=', 'c.id')
            ->select(
                'c.nom as label',
                DB::raw('COUNT(souscategories.id) as total')
            )
            ->groupBy('c.id', 'c.nom')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function articlesParCategorie(int $limit = 12): Collection
    {
        return DB::table('articles')
            ->join('categories as c', 'articles.categorie_id', '=', 'c.id')
            ->select('c.nom as label', DB::raw('COUNT(articles.id) as total'))
            ->groupBy('c.id', 'c.nom')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function evolutionBonCommandes(int $buId, array $filters, string $granularite = 'month'): Collection
    {
        if ($granularite === 'quarter') {
            $rows = $this->bonCommandesQuery($buId, $filters)
                ->select(
                    DB::raw("CONCAT(YEAR(bon_commandes.date_commande), '-Q', QUARTER(bon_commandes.date_commande)) as periode"),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('COALESCE(SUM(bon_commandes.montant_total), 0) as montant')
                )
                ->groupBy('periode')
                ->orderBy('periode')
                ->get();

            return $rows->map(function ($row) {
                if (preg_match('/^(\d{4})-Q(\d)$/', (string) $row->periode, $m)) {
                    $row->label = 'T'.$m[2].' '.$m[1];
                } else {
                    $row->label = (string) $row->periode;
                }

                return $row;
            });
        }

        $format = $granularite === 'year' ? '%Y' : '%Y-%m';

        return $this->bonCommandesQuery($buId, $filters)
            ->select(
                DB::raw("DATE_FORMAT(bon_commandes.date_commande, '{$format}') as periode"),
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(bon_commandes.montant_total), 0) as montant')
            )
            ->groupBy('periode')
            ->orderBy('periode')
            ->get()
            ->map(function ($row) use ($granularite) {
                $row->label = $this->formatPeriodeLabel((string) $row->periode, $granularite);

                return $row;
            });
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function evolutionFinanciere(int $buId, array $filters, string $granularite = 'month'): Collection
    {
        $format = match ($granularite) {
            'year' => '%Y',
            default => '%Y-%m',
        };

        $caseEntree = 'CASE WHEN '.self::BROUILLARD_ENTREE_SQL.' THEN montant ELSE 0 END';
        $caseSortie = 'CASE WHEN NOT ('.self::BROUILLARD_ENTREE_SQL.') THEN montant ELSE 0 END';

        return $this->brouillardQuery($buId, $filters)
            ->selectRaw("DATE_FORMAT(COALESCE(date_operation, DATE(created_at)), '{$format}') as periode")
            ->selectRaw("SUM({$caseEntree}) as entrees")
            ->selectRaw("SUM({$caseSortie}) as sorties")
            ->groupBy('periode')
            ->orderBy('periode')
            ->get()
            ->map(function ($row) use ($granularite) {
                $row->label = $this->formatPeriodeLabel((string) $row->periode, $granularite);

                return $row;
            });
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function bonCommandesQuery(int $buId, array $filters): Builder
    {
        $query = BonCommande::query()
            ->where(function (Builder $q) use ($buId) {
                $q->whereHas('projet', fn ($p) => $p->where('bu_id', $buId))
                    ->orWhereHas('demandeAchat.projet', fn ($p) => $p->where('bu_id', $buId));
            });

        if (! empty($filters['projet_id'])) {
            $projetId = (int) $filters['projet_id'];
            $query->where(function (Builder $q) use ($projetId) {
                $q->where('projet_id', $projetId)
                    ->orWhereHas('demandeAchat', fn ($da) => $da->where('projet_id', $projetId));
            });
        }

        if (! empty($filters['date_debut']) && ! empty($filters['date_fin'])) {
            $query->whereBetween('date_commande', [
                $filters['date_debut']->toDateString(),
                $filters['date_fin']->toDateString(),
            ]);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function contratsQuery(int $buId, array $filters): Builder
    {
        $query = Contrat::query()
            ->whereHas('projet', fn ($q) => $q->where('bu_id', $buId));

        if (! empty($filters['projet_id'])) {
            $query->where('id_projet', (int) $filters['projet_id']);
        }

        if (! empty($filters['contrat_id'])) {
            $query->where('id', (int) $filters['contrat_id']);
        }

        if (! empty($filters['date_debut']) && ! empty($filters['date_fin'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->whereBetween('contrats.date_debut', [
                    $filters['date_debut']->toDateString(),
                    $filters['date_fin']->toDateString(),
                ])->orWhereBetween('contrats.created_at', [$filters['date_debut'], $filters['date_fin']]);
            });
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function projetsQuery(int $buId, array $filters): Builder
    {
        $query = Projet::query()->where('bu_id', $buId);

        if (! empty($filters['projet_id'])) {
            $query->where('id', (int) $filters['projet_id']);
        }

        if (! empty($filters['date_debut']) && ! empty($filters['date_fin'])) {
            $query->whereBetween('date_creation', [
                $filters['date_debut']->toDateString(),
                $filters['date_fin']->toDateString(),
            ]);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function artisansQuery(int $buId, array $filters): Builder
    {
        $query = Artisan::query();

        if (! empty($filters['contrat_id'])) {
            return $query->whereHas('prestations', fn ($p) => $p->where('id_contrat', (int) $filters['contrat_id']));
        }

        if (! empty($filters['projet_id'])) {
            return $query->whereHas('prestations.contrat', fn ($c) => $c->where('id_projet', (int) $filters['projet_id']));
        }

        return $query->whereHas('prestations.contrat.projet', fn ($p) => $p->where('bu_id', $buId));
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function brouillardQuery(int $buId, array $filters): Builder
    {
        $query = BrouillardCaisse::query()->where('bus_id', $buId);

        if (! empty($filters['date_debut']) && ! empty($filters['date_fin'])) {
            $query->whereBetween(DB::raw('COALESCE(date_operation, DATE(created_at))'), [
                $filters['date_debut']->toDateString(),
                $filters['date_fin']->toDateString(),
            ]);
        }

        return $query;
    }

    /**
     * @return array<string, mixed>
     */
    public function chartPayload(int $buId, array $filters): array
    {
        $granularite = $filters['granularite'] ?? 'month';

        return [
            'bonCommandesParFournisseur' => $this->serializeChartRows($this->bonCommandesParFournisseur($buId, $filters)),
            'bonCommandesParStatut' => $this->serializeChartRows($this->bonCommandesParStatut($buId, $filters)),
            'projetsParClient' => $this->serializeChartRows($this->projetsParClient($buId, $filters)),
            'contratsParClient' => $this->serializeChartRows($this->contratsParClient($buId, $filters)),
            'artisansParCorpsMetier' => $this->serializeChartRows($this->artisansParCorpsMetier($buId, $filters)),
            'sousCategoriesParCategorie' => $this->serializeChartRows($this->sousCategoriesParCategorie()),
            'articlesParCategorie' => $this->serializeChartRows($this->articlesParCategorie()),
            'evolutionBonCommandes' => $this->serializeEvolutionRows($this->evolutionBonCommandes($buId, $filters, $granularite)),
            'evolutionFinanciere' => $this->serializeEvolutionRows($this->evolutionFinanciere($buId, $filters, $granularite), true),
        ];
    }

    private function clientLabelSql(string $nameColumn, string $fallbackPrefix): string
    {
        return "COALESCE(NULLIF(TRIM({$nameColumn}), ''), '{$fallbackPrefix} inconnu')";
    }

    private function humanizeStatut(?string $statut): string
    {
        return match ($statut) {
            'en attente' => 'En attente',
            'confirmée' => 'Confirmée',
            'livrée' => 'Livrée',
            'annulée' => 'Annulée',
            'partiellement_reçu' => 'Partiellement reçu',
            'reçu' => 'Reçu',
            default => $statut ? ucfirst($statut) : 'Non renseigné',
        };
    }

    private function formatPeriodeLabel(string $periode, string $granularite): string
    {
        if ($granularite === 'year' || str_starts_with($periode, 'T')) {
            return $periode;
        }

        if (preg_match('/^(\d{4})-(\d{2})$/', $periode, $m)) {
            $mois = (int) $m[2];
            $noms = ['', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

            return ($noms[$mois] ?? $m[2]).' '.$m[1];
        }

        return $periode;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function serializeChartRows(Collection $rows): array
    {
        return $rows->map(function ($row) {
            $item = [
                'label' => (string) ($row->label ?? 'Non renseigné'),
                'total' => (int) ($row->total ?? 0),
            ];

            if (isset($row->montant)) {
                $item['montant'] = (float) $row->montant;
            }

            return $item;
        })->values()->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function serializeEvolutionRows(Collection $rows, bool $financial = false): array
    {
        return $rows->map(function ($row) use ($financial) {
            $label = (string) ($row->label ?? $row->periode ?? '');

            if ($financial) {
                return [
                    'label' => $label,
                    'entrees' => (float) ($row->entrees ?? 0),
                    'sorties' => (float) ($row->sorties ?? 0),
                ];
            }

            return [
                'label' => $label,
                'total' => (int) ($row->total ?? 0),
                'montant' => (float) ($row->montant ?? 0),
            ];
        })->values()->all();
    }
}
