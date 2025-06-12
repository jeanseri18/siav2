@extends('layouts.app')

@section('title', 'Modifier une Prestation')
@section('page-title', 'Modifier une Prestation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('prestations.index') }}">Prestations</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
<i class="fas fa-edit me-2"></i>Modifier la Prestation
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('prestations.update', $prestation->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_contrat" class="app-form-label">
                                <i class="fas fa-file-contract me-2"></i>Contrat
                            </label>
                            <select name="id_contrat" id="id_contrat" class="app-form-select" required>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}" {{ $prestation->id_contrat == $contrat->id ? 'selected' : '' }}>
                                        {{ $contrat->nom_contrat }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Contrat associé à cette prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_artisan" class="app-form-label">
                                <i class="fas fa-hard-hat me-2"></i>Artisan (optionnel)
                            </label>
                            <select name="id_artisan" id="id_artisan" class="app-form-select">
                                <option value="">-- Sélectionnez un artisan --</option>
                                @foreach($artisans as $artisan)
                                    <option value="{{ $artisan->id }}" {{ $prestation->id_artisan == $artisan->id ? 'selected' : '' }}>
                                        {{ $artisan->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Artisan assigné à cette prestation</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="prestation_titre" class="app-form-label">
                                <i class="fas fa-clipboard-list me-2"></i>Prestation
                            </label>
                            <input type="string" name="prestation_titre" id="prestation_titre" class="app-form-control" value="{{ $prestation->prestation_titre }}" required>
                            <div class="app-form-text">Titre ou description courte de la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="detail" class="app-form-label">
                                <i class="fas fa-align-left me-2"></i>Détail
                            </label>
                            <input type="string" name="detail" id="detail" class="app-form-control" value="{{ $prestation->detail }}" required>
                            <div class="app-form-text">Description détaillée de la prestation</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="montant" class="app-form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>Montant
                            </label>
                            <input type="number" step="0.01" name="montant" id="montant" class="app-form-control" value="{{ $prestation->montant }}">
                            <div class="app-form-text">Montant de la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="taux_avancement" class="app-form-label">
                                <i class="fas fa-percentage me-2"></i>Taux d'avancement
                            </label>
                            <input type="number" min="0" max="100" name="taux_avancement" id="taux_avancement" class="app-form-control" value="{{ $prestation->taux_avancement ?? 0 }}">
                            <div class="app-form-text">Pourcentage d'avancement de la prestation (0-100)</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-group">
                    <label for="statut" class="app-form-label">
                        <i class="fas fa-tasks me-2"></i>Statut
                    </label>
                    <select name="statut" id="statut" class="app-form-select">
                        <option value="En cours" {{ $prestation->statut == 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Terminée" {{ $prestation->statut == 'Terminée' ? 'selected' : '' }}>Terminée</option>
                        <option value="Annulée" {{ $prestation->statut == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                    </select>
                    <div class="app-form-text">État actuel de la prestation</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('prestations.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-warning">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection