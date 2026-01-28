@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Modifier la Demande de Ravitaillement</h3>
                    <a href="{{ route('demandes-ravitaillement.show', $demandeRavitaillement) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('demandes-ravitaillement.update', $demandeRavitaillement) }}" method="POST" id="demandeForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Informations générales -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Informations générales</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                                   id="reference" name="reference" 
                                                   value="{{ old('reference', $demandeRavitaillement->reference) }}" required>
                                            @error('reference')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="priorite" class="form-label">Priorité <span class="text-danger">*</span></label>
                                            <select class="form-control @error('priorite') is-invalid @enderror" id="priorite" name="priorite" required>
                                                <option value="">Sélectionner une priorité</option>
                                                <option value="basse" {{ old('priorite', $demandeRavitaillement->priorite) == 'basse' ? 'selected' : '' }}>Basse</option>
                                                <option value="normale" {{ old('priorite', $demandeRavitaillement->priorite) == 'normale' ? 'selected' : '' }}>Normale</option>
                                                <option value="haute" {{ old('priorite', $demandeRavitaillement->priorite) == 'haute' ? 'selected' : '' }}>Haute</option>
                                                <option value="urgente" {{ old('priorite', $demandeRavitaillement->priorite) == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                            </select>
                                            @error('priorite')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="objet" class="form-label">Objet <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('objet') is-invalid @enderror" 
                                                   id="objet" name="objet" 
                                                   value="{{ old('objet', $demandeRavitaillement->objet) }}" required>
                                            @error('objet')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <input type="hidden" name="contrat_id" value="{{ $contratSessionId }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Dates et description -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Dates et description</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="date_demande" class="form-label">Date de demande <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('date_demande') is-invalid @enderror" 
                                                   id="date_demande" name="date_demande" 
                                                   value="{{ old('date_demande', $demandeRavitaillement->date_demande->format('Y-m-d')) }}" required>
                                            @error('date_demande')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="date_livraison_souhaitee" class="form-label">Date de livraison souhaitée</label>
                                            <input type="date" class="form-control @error('date_livraison_souhaitee') is-invalid @enderror" 
                                                   id="date_livraison_souhaitee" name="date_livraison_souhaitee" 
                                                   value="{{ old('date_livraison_souhaitee', $demandeRavitaillement->date_livraison_souhaitee?->format('Y-m-d')) }}">
                                            @error('date_livraison_souhaitee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>



                                        <div class="form-group mb-3">
                                            <label for="commentaires" class="form-label">Commentaires</label>
                                            <textarea class="form-control @error('commentaires') is-invalid @enderror" 
                                                      id="commentaires" name="commentaires" rows="3" 
                                                      placeholder="Commentaires additionnels...">{{ old('commentaires', $demandeRavitaillement->commentaires) }}</textarea>
                                            @error('commentaires')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Articles demandés -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Articles demandés</h5>
                                        <button type="button" class="btn btn-primary btn-sm" id="addArticle">
                                            <i class="fas fa-plus"></i> Ajouter un article
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="articlesContainer">
                                            @foreach($demandeRavitaillement->lignes as $index => $ligne)
                                                <div class="article-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="form-label">Article <span class="text-danger">*</span></label>
                                                                <select class="form-control article-select" name="lignes[{{ $index }}][article_id]" required>
                                                                    <option value="">Sélectionner un article</option>
                                                                    @foreach($articles as $article)
                                                                        <option value="{{ $article->id }}" 
                                                                                data-unite="{{ $article->uniteMesure->id ?? '' }}" 
                                                                                data-prix="{{ $article->prix_unitaire ?? '' }}"
                                                                                {{ $ligne->article_id == $article->id ? 'selected' : '' }}>
                                                                            {{ $article->nom }} ({{ $article->reference }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control quantite-input" 
                                                                       name="lignes[{{ $index }}][quantite_demandee]" 
                                                                       value="{{ $ligne->quantite_demandee }}" 
                                                                       step="0.001" min="0" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label class="form-label">Unité</label>
                                                                <select class="form-control unite-select" name="lignes[{{ $index }}][unite_mesure_id]">
                                                                    <option value="">Sélectionner</option>
                                                                    @foreach($unitesMesure as $unite)
                                                                        <option value="{{ $unite->id }}" {{ $ligne->unite_mesure_id == $unite->id ? 'selected' : '' }}>
                                                                            {{ $unite->nom }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm d-block remove-article">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @if($demandeRavitaillement->lignes->isEmpty())
                                            <div class="text-center text-muted py-4" id="noArticlesMessage">
                                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                                <p>Aucun article ajouté. Cliquez sur "Ajouter un article" pour commencer.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('demandes-ravitaillement.show', $demandeRavitaillement) }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times"></i> Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template pour nouvel article -->
<template id="articleTemplate">
    <div class="article-row border rounded p-3 mb-3" data-index="__INDEX__">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Article <span class="text-danger">*</span></label>
                    <select class="form-control article-select" name="lignes[__INDEX__][article_id]" required>
                        <option value="">Sélectionner un article</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" 
                                    data-unite="{{ $article->uniteMesure->id ?? '' }}" 
                                    data-prix="{{ $article->prix_unitaire ?? '' }}">
                                {{ $article->nom }} ({{ $article->reference }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Quantité <span class="text-danger">*</span></label>
                    <input type="number" class="form-control quantite-input" 
                           name="lignes[__INDEX__][quantite_demandee]" 
                           step="0.001" min="0" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="form-label">Unité</label>
                    <select class="form-control unite-select" name="lignes[__INDEX__][unite_mesure_id]">
                        <option value="">Sélectionner</option>
                        @foreach($unitesMesure as $unite)
                            <option value="{{ $unite->id }}">{{ $unite->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-1">
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm d-block remove-article">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let articleIndex = {{ $demandeRavitaillement->lignes->count() }};
    
    // Ajouter un nouvel article
    $('#addArticle').click(function() {
        const template = $('#articleTemplate').html();
        const newArticle = template.replace(/__INDEX__/g, articleIndex);
        $('#articlesContainer').append(newArticle);
        $('#noArticlesMessage').hide();
        articleIndex++;
    });
    
    // Supprimer un article
    $(document).on('click', '.remove-article', function() {
        $(this).closest('.article-row').remove();
        if ($('.article-row').length === 0) {
            $('#noArticlesMessage').show();
        }
    });
    
    // Auto-remplir l'unité et le prix quand un article est sélectionné
    $(document).on('change', '.article-select', function() {
        const selectedOption = $(this).find('option:selected');
        const uniteId = selectedOption.data('unite');
        const prix = selectedOption.data('prix');
        const row = $(this).closest('.article-row');
        
        if (uniteId) {
            row.find('.unite-select').val(uniteId);
        }
        if (prix) {
            row.find('.prix-input').val(prix);
        }
    });
    
    // Validation du formulaire
    $('#demandeForm').submit(function(e) {
        let valid = true;
        let errorMessage = '';
        
        // Vérifier qu'au moins un article est ajouté
        if ($('.article-row').length === 0) {
            valid = false;
            errorMessage += 'Veuillez ajouter au moins un article.\n';
        }
        
        // Vérifier que tous les articles ont une quantité
        $('.article-row').each(function() {
            const articleSelect = $(this).find('.article-select');
            const quantiteInput = $(this).find('.quantite-input');
            
            if (!articleSelect.val()) {
                valid = false;
                errorMessage += 'Veuillez sélectionner un article pour toutes les lignes.\n';
            }
            
            if (!quantiteInput.val() || parseFloat(quantiteInput.val()) <= 0) {
                valid = false;
                errorMessage += 'Veuillez saisir une quantité valide pour tous les articles.\n';
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert(errorMessage);
        }
    });
    
    // Masquer le message "aucun article" si des articles existent
    if ($('.article-row').length > 0) {
        $('#noArticlesMessage').hide();
    }
});
</script>
@endpush