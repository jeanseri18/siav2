@extends('layouts.app')

@section('title', 'Tous les Contrats')
@section('page-title', 'Tous les Contrats')



@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-contract me-2"></i>Liste de Tous les Contrats
            </h2>
            <div class="app-card-actions">
                <button type="button" class="app-btn app-btn-primary app-btn-icon" data-bs-toggle="modal" data-bs-target="#createContractModal">
                    <i class="fas fa-plus"></i> Créer un nouveau contrat
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="app-alert app-alert-success">
            <div class="app-alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">{{ session('success') }}</div>
            </div>
            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <div class="app-card-body app-table-responsive">
            <table id="contractsTable" class="app-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Nom du contrat</th>
                        <th>Projet</th>
                        <th>Client</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contrats as $contrat)
                    <tr>
                        <td>
                            <a href="{{ route('contrats.show', $contrat) }}" class="badge bg-primary text-decoration-none">{{ $contrat->ref_contrat }}</a>
                        </td>
                        <td>{{ $contrat->nom_contrat }}</td>
                        <td>
                            @if($contrat->projet)
                                <span class="badge bg-info">{{ $contrat->projet->nom_projet }}</span>
                            @else
                                <span class="text-muted">Aucun projet</span>
                            @endif
                        </td>
                        <td>
                            @if($contrat->client)
                                {{ $contrat->client->nom }}
                            @else
                                <span class="text-muted">Aucun client</span>
                            @endif
                        </td>
                        <td>{{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : '-' }}</td>
                        <td>{{ number_format($contrat->montant, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($contrat->statut == 'en cours')
                                <span class="badge bg-warning">En cours</span>
                            @elseif($contrat->statut == 'terminé')
                                <span class="badge bg-success">Terminé</span>
                            @else
                                <span class="badge bg-danger">Annulé</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="app-btn app-btn-sm app-btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('contrats.show', $contrat->id) }}">
                                            <i class="fas fa-eye me-2"></i>Voir
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('contrats.edit', $contrat->id) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('contrats.destroy', $contrat->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-trash me-2"></i>Supprimer
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="app-empty-state">
                                <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun contrat trouvé</h5>
                                <p class="text-muted">Commencez par créer votre premier contrat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal pour créer un nouveau contrat -->
<div class="modal fade" id="createContractModal" tabindex="-1" aria-labelledby="createContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createContractModalLabel">
                    <i class="fas fa-plus me-2"></i>Créer un nouveau contrat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('contrats.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="projet_id" class="form-label">Projet <span class="text-danger">*</span></label>
                                <select class="form-select" id="projet_id" name="projet_id" required>
                                    <option value="">Sélectionner un projet</option>
                                    @foreach($projets as $projet)
                                        <option value="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom_contrat" class="form-label">Nom du contrat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom_contrat" name="nom_contrat" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_fin" class="form-label">Date de fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="montant" name="montant" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="en cours">En cours</option>
                                    <option value="terminé">Terminé</option>
                                    <option value="annulé">Annulé</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_travaux" class="form-label">Type de travaux <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="type_travaux" name="type_travaux" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="taux_garantie" class="form-label">Taux de garantie (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="taux_garantie" name="taux_garantie" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <select class="form-select" id="client_id" name="client_id" required>
                            <option value="">Sélectionner un client</option>
                            <!-- Les clients seront chargés dynamiquement selon le projet sélectionné -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Créer le contrat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser DataTable
    $('#contractsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
        },
        responsive: true,
        order: [[0, 'desc']]
    });
    
    // Confirmation de suppression
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?')) {
            this.submit();
        }
    });
    
    // Charger les clients selon le projet sélectionné
    $('#projet_id').on('change', function() {
        const projetId = $(this).val();
        const clientSelect = $('#client_id');
        
        clientSelect.html('<option value="">Chargement...</option>');
        
        if (projetId) {
            // Ici vous pouvez ajouter un appel AJAX pour charger les clients du projet
            // Pour l'instant, on laisse vide
            clientSelect.html('<option value="">Sélectionner un client</option>');
        } else {
            clientSelect.html('<option value="">Sélectionner un client</option>');
        }
    });
});
</script>
@endpush