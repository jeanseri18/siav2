@extends('layouts.app')

@section('title', 'Ajouter une Sous-Catégorie')
@section('page-title', 'Ajouter une Sous-Catégorie')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sous_categories.index') }}">Sous-Catégories</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-sitemap me-2"></i>Créer une nouvelle sous-catégorie
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('sous_categories.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                        <i class="fas fa-font me-2"></i>Nom de la sous-catégorie
                    </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" required>
                    <div class="app-form-text">Nom de la sous-catégorie à créer</div>
                </div>
                
                <div class="app-form-group">
                    <label for="categorie_id" class="app-form-label">
                        <i class="fas fa-layer-group me-2"></i>Catégorie
                    </label>
                    <select name="categorie_id" id="categorie_id" class="app-form-select" required>
                        <option value="">-- Sélectionner une catégorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                        @endforeach
                    </select>
                    <div class="app-form-text">Catégorie parente de cette sous-catégorie</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('sous_categories.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection