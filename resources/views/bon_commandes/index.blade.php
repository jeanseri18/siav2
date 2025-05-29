{{-- Page Index - Liste des Bons de Commande --}}
@extends('layouts.app')

@section('title', 'Gestion des Bons de Commande')
@section('page-title', 'Gestion des Bons de Commande')

@section('breadcrumb')
<li class="breadcrumb-item active">Bons de Commande</li>
@endsection

@section('content')

<div class=" app-fade-in">
    
    <!-- Statistiques rapides -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-file-invoice fa-2x text-primary mb-3"></i>
                    <h3 class="app-card-title">{{ $bonCommandes->count() }}</h3>
                    <p class="text-muted mb-0">Total des bons</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-3"></i>
                    <h3 class="app-card-title">{{ $bonCommandes->where('statut', 'en attente')->count() }}</h3>
                    <p class="text-muted mb-0">En attente</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-check-circle fa-2x text-info mb-3"></i>
                    <h3 class="app-card-title">{{ $bonCommandes->where('statut', 'confirmée')->count() }}</h3>
                    <p class="text-muted mb-0">Confirmées</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="app-card text-center">
                <div class="app-card-body">
                    <i class="fas fa-truck fa-2x text-success mb-3"></i>
                    <h3 class="app-card-title">{{ $bonCommandes->where('statut', 'livrée')->count() }}</h3>
                    <p class="text-muted mb-0">Livrées</p>
                </div>
            </div>
        </div>
    </div>
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-invoice me-2"></i>Liste des Bons de Commande
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('bon-commandes.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Nouveau Bon
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
            <table id="bonCommandeTable" class="app-table display">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Fournisseur</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bonCommandes as $bonCommande)
                    <tr>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-file-invoice text-primary"></i>
                                </div>
                                <span class="app-fw-bold">{{ $bonCommande->reference }}</span>
                            </div>
                        </td>
                        <td>{{ $bonCommande->date_commande->format('d/m/Y') }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-building text-info"></i>
                                </div>
                                <span>{{ $bonCommande->fournisseur ? $bonCommande->fournisseur->nom_raison_sociale : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="app-fw-bold text-success">
                            <i class="fas fa-coins me-1"></i>
                            {{ number_format($bonCommande->montant_total, 0, ',', ' ') }} FCFA
                        </td>
                        <td>
                            @php
                                $statutClass = '';
                                $statutIcon = '';
                                switch($bonCommande->statut) {
                                    case 'en attente':
                                        $statutClass = 'warning';
                                        $statutIcon = 'clock';
                                        break;
                                    case 'confirmée':
                                        $statutClass = 'info';
                                        $statutIcon = 'check-circle';
                                        break;
                                    case 'livrée':
                                        $statutClass = 'success';
                                        $statutIcon = 'truck';
                                        break;
                                    case 'annulée':
                                        $statutClass = 'danger';
                                        $statutIcon = 'times-circle';
                                        break;
                                    default:
                                        $statutClass = 'secondary';
                                        $statutIcon = 'question-circle';
                                }
                            @endphp
                            <span class="app-badge app-badge-{{ $statutClass }} app-badge-pill">
                                <i class="fas fa-{{ $statutIcon }} me-1"></i> 
                                {{ ucfirst($bonCommande->statut) }}
                            </span>
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('bon-commandes.show', $bonCommande) }}" 
                                   class="app-btn app-btn-info app-btn-sm app-btn-icon" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($bonCommande->statut == 'en attente')
                                <a href="{{ route('bon-commandes.edit', $bonCommande) }}" 
                                   class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('bon-commandes.destroy', $bonCommande) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                                
                                @if($bonCommande->statut == 'en attente')
                                <form action="{{ route('bon-commandes.confirm', $bonCommande) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="app-btn app-btn-success app-btn-sm app-btn-icon" title="Confirmer">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
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
        $('#bonCommandeTable').DataTable({
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce bon de commande ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection