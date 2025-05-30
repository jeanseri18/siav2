{{-- Page Edit - Modifier une Demande d'Approvisionnement --}}
@extends('layouts.app')

@section('title', 'Modifier une Demande d\'Approvisionnement')
@section('page-title', 'Modifier une Demande d\'Approvisionnement')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-approvisionnements.index') }}">Demandes d'Approvisionnement</a></li>
<li class="breadcrumb-item"><a href="{{ route('demande-approvisionnements.show', $demandeApprovisionnement) }}">{{ $demandeApprovisionnement->reference }}</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-edit me-2"></i>Modifier la Demande d'Approvisionnement
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('demande-approvisionnements.show', $demandeApprovisionnement) }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('demande-approvisionnements.update', $demandeApprovisionnement) }}" method="POST" class="app-form">
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
                                        value="{{ old('date_demande', $demandeApprovisionnement->date_demande->format('Y-m-d')) }}" required>
                                    @error('date_demande')
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
                                            <option value="{{ $projet->id }}" 
                                                {{ old('projet_id', $demandeApprovisionnement->projet_id) == $projet->id ? 'selected' : '' }}>
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
                        
                        <div class="app-form-group">
                            <label for="description" class="app-form-label">
                                <i class="fas fa-align-left me-2"></i>Description
                            </label>
                            <textarea class="app-form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description', $demandeApprovisionnement->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="app-form-text">Description ou informations complémentaires sur cette demande</div>
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
                                            <th>Article <span class="text-danger">*</span></th>
                                            <th>Quantité <span class="text-danger">*</span></th>
                                            <th>Commentaire</th>
                                            <th style="width: 80px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($demandeApprovisionnement->lignes as $ligne)
                                        <tr>
                                            <td>
                                                <select class="app-form-select article-select" name="article_id[]" required>
                                                    <option value="">Sélectionner un article</option>
                                                    @foreach($articles as $article)
                                                        <option value="{{ $article->id }}" data-unite="{{ $article->unite_mesure }}"
                                                            {{ $ligne->article_id == $article->id ? 'selected' : '' }}>
                                                            {{ $article->code }} - {{ $article->designation }} 
                                                            ({{ $article->categorie->nom }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="app-form-control" name="quantite_demandee[]" 
                                                    min="1" value="{{ $ligne->quantite_demandee }}" required>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control" name="commentaire[]" 
                                                    value="{{ $ligne->commentaire }}">
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
                                    <i class="fas fa-plus me-2"></i> Ajouter une ligne
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
            <select class="app-form-select article-select" name="article_id[]" required>
                <option value="">Sélectionner un article</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}" data-unite="{{ $article->unite_mesure }}">
                        {{ $article->code }} - {{ $article->designation }} 
                        ({{ $article->categorie->nom }})
                    </option>
                @endforeach
            </select>
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

// Initialiser Select2 au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
});

// Alternative avec jQuery si préféré
$(document).ready(function() {
    // Initialiser les select2 existants
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
});
</script>
@endpush
@endsection