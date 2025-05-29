@extends('layouts.app')

@section('title', 'Modifier un Type de Travaux')
@section('page-title', 'Modifier un Type de Travaux')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('type-travaux.index') }}">Types de Travaux</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Type de Travaux: {{ $type->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('type-travaux.update', $type->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-font me-2"></i>Nom du Type de Travaux
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $type->nom }}" required>
                    <div class="app-form-text">Nom descriptif du type de travaux</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('type-travaux.index') }}" class="app-btn app-btn-secondary">
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