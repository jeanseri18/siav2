@extends('layouts.app')

@section('title', 'Détails du Projet')
@section('page-title', 'Détails du Projet')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item active">{{ session('projet_nom') }}</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-project-diagram me-2"></i>Détails du Projet: {{ $projet->nom_projet }}
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('projets.edit', $projet->id) }}" class="app-btn app-btn-warning app-btn-icon">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>
        </div>
        
        <div class="app-card-body">
            <div class="app-d-flex app-flex-column app-gap-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="app-d-flex app-flex-column app-gap-3">
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-hashtag me-2"></i>Référence
                                </h5>
                                <p>{{ $projet->ref_projet }}</p>
                            </div>
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-folder me-2"></i>Nom
                                </h5>
                                <p>{{ $projet->nom_projet }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="app-d-flex app-flex-column app-gap-3">
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-user-tie me-2"></i>Client
                                </h5>
                                <p>{{ $projet->client }}</p>
                            </div>
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-hard-hat me-2"></i>Conducteur
                                </h5>
                                <p>{{ $projet->conducteur_travaux }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="app-d-flex app-flex-column app-gap-3">
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-play-circle me-2"></i>Début
                                </h5>
                                <p>{{ \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-flag-checkered me-2"></i>Fin
                                </h5>
                                <p>{{ $projet->date_fin ? \Carbon\Carbon::parse($projet->date_fin)->format('d/m/Y') : 'Non défini' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="app-d-flex app-flex-column app-gap-3">
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-industry me-2"></i>Secteur
                                </h5>
                                <p>{{ $projet->secteurActivite->nom }}</p>
                            </div>
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-building me-2"></i>BU
                                </h5>
                                <p>{{ $projet->bu->nom }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="app-d-flex app-flex-column app-gap-3">
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-percent me-2"></i>TVA
                                </h5>
                                <p>{{ $projet->hastva ? 'Oui' : 'Non' }}</p>
                            </div>
                            <div>
                                <h5 class="app-fw-bold text-primary">
                                    <i class="fas fa-tasks me-2"></i>Statut
                                </h5>
                                <span class="app-badge app-badge-{{ $projet->statut == 'en cours' ? 'warning' : ($projet->statut == 'terminé' ? 'success' : 'danger') }} app-badge-pill">
                                    {{ ucfirst($projet->statut) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div>
                            <h5 class="app-fw-bold text-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Création
                            </h5>
                            <p>{{ \Carbon\Carbon::parse($projet->date_creation)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                
                @if($projet->description)
                <div class="row mt-4">
                    <div class="col-12">
                        <div>
                            <h5 class="app-fw-bold text-primary">
                                <i class="fas fa-align-left me-2"></i>Description
                            </h5>
                            <div class="app-p-3 app-bg-light rounded">
                                {{ $projet->description }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="app-card-footer">
            <a href="{{ route('projets.index') }}" class="app-btn app-btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection