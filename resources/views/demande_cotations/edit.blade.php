{{-- Page Edit - Modifier une Demande de Cotation --}}
@extends('layouts.app')

@section('title', 'Modifier une Demande de Cotation')
@section('page-title', 'Modifier une Demande de Cotation')

@section('styles')
<style>
    .article-row {
        border-bottom: 1px solid #e9ecef;
    }
    
    .article-row:last-child {
        border-bottom: none;
    }
    
    .article-row td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }
    
    .article-row input,
    .article-row select {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .article-row input:focus,
    .article-row select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(3, 61, 113, 0.25);
    }
    
    .badge-unite {
        background-color: var(--primary);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Styles personnalisés pour Select2 */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        min-height: 38px;
    }
    
    .select2-container--default .select2-selection--multiple:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(3, 61, 113, 0.25);
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary);
        border: 1px solid var(--primary);
        color: white;
        border-radius: 0.25rem;
        padding: 2px 8px;
        margin: 2px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ffcccc;
    }
    
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary);
    }
</style>
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-cotations.index') }}">Demandes de Cotation</a></li>
<li class="breadcrumb-item"><a href="{{ route('demande-cotations.show', $demandeCotation) }}">{{ $demandeCotation->reference }}</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-edit me-2"></i>Modifier la Demande de Cotation
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('demande-cotations.show', $demandeCotation) }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('demande-cotations.update', $demandeCotation) }}" method="POST" class="app-form">
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
                                        value="{{ old('date_demande', $demandeCotation->date_demande->format('Y-m-d')) }}" required>
                                    @error('date_demande')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="app-form-text">Date à laquelle la demande est créée</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_expiration" class="app-form-label">
                                        <i class="fas fa-calendar-times me-2"></i>Date d'expiration <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="app-form-control @error('date_expiration') is-invalid @enderror" 
                                        id="date_expiration" name="date_expiration" 
                                        value="{{ old('date_expiration', $demandeCotation->date_expiration->format('Y-m-d')) }}" required>
                                    @error('date_expiration')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="app-form-text">Date limite pour recevoir les cotations</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="demande_achat_id" class="app-form-label">
                                        <i class="fas fa-shopping-cart me-2"></i>Demande d'achat
                                    </label>
                                    <select class="app-form-select @error('demande_achat_id') is-invalid @enderror" 
                                        id="demande_achat_id" name="demande_achat_id">
                                        <option value="">-- Sélectionner une demande --</option>
                                        @foreach($demandesAchat as $demande)
                                            <option value="{{ $demande->id }}" 
                                                {{ old('demande_achat_id', $demandeCotation->demande_achat_id) == $demande->id ? 'selected' : '' }}>
                                                {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('demande_achat_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="app-form-text">Demande d'achat liée (optionnel)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="description" class="app-form-label">
                                <i class="fas fa-align-left me-2"></i>Description
                            </label>
                            <textarea class="app-form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="2" placeholder="Description de la demande...">{{ old('description', $demandeCotation->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="app-form-text">Description ou contexte de la demande de cotation</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="conditions_generales" class="app-form-label">
                                <i class="fas fa-clipboard-list me-2"></i>Spécifications particulières
                            </label>
                            <textarea class="app-form-control @error('conditions_generales') is-invalid @enderror" 
                                id="conditions_generales" name="conditions_generales" rows="3" placeholder="Spécifications particulières...">{{ old('conditions_generales', $demandeCotation->conditions_generales) }}</textarea>
                            @error('conditions_generales')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="app-form-text">Spécifications particulières à communiquer au fournisseur</div>
                        </div>
                        
                        <div class="app-card mt-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-truck me-2"></i>Fournisseurs <span class="text-danger">*</span>
                                </h3>
                            </div>
                            
                            <div class="app-card-body">
                                <select class="app-form-select select2-multiple @error('fournisseur_id') is-invalid @enderror" 
                                    id="fournisseur_id" name="fournisseur_id[]" multiple required>
                                    @foreach($fournisseurs as $fournisseur)
                                        @php
                                            $isSelected = false;
                                            if (old('fournisseur_id')) {
                                                $isSelected = in_array($fournisseur->id, old('fournisseur_id'));
                                            } else {
                                                $isSelected = $demandeCotation->fournisseurs->contains('fournisseur_id', $fournisseur->id);
                                            }
                                        @endphp
                                        <option value="{{ $fournisseur->id }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $fournisseur->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="app-form-text mt-2">Sélectionner un ou plusieurs fournisseurs à consulter</div>
                            </div>
                        </div>
                        
                        <div class="app-card mt-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-box me-2"></i>Articles
                                </h3>
                                <div class="app-card-actions">
                                    <button type="button" class="app-btn app-btn-success app-btn-sm" onclick="addArticle()">
                                        <i class="fas fa-plus me-2"></i> Ajouter une ligne
                                    </button>
                                </div>
                            </div>
                            
                            <div class="app-card-body app-table-responsive">
                                <table class="app-table" id="articles_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">N° ou Ref Article</th>
                                            <th style="width: 30%;">Désignation <span class="text-danger">*</span></th>
                                            <th style="width: 15%;">Qté <span class="text-danger">*</span></th>
                                            <th style="width: 15%;">Unité <span class="text-danger">*</span></th>
                                            <th style="width: 15%;">Spécification</th>
                                            <th style="width: 5%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($demandeCotation->lignes as $ligne)
                                        <tr>
                                            <td>
                                                <select class="app-form-select article-select" name="article_id[]" onchange="fillArticleInfo(this)">
                                                    <option value="">-- Sélectionner un article --</option>
                                                    @foreach($articles as $article)
                                                        <option value="{{ $article->id }}" data-unite="{{ $article->unite_mesure }}" 
                                                            data-designation="{{ $article->nom }}" data-reference="{{ $article->reference }}"
                                                            {{ $ligne->article_id == $article->id ? 'selected' : '' }}>
                                                            {{ $article->reference }} - {{ $article->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" class="article-reference" name="article_reference[]">
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control designation" 
                                                    name="designation[]" value="{{ $ligne->designation }}" required placeholder="Désignation...">
                                            </td>
                                            <td>
                                                <input type="number" class="app-form-control" 
                                                    name="quantite[]" min="1" value="{{ $ligne->quantite }}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control unite-mesure" 
                                                    name="unite_mesure[]" value="{{ $ligne->unite_mesure }}" required placeholder="Unité...">
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control" 
                                                    name="specifications[]" value="{{ $ligne->specifications }}" placeholder="Spécifications...">
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
                        </div>
                        
                        <div class="app-card-footer mt-4">
                            <a href="{{ route('demande-cotations.show', $demandeCotation) }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
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
                <option value="">-- Sélectionner un article --</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}" data-unite="{{ $article->unite_mesure }}" 
                        data-designation="{{ $article->nom }}" data-reference="{{ $article->reference }}">
                        {{ $article->reference }} - {{ $article->nom }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" class="article-reference" name="article_reference[]">
        </td>
        <td>
            <input type="text" class="app-form-control designation" 
                name="designation[]" required placeholder="Désignation...">
        </td>
        <td>
            <input type="number" class="app-form-control" 
                name="quantite[]" min="1" value="1" required>
        </td>
        <td>
            <input type="text" class="app-form-control unite-mesure" 
                name="unite_mesure[]" value="Unité" required placeholder="Unité...">
        </td>
        <td>
            <input type="text" class="app-form-control" 
                name="specifications[]" placeholder="Spécifications...">
        </td>
        <td>
            <button type="button" class="app-btn app-btn-danger app-btn-sm app-btn-icon" onclick="removeLine(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
// Remplir automatiquement les informations d'un article
function fillArticleInfo(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const row = selectElement.closest('tr');
    
    if (selectedOption.value) {
        const designation = selectedOption.getAttribute('data-designation');
        const unite = selectedOption.getAttribute('data-unite');
        const reference = selectedOption.getAttribute('data-reference');
        
        row.querySelector('.designation').value = designation || '';
        row.querySelector('.unite-mesure').value = unite || '';
        row.querySelector('.article-reference').value = reference || '';
    } else {
        row.querySelector('.designation').value = '';
        row.querySelector('.unite-mesure').value = '';
        row.querySelector('.article-reference').value = '';
    }
}

// Ajouter une ligne
function addArticle() {
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    
    document.querySelector('#articles_table tbody').appendChild(clone);
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
    // Initialiser Select2 pour la sélection multiple des fournisseurs
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-multiple').select2({
            placeholder: "Sélectionner un ou plusieurs fournisseurs"
        });
        
        // Les sélecteurs d'articles utilisent l'affichage HTML natif
    }
});

// Support jQuery si disponible
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-multiple').select2({
            placeholder: "Sélectionner un ou plusieurs fournisseurs"
        });
        // Les sélecteurs d'articles utilisent l'affichage HTML natif
    }
});
</script>
@endpush
@endsection