{{-- Page Index - Liste des Ventes --}}
@extends('layouts.app')

@section('title', 'Gestion des Ventes')
@section('page-title', 'Gestion des Ventes')

@section('breadcrumb')
<li class="breadcrumb-item active">Ventes</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-shopping-cart me-2"></i>Liste des Ventes
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('ventes.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Nouvelle Vente
                </a>
                <a href="{{ route('ventes.report') }}" class="app-btn app-btn-info app-btn-icon">
                    <i class="fas fa-chart-bar"></i> Rapport
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
            <table id="ventesTable" class="app-table display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventes as $vente)
                    <tr>
                        <td>
                            <span class="app-badge app-badge-light">
                                #{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <span>{{ $vente->client->nom_raison_sociale ?? $vente->client->prenoms }}</span>
                            </div>
                        </td>
                        <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                        <td class="app-fw-bold text-success">
                            <i class="fas fa-coins me-1"></i>
                            {{ number_format($vente->total, 0, ',', ' ') }} FCFA
                        </td>
                        <td>
                            @php
                                $statutClass = '';
                                $statutIcon = '';
                                switch($vente->statut) {
                                    case 'Payée':
                                        $statutClass = 'success';
                                        $statutIcon = 'check-circle';
                                        break;
                                    case 'En attente':
                                        $statutClass = 'warning';
                                        $statutIcon = 'clock';
                                        break;
                                    case 'Annulée':
                                        $statutClass = 'danger';
                                        $statutIcon = 'times-circle';
                                        break;
                                    default:
                                        $statutClass = 'secondary';
                                        $statutIcon = 'question-circle';
                                }
                            @endphp
                            <span class="app-badge app-badge-{{ $statutClass }} app-badge-pill">
                                <i class="fas fa-{{ $statutIcon }} me-1"></i> {{ $vente->statut }}
                            </span>
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('ventes.show', $vente) }}" class="app-btn app-btn-info app-btn-sm app-btn-icon" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($vente->statut !== 'Payée')
                                <form action="{{ route('ventes.updateStatus', $vente->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="app-btn app-btn-success app-btn-sm app-btn-icon" title="Valider">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('ventes.destroy', $vente) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
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
        $('#ventesTable').DataTable({
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
            order: [[0, 'desc']] // Trier par ID décroissant
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection