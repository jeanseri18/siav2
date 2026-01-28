{{-- Page Create - Nouvelle Demande d'Approvisionnement --}}
@extends('layouts.app')

@section('title', 'Nouvelle Demande d\'Approvisionnement')
@section('page-title', 'Nouvelle Demande d\'Approvisionnement')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-approvisionnements.index') }}">Demandes d'Approvisionnement</a></li>
<li class="breadcrumb-item active">Nouvelle</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-boxes me-2"></i>Nouvelle Demande d'Approvisionnement
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('demande-approvisionnements.index') }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('demande-approvisionnements.store') }}" method="POST" class="app-form">
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
                                    <label for="date_reception" class="app-form-label">
                                        <i class="fas fa-calendar-check me-2"></i>Date de réception souhaitée
                                    </label>
                                    <input type="date" class="app-form-control @error('date_reception') is-invalid @enderror" 
                                        id="date_reception" name="date_reception" value="{{ old('date_reception') }}">
                                    @error('date_reception')
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
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="initiateur" class="app-form-label">
                                        <i class="fas fa-user me-2"></i>Initiateur (Chef de projet)
                                    </label>
                                    <input type="text" class="app-form-control" 
                                        id="initiateur" name="initiateur" value="{{ Auth::user()->nom }}" readonly>
                                </div>
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
                                            <th style="width: 25%;">Réf Article <span class="text-danger">*</span></th>
                                            <th>Désignation</th>
                                            <th style="width: 10%;">Unité</th>
                                            <th style="width: 12%;">Quantité <span class="text-danger">*</span></th>
                                            <th>Commentaire</th>
                                            <th style="width: 80px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="app-form-select article-select" name="article_id[]" required>
                                                    <option value="">Sélectionner un article</option>
                                                    @foreach($articles as $article)
                                                        <option value="{{ $article->id }}" 
                                                            data-unite="{{ $article->uniteMesure->ref ?? '' }}"
                                        data-designation="{{ $article->nom }}"
                                        data-code="{{ $article->reference }}">
                                                            {{ $article->reference }} - {{ $article->nom }} 
                                                            ({{ $article->categorie->nom }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control designation-field" name="designation[]" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control unite-field" name="unite[]" readonly>
                                            </td>
                                            <td>
                                                <input type="number" class="app-form-control" name="quantite_demandee[]" 
                                                    min="1" value="1" required>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control" name="commentaire[]">
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
            <select class="app-form-select article-select" name="article_id[]" required>
                <option value="">Sélectionner un article</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}" 
                        data-unite="{{ $article->uniteMesure->ref ?? '' }}"
                        data-designation="{{ $article->nom }}"
                        data-code="{{ $article->reference }}">
                        {{ $article->reference }} - {{ $article->nom }} 
                        ({{ $article->categorie->nom }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="app-form-control designation-field" name="designation[]" readonly>
        </td>
        <td>
            <input type="text" class="app-form-control unite-field" name="unite[]" readonly>
        </td>
        <td>
            <input type="number" class="app-form-control" name="quantite_demandee[]" min="1" value="1" required>
        </td>
        <td>
            <input type="text" class="app-form-control" name="commentaire[]">
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
function addArticle() {
    // Cloner le template
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    
    // Ajouter la nouvelle ligne au tableau
    document.querySelector('#articles_table tbody').appendChild(clone);
    
    // Initialiser Select2 sur la nouvelle ligne si disponible
    if (typeof $.fn.select2 !== 'undefined') {
        $('#articles_table tbody tr:last-child .article-select').select2();
    }
    
    // Ajouter l'événement change pour la nouvelle ligne
    const newSelect = document.querySelector('#articles_table tbody tr:last-child .article-select');
    newSelect.addEventListener('change', handleArticleChange);
}

function removeLine(button) {
    const tbody = document.querySelector('#articles_table tbody');
    const rowCount = tbody.querySelectorAll('tr').length;
    
    if (rowCount > 1) {
        button.closest('tr').remove();
    } else {
        alert('Vous devez avoir au moins une ligne d\'article');
    }
}

function handleArticleChange(event) {
    const select = event.target;
    const selectedOption = select.options[select.selectedIndex];
    const row = select.closest('tr');
    
    if (selectedOption.value) {
        // Récupérer les données depuis les attributs data de l'option
        const designation = selectedOption.getAttribute('data-designation') || '';
        const unite = selectedOption.getAttribute('data-unite') || '';
        
        // Remplir automatiquement les champs
        const designationField = row.querySelector('.designation-field');
        designationField.value = designation;
        
        const uniteField = row.querySelector('.unite-field');
        uniteField.value = unite;
    } else {
        // Vider les champs si aucun article sélectionné
        row.querySelector('.designation-field').value = '';
        row.querySelector('.unite-field').value = '';
    }
}

// Initialiser Select2 au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
    
    // Ajouter les événements change pour les selects existants
    document.querySelectorAll('.article-select').forEach(function(select) {
        select.addEventListener('change', handleArticleChange);
    });
});

// Alternative avec jQuery si préféré
$(document).ready(function() {
    // Initialiser les select2 existants
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
    
    // Gérer les changements d'articles avec jQuery (pour Select2)
    $(document).on('change', '.article-select', function() {
        const selectedOption = $(this).find('option:selected');
        const row = $(this).closest('tr');
        
        if (selectedOption.val()) {
            // Récupérer les données depuis les attributs data de l'option
            const designation = selectedOption.attr('data-designation') || '';
            const unite = selectedOption.attr('data-unite') || '';
            
            // Remplir automatiquement les champs
            row.find('.designation-field').val(designation);
            row.find('.unite-field').val(unite);
        } else {
            // Vider les champs si aucun article sélectionné
            row.find('.designation-field').val('');
            row.find('.unite-field').val('');
        }
    });
});
</script>
@endpush
@endsection