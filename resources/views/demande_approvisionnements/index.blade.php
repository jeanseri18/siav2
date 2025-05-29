{{-- Page Index - Liste des Demandes d'Approvisionnement --}}
@extends('layouts.app')

@section('title', 'Gestion des Demandes d\'Approvisionnement')
@section('page-title', 'Gestion des Demandes d\'Approvisionnement')

@section('breadcrumb')
<li class="breadcrumb-item active">Demandes d'Approvisionnement</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="app-card" style="background: var(--primary); color: var(--white); margin-bottom: var(--spacing-lg);">
        <div class="app-card-body">
            <h2 class="app-fw-bold app-mb-3">Gestion des Demandes d'Approvisionnement</h2>
            <div class="app-d-flex app-gap-3">
                <a href="{{ route('demande-approvisionnements.index') }}" class="app-btn" 
                   style="background: var(--primary-light); color: var(--white); width: 200px;">
                    <i class="fas fa-list me-2"></i>Liste des Demandes
                </a>
                <a href="{{ route('demande-approvisionnements.create') }}" class="app-btn" 
                   style="background: var(--primary-light); color: var(--white); width: 200px;">
                    <i class="fas fa-plus me-2"></i>Nouvelle Demande
                </a>
            </div>
        </div>
    </div>

    <div class="app-card app-hover-shadow">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-boxes me-2"></i>Liste des Demandes d'Approvisionnement
            </h2>
        </div>
        
        <div class="app-card-body app-table-responsive">
            <table id="demandeTable" class="app-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Projet</th>
                        <th>Demandeur</th>
                        <th>Statut</th>
                        <th style="width: 180px;">Actions</th>
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
                                    <span>{{ $demande->reference }}</span>
                                </div>
                            </td>
                            <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                            <td>{{ $demande->projet ? $demande->projet->nom_projet : 'N/A' }}</td>
                            <td>{{ $demande->user ? $demande->user->name : 'N/A' }}</td>
                            <td>
                                @if($demande->statut == 'en attente')
                                    <span class="app-badge app-badge-warning app-badge-pill">
                                        <i class="fas fa-clock me-1"></i> En attente
                                    </span>
                                @elseif($demande->statut == 'approuvée')
                                    <span class="app-badge app-badge-success app-badge-pill">
                                        <i class="fas fa-check-circle me-1"></i> Approuvée
                                    </span>
                                @elseif($demande->statut == 'rejetée')
                                    <span class="app-badge app-badge-danger app-badge-pill">
                                        <i class="fas fa-times-circle me-1"></i> Rejetée
                                    </span>
                                @elseif($demande->statut == 'terminée')
                                    <span class="app-badge app-badge-secondary app-badge-pill">
                                        <i class="fas fa-check me-1"></i> Terminée
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="app-d-flex app-gap-2">
                                    <a href="{{ route('demande-approvisionnements.show', $demande) }}" class="app-btn app-btn-info app-btn-sm app-btn-icon">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($demande->statut == 'en attente')
                                        <a href="{{ route('demande-approvisionnements.edit', $demande) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('demande-approvisionnements.destroy', $demande) }}" method="POST" class="delete-form" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn">
                                                <i class="fas fa-trash"></i>
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
            }
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette demande?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection