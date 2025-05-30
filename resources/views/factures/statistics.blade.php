{{-- Page Statistics - Statistiques des factures --}}
@extends('layouts.app')

@section('title', 'Statistiques des factures')
@section('page-title', 'Statistiques des factures')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('factures.index') }}">Factures</a></li>
<li class="breadcrumb-item active">Statistiques</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="container app-fade-in">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="app-d-flex app-justify-content-between app-align-items-center">
                <h2><i class="fas fa-chart-bar me-2"></i>Statistiques des factures</h2>
                <a href="{{ route('factures.index') }}" class="app-btn app-btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Statistiques par statut -->
        <div class="col-md-4">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-tags me-2"></i>Par statut
                    </h3>
                </div>
                <div class="app-card-body">
                    <canvas id="chartByStatus" width="400" height="300"></canvas>
                    <div class="mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Statut</th>
                                    <th>Montant</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAmount = $statsByStatus->sum('total');
                                @endphp
                                @foreach($statsByStatus as $stat)
                                <tr>
                                    <td>
                                        @if($stat->statut == 'en attente')
                                            <span class="app-badge app-badge-warning">En attente</span>
                                        @elseif($stat->statut == 'payée')
                                            <span class="app-badge app-badge-success">Payée</span>
                                        @elseif($stat->statut == 'annulée')
                                            <span class="app-badge app-badge-danger">Annulée</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($stat->total, 2, ',', ' ') }} CFA</td>
                                    <td>{{ $totalAmount > 0 ? number_format(($stat->total / $totalAmount) * 100, 2) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th>{{ number_format($totalAmount, 2, ',', ' ') }} CFA</th>
                                    <th>100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques par mois -->
        <div class="col-md-8">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-calendar-alt me-2"></i>Évolution mensuelle ({{ date('Y') }})
                    </h3>
                </div>
                <div class="app-card-body">
                    <canvas id="chartByMonth" width="800" height="300"></canvas>
                    <div class="mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Montant</th>
                                    <th>% de l'année</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalYearAmount = $statsByMonth->sum('total');
                                    $monthNames = [
                                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 
                                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 
                                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                                    ];
                                    
                                    // Créer un tableau complet des mois, même ceux sans données
                                    $monthlyData = [];
                                    foreach($monthNames as $num => $name) {
                                        $monthlyData[$num] = 0;
                                    }
                                    
                                    // Remplir avec les données existantes
                                    foreach($statsByMonth as $stat) {
                                        $monthlyData[$stat->month] = $stat->total;
                                    }
                                @endphp
                                
                                @foreach($monthlyData as $month => $total)
                                <tr>
                                    <td>{{ $monthNames[$month] }}</td>
                                    <td>{{ number_format($total, 2, ',', ' ') }} CFA</td>
                                    <td>{{ $totalYearAmount > 0 ? number_format(($total / $totalYearAmount) * 100, 2) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <th>{{ number_format($totalYearAmount, 2, ',', ' ') }} CFA</th>
                                    <th>100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistiques par contrat -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-file-contract me-2"></i>Par contrat
                    </h3>
                </div>
                <div class="app-card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Contrat</th>
                                    <th>Référence</th>
                                    <th>Montant du contrat</th>
                                    <th>Total facturé</th>
                                    <th>% Facturé</th>
                                    <th>Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statsByContrat as $stat)
                                <tr>
                                    <td>{{ $stat->contrat->nom_contrat }}</td>
                                    <td>{{ $stat->contrat->ref_contrat }}</td>
                                    <td>{{ number_format($stat->contrat->montant, 2, ',', ' ') }} CFA</td>
                                    <td>{{ number_format($stat->total, 2, ',', ' ') }} CFA</td>
                                    <td>
                                        @php
                                            $percentage = $stat->contrat->montant > 0 ? ($stat->total / $stat->contrat->montant) * 100 : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar {{ $percentage > 100 ? 'bg-danger' : 'bg-success' }}" role="progressbar" 
                                                 style="width: {{ min($percentage, 100) }}%;" 
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($percentage, 2) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('contrats.show', $stat->contrat->id) }}" class="app-btn app-btn-primary app-btn-sm">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration des couleurs
        const colors = {
            'en attente': '#ffc107',
            'payée': '#28a745',
            'annulée': '#dc3545'
        };
        
        // Graphique par statut
        const ctxStatus = document.getElementById('chartByStatus').getContext('2d');
        const chartByStatus = new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($statsByStatus as $stat)
                        '{{ ucfirst($stat->statut) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($statsByStatus as $stat)
                            {{ $stat->total }},
                        @endforeach
                    ],
                    backgroundColor: [
                        @foreach($statsByStatus as $stat)
                            '{{ $colors[$stat->statut] ?? '#6c757d' }}',
                        @endforeach
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.raw;
                                label += new Intl.NumberFormat('fr-FR', { 
                                    style: 'currency', 
                                    currency: 'XOF',
                                    minimumFractionDigits: 0 
                                }).format(value);
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique par mois
        const ctxMonth = document.getElementById('chartByMonth').getContext('2d');
        const chartByMonth = new Chart(ctxMonth, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($monthNames as $name)
                        '{{ $name }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Montant facturé',
                    data: [
                        @foreach($monthlyData as $total)
                            {{ $total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(3, 55, 101, 0.7)',
                    borderColor: 'rgba(3, 55, 101, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR', { 
                                    style: 'currency', 
                                    currency: 'XOF',
                                    minimumFractionDigits: 0 
                                }).format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.raw;
                                label += new Intl.NumberFormat('fr-FR', { 
                                    style: 'currency', 
                                    currency: 'XOF',
                                    minimumFractionDigits: 0 
                                }).format(value);
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection