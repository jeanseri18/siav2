{{-- Page Edit - Modifier un BU --}}
@extends('layouts.app')

@section('title', 'Modifier un BU')
@section('page-title', 'Modifier un BU')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bu.index') }}">Business Units</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')


<div class=" app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-building me-2"></i>Modifier le BU : {{ $bu->nom }}
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('bu.update', $bu->id) }}" method="POST" enctype="multipart/form-data" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Nom
                            </label>
                            <input type="text" name="nom" value="{{ old('nom', $bu->nom) }}" class="app-form-control" required>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="secteur_activite_id" class="app-form-label">
                                <i class="fas fa-industry me-2"></i>Secteur d'activité
                            </label>
                            <select name="secteur_activite_id" class="app-form-select" required>
                                @foreach($secteurs as $secteur)
                                    <option value="{{ $secteur->id }}" {{ $secteur->id == old('secteur_activite_id', $bu->secteur_activite_id) ? 'selected' : '' }}>
                                        {{ $secteur->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="adresse" class="app-form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Adresse
                            </label>
                            <input type="text" name="adresse" value="{{ old('adresse', $bu->adresse) }}" class="app-form-control" required>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="numero_rccm" class="app-form-label">
                                        <i class="fas fa-file-alt me-2"></i>Numéro RCCM
                                    </label>
                                    <input type="text" name="numero_rccm" value="{{ old('numero_rccm', $bu->numero_rccm) }}" class="app-form-control" required>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="numero_cc" class="app-form-label">
                                        <i class="fas fa-id-card me-2"></i>Numéro CC
                                    </label>
                                    <input type="text" name="numero_cc" value="{{ old('numero_cc', $bu->numero_cc) }}" class="app-form-control" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="statut" class="app-form-label">
                                <i class="fas fa-toggle-on me-2"></i>Statut
                            </label>
                            <select name="statut" class="app-form-select">
                                <option value="actif" {{ old('statut', $bu->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ old('statut', $bu->statut) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="logo" class="app-form-label">
                                <i class="fas fa-image me-2"></i>Logo
                            </label>
                            <input type="file" name="logo" class="app-form-control">
                            <div class="app-form-text">Télécharger un nouveau logo (facultatif)</div>
                            @if ($bu->logo)
                                <div class="app-mt-3">
                                    <img src="{{ asset('storage/' . $bu->logo) }}" alt="Logo actuel" class="img-fluid" style="max-width: 150px; border-radius: var(--border-radius-md); border: 1px solid var(--gray-200);">
                                </div>
                            @endif
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('bu.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection