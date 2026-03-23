@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<style>
.planning-container {
    overflow-x: auto;
    margin-top: 20px;
}

.planning-table {
    min-width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.planning-table th,
.planning-table td {
    border: 1px solid #dee2e6;
    padding: 6px 4px;
    font-size: 0.8rem;
}

.planning-table thead th {
    background-color: #495057;
    color: white;
    font-weight: 600;
    position: sticky;
    top: 0;
    z-index: 10;
    text-align: center;
    font-size: 0.75rem;
}

.planning-table .task-col {
    background-color: #f8f9fa;
    font-weight: 500;
    min-width: 250px;
    max-width: 250px;
    position: sticky;
    left: 0;
    z-index: 5;
    font-size: 0.8rem;
}

.planning-table .date-col {
    background-color: #fff;
    min-width: 35px;
    max-width: 35px;
    width: 35px;
    text-align: center;
    padding: 4px 2px;
    font-size: 0.75rem;
}

.planning-table .week-header {
    background-color: #0d6efd;
    color: white;
    text-align: center;
    font-weight: bold;
    font-size: 0.7rem;
}

.planning-table .month-header {
    background-color: #6c757d;
    color: white;
    text-align: center;
    font-weight: bold;
    font-size: 0.75rem;
}

.task-bar {
    height: 24px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.65rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.task-bar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.task-bar.non_demarre {
    background-color: #6c757d;
}

.task-bar.en_cours {
    background-color: #0dcaf0;
}

.task-bar.retard {
    background-color: #dc3545;
}

.task-bar.termine {
    background-color: #198754;
}

.category-row {
    background-color: #17a2b8 !important;
    color: white;
    font-weight: bold;
}

.subcategory-row {
    background-color: #e9ecef;
    font-weight: 600;
    padding-left: 20px !important;
}

.task-row:hover {
    background-color: #f1f3f5;
}

.add-task-row {
    background-color: #fff3cd;
}

.weekend-col {
    background-color: #ffe5e5 !important;
}

.btn-add-planning {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

.planning-container {
    max-height: 80vh;
    overflow: auto;
}
</style>

<div class="container-fluid mt-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-calendar-alt"></i> Planning du Projet</h2>
                <div>
                    <button class="btn btn-primary" onclick="toggleAddTaskRow()">
                        <i class="fas fa-plus"></i> Ajouter une tâche
                    </button>
                    <button class="btn btn-info" onclick="exportPlanning()">
                        <i class="fas fa-file-export"></i> Exporter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="planning-container">
        <table class="planning-table table table-bordered table-sm">
            <thead>
                <!-- Ligne des mois -->
                <tr>
                    <th class="task-col">TÂCHE</th>
                    <th class="month-header" style="min-width: 80px; max-width: 80px;">DÉBUT</th>
                    <th class="month-header" style="min-width: 80px; max-width: 80px;">FIN</th>
                    @php
                        $moisActuel = '';
                        $compteurMois = 0;
                        $colonnesMois = [];
                    @endphp
                    @foreach($jours as $jour)
                        @if($jour['moisChange'])
                            @if($compteurMois > 0)
                                @php
                                    $colonnesMois[] = ['mois' => strtoupper($moisActuel), 'count' => $compteurMois];
                                @endphp
                            @endif
                            @php
                                $moisActuel = $jour['mois'];
                                $compteurMois = 1;
                            @endphp
                        @else
                            @php $compteurMois++; @endphp
                        @endif
                    @endforeach
                    @if($compteurMois > 0)
                        @php
                            $colonnesMois[] = ['mois' => strtoupper($moisActuel), 'count' => $compteurMois];
                        @endphp
                    @endif
                    @foreach($colonnesMois as $moisCol)
                        <th colspan="{{ $moisCol['count'] }}" class="month-header">{{ $moisCol['mois'] }}</th>
                    @endforeach
                </tr>
                <!-- Ligne des semaines et jours -->
                <tr>
                    <th class="task-col"></th>
                    <th style="min-width: 80px; max-width: 80px;"></th>
                    <th style="min-width: 80px; max-width: 80px;"></th>
                    @foreach($jours as $jour)
                        <th class="date-col {{ $jour['isWeekend'] ? 'weekend-col' : '' }}" 
                            title="{{ $jour['date']->locale('fr')->translatedFormat('l d F Y') }}">
                            @if($jour['numSemaine'])
                                <div style="font-weight: bold; font-size: 0.65rem;">{{ $jour['numSemaine'] }}</div>
                            @endif
                            <div style="font-size: 0.7rem;">{{ $jour['jour'] }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <!-- Ligne d'ajout de tâche (masquée par défaut) -->
                <tr id="add-task-row" class="add-task-row" style="display: none;">
                    <td class="task-col">
                        <input type="text" class="form-control form-control-sm" id="new-task-name" placeholder="Nom de la tâche" style="font-size: 0.75rem;">
                        <select class="form-select form-select-sm mt-1" id="new-task-category" style="font-size: 0.7rem;">
                            <option value="">Sélectionner une sous-catégorie (optionnel)</option>
                            @foreach($categories as $categorie)
                                @foreach($categorie->sousCategories as $sousCategorie)
                                    <option value="{{ $sousCategorie->id }}">{{ $categorie->nom }} - {{ $sousCategorie->nom }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </td>
                    <td style="min-width: 80px; max-width: 80px;">
                        <input type="date" class="form-control form-control-sm" id="new-task-debut" style="font-size: 0.7rem;">
                    </td>
                    <td style="min-width: 80px; max-width: 80px;">
                        <input type="date" class="form-control form-control-sm" id="new-task-fin" style="font-size: 0.7rem;">
                    </td>
                    <td colspan="{{ count($jours) }}">
                        <select class="form-select form-select-sm" id="new-task-statut" style="max-width: 150px;">
                            <option value="non_demarre">Non démarré</option>
                            <option value="en_cours">En cours</option>
                            <option value="retard">En retard</option>
                            <option value="termine">Terminé</option>
                        </select>
                        <button class="btn btn-success btn-sm mt-1" onclick="saveNewTask()">
                            <i class="fas fa-check"></i> Enregistrer
                        </button>
                        <button class="btn btn-secondary btn-sm mt-1" onclick="toggleAddTaskRow()">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </td>
                </tr>

                @php
                    $totalJours = count($jours);
                @endphp

                <!-- Afficher les catégories et plannings -->
                @forelse($categories as $categorie)
                    <tr class="category-row">
                        <td class="task-col" colspan="{{ 3 + $totalJours }}">
                            <strong>{{ $categorie->nom }}</strong>
                        </td>
                    </tr>
                    
                    @foreach($categorie->sousCategories as $sousCategorie)
                        <tr class="subcategory-row">
                            <td class="task-col">{{ $sousCategorie->nom }}</td>
                            <td colspan="{{ 2 + $totalJours }}">
                                <button class="btn btn-sm btn-outline-primary btn-add-planning" 
                                        onclick="showAddTaskForCategory({{ $sousCategorie->id }}, '{{ $sousCategorie->nom }}')">
                                    <i class="fas fa-plus"></i> Ajouter
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Plannings de cette sous-catégorie -->
                        @foreach($plannings->where('id_souscategorie', $sousCategorie->id) as $planning)
                            <tr class="task-row">
                                <td class="task-col" style="padding-left: 40px;">
                                    {{ $planning->nom_tache_planning }}
                                </td>
                                <td class="text-center" style="font-size: 0.7rem; min-width: 80px; max-width: 80px;">{{ $planning->date_debut->format('d/m/Y') }}</td>
                                <td class="text-center" style="font-size: 0.7rem; min-width: 80px; max-width: 80px;">{{ $planning->date_fin->format('d/m/Y') }}</td>
                                
                                @foreach($jours as $jour)
                                    @php
                                        $isInTask = $jour['date']->between($planning->date_debut, $planning->date_fin);
                                        $isFirstDay = $jour['date']->isSameDay($planning->date_debut);
                                    @endphp
                                    <td class="date-col {{ $jour['isWeekend'] ? 'weekend-col' : '' }}" style="padding: 2px;">
                                        @if($isFirstDay)
                                            <div class="task-bar {{ $planning->statut }}" 
                                                 title="{{ $planning->nom_tache_planning }} ({{ $planning->duree_jours }} jours)">
                                            </div>
                                        @elseif($isInTask && !$isFirstDay)
                                            <div class="task-bar {{ $planning->statut }}" style="border-radius: 0;"></div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                @empty
                    <tr>
                        <td colspan="{{ 3 + count($jours) }}" class="text-center text-muted py-4">
                            Aucune catégorie DQE validée. Veuillez d'abord valider un DQE pour ce contrat.
                        </td>
                    </tr>
                @endforelse
                
                <!-- Plannings sans catégorie -->
                @foreach($plannings->whereNull('id_souscategorie') as $planning)
                    <tr class="task-row">
                        <td class="task-col">
                            {{ $planning->nom_tache_planning }}
                            <span class="badge bg-secondary" style="font-size: 0.65rem;">Sans catégorie</span>
                        </td>
                        <td class="text-center" style="font-size: 0.7rem; min-width: 80px; max-width: 80px;">{{ $planning->date_debut->format('d/m/Y') }}</td>
                        <td class="text-center" style="font-size: 0.7rem; min-width: 80px; max-width: 80px;">{{ $planning->date_fin->format('d/m/Y') }}</td>
                        
                        @foreach($jours as $jour)
                            @php
                                $isInTask = $jour['date']->between($planning->date_debut, $planning->date_fin);
                                $isFirstDay = $jour['date']->isSameDay($planning->date_debut);
                            @endphp
                            <td class="date-col {{ $jour['isWeekend'] ? 'weekend-col' : '' }}" style="padding: 2px;">
                                @if($isFirstDay)
                                    <div class="task-bar {{ $planning->statut }}" 
                                         title="{{ $planning->nom_tache_planning }} ({{ $planning->duree_jours }} jours)">
                                    </div>
                                @elseif($isInTask && !$isFirstDay)
                                    <div class="task-bar {{ $planning->statut }}" style="border-radius: 0;"></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleAddTaskRow() {
    const row = document.getElementById('add-task-row');
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    
    if (row.style.display === 'table-row') {
        document.getElementById('new-task-name').focus();
    }
}

function showAddTaskForCategory(categoryId, categoryName) {
    toggleAddTaskRow();
    document.getElementById('new-task-category').value = categoryId;
    document.getElementById('new-task-name').placeholder = `Tâche pour ${categoryName}`;
}

function saveNewTask() {
    const nom = document.getElementById('new-task-name').value;
    const categorie = document.getElementById('new-task-category').value;
    const debut = document.getElementById('new-task-debut').value;
    const fin = document.getElementById('new-task-fin').value;
    const statut = document.getElementById('new-task-statut').value;
    
    if (!nom || !debut || !fin) {
        alert('Veuillez remplir au minimum le nom et les dates');
        return;
    }
    
    const formData = new FormData();
    formData.append('nom_tache_planning', nom);
    if (categorie) formData.append('id_souscategorie', categorie);
    formData.append('date_debut', debut);
    formData.append('date_fin', fin);
    formData.append('statut', statut);
    
    fetch('{{ route("planning.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showSuccessMessage('Tâche ajoutée au planning !');
            setTimeout(() => location.reload(), 800);
        } else {
            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout');
    });
}

function exportPlanning() {
    alert('Fonctionnalité d\'export en cours de développement...');
    // TODO: Implémenter l'export PDF/Excel
}

function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'position-fixed top-0 end-0 p-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Succès</strong>
            </div>
            <div class="toast-body">${message}</div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endpush
