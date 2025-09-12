@extends('layouts.app')

@section('title', 'Ajouter un Article')
@section('page-title', 'Ajouter un Article')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_article') }}">Articles</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-box-open me-2"></i>Ajouter un Article
            </h2>
        </div>
        
        <div class="app-card-body">
            @if ($errors->any())
            <div class="app-alert app-alert-danger">
                <div class="app-alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="app-alert-content">
                    <div class="app-alert-text">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <form action="{{ route('articles.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="categorie_id" class="app-form-label">
                                <i class="fas fa-layer-group me-2"></i>Catégorie
                            </label>
                            <select name="categorie_id" id="categorie_id" class="app-form-select" required>
                                <option value="">-- Sélectionner une catégorie --</option>
                                @foreach ($categories as $categorie)
                                    <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Catégorie principale de l'article</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="sous_categorie_id" class="app-form-label">
                                <i class="fas fa-sitemap me-2"></i>Sous-catégorie
                            </label>
                            <select name="sous_categorie_id" id="sous_categorie_id" class="app-form-select">
                                <option value="">Aucune</option>
                                @foreach ($sousCategories as $sousCategorie)
                                    <option value="{{ $sousCategorie->id }}">{{ $sousCategorie->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Sous-catégorie optionnelle</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                    <div class="app-form-group">
                        <label for="reference_fournisseur" class="app-form-label">
                            <i class="fas fa-building me-2"></i>Fournisseur
                        </label>
                        <select name="reference_fournisseur" id="reference_fournisseur" class="app-form-select">
                            <option value="">-- Sélectionner un fournisseur --</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ old('reference_fournisseur') == $fournisseur->id ? 'selected' : '' }}>
                                    {{ $fournisseur->nom_raison_sociale }}                                     {{ $fournisseur->prenoms }}
                                </option>
                            @endforeach
                        </select>
                        <div class="app-form-text">Fournisseur de l'article (optionnel)</div>
                    </div>
                </div>
                
                <div class="app-form-col">
                    <div class="app-form-group">
                        <label for="type" class="app-form-label">
                            <i class="fas fa-tag me-2"></i>Type
                        </label>
                        <select name="type" id="type" class="app-form-select">
                            <option value="">-- Sélectionner un type --</option>
                            <option value="Matériau" {{ old('type') == 'Matériau' ? 'selected' : '' }}>Matériau</option>
                            <option value="Outil" {{ old('type') == 'Outil' ? 'selected' : '' }}>Outil</option>
                            <option value="Matériel" {{ old('type') == 'Matériel' ? 'selected' : '' }}>Matériel</option>
                        </select>
                        <div class="app-form-text">Type d'article (optionnel)</div>
                    </div>
                </div>
                </div>

                <div class="app-form-group">
                    <label for="nom" class="app-form-label">
                                <i class="fas fa-font me-2"></i>Désignation
                            </label>
                    <input type="text" name="nom" id="nom" class="app-form-control" required>
                    <div class="app-form-text">Nom complet ou description de l'article</div>
                </div>
                
                <div class="app-form-group">
                    <label for="unite_mesure" class="app-form-label">
                        <i class="fas fa-ruler me-2"></i>Unité de mesure
                    </label>
                    <select name="unite_mesure" id="unite_mesure" class="app-form-select">
                        <option value="">Sélectionner une unité</option>
                        @foreach ($uniteMesures as $uniteMesure)
                            <option value="{{ $uniteMesure->id }}">{{ $uniteMesure->nom }}</option>
                        @endforeach
                    </select>
                    <div class="app-form-text">Unité de mesure de l'article (ex: kg, m, L)</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('articles.index') }}" class="app-btn app-btn-secondary">
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