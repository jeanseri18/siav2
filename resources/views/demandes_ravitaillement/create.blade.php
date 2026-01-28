@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nouvelle Demande de Ravitaillement</h3>
                    <div class="card-tools">
                        <a href="{{ route('demandes-ravitaillement.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('demandes-ravitaillement.store') }}" method="POST" id="demandeForm">
                    @csrf
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="reference" class="form-label">Référence <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                           id="reference" name="reference" value="{{ old('reference', 'DR-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)) }}" required>
                                    @error('reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="priorite" class="form-label">Priorité <span class="text-danger">*</span></label>
                                    <select class="form-select @error('priorite') is-invalid @enderror" id="priorite" name="priorite" required>
                                        <option value="">Sélectionner une priorité</option>
                                        <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                                        <option value="normale" {{ old('priorite', 'normale') == 'normale' ? 'selected' : '' }}>Normale</option>
                                        <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                                        <option value="urgente" {{ old('priorite') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                    @error('priorite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="objet" class="form-label">Objet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('objet') is-invalid @enderror" 
                                           id="objet" name="objet" value="{{ old('objet') }}" required>
                                    @error('objet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Champ caché pour le contrat_id depuis la session -->
                        <input type="hidden" name="contrat_id" value="{{ $contratSessionId }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Demandeur</label>
                                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                    <input type="hidden" name="demandeur_id" value="{{ Auth::user()->id }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date_demande" class="form-label">Date de demande <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date_demande') is-invalid @enderror" 
                                           id="date_demande" name="date_demande" value="{{ old('date_demande', date('Y-m-d')) }}" required>
                                    @error('date_demande')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="date_livraison_souhaitee" class="form-label">Date de livraison souhaitée</label>
                                    <input type="date" class="form-control @error('date_livraison_souhaitee') is-invalid @enderror" 
                                           id="date_livraison_souhaitee" name="date_livraison_souhaitee" value="{{ old('date_livraison_souhaitee') }}">
                                    @error('date_livraison_souhaitee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="commentaires" class="form-label">Commentaires</label>
                                    <textarea class="form-control @error('commentaires') is-invalid @enderror" 
                                              id="commentaires" name="commentaires" rows="2">{{ old('commentaires') }}</textarea>
                                    @error('commentaires')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Articles demandés</h5>
                            <button type="button" class="btn btn-success" id="addLigne">
                                <i class="fas fa-plus"></i> Ajouter un article
                            </button>
                        </div>

                        <div id="lignesContainer">
                            <!-- Les lignes d'articles seront ajoutées ici dynamiquement -->
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="{{ route('demandes-ravitaillement.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template pour une ligne d'article -->
<template id="ligneTemplate">
    <div class="ligne-article border rounded p-3 mb-3">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label">Article <span class="text-danger">*</span></label>
                    <select class="form-select article-select" name="lignes[INDEX][article_id]" required>
                        <option value="">Sélectionner un article</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->id }}" data-unite="{{ $article->uniteMesure->nom ?? '' }}">
                                {{ $article->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group mb-3">
                    <label class="form-label">Quantité <span class="text-danger">*</span></label>
                    <input type="number" class="form-control quantite-input" name="lignes[INDEX][quantite_demandee]" 
                           step="0.001" min="0.001" required>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group mb-3">
                    <label class="form-label">Unité</label>
                    <select class="form-select unite-select" name="lignes[INDEX][unite_mesure_id]">
                        <option value="">Sélectionner</option>
                        @foreach($unitesMesure as $unite)
                            <option value="{{ $unite->id }}">{{ $unite->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            

            
            <div class="col-md-1">
                <div class="form-group mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger w-100 remove-ligne">
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
    let ligneIndex = 0;
    
    // Ajouter une ligne d'article
    $('#addLigne').click(function() {
        let template = $('#ligneTemplate').html();
        template = template.replace(/INDEX/g, ligneIndex);
        $('#lignesContainer').append(template);
        ligneIndex++;
    });
    
    // Supprimer une ligne d'article
    $(document).on('click', '.remove-ligne', function() {
        $(this).closest('.ligne-article').remove();
    });
    
    // Auto-sélection de l'unité de mesure selon l'article
    $(document).on('change', '.article-select', function() {
        let unite = $(this).find('option:selected').data('unite');
        let uniteSelect = $(this).closest('.ligne-article').find('.unite-select');
        
        if (unite) {
            uniteSelect.find('option').each(function() {
                if ($(this).text() === unite) {
                    $(this).prop('selected', true);
                    return false;
                }
            });
        }
    });
    
    // Validation du formulaire
    $('#demandeForm').submit(function(e) {
        let lignes = $('.ligne-article').length;
        if (lignes === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un article à la demande.');
            return false;
        }
    });
    
    // Ajouter une première ligne par défaut
    $('#addLigne').click();
});
</script>
@endpush