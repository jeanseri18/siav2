@extends('layouts.app')

@section('title', 'Détails du Projet')
@section('page-title', 'Détails du Projet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
    <li class="breadcrumb-item active">{{ $projet->nom_projet }}</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in projet-show-page">
    <div class="app-card shadow-lg border-0">
        <!-- En-tête principal -->
        <div class="app-card-header bg-gradient-primary text-white rounded-top projet-hero-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="app-card-title mb-0 projet-hero-title">
                    <i class="fas fa-project-diagram me-3"></i>
                    {{ $projet->nom_projet }}
                </h2>
                <div>
                    <span class="badge bg-white text-app-primary fs-6 px-3 py-2">{{ $projet->ref_projet }}</span>
                </div>
            </div>
        </div>

        <div class="app-card-body p-4">

            @php
                $contactsProjet = collect($projet->clientFournisseur?->contactPersons ?? [])
                    ->filter(fn (\App\Models\ContactPerson $c) => $c->hasDisplayableData())
                    ->values();
            @endphp

            <!-- ========== CLIENT ========== -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="app-card projet-section-card border-left-primary shadow-sm">
                        <div class="app-card-header projet-section-head">
                            <h5 class="mb-0 projet-section-title">
                                <i class="fas fa-user-tie me-2"></i>Client
                            </h5>
                        </div>
                        <div class="app-card-body">
                            <div class="row g-4">
                                <div class="{{ $contactsProjet->isNotEmpty() ? 'col-md-6' : 'col-12' }}">
                                    <div class="projet-info-stack">
                                        <div class="projet-info-row"><span class="projet-info-label">Raison sociale</span> <span class="projet-info-value">{{ $projet->clientFournisseur->nom_raison_sociale ?? '—' }}</span></div>
                                        <div class="projet-info-row"><span class="projet-info-label">Code client</span> <span class="projet-info-value text-app-primary fw-bold">{{ $projet->clientFournisseur->code ?? '—' }}</span></div>
                                        <div class="projet-info-row"><span class="projet-info-label">Type</span> <span class="projet-info-value">{{ ucfirst($projet->clientFournisseur->type ?? '—') }}</span></div>
                                        <div class="projet-info-row"><span class="projet-info-label">Délai paiement</span> <span class="projet-info-value">{{ $projet->clientFournisseur->delai_paiement ?? '—' }} jours</span></div>
                                        <div class="projet-info-row"><span class="projet-info-label">Secteur</span> <span class="projet-info-value">{{ $projet->clientFournisseur->secteur_activite ?? '—' }}</span></div>
                                    </div>
                                </div>
                                @if($contactsProjet->isNotEmpty())
                                <div class="col-md-6">
                                    <h6 class="projet-subsection-title mb-3"><i class="fas fa-users me-2"></i>Représentants / contacts</h6>
                                    @foreach($contactsProjet->take(4) as $contact)
                                        <div class="projet-contact-card mb-2">
                                            <div class="fw-bold text-app-primary-dark">{{ trim(implode(' ', array_filter([$contact->civilite, $contact->prenoms, $contact->nom]))) ?: '—' }}</div>
                                            @if($contact->fonction)
                                                <small class="text-muted d-block">{{ $contact->fonction }}</small>
                                            @endif
                                            @if($contact->telephone_1)
                                                <small><i class="fas fa-phone projet-accent-icon"></i> {{ $contact->telephone_1 }}</small>
                                            @endif
                                            @if($contact->telephone_2)
                                                <small> · {{ $contact->telephone_2 }}</small>
                                            @endif
                                            @if($contact->email)
                                                <small class="d-block mt-1"><i class="fas fa-envelope projet-accent-icon"></i> <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== PROJET ========== -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="app-card projet-section-card border-left-primary shadow-sm">
                        <div class="app-card-header projet-section-head">
                            <h5 class="mb-0 projet-section-title"><i class="fas fa-cogs me-2"></i>Informations Projet</h5>
                        </div>
                        <div class="app-card-body">
                            <div class="row g-5">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light-primary rounded">
                                        <i class="fas fa-play-circle fa-2x text-app-primary mb-2"></i>
                                        <div class="small text-muted">Début</div>
                                        <div class="fw-bold">{{ $projet->date_debut ? \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') : '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light-primary rounded">
                                        <i class="fas fa-flag-checkered fa-2x text-app-primary mb-2"></i>
                                        <div class="small text-muted">Fin prévue</div>
                                        <div class="fw-bold">{{ $projet->date_fin ? \Carbon\Carbon::parse($projet->date_fin)->format('d/m/Y') : 'Non définie' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 {{ $projet->statut == 'annulé' ? 'bg-light-danger' : 'bg-light-primary' }} rounded">
                                        <i class="fas fa-tasks fa-2x {{ $projet->statut == 'annulé' ? 'text-danger' : 'text-app-primary' }} mb-2"></i>
                                        <div class="small text-muted">Statut</div>
                                        <div class="fw-bold text-uppercase">{{ $projet->statut ?? 'Non défini' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6 class="projet-subsection-title"><i class="fas fa-user-tie me-2"></i>Chef de projet</h6>
                                    @if($projet->chefProjet)
                                        <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                                            <div class="me-3"><i class="fas fa-user-circle fa-3x text-app-primary"></i></div>
                                            <div>
                                                <div class="fw-bold">{{ $projet->chefProjet->prenom }} {{ $projet->chefProjet->nom }}</div>
                                                <small class="text-muted">{{ $projet->chefProjet->email }}</small><br>
                                                <small><i class="fas fa-phone"></i> {{ $projet->chefProjet->telephone }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted fst-italic">Non assigné</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="projet-subsection-title"><i class="fas fa-hard-hat me-2"></i>Conducteur de travaux</h6>
                                    @if($projet->conducteurTravaux)
                                        <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                                            <div class="me-3"><i class="fas fa-user-hard-hat fa-3x text-app-primary"></i></div>
                                            <div>
                                                <div class="fw-bold">{{ $projet->conducteurTravaux->prenom }} {{ $projet->conducteurTravaux->nom }}</div>
                                                <small class="text-muted">{{ $projet->conducteurTravaux->email }}</small><br>
                                                <small><i class="fas fa-phone"></i> {{ $projet->conducteurTravaux->telephone }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted fst-italic">Non assigné</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== FINANCES ========== -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="app-card projet-section-card border-left-primary shadow-sm">
                        <div class="app-card-header projet-section-head">
                            <h5 class="mb-0 projet-section-title"><i class="fas fa-chart-line me-2"></i>Finances du projet</h5>
                        </div>
                        <div class="app-card-body">
                            <div class="row text-center g-4">
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm projet-kpi-tile">
                                        <i class="fas fa-file-invoice-dollar fa-2x text-app-primary mb-2"></i>
                                        <div class="small text-muted">Montant global</div>
                                        <div class="projet-kpi-value text-app-primary">{{ number_format($projet->montant_global ?? 0, 0, ',', ' ') }} <span class="projet-kpi-currency">FCFA</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm projet-kpi-tile">
                                        <i class="fas fa-money-bill-wave fa-2x text-app-primary mb-2"></i>
                                        <div class="small text-muted">Chiffre d'affaires</div>
                                        <div class="projet-kpi-value text-app-primary">{{ number_format($projet->chiffre_affaire_global ?? 0, 0, ',', ' ') }} <span class="projet-kpi-currency">FCFA</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm projet-kpi-tile">
                                        <i class="fas fa-shopping-cart fa-2x text-app-primary mb-2"></i>
                                        <div class="small text-muted">Dépenses totales</div>
                                        <div class="projet-kpi-value text-app-primary">{{ number_format($projet->total_depenses ?? 0, 0, ',', ' ') }} <span class="projet-kpi-currency">FCFA</span></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm projet-kpi-tile">
                                        <i class="fas fa-percentage fa-2x text-app-primary mb-2"></i>
                                        <div class="small text-muted">Marge estimée</div>
                                        <div class="projet-kpi-value text-app-primary">
                                            @php
                                                $marge = ($projet->montant_global ?? 0) > 0
                                                    ? ((($projet->montant_global ?? 0) - ($projet->total_depenses ?? 0)) / ($projet->montant_global ?? 0)) * 100
                                                    : 0;
                                            @endphp
                                            {{ number_format($marge, 1, ',', ' ') }}<span class="projet-kpi-unit"> %</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <span>TVA 18% sur ventes</span>
                                        <span class="badge {{ $projet->hastva ? 'bg-success' : 'bg-danger' }} fs-6 px-3"> {{ $projet->hastva ? 'Oui' : 'Non' }} </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <span>TVA 18% sur achats</span>
                                        <span class="badge {{ $projet->tva_achat ? 'bg-success' : 'bg-danger' }} fs-6 px-3"> {{ $projet->tva_achat ? 'Oui' : 'Non' }} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== DESCRIPTION ========== -->
            @if($projet->description)
                <div class="app-card projet-section-card border-left-primary shadow-sm mb-5">
                    <div class="app-card-header projet-section-head">
                        <h5 class="mb-0 projet-section-title"><i class="fas fa-align-left me-2"></i>Description du projet</h5>
                    </div>
                    <div class="app-card-body bg-light rounded">
                        <div class="p-4 bg-white rounded shadow-sm">
                            {!! nl2br(e($projet->description)) !!}
                        </div>
                    </div>
                </div>
            @endif

            <!-- ========== INFO SYSTÈME ========== -->
            <div class="row">
                <div class="col-12">
                    <div class="app-card projet-section-card border-left-primary">
                        <div class="app-card-header projet-section-head">
                            <h6 class="mb-0 projet-section-title"><i class="fas fa-info-circle me-2"></i>Informations système</h6>
                        </div>
                        <div class="app-card-body">
                            <div class="row text-muted small">
                                <div class="col-md-4"><strong>Créé le</strong> {{ $projet->date_creation ? \Carbon\Carbon::parse($projet->date_creation)->format('d/m/Y à H:i') : '—' }}</div>
                                <div class="col-md-4"><strong>Par</strong> {{ $projet->createdBy?->prenom ?? 'Système' }} {{ $projet->createdBy?->nom ?? '' }}</div>
                                <div class="col-md-4"><strong>Dernière modification</strong> {{ $projet->updated_at ? \Carbon\Carbon::parse($projet->updated_at)->format('d/m/Y à H:i') : '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer avec actions -->
        <div class="app-card-footer projet-show-footer border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('projets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('projets.edit', $projet->id) }}" class="btn projet-btn-edit text-white">
                <i class="fas fa-edit me-2"></i>Modifier le projet
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .projet-show-page {
        --projet-nav-blue: #033d71;
        --projet-nav-blue-light: #033d71;
        --projet-nav-blue-dark: #033d71;
    }
    .projet-show-page .bg-gradient-primary {
        background: linear-gradient(135deg, var(--projet-nav-blue) 0%, var(--projet-nav-blue-dark) 100%) !important;
    }
    .projet-show-page .projet-hero-header {
        color: #fff !important;
    }
    .projet-show-page .projet-hero-header .projet-hero-title,
    .projet-show-page .projet-hero-header .app-card-title {
        color: #fff !important;
        font-size: clamp(1.1rem, 2.5vw, 1.65rem);
        font-weight: 700;
        line-height: 1.3;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    }
    .projet-show-page .projet-hero-header .projet-hero-title i,
    .projet-show-page .projet-hero-header .app-card-title i {
        color: rgba(255, 255, 255, 0.95) !important;
    }
    .projet-show-page .projet-hero-header .badge.bg-white {
        color: var(--projet-nav-blue-dark) !important;
        font-weight: 700;
    }
    .projet-show-page .border-left-primary {
        border-left: 5px solid var(--projet-nav-blue) !important;
    }
    .projet-section-card {
        border: 1px solid rgba(3, 61, 113, 0.12) !important;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .projet-section-head {
        background: linear-gradient(135deg, rgba(3, 61, 113, 0.12) 0%, rgba(10, 140, 255, 0.14) 100%) !important;
        border-bottom: 1px solid rgba(3, 61, 113, 0.12);
        padding: 0.85rem 1.25rem;
    }
    .projet-section-title {
        color: var(--projet-nav-blue-dark) !important;
        font-weight: 700;
    }
    .projet-show-page .bg-light-primary {
        background-color: rgba(3, 61, 113, 0.1) !important;
    }
    .projet-show-page .bg-light-danger {
        background-color: rgba(220, 53, 69, 0.12) !important;
    }
    .projet-show-page .text-app-primary {
        color: var(--projet-nav-blue) !important;
    }
    .projet-show-page .text-app-primary-dark {
        color: var(--projet-nav-blue-dark) !important;
    }
    .projet-show-page .border-app-primary {
        border-color: var(--projet-nav-blue) !important;
    }
    .projet-info-stack {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }
    .projet-info-row {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 0.35rem 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(3, 61, 113, 0.08);
    }
    .projet-info-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .projet-info-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
        min-width: 8.5rem;
        font-weight: 600;
    }
    .projet-info-value {
        flex: 1;
        font-weight: 500;
        color: #212529;
    }
    .projet-subsection-title {
        color: var(--projet-nav-blue-dark) !important;
        font-weight: 700;
    }
    .projet-contact-card {
        border: 1px solid rgba(3, 61, 113, 0.14);
        border-left: 4px solid var(--projet-nav-blue-light);
        border-radius: 0.375rem;
        padding: 0.85rem 1rem;
        background: linear-gradient(90deg, rgba(10, 140, 255, 0.06) 0%, #fff 12%);
    }
    .projet-accent-icon {
        color: var(--projet-nav-blue);
    }
    .projet-kpi-tile {
        border: 1px solid rgba(3, 61, 113, 0.12);
        border-top: 3px solid var(--projet-nav-blue);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .projet-kpi-tile:hover {
        box-shadow: 0 8px 24px rgba(3, 61, 113, 0.12) !important;
        transform: translateY(-2px);
    }
    .projet-kpi-value {
        font-size: 1.125rem;
        font-weight: 700;
        line-height: 1.35;
        margin-top: 0.35rem;
        word-break: break-word;
    }
    @media (min-width: 992px) {
        .projet-kpi-value {
            font-size: 1.25rem;
        }
    }
    .projet-kpi-currency,
    .projet-kpi-unit {
        font-size: 0.82em;
        font-weight: 600;
        opacity: 0.92;
    }
    .projet-show-footer {
        background: linear-gradient(180deg, #f8f9fa 0%, #eef2f6 100%) !important;
        padding: 1rem 1.25rem;
    }
    .projet-btn-edit {
        background: linear-gradient(135deg, var(--projet-nav-blue-light) 0%, var(--projet-nav-blue-dark) 100%);
        border: none;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        border-radius: 0.375rem;
    }
    .projet-btn-edit:hover {
        color: #fff;
        filter: brightness(1.06);
    }
</style>
@endpush