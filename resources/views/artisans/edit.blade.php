@extends('layouts.app')

@section('title', 'Modifier un Artisan')
@section('page-title', 'Modifier un Artisan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('artisans.index') }}">Artisans</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier l'Artisan: {{ $artisan->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('artisans.update', $artisan->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-user me-2"></i>Nom
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $artisan->nom }}" required>
                    <div class="app-form-text">Nom complet de l'artisan</div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_corpmetier" class="app-form-label">
                                <i class="fas fa-hammer me-2"></i>Corps de Métier
                            </label>
                            <select name="id_corpmetier" id="id_corpmetier" class="app-form-select" required>
                                @foreach($corpsMetiers as $corp)
                                    <option value="{{ $corp->id }}" {{ $artisan->id_corpmetier == $corp->id ? 'selected' : '' }}>
                                        {{ $corp->nom }}
                                    </option>
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
                                <option value="artisan" {{ $artisan->type == 'artisan' ? 'selected' : '' }}>Artisan</option>
                                <option value="travailleur" {{ $artisan->type == 'travailleur' ? 'selected' : '' }}>Travailleur</option>
                            </select>
                            <div class="app-form-text">Catégorie de prestataire</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('artisans.index') }}" class="app-btn app-btn-secondary">
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