@extends('layouts.app')

@section('title', 'Ajouter une Prestation')
@section('page-title', 'Ajouter une Prestation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('prestations.index') }}">Prestations</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-tools me-2"></i>Ajouter une Prestation
            </h2>
        </div>
        
        @if ($errors->any())
        <div class="app-alert app-alert-danger">
            <div class="app-alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
        
        <div class="app-card-body">
            <form action="{{ route('prestations.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_artisan" class="app-form-label">
                                <i class="fas fa-hard-hat me-2"></i>Artisan
                            </label>
                            <select name="id_artisan" id="id_artisan" class="app-form-select" required>
                                <option value="">-- Sélectionnez un artisan --</option>
                                @foreach($artisans as $artisan)
                                    <option value="{{ $artisan->id }}">{{ $artisan->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Artisan qui réalisera la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_contrat" class="app-form-label">
                                <i class="fas fa-file-contract me-2"></i>Contrat
                            </label>
                            <select name="id_contrat" id="id_contrat" class="app-form-select" required>
                                <option value="">-- Sélectionnez un contrat --</option>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}">{{ $contrat->nom_contrat }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Contrat associé à cette prestation</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="prestation_titre" class="app-form-label">
                                <i class="fas fa-clipboard-list me-2"></i>Prestation
                            </label>
                            <input type="string" name="prestation_titre" id="prestation_titre" class="app-form-control" required>
                            <div class="app-form-text">Titre ou description courte de la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="detail" class="app-form-label">
                                <i class="fas fa-align-left me-2"></i>Détail
                            </label>
                            <input type="string" name="detail" id="detail" class="app-form-control" required>
                            <div class="app-form-text">Description détaillée de la prestation</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('prestations.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection