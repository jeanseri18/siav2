@extends('layouts.app')

@section('title', 'Ajouter une Référence')
@section('page-title', 'Ajouter une Référence')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('references.index') }}">Références</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-bookmark me-2"></i>Ajouter une Référence
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('references.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-font me-2"></i>Nom
                            </label>
                            <input type="text" name="nom" id="nom" class="app-form-control" required>
                            <div class="app-form-text">Nom de la référence</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="ref" class="app-form-label">
                                <i class="fas fa-hashtag me-2"></i>Référence
                            </label>
                            <input type="text" name="ref" id="ref" class="app-form-control" required>
                            <div class="app-form-text">Code de référence unique</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('references.index') }}" class="app-btn app-btn-secondary">
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