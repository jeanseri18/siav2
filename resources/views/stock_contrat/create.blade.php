@extends('layouts.app')

@section('title', 'Ajouter un Article au Stock')
@section('page-title', 'Ajouter un Article au Stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item"><a href="{{ route('stock_contrat.index') }}">Stock</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-plus-circle me-2"></i>Ajouter un Article au Stock
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('stock_contrat.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-group">
                    <label for="article_id" class="app-form-label">
                        <i class="fas fa-box me-2"></i>Sélectionner un article
                    </label>
                    <select name="article_id" id="article_id" class="app-form-select" required>
                        <option value="">-- Sélectionnez un article --</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}">{{ $article->nom }} - {{ $article->reference }}</option>
                        @endforeach
                    </select>
                    <div class="app-form-text">Article à ajouter au stock</div>
                </div>
                
                <div class="app-form-group">
                    <label for="quantite" class="app-form-label">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                        </label>
                    <input type="number" name="quantite" id="quantite" class="app-form-control" min="1" required>
                    <div class="app-form-text">Quantité à ajouter au stock</div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('stock_contrat.index') }}" class="app-btn app-btn-secondary">
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