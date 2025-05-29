{{-- Page Create - Nouvelle Demande de Cotation --}}
@extends('layouts.app')

@section('title', 'Nouvelle Demande de Cotation')
@section('page-title', 'Nouvelle Demande de Cotation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-cotations.index') }}">Demandes de Cotation</a></li>
<li class="breadcrumb-item active">Nouvelle</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-invoice me-2"></i>Nouvelle Demande de Cotation
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('demande-cotations.index') }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('demande-cotations.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_demande" class="app-form-label">
                                        <i class="fas fa-calendar me-2"></i>Date de la demande <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="app-form-control @error('date_demande') is-invalid @enderror" 
                                        id="date_demande" name="date_demande" value="{{ old('date_demande', date('Y-m-d')) }}" required>
                                    @error('date_demande')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_expiration" class="app-form-label">
                                        <i class="fas fa-calendar-times me-2"></i>Date d'expiration <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="app-form-control @error('date_expiration') is-invalid @enderror" 
                                        id="date_expiration" name="date_expiration" 
                                        value="{{ old('date_expiration', date('Y-m-d', strtotime('+15 days'))) }}" required>
                                    @error('date_expiration')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="demande_achat_id" class="app-form-label">
                                        <i class="fas fa-shopping-cart me-2"></i>Demande d'achat
                                    </label>
                                    <select class="app-form-select @error('demande_achat_id') is-invalid @enderror" 
                                        id="demande_achat_id" name="demande_achat_id">
                                        <option value="">Sélectionner une demande</option>
                                        @foreach($demandesAchat as $demande)
                                            <option value="{{ $demande->id }}" {{ old('demande_achat_id') == $demande->id ? 'selected' : '' }}>
                                                {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('demande_achat_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <div class="app-form-text">Sélectionner une demande d'achat existante (optionnel)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="description" class="app-form-label">
                                <i class="fas fa-align-left me-2"></i>Description
                            </label>
                            <textarea class="app-form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="app-form-text">Description ou contexte de la demande de cotation</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="conditions_generales" class="app-form-label">
                                <i class="fas fa-gavel me-2"></i>Conditions générales
                            </label>
                            <textarea class="app-form-control @error('conditions_generales') is-invalid @enderror" 
                                id="conditions_generales" name="conditions_generales" rows="3">{{ old('conditions_generales') }}</textarea>
                            @error('conditions_generales')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="app-form-text">Conditions générales à communiquer aux fournisseurs</div>
                        </div>
                        
                        <div class="app-card app-mt-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-truck me-2"></i>Fournisseurs <span class="text-danger">*</span>
                                </h3>
                            </div>
                            
                            <div class="app-card-body">
                                <select class="app-form-select select2-multiple @error('fournisseur_id') is-invalid @enderror" 
                                    id="fournisseur_id" name="fournisseur_id[]" multiple required>
                                    @foreach($fournisseurs as $fournisseur)
                                        <option value="{{ $fournisseur->id }}" 
                                            {{ (old('fournisseur_id') && in_array($fournisseur->id, old('fournisseur_id'))) ? 'selected' : '' }}>
                                            {{ $fournisseur->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="app-form-text app-mt-2">Sélectionner un ou plusieurs fournisseurs à consulter</div>
                            </div>
                        </div>
                        
                        <div class="app-card app-mt-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-box me-2"></i>Articles
                                </h3>
                            </div>
                            
                            <div class="app-card-body app-table-responsive">
                                <table class="app-table" id="articles_table">
                                    <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Désignation <span class="text-danger">*</span></th>
                                            <th>Quantité <span class="text-danger">*</span></th>
                                            <th>Unité <span class="text-danger">*</span></th>
                                            <th>Spécifications</th>
                                            <th style="width: 80px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="app-form-select article-select" name="article_id[]" onchange="fillArticleInfo(this)">
                                                    <option value="">Sélectionner un article</option>
                                                    @foreach($articles as $article)
                                                        <option value="{{ $article->id }}" data-unite="{{ $article->unite_mesure }}" 
                                                            data-designation="{{ $article->nom }}">
                                                            {{ $article->code }} - {{ $article->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control designation" 
                                                    name="designation[]" required>
                                            </td>
                                            <td>
                                                <input type="number" class="app-form-control" 
                                                    name="quantite[]" min="1" value="1" required>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control unite-mesure" 
                                                    name="unite_mesure[]" value="Unité" required>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control" 
                                                    name="specifications[]">
                                            </td>
                                            <td>
                                                <button type="button" class="app-btn app-btn-danger app-btn-sm app-btn-icon" onclick="removeLine(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="app-card-footer">
                                <button type="button" class="app-btn app-btn-info app-btn-icon" onclick="addArticle()">
                                    <i class="fas fa-plus me-2"></i> Ajouter une ligne
                                </button>
                            </div>
                        </div>
                        
                        <div class="app-card-footer app-mt-4 app-text-center">
                            <button type="submit" class="app-btn app-btn-primary app-btn-lg">
                                <i class="fas fa-save me-2"></i> Enregistrer
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
                    <option value="{{ $article->id }}" data-unite="{{ $article->unite_mesure }}" 
                        data-designation="{{ $article->nom }}">
                        {{ $article->code }} - {{ $article->nom }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="app-form-control designation" 
                name="designation[]" required>
        </td>
        <td>
            <input type="number" class="app-form-control" 
                name="quantite[]" min="1" value="1" required>
        </td>
        <td>
            <input type="text" class="app-form-control unite-mesure" 
                name="unite_mesure[]" value="Unité" required>
        </td>
        <td>
            <input type="text" class="app-form-control" 
                name="specifications[]">
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
    const designation = selectedOption.getAttribute('data-designation');
    const unite = selectedOption.getAttribute('data-unite');
    const row = selectElement.closest('tr');
    
    if (designation) {
        row.querySelector('.designation').value = designation;
    }
    
    if (unite) {
        row.querySelector('.unite-mesure').value = unite;
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

// Charger les articles d'une demande d'achat
function chargerArticlesDemandeAchat(demandeId) {
    if (!demandeId) return;
    
    fetch(`/api/demandes-achat/${demandeId}/articles`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Vider le tableau
                document.querySelector('#articles_table tbody').innerHTML = '';
                
                // Ajouter les articles
                data.forEach(item => {
                    const template = document.getElementById('article-row-template');
                    const clone = template.content.cloneNode(true);
                    
                    // Sélectionner l'article
                    const select = clone.querySelector('.article-select');
                    if (item.article_id) {
                        select.value = item.article_id;
                    }
                    
                    // Remplir les champs
                    clone.querySelector('.designation').value = item.designation || '';
                    clone.querySelector('input[name="quantite[]"]').value = item.quantite || 1;
                    clone.querySelector('.unite-mesure').value = item.unite_mesure || 'Unité';
                    clone.querySelector('input[name="specifications[]"]').value = item.specifications || '';
                    
                    document.querySelector('#articles_table tbody').appendChild(clone);
                });
                
                // Initialiser Select2
                if (typeof $.fn.select2 !== 'undefined') {
                    $('.article-select').select2();
                }
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser Select2 pour la sélection multiple
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-multiple').select2({
            placeholder: "Sélectionner un ou plusieurs fournisseurs"
        });
        
        // Initialiser Select2 pour les articles
        $('.article-select').select2();
    }
    
    // Événement pour charger les articles d'une demande d'achat
    document.getElementById('demande_achat_id').addEventListener('change', function() {
        chargerArticlesDemandeAchat(this.value);
    });
});

// Support jQuery si disponible
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-multiple').select2({
            placeholder: "Sélectionner un ou plusieurs fournisseurs"
        });
        $('.article-select').select2();
    }
});
</script>
@endpush
@endsection