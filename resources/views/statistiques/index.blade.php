@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord KPI')

@section('content')
@php
    $fmt = fn ($v) => number_format((float) $v, 0, ',', ' ');
@endphp

<div class="app-fade-in dash-fintech">
    {{-- En-tête + filtres compacts --}}
    <div class="dash-topbar">
        <div class="dash-topbar__title">
            <h1>Tableau de bord</h1>
            <p class="mb-0">{{ $bus->nom ?? 'BU' }} · <span id="lastUpdateBadge">MAJ {{ now()->format('H:i') }}</span></p>
        </div>
        <form method="GET" action="{{ route('statistiques.index') }}" id="dashboardFiltersForm" class="dash-filters">
            <select name="periode" id="periode" class="form-select form-select-sm dash-filter-select">
                @foreach([
                    '1m' => 'Ce mois',
                    '3m' => '3 derniers mois',
                    '6m' => '6 derniers mois',
                    '12m' => '12 derniers mois',
                    'ytd' => 'Année en cours',
                    'all' => 'Toute la période',
                    'custom' => 'Personnalisée',
                ] as $value => $label)
                <option value="{{ $value }}" @selected(($filters['periode'] ?? '12m') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_debut" id="date_debut"
                   class="form-control form-control-sm dash-filter-select custom-date-field {{ ($filters['periode'] ?? '') === 'custom' ? '' : 'd-none' }}"
                   value="{{ $filters['date_debut']?->format('Y-m-d') }}">
            <input type="date" name="date_fin" id="date_fin"
                   class="form-control form-control-sm dash-filter-select custom-date-field {{ ($filters['periode'] ?? '') === 'custom' ? '' : 'd-none' }}"
                   value="{{ $filters['date_fin']?->format('Y-m-d') }}">
            <select name="projet_id" id="projet_id" class="form-select form-select-sm dash-filter-select">
                <option value="">Tous les projets</option>
                @foreach($projets as $projet)
                <option value="{{ $projet->id }}" @selected(($filters['projet_id'] ?? null) == $projet->id)>
                    {{ $projet->ref_projet }} — {{ \Illuminate\Support\Str::limit($projet->nom_projet, 24) }}
                </option>
                @endforeach
            </select>
            <select name="contrat_id" id="contrat_id" class="form-select form-select-sm dash-filter-select">
                <option value="">Tous les contrats</option>
                @foreach($contrats as $contrat)
                <option value="{{ $contrat->id }}"
                        data-projet="{{ $contrat->id_projet }}"
                        @selected(($filters['contrat_id'] ?? null) == $contrat->id)>
                    {{ $contrat->ref_contrat }}
                </option>
                @endforeach
            </select>
            <button type="submit" class="app-btn app-btn-primary app-btn-sm">
                <i class="fas fa-filter"></i> Appliquer
            </button>
            <a href="{{ route('statistiques.index') }}" class="app-btn app-btn-secondary app-btn-sm" title="Réinitialiser">
                <i class="fas fa-undo"></i>
            </a>
        </form>
    </div>

    {{-- Ligne haute : hero solde + résumé + actions --}}
    <div class="row g-3 mb-3">
        <div class="col-xl-6">
            <div class="dash-card dash-hero h-100">
                <div class="dash-card__label">Solde caisse BU</div>
                <div class="dash-hero__value {{ $soldeCaisse >= 0 ? '' : 'is-negative' }}">
                    {{ $fmt($soldeCaisse) }} <span>FCFA</span>
                </div>
                @if($articlesAlerte > 0)
                <div class="dash-hero__alert">
                    <i class="fas fa-exclamation-triangle"></i> {{ $articlesAlerte }} alerte(s) stock
                </div>
                @endif
                <div class="dash-hero__chart">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="dash-card__label mb-0">Évolution financière</span>
                        <div class="dash-pills">
                            <button type="button" class="dash-pill btn-granularite active" data-granularite="month">Mensuel</button>
                            <button type="button" class="dash-pill btn-granularite" data-granularite="quarter">Trim.</button>
                            <button type="button" class="dash-pill btn-granularite" data-granularite="year">Annuel</button>
                        </div>
                    </div>
                    <div class="chart-box chart-box-hero">
                        <canvas id="chartEvolutionFinanciere"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dash-card dash-summary h-100">
                <div class="dash-card__label">Résumé période</div>
                <div class="dash-summary__item">
                    <div>
                        <div class="dash-summary__name">Entrées caisse</div>
                        <div class="dash-summary__hint">Revenus</div>
                    </div>
                    <div class="dash-summary__amount is-up">{{ $fmt($summary['revenus_caisse']) }}</div>
                </div>
                <div class="dash-summary__item">
                    <div>
                        <div class="dash-summary__name">Sorties caisse</div>
                        <div class="dash-summary__hint">Dépenses</div>
                    </div>
                    <div class="dash-summary__amount is-down">{{ $fmt($summary['depenses_caisse']) }}</div>
                </div>
                <div class="dash-summary__item">
                    <div>
                        <div class="dash-summary__name">Montant BC</div>
                        <div class="dash-summary__hint">{{ $fmt($summary['total_bon_commandes']) }} commandes</div>
                    </div>
                    <div class="dash-summary__amount">{{ $fmt($summary['montant_bon_commandes']) }}</div>
                </div>
                <div class="dash-summary__item">
                    <div>
                        <div class="dash-summary__name">Montant contrats</div>
                        <div class="dash-summary__hint">{{ $fmt($summary['total_contrats']) }} contrats</div>
                    </div>
                    <div class="dash-summary__amount">{{ $fmt($summary['montant_contrats']) }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="dash-card h-100">
                <div class="dash-card__label">Actions rapides</div>
                <div class="dash-quick">
                    <a href="{{ route('sublayouts_projet') }}" class="dash-quick__item">
                        <span class="dash-quick__icon"><i class="fas fa-project-diagram"></i></span>
                        <span>Projets</span>
                    </a>
                    <a href="{{ route('sublayouts_article') }}" class="dash-quick__item">
                        <span class="dash-quick__icon"><i class="fas fa-boxes"></i></span>
                        <span>Stock</span>
                    </a>
                    <a href="{{ route('sublayouts_tresorerie') }}" class="dash-quick__item">
                        <span class="dash-quick__icon"><i class="fas fa-wallet"></i></span>
                        <span>Trésorerie</span>
                    </a>
                    <a href="{{ route('sublayouts_vente') }}" class="dash-quick__item">
                        <span class="dash-quick__icon"><i class="fas fa-shopping-cart"></i></span>
                        <span>Vente</span>
                    </a>
                    <a href="{{ route('bon-commandes.index') }}" class="dash-quick__item">
                        <span class="dash-quick__icon"><i class="fas fa-file-invoice"></i></span>
                        <span>BC</span>
                    </a>
                    <a href="{{ route('sublayouts_until') }}" class="dash-quick__item">
                        <span class="dash-quick__icon"><i class="fas fa-ellipsis-h"></i></span>
                        <span>Plus</span>
                    </a>
                </div>
                <div class="dash-card__label mt-4 mb-2">Évolution BC</div>
                <div class="chart-box chart-box-sm">
                    <canvas id="chartEvolutionBc"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI chiffres --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="dash-card dash-kpi">
                <div class="dash-kpi__icon"><i class="fas fa-shopping-cart"></i></div>
                <div>
                    <div class="dash-kpi__value" id="kpiBcCount">{{ $fmt($summary['total_bon_commandes']) }}</div>
                    <div class="dash-kpi__label">Bons de commande</div>
                    <div class="dash-kpi__sub">{{ $fmt($summary['montant_bon_commandes']) }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dash-card dash-kpi">
                <div class="dash-kpi__icon"><i class="fas fa-file-contract"></i></div>
                <div>
                    <div class="dash-kpi__value" id="kpiContratsCount">{{ $fmt($summary['total_contrats']) }}</div>
                    <div class="dash-kpi__label">Contrats</div>
                    <div class="dash-kpi__sub">{{ $fmt($summary['montant_contrats']) }} FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dash-card dash-kpi">
                <div class="dash-kpi__icon"><i class="fas fa-project-diagram"></i></div>
                <div>
                    <div class="dash-kpi__value" id="kpiProjetsCount">{{ $fmt($summary['total_projets']) }}</div>
                    <div class="dash-kpi__label">Projets</div>
                    <div class="dash-kpi__sub">{{ $summary['projets_en_cours'] }} en cours</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dash-card dash-kpi">
                <div class="dash-kpi__icon"><i class="fas fa-hard-hat"></i></div>
                <div>
                    <div class="dash-kpi__value">{{ $fmt($summary['total_artisans']) }}</div>
                    <div class="dash-kpi__label">Artisans</div>
                    <div class="dash-kpi__sub">{{ $summary['total_sous_categories'] }} sous-catégories</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Répartitions --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-card__header">
                    <h3 class="dash-card__title">BC par fournisseur</h3>
                </div>
                <div class="chart-box chart-box-sm">
                    <canvas id="chartBcFournisseur"></canvas>
                </div>
                @include('statistiques.partials.kpi-table', [
                    'rows' => $charts['bonCommandesParFournisseur'],
                    'columns' => ['label' => 'Fournisseur', 'total' => 'Nbre BC', 'montant' => 'Montant (FCFA)'],
                ])
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-card__header">
                    <h3 class="dash-card__title">BC par statut</h3>
                </div>
                <div class="chart-box chart-box-sm">
                    <canvas id="chartBcStatut"></canvas>
                </div>
                @include('statistiques.partials.kpi-table', [
                    'rows' => $charts['bonCommandesParStatut'],
                    'columns' => ['label' => 'Statut', 'total' => 'Nombre'],
                ])
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-card__header">
                    <h3 class="dash-card__title">Projets par client</h3>
                </div>
                <div class="chart-box chart-box-sm">
                    <canvas id="chartProjetsClient"></canvas>
                </div>
                @include('statistiques.partials.kpi-table', [
                    'rows' => $charts['projetsParClient'],
                    'columns' => ['label' => 'Client', 'total' => 'Nbre projets'],
                ])
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-card__header">
                    <h3 class="dash-card__title">Contrats par client</h3>
                </div>
                <div class="chart-box chart-box-sm">
                    <canvas id="chartContratsClient"></canvas>
                </div>
                @include('statistiques.partials.kpi-table', [
                    'rows' => $charts['contratsParClient'],
                    'columns' => ['label' => 'Client', 'total' => 'Nbre contrats', 'montant' => 'Montant (FCFA)'],
                ])
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-card__header">
                    <h3 class="dash-card__title">Artisans par corps de métier</h3>
                </div>
                <div class="chart-box chart-box-sm">
                    <canvas id="chartArtisansMetier"></canvas>
                </div>
                @include('statistiques.partials.kpi-table', [
                    'rows' => $charts['artisansParCorpsMetier'],
                    'columns' => ['label' => 'Corps de métier', 'total' => 'Nbre artisans'],
                ])
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dash-card h-100">
                <div class="dash-card__header">
                    <h3 class="dash-card__title">Catalogue articles</h3>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="dash-card__label">Sous-catégories</div>
                        <div class="chart-box chart-box-sm">
                            <canvas id="chartSousCategories"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="dash-card__label">Articles par catégorie</div>
                        <div class="chart-box chart-box-sm">
                            <canvas id="chartArticlesCategorie"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dash-fintech {
    background: #f8f9fa;
    margin: -0.5rem -0.75rem;
    padding: 1.25rem 1rem 2rem;
    min-height: calc(100vh - 100px);
}

.dash-topbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.25rem;
}

.dash-topbar__title h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #033d71;
    margin: 0 0 0.2rem;
}

.dash-topbar__title p {
    font-size: 0.85rem;
    color: #6c757d;
}

.dash-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
}

.dash-filter-select {
    width: auto;
    min-width: 140px;
    max-width: 200px;
    border-radius: 999px !important;
    border-color: #e9ecef !important;
    background: #fff !important;
    height: 2.25rem !important;
    min-height: 2.25rem !important;
    font-size: 0.8rem !important;
    padding: 0.25rem 0.85rem !important;
}

.dash-card {
    background: #fff;
    border: none;
    border-radius: 1.25rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    padding: 1.25rem 1.35rem;
}

.dash-card__label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-bottom: 0.5rem;
}

.dash-card__header {
    margin-bottom: 0.75rem;
}

.dash-card__title {
    font-size: 1rem;
    font-weight: 700;
    color: #033d71;
    margin: 0;
}

.dash-hero__value {
    font-size: 2.25rem;
    font-weight: 800;
    color: #033d71;
    line-height: 1.1;
    margin-bottom: 0.5rem;
}

.dash-hero__value span {
    font-size: 1rem;
    font-weight: 600;
    color: #94a3b8;
}

.dash-hero__value.is-negative {
    color: #dc3545;
}

.dash-hero__alert {
    font-size: 0.8rem;
    color: #b45309;
    margin-bottom: 0.75rem;
}

.dash-pills {
    display: flex;
    gap: 0.35rem;
}

.dash-pill {
    border: 1px solid #e9ecef;
    background: #fff;
    color: #64748b;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    cursor: pointer;
}

.dash-pill.active,
.dash-pill:hover {
    background: #033d71;
    border-color: #033d71;
    color: #fff;
}

.dash-summary__item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.85rem 0;
    border-bottom: 1px solid #f1f3f5;
}

.dash-summary__item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.dash-summary__name {
    font-weight: 600;
    color: #033d71;
    font-size: 0.9rem;
}

.dash-summary__hint {
    font-size: 0.75rem;
    color: #94a3b8;
}

.dash-summary__amount {
    font-weight: 700;
    color: #033d71;
    font-size: 0.95rem;
}

.dash-summary__amount.is-up { color: #10b981; }
.dash-summary__amount.is-down { color: #ef4444; }

.dash-quick {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.85rem 0.5rem;
    margin-bottom: 0.5rem;
}

.dash-quick__item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
    color: #64748b;
    font-size: 0.72rem;
    font-weight: 600;
}

.dash-quick__icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: rgba(3, 61, 113, 0.08);
    color: #033d71;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    transition: background 0.2s ease, color 0.2s ease;
}

.dash-quick__item:hover {
    color: #033d71;
    text-decoration: none;
}

.dash-quick__item:hover .dash-quick__icon {
    background: #033d71;
    color: #fff;
}

.dash-kpi {
    display: flex;
    align-items: center;
    gap: 1rem;
    height: 100%;
}

.dash-kpi__icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: rgba(3, 61, 113, 0.08);
    color: #033d71;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.dash-kpi__value {
    font-size: 1.55rem;
    font-weight: 800;
    color: #033d71;
    line-height: 1.15;
}

.dash-kpi__label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748b;
}

.dash-kpi__sub {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 0.1rem;
}

.dash-fintech .chart-box {
    position: relative;
    height: 220px;
    margin-bottom: 0.75rem;
}

.dash-fintech .chart-box-hero {
    height: 200px;
    margin-bottom: 0;
}

.dash-fintech .chart-box-sm {
    height: 200px;
}

.dash-fintech .chart-empty-msg {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 0.85rem;
    pointer-events: none;
    z-index: 1;
}

.dash-fintech .kpi-mini-table {
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

.dash-fintech .kpi-mini-table thead th {
    background: #f8f9fa;
    color: #64748b;
    font-weight: 600;
    border-bottom: 1px solid #eef1f4;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.dash-fintech .kpi-mini-table td {
    border-color: #f1f3f5;
    color: #334155;
    vertical-align: middle;
}

@media (max-width: 991.98px) {
    .dash-fintech {
        margin: 0;
        padding: 0.75rem 0 1.5rem;
    }

    .dash-hero__value {
        font-size: 1.75rem;
    }

    .dash-filter-select {
        max-width: 100%;
        min-width: 120px;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js non chargé');
        return;
    }

    Chart.defaults.font.family = "'Segoe UI', 'DejaVu Sans', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 16;

    const chartData = @json($charts);
    const filterQuery = new URLSearchParams(new FormData(document.getElementById('dashboardFiltersForm'))).toString();
    const urls = {
        evolution: @json(route('statistiques.evolution-data')),
        realtime: @json(route('statistiques.realtime-stats')) + '?' + filterQuery,
    };

    const palette = ['#033d71', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#14b8a6', '#6366f1'];
    const charts = {};

    function fmtMoney(v) {
        return new Intl.NumberFormat('fr-FR').format(Math.round(v || 0)) + ' FCFA';
    }

    function pluck(rows, key) {
        return (rows || []).map(r => (r && r[key] != null) ? String(r[key]) : '');
    }

    function pluckNum(rows, key) {
        return (rows || []).map(r => Number(r[key] || 0));
    }

    function truncateLabel(label, max = 28) {
        const s = String(label || '');
        return s.length > max ? s.slice(0, max - 1) + '…' : s;
    }

    function hasData(values) {
        return (values || []).some(v => Number(v) > 0);
    }

    function toggleEmpty(canvasId, show) {
        const box = document.getElementById(canvasId)?.closest('.chart-box');
        if (!box) return;
        let msg = box.querySelector('.chart-empty-msg');
        if (show && !msg) {
            msg = document.createElement('div');
            msg.className = 'chart-empty-msg';
            msg.textContent = 'Aucune donnée sur cette période';
            box.appendChild(msg);
        } else if (!show && msg) {
            msg.remove();
        }
    }

    function barChart(id, labels, data, label, horizontal) {
        const ctx = document.getElementById(id);
        if (!ctx) return null;
        toggleEmpty(id, !hasData(data));
        const bg = labels.map((_, i) => palette[i % palette.length] + (horizontal ? 'cc' : 'dd'));
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels.map(l => truncateLabel(l, horizontal ? 32 : 18)),
                datasets: [{
                    label,
                    data,
                    backgroundColor: bg,
                    borderColor: labels.map((_, i) => palette[i % palette.length]),
                    borderWidth: 0,
                    borderRadius: 10,
                    borderSkipped: false,
                    maxBarThickness: horizontal ? 28 : 48,
                }],
            },
            options: {
                indexAxis: horizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 700, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#033d71',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            title: (items) => labels[items[0]?.dataIndex] || '',
                            label: (ctx) => ctx.dataset.label + ' : ' + ctx.parsed[horizontal ? 'x' : 'y'],
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: !horizontal, color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: { font: { size: 11 } },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: { font: { size: 11 } },
                    },
                },
            },
        });
    }

    function doughnutChart(id, labels, data) {
        const ctx = document.getElementById(id);
        if (!ctx) return null;
        toggleEmpty(id, !hasData(data));
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.map(l => truncateLabel(l, 24)),
                datasets: [{
                    data,
                    backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 12,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                animation: { animateRotate: true, duration: 800 },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 11 }, padding: 12 },
                    },
                    tooltip: {
                        backgroundColor: '#033d71',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            title: (items) => labels[items[0]?.dataIndex] || '',
                        },
                    },
                },
            },
        });
    }

    function lineChart(id, labels, datasets, moneyFormat) {
        const ctx = document.getElementById(id);
        if (!ctx) return null;
        const hasValues = datasets.some(ds => hasData(ds.data));
        toggleEmpty(id, !hasValues);
        return new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                animation: { duration: 800, easing: 'easeOutQuart' },
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        backgroundColor: '#033d71',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: moneyFormat ? {
                            label: (ctx) => ctx.dataset.label + ' : ' + fmtMoney(ctx.parsed.y),
                        } : undefined,
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: moneyFormat ? {
                            callback: (v) => new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(v),
                        } : undefined,
                    },
                },
            },
        });
    }

    function initCharts(data) {
        Object.values(charts).forEach(c => c?.destroy?.());

        const bcF = data.bonCommandesParFournisseur || [];
        charts.bcFournisseur = barChart('chartBcFournisseur', pluck(bcF, 'label'), pluckNum(bcF, 'total'), 'Nbre BC', true);
        charts.bcStatut = doughnutChart('chartBcStatut', pluck(data.bonCommandesParStatut, 'label'), pluckNum(data.bonCommandesParStatut, 'total'));
        charts.projetsClient = barChart('chartProjetsClient', pluck(data.projetsParClient, 'label'), pluckNum(data.projetsParClient, 'total'), 'Projets', true);
        charts.contratsClient = barChart('chartContratsClient', pluck(data.contratsParClient, 'label'), pluckNum(data.contratsParClient, 'total'), 'Contrats', true);
        charts.artisansMetier = doughnutChart('chartArtisansMetier', pluck(data.artisansParCorpsMetier, 'label'), pluckNum(data.artisansParCorpsMetier, 'total'));
        charts.sousCategories = barChart('chartSousCategories', pluck(data.sousCategoriesParCategorie, 'label'), pluckNum(data.sousCategoriesParCategorie, 'total'), 'Sous-catégories', false);
        charts.articlesCategorie = barChart('chartArticlesCategorie', pluck(data.articlesParCategorie, 'label'), pluckNum(data.articlesParCategorie, 'total'), 'Articles', false);

        const evoBc = data.evolutionBonCommandes || [];
        charts.evolutionBc = lineChart('chartEvolutionBc', pluck(evoBc, 'label'), [{
            label: 'Montant BC',
            data: pluckNum(evoBc, 'montant'),
            borderColor: '#033d71',
            backgroundColor: 'rgba(3, 61, 113, 0.10)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 5,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#033d71',
            pointBorderWidth: 2,
        }], true);
    }

    function loadEvolutionFinanciere(granularite) {
        const params = new URLSearchParams(new FormData(document.getElementById('dashboardFiltersForm')));
        params.set('period', granularite === 'month' ? 'month' : granularite);
        params.set('granularite', granularite);

        fetch(urls.evolution + '?' + params.toString())
            .then(r => r.json())
            .then(json => {
                charts.evolutionFin?.destroy?.();
                charts.evolutionFin = lineChart('chartEvolutionFinanciere', json.labels || [], [
                    {
                        label: 'Entrées caisse',
                        data: (json.entrees || []).map(Number),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBorderColor: '#10b981',
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                    },
                    {
                        label: 'Sorties caisse',
                        data: (json.sorties || []).map(Number),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointBorderColor: '#ef4444',
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                    },
                ], true);
            })
            .catch(() => toggleEmpty('chartEvolutionFinanciere', true));
    }

    initCharts(chartData);
    loadEvolutionFinanciere('month');

    document.querySelectorAll('.btn-granularite').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.btn-granularite').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadEvolutionFinanciere(this.dataset.granularite);
        });
    });

    document.getElementById('periode')?.addEventListener('change', function () {
        document.querySelectorAll('.custom-date-field').forEach(el => {
            el.classList.toggle('d-none', this.value !== 'custom');
        });
    });

    const projetSelect = document.getElementById('projet_id');
    const contratSelect = document.getElementById('contrat_id');
    if (projetSelect && contratSelect) {
        const allContratOptions = Array.from(contratSelect.options).slice(1);
        projetSelect.addEventListener('change', function () {
            const projetId = this.value;
            contratSelect.querySelectorAll('option:not(:first-child)').forEach(o => o.remove());
            allContratOptions.forEach(opt => {
                if (!projetId || opt.dataset.projet === projetId) {
                    contratSelect.appendChild(opt.cloneNode(true));
                }
            });
            contratSelect.value = '';
        });
    }

    setInterval(function () {
        fetch(urls.realtime)
            .then(r => r.json())
            .then(data => {
                if (data.error) return;
                document.getElementById('kpiBcCount').textContent = new Intl.NumberFormat('fr-FR').format(data.total_bon_commandes || 0);
                document.getElementById('kpiContratsCount').textContent = new Intl.NumberFormat('fr-FR').format(data.total_contrats || 0);
                document.getElementById('kpiProjetsCount').textContent = new Intl.NumberFormat('fr-FR').format(data.total_projets || 0);
                document.getElementById('lastUpdateBadge').textContent = 'MAJ ' + (data.derniere_mise_a_jour || '');
            })
            .catch(() => {});
    }, 60000);
});
</script>
@endpush
@endsection
