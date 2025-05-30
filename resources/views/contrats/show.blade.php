{{-- Page Show - Détail du contrat --}}
@extends('layouts.app')

@section('title', 'Détail du contrat')
@section('page-title', 'Détail du contrat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Détail</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-fade-in">
    <div class="row">
        <div class="col-md-3">
            <div class="app-card" style="background-color: var(--primary); color: var(--white); height: 200px">
                <div class="app-card-body">
                    <h3 class="app-fw-bold">{{ $contrat->nom_contrat }}</h3>
                    <div class="app-mt-3">
                        <p><i class="fas fa-calendar-alt me-2"></i> Début: {{ $contrat->date_debut }}</p>
                        <p><i class="fas fa-calendar-check me-2"></i> Fin: {{ $contrat->date_fin ?: 'Non défini' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="app-mt-4">
                <div class="app-card" style="background-color: var(--primary); color: var(--white); height: 100px">
                    <div class="app-card-body">
                        <h4 class="app-fw-bold">{{ $contrat->statut }}</h4>
                        <p><i class="fas fa-user me-2"></i> {{ $contrat->client->nom_raison_sociale ?? 'Client' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="row app-mb-4">
                @foreach ($stats as $title => $amount)
                    <div class="col-md-3 app-mt-3">
                        <div class="app-card" style="background-color: {{ 
                            $title === 'Écart' ? 
                                ($amount >= 0 ? '#28a745' : '#dc3545') : 
                                '#5EB3F6' 
                        }}; border: none; padding: 10px;">
                            <div class="app-card-body app-p-2">
                                <p class="app-fw-bold app-mb-1" style="color: var(--primary-dark);">{{ $title }}</p>
                                <h3 class="app-fw-bold app-mb-0" style="{{ 
                                    $title === 'Écart' && $amount < 0 ? 'color: white;' : '' 
                                }}">{{ number_format($amount, 0, ',', ' ') }} CFA</h3>
                                
                                @if($title === 'Coût de revient Réel' && $stats['Coût de revient Prév.'] > 0)
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-{{ 
                                            $amount <= $stats['Coût de revient Prév.'] ? 'success' : 'danger' 
                                        }}" role="progressbar" 
                                             style="width: {{ min(($amount / $stats['Coût de revient Prév.']) * 100, 100) }}%;" 
                                             aria-valuenow="{{ ($amount / $stats['Coût de revient Prév.']) * 100 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                @endif
                                
                                @if($title === 'CA Réalisé' && $stats['Montant du contrat'] > 0)
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: {{ min(($amount / $stats['Montant du contrat']) * 100, 100) }}%;" 
                                             aria-valuenow="{{ ($amount / $stats['Montant du contrat']) * 100 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100"></div>
                                    </div>
                                    <small style="color: var(--primary-dark);">
                                        {{ number_format(($amount / $stats['Montant du contrat']) * 100, 1) }}%
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="app-card app-mt-4">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-cogs me-2"></i>Informations du contrat
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('contrats.update', $contrat->id) }}" method="POST" class="app-form">
                        @csrf
                        @method('PUT')

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="ref_contrat" class="app-form-label">
                                        <i class="fas fa-hashtag me-2"></i>Référence du contrat
                                    </label>
                                    <input type="text" class="app-form-control" id="ref_contrat" name="ref_contrat" value="{{ $contrat->ref_contrat }}" required>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="nom_contrat" class="app-form-label">
                                        <i class="fas fa-file-signature me-2"></i>Nom du contrat
                                    </label>
                                    <input type="text" class="app-form-control" id="nom_contrat" name="nom_contrat" value="{{ $contrat->nom_contrat }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_debut" class="app-form-label">
                                        <i class="fas fa-calendar-alt me-2"></i>Date de début
                                    </label>
                                    <input type="date" class="app-form-control" id="date_debut" name="date_debut" value="{{ $contrat->date_debut }}" required>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_fin" class="app-form-label">
                                        <i class="fas fa-calendar-check me-2"></i>Date de fin
                                    </label>
                                    <input type="date" class="app-form-control" id="date_fin" name="date_fin" value="{{ $contrat->date_fin }}">
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="type_travaux" class="app-form-label">
                                        <i class="fas fa-hard-hat me-2"></i>Type de travaux
                                    </label>
                                    <input type="text" class="app-form-control" id="type_travaux" name="type_travaux" value="{{ $contrat->type_travaux }}" required>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="taux_garantie" class="app-form-label">
                                        <i class="fas fa-shield-alt me-2"></i>Taux de garantie
                                    </label>
                                    <input type="number" step="0.01" class="app-form-control" id="taux_garantie" name="taux_garantie" value="{{ $contrat->taux_garantie }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="client_id" class="app-form-label">
                                        <i class="fas fa-user me-2"></i>Client
                                    </label>
                                    <select class="app-form-select" id="client_id" name="client_id" required>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" @if($contrat->client_id == $client->id) selected @endif>{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="montant" class="app-form-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>Montant
                                    </label>
                                    <input type="number" step="0.01" class="app-form-control" id="montant" name="montant" value="{{ $contrat->montant }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-group">
                            <label for="statut" class="app-form-label">
                                <i class="fas fa-info-circle me-2"></i>Statut
                            </label>
                            <select class="app-form-select" id="statut" name="statut" required>
                                <option value="en cours" @if($contrat->statut == 'en cours') selected @endif>En cours</option>
                                <option value="terminé" @if($contrat->statut == 'terminé') selected @endif>Terminé</option>
                                <option value="annulé" @if($contrat->statut == 'annulé') selected @endif>Annulé</option>
                            </select>
                        </div>

                        <div class="app-card-footer">
                            <a href="{{ route('contrats.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="app-card app-mt-4">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-chart-line me-2"></i>Analyse financière du contrat
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> Les statistiques sont basées sur les prestations et factures liées au contrat.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="app-fw-bold">Prévisions vs Réalisations</h4>
                            <canvas id="comparisonChart" width="400" height="250"></canvas>
                            
                            <div class="app-mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Indicateur</th>
                                            <th>Prévisionnel</th>
                                            <th>Réalisé</th>
                                            <th>Écart</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong>Déboursé Sec</strong>
                                                <small class="d-block text-muted">Basé sur le DQE validé / prestations réalisées</small>
                                            </td>
                                            <td>{{ number_format($stats['DS Prévisionnel'], 0, ',', ' ') }} CFA</td>
                                            <td>{{ number_format($stats['DS Réalisé'], 0, ',', ' ') }} CFA</td>
                                            <td class="{{ $stats['DS Prévisionnel'] - $stats['DS Réalisé'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($stats['DS Prévisionnel'] - $stats['DS Réalisé'], 0, ',', ' ') }} CFA
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong>Coût de revient</strong>
                                                <small class="d-block text-muted">Basé sur le DQE validé / prestations payées</small>
                                            </td>
                                            <td>{{ number_format($stats['Coût de revient Prév.'], 0, ',', ' ') }} CFA</td>
                                            <td>{{ number_format($stats['Coût de revient Réel'], 0, ',', ' ') }} CFA</td>
                                            <td class="{{ $stats['Coût de revient Prév.'] - $stats['Coût de revient Réel'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($stats['Coût de revient Prév.'] - $stats['Coût de revient Réel'], 0, ',', ' ') }} CFA
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4 class="app-fw-bold">Rentabilité du contrat</h4>
                            <canvas id="profitabilityChart" width="400" height="250"></canvas>
                            
                            <div class="app-mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Indicateur</th>
                                            <th>Valeur</th>
                                            <th>% du montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Montant du contrat</strong></td>
                                            <td>{{ number_format($stats['Montant du contrat'], 0, ',', ' ') }} CFA</td>
                                            <td>100%</td>
                                        </tr>
                                        <tr>
                                            <td><strong>CA Réalisé</strong></td>
                                            <td>{{ number_format($stats['CA Réalisé'], 0, ',', ' ') }} CFA</td>
                                            <td>
                                                {{ $stats['Montant du contrat'] > 0 ? 
                                                    number_format(($stats['CA Réalisé'] / $stats['Montant du contrat']) * 100, 1) : 0 
                                                }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Marge brute estimée</strong></td>
                                            <td>{{ number_format($stats['Montant du contrat'] - $stats['Coût de revient Prév.'], 0, ',', ' ') }} CFA</td>
                                            <td>
                                                {{ $stats['Montant du contrat'] > 0 ? 
                                                    number_format((($stats['Montant du contrat'] - $stats['Coût de revient Prév.']) / $stats['Montant du contrat']) * 100, 1) : 0 
                                                }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Marge brute réalisée</strong></td>
                                            <td>{{ number_format($stats['CA Réalisé'] - $stats['Coût de revient Réel'], 0, ',', ' ') }} CFA</td>
                                            <td>
                                                {{ $stats['CA Réalisé'] > 0 ? 
                                                    number_format((($stats['CA Réalisé'] - $stats['Coût de revient Réel']) / $stats['CA Réalisé']) * 100, 1) : 0 
                                                }}%
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row app-mt-4">
                        <div class="col-md-12">
                            <h4 class="app-fw-bold">Historique des factures</h4>
                            
                            @php
                                $factures = \App\Models\Facture::where('id_contrat', $contrat->id)
                                    ->orderBy('date_emission', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            
                            @if($factures->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>N° Facture</th>
                                                <th>Date d'émission</th>
                                                <th>Montant</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($factures as $facture)
                                            <tr>
                                                <td>{{ $facture->num }}</td>
                                                <td>{{ \Carbon\Carbon::parse($facture->date_emission)->format('d/m/Y') }}</td>
                                                <td>{{ number_format($facture->montant_total, 0, ',', ' ') }} CFA</td>
                                                <td>
                                                    @if($facture->statut == 'en attente')
                                                        <span class="app-badge app-badge-warning app-badge-pill">En attente</span>
                                                    @elseif($facture->statut == 'payée')
                                                        <span class="app-badge app-badge-success app-badge-pill">Payée</span>
                                                    @elseif($facture->statut == 'annulée')
                                                        <span class="app-badge app-badge-danger app-badge-pill">Annulée</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('factures.show', $facture->id) }}" class="app-btn app-btn-primary app-btn-sm">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="app-text-end app-mt-2">
                                    <a href="{{ route('factures.index') }}" class="app-btn app-btn-outline-primary app-btn-sm">
                                        Voir toutes les factures <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Aucune facture n'a encore été émise pour ce contrat.
                                </div>
                            @endif
                        </div>
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
        // Graphique de comparaison prévisionnel vs réel
        const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
        const comparisonChart = new Chart(comparisonCtx, {
            type: 'bar',
            data: {
                labels: ['Déboursé Sec', 'Coût de revient'],
                datasets: [
                    {
                        label: 'Prévisionnel',
                        data: [{{ $stats['DS Prévisionnel'] }}, {{ $stats['Coût de revient Prév.'] }}],
                        backgroundColor: 'rgba(3, 55, 101, 0.7)',
                        borderColor: 'rgba(3, 55, 101, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Réalisé',
                        data: [{{ $stats['DS Réalisé'] }}, {{ $stats['Coût de revient Réel'] }}],
                        backgroundColor: 'rgba(10, 140, 255, 0.7)',
                        borderColor: 'rgba(10, 140, 255, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('fr-FR', { 
                                    style: 'currency', 
                                    currency: 'XOF',
                                    minimumFractionDigits: 0 
                                }).format(context.raw);
                                return label;
                            }
                        }
                    }
                },
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
                }
            }
        });
        
        // Graphique de rentabilité
        const profitabilityCtx = document.getElementById('profitabilityChart').getContext('2d');
        const profitabilityChart = new Chart(profitabilityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Coût de revient réel', 'Marge brute réalisée'],
                datasets: [{
                    data: [
                        {{ $stats['Coût de revient Réel'] }}, 
                        {{ max(0, $stats['CA Réalisé'] - $stats['Coût de revient Réel']) }}
                    ],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(40, 167, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(220, 53, 69, 1)',
                        'rgba(40, 167, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('fr-FR', { 
                                    style: 'currency', 
                                    currency: 'XOF',
                                    minimumFractionDigits: 0 
                                }).format(context.raw);
                                
                                // Ajouter le pourcentage
                                if (context.label === 'Marge brute réalisée' && {{ $stats['CA Réalisé'] }} > 0) {
                                    const percentage = {{ 
                                        $stats['CA Réalisé'] > 0 ? 
                                        ($stats['CA Réalisé'] - $stats['Coût de revient Réel']) / $stats['CA Réalisé'] * 100 : 0 
                                    }};
                                    label += ` (${percentage.toFixed(1)}%)`;
                                }
                                
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