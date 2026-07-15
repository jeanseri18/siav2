@extends('layouts.app')

@section('content')
<div class="container-fluid reception-detail-page">
    <x-stock-flux-nav module="reception" context="show" />
    <div class="card reception-detail-main shadow-sm border-0">
        <div class="reception-detail-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h3 class="reception-detail-title mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-receipt"></i>
                Détails de la réception <span class="reception-detail-ref">{{ $reception->numero_reception }}</span>
            </h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('receptions.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <a href="{{ route('receptions.bon-livraison.pdf', $reception->id) }}" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Bon de livraison fournisseur (PDF)
                </a>
                <a href="{{ route('receptions.show', $reception->bonCommande->id) }}" class="btn reception-btn-accent btn-sm">
                    <i class="fas fa-file-invoice me-1"></i> Voir le bon de commande
                </a>
            </div>
        </div>

        <div class="card-body reception-detail-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="reception-info-card h-100">
                        <div class="reception-info-card-head">
                            <i class="fas fa-info-circle"></i> Informations réception
                        </div>
                        <div class="reception-info-card-body">
                            <dl class="reception-dl mb-0">
                                <dt>Numéro</dt>
                                <dd>{{ $reception->numero_reception }}</dd>
                                <dt>Date de réception</dt>
                                <dd>{{ $reception->date_reception->format('d/m/Y H:i') }}</dd>
                                @if($reception->numero_bon_livraison)
                                    <dt>Bon de livraison fournisseur</dt>
                                    <dd>{{ $reception->numero_bon_livraison }}</dd>
                                @endif
                                @if($reception->transporteur)
                                    <dt>Transporteur</dt>
                                    <dd>{{ $reception->transporteur }}</dd>
                                @endif
                                <dt>Statut</dt>
                                <dd><span class="badge reception-badge-status {{ $reception->statut_badge_class }}">{{ $reception->statut_formate }}</span></dd>
                                <dt>Réceptionné par</dt>
                                <dd>{{ $reception->user->nom ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="reception-info-card h-100">
                        <div class="reception-info-card-head reception-info-card-head--muted">
                            <i class="fas fa-file-invoice"></i> Bon de commande
                        </div>
                        <div class="reception-info-card-body">
                            <dl class="reception-dl mb-0">
                                <dt>Référence</dt>
                                <dd><strong class="text-primary-emphasis">{{ $reception->bonCommande->reference }}</strong></dd>
                                <dt>Date commande</dt>
                                <dd>{{ \Carbon\Carbon::parse($reception->bonCommande->date_commande)->format('d/m/Y') }}</dd>
                                <dt>Fournisseur</dt>
                                <dd>{{ $reception->bonCommande->fournisseur->nom }}</dd>
                                <dt>Projet</dt>
                                <dd>
                                    @if($reception->bonCommande->projet)
                                        <span class="fw-semibold">{{ $reception->bonCommande->projet->nom_projet }}</span>
                                        @if($reception->bonCommande->projet->ref_projet)
                                            <span class="text-muted small">({{ $reception->bonCommande->projet->ref_projet }})</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="reception-totals-card h-100">
                        <div class="reception-totals-card-head">
                            <i class="fas fa-chart-bar"></i> Totaux réception
                        </div>
                        <div class="reception-totals-card-body">
                            <p class="mb-2"><span class="reception-totals-label">Quantité totale reçue</span><br>
                                <span class="reception-totals-value">{{ number_format($reception->quantite_totale_recue, 0, ',', ' ') }}</span></p>
                            <p class="mb-2"><span class="reception-totals-label">Montant total reçu</span><br>
                                <span class="reception-totals-value">{{ number_format($reception->montant_total_recu, 0, ',', ' ') }} <small>FCFA</small></span></p>
                            <p class="mb-0"><span class="reception-totals-label">Progression</span><br>
                                <span class="reception-totals-pct">{{ $reception->pourcentage_reception }}%</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reception-table-section">
                <div class="reception-table-section-header">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Articles reçus</h5>
                </div>
                <div class="table-responsive">
                    <table class="table reception-detail-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Référence</th>
                                <th>Unité</th>
                                <th class="text-end">Qté reçue</th>
                                <th class="text-end">Conforme</th>
                                <th class="text-end">Non conforme</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Montant</th>
                                <th>État</th>
                                <th>Lot / série</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reception->lignes as $ligne)
                                <tr>
                                    <td>
                                        <strong class="reception-article-name">{{ $ligne->article->nom }}</strong>
                                        @if($ligne->article->description)
                                            <br><small class="text-muted">{{ Str::limit($ligne->article->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><code class="reception-code">{{ $ligne->article->reference_fournisseur ?? $ligne->article->reference }}</code></td>
                                    <td>
                                        @php
                                            $um = $ligne->article->uniteMesure ?? null;
                                        @endphp
                                        @if($um)
                                            <span class="text-nowrap">{{ $um->ref ?? $um->nom ?? '—' }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end"><span class="badge reception-badge-qty bg-primary-subtle text-primary border border-primary-subtle">{{ number_format($ligne->quantite_recue, 0, ',', ' ') }}</span></td>
                                    <td class="text-end"><span class="badge bg-success-subtle text-success border border-success-subtle">{{ number_format($ligne->quantite_conforme, 0, ',', ' ') }}</span></td>
                                    <td class="text-end">
                                        @if($ligne->quantite_non_conforme > 0)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ number_format($ligne->quantite_non_conforme, 0, ',', ' ') }}</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format($ligne->prix_unitaire_recu, 0, ',', ' ') }} FCFA</td>
                                    <td class="text-end fw-semibold">{{ number_format($ligne->montant_total, 0, ',', ' ') }} FCFA</td>
                                    <td><span class="badge {{ $ligne->etat_badge_class }}">{{ $ligne->etat_article_formate }}</span></td>
                                    <td>
                                        @if($ligne->numero_lot)
                                            <small>{{ $ligne->numero_lot }}</small>
                                            @if($ligne->date_peremption)
                                                <br><small class="text-muted">Exp. {{ $ligne->date_peremption->format('d/m/Y') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($reception->montant_total_recu, 0, ',', ' ') }} FCFA</th>
                                <th colspan="2"><span class="badge reception-badge-status {{ $reception->statut_badge_class }}">{{ $reception->statut_formate }}</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($reception->observations)
                <div class="reception-notes-card mt-4">
                    <div class="reception-notes-header"><i class="fas fa-sticky-note me-2"></i>Observations</div>
                    <div class="reception-notes-body">{{ $reception->observations }}</div>
                </div>
            @endif

            @php
                $autresReceptions = $reception->bonCommande->receptions()->where('id', '!=', $reception->id)->orderBy('date_reception', 'desc')->get();
            @endphp
            @if($autresReceptions->count() > 0)
                <div class="reception-table-section mt-4">
                    <div class="reception-table-section-header">
                        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Autres réceptions pour ce bon de commande</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table reception-detail-table reception-detail-table--compact mb-0">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Date</th>
                                    <th class="text-end">Quantité</th>
                                    <th class="text-end">Montant</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($autresReceptions as $autreReception)
                                    <tr>
                                        <td>{{ $autreReception->numero_reception }}</td>
                                        <td>{{ $autreReception->date_reception->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ number_format($autreReception->quantite_totale_recue, 0, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($autreReception->montant_total_recu, 0, ',', ' ') }} FCFA</td>
                                        <td><span class="badge {{ $autreReception->statut_badge_class }}">{{ $autreReception->statut_formate }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('receptions.show', $autreReception->id) }}" class="btn btn-sm reception-btn-outline" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

.reception-btn-accent {
    background: rgba(255, 255, 255, 0.95);
    color: var(--rcv-primary);
    font-weight: 600;
    border: none;
}
.reception-btn-accent:hover {
    background: #fff;
    color: var(--rcv-primary-dark);
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

.reception-totals-card {
    background: linear-gradient(145deg, rgba(10, 140, 255, 0.12) 0%, #fff 45%);
    border: 1px solid rgba(3, 61, 113, 0.18);
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(3, 61, 113, 0.08);
}

.reception-totals-card-head {
    background: linear-gradient(135deg, var(--rcv-primary-light) 0%, var(--rcv-primary) 100%);
    color: #fff;
    padding: 0.65rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.reception-totals-card-body {
    padding: 1rem;
}
.reception-totals-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #495057;
    letter-spacing: 0.02em;
}
.reception-totals-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--rcv-primary-dark);
}
.reception-totals-pct {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--rcv-primary);
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

.reception-detail-table--compact thead th,
.reception-detail-table--compact tbody td {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
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
.reception-badge-status.bg-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important; }
.reception-badge-status.bg-success { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important; }
.reception-badge-status.bg-warning { color: #212529 !important; }

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

.reception-btn-outline {
    color: var(--rcv-primary);
    border: 1px solid rgba(3, 61, 113, 0.35);
    background: #fff;
}
.reception-btn-outline:hover {
    background: rgba(10, 140, 255, 0.1);
    border-color: var(--rcv-primary);
    color: var(--rcv-primary-dark);
}
</style>
@endpush
@endsection
