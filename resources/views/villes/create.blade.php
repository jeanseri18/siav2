@extends('layouts.app')

@section('title', 'Ajouter une Ville')
@section('page-title', 'Ajouter une Ville')

@section('breadcrumb')
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-city me-2"></i>Ajouter une Ville
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
                        <ul class="app-m-0 app-p-0" style="list-style-type: none;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <form action="{{ route('villes.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-font me-2"></i>Nom de la Ville
                            </label>
                            <input type="text" name="nom" id="nom" class="app-form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="app-form-text text-danger">{{ $message }}</div>
                            @else
                                <div class="app-form-text">Nom complet de la ville</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="coef_eloignement" class="app-form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Coefficient éloignement
                            </label>
                            <input type="text" name="coef_eloignement" id="coef_eloignement" class="app-form-control @error('coef_eloignement') is-invalid @enderror" value="{{ old('coef_eloignement') }}" required>
                            @error('coef_eloignement')
                                <div class="app-form-text text-danger">{{ $message }}</div>
                            @else
                                <div class="app-form-text">Coefficient d'éloignement de la ville</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="app-form-group">
                    <label for="pays_id" class="app-form-label">
                        <i class="fas fa-globe me-2"></i>Pays
                    </label>
                    <select name="pays_id" id="pays_id" class="app-form-select @error('pays_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner un Pays --</option>
                        @foreach($pays as $p)
                            <option value="{{ $p->id }}" {{ old('pays_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('pays_id')
                        <div class="app-form-text text-danger">{{ $message }}</div>
                    @else
                        <div class="app-form-text">Pays auquel appartient cette ville</div>
                    @enderror
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('villes.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection