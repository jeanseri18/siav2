@extends('layouts.app')

@section('title', 'Modifier une Unité de Mesure')
@section('page-title', 'Modifier une Unité de Mesure')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('unite-mesures.index') }}">Unités de Mesure</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier l'Unité de Mesure: {{ $unite->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('unite-mesures.update', $unite->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-font me-2"></i>Nom de l'Unité
                            </label>
                            <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $unite->nom }}" required>
                            <div class="app-form-text">Nom complet de l'unité de mesure</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="ref" class="app-form-label">
                                <i class="fas fa-hashtag me-2"></i>Référence
                            </label>
                            <input type="text" name="ref" id="ref" class="app-form-control" value="{{ $unite->ref }}" required>
                            <div class="app-form-text">Abréviation ou symbole de l'unité (ex: kg, m, L)</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('unite-mesures.index') }}" class="app-btn app-btn-secondary">
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