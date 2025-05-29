@extends('layouts.app')

@section('title', 'Modifier un Mode de Paiement')
@section('page-title', 'Modifier un Mode de Paiement')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('modes_de_paiement.index') }}">Modes de Paiement</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Mode de Paiement
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('modes_de_paiement.update', $modeDePaiement) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-money-check-alt me-2"></i>Nom
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $modeDePaiement->nom }}" required>
                    <div class="app-form-text">Nom du mode de paiement</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('modes_de_paiement.index') }}" class="app-btn app-btn-secondary">
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