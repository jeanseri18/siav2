@extends('layouts.app')

@section('title', 'Modifier un Secteur')
@section('page-title', 'Modifier un Secteur')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('secteurs.index') }}">Secteurs</a></li>
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
            <form action="{{ route('secteurs.update', $secteur->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-font me-2"></i>Nom du secteur
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $secteur->nom }}" required>
                    <div class="app-form-text">Nom du secteur géographique</div>
                </div>
                
                <div class="app-form-group">
                    <label for="quartier_id" class="app-form-label">
                        <i class="fas fa-map-marker-alt me-2"></i>Quartier
                    </label>
                    <select name="quartier_id" id="quartier_id" class="app-form-select" required>
                        @foreach($quartiers as $quartier)
                            <option value="{{ $quartier->id }}" {{ $secteur->quartier_id == $quartier->id ? 'selected' : '' }}>
                                {{ $quartier->nom }} - {{ $quartier->commune->nom }} ({{ $quartier->commune->ville->nom }})
                            </option>
                        @endforeach
                    </select>
                    <div class="app-form-text">Quartier auquel appartient ce secteur</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('secteurs.index') }}" class="app-btn app-btn-secondary">
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