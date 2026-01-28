{{-- Page Create - Nouvelle Demande d'Achat --}}
@extends('layouts.app')

@section('title', 'Nouvelle Demande d\'Achat')
@section('page-title', 'Nouvelle Demande d\'Achat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-achats.index') }}">Demandes d'Achat</a></li>
<li class="breadcrumb-item active">Nouvelle</li>
@endsection

@section('content')
<div class="container app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-shopping-cart me-2"></i>Nouvelle Demande d'Achat
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('demande-achats.index') }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('demande-achats.store') }}" method="POST" class="app-form">
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
                                    <label for="date_besoin" class="app-form-label">
                                        <i class="fas fa-clock me-2"></i>Date de besoin
                                    </label>
                                    <input type="date" class="app-form-control @error('date_besoin') is-invalid @enderror" 
                                        id="date_besoin" name="date_besoin" value="{{ old('date_besoin') }}">
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
                                        <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                                        <option value="normale" {{ old('priorite') == 'normale' ? 'selected' : '' }}>Normale</option>
                                        <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                                        <option value="urgente" {{ old('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
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
                                    <label for="demande_approvisionnement_id" class="app-form-label">
                                        <i class="fas fa-clipboard-list me-2"></i>Demande d'Approvisionnement (Optionnel)
                                    </label>
                                    <select class="app-form-select @error('demande_approvisionnement_id') is-invalid @enderror" 
                                        id="demande_approvisionnement_id" name="demande_approvisionnement_id" onchange="loadDemandeApprovisionnement()">
                                        <option value="">Sélectionner une demande approuvée</option>
                                        @foreach($demandesApprovisionnement as $demande)
                                            @php
                                                $lignesData = $demande->lignes->map(function($ligne) {
                                                    return [
                                                        'article_id' => $ligne->article_id,
                                                        'designation' => $ligne->article ? $ligne->article->nom : '',
                                                        'reference' => $ligne->article ? $ligne->article->reference : '',
                                                        'quantite' => $ligne->quantite_approuvee ?? $ligne->quantite_demandee,
                                                        'unite_mesure' => $ligne->article && $ligne->article->uniteMesure ? $ligne->article->uniteMesure->ref : 'Unité'
                                                    ];
                                                });
                                            @endphp
                                            <option value="{{ $demande->id }}" 
                                                data-projet-id="{{ $demande->projet_id }}"
                                                data-projet-nom="{{ $demande->projet ? $demande->projet->nom_projet : '' }}"
                                                data-lignes='{{ json_encode($lignesData) }}'>
                                                {{ $demande->reference }} - {{ $demande->projet ? $demande->projet->nom_projet : 'Projet non défini' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('demande_approvisionnement_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="projet_id" class="app-form-label">
                                        <i class="fas fa-project-diagram me-2"></i>Projet
                                    </label>
                                    <select class="app-form-select @error('projet_id') is-invalid @enderror" 
                                        id="projet_id" name="projet_id">
                                        <option value="">Sélectionner un projet</option>
                                        @foreach($projets as $projet)
                                            <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
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
                        </div>
                        
                        <div class="app-form-row">
                            
                            <div class="app-form-col">
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
                                <table class="app-table display" id="articles_table">
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

@push('styles')
<style>
#articles_table .item-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
}

#articles_table .designation {
    flex: 1;
    min-width: 200px;
}

#articles_table .app-d-flex {
    display: flex;
    align-items: center;
    gap: 8px;
}

#articles_table .app-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

#articles_table .app-badge-info {
    background-color: #17a2b8;
    color: white;
}

#articles_table td {
    vertical-align: middle;
}

#articles_table .article-select {
    min-width: 250px;
}
</style>
@endpush

@push('scripts')
<script>
// Charger les données d'une demande d'approvisionnement
function loadDemandeApprovisionnement() {
    const select = document.getElementById('demande_approvisionnement_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        // Charger le projet
        const projetId = selectedOption.getAttribute('data-projet-id');
        const projetSelect = document.getElementById('projet_id');
        if (projetId && projetSelect) {
            projetSelect.value = projetId;
        }
        
        // Charger les lignes d'articles
        const lignesData = JSON.parse(selectedOption.getAttribute('data-lignes') || '[]');
        const tbody = document.querySelector('#articles_table tbody');
        
        // Vider le tableau actuel
        tbody.innerHTML = '';
        
        // Ajouter les lignes de la demande d'approvisionnement
        lignesData.forEach(ligne => {
            addArticleFromDemande(ligne);
        });
        
        // Si aucune ligne, ajouter une ligne vide
        if (lignesData.length === 0) {
            addArticle();
        }
    } else {
        // Réinitialiser le formulaire
        document.getElementById('projet_id').value = '';
        const tbody = document.querySelector('#articles_table tbody');
        tbody.innerHTML = '';
        addArticle();
    }
}

// Ajouter une ligne à partir des données de demande d'approvisionnement
function addArticleFromDemande(ligneData) {
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    
    // Remplir les données
    const articleSelect = clone.querySelector('.article-select');
    if (ligneData.article_id) {
        articleSelect.value = ligneData.article_id;
    }
    
    const designation = clone.querySelector('.designation');
    if (ligneData.designation) {
        designation.value = ligneData.designation;
    }
    
    const quantite = clone.querySelector('input[name="quantite[]"]');
    if (ligneData.quantite) {
        quantite.value = ligneData.quantite;
    }
    
    const uniteBadge = clone.querySelector('.unite-mesure');
    const uniteInput = clone.querySelector('.unite-mesure-input');
    if (ligneData.unite_mesure) {
        if (uniteBadge) uniteBadge.textContent = ligneData.unite_mesure;
        if (uniteInput) uniteInput.value = ligneData.unite_mesure;
    }
    
    document.querySelector('#articles_table tbody').appendChild(clone);
}

// Remplir automatiquement les informations d'un article
function fillArticleInfo(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const designation = selectedOption.getAttribute('data-designation');
    const unite = selectedOption.getAttribute('data-unite');
    const reference = selectedOption.getAttribute('data-reference');
    const row = selectElement.closest('tr');
    
    if (designation) {
        row.querySelector('.designation').value = designation;
    }
    
    if (unite) {
        const uniteBadge = row.querySelector('.unite-mesure');
        const uniteInput = row.querySelector('.unite-mesure-input');
        if (uniteBadge) {
            uniteBadge.textContent = unite;
        }
        if (uniteInput) {
            uniteInput.value = unite;
        }
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
@endpush