@extends('layouts.app')

@section('title', 'Ajouter un Type de Travaux')
@section('page-title', 'Ajouter un Type de Travaux')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('type-travaux.index') }}">Types de Travaux</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-hammer me-2"></i>Ajouter un Type de Travaux
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('type-travaux.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-font me-2"></i>Nom du Type de Travaux
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" required>
                    <div class="app-form-text">Nom descriptif du type de travaux</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('type-travaux.index') }}" class="app-btn app-btn-secondary">
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