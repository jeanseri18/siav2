{{-- Page Index - Liste des Demandes d'Approvisionnement --}}
@extends('layouts.app')

@section('title', 'Gestion des Demandes d\'Approvisionnement')
@section('page-title', 'Gestion des Demandes d\'Approvisionnement')

@section('breadcrumb')
<li class="breadcrumb-item active">Demandes d'Approvisionnement</li>
@endsection

@section('content')

<div class=" app-fade-in">
    
    <!-- Statistiques rapides -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-boxes fa-2x text-primary mb-3"></i>
                    <h3 class="app-card-title">{{ $demandes->count() }}</h3>
                    <p class="text-muted mb-0">Total des demandes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                    <h3 class="app-card-title">{{ $demandes->where('statut', 'en attente')->count() }}</h3>
                    <p class="text-muted mb-0">En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                    <h3 class="app-card-title">{{ $demandes->where('statut', 'approuvée')->count() }}</h3>
                    <p class="text-muted mb-0">Approuvées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-check fa-2x text-secondary mb-3"></i>
                    <h3 class="app-card-title">{{ $demandes->where('statut', 'terminée')->count() }}</h3>
                    <p class="text-muted mb-0">Terminées</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique ou tableau de répartition par projet -->
    @if($demandes->where('projet_id', '!=', null)->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par Projet
                    </h3>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        @foreach($demandes->groupBy('projet.nom_projet') as $nomProjet => $demandesProjet)
                        @if($nomProjet)
                        <div class="col-md-4 mb-3">
                            <div class="app-d-flex app-justify-content-between app-align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">{{ $nomProjet }}</h6>
                                    <small class="text-muted">{{ $demandesProjet->count() }} demande(s)</small>
                                </div>
                                <div class="app-badge app-badge-primary app-badge-pill">
                                    {{ round(($demandesProjet->count() / $demandes->count()) * 100, 1) }}%
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-boxes me-2"></i>Liste des Demandes d'Approvisionnement
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('demande-approvisionnements.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Nouvelle Demande
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
            <table id="demandeTable" class="app-table display">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Projet</th>
                        <th>Demandeur</th>
                        <th>Nb Articles</th>
                        <th>Statut</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($demandes as $demande)
                    <tr>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-file-invoice text-primary"></i>
                                </div>
                                <span class="app-fw-bold">{{ $demande->reference }}</span>
                            </div>
                        </td>
                        <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                        <td>
                            @if($demande->projet)
                                <div class="app-d-flex app-align-items-center app-gap-2">
                                    <div class="item-icon">
                                        <i class="fas fa-project-diagram text-info"></i>
                                    </div>
                                    <span>{{ $demande->projet->nom_projet }}</span>
                                </div>
                            @else
                                <span class="app-badge app-badge-light">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                                <span>{{ $demande->user ? $demande->user->nom : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="app-badge app-badge-info app-badge-pill">
                                <i class="fas fa-box me-1"></i>
                                {{ $demande->lignes->count() }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statutClass = '';
                                $statutIcon = '';
                                switch($demande->statut) {
                                    case 'en attente':
                                        $statutClass = 'warning';
                                        $statutIcon = 'clock';
                                        break;
                                    case 'approuvée':
                                        $statutClass = 'success';
                                        $statutIcon = 'check-circle';
                                        break;
                                    case 'rejetée':
                                        $statutClass = 'danger';
                                        $statutIcon = 'times-circle';
                                        break;
                                    case 'terminée':
                                        $statutClass = 'secondary';
                                        $statutIcon = 'check';
                                        break;
                                    default:
                                        $statutClass = 'light';
                                        $statutIcon = 'question-circle';
                                }
                            @endphp
                            <span class="app-badge app-badge-{{ $statutClass }} app-badge-pill">
                                <i class="fas fa-{{ $statutIcon }} me-1"></i> {{ ucfirst($demande->statut) }}
                            </span>
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('demande-approvisionnements.show', $demande) }}" 
                                   class="app-btn app-btn-info app-btn-sm app-btn-icon" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($demande->statut == 'en attente')
                                <a href="{{ route('demande-approvisionnements.edit', $demande) }}" 
                                   class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('demande-approvisionnements.destroy', $demande) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('demande-approvisionnements.approve', $demande) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="app-btn app-btn-success app-btn-sm app-btn-icon" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                
                                @if($demande->statut == 'approuvée')
                                <a href="{{ route('bon-commandes.create', ['demande_approvisionnement_id' => $demande->id]) }}" 
                                   class="app-btn app-btn-primary app-btn-sm app-btn-icon" title="Créer bon de commande">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                                @endif
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
        $('#demandeTable').DataTable({
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
            },
            order: [[1, 'desc']] // Trier par date décroissante
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette demande d\'approvisionnement ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection