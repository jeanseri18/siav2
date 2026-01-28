@extends('layouts.app')

@section('title', 'Ajouter une Prestation')
@section('page-title', 'Ajouter une Prestation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('prestations.index') }}">Prestations</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@push('styles')
<style>
.app-form-radio-group {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.app-form-radio {
    display: flex;
    align-items: center;
    cursor: pointer;
    margin-right: 1rem;
}

.app-form-radio input[type="radio"] {
    margin-right: 0.5rem;
    accent-color: var(--primary, #033d71);
}

.app-form-radio-label {
    font-weight: 500;
    color: var(--gray-700, #495057);
}
</style>
@endpush

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Gérer le changement de type de prestataire
    $('input[name="prestataire_type"]').change(function() {
        var selectedType = $(this).val();
        
        if (selectedType === 'artisan') {
            $('#artisan_selection').show();
            $('#fournisseur_selection').hide();
            $('#id_artisan').prop('disabled', false);
            $('#fournisseur_id').prop('disabled', true);
            $('#fournisseur_id').val('');
        } else if (selectedType === 'fournisseur') {
            $('#artisan_selection').hide();
            $('#fournisseur_selection').show();
            $('#id_artisan').prop('disabled', true);
            $('#fournisseur_id').prop('disabled', false);
            $('#id_artisan').val('');
        }
    });
    
    // Initialiser l'état au chargement
    var initialType = $('input[name="prestataire_type"]:checked').val();
    if (initialType === 'fournisseur') {
        $('#artisan_selection').hide();
        $('#fournisseur_selection').show();
        $('#id_artisan').prop('disabled', true);
        $('#fournisseur_id').prop('disabled', false);
    }
});
</script>
@endpush

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-tools me-2"></i>Ajouter une Prestation
            </h2>
        </div>
        
        @if ($errors->any())
        <div class="app-alert app-alert-danger">
            <div class="app-alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            </div>
            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
        
        <div class="app-card-body">
            <form action="{{ route('prestations.store') }}" method="POST" class="app-form">
                @csrf
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_contrat" class="app-form-label">
                                <i class="fas fa-file-contract me-2"></i>Contrat
                            </label>
                            <select name="id_contrat" id="id_contrat" class="app-form-select" required>
                                <option value="">-- Sélectionnez un contrat --</option>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}" {{ session("contrat_id") == $contrat->id ? 'selected' : '' }}>
                                        {{ $contrat->nom_contrat }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Contrat associé à cette prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label class="app-form-label">
                                <i class="fas fa-handshake me-2"></i>Type de prestataire
                            </label>
                            <div class="app-form-radio-group">
                                <label class="app-form-radio">
                                    <input type="radio" name="prestataire_type" value="artisan" id="type_artisan" checked>
                                    <span class="app-form-radio-label">Artisan</span>
                                </label>
                                <label class="app-form-radio">
                                    <input type="radio" name="prestataire_type" value="fournisseur" id="type_fournisseur">
                                    <span class="app-form-radio-label">Fournisseur</span>
                                </label>
                            </div>
                            <div class="app-form-text">Choisir le type de prestataire</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group" id="artisan_selection">
                            <label for="id_artisan" class="app-form-label">
                                <i class="fas fa-hard-hat me-2"></i>Artisan
                            </label>
                            <select name="id_artisan" id="id_artisan" class="app-form-select">
                                <option value="">-- Sélectionnez un artisan --</option>
                                @foreach($artisans as $artisan)
                                    <option value="{{ $artisan->id }}">{{ $artisan->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Artisan assigné à cette prestation</div>
                        </div>
                        
                        <div class="app-form-group" id="fournisseur_selection" style="display: none;">
                            <label for="fournisseur_id" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Fournisseur
                            </label>
                            <select name="fournisseur_id" id="fournisseur_id" class="app-form-select">
                                <option value="">-- Sélectionnez un fournisseur --</option>
                                @foreach($fournisseurs as $fournisseur)
                                    <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom_raison_sociale }} {{ $fournisseur->prenoms }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Fournisseur assigné à cette prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <!-- Colonne vide pour maintenir l'alignement -->
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="corps_metier_id" class="app-form-label">
                                <i class="fas fa-tools me-2"></i>Corps de Métier
                            </label>
                            <select name="corps_metier_id" id="corps_metier_id" class="app-form-select">
                                <option value="">-- Sélectionnez un corps de métier --</option>
                                @foreach($corpMetiers as $corpMetier)
                                    <option value="{{ $corpMetier->id }}">{{ $corpMetier->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Corps de métier associé à cette prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <!-- Colonne vide pour maintenir l'alignement -->
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="prestation_titre" class="app-form-label">
                                <i class="fas fa-clipboard-list me-2"></i>Intitulé de la Prestation
                            </label>
                            <input type="string" name="prestation_titre" id="prestation_titre" class="app-form-control" required>
                            <div class="app-form-text">Titre ou description courte de la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="detail" class="app-form-label">
                                <i class="fas fa-align-left me-2"></i>Description
                            </label>
                            <input type="string" name="detail" id="detail" class="app-form-control" required>
                            <div class="app-form-text">Description détaillée de la prestation</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="montant" class="app-form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>Montant
                            </label>
                            <input type="number" step="0.01" name="montant" id="montant" class="app-form-control">
                            <div class="app-form-text">Montant de la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="taux_avancement" class="app-form-label" style="color: #999;">
                                <i class="fas fa-percentage me-2"></i>Taux d'avancement
                            </label>
                            <input type="number" min="0" max="100" name="taux_avancement" id="taux_avancement" class="app-form-control" value="0" disabled style="background-color: #f5f5f5; color: #999; cursor: not-allowed;">
                            <div class="app-form-text" style="color: #999;">Pourcentage d'avancement de la prestation (0-100)</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('prestations.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection