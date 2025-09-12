{{-- Page Create - Créer un contrat --}}
@extends('layouts.app')

@section('title', 'Créer un contrat')
@section('page-title', 'Créer un contrat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Créer</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class=" app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-contract me-2"></i>Créer un nouveau contrat
                    </h2>
                </div>
                
                <div class="app-card-body">
                    @if ($errors->any())
                    <div class="app-alert app-alert-danger">
                        <div class="app-alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="app-alert-content">
                            <div class="app-alert-text">
                                @foreach ($errors->all() as $error)
                                    <p class="app-mb-1">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                    
                    <form action="{{ route('contrats.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <div class="app-form-group">
                            <label for="nom_contrat" class="app-form-label">
                                <i class="fas fa-file-signature me-2"></i>Nom du contrat
                            </label>
                            <input type="text" class="app-form-control" id="nom_contrat" name="nom_contrat" required>
                            <div class="app-form-text">Nom ou titre du contrat</div>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_debut" class="app-form-label">
                                        <i class="fas fa-calendar-alt me-2"></i>Date de début
                                    </label>
                                    <input type="date" class="app-form-control" id="date_debut" name="date_debut" required>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_fin" class="app-form-label">
                                        <i class="fas fa-calendar-check me-2"></i>Date de fin
                                    </label>
                                    <input type="date" class="app-form-control" id="date_fin" name="date_fin">
                                    <div class="app-form-text">Optionnel pour les contrats sans date de fin</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="type_travaux" class="app-form-label">
                                <i class="fas fa-hard-hat me-2"></i>Type de travaux
                            </label>
                            <select name="type_travaux" id="type_travaux" class="app-form-select" required>
                                <option value="">Sélectionner un type Travaux</option>
                                @foreach ($typeTravaux as $type)
                                    <option value="{{ $type->nom }}">{{ $type->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Catégorie des travaux à réaliser</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="taux_garantie" class="app-form-label">
                                <i class="fas fa-shield-alt me-2"></i>Taux de garantie
                            </label>
                            <input type="number" step="0.01" class="app-form-control" id="taux_garantie" name="taux_garantie" required>
                            <div class="app-form-text">Pourcentage de garantie applicable</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="client_id" class="app-form-label">
                                <i class="fas fa-user me-2"></i>Client
                            </label>
                            <select name="client_id" id="client_id" class="app-form-select" required>
                                <option value="">Sélectionner un client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->nom_raison_sociale && $client->prenoms ? $client->nom_raison_sociale . ' ' . $client->prenoms : ($client->nom_raison_sociale ?? $client->prenoms) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Client avec lequel le contrat est établi</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="chef_chantier_id" class="app-form-label">
                                <i class="fas fa-user-tie me-2"></i>Chef de chantier
                            </label>
                            <select name="chef_chantier_id" id="chef_chantier_id" class="app-form-select">
                                <option value="">Sélectionner un chef de chantier</option>
                                @foreach ($chefsChantier as $chef)
                                    <option value="{{ $chef->id }}">
                                        {{ $chef->nom }} {{ $chef->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Chef de chantier responsable du contrat</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="montant" class="app-form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>Montant <span class="text-muted">(optionnel)</span>
                            </label>
                            <input type="number" step="0.01" class="app-form-control" id="montant" name="montant" placeholder="Sera mis à jour après validation du DQE">
                            <div class="app-form-text">Montant total du contrat - sera défini après validation du DQE</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="statut" class="app-form-label">
                                <i class="fas fa-info-circle me-2"></i>Statut
                            </label>
                            <select class="app-form-select" id="statut" name="statut" required>
                                <option value="en cours">En cours</option>
                                <option value="terminé">Terminé</option>
                                <option value="annulé">Annulé</option>
                            </select>
                            <div class="app-form-text">État actuel du contrat</div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('contrats.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Créer le contrat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection