@extends('layouts.app')

@section('title', 'Modifier un Régime d\'Imposition')
@section('page-title', 'Modifier un Régime d\'Imposition')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('regime-impositions.index') }}">Régimes d'Imposition</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Régime d'Imposition: {{ $regime->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('regime-impositions.update', $regime->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-font me-2"></i>Nom du Régime
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" value="{{ $regime->nom }}" required>
                    <div class="app-form-text">Nom complet du régime d'imposition</div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="ref" class="app-form-label">
                                <i class="fas fa-hashtag me-2"></i>Référence
                            </label>
                            <input type="text" name="ref" id="ref" class="app-form-control" value="{{ $regime->ref }}" required>
                            <div class="app-form-text">Code de référence du régime</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="tva" class="app-form-label">
                                <i class="fas fa-percent me-2"></i>TVA
                            </label>
                            <input type="text" name="tva" id="tva" class="app-form-control" value="{{ $regime->tva }}" required>
                            <div class="app-form-text">Taux de TVA applicable</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('regime-impositions.index') }}" class="app-btn app-btn-secondary">
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