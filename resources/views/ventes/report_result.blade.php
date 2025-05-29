{{-- Page Report Results - Résultats du Rapport des Ventes --}}
@extends('layouts.app')

@section('title', 'Résultats du Rapport des Ventes')
@section('page-title', 'Résultats du Rapport des Ventes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('ventes.index') }}">Ventes</a></li>
<li class="breadcrumb-item"><a href="{{ route('ventes.report') }}">Rapport</a></li>
<li class="breadcrumb-item active">Résultats</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <!-- En-tête avec actions -->
    <div class="app-card mb-4">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-chart-bar me-2"></i>Résultats du Rapport des Ventes
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('ventes.report') }}" class="app-btn app-btn-secondary app-btn-sm">
                    <i class="fas fa-filter me-2"></i>Modifier les filtres
                </a>
                <button class="app-btn app-btn-success app-btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Imprimer
                </button>
                {{-- Bouton PDF si disponible
                <a href="{{ route('ventes.report.pdf', request()->all()) }}" class="app-btn app-btn-danger app-btn-sm">
                    <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                </a>
                --}}
            </div>
        </div>
    </div>

    @if($ventes->isEmpty())
        <!-- Aucun résultat -->
        <div class="app-card">
            <div class="app-card-body text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                <h4 class="text-muted mb-3">Aucune vente trouvée</h4>
                <p class="text-muted mb-4">
                    Aucune vente ne correspond aux critères sélectionnés. 
                    Essayez de modifier vos filtres de recherche.
                </p>
                <a href="{{ route('ventes.report') }}" class="app-btn app-btn-primary">
                    <i class="fas fa-filter me-2"></i>Modifier les filtres
                </a>
            </div>
        </div>
    @else
        <!-- Statistiques du rapport -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="app-card text-center">
                    <div class="app-card-body">
                        <i class="fas fa-shopping-cart fa-2x text-primary mb-3"></i>
                        <h3 class="app-card-title">{{ $ventes->count() }}</h3>
                        <p class="text-muted mb-0">Ventes trouvées</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="app-card text-center">
                    <div class="app-card-body">
                        <i class="fas fa-coins fa-2x text-success mb-3"></i>
                        <h3 class="app-card-title">{{ number_format($ventes->sum('total'), 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">FCFA (Total)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="app-card text-center">
                    <div class="app-card-body">
                        <i class="fas fa-calculator fa-2x text-info mb-3"></i>
                        <h3 class="app-card-title">{{ number_format($ventes->avg('total'), 0, ',', ' ') }}</h3>
                        <p class="text-muted mb-0">FCFA (Moyenne)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="app-card text-center">
                    <div class="app-card-body">
                        <i class="fas fa-boxes fa-2x text-warning mb-3"></i>
                        <h3 class="app-card-title">{{ $ventes->sum(function($vente) { return $vente->articles->sum('pivot.quantite'); }) }}</h3>
                        <p class="text-muted mb-0">Articles vendus</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des résultats -->
        <div class="app-card">
            <div class="app-card-header">
                <h3 class="app-card-title">
                    <i class="fas fa-table me-2"></i>Détail des Ventes
                </h3>
            </div>
            <div class="app-card-body app-table-responsive">
                <table id="reportTable" class="app-table display">
                    <thead>
                        <tr>
                            <th>ID Vente</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Articles</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventes as $vente)
                        <tr>
                            <td>
                                <a href="{{ route('ventes.show', $vente) }}" class="app-badge app-badge-primary">
                                    #{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td>
                                <div class="app-d-flex app-align-items-center app-gap-2">
                                    <div class="item-icon">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <span>{{ $vente->client->prenoms }}</span>
                                </div>
                            </td>
                            <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $statutClass = '';
                                    $statutIcon = '';
                                    switch($vente->statut) {
                                        case 'Payée':
                                            $statutClass = 'success';
                                            $statutIcon = 'check-circle';
                                            break;
                                        case 'En attente':
                                            $statutClass = 'warning';
                                            $statutIcon = 'clock';
                                            break;
                                        case 'Annulée':
                                            $statutClass = 'danger';
                                            $statutIcon = 'times-circle';
                                            break;
                                        default:
                                            $statutClass = 'secondary';
                                            $statutIcon = 'question-circle';
                                    }
                                @endphp
                                <span class="app-badge app-badge-{{ $statutClass }} app-badge-pill">
                                    <i class="fas fa-{{ $statutIcon }} me-1"></i> {{ $vente->statut }}
                                </span>
                            </td>
                            <td>
                                <div class="app-d-flex app-flex-wrap app-gap-1">
                                    @foreach($vente->articles as $article)
                                        <span class="app-badge app-badge-light app-badge-sm">
                                            {{ $article->nom }} ({{ $article->pivot->quantite }})
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-end app-fw-bold text-success">
                                {{ number_format($vente->total, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <td colspan="5" class="text-end app-fw-bold">
                                <i class="fas fa-calculator me-2"></i>Total général :
                            </td>
                            <td class="text-end app-fw-bold text-success h5">
                                {{ number_format($ventes->sum('total'), 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>

@push('styles')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.css" rel="stylesheet">
<style>
@media print {
    .app-card-actions,
    .breadcrumb,
    .app-btn {
        display: none !important;
    }
    
    .app-card {
        border: none !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    
    body {
        background: white !important;
    }
    
    .app-table {
        font-size: 12px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        // Configuration DataTable
        $('#reportTable').DataTable({
            responsive: true,
            dom: '<"dt-header"Bf>rt<"dt-footer"ip>',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-file-export"></i> Exporter',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                },
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i> Colonnes'
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            order: [[2, 'desc']], // Trier par date décroissante
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                
                // Calcul du total des ventes affichées
                var total = api
                    .column(5, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return parseFloat(a.replace(/[^\d]/g, '')) + parseFloat(b.replace(/[^\d]/g, ''));
                    }, 0);
                
                // Mise à jour du pied de page
                $(api.column(5).footer()).html(
                    total.toLocaleString() + ' FCFA'
                );
            }
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
    });
</script>
@endpush
@endsection