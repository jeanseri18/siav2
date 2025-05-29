@extends('layouts.app')

@section('title', 'Modifier un Secteur d\'Activité')
@section('page-title', 'Modifier un Secteur d\'Activité')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('secteur_activites.index') }}">Secteurs d'Activité</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Secteur: {{ $secteur->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('secteur_activites.update', $secteur->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-font me-2"></i>Nom
                    </label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $secteur->nom) }}" class="app-form-control" required>
                    <div class="app-form-text">Nom du secteur d'activité</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('secteur_activites.index') }}" class="app-btn app-btn-secondary">
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