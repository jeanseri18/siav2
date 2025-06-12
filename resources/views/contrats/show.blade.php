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

<style>
.contract-detail-page {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 100vh;
    padding: 20px 0;
}

.contract-hero {
    background: linear-gradient(135deg, #5EB3F6 0%, #033765 100%);
    border-radius: 20px;
    color: white;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 15px 35px rgba(94, 179, 246, 0.3);
    position: relative;
    overflow: hidden;
}

.contract-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    transform: rotate(45deg);
}

.contract-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.contract-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.meta-item {
    background: rgba(255,255,255,0.15);
    padding: 15px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.meta-item i {
    font-size: 1.2rem;
    margin-right: 10px;
    opacity: 0.9;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #5EB3F6, #033765);
}

.stat-card.positive::before {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.stat-card.negative::before {
    background: linear-gradient(90deg, #dc3545, #fd7e14);
}

.stat-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
}

.stat-progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 10px;
}

.stat-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #5EB3F6, #033765);
    border-radius: 3px;
    transition: width 0.8s ease;
}

.main-content {
    display: grid;
    grid-template-columns: 1fr;
    gap: 30px;
}

.content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px 30px;
    border-bottom: 1px solid #dee2e6;
}

.card-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
}

.card-title i {
    margin-right: 12px;
    color: #5EB3F6;
}

.card-body {
    padding: 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.form-label i {
    margin-right: 8px;
    color: #5EB3F6;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-control:focus, .form-select:focus {
    border-color: #5EB3F6;
    box-shadow: 0 0 0 0.2rem rgba(94, 179, 246, 0.25);
    background: white;
}

.btn-group {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}

.btn {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(135deg, #5EB3F6 0%, #033765 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(94, 179, 246, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(94, 179, 246, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.analysis-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.chart-container {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    text-align: center;
}

.data-table {
    margin-top: 20px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.table {
    margin: 0;
}

.table thead th {
    background: linear-gradient(135deg, #5EB3F6 0%, #033765 100%);
    color: white;
    font-weight: 600;
    border: none;
    padding: 15px;
}

.table tbody td {
    padding: 15px;
    border-color: #e9ecef;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-paid {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-overdue {
    background: #f8d7da;
    color: #721c24;
}

.alert {
    border-radius: 12px;
    border: none;
    padding: 20px;
    margin-bottom: 25px;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
}

@media (max-width: 768px) {
    .contract-hero {
        padding: 20px;
    }
    
    .contract-hero h1 {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .analysis-grid {
        grid-template-columns: 1fr;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .btn-group {
        flex-direction: column;
    }
}
</style>

<div class="contract-detail-page">
    <div class="container-fluid">
        <!-- Hero Section -->
        

        <!-- Statistics Cards -->
        <div class="stats-grid">
            @foreach ($stats as $title => $amount)
                <div class="stat-card {{ $title === 'Écart' ? ($amount >= 0 ? 'positive' : 'negative') : '' }}">
                    <div class="stat-title">{{ $title }}</div>
                    <div class="stat-value" style="{{ $title === 'Écart' && $amount < 0 ? 'color: #dc3545;' : '' }}">
                        {{ number_format($amount, 0, ',', ' ') }} CFA
                    </div>
                    
                    @if($title === 'Coût de revient Réel' && $stats['Coût de revient Prév.'] > 0)
                        <div class="stat-progress">
                            <div class="stat-progress-bar" 
                                 style="width: {{ min(($amount / $stats['Coût de revient Prév.']) * 100, 100) }}%; 
                                        background: {{ $amount <= $stats['Coût de revient Prév.'] ? 'linear-gradient(90deg, #28a745, #20c997)' : 'linear-gradient(90deg, #dc3545, #fd7e14)' }};"></div>
                        </div>
                        <small style="color: #6c757d; margin-top: 5px; display: block;">
                            {{ number_format(($amount / $stats['Coût de revient Prév.']) * 100, 1) }}% du prévisionnel
                        </small>
                    @endif
                    
                    @if($title === 'CA Réalisé' && $stats['Montant du contrat'] > 0)
                        <div class="stat-progress">
                            <div class="stat-progress-bar" 
                                 style="width: {{ min(($amount / $stats['Montant du contrat']) * 100, 100) }}%;"></div>
                        </div>
                        <small style="color: #6c757d; margin-top: 5px; display: block;">
                            {{ number_format(($amount / $stats['Montant du contrat']) * 100, 1) }}% du contrat
                        </small>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="main-content">
            <!-- Contract Information Form -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-cogs"></i>Informations du contrat
                    </h2>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('contrats.update', $contrat->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="ref_contrat" class="form-label">
                                    <i class="fas fa-hashtag"></i>Référence du contrat
                                </label>
                                <input type="text" class="form-control" id="ref_contrat" name="ref_contrat" value="{{ $contrat->ref_contrat }}" required>
                            </div>

                            <div class="form-group">
                                <label for="nom_contrat" class="form-label">
                                    <i class="fas fa-file-signature"></i>Nom du contrat
                                </label>
                                <input type="text" class="form-control" id="nom_contrat" name="nom_contrat" value="{{ $contrat->nom_contrat }}" required>
                            </div>

                            <div class="form-group">
                                <label for="date_debut" class="form-label">
                                    <i class="fas fa-calendar-alt"></i>Date de début
                                </label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ $contrat->date_debut }}" required>
                            </div>

                            <div class="form-group">
                                <label for="date_fin" class="form-label">
                                    <i class="fas fa-calendar-check"></i>Date de fin
                                </label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ $contrat->date_fin }}">
                            </div>

                            <div class="form-group">
                                <label for="type_travaux" class="form-label">
                                    <i class="fas fa-hard-hat"></i>Type de travaux
                                </label>
                                <input type="text" class="form-control" id="type_travaux" name="type_travaux" value="{{ $contrat->type_travaux }}" required>
                            </div>

                            <div class="form-group">
                                <label for="taux_garantie" class="form-label">
                                    <i class="fas fa-shield-alt"></i>Taux de garantie (%)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="taux_garantie" name="taux_garantie" value="{{ $contrat->taux_garantie }}" required>
                            </div>

                            <div class="form-group">
                                <label for="client_id" class="form-label">
                                    <i class="fas fa-user"></i>Client
                                </label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @if($contrat->client_id == $client->id) selected @endif>{{ $client->nom_raison_sociale }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="montant" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i>Montant (CFA)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="montant" name="montant" value="{{ $contrat->montant }}" required>
                            </div>

                            <div class="form-group">
                                <label for="statut" class="form-label">
                                    <i class="fas fa-info-circle"></i>Statut
                                </label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="en cours" @if($contrat->statut == 'en cours') selected @endif>En cours</option>
                                    <option value="terminé" @if($contrat->statut == 'terminé') selected @endif>Terminé</option>
                                    <option value="annulé" @if($contrat->statut == 'annulé') selected @endif>Annulé</option>
                                </select>
                            </div>
                        </div>

                        <div class="btn-group">
                            <a href="{{ route('contrats.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Financial Analysis -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-line"></i>Analyse financière du contrat
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Les statistiques sont basées sur les prestations et factures liées au contrat.
                    </div>
                    
                    <div class="analysis-grid">
                        <div class="chart-container">
                            <h4 class="chart-title">Prévisions vs Réalisations</h4>
                            <canvas id="comparisonChart" width="400" height="250"></canvas>
                            
                            <div class="data-table">
                                <table class="table">
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
                        
                        <div class="chart-container">
                            <h4 class="chart-title">Rentabilité du contrat</h4>
                            <canvas id="profitabilityChart" width="400" height="250"></canvas>
                            
                            <div class="data-table">
                                <table class="table">
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
                    
                    <!-- Invoice History -->
                    <div class="chart-container" style="margin-top: 30px;">
                        <h4 class="chart-title">Historique des factures</h4>
                        
                        @php
                            $factures = \App\Models\Facture::where('id_contrat', $contrat->id)
                                ->orderBy('date_emission', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @if($factures->count() > 0)
                            <div class="data-table">
                                <table class="table">
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
                                                <td><strong>{{ $facture->numero_facture }}</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($facture->date_emission)->format('d/m/Y') }}</td>
                                                <td>{{ number_format($facture->montant_total, 0, ',', ' ') }} CFA</td>
                                                <td>
                                                    <span class="status-badge status-{{ $facture->statut == 'payée' ? 'paid' : ($facture->statut == 'en attente' ? 'pending' : 'overdue') }}">
                                                        {{ ucfirst($facture->statut) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('factures.show', $facture->id) }}" class="btn btn-sm" style="background: #667eea; color: white; padding: 5px 10px; border-radius: 6px; text-decoration: none;">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Aucune facture trouvée pour ce contrat.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique de comparaison
const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
const comparisonChart = new Chart(comparisonCtx, {
    type: 'bar',
    data: {
        labels: ['Déboursé Sec', 'Coût de revient'],
        datasets: [{
            label: 'Prévisionnel',
            data: [{{ $stats['DS Prévisionnel'] }}, {{ $stats['Coût de revient Prév.'] }}],
            backgroundColor: 'rgba(94, 179, 246, 0.8)',
            borderColor: 'rgba(94, 179, 246, 1)',
            borderWidth: 2,
            borderRadius: 8
        }, {
            label: 'Réalisé',
            data: [{{ $stats['DS Réalisé'] }}, {{ $stats['Coût de revient Réel'] }}],
            backgroundColor: 'rgba(3, 55, 101, 0.8)',
            borderColor: 'rgba(3, 55, 101, 1)',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: 'rgba(94, 179, 246, 1)',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' CFA';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('fr-FR', {
                            notation: 'compact',
                            compactDisplay: 'short'
                        }).format(value) + ' CFA';
                    }
                }
            },
            x: {
                grid: {
                    display: false
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
            data: [{{ $stats['Coût de revient Réel'] }}, {{ max(0, $stats['CA Réalisé'] - $stats['Coût de revient Réel']) }}],
            backgroundColor: [
                'rgba(220, 53, 69, 0.8)',
                'rgba(40, 167, 69, 0.8)'
            ],
            borderColor: [
                'rgba(220, 53, 69, 1)',
                'rgba(40, 167, 69, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: 'white',
                bodyColor: 'white',
                borderColor: 'rgba(94, 179, 246, 1)',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.parsed) + ' CFA (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>
@endsection