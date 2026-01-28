@extends('layouts.app')

@section('title', 'Lignes de Prestation')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-list me-2"></i>Lignes de Prestation
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('prestations.index') }}">Prestations</a></li>
                            <li class="breadcrumb-item active">Lignes</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('prestations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Affichage des catégories, sous-catégories et rubriques dans un tableau -->
    @if($dqe)
    <form action="{{ route('prestations.storeLignes', $prestation->id) }}" method="POST" id="formLignesPrestation">
        @csrf
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-folder-tree me-2"></i>Structure du DQE - {{ $dqe->reference }}
                </h5>
            </div>
            <div class="card-body">
                @if($categories && $categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                @foreach($categories as $categorie)
                                    <!-- Ligne Catégorie -->
                                    <tr class="table-primary">
                                        <td colspan="2">
                                            <i class="fas fa-folder me-2"></i>
                                            <strong>{{ $categorie->nom }}</strong>
                                        </td>
                                    </tr>
                                    
                                    @if($categorie->sousCategories && $categorie->sousCategories->count() > 0)
                                        @foreach($categorie->sousCategories as $sousCategorie)
                                            <!-- Ligne Sous-catégorie -->
                                            <tr class="table-info">
                                                <td colspan="2" class="ps-4">
                                                    <i class="fas fa-folder-open me-2"></i>
                                                    {{ $sousCategorie->nom }}
                                                </td>
                                            </tr>
                                            
                                            @if($sousCategorie->rubriques && $sousCategorie->rubriques->count() > 0)
                                                @foreach($sousCategorie->rubriques as $rubrique)
                                                    <!-- Ligne Rubrique avec checkbox -->
                                                    <tr>
                                                        <td class="ps-5" colspan="2">
                                                            <div class="form-check">
                                                                <input class="form-check-input rubrique-checkbox" 
                                                                       type="checkbox" 
                                                                       id="rubrique_{{ $rubrique->id }}"
                                                                       onchange="toggleLignesForm({{ $rubrique->id }})">
                                                                <label class="form-check-label" for="rubrique_{{ $rubrique->id }}">
                                                                    <i class="fas fa-file-alt me-2 text-secondary"></i>
                                                                    {{ $rubrique->nom }}
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- Formulaire pour les lignes de prestation (caché par défaut) -->
                                                    <tr id="lignes_form_{{ $rubrique->id }}" class="lignes-form-row" style="display: none;">
                                                        <td colspan="2" class="ps-5 bg-light">
                                                            <div class="p-3">
                                                                <h6 class="mb-3">
                                                                    <i class="fas fa-list me-2 text-primary"></i>
                                                                    Lignes du DQE pour : {{ $rubrique->nom }}
                                                                </h6>
                                                                
                                                                <!-- Lignes DQE existantes -->
                                                                @if($rubrique->dqeLignes && $rubrique->dqeLignes->count() > 0)
                                                                    <div class="alert alert-info mb-3">
                                                                        <i class="fas fa-info-circle me-2"></i>
                                                                        {{ $rubrique->dqeLignes->count() }} ligne(s) trouvée(s) dans le DQE. Vous pouvez les modifier avant l'enregistrement.
                                                                    </div>
                                                                    
                                                                    <div id="lignes_dqe_container_{{ $rubrique->id }}" class="mb-3">
                                                                        @foreach($rubrique->dqeLignes as $indexDqe => $ligneDqe)
                                                                            <div id="ligne_{{ $rubrique->id }}_dqe_{{ $indexDqe }}" class="ligne-prestation card mb-2 border-primary">
                                                                                <div class="card-body">
                                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                                        <span class="badge bg-primary">Ligne DQE</span>
                                                                                        <small class="text-muted">Code: {{ $ligneDqe->code }}</small>
                                                                                    </div>
                                                                                    <div class="row align-items-end">
                                                                                        <input type="hidden" name="lignes[{{ $rubrique->id }}_dqe_{{ $indexDqe }}][id_rubrique]" value="{{ $rubrique->id }}">
                                                                                        
                                                                                        <div class="col-md-3">
                                                                                            <label class="form-label">Désignation <span class="text-danger">*</span></label>
                                                                                            <input type="text" 
                                                                                                   class="form-control form-control-sm" 
                                                                                                   name="lignes[{{ $rubrique->id }}_dqe_{{ $indexDqe }}][designation]" 
                                                                                                   value="{{ $ligneDqe->designation }}"
                                                                                                   required>
                                                                                        </div>
                                                                                        
                                                                        <div class="col-md-2">
                                                                            <label class="form-label">Unité <span class="text-danger">*</span></label>
                                                                            <input type="text" 
                                                                                   class="form-control form-control-sm" 
                                                                                   name="lignes[{{ $rubrique->id }}_dqe_{{ $indexDqe }}][unite]" 
                                                                                   value="{{ $ligneDqe->unite }}"
                                                                                   readonly
                                                                                   style="background-color: #e9ecef;"
                                                                                   required>
                                                                        </div>
                                                                                        
                                                                        <div class="col-md-2">
                                                                            <label class="form-label">Quantité <span class="text-danger">*</span></label>
                                                                            <input type="number" 
                                                                                   step="0.01" 
                                                                                   class="form-control form-control-sm quantite-input" 
                                                                                   name="lignes[{{ $rubrique->id }}_dqe_{{ $indexDqe }}][quantite]" 
                                                                                   value="{{ $ligneDqe->quantite }}"
                                                                                   readonly
                                                                                   style="background-color: #e9ecef;"
                                                                                   required>
                                                                        </div>
                                                                        
                                                                        <div class="col-md-2">
                                                                            <label class="form-label">Coût unitaire <span class="text-danger">*</span></label>
                                                                            <input type="number" 
                                                                                   step="0.01" 
                                                                                   class="form-control form-control-sm cout-input" 
                                                                                   name="lignes[{{ $rubrique->id }}_dqe_{{ $indexDqe }}][cout_unitaire]" 
                                                                                   value=""
                                                                                   onchange="calculerMontant({{ $rubrique->id }}, 'dqe_{{ $indexDqe }}')"
                                                                                   placeholder="Saisir le coût"
                                                                                   required>
                                                                        </div>
                                                                                        
                                                                        <div class="col-md-2">
                                                                            <label class="form-label">Montant</label>
                                                                            <input type="text" 
                                                                                   class="form-control form-control-sm montant-display" 
                                                                                   id="montant_{{ $rubrique->id }}_dqe_{{ $indexDqe }}" 
                                                                                   value=""
                                                                                   placeholder="0,00 FCFA"
                                                                                   readonly 
                                                                                   style="background-color: #e9ecef;">
                                                                        </div>
                                                                                        
                                                                                        <div class="col-md-1">
                                                                                            <button type="button" 
                                                                                                    class="btn btn-sm btn-danger" 
                                                                                                    onclick="supprimerLigne({{ $rubrique->id }}, 'dqe_{{ $indexDqe }}')">
                                                                                                <i class="fas fa-trash"></i>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                                
                                                                <h6 class="mb-3 mt-4">
                                                                    <i class="fas fa-plus-circle me-2 text-success"></i>
                                                                    Ajouter de nouvelles lignes
                                                                </h6>
                                                                
                                                                <div id="lignes_container_{{ $rubrique->id }}">
                                                                    <!-- Les nouvelles lignes seront ajoutées ici -->
                                                                </div>
                                                                
                                                                <button type="button" 
                                                                        class="btn btn-sm btn-success mt-2" 
                                                                        onclick="ajouterLigne({{ $rubrique->id }})">
                                                                    <i class="fas fa-plus me-1"></i>Ajouter une ligne
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2" class="text-muted fst-italic ps-5">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        Aucune rubrique dans cette sous-catégorie
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-muted fst-italic ps-4">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Aucune sous-catégorie dans cette catégorie
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Aucune catégorie trouvée pour ce DQE
                    </div>
                @endif
            </div>
        </div>

        <!-- Bouton Enregistrer -->
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i>Enregistrer les lignes de prestation
            </button>
        </div>
    </form>
    @else
    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucun DQE validé trouvé pour ce contrat. Veuillez d'abord valider un DQE.
            </div>
        </div>
    </div>
    @endif
</div>

<script>
// Compteur global pour les lignes
let ligneCounters = {};

// Unités de mesure disponibles
const unitesMesure = @json($unites ?? []);

// Toggle du formulaire de lignes
function toggleLignesForm(rubriqueId) {
    const checkbox = document.getElementById('rubrique_' + rubriqueId);
    const formRow = document.getElementById('lignes_form_' + rubriqueId);
    
    if (checkbox.checked) {
        formRow.style.display = 'table-row';
        // Ajouter une première ligne par défaut
        if (!ligneCounters[rubriqueId]) {
            ajouterLigne(rubriqueId);
        }
    } else {
        formRow.style.display = 'none';
        // Vider le container
        document.getElementById('lignes_container_' + rubriqueId).innerHTML = '';
        ligneCounters[rubriqueId] = 0;
    }
}

// Ajouter une ligne de prestation
function ajouterLigne(rubriqueId) {
    if (!ligneCounters[rubriqueId]) {
        ligneCounters[rubriqueId] = 0;
    }
    
    const lineIndex = ligneCounters[rubriqueId]++;
    const container = document.getElementById('lignes_container_' + rubriqueId);
    
    // Créer les options pour le select des unités
    let optionsUnites = '<option value="">-- Sélectionner --</option>';
    unitesMesure.forEach(unite => {
        optionsUnites += `<option value="${unite.nom}">${unite.nom}</option>`;
    });
    
    const ligneHtml = `
        <div class="ligne-prestation card mb-2" id="ligne_${rubriqueId}_${lineIndex}">
            <div class="card-body">
                <div class="row align-items-end">
                    <input type="hidden" name="lignes[${rubriqueId}_${lineIndex}][id_rubrique]" value="${rubriqueId}">
                    
                    <div class="col-md-3">
                        <label class="form-label">Désignation <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm" 
                               name="lignes[${rubriqueId}_${lineIndex}][designation]" 
                               required>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Unité <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" 
                                name="lignes[${rubriqueId}_${lineIndex}][unite]" 
                                required>
                            ${optionsUnites}
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Quantité <span class="text-danger">*</span></label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control form-control-sm quantite-input" 
                               name="lignes[${rubriqueId}_${lineIndex}][quantite]" 
                               onchange="calculerMontant(${rubriqueId}, ${lineIndex})"
                               required>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Coût unitaire <span class="text-danger">*</span></label>
                        <input type="number" 
                               step="0.01" 
                               class="form-control form-control-sm cout-input" 
                               name="lignes[${rubriqueId}_${lineIndex}][cout_unitaire]" 
                               onchange="calculerMontant(${rubriqueId}, ${lineIndex})"
                               required>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Montant</label>
                        <input type="text" 
                               class="form-control form-control-sm montant-display" 
                               id="montant_${rubriqueId}_${lineIndex}" 
                               readonly 
                               style="background-color: #e9ecef;">
                    </div>
                    
                    <div class="col-md-1">
                        <button type="button" 
                                class="btn btn-sm btn-danger" 
                                onclick="supprimerLigne(${rubriqueId}, ${lineIndex})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', ligneHtml);
}

// Calculer le montant automatiquement
function calculerMontant(rubriqueId, lineIndex) {
    const quantiteInput = document.querySelector(`input[name="lignes[${rubriqueId}_${lineIndex}][quantite]"]`);
    const coutInput = document.querySelector(`input[name="lignes[${rubriqueId}_${lineIndex}][cout_unitaire]"]`);
    const montantDisplay = document.getElementById(`montant_${rubriqueId}_${lineIndex}`);
    
    if (quantiteInput && coutInput && montantDisplay) {
        const quantite = parseFloat(quantiteInput.value) || 0;
        const cout = parseFloat(coutInput.value) || 0;
        const montant = quantite * cout;
        
        montantDisplay.value = montant.toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' FCFA';
    }
}

// Supprimer une ligne
function supprimerLigne(rubriqueId, lineIndex) {
    const ligne = document.getElementById(`ligne_${rubriqueId}_${lineIndex}`);
    if (ligne) {
        ligne.remove();
    }
}
</script>

@endsection
