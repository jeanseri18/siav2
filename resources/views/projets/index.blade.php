@extends('layouts.app')

@section('title', 'Liste des Projets')
@section('page-title', 'Liste des Projets')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Accueil</a></li>
<li class="breadcrumb-item active">Projets</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-project-diagram me-2"></i>Liste des Projets
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('projets.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter un projet
                </a>
            </div>
        </div>

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table display">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Client</th>
                        <th>Date de création</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Secteur d'activité</th>
                        <th>Conducteur de travaux</th>
                        <th>TVA</th>
                        <th>Statut</th>
                        <th>Business Unit</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projets as $projet)
                    <tr>
                        <td>
                            <a href="{{ route('projets.show', ['projet' => $projet]) }}" class="app-badge app-badge-primary text-decoration-none">
                                {{ $projet->ref_projet }}
                            </a>
                        </td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-folder-open text-primary"></i>
                                </div>
                                <span>{{ $projet->nom_projet }}</span>
                            </div>
                        </td>
                        <td>{{ $projet->client }}</td>
                        <td>{{ $projet->date_creation }}</td>
                        <td>{{ $projet->date_debut }}</td>
                        <td>{{ $projet->date_fin ?? 'Non spécifiée' }}</td>
                        <td>{{ $projet->secteurActivite->nom ?? 'Secteur non défini' }}</td>
                        <td>{{ $projet->conducteur_travaux }}</td>
                        <td>
                            <span class="app-badge app-badge-{{ $projet->hastva ? 'success' : 'secondary' }} app-badge-pill">
                                {{ $projet->hastva ? 'Oui' : 'Non' }}
                            </span>
                        </td>
                        <td>
                            <span class="app-badge app-badge-{{ $projet->statut == 'en cours' ? 'warning' : ($projet->statut == 'terminé' ? 'success' : 'danger') }} app-badge-pill">
                                {{ ucfirst($projet->statut) }}
                            </span>
                        </td>
                        <td>{{ $projet->bu->nom ?? 'Business Unit non défini' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="app-btn app-btn-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('projets.show', ['projet' => $projet]) }}">
                                            <i class="fas fa-eye me-2"></i>Voir les détails
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('projets.edit', $projet) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('projets.destroy', $projet) }}" method="POST" class="delete-form">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger delete-btn" style="border: none; background: none;">
                                                <i class="fas fa-trash-alt me-2"></i>Supprimer
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        // Configuration DataTable
        $('#Table').DataTable({
            responsive: true,
            dom: '<"dt-header"Bf>rt<"dt-footer"ip>',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-file-export"></i> Exporter',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                },
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i> Colonnes'
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            }
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection