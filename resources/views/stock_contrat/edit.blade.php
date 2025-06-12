@extends('layouts.app')

@section('title', 'Modifier un Produit du Stock')
@section('page-title', 'Modifier un Produit du Stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item"><a href="{{ route('stock_contrat.index') }}">Stock</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Produit: {{ $stock->article->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('stock_contrat.update', $stock->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="article_id" class="app-form-label">
                        <i class="fas fa-box me-2"></i>Sélectionner un article
                    </label>
                    <select name="article_id" id="article_id" class="app-form-select" required>
                        <option value="">-- Sélectionnez un article --</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" {{ $article->id == $stock->article_id ? 'selected' : '' }}>
                                {{ $article->nom }} - {{ $article->reference }}
                            </option>
                        @endforeach
                    </select>
                    <div class="app-form-text">Article dans le stock</div>
                </div>
                
                <div class="app-form-group">
                    <label for="quantite" class="app-form-label">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                        </label>
                    <input type="number" name="quantite" id="quantite" class="app-form-control" value="{{ $stock->quantite }}" min="0" required>
                    <div class="app-form-text">Quantité en stock</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('stock_contrat.index') }}" class="app-btn app-btn-secondary">
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