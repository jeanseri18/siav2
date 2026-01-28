@extends('layouts.app')

@section('title', 'Modifier une Prestation')
@section('page-title', 'Modifier une Prestation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('prestations.index') }}">Prestations</a></li>
<li class="breadcrumb-item active">Modifier</li>
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
    // Données DQE hiérarchiques
    const categories = @json($categories);
    const dqes = @json($dqes);
    
    console.log('DQEs loaded:', dqes);
    

    
    // Fonction pour charger les catégories à partir du DQE sélectionné
    function loadCategories(dqeId) {
        const categorieSelect = $('#categorie_select');
        const sousCategorieSelect = $('#sous_categorie_select');
        const rubriqueSelect = $('#rubrique_select');
        const dqeLigneSelect = $('#dqe_ligne_select');
        
        // Réinitialiser tous les selects
        categorieSelect.empty().append('<option value="">-- Sélectionnez une catégorie --</option>');
        sousCategorieSelect.empty().append('<option value="">-- Sélectionnez d\'abord une catégorie --</option>').prop('disabled', true);
        rubriqueSelect.empty().append('<option value="">-- Sélectionnez d\'abord une sous-catégorie --</option>').prop('disabled', true);
        dqeLigneSelect.empty().append('<option value="">-- Sélectionnez d\'abord une rubrique --</option>').prop('disabled', true);
        
        if (dqeId) {
            // Trouver le DQE sélectionné
            const dqe = dqes.find(d => d.id == dqeId);
            
            if (dqe && dqe.lignes) {
                // Extraire les catégories uniques à partir des lignes DQE
                const categoriesMap = new Map();
                
                dqe.lignes.forEach(function(ligne) {
                    if (ligne.rubrique && ligne.rubrique.sous_categorie && ligne.rubrique.sous_categorie.categorie) {
                        const cat = ligne.rubrique.sous_categorie.categorie;
                        if (!categoriesMap.has(cat.id)) {
                            categoriesMap.set(cat.id, cat);
                        }
                    }
                });
                
                // Trier par nom et ajouter au select
                const uniqueCategories = Array.from(categoriesMap.values()).sort((a, b) => a.nom.localeCompare(b.nom));
                uniqueCategories.forEach(function(categorie) {
                    categorieSelect.append(`<option value="${categorie.id}">${categorie.nom}</option>`);
                });
                
                categorieSelect.prop('disabled', uniqueCategories.length === 0);
            } else {
                categorieSelect.prop('disabled', true);
            }
        } else {
            categorieSelect.prop('disabled', true);
        }
    }
    
    // Fonction pour charger les sous-catégories
    function loadSousCategories(categorieId) {
        const sousCategorieSelect = $('#sous_categorie_select');
        const rubriqueSelect = $('#rubrique_select');
        const dqeLigneSelect = $('#dqe_ligne_select');
        
        sousCategorieSelect.empty().append('<option value="">-- Sélectionnez une sous-catégorie --</option>');
        rubriqueSelect.empty().append('<option value="">-- Sélectionnez d\'abord une sous-catégorie --</option>').prop('disabled', true);
        dqeLigneSelect.empty().append('<option value="">-- Sélectionnez d\'abord une rubrique --</option>').prop('disabled', true);
        
        if (categorieId) {
            const dqeId = $('#dqe_select').val();
            const dqe = dqes.find(d => d.id == dqeId);
            
            if (dqe && dqe.lignes) {
                // Extraire les sous-catégories uniques pour cette catégorie
                const sousCategoriesMap = new Map();
                
                dqe.lignes.forEach(function(ligne) {
                    if (ligne.rubrique && ligne.rubrique.sous_categorie && 
                        ligne.rubrique.sous_categorie.categorie && 
                        ligne.rubrique.sous_categorie.categorie.id == categorieId) {
                        const sousCat = ligne.rubrique.sous_categorie;
                        if (!sousCategoriesMap.has(sousCat.id)) {
                            sousCategoriesMap.set(sousCat.id, sousCat);
                        }
                    }
                });
                
                // Trier par nom et ajouter au select
                const uniqueSousCategories = Array.from(sousCategoriesMap.values()).sort((a, b) => a.nom.localeCompare(b.nom));
                uniqueSousCategories.forEach(function(sousCategorie) {
                    sousCategorieSelect.append(`<option value="${sousCategorie.id}">${sousCategorie.nom}</option>`);
                });
                
                sousCategorieSelect.prop('disabled', uniqueSousCategories.length === 0);
            } else {
                sousCategorieSelect.prop('disabled', true);
            }
        } else {
            sousCategorieSelect.prop('disabled', true);
        }
    }
    
    // Fonction pour charger les rubriques
    function loadRubriques(sousCategorieId) {
        const rubriqueSelect = $('#rubrique_select');
        const dqeLigneSelect = $('#dqe_ligne_select');
        
        rubriqueSelect.empty().append('<option value="">-- Sélectionnez une rubrique --</option>');
        dqeLigneSelect.empty().append('<option value="">-- Sélectionnez d\'abord une rubrique --</option>').prop('disabled', true);
        
        console.log('loadRubriques called with sousCategorieId:', sousCategorieId);
        
        if (sousCategorieId) {
            const dqeId = $('#dqe_select').val();
            const dqe = dqes.find(d => d.id == dqeId);
            
            console.log('Found DQE in loadRubriques:', dqe);
            
            if (dqe && dqe.lignes) {
                // Extraire les rubriques uniques pour cette sous-catégorie
                const rubriquesMap = new Map();
                
                dqe.lignes.forEach(function(ligne) {
                    console.log('Processing ligne in loadRubriques:', ligne);
                    if (ligne.rubrique && ligne.rubrique.sous_categorie && 
                        ligne.rubrique.sous_categorie.id == sousCategorieId) {
                        const rubrique = ligne.rubrique;
                        console.log('Found matching rubrique:', rubrique);
                        if (!rubriquesMap.has(rubrique.id)) {
                            rubriquesMap.set(rubrique.id, rubrique);
                        }
                    }
                });
                
                // Trier par nom et ajouter au select
                const uniqueRubriques = Array.from(rubriquesMap.values()).sort((a, b) => a.nom.localeCompare(b.nom));
                console.log('Unique rubriques found:', uniqueRubriques);
                uniqueRubriques.forEach(function(rubrique) {
                    rubriqueSelect.append(`<option value="${rubrique.id}">${rubrique.nom}</option>`);
                });
                
                rubriqueSelect.prop('disabled', uniqueRubriques.length === 0);
            } else {
                rubriqueSelect.prop('disabled', true);
            }
        } else {
            rubriqueSelect.prop('disabled', true);
        }
    }
    
    // Fonction pour charger les lignes DQE
    function loadDQELignes(rubriqueId) {
        const lignesContainer = $('#dqe_lignes_list');
        lignesContainer.empty();
        
        // Réinitialiser le compteur et le bouton
        $('#lignes_count').text('0 sélectionnée(s)');
        $('#add_ligne_prestataire').prop('disabled', true);
        $('#add_ligne_prestataire').html('<i class="fas fa-plus me-2"></i>Ajouter la ligne prestataire');
        
        console.log('loadDQELignes called with rubriqueId:', rubriqueId);
        
        if (rubriqueId) {
            const dqeId = $('#dqe_select').val();
            const dqe = dqes.find(d => d.id == dqeId);
            
            console.log('Found DQE:', dqe);
            
            if (dqe && dqe.lignes) {
                console.log('DQE lignes:', dqe.lignes);
                // Filtrer les lignes DQE pour cette rubrique
                const lignesDQE = dqe.lignes.filter(ligne => ligne.id_rubrique == rubriqueId);
                
                console.log('Filtered lignesDQE:', lignesDQE);
                
                if (lignesDQE.length > 0) {
                    lignesDQE.forEach(function(ligne) {
                        const ligneHtml = `
                            <div class="form-check" style="margin-bottom: 8px;">
                                <input class="form-check-input dqe-ligne-checkbox" 
                                       type="checkbox" 
                                       value="${ligne.id}" 
                                       id="ligne_${ligne.id}"
                                       data-ligne='${JSON.stringify(ligne)}'>
                                <label class="form-check-label" for="ligne_${ligne.id}" style="font-size: 0.9rem;">
                                   ${ligne.designation}
                                    <br><small class="text-muted">Qté: ${ligne.quantite} | PU: ${Number(ligne.pu_ht).toFixed(2)} | MT: ${Number(ligne.montant_ht).toFixed(2)}</small>
                                </label>
                            </div>
                        `;
                        lignesContainer.append(ligneHtml);
                    });
                } else {
                    lignesContainer.append('<div class="text-muted text-center">Aucune ligne disponible pour cette rubrique</div>');
                }
            } else {
                lignesContainer.append('<div class="text-muted text-center">Aucune ligne disponible</div>');
            }
        } else {
            lignesContainer.append('<div class="text-muted text-center">-- Sélectionnez d\'abord une rubrique --</div>');
        }
    }
    

    
    // Événements de changement
    $('#dqe_select').change(function() {
        const dqeId = $(this).val();
        loadCategories(dqeId);
    });
    
    $('#categorie_select').change(function() {
        const categorieId = $(this).val();
        loadSousCategories(categorieId);
    });
    
    $('#sous_categorie_select').change(function() {
        const sousCategorieId = $(this).val();
        loadRubriques(sousCategorieId);
    });
    
    $('#rubrique_select').change(function() {
        const rubriqueId = $(this).val();
        loadDQELignes(rubriqueId);
    });
    
    // Gérer la sélection multiple des lignes DQE
    $(document).on('change', '.dqe-ligne-checkbox', function() {
        const selectedCount = $('.dqe-ligne-checkbox:checked').length;
        $('#add_ligne_prestataire').prop('disabled', selectedCount === 0);
        
        // Mettre à jour le compteur
        $('#lignes_count').text(`${selectedCount} sélectionnée(s)`);
        
        if (selectedCount > 0) {
            $('#add_ligne_prestataire').html(`<i class="fas fa-plus me-2"></i>Ajouter ${selectedCount} ligne(s) prestataire`);
        } else {
            $('#add_ligne_prestataire').html('<i class="fas fa-plus me-2"></i>Ajouter la ligne prestataire');
        }
    });
    
    // Ajouter une ou plusieurs lignes prestataire
    $('#add_ligne_prestataire').click(function() {
        const selectedCheckboxes = $('.dqe-ligne-checkbox:checked');
        
        if (selectedCheckboxes.length > 0) {
            selectedCheckboxes.each(function() {
                const ligneData = $(this).data('ligne');
                
                if (ligneData) {
                    // Code removed as part of decompte functionality removal
                }
            });
            
            // Réinitialiser la sélection
            $('.dqe-ligne-checkbox:checked').prop('checked', false);
            $('#add_ligne_prestataire').prop('disabled', true);
            $('#add_ligne_prestataire').html('<i class="fas fa-plus me-2"></i>Ajouter la ligne prestataire');
        }
    });
    
    // Soumettre le formulaire
    
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
});
</script>
@endpush



@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
<i class="fas fa-edit me-2"></i>Modifier la Prestation
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('prestations.update', $prestation->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_contrat" class="app-form-label">
                                <i class="fas fa-file-contract me-2"></i>Contrat
                            </label>
                            <select name="id_contrat" id="id_contrat" class="app-form-select" required>
                                <option value="">-- Sélectionnez un contrat --</option>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}" {{ $prestation->id_contrat == $contrat->id ? 'selected' : '' }}>
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
                                    <input type="radio" name="prestataire_type" value="artisan" id="type_artisan" {{ $prestation->id_artisan ? 'checked' : '' }}>
                                    <span class="app-form-radio-label">Artisan</span>
                                </label>
                                <label class="app-form-radio">
                                    <input type="radio" name="prestataire_type" value="fournisseur" id="type_fournisseur" {{ $prestation->fournisseur_id ? 'checked' : '' }}>
                                    <span class="app-form-radio-label">Fournisseur</span>
                                </label>
                            </div>
                            <div class="app-form-text">Choisir le type de prestataire</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group" id="artisan_selection" style="{{ $prestation->fournisseur_id ? 'display: none;' : '' }}">
                            <label for="id_artisan" class="app-form-label">
                                <i class="fas fa-hard-hat me-2"></i>Artisan
                            </label>
                            <select name="id_artisan" id="id_artisan" class="app-form-select" {{ $prestation->fournisseur_id ? 'disabled' : '' }}>
                                <option value="">-- Sélectionnez un artisan --</option>
                                @foreach($artisans as $artisan)
                                    <option value="{{ $artisan->id }}" {{ $prestation->id_artisan == $artisan->id ? 'selected' : '' }}>{{ $artisan->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Artisan assigné à cette prestation</div>
                        </div>
                        
                        <div class="app-form-group" id="fournisseur_selection" style="{{ $prestation->id_artisan ? 'display: none;' : '' }}">
                            <label for="fournisseur_id" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Fournisseur
                            </label>
                            <select name="fournisseur_id" id="fournisseur_id" class="app-form-select" {{ $prestation->id_artisan ? 'disabled' : '' }}>
                                <option value="">-- Sélectionnez un fournisseur --</option>
                                @foreach($fournisseurs as $fournisseur)
                                    <option value="{{ $fournisseur->id }}" {{ $prestation->fournisseur_id == $fournisseur->id ? 'selected' : '' }}>{{ $fournisseur->nom_raison_sociale }} {{ $fournisseur->prenoms }}</option>
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
                                    <option value="{{ $corpMetier->id }}" {{ $prestation->corps_metier_id == $corpMetier->id ? 'selected' : '' }}>
                                        {{ $corpMetier->nom }}
                                    </option>
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
                            <input type="string" name="prestation_titre" id="prestation_titre" class="app-form-control" value="{{ $prestation->prestation_titre }}" required>
                            <div class="app-form-text">Titre ou description courte de la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="detail" class="app-form-label">
                                <i class="fas fa-align-left me-2"></i>Description
                            </label>
                            <input type="string" name="detail" id="detail" class="app-form-control" value="{{ $prestation->detail }}" required>
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
                            <input type="number" step="0.01" name="montant" id="montant" class="app-form-control" value="{{ $prestation->montant }}">
                            <div class="app-form-text">Montant de la prestation</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="taux_avancement" class="app-form-label" style="color: #999;">
                                <i class="fas fa-percentage me-2"></i>Taux d'avancement
                            </label>
                            <input type="number" min="0" max="100" name="taux_avancement" id="taux_avancement" class="app-form-control" value="{{ $prestation->taux_avancement ?? 0 }}" disabled style="background-color: #f5f5f5; color: #999; cursor: not-allowed;">
                            <div class="app-form-text" style="color: #999;">Pourcentage d'avancement de la prestation (0-100)</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-group">
                    <label for="statut" class="app-form-label">
                        <i class="fas fa-tasks me-2"></i>Statut
                    </label>
                    <select name="statut" id="statut" class="app-form-select">
                        <option value="En cours" {{ $prestation->statut == 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Terminée" {{ $prestation->statut == 'Terminée' ? 'selected' : '' }}>Terminée</option>
                        <option value="Annulée" {{ $prestation->statut == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                    </select>
                    <div class="app-form-text">État actuel de la prestation</div>
                </div>
                

                
                <div class="app-card-footer">
                    <a href="{{ route('prestations.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-warning">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection