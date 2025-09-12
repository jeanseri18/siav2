@extends('layouts.app')

@section('title', 'Détails de l\'Article')
@section('page-title', 'Détails de l\'Article')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_article') }}">Stock</a></li>
<li class="breadcrumb-item"><a href="{{ route('articles.index') }}">Articles</a></li>
<li class="breadcrumb-item active">{{ $article->nom }}</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-box me-2"></i>Détails de l'Article
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('articles.edit', $article->id) }}" class="app-btn app-btn-warning app-btn-icon">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                        <a href="{{ route('articles.index') }}" class="app-btn app-btn-secondary app-btn-icon">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-tag me-2"></i>Référence
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $article->reference ?? 'Non définie' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-barcode me-2"></i>Référence Fournisseur
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $article->reference_fournisseur ?? 'Non définie' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-cube me-2"></i>Nom de l'Article
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $article->nom }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-layer-group me-2"></i>Type
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $article->type ?? 'Non défini' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-ruler me-2"></i>Unité de Mesure
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $article->uniteMesure ? $article->uniteMesure->ref : 'Non définie' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-warehouse me-2"></i>Quantité en Stock
                                </label>
                                <div class="app-form-control bg-light">
                                    <span class="badge {{ $article->quantite_stock > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                        {{ number_format($article->quantite_stock, 0, ',', ' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-money-bill-wave me-2"></i>Prix Unitaire
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ number_format($article->prix_unitaire, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-calculator me-2"></i>Coût Moyen Pondéré
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ number_format($article->cout_moyen_pondere ?? 0, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informations de classification -->
        <div class="col-md-4">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-sitemap me-2"></i>Classification
                    </h3>
                </div>
                
                <div class="app-card-body">
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-folder me-2"></i>Catégorie
                        </label>
                        <div class="app-form-control bg-light">
                            {{ $article->categorie ? $article->categorie->nom : 'Non définie' }}
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-folder-open me-2"></i>Sous-Catégorie
                        </label>
                        <div class="app-form-control bg-light">
                            {{ $article->sousCategorie ? $article->sousCategorie->nom : 'Non définie' }}
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-truck me-2"></i>Fournisseur
                        </label>
                        <div class="app-form-control bg-light">
{{ $article->fournisseur ? $article->fournisseur->nom_raison_sociale . ' - ' . $article->fournisseur->prenoms : 'Non défini' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informations système -->
            <div class="app-card mt-3">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-info-circle me-2"></i>Informations Système
                    </h3>
                </div>
                
                <div class="app-card-body">
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-calendar-plus me-2"></i>Date de création
                        </label>
                        <div class="app-form-control bg-light">
                            {{ $article->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Dernière modification
                        </label>
                        <div class="app-form-control bg-light">
                            {{ $article->updated_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
