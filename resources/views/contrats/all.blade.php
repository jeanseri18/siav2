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
                <a href="{{ route('contrats.allcreate') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Créer un nouveau contrat
                </a>
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
});
</script>
@endpush