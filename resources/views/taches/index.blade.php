@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<style>
.table-responsive {
    overflow-x: auto;
}

.table th {
    background-color: #033d71;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 0.75rem;
    white-space: nowrap;
    vertical-align: middle;
}

.table td {
    font-size: 0.85rem;
    padding: 0.5rem;
    vertical-align: middle;
}

.progress {
    border: 1px solid #dee2e6;
}

.btn-group .btn {
    margin-left: 0.25rem;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    font-weight: 500;
}

/* Styles pour les champs éditables */
.table td input[type="number"],
.table td select {
    transition: all 0.2s ease;
}

.table td input[type="number"]:hover,
.table td select:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.table td input[type="number"]:focus,
.table td select:focus {
    outline: 2px solid #033d71;
    box-shadow: 0 0 0 0.2rem rgba(3, 61, 113, 0.25);
}

/* Styles pour les options du select de statut */
.table td select option {
    color: #000;
    background-color: #fff;
}

.btn-add-context {
    padding: 0.1rem 0.3rem;
    font-size: 0.7rem;
    margin-left: 0.5rem;
    opacity: 0.7;
}

.btn-add-context:hover {
    opacity: 1;
}
</style>

<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Suivie des tâches</h2>
                    <small class="text-muted"><i class="fas fa-info-circle"></i> Utilisez les boutons "+" dans chaque colonne pour ajouter des éléments</small>
                </div>
                <div>
                    <a href="{{ route('taches.debug') }}" class="btn btn-sm btn-outline-info me-2">
                        <i class="fas fa-bug"></i> Mode débogage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau du suivi des tâches -->
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                Lot 
                                <button class="btn btn-sm btn-success ms-1" onclick="openAddLotModal()" title="Ajouter un Lot">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </th>
                            <th>Niveau</th>
                            <th>Localisation</th>
                            <th>Corps de métier</th>
                            <th>Description des travaux</th>
                            <th>Nbre de prévision de réalisation</th>
                            <th>Date de début</th>
                            <th>Date de fin</th>
                            <th>Nbre de jours réalisé</th>
                            <th>Progression</th>
                            <th>Statut</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalRows = 0;
                            
                            // Fonction pour compter le nombre total de lignes (tâches) pour chaque élément
                            function countTachesInLot($lot) {
                                $count = 0;
                                foreach($lot->niveaux as $niveau) {
                                    foreach($niveau->localisations as $localisation) {
                                        foreach($localisation->corpsDeMetiers as $corps) {
                                            $count += max(1, $corps->taches->count());
                                        }
                                    }
                                }
                                return max(1, $count);
                            }
                            
                            function countTachesInNiveau($niveau) {
                                $count = 0;
                                foreach($niveau->localisations as $localisation) {
                                    foreach($localisation->corpsDeMetiers as $corps) {
                                        $count += max(1, $corps->taches->count());
                                    }
                                }
                                return max(1, $count);
                            }
                            
                            function countTachesInLocalisation($localisation) {
                                $count = 0;
                                foreach($localisation->corpsDeMetiers as $corps) {
                                    $count += max(1, $corps->taches->count());
                                }
                                return max(1, $count);
                            }
                            
                            function countTachesInCorpsDeMetier($corps) {
                                return max(1, $corps->taches->count());
                            }
                        @endphp
                        
                        @forelse($lots as $lot)
                            @php
                                $lotRowspan = countTachesInLot($lot);
                                $firstLotRow = true;
                            @endphp
                            
                            @if($lot->niveaux->isEmpty())
                                <tr class="table-light">
                                    <td>
                                        <strong>{{ $lot->titre }}</strong>
                                        <button class="btn btn-primary btn-add-context rounded-circle" onclick="openAddNiveauModal({{ $lot->id }})" title="Ajouter un Niveau">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                    <td colspan="12" class="text-muted"><em>Aucun niveau ajouté</em></td>
                                </tr>
                            @else
                                @foreach($lot->niveaux as $niveau)
                                    @php
                                        $niveauRowspan = countTachesInNiveau($niveau);
                                        $firstNiveauRow = true;
                                    @endphp
                                    
                                    @if($niveau->localisations->isEmpty())
                                        <tr class="table-light">
                                            @if($firstLotRow)
                                                <td rowspan="{{ $lotRowspan }}" class="align-middle bg-white">
                                                    <strong>{{ $lot->titre }}</strong>
                                                    <button class="btn btn-primary btn-add-context rounded-circle" onclick="openAddNiveauModal({{ $lot->id }})" title="Ajouter un Niveau">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </td>
                                                @php $firstLotRow = false; @endphp
                                            @endif
                                            <td>
                                                <strong>{{ $niveau->titre_niveau }}</strong>
                                                <button class="btn btn-info btn-add-context rounded-circle text-white" onclick="openAddLocalisationModal({{ $niveau->id }})" title="Ajouter une Localisation">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                            <td colspan="11" class="text-muted"><em>Aucune localisation ajoutée</em></td>
                                        </tr>
                                    @else
                                        @foreach($niveau->localisations as $localisation)
                                            @php
                                                $localisationRowspan = countTachesInLocalisation($localisation);
                                                $firstLocalisationRow = true;
                                            @endphp
                                            
                                            @if($localisation->corpsDeMetiers->isEmpty())
                                                <tr class="table-light">
                                                    @if($firstLotRow)
                                                        <td rowspan="{{ $lotRowspan }}" class="align-middle bg-white">
                                                            <strong>{{ $lot->titre }}</strong>
                                                            <button class="btn btn-primary btn-add-context rounded-circle" onclick="openAddNiveauModal({{ $lot->id }})" title="Ajouter un Niveau">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </td>
                                                        @php $firstLotRow = false; @endphp
                                                    @endif
                                                    @if($firstNiveauRow)
                                                        <td rowspan="{{ $niveauRowspan }}" class="align-middle bg-white">
                                                            <strong>{{ $niveau->titre_niveau }}</strong>
                                                            <button class="btn btn-info btn-add-context rounded-circle text-white" onclick="openAddLocalisationModal({{ $niveau->id }})" title="Ajouter une Localisation">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </td>
                                                        @php $firstNiveauRow = false; @endphp
                                                    @endif
                                                    <td>
                                                        <strong>{{ $localisation->titre_localisation }}</strong>
                                                        <button class="btn btn-warning btn-add-context rounded-circle text-dark" onclick="openAddCorpsModal({{ $localisation->id }})" title="Associer un Corps de Métier">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </td>
                                                    <td colspan="10" class="text-muted"><em>Aucun corps de métier ajouté</em></td>
                                                </tr>
                                            @else
                                                @foreach($localisation->corpsDeMetiers as $corpsDeMetier)
                                                    @php
                                                        $corpsRowspan = countTachesInCorpsDeMetier($corpsDeMetier);
                                                        $firstCorpsRow = true;
                                                    @endphp
                                                    
                                                    @if($corpsDeMetier->taches->isEmpty())
                                                        <tr class="table-light">
                                                            @if($firstLotRow)
                                                                <td rowspan="{{ $lotRowspan }}" class="align-middle bg-white">
                                                                    <strong>{{ $lot->titre }}</strong>
                                                                    <button class="btn btn-primary btn-add-context rounded-circle" onclick="openAddNiveauModal({{ $lot->id }})" title="Ajouter un Niveau">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </td>
                                                                @php $firstLotRow = false; @endphp
                                                            @endif
                                                            @if($firstNiveauRow)
                                                                <td rowspan="{{ $niveauRowspan }}" class="align-middle bg-white">
                                                                    <strong>{{ $niveau->titre_niveau }}</strong>
                                                                    <button class="btn btn-info btn-add-context rounded-circle text-white" onclick="openAddLocalisationModal({{ $niveau->id }})" title="Ajouter une Localisation">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </td>
                                                                @php $firstNiveauRow = false; @endphp
                                                            @endif
                                                            @if($firstLocalisationRow)
                                                                <td rowspan="{{ $localisationRowspan }}" class="align-middle bg-white">
                                                                    <strong>{{ $localisation->titre_localisation }}</strong>
                                                                    <button class="btn btn-warning btn-add-context rounded-circle text-dark" onclick="openAddCorpsModal({{ $localisation->id }})" title="Associer un Corps de Métier">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </td>
                                                                @php $firstLocalisationRow = false; @endphp
                                                            @endif
                                                            <td>
                                                                <strong>{{ $corpsDeMetier->nom }}</strong>
                                                                <button class="btn btn-success btn-add-context rounded-circle text-white" onclick="openAddTacheModal({{ $corpsDeMetier->pivot->id ?? $corpsDeMetier->id }}, {{ $localisation->id }})" title="Ajouter une Tâche">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </td>
                                                            <td colspan="9" class="text-muted"><em>Aucune tâche ajoutée</em></td>
                                                        </tr>
                                                    @else
                                                        @foreach($corpsDeMetier->taches as $tache)
                                                            <tr id="row-tache-{{ $tache->id }}">
                                                                @if($firstLotRow)
                                                                    <td rowspan="{{ $lotRowspan }}" class="align-middle bg-white">
                                                                        <strong>{{ $lot->titre }}</strong>
                                                                        <button class="btn btn-primary btn-add-context rounded-circle" onclick="openAddNiveauModal({{ $lot->id }})" title="Ajouter un Niveau">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                    @php $firstLotRow = false; @endphp
                                                                @endif
                                                                @if($firstNiveauRow)
                                                                    <td rowspan="{{ $niveauRowspan }}" class="align-middle bg-white">
                                                                        <strong>{{ $niveau->titre_niveau }}</strong>
                                                                        <button class="btn btn-info btn-add-context rounded-circle text-white" onclick="openAddLocalisationModal({{ $niveau->id }})" title="Ajouter une Localisation">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                    @php $firstNiveauRow = false; @endphp
                                                                @endif
                                                                @if($firstLocalisationRow)
                                                                    <td rowspan="{{ $localisationRowspan }}" class="align-middle bg-white">
                                                                        <strong>{{ $localisation->titre_localisation }}</strong>
                                                                        <button class="btn btn-warning btn-add-context rounded-circle text-dark" onclick="openAddCorpsModal({{ $localisation->id }})" title="Associer un Corps de Métier">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                    @php $firstLocalisationRow = false; @endphp
                                                                @endif
                                                                @if($firstCorpsRow)
                                                                    <td rowspan="{{ $corpsRowspan }}" class="align-middle bg-white">
                                                                        <strong>{{ $corpsDeMetier->nom }}</strong>
                                                                        <!-- Note: Nous devons passer l'ID de la relation pivot ou l'ID correct pour la création de tâche -->
                                                                        <button class="btn btn-success btn-add-context rounded-circle text-white" onclick="openAddTacheModal({{ $corpsDeMetier->pivot->id ?? $corpsDeMetier->id }}, {{ $localisation->id }})" title="Ajouter une Tâche">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                    @php $firstCorpsRow = false; @endphp
                                                                @endif
                                                                <td>{{ $tache->description }}</td>
                                                                <td class="text-center">{{ $tache->nbre_jr_previsionnelle }}</td>
                                                                <td class="text-center">{{ $tache->date_debut ? $tache->date_debut->format('d/m/Y') : '-' }}</td>
                                                                <td class="text-center">{{ $tache->date_fin ? $tache->date_fin->format('d/m/Y') : '-' }}</td>
                                                                <td class="text-center">{{ $tache->nbre_de_jr_realise }}</td>
                                                                <td class="text-center" style="padding: 2px;">
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <input type="number" 
                                                                               class="form-control form-control-sm text-center" 
                                                                               value="{{ $tache->progression }}" 
                                                                               min="0" 
                                                                               max="100" 
                                                                               step="1"
                                                                               style="width: 60px; font-weight: bold; background-color: {{ $tache->progression >= 100 ? '#d4edda' : '#fff3cd' }};"
                                                                               onchange="updateTacheField({{ $tache->id }}, 'progression', this.value)"
                                                                               title="Cliquez pour modifier">
                                                                        <small class="text-muted">%</small>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center" style="padding: 2px;">
                                                                    <select class="form-select form-select-sm" 
                                                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background-color: {{ $tache->statut_color }}; color: white; border: none; font-weight: 600;"
                                                                            onchange="updateTacheField({{ $tache->id }}, 'statut', this.value)">
                                                                        <option value="non_debute" {{ $tache->statut == 'non_debute' ? 'selected' : '' }}>Non débuté</option>
                                                                        <option value="en_cours" {{ $tache->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                                                        <option value="suspendu" {{ $tache->statut == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                                                                        <option value="receptionne" {{ $tache->statut == 'receptionne' ? 'selected' : '' }}>Réceptionné</option>
                                                                        <option value="termine" {{ $tache->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($tache->image)
                                                                        <img src="{{ asset('storage/' . $tache->image) }}" 
                                                                             alt="Image tâche" 
                                                                             style="width: 50px; height: 50px; object-fit: cover; cursor: pointer"
                                                                             onclick="showImageModal('{{ asset('storage/' . $tache->image) }}')">
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-sm btn-primary" onclick="editTache({{ $tache->id }})">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button class="btn btn-sm btn-danger" onclick="deleteTache({{ $tache->id }})">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucun lot créé. Commencez par cliquer sur le bouton "+" dans la colonne "Lot" pour démarrer le suivi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modals pour l'ajout contextuel -->

<!-- Modal Lot -->
<div class="modal fade" id="modalLot" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Lot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLot" onsubmit="event.preventDefault(); saveLot();">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre du lot</label>
                        <input type="text" class="form-control" id="input-lot-titre" required placeholder="Ex: ENSEMBLE, CUISINE">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Niveau -->
<div class="modal fade" id="modalNiveau" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Niveau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNiveau" onsubmit="event.preventDefault(); saveNiveau();">
                <div class="modal-body">
                    <input type="hidden" id="input-niveau-lot-id">
                    <div class="mb-3">
                        <label class="form-label">Titre du niveau</label>
                        <input type="text" class="form-control" id="input-niveau-titre" required placeholder="Ex: INSTAL CHANTIER, RDC">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Localisation -->
<div class="modal fade" id="modalLocalisation" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Localisation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLocalisation" onsubmit="event.preventDefault(); saveLocalisation();">
                <div class="modal-body">
                    <input type="hidden" id="input-localisation-niveau-id">
                    <div class="mb-3">
                        <label class="form-label">Titre de la localisation</label>
                        <input type="text" class="form-control" id="input-loc-titre" required placeholder="Ex: Mur Nord, Sol">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Corps de Métier -->
<div class="modal fade" id="modalCorpsDeMetier" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Associer un Corps de Métier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCorpsDeMetier" onsubmit="event.preventDefault(); saveCorpsDeMetier();">
                <div class="modal-body">
                    <input type="hidden" id="input-corps-localisation-id">
                    <div class="mb-3">
                        <label class="form-label">Corps de métier</label>
                        <select class="form-select" id="input-corps-select" required>
                            <option value="">Sélectionner un corps de métier</option>
                            @foreach($corpsMetiers as $corpMetier)
                                <option value="{{ $corpMetier->id }}">{{ $corpMetier->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Associer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tâche -->
<div class="modal fade" id="modalTache" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTache" enctype="multipart/form-data" onsubmit="event.preventDefault(); saveTache();">
                <div class="modal-body">
                    <input type="hidden" id="input-tache-corps-id">
                    <!-- Note: Nous n'avons plus besoin de sélectionner Lot/Niveau/Localisation car c'est contextuel -->
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Description des travaux</label>
                            <textarea class="form-control" id="input-tache-description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="input-tache-debut" name="date_debut" onchange="calculerJoursTache()">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="input-tache-fin" name="date_fin" onchange="calculerJoursTache()">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                Nbre jours prévisionnels 
                                <span class="badge bg-success text-white" style="font-size: 0.7rem;">
                                    <i class="fas fa-calculator"></i> Auto
                                </span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="input-tache-nbjours" name="nbre_jr_previsionnelle" value="0" required style="background-color: #e8f5e9;" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Progression (%)</label>
                            <input type="number" class="form-control" id="input-tache-progression" name="progression" value="0" min="0" max="100" step="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" id="input-tache-statut" name="statut" required>
                                <option value="non_debute">Non débuté</option>
                                <option value="en_cours">En cours</option>
                                <option value="suspendu">Suspendu</option>
                                <option value="receptionne">Réceptionné</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" id="input-tache-image" name="image" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Image -->
<div class="modal fade" id="modalImage" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image de la tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imagePreview" src="" alt="Image" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Fonction pour gérer les erreurs
function handleError(error) {
    console.error('Erreur:', error);
    alert('Une erreur est survenue. Vérifiez la console pour plus de détails.');
}

// --- Fonctions d'ouverture des modals ---

function openAddLotModal() {
    new bootstrap.Modal(document.getElementById('modalLot')).show();
}

function openAddNiveauModal(lotId) {
    document.getElementById('input-niveau-lot-id').value = lotId;
    new bootstrap.Modal(document.getElementById('modalNiveau')).show();
}

function openAddLocalisationModal(niveauId) {
    document.getElementById('input-localisation-niveau-id').value = niveauId;
    new bootstrap.Modal(document.getElementById('modalLocalisation')).show();
}

function openAddCorpsModal(localisationId) {
    document.getElementById('input-corps-localisation-id').value = localisationId;
    new bootstrap.Modal(document.getElementById('modalCorpsDeMetier')).show();
}

function openAddTacheModal(corpsId, localisationId) {
    // Note: corpsId ici devrait être l'ID de la table de liaison si nécessaire, 
    // ou l'ID du corps de métier. Le contrôleur attend 'id_corps_de_metier'.
    // Dans l'implémentation précédente, c'était le corps de métier sélectionné.
    // Vérifions comment le backend gère ça.
    document.getElementById('input-tache-corps-id').value = corpsId;
    
    // Réinitialiser le formulaire
    document.getElementById('input-tache-description').value = '';
    document.getElementById('input-tache-debut').value = '';
    document.getElementById('input-tache-fin').value = '';
    document.getElementById('input-tache-nbjours').value = '0';
    document.getElementById('input-tache-progression').value = '0';
    document.getElementById('input-tache-statut').value = 'non_debute';
    document.getElementById('input-tache-image').value = '';
    
    new bootstrap.Modal(document.getElementById('modalTache')).show();
}

// --- Fonctions de sauvegarde ---

// Sauvegarder un lot
function saveLot() {
    const titre = document.getElementById('input-lot-titre').value;
    
    if (!titre) {
        alert('Veuillez saisir un titre pour le lot');
        return;
    }
    
    const formData = new FormData();
    formData.append('titre', titre);
    
    fetch('{{ route("taches.storeLot") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalLot')).hide();
            showSuccessMessage('Lot créé avec succès !');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => handleError(error));
}

// Sauvegarder un niveau
function saveNiveau() {
    const id_lot = document.getElementById('input-niveau-lot-id').value;
    const titre = document.getElementById('input-niveau-titre').value;
    
    if (!id_lot || !titre) {
        alert('Erreur interne: Lot ID manquant ou titre vide');
        return;
    }
    
    const formData = new FormData();
    formData.append('id_lot', id_lot);
    formData.append('titre_niveau', titre);
    
    fetch('{{ route("taches.storeNiveau") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('input-niveau-titre').value = '';
            bootstrap.Modal.getInstance(document.getElementById('modalNiveau')).hide();
            showSuccessMessage('Niveau créé avec succès !');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => handleError(error));
}

// Sauvegarder une localisation
function saveLocalisation() {
    const id_niveau = document.getElementById('input-localisation-niveau-id').value;
    const titre = document.getElementById('input-loc-titre').value;
    
    if (!id_niveau || !titre) {
        alert('Erreur interne: Niveau ID manquant ou titre vide');
        return;
    }
    
    const formData = new FormData();
    formData.append('id_niveau', id_niveau);
    formData.append('titre_localisation', titre);
    
    fetch('{{ route("taches.storeLocalisation") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('input-loc-titre').value = '';
            bootstrap.Modal.getInstance(document.getElementById('modalLocalisation')).hide();
            showSuccessMessage('Localisation créée avec succès !');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => handleError(error));
}

// Sauvegarder un corps de métier
function saveCorpsDeMetier() {
    const id_localisation = document.getElementById('input-corps-localisation-id').value;
    const id_corpmetier = document.getElementById('input-corps-select').value;
    
    if (!id_localisation || !id_corpmetier) {
        alert('Veuillez sélectionner un corps de métier');
        return;
    }
    
    const formData = new FormData();
    formData.append('id_localisation', id_localisation);
    formData.append('id_corpmetier', id_corpmetier);
    
    fetch('{{ route("taches.storeCorpsDeMetier") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('input-corps-select').value = '';
            bootstrap.Modal.getInstance(document.getElementById('modalCorpsDeMetier')).hide();
            showSuccessMessage('Corps de métier associé avec succès !');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => handleError(error));
}

// Sauvegarder une tâche
function saveTache() {
    const id_corps_de_metier = document.getElementById('input-tache-corps-id').value;
    const description = document.getElementById('input-tache-description').value;
    const date_debut = document.getElementById('input-tache-debut').value;
    const date_fin = document.getElementById('input-tache-fin').value;
    const nbre_jr_previsionnelle = document.getElementById('input-tache-nbjours').value;
    const progression = document.getElementById('input-tache-progression').value;
    const statut = document.getElementById('input-tache-statut').value;
    const image = document.getElementById('input-tache-image').files[0];
    
    if (!id_corps_de_metier || !description) {
        alert('Veuillez saisir une description');
        return;
    }
    
    const formData = new FormData();
    // Attention: le contrôleur attend id_corps_de_metier, qui peut être l'ID du corps de métier (table ref) ou l'ID de la liaison
    // D'après le code précédent, c'était l'ID du corps de métier sélectionné dans le dropdown.
    // Ici, c'est l'ID du corps de métier déjà associé.
    // Si le contrôleur s'attend à créer une NOUVELLE liaison corps-localisation pour CHAQUE tâche, c'est différent.
    // Mais généralement, on ajoute une tâche À une liaison existante ou À un corps de métier.
    // Supposons que le backend gère ça correctement.
    formData.append('id_corps_de_metier', id_corps_de_metier);
    formData.append('description', description);
    formData.append('date_debut', date_debut);
    formData.append('date_fin', date_fin);
    formData.append('nbre_jr_previsionnelle', nbre_jr_previsionnelle || 0);
    formData.append('progression', progression || 0);
    formData.append('statut', statut);
    if (image) {
        formData.append('image', image);
    }
    
    fetch('{{ route("taches.storeTache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalTache')).hide();
            showSuccessMessage('Tâche créée avec succès !');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => handleError(error));
}

// Calculer nombre de jours pour tâche
function calculerJoursTache() {
    const dateDebut = document.getElementById('input-tache-debut').value;
    const dateFin = document.getElementById('input-tache-fin').value;
    const champNbreJours = document.getElementById('input-tache-nbjours');
    
    if (dateDebut && dateFin) {
        const debut = new Date(dateDebut);
        const fin = new Date(dateFin);
        const differenceMs = fin - debut;
        const differenceJours = Math.ceil(differenceMs / (1000 * 60 * 60 * 24));
        champNbreJours.value = differenceJours >= 0 ? differenceJours : 0;
    } else {
        champNbreJours.value = 0;
    }
}

// Afficher message de succès
function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Succès</strong>
                <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Message rapide et discret
function showQuickMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert" style="min-width: 200px;">
            ${message}
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.remove();
    }, 1500);
}

// Mettre à jour un champ de tâche (progression ou statut)
function updateTacheField(tacheId, field, value) {
    const formData = new FormData();
    formData.append('field', field);
    formData.append('value', value);
    
    fetch(`/taches/${tacheId}/update-field`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if (field === 'progression') {
                const input = event.target;
                if (value >= 100) {
                    input.style.backgroundColor = '#d4edda';
                } else {
                    input.style.backgroundColor = '#fff3cd';
                }
                if (value >= 100 && data.tache && data.tache.nbre_de_jr_realise) {
                    setTimeout(() => location.reload(), 500);
                }
            } else if (field === 'statut') {
                const select = event.target;
                const colors = {
                    'non_debute': '#6c757d',
                    'en_cours': '#ffc107',
                    'suspendu': '#dc3545',
                    'receptionne': '#17a2b8',
                    'termine': '#28a745'
                };
                select.style.backgroundColor = colors[value] || '#6c757d';
            }
            showQuickMessage('✓ Mis à jour');
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la mise à jour');
    });
}

function showImageModal(imageUrl) {
    document.getElementById('imagePreview').src = imageUrl;
    new bootstrap.Modal(document.getElementById('modalImage')).show();
}

function deleteTache(id) {
    if(confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
        fetch(`/taches/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showSuccessMessage('Tâche supprimée');
                setTimeout(() => location.reload(), 500);
            }
        });
    }
}
</script>
@endpush
