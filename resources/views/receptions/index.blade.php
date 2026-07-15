@extends('layouts.app')

@section('content')
<div class="container-fluid receptions-page">
    <x-stock-flux-nav module="reception" context="list" />
    <div class="row">
        <div class="col-12">
            <div class="card receptions-card shadow-sm">
                <div class="card-header receptions-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h3 class="card-title mb-0 text-white d-flex align-items-center gap-2">
                        <i class="fas fa-truck"></i>
                        Gestion des réceptions
                    </h3>
                    <x-export-pdf-button :route="route('receptions.export.pdf')" class="btn btn-sm btn-outline-light" />
                </div>

                <div class="card-body receptions-card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif

                    <ul class="nav nav-tabs receptions-nav-tabs mb-0" id="receptionsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active d-flex align-items-center gap-2"
                                    id="tab-bc-a-receptionner"
                                    data-bs-toggle="tab"
                                    data-bs-target="#pane-bc-a-receptionner"
                                    type="button"
                                    role="tab"
                                    aria-controls="pane-bc-a-receptionner"
                                    aria-selected="true">
                                <i class="fas fa-file-invoice"></i>
                                Bons de commande à réceptionner
                                @if($bonCommandes->total() > 0)
                                    <span class="badge rounded-pill receptions-tab-badge">{{ $bonCommandes->total() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center gap-2"
                                    id="tab-liste-receptions"
                                    data-bs-toggle="tab"
                                    data-bs-target="#pane-liste-receptions"
                                    type="button"
                                    role="tab"
                                    aria-controls="pane-liste-receptions"
                                    aria-selected="false">
                                <i class="fas fa-list-ul"></i>
                                Liste des réceptions
                                @if($receptions->total() > 0)
                                    <span class="badge rounded-pill receptions-tab-badge receptions-tab-badge--muted">{{ $receptions->total() }}</span>
                                @endif
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content receptions-tab-content border border-top-0 rounded-bottom p-3 p-lg-4 bg-white" id="receptionsTabsContent">
                        {{-- Onglet : Bons de commande --}}
                        <div class="tab-pane fade show active" id="pane-bc-a-receptionner" role="tabpanel" aria-labelledby="tab-bc-a-receptionner" tabindex="0">
                            @if($bonCommandes->count() > 0)
                                <div class="receptions-table-wrap">
                                    <table class="table receptions-table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Référence</th>
                                                <th>Fournisseur</th>
                                                <th>Date commande</th>
                                                <th>Date livraison prévue</th>
                                                <th>Statut</th>
                                                <th style="min-width: 140px;">Progression</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bonCommandes as $bonCommande)
                                                @php
                                                    $totalQuantite = $bonCommande->lignes->sum('quantite');
                                                    $totalRecue = $bonCommande->lignes->sum('quantite_recue');
                                                    $pourcentage = $totalQuantite > 0 ? round(($totalRecue / $totalQuantite) * 100, 1) : 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="receptions-ref">{{ $bonCommande->reference }}</span>
                                                    </td>
                                                    <td>{{ $bonCommande->fournisseur->nom ?? 'N/A' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if($bonCommande->date_livraison_prevue)
                                                            {{ \Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->format('d/m/Y') }}
                                                            @if(\Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->isPast())
                                                                <span class="badge receptions-badge receptions-badge--delay ms-1">En retard</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted small">Non définie</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pourcentage == 0)
                                                            <span class="badge receptions-badge receptions-badge--pending">En attente</span>
                                                        @elseif($pourcentage < 100)
                                                            <span class="badge receptions-badge receptions-badge--partial">Partielle</span>
                                                        @else
                                                            <span class="badge receptions-badge receptions-badge--done">Complète</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="progress receptions-progress">
                                                            <div class="progress-bar receptions-progress-bar {{ $pourcentage == 100 ? 'receptions-progress-bar--full' : ($pourcentage > 0 ? 'receptions-progress-bar--mid' : '') }}"
                                                                 role="progressbar"
                                                                 style="width: {{ $pourcentage }}%"
                                                                 aria-valuenow="{{ $pourcentage }}"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100">
                                                                <span class="receptions-progress-label">{{ $pourcentage }}%</span>
                                                            </div>
                                                        </div>
                                                        <small class="text-muted receptions-progress-caption">{{ $totalRecue }}/{{ $totalQuantite }} articles</small>
                                                    </td>
                                                    <td class="text-end receptions-actions-cell">
                                                        <div class="dropdown receptions-actions-dropdown">
                                                            <button class="btn btn-sm receptions-btn-actions dropdown-toggle" type="button"
                                                                    data-bs-toggle="dropdown"
                                                                    data-bs-offset="0,6"
                                                                    data-bs-auto-close="true"
                                                                    data-bs-popper-config='{"strategy":"fixed"}'
                                                                    aria-expanded="false" aria-haspopup="true">
                                                                Actions
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end receptions-dropdown-menu shadow border-0">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('receptions.show', $bonCommande->id) }}">
                                                                        <i class="fas fa-eye me-2 text-primary"></i>Voir les détails
                                                                    </a>
                                                                </li>
                                                                @if($pourcentage < 100)
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('receptions.create', $bonCommande->id) }}">
                                                                            <i class="fas fa-truck-loading me-2 text-primary"></i>Effectuer réception
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route('receptions.non-conformite.create', $bonCommande->id) }}">
                                                                            <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Signaler une non-conformité
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('receptions.history', $bonCommande->id) }}">
                                                                        <i class="fas fa-history me-2 text-muted"></i>Historique
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end pt-3">
                                    {{ $bonCommandes->links() }}
                                </div>
                            @else
                                <div class="receptions-empty text-center py-5 px-3">
                                    <div class="receptions-empty-icon mb-3">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <h5 class="receptions-empty-title">Aucun bon de commande en attente de réception</h5>
                                    <p class="text-muted mb-4">Tous les bons de commande ont été entièrement reçus ou aucun bon de commande n'est validé.</p>
                                    <a href="{{ route('bon-commandes.index') }}" class="btn receptions-btn-primary">
                                        <i class="fas fa-file-invoice me-1"></i>
                                        Voir les bons de commande
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Onglet : Liste des réceptions --}}
                        <div class="tab-pane fade" id="pane-liste-receptions" role="tabpanel" aria-labelledby="tab-liste-receptions" tabindex="0">
                            @if($receptions->count() > 0)
                                <div class="receptions-table-wrap">
                                    <table class="table receptions-table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th>Bon de commande</th>
                                                <th>Unité(s)</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                                <th class="text-end">Qté</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($receptions as $reception)
                                                <tr>
                                                    <td><span class="receptions-ref">{{ $reception->numero_reception }}</span></td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $reception->bonCommande->reference ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $reception->bonCommande->fournisseur->nom ?? '' }}</small>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $labelsUnite = $reception->lignes
                                                                ->map(fn ($lr) => optional($lr->article?->uniteMesure)->ref ?? optional($lr->article?->uniteMesure)->nom)
                                                                ->filter()
                                                                ->unique()
                                                                ->values();
                                                        @endphp
                                                        @if($labelsUnite->isNotEmpty())
                                                            <span class="small">{{ $labelsUnite->join(', ') }}</span>
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($reception->date_reception)->format('d/m/Y') }}</td>
                                                    <td>
                                                        @php
                                                            $st = $reception->statut;
                                                            $badgeClass = match ($st) {
                                                                'complete' => 'receptions-badge receptions-badge--done',
                                                                'partielle' => 'receptions-badge receptions-badge--partial-bc',
                                                                'en_cours' => 'receptions-badge receptions-badge--pending',
                                                                'annulee' => 'receptions-badge receptions-badge--cancel',
                                                                default => 'receptions-badge receptions-badge--neutral',
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ $reception->statut_formate }}</span>
                                                    </td>
                                                    <td class="text-end font-monospace">{{ number_format((float) $reception->quantite_totale_recue, 2, ',', ' ') }}</td>
                                                    <td class="text-end">
                                                        <a class="btn btn-sm receptions-btn-icon" href="{{ route('receptions.show', $reception->id) }}" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a class="btn btn-sm btn-outline-danger" href="{{ route('receptions.bon-livraison.pdf', $reception->id) }}" target="_blank" title="Bon de livraison fournisseur PDF">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end pt-3">
                                    {{ $receptions->links() }}
                                </div>
                            @else
                                <div class="alert receptions-alert-info mb-0 border-0 shadow-sm d-flex align-items-start gap-2">
                                    <i class="fas fa-info-circle mt-1"></i>
                                    <div>Aucune réception enregistrée pour le moment.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.receptions-page {
    --rcv-primary: var(--primary, #033d71);
    --rcv-primary-light: var(--primary-light, #033d71);
    --rcv-primary-dark: var(--primary-dark, #033d71);
}

.receptions-card {
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: var(--border-radius-md, 0.5rem);
    overflow: visible;
}

.receptions-card-header {
    background: linear-gradient(135deg, var(--rcv-primary) 0%, var(--rcv-primary-dark) 100%);
    border-bottom: none;
    padding: 1rem 1.25rem;
}

.receptions-card-header .card-title {
    font-size: 1.15rem;
    font-weight: 600;
}

.receptions-card-body {
    padding-top: 1.25rem;
    background: var(--gray-100, #f8f9fa);
}

.receptions-nav-tabs {
    border-bottom: none;
    gap: 0.25rem;
    flex-wrap: nowrap;
    overflow-x: auto;
}

.receptions-nav-tabs .nav-link {
    color: var(--rcv-primary);
    border: 1px solid transparent;
    border-radius: var(--border-radius-md, 0.5rem) var(--border-radius-md, 0.5rem) 0 0;
    padding: 0.65rem 1.1rem;
    font-weight: 600;
    white-space: nowrap;
    transition: var(--transition-base, all 0.2s ease);
}

.receptions-nav-tabs .nav-link:hover {
    background: rgba(10, 140, 255, 0.08);
    border-color: rgba(3, 61, 113, 0.15);
}

.receptions-nav-tabs .nav-link.active {
    color: var(--rcv-primary-dark);
    background: #fff;
    border-color: rgba(3, 61, 113, 0.2);
    border-bottom-color: #fff;
    box-shadow: 0 -2px 0 var(--rcv-primary-light) inset;
}

.receptions-tab-badge {
    background: var(--rcv-primary-light) !important;
    color: #fff !important;
    font-size: 0.7rem;
    padding: 0.25em 0.55em;
}

.receptions-tab-badge--muted {
    background: rgba(3, 61, 113, 0.35) !important;
    color: #fff !important;
}

.receptions-tab-content {
    border-color: rgba(3, 61, 113, 0.12) !important;
}

/* overflow-y visible : ne pas couper les dropdowns ; scroll horizontal si besoin */
.receptions-table-wrap {
    border-radius: var(--border-radius-sm, 0.375rem);
    box-shadow: var(--shadow-sm, 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075));
    border: 1px solid rgba(3, 61, 113, 0.1);
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}

.receptions-actions-dropdown.show {
    position: relative;
    z-index: 1060;
}

.receptions-dropdown-menu {
    z-index: 1061 !important;
}

.receptions-table {
    margin-bottom: 0;
    font-size: 0.925rem;
}

.receptions-table thead th {
    background: linear-gradient(135deg, var(--rcv-primary) 0%, var(--rcv-primary-dark) 100%);
    color: #fff;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0.01em;
    padding: 0.85rem 0.75rem;
    border: none;
    vertical-align: middle;
}

.receptions-table tbody tr {
    border-color: rgba(3, 61, 113, 0.08);
}

.receptions-table tbody tr:hover {
    background: rgba(10, 140, 255, 0.06);
}

.receptions-table tbody td {
    padding: 0.75rem;
    vertical-align: middle;
}

.receptions-ref {
    font-weight: 700;
    color: var(--rcv-primary);
}

.receptions-badge {
    font-weight: 600;
    padding: 0.35em 0.65em;
    border-radius: var(--border-radius-sm, 0.25rem);
}

.receptions-badge--pending {
    background: #6c757d;
    color: #fff;
}

.receptions-badge--partial {
    background: #e67e22;
    color: #fff;
}

.receptions-badge--partial-bc {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: #fff;
}

.receptions-badge--done {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    color: #fff;
}

.receptions-badge--delay {
    background: #ffc107;
    color: #212529;
}

.receptions-badge--cancel {
    background: #dc3545;
    color: #fff;
}

.receptions-badge--neutral {
    background: var(--secondary, #6c757d);
    color: #fff;
}

.receptions-progress {
    height: 1.35rem;
    background: var(--gray-200, #e9ecef);
    border-radius: 50rem;
    overflow: hidden;
}

.receptions-progress-bar {
    background: linear-gradient(90deg, var(--rcv-primary) 0%, var(--rcv-primary-light) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.receptions-progress-bar--mid {
    background: linear-gradient(90deg, #f39c12 0%, #ffc107 100%);
}

.receptions-progress-bar--full {
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
}

.receptions-progress-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
}

.receptions-progress-caption {
    display: block;
    margin-top: 0.35rem;
    font-size: 0.75rem;
}

.receptions-btn-actions {
    color: var(--rcv-primary);
    border: 1px solid rgba(3, 61, 113, 0.35);
    background: #fff;
    font-weight: 600;
}

.receptions-btn-actions:hover {
    background: rgba(10, 140, 255, 0.12);
    border-color: var(--rcv-primary);
    color: var(--rcv-primary-dark);
}

.receptions-btn-primary {
    background: linear-gradient(135deg, var(--rcv-primary) 0%, var(--rcv-primary-dark) 100%);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    border-radius: var(--border-radius-md, 0.5rem);
}

.receptions-btn-primary:hover {
    color: #fff;
    filter: brightness(1.08);
}

.receptions-btn-icon {
    color: var(--rcv-primary);
    border: 1px solid rgba(3, 61, 113, 0.35);
    background: #fff;
    width: 2.1rem;
    height: 2.1rem;
    padding: 0;
    line-height: 1;
    border-radius: var(--border-radius-sm, 0.25rem);
}

.receptions-btn-icon:hover {
    background: rgba(10, 140, 255, 0.12);
    color: var(--rcv-primary-dark);
    border-color: var(--rcv-primary);
}

.receptions-empty-icon {
    width: 4.5rem;
    height: 4.5rem;
    margin-inline: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(3, 61, 113, 0.12) 0%, rgba(10, 140, 255, 0.2) 100%);
    color: var(--rcv-primary);
    font-size: 2rem;
}

.receptions-empty-title {
    color: var(--rcv-primary-dark);
    font-weight: 700;
}

.receptions-alert-info {
    background: rgba(10, 140, 255, 0.12);
    color: var(--rcv-primary-dark);
    border-left: 4px solid var(--rcv-primary-light) !important;
}
</style>
@endpush
@endsection
