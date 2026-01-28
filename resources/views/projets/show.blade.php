@extends('layouts.app')

@section('title', 'Détails du Projet')
@section('page-title', 'Détails du Projet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
    <li class="breadcrumb-item active">{{ $projet->nom_projet }}</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in">
    <div class="app-card shadow-lg border-0">
        <!-- En-tête principal -->
        <div class="app-card-header bg-gradient-primary text-white rounded-top">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="app-card-title mb-0">
                    <i class="fas fa-project-diagram me-3"></i>
                    {{ $projet->nom_projet }}
                </h2>
                <div>
                    <span class="badge bg-white text-primary fs-6 px-3 py-2">{{ $projet->ref_projet }}</span>
                </div>
            </div>
        </div>

        <div class="app-card-body p-4">

            <!-- ========== CLIENT ========== -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="app-card border-left-primary shadow-sm">
                        <div class="app-card-header bg-light-primary">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-user-tie me-2"></i>Client
                            </h5>
                        </div>
                        <div class="app-card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex"><strong class="text-muted me-3">Raison sociale :</strong> <span>{{ $projet->clientFournisseur->nom_raison_sociale ?? '—' }}</span></div>
                                    <div class="d-flex"><strong class="text-muted me-3">Code client :</strong> <span class="text-primary fw-bold">{{ $projet->clientFournisseur->code ?? '—' }}</span></div>
                                    <div class="d-flex"><strong class="text-muted me-3">Type :</strong> <span>{{ ucfirst($projet->clientFournisseur->type ?? '—') }}</span></div>
                                    <div class="d-flex"><strong class="text-muted me-3">Délai paiement :</strong> <span>{{ $projet->clientFournisseur->delai_paiement ?? '—' }} jours</span></div>
                                    <div class="d-flex"><strong class="text-muted me-3">Secteur :</strong> <span>{{ $projet->clientFournisseur->secteur_activite ?? '—' }}</span></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Contacts principaux</h6>
                                    @forelse($projet->clientFournisseur->contactPersons->take(4) as $contact)
                                        <div class="border-start border-primary border-4 ps-3 py-2 mb-2 bg-light rounded">
                                            <div class="fw-bold">{{ $contact->civilite }} {{ $contact->prenoms }} {{ $contact->nom }}</div>
                                            <small class="text-muted">{{ $contact->fonction }}</small><br>
                                            <small><i class="fas fa-phone text-primary"></i> {{ $contact->telephone_1 }}</small>
                                            @if($contact->telephone_2)
                                                <small> • {{ $contact->telephone_2 }}</small>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-muted fst-italic">Aucun contact enregistré</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== PROJET ========== -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="app-card border-left-success shadow-sm">
                        <div class="app-card-header bg-light-success">
                            <h5 class="mb-0 text-primary"><i class="fas fa-cogs me-2"></i>Informations Projet</h5>
                        </div>
                        <div class="app-card-body">
                            <div class="row g-5">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light-success rounded">
                                        <i class="fas fa-play-circle fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Début</div>
                                        <div class="fw-bold">{{ $projet->date_debut ? \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') : '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light-warning rounded">
                                        <i class="fas fa-flag-checkered fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Fin prévue</div>
                                        <div class="fw-bold">{{ $projet->date_fin ? \Carbon\Carbon::parse($projet->date_fin)->format('d/m/Y') : 'Non définie' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 {{ $projet->statut == 'terminé' ? 'bg-light-success' : ($projet->statut == 'en cours' ? 'bg-light-warning' : 'bg-light-danger') }} rounded">
                                        <i class="fas fa-tasks fa-2x {{ $projet->statut == 'terminé' ? 'text-primary' : ($projet->statut == 'en cours' ? 'text-primary' : 'text-primary') }} mb-2"></i>
                                        <div class="small text-muted">Statut</div>
                                        <div class="fw-bold text-uppercase">{{ $projet->statut ?? 'Non défini' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6 class="text-primary"><i class="fas fa-user-tie me-2"></i>Chef de projet</h6>
                                    @if($projet->chefProjet)
                                        <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                                            <div class="me-3"><i class="fas fa-user-circle fa-3x text-primary"></i></div>
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
                                    <h6 class="text-primary"><i class="fas fa-hard-hat me-2"></i>Conducteur de travaux</h6>
                                    @if($projet->conducteurTravaux)
                                        <div class="d-flex align-items-center p-3 bg-light rounded shadow-sm">
                                            <div class="me-3"><i class="fas fa-user-hard-hat fa-3x text-primary"></i></div>
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
                    <div class="app-card border-left-warning shadow-sm">
                        <div class="app-card-header bg-light-warning">
                            <h5 class="mb-0 text-primary"><i class="fas fa-chart-line me-2"></i>Finances du projet</h5>
                        </div>
                        <div class="app-card-body">
                            <div class="row text-center g-4">
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm border">
                                        <i class="fas fa-file-invoice-dollar fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Montant global</div>
                                        <div class="display-6 fw-bold text-primary">{{ number_format($projet->montant_global ?? 0, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm border">
                                        <i class="fas fa-money-bill-wave fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Chiffre d'affaires</div>
                                        <div class="display-6 fw-bold text-primary">{{ number_format($projet->chiffre_affaire_global ?? 0, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm border">
                                        <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Dépenses totales</div>
                                        <div class="display-6 fw-bold text-primary">{{ number_format($projet->total_depenses ?? 0, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-4 bg-white rounded shadow-sm border">
                                        <i class="fas fa-percentage fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Marge estimée</div>
                                        <div class="display-6 fw-bold text-primary">
                                            @php
                                                $marge = $projet->montant_global && $projet->total_depenses
                                                    ? (($projet->montant_global - $projet->total_depenses) / $projet->montant_global) * 100
                                                    : 0;
                                            @endphp
                                            {{ number_format($marge, 1) }} %
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
                <div class="app-card border-left-info shadow-sm mb-5">
                    <div class="app-card-header bg-light-info">
                        <h5 class="mb-0 text-primary"><i class="fas fa-align-left me-2"></i>Description du projet</h5>
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
                    <div class="app-card border-left-secondary">
                        <div class="app-card-header bg-light">
                            <h6 class="mb-0 text-secondary"><i class="fas fa-info-circle me-2"></i>Informations système</h6>
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
        <div class="app-card-footer bg-light border-top d-flex justify-content-between align-items-center">
            <a href="{{ route('projets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('projets.edit', $projet->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Modifier le projet
            </a>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #1e3c72, #2a5298) !important;
    }
    .border-left-primary { border-left: 5px solid #1e3c72 !important; }
    .border-left-success { border-left: 5px solid #28a745 !important; }
    .border-left-warning { border-left: 5px solid #ffc107 !important; }
    .border-left-info { border-left: 5px solid #17a2b8 !important; }
    .bg-light-primary { background-color: rgba(30, 60, 114, 0.1) !important; }
    .bg-light-success { background-color: rgba(40, 167, 69, 0.1) !important; }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-light-info { background-color: rgba(23, 162, 184, 0.1) !important; }
</style>
@endsection