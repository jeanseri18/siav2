@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<style>
.table-container {
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.table .table-primary td {
    background-color: #cfe2ff !important;
    font-weight: 600;
}

.table .table-info td {
    background-color: #d1ecf1 !important;
    font-weight: 500;
}

.table .table-warning td {
    background-color: #fff3cd !important;
    font-weight: 500;
}

.editable-designation:hover {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
}

.form-control-sm {
    font-size: 0.875rem;
}

.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table th, .table td {
        padding: 0.5rem 0.25rem;
    }
}
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Édition du DQE</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
            <p>Référence : {{ $dqe->reference ?? 'Sans référence' }}</p>
        </div>
        <div class="col-md-6 text-end">
      
           
            @if($dqe->statut == 'brouillon')
                <!-- Boutons de génération spécifique -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" title="Générer déboursé sec" 
                            onclick="generateDebourseSec()" data-url="{{ route('debourse-sec.generate', $dqe) }}">
                        <i class="fas fa-calculator"></i> Déboursé sec
                    </button>
                    <button type="button" class="btn btn-outline-primary" title="Générer frais de chantier" 
                            onclick="generateFraisChantier()" data-url="{{ route('frais-chantier.generate', $dqe) }}">
                        <i class="fas fa-calculator"></i> Frais chantier
                    </button>
                    <button type="button" class="btn btn-outline-primary" title="Générer frais généraux" 
                            onclick="generateFraisGeneraux()" data-url="{{ route('frais-generaux.generate', $dqe) }}">
                        <i class="fas fa-calculator"></i> Frais généraux
                    </button>
                    <button type="button" class="btn btn-outline-primary" title="Générer bénéfice" 
                            onclick="generateLigneBenefice()" data-url="{{ route('ligne-benefice.generate', $dqe) }}">
                        <i class="fas fa-calculator"></i> Bénéfice
                    </button>
                    <button type="button" class="btn btn-outline-primary" title="Générer déboursé chantier" 
                            onclick="generateDebourseChantier()" data-url="{{ route('debourse-chantier.generate', $dqe) }}">
                        <i class="fas fa-calculator"></i> Déboursé chantier
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card table-container">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="dqeTable" class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th style="min-width: 80px;">Code</th>
                            <th style="min-width: 300px;">Désignation</th>
                            <th style="min-width: 80px; text-align: center;">Section</th>
                            <th style="min-width: 100px; text-align: center;">Unité</th>
                            <th style="min-width: 120px; text-align: right;">Quantité</th>
                            <th style="min-width: 120px; text-align: right;">PU HT</th>
                            <th style="min-width: 140px; text-align: right;">Montant HT</th>
                            <th style="min-width: 100px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td colspan="8">
                            <div id="nouvelleCategorieForm" style="display: none;">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="nomCategorie" placeholder="Nom de la catégorie" required>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="sauvegarderCategorie()">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="annulerCategorie()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="afficherCategorieForm()" title="Ajouter une catégorie" id="btnAjouterCategorie">
                                <i class="fas fa-plus"></i> Ajouter catégorie
                            </button>
                        </td>
                    </tr>
                        @forelse($lignesOrganisees as $categorieNom => $categorieData)
                            <!-- Calcul du sous-total pour cette catégorie -->
                            @php
                                $sousTotalCategorie = 0;
                                foreach($categorieData['sousCategories'] as $sousCategorieData) {
                                    foreach($sousCategorieData['rubriques'] as $rubriqueData) {
                                        foreach($rubriqueData['lignes'] as $ligne) {
                                            $sousTotalCategorie += $ligne->montant_ht;
                                        }
                                    }
                                }
                            @endphp
                            <!-- En-tête de catégorie -->
                            <tr class="table-primary">
                                <td colspan="6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="view-mode-categorie">
                                                <strong>{{ $categorieNom }}</strong>
                                                @if($categorieData['categorie'])
                                                    <small class="ms-2">(ID: {{ $categorieData['categorie']->id }})</small>
                                                @endif
                                            </span>
                                            <div class="edit-mode-categorie" style="display: none;">
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control" id="categorie_nom_{{ $categorieData['categorie']->id ?? 0 }}" value="{{ $categorieNom }}" placeholder="Nom de la catégorie">
                                                    <button type="button" class="btn btn-success btn-sm" onclick="saveCategorie({{ $categorieData['categorie']->id ?? 0 }})" title="Enregistrer">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEditCategorie({{ $categorieData['categorie']->id ?? 0 }})" title="Annuler">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-mode-categorie">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editCategorie({{ $categorieData['categorie']->id ?? 0 }})" title="Modifier la catégorie">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: right; white-space: nowrap; font-weight: bold; background-color: #e2e3e5;">
                                    Sous-total: {{ number_format($sousTotalCategorie, 2, ',', ' ') }} FCFA
                                </td>
                                <td style="text-align: center;">
                                    <!-- Cellule vide pour les actions -->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8">
                                    <div id="nouvelleSousCategorieForm_{{ $categorieData['categorie']->id ?? 0 }}" style="display: none;">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="nomSousCategorie_{{ $categorieData['categorie']->id ?? 0 }}" placeholder="Nom de la sous-catégorie" required>
                                            <input type="hidden" id="categorieId_{{ $categorieData['categorie']->id ?? 0 }}" value="{{ $categorieData['categorie']->id ?? 0 }}">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="sauvegarderSousCategorie({{ $categorieData['categorie']->id ?? 0 }})">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="annulerSousCategorie({{ $categorieData['categorie']->id ?? 0 }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="afficherSousCategorieForm('{{ $categorieNom }}', {{ $categorieData['categorie']->id ?? 0 }})" title="Ajouter une sous-catégorie" id="btnAjouterSousCategorie_{{ $categorieData['categorie']->id ?? 0 }}">
                                        <i class="fas fa-plus"></i> Ajouter sous-catégorie
                                    </button>
                                </td>

                            </tr>
                            
                            @foreach($categorieData['sousCategories'] as $sousCategorieNom => $sousCategorieData)
                                <!-- En-tête de sous-catégorie -->
                                <tr class="table-info">
                                    <td colspan="8">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="view-mode-souscategorie">
                                                    <strong>&nbsp;&nbsp;&nbsp;{{ $sousCategorieNom }}</strong>
                                                    @if($sousCategorieData['sousCategorie'])
                                                        <small class="ms-2">(ID: {{ $sousCategorieData['sousCategorie']->id }})</small>
                                                    @endif
                                                </span>
                                                <div class="edit-mode-souscategorie" style="display: none;">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control" id="souscategorie_nom_{{ $sousCategorieData['sousCategorie']->id ?? 0 }}" value="{{ $sousCategorieNom }}" placeholder="Nom de la sous-catégorie">
                                                        <button type="button" class="btn btn-success btn-sm" onclick="saveSousCategorie({{ $sousCategorieData['sousCategorie']->id ?? 0 }})" title="Enregistrer">
                                                            <i class="fas fa-save"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEditSousCategorie({{ $sousCategorieData['sousCategorie']->id ?? 0 }})" title="Annuler">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="view-mode-souscategorie">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editSousCategorie({{ $sousCategorieData['sousCategorie']->id ?? 0 }})" title="Modifier la sous-catégorie">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                      <!-- Formulaire pour ajouter une nouvelle rubrique -->
                                    <tr class="table-light">
                                        <td colspan="8">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text" class="form-control form-control-sm" id="nouvelle_rubrique_{{ isset($sousCategorieData['sousCategorie']) ? $sousCategorieData['sousCategorie']->id : '' }}" placeholder="Nouvelle rubrique..." style="max-width: 300px;">
                                                <button type="button" class="btn btn-sm btn-success" onclick="ajouterRubrique({{ isset($sousCategorieData['sousCategorie']) ? $sousCategorieData['sousCategorie']->id : 0 }}, this)">
                                                    <i class="fas fa-plus"></i> Ajouter Rubrique
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @foreach($sousCategorieData['rubriques'] as $rubriqueNom => $rubriqueData)
                                    <!-- En-tête de rubrique -->
                                    <tr class="table-warning">
                                        <td colspan="8">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $rubriqueNom }}</strong>
                                                    @if($rubriqueData['rubrique'])
                                                        <small class="ms-2">(ID: {{ $rubriqueData['rubrique']->id }})</small>
                                                    @endif
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="ouvrirSelectionBPU({{ $rubriqueData['rubrique']->id ?? 0 }}, '{{ $rubriqueNom }}')" title="Sélectionner des lignes BPU">
                                                        <i class="fas fa-plus"></i> Sélectionner BPU
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Lignes DQE pour cette rubrique -->
                                    @foreach($rubriqueData['lignes'] as $ligne)
                                        <tr data-ligne-id="{{ $ligne->id }}" data-rubrique-id="{{ $ligne->rubrique_id }}" class="ligne-row">
                                            <td>{{ $ligne->code }}</td>
                                            <td>
                                                <span class="view-mode">{{ $ligne->designation }}</span>
                                                <input type="text" class="form-control form-control-sm edit-mode" name="designation" value="{{ $ligne->designation }}" style="display: none;">
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="view-mode">{{ $ligne->section }}</span>
                                                <input type="text" class="form-control form-control-sm edit-mode" name="section" value="{{ $ligne->section }}" style="display: none;">
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="view-mode">{{ $ligne->unite }}</span>
                                                <input type="text" class="form-control form-control-sm edit-mode" name="unite" value="{{ $ligne->unite }}" style="display: none;">
                                            </td>
                                            <td style="text-align: right;">
                                                <span class="view-mode">{{ number_format($ligne->quantite, 2, ',', ' ') }}</span>
                                                <input type="number" class="form-control form-control-sm edit-mode" name="quantite" value="{{ $ligne->quantite }}" step="0.01" min="0" style="display: none;">
                                            </td>
                                            <td style="text-align: right; white-space: nowrap;">
                                                <span class="view-mode">{{ number_format($ligne->pu_ht, 2, ',', ' ') }} FCFA</span>
                                                <input type="number" class="form-control form-control-sm edit-mode" name="pu_ht" value="{{ $ligne->pu_ht }}" step="0.01" min="0" style="display: none;">
                                            </td>
                                            <td style="text-align: right; white-space: nowrap;">
                                                <span class="view-mode montant-value">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} FCFA</span>
                                                <input type="text" class="form-control form-control-sm edit-mode" value="{{ number_format($ligne->montant_ht, 2, ',', ' ') }} FCFA" readonly style="display: none;">
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="view-mode">
                                                    <button type="button" class="btn btn-sm btn-primary btn-xs" onclick="editLigneInline(this)" title="Éditer">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form action="{{ route('dqe.lignes.delete', $ligne->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger btn-xs" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="edit-mode" style="display: none;">
                                                    <button type="button" class="btn btn-sm btn-success btn-xs" onclick="saveLigne(this)" title="Enregistrer">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary btn-xs" onclick="cancelEdit(this)" title="Annuler">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucune ligne DQE trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <td colspan="7" style="text-align: right;"><strong>TOTAL HT:</strong></td>
                            <td style="text-align: right; white-space: nowrap;">
                                <strong>{{ number_format($dqe->montant_total_ht, 2, ',', ' ') }} FCFA</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let ligneEnCoursEdition = null;
let anciennesValeurs = {};

function editLigneInline(button) {
    const row = button.closest('tr');
    const ligneId = row.dataset.ligneId;
    
    // Si une ligne est déjà en édition, l'annuler
    if (ligneEnCoursEdition && ligneEnCoursEdition !== row) {
        cancelEdit(ligneEnCoursEdition.querySelector('.btn-secondary'));
    }
    
    ligneEnCoursEdition = row;
    anciennesValeurs[ligneId] = {};
    
    // Sauvegarder les valeurs actuelles
    row.querySelectorAll('.edit-mode').forEach(input => {
        anciennesValeurs[ligneId][input.name] = input.value;
    });
    
    // Passer en mode édition
    row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
    row.querySelectorAll('.edit-mode').forEach(el => el.style.display = '');
    
    // Focus sur le premier champ
    row.querySelector('input[name="designation"]').focus();
}

function saveLigne(button) {
    const row = button.closest('tr');
    const ligneId = row.dataset.ligneId;
    
    // Récupérer l'ID de la rubrique depuis les données de la ligne (optionnel)
    const rubriqueId = row.dataset.rubriqueId;
    
    // Collecter les données
    const data = {
        _token: '{{ csrf_token() }}',
        _method: 'PUT',
        designation: row.querySelector('input[name="designation"]').value,
        section: row.querySelector('input[name="section"]').value,
        unite: row.querySelector('input[name="unite"]').value,
        quantite: parseFloat(row.querySelector('input[name="quantite"]').value) || 0,
        pu_ht: parseFloat(row.querySelector('input[name="pu_ht"]').value) || 0
    };
    
    // Ajouter le rubrique_id seulement s'il est disponible et non nul
    if (rubriqueId && rubriqueId !== '0') {
        data.rubrique_id = rubriqueId;
    }
    
    // Validation
    if (!data.designation || !data.unite || data.quantite < 0 || data.pu_ht < 0) {
        alert('Veuillez remplir tous les champs correctement');
        return;
    }
    
    // Envoyer la requête AJAX
    fetch(`/dqe/lignes/${ligneId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Mettre à jour l'affichage avec les nouvelles valeurs
            const ligne = result.ligne;
            row.querySelector('.view-mode').textContent = ligne.designation;
            row.querySelector('input[name="designation"]').value = ligne.designation;
            
            row.querySelector('td:nth-child(3) .view-mode').textContent = ligne.section;
            row.querySelector('input[name="section"]').value = ligne.section;
            
            row.querySelector('td:nth-child(4) .view-mode').textContent = ligne.unite;
            row.querySelector('input[name="unite"]').value = ligne.unite;
            
            row.querySelector('td:nth-child(5) .view-mode').textContent = ligne.quantite.toFixed(2).replace('.', ',');
            row.querySelector('input[name="quantite"]').value = ligne.quantite;
            
            row.querySelector('td:nth-child(6) .view-mode').textContent = ligne.pu_ht.toFixed(2).replace('.', ',') + ' FCFA';
            row.querySelector('input[name="pu_ht"]').value = ligne.pu_ht;
            
            row.querySelector('td:nth-child(7) .view-mode').textContent = ligne.montant_ht.toFixed(2).replace('.', ',') + ' FCFA';
            row.querySelector('td:nth-child(7) .montant-value').textContent = ligne.montant_ht.toFixed(2).replace('.', ',') + ' FCFA';
            
            // Revenir en mode visualisation
            row.querySelectorAll('.view-mode').forEach(el => el.style.display = '');
            row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
            
            ligneEnCoursEdition = null;
            delete anciennesValeurs[ligneId];
            
            // Afficher un message de succès
            showAlert('Ligne modifiée avec succès', 'success');
            
            // Mettre à jour le total si nécessaire
            if (result.dqe && result.dqe.montant_total_ht) {
                updateTotalHT(result.dqe.montant_total_ht);
            }
            
            // Mettre à jour les sous-totaux par catégorie
            updateSousTotaux();
        } else {
            alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de la sauvegarde.');
    });
}

function cancelEdit(button) {
    const row = button.closest('tr');
    const ligneId = row.dataset.ligneId;
    
    // Restaurer les anciennes valeurs
    if (anciennesValeurs[ligneId]) {
        Object.keys(anciennesValeurs[ligneId]).forEach(fieldName => {
            const input = row.querySelector(`input[name="${fieldName}"]`);
            if (input) {
                input.value = anciennesValeurs[ligneId][fieldName];
            }
        });
    }
    
    // Revenir en mode visualisation
    row.querySelectorAll('.view-mode').forEach(el => el.style.display = '');
    row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
    
    ligneEnCoursEdition = null;
    delete anciennesValeurs[ligneId];
}

function updateTotalHT(newTotal) {
    const totalCell = document.querySelector('tfoot .table-primary td:last-child strong');
    if (totalCell) {
        // S'assurer que newTotal est un nombre
        const total = parseFloat(newTotal) || 0;
        totalCell.textContent = total.toFixed(2).replace('.', ',') + ' FCFA';
    }
}

// Fonction pour calculer et mettre à jour les sous-totaux par catégorie
function updateSousTotaux() {
    const categories = document.querySelectorAll('tr.table-primary');
    
    categories.forEach(categorieRow => {
        let sousTotal = 0;
        
        // Trouver toutes les lignes DQE appartenant à cette catégorie
        let currentRow = categorieRow.nextElementSibling;
        
        while (currentRow && !currentRow.classList.contains('table-primary')) {
            // Vérifier si c'est une ligne DQE (pas une ligne de formulaire ou d'en-tête)
            if (currentRow.classList.contains('ligne-row')) {
                const montantCell = currentRow.querySelector('td:nth-child(7) .montant-value');
                if (montantCell) {
                    const montantText = montantCell.textContent.replace(/[^0-9,-]/g, '').replace(',', '.');
                    const montant = parseFloat(montantText) || 0;
                    sousTotal += montant;
                }
            }
            currentRow = currentRow.nextElementSibling;
        }
        
        // Mettre à jour l'affichage du sous-total dans la cellule appropriée (colonne 7)
        const sousTotalCell = categorieRow.querySelector('td:nth-child(7)');
        if (sousTotalCell) {
            sousTotalCell.innerHTML = `<strong>Sous-total: ${sousTotal.toFixed(2).replace('.', ',')} FCFA</strong>`;
        }
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Supprimer l'alerte après 3 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

// Calcul automatique du montant HT lors de la modification
function setupInlineCalculations() {
    document.addEventListener('input', function(e) {
        if (e.target.matches('.edit-mode[name="quantite"], .edit-mode[name="pu_ht"]')) {
            const row = e.target.closest('tr');
            const quantite = parseFloat(row.querySelector('input[name="quantite"]').value) || 0;
            const pu_ht = parseFloat(row.querySelector('input[name="pu_ht"]').value) || 0;
            const montant = quantite * pu_ht;
            
            row.querySelector('input[name="montant_ht"]').value = montant.toFixed(2).replace('.', ',') + ' FCFA';
        }
    });
}

// Initialiser les calculs
setupInlineCalculations();

// Initialiser les sous-totaux au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    updateSousTotaux();
});

// Gestionnaire pour les suppressions de lignes (après soumission du formulaire)
document.addEventListener('DOMContentLoaded', function() {
    // Observer les changements dans le tableau pour mettre à jour les sous-totaux
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.removedNodes.length > 0) {
                // Une ligne a été supprimée, mettre à jour les sous-totaux
                setTimeout(updateSousTotaux, 100);
            }
        });
    });
    
    // Observer le tbody du tableau DQE
    const tbody = document.querySelector('#dqeTable tbody');
    if (tbody) {
        observer.observe(tbody, { childList: true, subtree: true });
    }
});

// Fonction pour afficher le formulaire d'ajout de catégorie
function afficherCategorieForm() {
    document.getElementById('nouvelleCategorieForm').style.display = '';
    document.getElementById('btnAjouterCategorie').style.display = 'none';
    document.getElementById('nomCategorie').focus();
}

// Fonction pour annuler l'ajout de catégorie
function annulerCategorie() {
    document.getElementById('nouvelleCategorieForm').style.display = 'none';
    document.getElementById('btnAjouterCategorie').style.display = '';
    document.getElementById('nomCategorie').value = '';
}

// Fonction pour afficher le formulaire d'ajout de sous-catégorie
function afficherSousCategorieForm(nomCategorie, categorieId) {
    const formId = 'nouvelleSousCategorieForm_' + categorieId;
    const btnId = 'btnAjouterSousCategorie_' + categorieId;
    
    document.getElementById(formId).style.display = '';
    document.getElementById(btnId).style.display = 'none';
    document.getElementById('nomSousCategorie_' + categorieId).focus();
}

// Fonction pour annuler l'ajout de sous-catégorie
function annulerSousCategorie(categorieId) {
    const formId = 'nouvelleSousCategorieForm_' + categorieId;
    const btnId = 'btnAjouterSousCategorie_' + categorieId;
    
    document.getElementById(formId).style.display = 'none';
    document.getElementById(btnId).style.display = '';
    document.getElementById('nomSousCategorie_' + categorieId).value = '';
}

// Fonction pour sauvegarder la nouvelle catégorie
function sauvegarderCategorie() {
    const nomCategorie = document.getElementById('nomCategorie').value.trim();
    
    if (!nomCategorie) {
        alert('Veuillez entrer un nom de catégorie');
        return;
    }
    
    // Envoyer la requête AJAX pour créer la catégorie DQE
    fetch('/contrats/{{ $contrat->id }}/categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nom: nomCategorie,
            id_qe: {{ $dqe->id }},
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Cacher le formulaire et réinitialiser
            annulerCategorie();
            
            // Afficher un message de succès
            showAlert('Catégorie ajoutée avec succès', 'success');
            
            // Recharger la page pour afficher la nouvelle catégorie
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de l\'ajout de la catégorie.');
    });
}

// Gérer la soumission du formulaire avec la touche Entrée
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && e.target.id === 'nomCategorie') {
        sauvegarderCategorie();
    }
    if (e.target.id && e.target.id.startsWith('nomSousCategorie_') && e.key === 'Enter') {
        const categorieId = e.target.id.replace('nomSousCategorie_', '');
        sauvegarderSousCategorie(categorieId);
    }
});

// Fonction pour sauvegarder la nouvelle sous-catégorie
function sauvegarderSousCategorie(categorieId) {
    const nomSousCategorie = document.getElementById('nomSousCategorie_' + categorieId).value.trim();
    
    if (!nomSousCategorie) {
        alert('Veuillez entrer un nom de sous-catégorie');
        return;
    }
    
    if (!categorieId || categorieId === '0') {
        alert('ID de catégorie invalide');
        return;
    }
    
    // Envoyer la requête AJAX pour créer la sous-catégorie DQE
    fetch('/contrats/{{ $contrat->id }}/sous-categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nom: nomSousCategorie,
            categorie_id: categorieId,
            id_qe: {{ $dqe->id }},
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Cacher le formulaire et réinitialiser
            annulerSousCategorie(categorieId);
            
            // Afficher un message de succès
            showAlert('Sous-catégorie ajoutée avec succès', 'success');
            
            // Recharger la page pour afficher la nouvelle sous-catégorie
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de l\'ajout de la sous-catégorie.');
    });
}

// Fonction pour afficher des alertes
function showAlert(message, type = 'info') {
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Supprimer l'alerte après 3 secondes
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 3000);
}

// Fonctions pour l'édition des catégories
function editCategorie(categorieId) {
    // Masquer le mode visualisation et afficher le mode édition
    const row = document.querySelector(`#categorie_nom_${categorieId}`).closest('tr');
    row.querySelector('.view-mode-categorie').style.display = 'none';
    row.querySelector('.edit-mode-categorie').style.display = '';
    
    // Focus sur le champ de saisie
    document.getElementById(`categorie_nom_${categorieId}`).focus();
}

function cancelEditCategorie(categorieId) {
    // Afficher le mode visualisation et masquer le mode édition
    const row = document.querySelector(`#categorie_nom_${categorieId}`).closest('tr');
    row.querySelector('.view-mode-categorie').style.display = '';
    row.querySelector('.edit-mode-categorie').style.display = 'none';
    
    // Réinitialiser la valeur du champ
    const currentNom = row.querySelector('.view-mode-categorie strong').textContent;
    document.getElementById(`categorie_nom_${categorieId}`).value = currentNom;
}

function saveCategorie(categorieId) {
    const newNom = document.getElementById(`categorie_nom_${categorieId}`).value.trim();
    
    if (!newNom) {
        alert('Veuillez entrer un nom de catégorie');
        return;
    }
    
    // Envoyer la requête AJAX pour mettre à jour la catégorie
    fetch(`/dqe/categories/${categorieId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nom: newNom,
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Mettre à jour l'affichage avec le nouveau nom
            const row = document.querySelector(`#categorie_nom_${categorieId}`).closest('tr');
            row.querySelector('.view-mode-categorie strong').textContent = newNom;
            
            // Revenir en mode visualisation
            cancelEditCategorie(categorieId);
            
            // Afficher un message de succès
            showAlert('Catégorie modifiée avec succès', 'success');
        } else {
            alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de la modification de la catégorie.');
    });
}

// Fonctions pour l'édition des sous-catégories
function editSousCategorie(sousCategorieId) {
    // Masquer le mode visualisation et afficher le mode édition
    const row = document.querySelector(`#souscategorie_nom_${sousCategorieId}`).closest('tr');
    row.querySelector('.view-mode-souscategorie').style.display = 'none';
    row.querySelector('.edit-mode-souscategorie').style.display = '';
    
    // Focus sur le champ de saisie
    document.getElementById(`souscategorie_nom_${sousCategorieId}`).focus();
}

function cancelEditSousCategorie(sousCategorieId) {
    // Afficher le mode visualisation et masquer le mode édition
    const row = document.querySelector(`#souscategorie_nom_${sousCategorieId}`).closest('tr');
    row.querySelector('.view-mode-souscategorie').style.display = '';
    row.querySelector('.edit-mode-souscategorie').style.display = 'none';
    
    // Réinitialiser la valeur du champ
    const currentNom = row.querySelector('.view-mode-souscategorie strong').textContent.trim();
    document.getElementById(`souscategorie_nom_${sousCategorieId}`).value = currentNom;
}

function saveSousCategorie(sousCategorieId) {
    const newNom = document.getElementById(`souscategorie_nom_${sousCategorieId}`).value.trim();
    
    if (!newNom) {
        alert('Veuillez entrer un nom de sous-catégorie');
        return;
    }
    
    // Récupérer le categorie_id depuis la ligne de catégorie parente
    const sousCategorieRow = document.querySelector(`#souscategorie_nom_${sousCategorieId}`).closest('tr');
    let categorieId = null;
    
    // Chercher la ligne de catégorie parente en remontant dans le DOM
    let currentRow = sousCategorieRow.previousElementSibling;
    while (currentRow) {
        // Vérifier si c'est une ligne de catégorie (pas de sous-catégorie ou rubrique)
        const hiddenCategorieId = currentRow.querySelector('input[id^="categorieId_"]');
        const categorieInput = currentRow.querySelector('input[id^="categorie_nom_"]');
        
        if (hiddenCategorieId && hiddenCategorieId.value && hiddenCategorieId.value !== '0') {
            categorieId = hiddenCategorieId.value;
            break;
        } else if (categorieInput && categorieInput.id) {
            const extractedId = categorieInput.id.replace('categorie_nom_', '');
            if (extractedId && extractedId !== '0') {
                categorieId = extractedId;
                break;
            }
        }
        
        currentRow = currentRow.previousElementSibling;
    }
    
    if (!categorieId || categorieId === '0') {
        alert('Impossible de trouver l\'ID de la catégorie parente');
        return;
    }
    
    // Envoyer la requête AJAX pour mettre à jour la sous-catégorie
    fetch(`/dqe/sous-categories/${sousCategorieId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nom: newNom,
            categorie_id: categorieId,
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Mettre à jour l'affichage avec le nouveau nom
            const row = document.querySelector(`#souscategorie_nom_${sousCategorieId}`).closest('tr');
            row.querySelector('.view-mode-souscategorie strong').textContent = newNom;
            
            // Revenir en mode visualisation
            cancelEditSousCategorie(sousCategorieId);
            
            // Afficher un message de succès
            showAlert('Sous-catégorie modifiée avec succès', 'success');
        } else {
            alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de la modification de la sous-catégorie.');
    });
}

// Fonction pour ajouter une nouvelle rubrique
function ajouterRubrique(sousCategorieId, button) {
    // Vérifier que la sous-catégorie existe
    if (!sousCategorieId || sousCategorieId === 0) {
        alert('Impossible d\'ajouter une rubrique: sous-catégorie invalide');
        return;
    }
    
    const input = document.getElementById(`nouvelle_rubrique_${sousCategorieId}`);
    if (!input) {
        alert('Erreur: champ de saisie non trouvé');
        return;
    }
    
    const nomRubrique = input.value.trim();
    
    if (!nomRubrique) {
        alert('Veuillez entrer un nom de rubrique');
        return;
    }
    
    // Récupérer le contrat_id depuis la session ou le contexte
    const contratId = {{ session('contrat_id', 'null') }};
    if (!contratId) {
        alert('Aucun contrat sélectionné');
        return;
    }
    
    // Récupérer le DQE ID depuis le contexte
    const dqeId = {{ $dqe->id ?? 'null' }};
    
    // Récupérer le sous-catégorie_id depuis le contexte
    const data = {
        nom: nomRubrique,
        sous_categorie_id: sousCategorieId,
        id_qe: dqeId,
        _token: '{{ csrf_token() }}'
    };
    
    // Envoyer la requête AJAX pour créer la nouvelle rubrique
    console.log('Création de rubrique avec données:', data);
    
    fetch(`/contrats/${contratId}/rubriques`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Réinitialiser le champ
            input.value = '';
            
            // Afficher un message de succès
            showAlert('Rubrique ajoutée avec succès', 'success');
            
            // Recharger la page pour afficher la nouvelle rubrique
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 1000);
        } else {
            alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de l\'ajout de la rubrique.');
    });
}

// Fonction pour ouvrir la sélection BPU
function ouvrirSelectionBPU(rubriqueId, rubriqueNom) {
    if (!rubriqueId || rubriqueId === 0) {
        alert('Rubrique invalide');
        return;
    }
    
    // Stocker les informations de la rubrique
    window.currentRubriqueId = rubriqueId;
    window.currentRubriqueNom = rubriqueNom;
    
    // Charger les lignes BPU du contrat
    chargerLignesBPU();
    
    // Afficher la modal
    const modal = new bootstrap.Modal(document.getElementById('modalSelectionBPU'));
    modal.show();
}

// Fonction pour charger les lignes BPU du contrat
function chargerLignesBPU() {
    const contratId = {{ session('contrat_id', 'null') }};
    if (!contratId) {
        alert('Aucun contrat sélectionné');
        return;
    }
    
    fetch(`/contrats/${contratId}/bpu/lignes`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                afficherLignesBPU(data.lignes);
            } else {
                alert('Erreur lors du chargement des lignes BPU');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du chargement des lignes BPU');
        });
}

// Fonction pour afficher les lignes BPU dans la modal groupées par catégorie, sous-catégorie et rubrique
function afficherLignesBPU(lignes) {
    const tbody = document.querySelector('#modalSelectionBPU tbody');
    tbody.innerHTML = '';
    
    if (!lignes || lignes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucune ligne BPU disponible</td></tr>';
        return;
    }
    
    // Grouper les lignes par catégorie, sous-catégorie et rubrique
    const groupedLignes = {};
    
    lignes.forEach(ligne => {
        const categorieNom = ligne.categorie ? ligne.categorie.nom : 'Sans catégorie';
        const sousCategorieNom = ligne.sous_categorie ? ligne.sous_categorie.nom : 'Sans sous-catégorie';
        const rubriqueNom = ligne.rubrique ? ligne.rubrique.nom : 'Sans rubrique';
        
        if (!groupedLignes[categorieNom]) {
            groupedLignes[categorieNom] = {};
        }
        if (!groupedLignes[categorieNom][sousCategorieNom]) {
            groupedLignes[categorieNom][sousCategorieNom] = {};
        }
        if (!groupedLignes[categorieNom][sousCategorieNom][rubriqueNom]) {
            groupedLignes[categorieNom][sousCategorieNom][rubriqueNom] = [];
        }
        
        groupedLignes[categorieNom][sousCategorieNom][rubriqueNom].push(ligne);
    });
    
    // Afficher les lignes groupées
    Object.keys(groupedLignes).forEach(categorieNom => {
        // En-tête de catégorie
        const categorieRow = document.createElement('tr');
        categorieRow.className = 'table-info';
        categorieRow.innerHTML = `
            <td colspan="6" style="background-color: #d1ecf1; font-weight: bold;">
                <i class="fas fa-folder"></i> ${categorieNom}
            </td>
        `;
        tbody.appendChild(categorieRow);
        
        Object.keys(groupedLignes[categorieNom]).forEach(sousCategorieNom => {
            // En-tête de sous-catégorie
            const sousCategorieRow = document.createElement('tr');
            sousCategorieRow.className = 'table-light';
            sousCategorieRow.innerHTML = `
                <td colspan="6" style="padding-left: 30px; font-style: italic;">
                    <i class="fas fa-folder-open"></i> ${sousCategorieNom}
                </td>
            `;
            tbody.appendChild(sousCategorieRow);
            
            Object.keys(groupedLignes[categorieNom][sousCategorieNom]).forEach(rubriqueNom => {
                // En-tête de rubrique
                const rubriqueRow = document.createElement('tr');
                rubriqueRow.className = 'table-warning';
                rubriqueRow.innerHTML = `
                    <td colspan="6" style="padding-left: 60px; font-size: 0.9em;">
                        <i class="fas fa-tag"></i> ${rubriqueNom}
                    </td>
                `;
                tbody.appendChild(rubriqueRow);
                
                // Lignes BPU pour cette rubrique
                groupedLignes[categorieNom][sousCategorieNom][rubriqueNom].forEach(ligne => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="checkbox" class="form-check-input ligne-bpu-checkbox" data-ligne-id="${ligne.id}"></td>
                        <td style="padding-left: 80px;">${ligne.designation || ''}</td>
                        <td>${ligne.unite || ''}</td>
                        <td>${ligne.prix_unitaire ? parseFloat(ligne.prix_unitaire).toFixed(2) : '0.00'}</td>
                        <td>${ligne.quantite ? parseFloat(ligne.quantite).toFixed(2) : '0.00'}</td>
                        <td>${ligne.categorie ? ligne.categorie.nom : ''}</td>
                    `;
                    tbody.appendChild(row);
                });
            });
        });
    });
}

// Fonction pour sélectionner/désélectionner toutes les lignes
function toggleAllLignesBPU() {
    const masterCheckbox = document.getElementById('selectAllBPU');
    const checkboxes = document.querySelectorAll('.ligne-bpu-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = masterCheckbox.checked;
    });
}

// Fonction pour créer les lignes DQE à partir des lignes BPU sélectionnées
function creerLignesDQEDepuisBPU() {
    const selectedCheckboxes = document.querySelectorAll('.ligne-bpu-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Veuillez sélectionner au moins une ligne BPU');
        return;
    }
    
    const lignesBPUIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.ligneId);
    const dqeId = {{ $dqe->id ?? 'null' }};
    const rubriqueId = window.currentRubriqueId;
    
    if (!dqeId || !rubriqueId) {
        alert('Paramètres manquants');
        return;
    }
    
    // Envoyer la requête pour créer les lignes DQE
    fetch(`/dqe/${dqeId}/lignes/depuis-bpu`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            lignes_bpu_ids: lignesBPUIds,
            rubrique_id: rubriqueId,
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Fermer la modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalSelectionBPU'));
            modal.hide();
            
            // Afficher un message de succès
            showAlert(`${result.lignes_creees} ligne(s) DQE créée(s) avec succès`, 'success');
            
            // Recharger la page pour afficher les nouvelles lignes
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 1500);
        } else {
            alert('Erreur: ' + (result.message || 'Une erreur est survenue'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la création des lignes DQE');
    });
}
</script>

<!-- Modal de sélection BPU -->
<div class="modal fade" id="modalSelectionBPU" tabindex="-1" aria-labelledby="modalSelectionBPULabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSelectionBPULabel">
                    Sélectionner des lignes BPU pour la rubrique: <span id="rubriqueNomModal"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" class="form-check-input" id="selectAllBPU" onchange="toggleAllLignesBPU()">
                                </th>
                                <th>Désignation</th>
                                <th>Unité</th>
                                <th>Prix Unitaire</th>
                                <th>Quantité</th>
                                <th>Catégorie</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les lignes BPU seront chargées ici -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="creerLignesDQEDepuisBPU()">
                    <i class="fas fa-plus"></i> Créer les lignes DQE
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Mettre à jour le nom de la rubrique dans la modal
document.getElementById('modalSelectionBPU').addEventListener('show.bs.modal', function (event) {
    document.getElementById('rubriqueNomModal').textContent = window.currentRubriqueNom || '';
});

// Fonctions pour générer les données
function generateDebourseSec() {
    const button = event.target.closest('button');
    const url = button.dataset.url;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la génération des déboursés secs');
    });
}

function generateFraisChantier() {
    const button = event.target.closest('button');
    const url = button.dataset.url;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la génération des frais de chantier');
    });
}

function generateFraisGeneraux() {
    const button = event.target.closest('button');
    const url = button.dataset.url;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la génération des frais généraux');
    });
}

function generateLigneBenefice() {
    const button = event.target.closest('button');
    const url = button.dataset.url;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la génération des lignes de bénéfice');
    });
}

function generateDebourseChantier() {
    const button = event.target.closest('button');
    const url = button.dataset.url;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => {
        // Vérifier si la réponse est JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                throw new Error('Réponse non-JSON reçue: ' + text.substring(0, 200));
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Rediriger vers l'URL fournie dans la réponse
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                location.reload();
            }
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la génération du déboursé chantier: ' + error.message);
    });
}

// Fonction pour afficher les alertes
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
}
</script>
@endsection