@extends('layouts.app')

@section('title', 'Modifier un Pays')
@section('page-title', 'Modifier un Pays')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pays.index') }}">Pays</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Pays: {{ $pays->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('pays.update', $pays->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-flag me-2"></i>Nom du Pays
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $pays->nom }}" required>
                    <div class="app-form-text">Nom complet du pays</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('pays.index') }}" class="app-btn app-btn-secondary">
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