@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Déboursé Chantier</h2>
            <h4>Contrat : {{ $debourseChantier->contrat->nom_contrat }}</h4>
            <p>DQE : {{ $debourseChantier->dqe->reference ?? 'Sans référence' }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('debourses_chantier.index', $debourseChantier->contrat_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('debourses_chantier.export', $debourseChantier->id) }}" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Exporter en PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Détails du déboursé chantier</h5>
                    <span class="badge bg-primary fs-5">Montant total : {{ number_format($debourseChantier->montant_total, 2, ',', ' ') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Section</th>
                                    <th>Désignation</th>
                                    <th>Unité</th>
                                    <th>Quantité</th>
                                    <th>Coût unitaire matériaux</th>
                                    <th>Coût unitaire main d'œuvre</th>
                                    <th>Coût unitaire matériel</th>
                                    <th>Total matériaux</th>
                                    <th>Total main d'œuvre</th>
                                    <th>Total matériel</th>
                                    <th>Déboursé sec</th>
                                    <th>Montant total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debourseChantier->details as $detail)
                                    <tr data-detail-id="{{ $detail->id }}">
                                        <td>{{ $detail->section ?? 'N/A' }}</td>
                                        <td>
                                            @if($debourseChantier->statut == 'brouillon')
                                                <span class="editable-designation-chantier" data-id="{{ $detail->id }}" data-value="{{ $detail->designation }}" style="cursor: pointer; border-bottom: 1px dashed #007bff;" title="Cliquer pour modifier">
                                                    {{ $detail->designation }}
                                                </span>
                                            @else
                                                {{ $detail->designation }}
                                            @endif
                                        </td>
                                        <td>{{ $detail->unite }}</td>
                                        <td>
                                            @if($debourseChantier->statut == 'brouillon')
                                                <input type="number" name="quantite" value="{{ $detail->quantite }}" step="0.01" min="0" class="form-control form-control-sm">
                                            @else
                                                {{ $detail->quantite }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($debourseChantier->statut == 'brouillon')
                                                <input type="number" name="cout_unitaire_materiaux" value="{{ $detail->cout_unitaire_materiaux }}" step="0.01" min="0" class="form-control form-control-sm">
                                            @else
                                                {{ number_format($detail->cout_unitaire_materiaux, 2, ',', ' ') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($debourseChantier->statut == 'brouillon')
                                                <input type="number" name="cout_unitaire_main_oeuvre" value="{{ $detail->cout_unitaire_main_oeuvre }}" step="0.01" min="0" class="form-control form-control-sm">
                                            @else
                                                {{ number_format($detail->cout_unitaire_main_oeuvre, 2, ',', ' ') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($debourseChantier->statut == 'brouillon')
                                                <input type="number" name="cout_unitaire_materiel" value="{{ $detail->cout_unitaire_materiel }}" step="0.01" min="0" class="form-control form-control-sm">
                                            @else
                                                {{ number_format($detail->cout_unitaire_materiel, 2, ',', ' ') }}
                                            @endif
                                        </td>
                                        <td>{{ number_format($detail->total_materiaux, 2, ',', ' ') }}</td>
                                        <td>{{ number_format($detail->total_main_oeuvre ?? ($detail->quantite * ($detail->cout_unitaire_main_oeuvre ?? 0)), 2, ',', ' ') }}</td>
                                        <td>{{ number_format($detail->total_materiel, 2, ',', ' ') }}</td>
                                        <td>{{ number_format(($detail->total_materiaux ?? 0) + ($detail->total_main_oeuvre ?? ($detail->quantite * ($detail->cout_unitaire_main_oeuvre ?? 0))), 2, ',', ' ') }}</td>
                                        <td>{{ number_format($detail->montant_total, 2, ',', ' ') }}</td>
                                        <td>
                                            @if($debourseChantier->statut == 'brouillon')
                                                <div class="dropdown">
                                                    <button class="btn btn-xs btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="editLineChantier({{ $detail->id }})"><i class="fas fa-edit"></i> Modifier</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="duplicateLineChantier({{ $detail->id }})"><i class="fas fa-copy"></i> Dupliquer</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteLineChantier({{ $detail->id }})"><i class="fas fa-trash"></i> Supprimer</a></li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour attacher l'événement click aux désignations éditables
function attachDesignationClickEvent(element) {
    element.addEventListener('click', function() {
        const currentValue = this.getAttribute('data-value');
        const detailId = this.getAttribute('data-id');
        
        // Créer un input avec boutons
        const inputContainer = document.createElement('div');
        inputContainer.className = 'd-flex align-items-center';
        inputContainer.innerHTML = `
            <input type="text" class="form-control form-control-sm me-2" value="${currentValue}" id="input-${detailId}">
            <button class="btn btn-xs btn-success me-1" onclick="saveDesignationChantier(${detailId}, '${currentValue}')">
                <i class="fas fa-check"></i>
            </button>
            <button class="btn btn-xs btn-secondary" onclick="cancelEditChantier(${detailId}, '${currentValue}')">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Remplacer l'élément
        this.parentNode.replaceChild(inputContainer, this);
        
        // Focus sur l'input
        document.getElementById(`input-${detailId}`).focus();
    });
}

// Gestion de l'édition en ligne des désignations
document.addEventListener('DOMContentLoaded', function() {
    const editableDesignations = document.querySelectorAll('.editable-designation-chantier');
    
    editableDesignations.forEach(function(element) {
        attachDesignationClickEvent(element);
    });
});

function saveDesignationChantier(detailId, originalValue) {
    const input = document.getElementById(`input-${detailId}`);
    const newValue = input.value;
    
    if (newValue.trim() === '') {
        alert('La désignation ne peut pas être vide.');
        return;
    }
    
    // Récupérer les autres valeurs de la ligne pour la mise à jour complète
    const row = document.querySelector(`tr[data-detail-id="${detailId}"]`);
    const quantite = row.querySelector('input[name="quantite"]').value;
    const coutMateriaux = row.querySelector('input[name="cout_unitaire_materiaux"]').value;
    const coutMainOeuvre = row.querySelector('input[name="cout_unitaire_main_oeuvre"]').value;
    const coutMateriel = row.querySelector('input[name="cout_unitaire_materiel"]').value;
    
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('designation', newValue);
    formData.append('quantite', quantite);
    formData.append('cout_unitaire_materiaux', coutMateriaux);
    formData.append('cout_unitaire_main_oeuvre', coutMainOeuvre);
    formData.append('cout_unitaire_materiel', coutMateriel);
    
    fetch(`/debourses_chantier/${detailId}/update-detail`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'affichage
            const span = document.createElement('span');
            span.className = 'editable-designation-chantier';
            span.setAttribute('data-id', detailId);
            span.setAttribute('data-value', newValue);
            span.style.cursor = 'pointer';
            span.style.borderBottom = '1px dashed #007bff';
            span.title = 'Cliquer pour modifier';
            span.textContent = newValue;
            
            input.parentNode.parentNode.replaceChild(span, input.parentNode);
            
            // Réattacher l'événement click
            attachDesignationClickEvent(span);
            
            // Recharger la page pour voir tous les changements
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde');
    });
}

function cancelEditChantier(detailId, originalValue) {
    const input = document.getElementById(`input-${detailId}`);
    
    const span = document.createElement('span');
    span.className = 'editable-designation-chantier';
    span.setAttribute('data-id', detailId);
    span.setAttribute('data-value', originalValue);
    span.style.cursor = 'pointer';
    span.style.borderBottom = '1px dashed #007bff';
    span.title = 'Cliquer pour modifier';
    span.textContent = originalValue;
    
    input.parentNode.parentNode.replaceChild(span, input.parentNode);
}

// Fonctions pour les actions du menu déroulant
function editLineChantier(detailId) {
    // Activer l'édition en ligne pour tous les champs de cette ligne
    const row = document.querySelector(`tr[data-detail-id="${detailId}"]`);
    if (row) {
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            input.style.backgroundColor = '#fff3cd';
            input.focus();
        });
        
        // Ajouter un bouton de sauvegarde temporaire
        const actionsCell = row.querySelector('td:last-child');
        if (!actionsCell.querySelector('.save-btn')) {
            const saveBtn = document.createElement('button');
            saveBtn.className = 'btn btn-sm btn-success save-btn me-2';
            saveBtn.innerHTML = '<i class="fas fa-save"></i> Sauvegarder';
            saveBtn.onclick = () => saveLineChantier(detailId);
            actionsCell.insertBefore(saveBtn, actionsCell.firstChild);
        }
    }
}

function saveLineChantier(detailId) {
    const row = document.querySelector(`tr[data-detail-id="${detailId}"]`);
    if (!row) return;
    
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Récupérer les valeurs des inputs
    const designation = row.querySelector('.editable-designation-chantier').getAttribute('data-value');
    const quantite = row.querySelector('input[name="quantite"]').value;
    const coutMateriaux = row.querySelector('input[name="cout_unitaire_materiaux"]').value;
    const coutMainOeuvre = row.querySelector('input[name="cout_unitaire_main_oeuvre"]').value;
    const coutMateriel = row.querySelector('input[name="cout_unitaire_materiel"]').value;
    
    formData.append('designation', designation);
    formData.append('quantite', quantite);
    formData.append('cout_unitaire_materiaux', coutMateriaux);
    formData.append('cout_unitaire_main_oeuvre', coutMainOeuvre);
    formData.append('cout_unitaire_materiel', coutMateriel);
    
    fetch(`/debourses_chantier/${detailId}/update-detail`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger la page pour voir les changements
        } else {
            alert('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde');
    });
}

function duplicateLineChantier(detailId) {
    if (confirm('Êtes-vous sûr de vouloir dupliquer cette ligne ?')) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/debourses_chantier/${detailId}/duplicate`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recharger la page pour voir la nouvelle ligne
            } else {
                alert('Erreur lors de la duplication: ' + (data.error || data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la duplication');
        });
    }
}

function deleteLineChantier(detailId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette ligne ? Cette action est irréversible.')) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/debourses_chantier/${detailId}/delete`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recharger la page pour voir les changements
            } else {
                alert('Erreur lors de la suppression: ' + (data.error || data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        });
    }
}
</script>

@endsection