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
    --ctd-primary: #033d71;
    --ctd-primary-light: #033d71;
    --ctd-primary-dark: #033d71;
    background: linear-gradient(180deg, #f1f5f9 0%, #e9ecef 100%);
    min-height: 100vh;
    padding: 20px 0;
}

.contract-hero {
    background: linear-gradient(135deg, var(--ctd-primary) 0%, var(--ctd-primary-dark) 100%);
    border-radius: 20px;
    color: white;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 15px 35px rgba(3, 61, 113, 0.25);
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
    background: linear-gradient(90deg, var(--ctd-primary-light), var(--ctd-primary));
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
    color: var(--ctd-primary-dark);
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
    background: linear-gradient(90deg, var(--ctd-primary-light), var(--ctd-primary));
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
    box-shadow: 0 10px 30px rgba(3, 61, 113, 0.08);
    border: 1px solid rgba(3, 61, 113, 0.1);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, rgba(3, 61, 113, 0.07) 0%, rgba(10, 140, 255, 0.1) 100%);
    padding: 25px 30px;
    border-bottom: 1px solid rgba(3, 61, 113, 0.12);
}

.card-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--ctd-primary-dark);
    margin: 0;
    display: flex;
    align-items: center;
}

.card-title i {
    margin-right: 12px;
    color: var(--ctd-primary-light);
}

.card-header-contract-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.contract-context-badge {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    background: #ffffff;
    border: 1px solid rgba(3, 61, 113, 0.18);
    border-radius: 8px;
    font-size: 0.85rem;
    max-width: min(100%, 42rem);
    margin-left: auto;
}

.contract-context-badge .contract-name {
    font-weight: 600;
    color: #212529;
    line-height: 1.3;
    text-align: right;
}

.contract-context-badge .contract-ref {
    color: #6c757d;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    background: #f8f9fa;
    padding: 0.2rem 0.45rem;
    border-radius: 4px;
    white-space: nowrap;
}

.contract-context-badge .status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28a745;
    flex-shrink: 0;
    animation: contractStatusPulse 2s infinite;
}

@keyframes contractStatusPulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
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
    color: var(--ctd-primary-light);
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
    border-color: var(--ctd-primary-light);
    box-shadow: 0 0 0 0.2rem rgba(10, 140, 255, 0.22);
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
    background: linear-gradient(135deg, var(--ctd-primary-light) 0%, var(--ctd-primary-dark) 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(3, 61, 113, 0.35);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(3, 61, 113, 0.4);
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
    background: linear-gradient(135deg, var(--ctd-primary) 0%, var(--ctd-primary-dark) 100%);
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

/* Représentants client (fiches non vides uniquement) */
.contract-rep-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--ctd-primary-dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.contract-rep-title i {
    color: var(--ctd-primary);
}
.contract-rep-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
}
.contract-rep-card {
    border: 1px solid rgba(3, 61, 113, 0.14);
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 14px rgba(3, 61, 113, 0.07);
}
.contract-rep-card-head {
    background: linear-gradient(135deg, var(--ctd-primary) 0%, var(--ctd-primary-dark) 100%);
    color: #fff;
    padding: 0.65rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.contract-rep-card-body {
    padding: 1rem;
}
.contract-rep-card-body .rep-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #6c757d;
    margin-bottom: 0.15rem;
}
.contract-rep-card-body .rep-value {
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.65rem;
}
</style>

<div class="contract-detail-page">
    <div class="container-fluid">
        <!-- Hero Section -->
        

        <!-- Contract Details Section -->
        <div class="content-card" style="margin-bottom: 30px;">
            <div class="card-header card-header-contract-meta">
                <h2 class="card-title mb-0">
                    <i class="fas fa-chart-line"></i>Détails du contrat
                </h2>
                <div class="contract-context-badge" title="Contrat en cours">
                    <span class="contract-name">{{ session('contrat_nom') }}</span>
                    <span class="contract-ref">{{ session('ref_contrat') }}</span>
                    <span class="status-indicator active" aria-hidden="true"></span>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <!-- Montant du contrat -->
            <div class="stat-card">
                <div class="stat-title">Montant du contrat</div>
                <div class="stat-value">{{ number_format($stats['Montant du contrat'], 0, ',', ' ') }} FCFA</div>
            </div>

            <!-- Coûts prévisionnels -->
            <div class="stat-card">
                <div class="stat-title">DS Prévisionnel</div>
                <div class="stat-value">{{ number_format($stats['DS Prévisionnel'], 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">FC Prévisionnel</div>
                <div class="stat-value">{{ number_format($stats['FC Prévisionnel'], 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">FG Prévisionnel</div>
                <div class="stat-value">{{ number_format($stats['FG Prévisionnel'], 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Coût de revient Prév.</div>
                <div class="stat-value">{{ number_format($stats['Coût de revient Prév.'], 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="stat-card positive">
                <div class="stat-title">Bénéfice Prévisionnel</div>
                <div class="stat-value" style="{{ $stats['Bénéfice Prévisionnel'] < 0 ? 'color: #dc3545;' : 'color: #28a745;' }}">
                    {{ number_format($stats['Bénéfice Prévisionnel'], 0, ',', ' ') }} FCFA
                </div>
            </div>

            <!-- Coûts réalisés -->
            <div class="stat-card">
                <div class="stat-title">DS Réalisé</div>
                <div class="stat-value">{{ number_format($stats['DS Réalisé'], 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">FC Réalisé</div>
                <div class="stat-value">{{ number_format($stats['FC Réalisé'], 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">FG Réalisé</div>
                <div class="stat-value">{{ number_format($stats['FG Réalisé'], 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Coût de revient Réel</div>
                <div class="stat-value">{{ number_format($stats['Coût de revient Réel'], 0, ',', ' ') }} FCFA</div>
                @if($stats['Coût de revient Prév.'] > 0)
                    <div class="stat-progress">
                        <div class="stat-progress-bar" 
                             style="width: {{ min(($stats['Coût de revient Réel'] / $stats['Coût de revient Prév.']) * 100, 100) }}%; 
                                    background: {{ $stats['Coût de revient Réel'] <= $stats['Coût de revient Prév.'] ? 'linear-gradient(90deg, #28a745, #20c997)' : 'linear-gradient(90deg, #dc3545, #fd7e14)' }};"></div>
                    </div>
                    <small style="color: #6c757d; margin-top: 5px; display: block;">
                        {{ number_format(($stats['Coût de revient Réel'] / $stats['Coût de revient Prév.']) * 100, 1) }}% du prévisionnel
                    </small>
                @endif
            </div>

            <div class="stat-card positive">
                <div class="stat-title">Bénéfice Réalisé</div>
                <div class="stat-value" style="{{ $stats['Bénéfice Réalisé'] < 0 ? 'color: #dc3545;' : 'color: #28a745;' }}">
                    {{ number_format($stats['Bénéfice Réalisé'], 0, ',', ' ') }} FCFA
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">CA Réalisé</div>
                <div class="stat-value">{{ number_format($stats['CA Réalisé'], 0, ',', ' ') }} FCFA</div>
                @if($stats['Montant du contrat'] > 0)
                    <div class="stat-progress">
                        <div class="stat-progress-bar" 
                             style="width: {{ min(($stats['CA Réalisé'] / $stats['Montant du contrat']) * 100, 100) }}%;"></div>
                    </div>
                    <small style="color: #6c757d; margin-top: 5px; display: block;">
                        {{ number_format(($stats['CA Réalisé'] / $stats['Montant du contrat']) * 100, 1) }}% du contrat
                    </small>
                @endif
            </div>

            <!-- <div class="stat-card {{ $stats['Écart'] >= 0 ? 'positive' : 'negative' }}">
                <div class="stat-title">Écart (Prév. - Réel)</div>
                <div class="stat-value" style="{{ $stats['Écart'] < 0 ? 'color: #dc3545;' : 'color: #28a745;' }}">
                    {{ number_format($stats['Écart'], 0, ',', ' ') }} FCFA
                </div>
            </div> -->
        </div>

        <div class="main-content">
            <!-- Contract Information Form -->
            <!-- Zone Client -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user"></i>Zone Client
                    </h2>
                </div>
                
                <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-hashtag"></i>N° Client
                                </label>
                                <div>{{ $contrat->client_id }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>Nom du client
                                </label>
                                <div>{{ $contrat->client->nom_raison_sociale ?? 'N/A' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tag"></i>Type de client
                                </label>
                                <div>{{ $contrat->client->type ?? 'N/A' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i>Délai de paiement
                                </label>
                                <div>{{ $contrat->client->delai_paiement ?? 'N/A' }} jours</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-industry"></i>Secteur d'activité
                                </label>
                                <div>{{ $contrat->client->secteur_activite ?? 'N/A' }}</div>
                            </div>
                        </div>

                        @php
                            $representantsClient = ($contrat->client?->contactPersons ?? collect())
                                ->filter(fn (\App\Models\ContactPerson $c) => $c->hasDisplayableData())
                                ->values();
                        @endphp

                        @if($representantsClient->isNotEmpty())
                        <!-- Représentants du client (uniquement les fiches renseignées) -->
                        <h4 class="contract-rep-title mt-4 mb-3">
                            <i class="fas fa-users"></i>Représentants du client
                        </h4>
                        <div class="contract-rep-grid">
                            @foreach($representantsClient as $index => $contact)
                                <div class="contract-rep-card">
                                    <div class="contract-rep-card-head">
                                        <i class="fas fa-user-tie"></i> Représentant {{ $index + 1 }}
                                    </div>
                                    <div class="contract-rep-card-body">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <div class="rep-label">Nom</div>
                                                <div class="rep-value">{{ $contact->nom ?: '—' }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="rep-label">Prénoms</div>
                                                <div class="rep-value">{{ $contact->prenoms ?: '—' }}</div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="rep-label">Fonction</div>
                                                <div class="rep-value">{{ $contact->fonction ?: '—' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="rep-label">Téléphone 1</div>
                                                <div class="rep-value">{{ $contact->telephone_1 ?: '—' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="rep-label">Téléphone 2</div>
                                                <div class="rep-value">{{ $contact->telephone_2 ?: '—' }}</div>
                                            </div>
                                            @if($contact->email)
                                                <div class="col-12">
                                                    <div class="rep-label">E-mail</div>
                                                    <div class="rep-value"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @endif
                </div>
            </div>

            <!-- Zone Contrat -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-file-contract"></i>Zone Contrat
                    </h2>
                </div>
                
                <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-hashtag"></i>N° Contrat
                                </label>
                                <div>{{ $contrat->ref_contrat }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-file-signature"></i>Nom du Contrat
                                </label>
                                <div>{{ $contrat->nom_contrat }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-hard-hat"></i>Type du travaux
                                </label>
                                <div>{{ $contrat->type_travaux }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i>Date de début
                                </label>
                                <div>{{ $contrat->date_debut }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-check"></i>Date de fin
                                </label>
                                <div>{{ $contrat->date_fin }}</div>
                            </div>
                        </div>

                        <!-- Chef chantier -->
                        <h4 class="mt-4 mb-3">
                            <i class="fas fa-user-tie"></i>Chef chantier
                        </h4>
                        <div class="form-grid">
                            @php
                                $chefChantier = $contrat->chefChantier;
                            @endphp
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>Nom
                                </label>
                                <div>{{ $chefChantier->nom ?? 'N/A' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>Prénoms
                                </label>
                                <div>{{ $chefChantier->prenom ?? 'N/A' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i>Email
                                </label>
                                <div>{{ $chefChantier->email ?? 'N/A' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>Téléphone
                                </label>
                                <div>{{ $chefChantier->telephone ?? 'N/A' }}</div>
                            </div>
                        </div>
                </div>
            </div>

            <!-- Zone Financière -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-money-bill-wave"></i>Zone Financière
                    </h2>
                </div>
                
                <div class="card-body">
                        <!-- Informations fiscales -->
                        <h4 class="mb-3">
                            <i class="fas fa-receipt"></i>Informations fiscales
                        </h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-percentage"></i>Facturation client soumise à TVA18%
                                </label>
                                <div>{{ ($contrat->tva_18 ?? true) ? 'Oui' : 'Non' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-shield-alt"></i>Retenues de garantie (%)
                                </label>
                                <div>{{ $contrat->taux_garantie }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-shield-alt"></i>Retenue décennale (%)
                                </label>
                                <div>{{ $contrat->retenue_decennale ?? 0 }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-money-check-alt"></i>Avance de démarrage
                                </label>
                                <div>{{ $contrat->avance_demarrage ?? 0 }}</div>
                            </div>
                        </div>

                        <!-- Données financières actuelles -->
                        <h4 class="mt-4 mb-3">
                            <i class="fas fa-chart-line"></i>Données financières actuelles
                        </h4>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave"></i>Mt du contrat (CFA)
                                </label>
                                <div>{{ number_format($stats['Montant du contrat'] ?? 0, 0, ',', ' ') }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-line"></i>CA réalisé (CFA)
                                </label>
                                <div>{{ number_format($stats['CA Réalisé'] ?? 0, 0, ',', ' ') }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-bar"></i>DS prévisionnel (CFA)
                                </label>
                                <div>{{ number_format($stats['DS Prévisionnel'] ?? 0, 0, ',', ' ') }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-bar"></i>DS réalisé (CFA)
                                </label>
                                <div>{{ number_format($stats['DS Réalisé'] ?? 0, 0, ',', ' ') }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-bar"></i>FC prévisionnel (CFA)
                                </label>
                                <div>{{ number_format($stats['FC Prévisionnel'] ?? 0, 0, ',', ' ') }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-bar"></i>FC réalisé (CFA)
                                </label>
                                <div>{{ number_format($stats['FC Réalisé'] ?? 0, 0, ',', ' ') }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-bar"></i>FG prévisionnel (CFA)
                                </label>
                                <div>{{ number_format($stats['FG Prévisionnel'] ?? 0, 0, ',', ' ') }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-bar"></i>FG réalisé (CFA)
                                </label>
                                <div>{{ number_format($stats['FG Réalisé'] ?? 0, 0, ',', ' ') }}</div>
                            </div>
                        </div>
                </div>
            </div>

            <!-- Information système -->
            <div class="content-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-info-circle"></i>Information système
                    </h2>
                </div>
                
                <div class="card-body">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-plus"></i>Date de création
                                </label>
                                <div>{{ $contrat->created_at ? $contrat->created_at->format('d/m/Y H:i') : 'N/A' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-edit"></i>Dernière date de modification
                                </label>
                                <div>{{ $contrat->updated_at ? $contrat->updated_at->format('d/m/Y H:i') : 'N/A' }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-info-circle"></i>Statut du contrat
                                </label>
                                <div>{{ ucfirst($contrat->statut) }}</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-building"></i>Business Unit
                                </label>
                                <div>{{ $contrat->projet?->bu?->nom ?? 'N/A' }}</div>
                            </div>
                        </div>
                </div>
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
                                            <td><strong>Bénéfice prévisionnel</strong></td>
                                            <td>{{ number_format($stats['Bénéfice Prévisionnel'], 0, ',', ' ') }} CFA</td>
                                            <td>
                                                {{ $stats['Montant du contrat'] > 0 ?
                                                    number_format(($stats['Bénéfice Prévisionnel'] / $stats['Montant du contrat']) * 100, 1) : 0
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($factures as $facture)
                                            <tr>
                                                <td><strong>{{ $facture->num ?? ('#'.$facture->id) }}</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($facture->date_emission)->format('d/m/Y') }}</td>
                                                <td>{{ number_format($facture->montant_total, 0, ',', ' ') }} CFA</td>
                                                <td>
                                                    <span class="status-badge status-{{ $facture->statut == 'payée' ? 'paid' : ($facture->statut == 'en attente' ? 'pending' : 'overdue') }}">
                                                        {{ ucfirst($facture->statut) }}
                                                    </span>
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
@endsection

@push('scripts')
<script>
(function () {
    function initContratCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js non chargé');
            return;
        }
        var compEl = document.getElementById('comparisonChart');
        var profEl = document.getElementById('profitabilityChart');
        if (!compEl || !profEl) {
            return;
        }

        var dsPrev = Number({!! json_encode((float) ($stats['DS Prévisionnel'] ?? 0)) !!});
        var coutPrev = Number({!! json_encode((float) ($stats['Coût de revient Prév.'] ?? 0)) !!});
        var dsReal = Number({!! json_encode((float) ($stats['DS Réalisé'] ?? 0)) !!});
        var coutReal = Number({!! json_encode((float) ($stats['Coût de revient Réel'] ?? 0)) !!});
        var caReal = Number({!! json_encode((float) ($stats['CA Réalisé'] ?? 0)) !!});

        new Chart(compEl.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Déboursé Sec', 'Coût de revient'],
                datasets: [{
                    label: 'Prévisionnel',
                    data: [dsPrev, coutPrev],
                    backgroundColor: 'rgba(94, 179, 246, 0.8)',
                    borderColor: 'rgba(94, 179, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }, {
                    label: 'Réalisé',
                    data: [dsReal, coutReal],
                    backgroundColor: 'rgba(3, 55, 101, 0.8)',
                    borderColor: 'rgba(3, 55, 101, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, padding: 20 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(94, 179, 246, 1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (context) {
                                var y = context.parsed.y;
                                if (y === undefined || y === null) {
                                    return context.dataset.label + ': —';
                                }
                                return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR').format(y) + ' CFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: {
                            callback: function (value) {
                                return new Intl.NumberFormat('fr-FR', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                }).format(value) + ' CFA';
                            }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        var marge = Math.max(0, caReal - coutReal);
        var profitData, profitLabels, profitBg, profitBorder;
        if (coutReal + marge <= 0) {
            profitData = [1];
            profitLabels = ['En attente de données (prévoir CA / coûts réalisés)'];
            profitBg = ['rgba(108, 117, 125, 0.35)'];
            profitBorder = ['rgba(108, 117, 125, 0.9)'];
        } else {
            profitData = [coutReal, marge];
            profitLabels = ['Coût de revient réel', 'Marge brute réalisée'];
            profitBg = ['rgba(220, 53, 69, 0.8)', 'rgba(40, 167, 69, 0.8)'];
            profitBorder = ['rgba(220, 53, 69, 1)', 'rgba(40, 167, 69, 1)'];
        }

        new Chart(profEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: profitLabels,
                datasets: [{
                    data: profitData,
                    backgroundColor: profitBg,
                    borderColor: profitBorder,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(94, 179, 246, 1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function (context) {
                                var raw = context.raw;
                                var dataArr = context.dataset.data || [];
                                var total = dataArr.reduce(function (a, b) { return a + Number(b); }, 0);
                                if (total <= 0) {
                                    return context.label;
                                }
                                var pct = ((Number(raw) / total) * 100).toFixed(1);
                                if (profitData.length === 1 && profitData[0] === 1 && coutReal + marge <= 0) {
                                    return 'Aucun montant réalisé à afficher pour l’instant';
                                }
                                return context.label + ': ' + new Intl.NumberFormat('fr-FR').format(raw) + ' CFA (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContratCharts);
    } else {
        initContratCharts();
    }
})();
</script>
@endpush