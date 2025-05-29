@extends('layouts.app')

@section('title', 'Ajouter un Mode de Paiement')
@section('page-title', 'Ajouter un Mode de Paiement')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('modes_de_paiement.index') }}">Modes de Paiement</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-plus-circle me-2"></i>Ajouter un Mode de Paiement
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('modes_de_paiement.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-money-check-alt me-2"></i>Nom
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" required>
                    <div class="app-form-text">Nom du mode de paiement à ajouter</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('modes_de_paiement.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
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