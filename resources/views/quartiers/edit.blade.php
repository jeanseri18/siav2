@extends('layouts.app')

@section('title', 'Modifier un Quartier')
@section('page-title', 'Modifier un Quartier')

@section('breadcrumb')
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Quartier: {{ $quartier->nom }}
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
            
            <form action="{{ route('quartiers.update', $quartier->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-font me-2"></i>Nom du Quartier
                            </label>
                            <input type="text" name="nom" id="nom" class="app-form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $quartier->nom) }}" required>
                            @error('nom')
                                <div class="app-form-text text-danger">{{ $message }}</div>
                            @else
                                <div class="app-form-text">Nom complet du quartier</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="code" class="app-form-label">
                                <i class="fas fa-hashtag me-2"></i>Code
                            </label>
                            <input type="text" name="code" id="code" class="app-form-control @error('code') is-invalid @enderror" value="{{ old('code', $quartier->code) }}">
                            @error('code')
                                <div class="app-form-text text-danger">{{ $message }}</div>
                            @else
                                <div class="app-form-text">Code du quartier (optionnel)</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="app-form-group">
                    <label for="commune_id" class="app-form-label">
                        <i class="fas fa-building me-2"></i>Commune
                    </label>
                    <select name="commune_id" id="commune_id" class="app-form-select @error('commune_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner une Commune --</option>
                        @foreach($communes as $commune)
                            <option value="{{ $commune->id }}" {{ (old('commune_id', $quartier->commune_id) == $commune->id) ? 'selected' : '' }}>
                                {{ $commune->nom }} ({{ $commune->ville->nom }})
                            </option>
                        @endforeach
                    </select>
                    @error('commune_id')
                        <div class="app-form-text text-danger">{{ $message }}</div>
                    @else
                        <div class="app-form-text">Commune à laquelle appartient ce quartier</div>
                    @enderror
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('quartiers.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
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