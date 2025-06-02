@extends('layouts.app')

@section('title', 'Ajouter un Pays')
@section('page-title', 'Ajouter un Pays')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pays.index') }}">Pays</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-globe me-2"></i>Ajouter un Pays
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('pays.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-flag me-2"></i>Nom du Pays
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" required>
                    <div class="app-form-text">Nom complet du pays à ajouter</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('pays.index') }}" class="app-btn app-btn-secondary">
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