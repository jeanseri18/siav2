@extends('layouts.app')

@section('content')
@php
    $totalQuantite = $bonCommande->lignes->sum('quantite');
    $totalRecue = $bonCommande->lignes->sum('quantite_recue');
    $pourcentage = $totalQuantite > 0 ? round(($totalRecue / $totalQuantite) * 100, 1) : 0;
@endphp
<div class="container-fluid reception-detail-page">
    <x-stock-flux-nav module="reception" context="show" />
    <div class="card reception-detail-main shadow-sm border-0">
        <div class="reception-detail-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h3 class="reception-detail-title mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-eye"></i>
                Bon de commande <span class="reception-detail-ref">{{ $bonCommande->reference }}</span>
            </h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('receptions.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
                @if($pourcentage < 100)
                    <a href="{{ route('receptions.create', $bonCommande->id) }}" class="btn reception-btn-success btn-sm">
                        <i class="fas fa-truck-loading me-1"></i> Effectuer réception
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body reception-detail-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="reception-info-card h-100">
                        <div class="reception-info-card-head">
                            <i class="fas fa-file-invoice"></i> Informations commande
                        </div>
                        <div class="reception-info-card-body">
                            <dl class="reception-dl mb-0">
                                <dt>Référence</dt>
                                <dd><strong class="text-primary-emphasis">{{ $bonCommande->reference }}</strong></dd>
                                <dt>Date de commande</dt>
                                <dd>{{ \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') }}</dd>
                                @if($bonCommande->date_livraison_prevue)
                                    <dt>Livraison prévue</dt>
                                    <dd>
                                        {{ \Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->format('d/m/Y') }}
                                        @if(\Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->isPast() && $pourcentage < 100)
                                            <span class="badge bg-warning text-dark ms-1">En retard</span>
                                        @endif
                                    </dd>
                                @endif
                                <dt>Statut réception</dt>
                                <dd>
                                    @if($bonCommande->statut == 'reçu')
                                        <span class="badge bg-success">Reçu</span>
                                    @elseif($pourcentage > 0)
                                        <span class="badge bg-warning text-dark">Partiellement reçu</span>
                                    @else
                                        <span class="badge bg-secondary">En attente</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="reception-info-card h-100">
                        <div class="reception-info-card-head reception-info-card-head--muted">
                            <i class="fas fa-building"></i> Fournisseur
                        </div>
                        <div class="reception-info-card-body">
                            <dl class="reception-dl mb-0">
                                <dt>Nom</dt>
                                <dd>{{ $bonCommande->fournisseur->nom }}</dd>
                                @if($bonCommande->fournisseur->telephone)
                                    <dt>Téléphone</dt>
                                    <dd>{{ $bonCommande->fournisseur->telephone }}</dd>
                                @endif
                                @if($bonCommande->fournisseur->email)
                                    <dt>Email</dt>
                                    <dd><a href="mailto:{{ $bonCommande->fournisseur->email }}">{{ $bonCommande->fournisseur->email }}</a></dd>
                                @endif
                                @if($bonCommande->fournisseur->adresse)
                                    <dt>Adresse</dt>
                                    <dd>{{ $bonCommande->fournisseur->adresse }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="reception-progress-card h-100">
                        <div class="reception-progress-card-head">
                            <i class="fas fa-chart-pie"></i> Progression globale
                        </div>
                        <div class="reception-progress-card-body">
                            <div class="reception-progress-track progress mb-3">
                                <div class="progress-bar reception-progress-bar" role="progressbar"
                                     style="width: {{ $pourcentage }}%"
                                     aria-valuenow="{{ $pourcentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $pourcentage }}%
                                </div>
                            </div>
                            <p class="mb-1 small"><strong>Articles reçus</strong> : {{ $totalRecue }} / {{ $totalQuantite }}</p>
                            <p class="mb-0 small"><strong>Montant total BC</strong> : {{ number_format($bonCommande->montant_total, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reception-table-section">
                <div class="reception-table-section-header">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Détail des articles</h5>
                </div>
                <div class="table-responsive">
                    <table class="table reception-detail-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Référence</th>
                                <th class="text-end">Cmd</th>
                                <th class="text-end">Reçu</th>
                                <th class="text-end">Reste</th>
                                <th class="text-end">Prix unit.</th>
                                <th class="text-end">Montant</th>
                                <th>Statut</th>
                                <th style="min-width: 110px;">Progression</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonCommande->lignes as $ligne)
                                @php
                                    $quantiteRestante = $ligne->quantite - $ligne->quantite_recue;
                                    $pourcentageLigne = $ligne->quantite > 0 ? round(($ligne->quantite_recue / $ligne->quantite) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong class="reception-article-name">{{ $ligne->article->nom }}</strong>
                                        @if($ligne->article->description)
                                            <br><small class="text-muted">{{ Str::limit($ligne->article->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><code class="reception-code">{{ $ligne->article->reference_fournisseur ?? $ligne->article->reference }}</code></td>
                                    <td class="text-end"><span class="badge reception-badge-qty bg-primary-subtle text-primary border border-primary-subtle">{{ $ligne->quantite }}</span></td>
                                    <td class="text-end"><span class="badge bg-success-subtle text-success border border-success-subtle">{{ $ligne->quantite_recue }}</span></td>
                                    <td class="text-end">
                                        @if($quantiteRestante > 0)
                                            <span class="badge bg-warning-subtle text-dark border border-warning">{{ $quantiteRestante }}</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-end fw-semibold">{{ number_format($ligne->quantite * $ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        @if($pourcentageLigne == 100)
                                            <span class="badge bg-success">Complet</span>
                                        @elseif($pourcentageLigne > 0)
                                            <span class="badge bg-warning text-dark">Partiel</span>
                                        @else
                                            <span class="badge bg-secondary">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress reception-line-progress">
                                            <div class="progress-bar {{ $pourcentageLigne == 100 ? 'bg-success' : ($pourcentageLigne > 0 ? 'bg-warning' : 'bg-secondary') }}"
                                                 role="progressbar"
                                                 style="width: {{ $pourcentageLigne }}%"
                                                 aria-valuenow="{{ $pourcentageLigne }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $pourcentageLigne }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($bonCommande->montant_total, 0, ',', ' ') }} FCFA</th>
                                <th colspan="2">
                                    @if($pourcentage == 100)
                                        <span class="badge bg-success">Réception complète</span>
                                    @elseif($pourcentage > 0)
                                        <span class="badge bg-warning text-dark">Partielle ({{ $pourcentage }}%)</span>
                                    @else
                                        <span class="badge bg-secondary">En attente</span>
                                    @endif
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($bonCommande->notes || $bonCommande->conditions_paiement)
                <div class="row g-3 mt-4">
                    @if($bonCommande->notes)
                        <div class="col-md-6">
                            <div class="reception-notes-card">
                                <div class="reception-notes-header"><i class="fas fa-sticky-note me-2"></i>Notes</div>
                                <div class="reception-notes-body">{{ $bonCommande->notes }}</div>
                            </div>
                        </div>
                    @endif
                    @if($bonCommande->conditions_paiement)
                        <div class="col-md-6">
                            <div class="reception-notes-card">
                                <div class="reception-notes-header"><i class="fas fa-credit-card me-2"></i>Conditions de paiement</div>
                                <div class="reception-notes-body">{{ $bonCommande->conditions_paiement }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.reception-detail-page {
    --rcv-primary: #033d71;
    --rcv-primary-light: #033d71;
    --rcv-primary-dark: #033d71;
}

.reception-detail-main {
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: 0.5rem;
    overflow: hidden;
}

.reception-detail-header {
    background: linear-gradient(135deg, var(--rcv-primary) 0%, var(--rcv-primary-dark) 100%);
    color: #fff;
    padding: 1rem 1.25rem;
}

.reception-detail-title {
    font-size: 1.15rem;
    font-weight: 600;
    color: #fff;
}

.reception-detail-ref {
    color: rgba(255, 255, 255, 0.95);
    font-weight: 700;
}

.reception-detail-body {
    background: #f8f9fa;
    padding: 1.25rem 1.25rem 1.5rem;
}

.reception-btn-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    color: #fff;
    font-weight: 600;
    border: none;
}
.reception-btn-success:hover {
    color: #fff;
    filter: brightness(1.05);
}

.reception-info-card {
    background: #fff;
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.06);
}

.reception-info-card-head {
    background: linear-gradient(135deg, var(--rcv-primary) 0%, var(--rcv-primary-dark) 100%);
    color: #fff;
    padding: 0.65rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
}
.reception-info-card-head--muted {
    background: linear-gradient(135deg, #1f384c 0%, #2c5282 100%);
}

.reception-info-card-body {
    padding: 1rem;
}

.reception-dl dt {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #6c757d;
    margin-bottom: 0.15rem;
    margin-top: 0.65rem;
}
.reception-dl dt:first-child { margin-top: 0; }
.reception-dl dd {
    margin-bottom: 0;
    font-size: 0.95rem;
}

.reception-progress-card {
    background: #fff;
    border: 1px solid rgba(3, 61, 113, 0.18);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(3, 61, 113, 0.08);
}

.reception-progress-card-head {
    background: linear-gradient(135deg, var(--rcv-primary-light) 0%, var(--rcv-primary) 100%);
    color: #fff;
    padding: 0.65rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.reception-progress-card-body {
    padding: 1rem;
}

.reception-progress-track {
    height: 1.35rem;
    background: rgba(3, 61, 113, 0.1);
    border-radius: 50rem;
}

.reception-progress-bar {
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    font-size: 75%;
    font-weight: 700;
}

.reception-table-section {
    background: #fff;
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.06);
}

.reception-table-section-header {
    background: linear-gradient(135deg, var(--rcv-primary) 0%, var(--rcv-primary-dark) 100%);
    color: #fff;
    padding: 0.75rem 1rem;
    font-weight: 600;
}

.reception-detail-table thead th {
    background: #f1f5f9;
    color: var(--rcv-primary-dark);
    font-weight: 600;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    border: none;
    padding: 0.75rem 0.85rem;
}

.reception-detail-table tbody td {
    border-color: rgba(3, 61, 113, 0.08);
    vertical-align: middle;
}

.reception-detail-table tbody tr:hover {
    background: rgba(10, 140, 255, 0.06);
}

.reception-detail-table tfoot th {
    background: #f8f9fa;
    border-top: 2px solid rgba(3, 61, 113, 0.15);
    font-weight: 700;
}

.reception-line-progress {
    height: 1.15rem;
    background: #e9ecef;
    border-radius: 50rem;
}

.reception-article-name { color: var(--rcv-primary-dark); }
.reception-code {
    background: rgba(3, 61, 113, 0.07);
    color: var(--rcv-primary);
    padding: 0.2rem 0.45rem;
    border-radius: 0.25rem;
    font-size: 0.85rem;
}

.reception-badge-qty { font-weight: 600; }

.reception-notes-card {
    border: 1px solid rgba(3, 61, 113, 0.12);
    border-radius: 0.5rem;
    overflow: hidden;
    background: #fff;
}
.reception-notes-header {
    background: rgba(10, 140, 255, 0.12);
    color: var(--rcv-primary-dark);
    padding: 0.6rem 1rem;
    font-weight: 600;
    border-left: 4px solid var(--rcv-primary-light);
}
.reception-notes-body {
    padding: 1rem;
}
</style>
@endpush
@endsection
