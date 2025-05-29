@extends('layouts.app')

@section('title', 'Modifier une Ville')
@section('page-title', 'Modifier une Ville')

@section('breadcrumb')
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier la Ville: {{ $ville->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('villes.update', $ville->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-font me-2"></i>Nom de la Ville
                            </label>
                            <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $ville->nom }}" required>
                            <div class="app-form-text">Nom complet de la ville</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_pays" class="app-form-label">
                                <i class="fas fa-globe me-2"></i>Pays
                            </label>
                            <select name="id_pays" id="id_pays" class="app-form-select" required>
                                @foreach($pays as $p)
                                    <option value="{{ $p->id }}" {{ $p->id == $ville->id_pays ? 'selected' : '' }}>{{ $p->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Pays auquel appartient cette ville</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('villes.index') }}" class="app-btn app-btn-secondary">
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