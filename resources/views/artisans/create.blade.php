@extends('layouts.app')

@section('title', 'Ajouter un Artisan')
@section('page-title', 'Ajouter un Artisan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('artisans.index') }}">Artisans</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-hard-hat me-2"></i>Ajouter un Artisan
            </h2></div>
        
        <div class="app-card-body">
            <form action="{{ route('artisans.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-user me-2"></i>Nom
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" required>
                    <div class="app-form-text">Nom complet de l'artisan</div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_corpmetier" class="app-form-label">
                                <i class="fas fa-hammer me-2"></i>Corps de Métier
                            </label>
                            <select name="id_corpmetier" id="id_corpmetier" class="app-form-select" required>
                                <option value="">-- Sélectionner un corps de métier --</option>
                                @foreach($corpsMetiers as $corp)
                                    <option value="{{ $corp->id }}">{{ $corp->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Spécialité de l'artisan</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="type" class="app-form-label">
                                <i class="fas fa-user-tag me-2"></i>Type
                            </label>
                            <select name="type" id="type" class="app-form-select" required>
                                <option value="artisan">Artisan</option>
                                <option value="travailleur">Travailleur</option>
                            </select>
                            <div class="app-form-text">Catégorie de prestataire</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('artisans.index') }}" class="app-btn app-btn-secondary">
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