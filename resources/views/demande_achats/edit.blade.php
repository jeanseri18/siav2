{{-- Page Edit - Modifier une Demande d'Achat --}}
@extends('layouts.app')

@section('title', 'Modifier une Demande d\'Achat')
@section('page-title', 'Modifier une Demande d\'Achat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-achats.index') }}">Demandes d'Achat</a></li>
<li class="breadcrumb-item"><a href="{{ route('demande-achats.show', $demandeAchat) }}">{{ $demandeAchat->reference }}</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-edit me-2"></i>Modifier la Demande d'Achat
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('demande-achats.show', $demandeAchat) }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('demande-achats.update', $demandeAchat) }}" method="POST" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_demande" class="app-form-label">
                                        <i class="fas fa-calendar me-2"></i>Date de la demande <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="app-form-control @error('date_demande') is-invalid @enderror" 
                                        id="date_demande" name="date_demande" 
                                        value="{{ old('date_demande', $demandeAchat->date_demande->format('Y-m-d')) }}" required>
                                    @error('date_demande')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_besoin" class="app-form-label">
                                        <i class="fas fa-clock me-2"></i>Date de besoin
                                    </label>
                                    <input type="date" class="app-form-control @error('date_besoin') is-invalid @enderror" 
                                        id="date_besoin" name="date_besoin" 
                                        value="{{ old('date_besoin', $demandeAchat->date_besoin ? $demandeAchat->date_besoin->format('Y-m-d') : '') }}">
                                    @error('date_besoin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="priorite" class="app-form-label">
                                        <i class="fas fa-exclamation-circle me-2"></i>Priorité <span class="text-danger">*</span>
                                    </label>
                                    <select class="app-form-select @error('priorite') is-invalid @enderror" 
                                        id="priorite" name="priorite" required>
                                        <option value="basse" {{ old('priorite', $demandeAchat->priorite) == 'basse' ? 'selected' : '' }}>Basse</option>
                                        <option value="normale" {{ old('priorite', $demandeAchat->priorite) == 'normale' ? 'selected' : '' }}>Normale</option>
                                        <option value="haute" {{ old('priorite', $demandeAchat->priorite) == 'haute' ? 'selected' : '' }}>Haute</option>
                                        <option value="urgente" {{ old('priorite', $demandeAchat->priorite) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                    @error('priorite')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="projet_id" class="app-form-label">
                                        <i class="fas fa-project-diagram me-2"></i>Projet
                                    </label>
                                    <select class="app-form-select @error('projet_id') is-invalid @enderror" 
                                        id="projet_id" name="projet_id">
                                        <option value="">Sélectionner un projet</option>
                                        @foreach($projets as $projet)
                                            <option value="{{ $projet->id }}" 
                                                {{ old('projet_id', $demandeAchat->projet_id) == $projet->id ? 'selected' : '' }}>
                                                {{ $projet->nom_projet }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('projet_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="description" class="app-form-label">
                                        <i class="fas fa-align-left me-2"></i>Description
                                    </label>
                                    <textarea class="app-form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" rows="2">{{ old('description', $demandeAchat->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-card app-mt-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-boxes me-2"></i>Articles
                                </h3>
                            </div>
                            
                            <div class="app-card-body app-table-responsive">
                                <table class="app-table" id="articles_table">
                                    <thead>
                                        <tr>
                                            <th>N° ou Réf Article</th>
                                            <th>Désignation <span class="text-danger">*</span></th>
                                            <th>Qté <span class="text-danger">*</span></th>
                                            <th>Unité</th>
                                            <th>Spécification</th>
                                            <th style="width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($demandeAchat->lignes as $ligne)
                                        <tr>
                                            <td>
                                                <select class="app-form-select article-select" name="article_id[]" onchange="fillArticleInfo(this)">
                                                    <option value="">Sélectionner un article</option>
                                                    @foreach($articles as $article)
                                                        <option value="{{ $article->id }}" 
                                                            data-unite="{{ $article->uniteMesure ? $article->uniteMesure->ref : 'Unité' }}" 
                                                            data-designation="{{ $article->nom }}"
                                                            data-reference="{{ $article->reference }}"
                                                            {{ $ligne->article_id == $article->id ? 'selected' : '' }}>
                                                            {{ $article->reference }} - {{ $article->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="app-d-flex app-align-items-center app-gap-2">
                                                    <div class="item-icon">
                                                        <i class="fas fa-box text-primary"></i>
                                                    </div>
                                                    <input type="text" class="app-form-control designation" 
                                                        name="designation[]" value="{{ $ligne->designation }}" required style="border: none; background: transparent;">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="app-form-control" 
                                                    name="quantite[]" min="1" value="{{ $ligne->quantite }}" required style="width: 80px;">
                                            </td>
                                            <td>
                                                <span class="app-badge app-badge-info unite-mesure">{{ $ligne->unite_mesure }}</span>
                                                <input type="hidden" class="unite-mesure-input" name="unite_mesure[]" value="{{ $ligne->unite_mesure }}">
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control" 
                                                    name="specifications[]" value="{{ $ligne->specifications }}" placeholder="Spécifications...">
                                                <input type="hidden" name="prix_estime[]" value="{{ $ligne->prix_estime ?? 0 }}">
                                                <input type="hidden" name="commentaire[]" value="{{ $ligne->commentaire ?? '' }}">
                                            </td>
                                            <td>
                                                <button type="button" class="app-btn app-btn-danger app-btn-sm app-btn-icon" onclick="removeLine(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="app-card-footer">
                                <button type="button" class="app-btn app-btn-info app-btn-icon" onclick="addArticle()">
                                    <i class="fas fa-plus"></i> Ajouter une ligne
                                </button>
                            </div>
                        </div>
                        
                        <div class="app-card-footer app-mt-4 app-text-center">
                            <button type="submit" class="app-btn app-btn-primary app-btn-lg">
                                <i class="fas fa-save me-2"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template pour nouvelles lignes -->
<template id="article-row-template">
    <tr>
        <td>
            <select class="app-form-select article-select" name="article_id[]" onchange="fillArticleInfo(this)">
                <option value="">Sélectionner un article</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}" 
                        data-unite="{{ $article->uniteMesure ? $article->uniteMesure->ref : 'Unité' }}" 
                        data-designation="{{ $article->nom }}"
                        data-reference="{{ $article->reference }}">
                        {{ $article->reference }} - {{ $article->nom }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <div class="app-d-flex app-align-items-center app-gap-2">
                <div class="item-icon">
                    <i class="fas fa-box text-primary"></i>
                </div>
                <input type="text" class="app-form-control designation" 
                    name="designation[]" required style="border: none; background: transparent;">
            </div>
        </td>
        <td>
            <input type="number" class="app-form-control" 
                name="quantite[]" min="1" value="1" required style="width: 80px;">
        </td>
        <td>
            <span class="app-badge app-badge-info unite-mesure">Unité</span>
            <input type="hidden" class="unite-mesure-input" name="unite_mesure[]" value="Unité">
        </td>
        <td>
            <input type="text" class="app-form-control" 
                name="specifications[]" placeholder="Spécifications...">
            <input type="hidden" name="prix_estime[]" value="0">
            <input type="hidden" name="commentaire[]" value="">
        </td>
        <td>
            <button type="button" class="app-btn app-btn-danger app-btn-sm app-btn-icon" onclick="removeLine(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

@endsection

@section('styles')
<style>
/* Styles pour le tableau des articles */
.item-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
}

.designation {
    flex: 1;
}

.app-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-weight: 500;
}

.app-badge-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

#articles-table tbody tr td {
    vertical-align: middle;
}
</style>
@endsection

@section('scripts')
<script>
// Remplir automatiquement les informations d'un article
function fillArticleInfo(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const designation = selectedOption.getAttribute('data-designation');
    const unite = selectedOption.getAttribute('data-unite');
    const row = selectElement.closest('tr');
    
    if (designation) {
        row.querySelector('.designation').value = designation;
    }
    
    if (unite) {
        const uniteBadge = row.querySelector('.unite-mesure');
        const uniteInput = row.querySelector('.unite-mesure-input');
        if (uniteBadge) uniteBadge.textContent = unite;
        if (uniteInput) uniteInput.value = unite;
    }
}

// Ajouter une ligne
function addArticle() {
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    
    document.querySelector('#articles_table tbody').appendChild(clone);
    
    // Initialiser Select2 si disponible
    if (typeof $.fn.select2 !== 'undefined') {
        $('#articles_table tbody tr:last-child .article-select').select2();
    }
}

// Supprimer une ligne
function removeLine(button) {
    const tbody = document.querySelector('#articles_table tbody');
    const rowCount = tbody.querySelectorAll('tr').length;
    
    if (rowCount > 1) {
        button.closest('tr').remove();
    } else {
        alert('Vous devez avoir au moins une ligne d\'article');
    }
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
});

// Support jQuery si disponible
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
});
</script>
@endsection