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
            <a href="{{ route('dqe.index', $contrat->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLineModal">
                <i class="fas fa-plus-circle"></i> Ajouter une ligne
            </button>
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                <i class="fas fa-layer-group"></i> Créer une section
            </button>
            @if($dqe->statut == 'brouillon')
                <!-- Boutons de génération spécifique -->
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-calculator"></i> Générer déboursés
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <form action="{{ route('debourses.generate_sec', $dqe->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-cube"></i> Déboursé sec
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('debourses.generate_frais_chantier', $dqe->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-tools"></i> Frais de chantier
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('debourses.generate_chantier', $dqe->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-hard-hat"></i> Déboursé chantier
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('debourses.generate', $dqe->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-calculator"></i> Tous les déboursés
                                </button>
                            </form>
                        </li>
                    </ul>
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

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Informations générales</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dqe.update', $dqe->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reference">Référence</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference', $dqe->reference) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="statut">Statut</label>
                                    <select class="form-control" id="statut" name="statut">
                                        <option value="brouillon" {{ $dqe->statut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                        @if(in_array(Auth::user()->role, ['chef_projet', 'conducteur_travaux', 'admin', 'dg']) || $dqe->statut == 'validé')
                                            <option value="validé" {{ $dqe->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                        @endif
                                        <option value="archivé" {{ $dqe->statut == 'archivé' ? 'selected' : '' }}>Archivé</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $dqe->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('dqe.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour à la liste
                                </a>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card table-container">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Lignes du DQE</h5>
                    <div>
                        <span class="badge bg-primary">Montant total HT : {{ number_format($dqe->montant_total_ht, 2, ',', ' ') }} FCFA</span>
                        <span class="badge bg-success">Montant total TTC : {{ number_format($dqe->montant_total_ttc, 2, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                    <div class="table-responsive" style=" overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th style="min-width: 120px;"><input type="checkbox" id="select-all"> Section</th>
                                    <th style="min-width: 250px;">Désignation</th>
                                    <th style="min-width: 80px;">Unité</th>
                                    <th style="min-width: 100px;">Quantité</th>
                                    <th style="min-width: 120px;">Prix Unitaire HT</th>
                                    <th style="min-width: 120px;">Montant HT</th>
                                    <th style="min-width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($lignesOrganisees) && count($lignesOrganisees) > 0)
                                    @foreach($lignesOrganisees as $categorieNom => $categorieData)
                                        <!-- Affichage Catégorie -->
                                        <tr class="table-primary">
                                            <td colspan="7">
                                                <strong><i class="fas fa-folder"></i> {{ $categorieNom }}</strong>
                                            </td>
                                        </tr>
                                        
                                        @foreach($categorieData['sousCategories'] as $sousCategorieNom => $sousCategorieData)
                                            <!-- Affichage Sous-catégorie -->
                                            <tr class="table-info">
                                                <td colspan="7" style="padding-left: 30px;">
                                                    <strong><i class="fas fa-folder-open"></i> {{ $sousCategorieNom }}</strong>
                                                </td>
                                            </tr>
                                            
                                            @foreach($sousCategorieData['rubriques'] as $rubriqueNom => $rubriqueData)
                                                <!-- Affichage Rubrique -->
                                                <tr class="table-warning">
                                                    <td colspan="7" style="padding-left: 60px;">
                                                        <strong><i class="fas fa-list"></i> {{ $rubriqueNom }}</strong>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Affichage des lignes de cette rubrique -->
                                                @foreach($rubriqueData['lignes'] as $ligne)
                                                    <tr style="padding-left: 90px;">
                                                        <td style="padding-left: 90px; white-space: nowrap;">
                                                            <input type="checkbox" name="selected_lines[]" value="{{ $ligne->id }}" class="line-checkbox"> 
                                                            {{ $ligne->section ?? 'N/A' }}
                                                        </td>
                                                        <td style="word-wrap: break-word; max-width: 300px;">
                                                            @if($dqe->statut == 'brouillon')
                                                                <span class="editable-designation" data-id="{{ $ligne->id }}" data-value="{{ $ligne->designation }}" style="cursor: pointer; border-bottom: 1px dashed #007bff;" title="Cliquer pour modifier">
                                                                    {{ $ligne->designation }}
                                                                </span>
                                                            @else
                                                                {{ $ligne->designation }}
                                                            @endif
                                                        </td>
                                                        <td style="text-align: center;">{{ $ligne->unite }}</td>
                                                        <td style="text-align: center;">
                                                            @if($dqe->statut == 'brouillon')
                                                                <form action="{{ route('dqe.lines.update', [$dqe->id, $ligne->id]) }}" method="POST" class="d-flex justify-content-center">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center" name="quantite" value="{{ $ligne->quantite }}" style="width: 80px;">
                                                                    <button type="submit" class="btn btn-xs btn-outline-primary ms-1" title="Valider">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                </form>
                                                            @else
                                                                {{ $ligne->quantite }}
                                                            @endif
                                                        </td>
                                                        <td style="text-align: right; white-space: nowrap;">{{ number_format($ligne->pu_ht, 2, ',', ' ') }}</td>
                                                        <td style="text-align: right; white-space: nowrap; font-weight: bold;">{{ number_format($ligne->montant_ht, 2, ',', ' ') }}</td>
                                                        <td>
                                                            @if($dqe->statut == 'brouillon')
                                                                <div class="dropdown">
                                                                    <button class="btn btn-xs btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu">
                                                                        <li><a class="dropdown-item" href="#" onclick="editLine({{ $ligne->id }})"><i class="fas fa-edit"></i> Modifier</a></li>
                                                                        <li><a class="dropdown-item" href="#" onclick="duplicateLine({{ $ligne->id }})"><i class="fas fa-copy"></i> Dupliquer</a></li>
                                                                        <li><hr class="dropdown-divider"></li>
                                                                        <li>
                                                                            <form action="{{ route('dqe.lines.delete', [$dqe->id, $ligne->id]) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?');" class="d-inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash"></i> Supprimer</button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune ligne dans ce DQE.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    
                    
                    <!-- Actions groupées -->
                    <div class="mt-3" id="bulk-actions" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Actions groupées :</strong>
                            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="deleteSelectedLines()">Supprimer les lignes sélectionnées</button>
                            <button type="button" class="btn btn-sm btn-warning ms-2" onclick="changeSelectedSection()">Changer la section</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter une ligne -->
<div class="modal fade" id="addLineModal" tabindex="-1" aria-labelledby="addLineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLineModalLabel">Ajouter une ligne au DQE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="bpuAccordion">
                    @foreach($categories as $categorie)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $categorie->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $categorie->id }}" aria-expanded="false" aria-controls="collapse{{ $categorie->id }}">
                                    {{ $categorie->nom }}
                                </button>
                            </h2>
                            <div id="collapse{{ $categorie->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $categorie->id }}" data-bs-parent="#bpuAccordion">
                                <div class="accordion-body">
                                    @foreach($categorie->sousCategories as $sousCategorie)
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h5>{{ $sousCategorie->nom }}</h5>
                                            </div>
                                            <div class="card-body">
                                                @foreach($sousCategorie->rubriques as $rubrique)
                                                    <h6 class="mt-3">{{ $rubrique->nom }}</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr class="bg-light">
                                                                    <th>Désignation</th>
                                                                    <th>Unité</th>
                                                                    <th>Prix Unitaire HT</th>
                                                                    <th style="width: 200px;">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($rubrique->bpus as $bpu)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="checkbox" class="bpu-checkbox" data-bpu-id="{{ $bpu->id }}" data-designation="{{ $bpu->designation }}" data-unite="{{ $bpu->unite }}" data-pu-ht="{{ $bpu->pu_ht }}">
                                                                            {{ $bpu->designation }}
                                                                        </td>
                                                                        <td>{{ $bpu->unite }}</td>
                                                                        <td>{{ number_format($bpu->pu_ht, 2, ',', ' ') }}</td>
                                                                        <td>
                                                                            <form action="{{ route('dqe.lines.add', $dqe->id) }}" method="POST" class="d-flex">
                                                                                @csrf
                                                                                <input type="hidden" name="bpu_id" value="{{ $bpu->id }}">
                                                                                <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" name="quantite" value="1" placeholder="Qté">
                                                                                <button type="submit" class="btn btn-sm btn-primary ms-2">
                                                                                    <i class="fas fa-plus"></i> Ajouter
                                                                                </button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <button type="button" class="btn btn-outline-primary" id="selectAllBpu">Tout sélectionner</button>
                    <button type="button" class="btn btn-outline-secondary" id="deselectAllBpu">Tout désélectionner</button>
                </div>
                <div>
                    <button type="button" class="btn btn-success" id="addSelectedLines" disabled>
                        <i class="fas fa-plus"></i> Ajouter les lignes sélectionnées
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour créer une section -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectionModalLabel">Créer une nouvelle section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dqe.sections.create', $dqe->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="section_name" class="form-label">Nom de la section</label>
                        <input type="text" class="form-control" id="section_name" name="section_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="section_description" class="form-label">Description (optionnelle)</label>
                        <textarea class="form-control" id="section_description" name="section_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Gestion de la sélection multiple
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const lineCheckboxes = document.querySelectorAll('.line-checkbox');
    const bulkActions = document.getElementById('bulk-actions');

    // Sélectionner/désélectionner tout
    selectAllCheckbox.addEventListener('change', function() {
        lineCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActions();
    });

    // Gérer la sélection individuelle
    lineCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.line-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === lineCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < lineCheckboxes.length;
            toggleBulkActions();
        });
    });

    function toggleBulkActions() {
        const checkedBoxes = document.querySelectorAll('.line-checkbox:checked');
        bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }
});

// Supprimer les lignes sélectionnées
function deleteSelectedLines() {
    const checkedBoxes = document.querySelectorAll('.line-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Veuillez sélectionner au moins une ligne.');
        return;
    }
    
    if (confirm('Êtes-vous sûr de vouloir supprimer les ' + checkedBoxes.length + ' ligne(s) sélectionnée(s) ?')) {
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        // Ici, vous pouvez implémenter l'appel AJAX pour supprimer les lignes
        console.log('Supprimer les lignes:', ids);
    }
}

// Changer la section des lignes sélectionnées
function changeSelectedSection() {
    const checkedBoxes = document.querySelectorAll('.line-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Veuillez sélectionner au moins une ligne.');
        return;
    }
    
    const newSection = prompt('Entrez le nom de la nouvelle section:');
    if (newSection) {
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        // Ici, vous pouvez implémenter l'appel AJAX pour changer la section
        console.log('Changer la section pour les lignes:', ids, 'vers:', newSection);
    }
}

// Édition en ligne des désignations
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.editable-designation').forEach(function(element) {
        element.addEventListener('click', function() {
            const currentValue = this.dataset.value;
            const lineId = this.dataset.id;
            
            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentValue;
            input.className = 'form-control form-control-sm';
            input.style.width = '100%';
            
            const saveBtn = document.createElement('button');
            saveBtn.innerHTML = '<i class="fas fa-check"></i>';
            saveBtn.className = 'btn btn-xs btn-success ms-1';
            saveBtn.type = 'button';
            
            const cancelBtn = document.createElement('button');
            cancelBtn.innerHTML = '<i class="fas fa-times"></i>';
            cancelBtn.className = 'btn btn-xs btn-secondary ms-1';
            cancelBtn.type = 'button';
            
            const container = document.createElement('div');
            container.className = 'd-flex align-items-center';
            container.appendChild(input);
            container.appendChild(saveBtn);
            container.appendChild(cancelBtn);
            
            this.parentNode.replaceChild(container, this);
            input.focus();
            
            const self = this;
            
            function restore() {
                container.parentNode.replaceChild(self, container);
            }
            
            function save() {
                const newValue = input.value.trim();
                if (newValue && newValue !== currentValue) {
                    // Ici, vous pouvez implémenter l'appel AJAX pour sauvegarder
                    self.textContent = newValue;
                    self.dataset.value = newValue;
                    console.log('Sauvegarder designation:', lineId, newValue);
                }
                restore();
            }
            
            saveBtn.addEventListener('click', save);
            cancelBtn.addEventListener('click', restore);
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') save();
                if (e.key === 'Escape') restore();
            });
        });
    });
});

// Modifier une ligne
function editLine(lineId) {
    console.log('Modifier la ligne:', lineId);
    // Ici, vous pouvez ouvrir un modal d'édition
}

// Gestion de la sélection multiple pour l'ajout de lignes
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAllBpu');
    const deselectAllBtn = document.getElementById('deselectAllBpu');
    const addSelectedBtn = document.getElementById('addSelectedLines');
    const bpuCheckboxes = document.querySelectorAll('.bpu-checkbox');
    
    // Fonction pour mettre à jour l'état du bouton d'ajout
    function updateAddButton() {
        const selectedCheckboxes = document.querySelectorAll('.bpu-checkbox:checked');
        addSelectedBtn.disabled = selectedCheckboxes.length === 0;
        addSelectedBtn.innerHTML = selectedCheckboxes.length > 0 
            ? `<i class="fas fa-plus"></i> Ajouter ${selectedCheckboxes.length} ligne(s) sélectionnée(s)`
            : '<i class="fas fa-plus"></i> Ajouter les lignes sélectionnées';
    }
    
    // Écouter les changements sur les checkboxes
    bpuCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateAddButton);
    });
    
    // Tout sélectionner
    selectAllBtn.addEventListener('click', function() {
        bpuCheckboxes.forEach(function(checkbox) {
            checkbox.checked = true;
        });
        updateAddButton();
    });
    
    // Tout désélectionner
    deselectAllBtn.addEventListener('click', function() {
        bpuCheckboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });
        updateAddButton();
    });
    
    // Ajouter les lignes sélectionnées
    addSelectedBtn.addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.bpu-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Veuillez sélectionner au moins une ligne à ajouter.');
            return;
        }
        
        // Ouvrir la modal de confirmation des quantités
        showQuantityModal(selectedCheckboxes);
    });
    
    // Fonction pour afficher la modal de saisie des quantités
    function showQuantityModal(selectedCheckboxes) {
        // Créer la modal
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'quantityModal';
        modal.setAttribute('tabindex', '-1');
        
        let modalContent = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Spécifier les quantités</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="multipleAddForm" action="{{ route('dqe.lines.addMultiple', $dqe->id) }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Désignation</th>
                                            <th>Unité</th>
                                            <th>Prix Unitaire HT</th>
                                            <th>Quantité</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
        
        selectedCheckboxes.forEach(function(checkbox, index) {
            modalContent += `
                <tr>
                    <td>${checkbox.dataset.designation}</td>
                    <td>${checkbox.dataset.unite}</td>
                    <td>${parseFloat(checkbox.dataset.puHt).toLocaleString('fr-FR', {minimumFractionDigits: 2})} FCFA</td>
                    <td>
                        <input type="hidden" name="bpus[${index}][bpu_id]" value="${checkbox.dataset.bpuId}">
                        <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" name="bpus[${index}][quantite]" value="1" required>
                    </td>
                </tr>`;
        });
        
        modalContent += `
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary" onclick="submitMultipleAdd()">Ajouter les lignes</button>
                    </div>
                </div>
            </div>`;
        
        modal.innerHTML = modalContent;
        document.body.appendChild(modal);
        
        // Afficher la modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Nettoyer après fermeture
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
    }
    
    // Fonction pour soumettre le formulaire d'ajout multiple
    window.submitMultipleAdd = function() {
        document.getElementById('multipleAddForm').submit();
    };
});

// Dupliquer une ligne
function duplicateLine(lineId) {
    if (confirm('Voulez-vous dupliquer cette ligne ?')) {
        console.log('Dupliquer la ligne:', lineId);
        // Ici, vous pouvez implémenter l'appel AJAX pour dupliquer
    }
}
</script>

@endsection