{{-- Page Index - Liste des Factures --}}
@extends('layouts.app')

@section('title', 'Gestion des Factures')
@section('page-title', 'Gestion des Factures')

@section('breadcrumb')
<li class="breadcrumb-item active">Factures</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class=" app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-invoice-dollar me-2"></i>Liste des Factures
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('factures.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter une nouvelle facture
                </a>

                   <a href="{{ route('factures.statistics') }}" class="app-btn app-btn-info app-btn-icon me-2">
        <i class="fas fa-chart-bar"></i> Statistiques
    </a>

            </div>
        </div>
        
        <div class="app-card-body">
            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filter-type" class="form-label">Filtrer par type :</label>
                    <select id="filter-type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="prestation">Prestation</option>
                        <option value="contrat">Contrat</option>
                        <option value="artisan">Artisan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter-statut" class="form-label">Filtrer par statut :</label>
                    <select id="filter-statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en attente">En attente</option>
                        <option value="payée">Payée</option>
                        <option value="annulée">Annulée</option>
                    </select>
                </div>
            </div>
            
            <div class="app-table-responsive">
                <table id="Table" class="app-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Numéro de Facture</th>
                            <th>Type / Référence</th>
                            <th>Montant HT</th>
                            <th>TVA (18%)</th>
                            <th>Montant à régler HT</th>
                            <th>Montant à régler TTC</th>
                            <th>Montant Réglé</th>
                            <th>Reste à Régler</th>
                            <th>Statut</th>
                            <th>Date d'Émission</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach($factures as $facture)
                        <tr data-type="{{ $facture->prestation ? 'prestation' : ($facture->contrat ? 'contrat' : ($facture->artisan ? 'artisan' : '')) }}" data-statut="{{ $facture->statut }}">
                            <td>{{ $facture->id }}</td>
                            <td>
                                <div class="app-d-flex app-align-items-center app-gap-2">
                                    <div class="item-icon">
                                        <i class="fas fa-file-invoice text-primary"></i>
                                    </div>
                                    <span>{{ $facture->num }}</span>
                                </div>
                            </td>
                            <td>
                                @if($facture->prestation)
                                    <span class="app-badge app-badge-info app-badge-pill mb-1">Prestation</span><br>
                                    <small>{{ $facture->prestation->artisan->nom ?? 'N/A' }} - {{ number_format($facture->prestation->montant ?? 0, 2) }} CFA</small>
                                @elseif($facture->contrat)
                                    <span class="app-badge app-badge-success app-badge-pill mb-1">Contrat</span><br>
                                    <small>{{ $facture->contrat->nom_contrat }}</small>
                                @elseif($facture->artisan)
                                    <span class="app-badge app-badge-warning app-badge-pill mb-1">Artisan</span><br>
                                    <small>{{ $facture->artisan->nom }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="app-fw-bold">{{ number_format($facture->montant_ht, 2) }} CFA</td>
                            <td class="app-fw-bold">{{ number_format($facture->tva ?? 0, 2) }} CFA</td>
                            <td class="app-fw-bold">{{ number_format($facture->montant_ht, 2) }} CFA</td>
                            <td class="app-fw-bold">{{ number_format($facture->montant_ttc ?? $facture->montant_total, 2) }} CFA</td>
                            <td>{{ number_format($facture->montant_reglement, 2) }} CFA</td>
                            <td>
                                <span class="app-badge app-badge-{{ $facture->reste_a_regler > 0 ? 'warning' : 'success' }} app-badge-pill">
                                    {{ number_format($facture->reste_a_regler, 2) }} CFA
                                </span>
                            </td>
                            <td>
                                @if($facture->statut == 'en attente')
                                    <span class="app-badge app-badge-warning app-badge-pill">
                                        <i class="fas fa-clock me-1"></i> En attente
                                    </span>
                                @elseif($facture->statut == 'payée')
                                    <span class="app-badge app-badge-success app-badge-pill">
                                        <i class="fas fa-check-circle me-1"></i> Payée
                                    </span>
                                @elseif($facture->statut == 'annulée')
                                    <span class="app-badge app-badge-danger app-badge-pill">
                                        <i class="fas fa-times-circle me-1"></i> Annulée
                                    </span>
                                @else
                                    <span class="app-badge app-badge-secondary app-badge-pill">
                                        {{ ucfirst($facture->statut) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($facture->date_emission)->format('d/m/Y') }}</td>
                            <td>
                              <div class="app-d-flex app-gap-2">
        <a href="{{ route('factures.show', $facture->id) }}" class="app-btn app-btn-primary app-btn-sm app-btn-icon">
            <i class="fas fa-eye"></i>
        </a>
        
        <a href="{{ route('factures.edit', $facture->id) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon">
            <i class="fas fa-edit"></i>
        </a>
        
        <form action="{{ route('factures.destroy', $facture->id) }}" method="POST" class="delete-form" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn">
                <i class="fas fa-trash"></i>
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
        var table = $('#Table').DataTable({
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
        
        // Filtrage par type
        $('#filter-type').on('change', function() {
            var selectedType = $(this).val();
            
            if (selectedType === '') {
                // Afficher toutes les lignes
                $('#Table tbody tr').show();
            } else {
                // Masquer toutes les lignes
                $('#Table tbody tr').hide();
                // Afficher seulement les lignes correspondant au type sélectionné
                $('#Table tbody tr[data-type="' + selectedType + '"]').show();
            }
            
            // Redessiner le tableau DataTable
            table.draw();
        });
        
        // Filtrage par statut
        $('#filter-statut').on('change', function() {
            var selectedStatut = $(this).val();
            
            if (selectedStatut === '') {
                // Afficher toutes les lignes
                $('#Table tbody tr').show();
            } else {
                // Masquer toutes les lignes
                $('#Table tbody tr').hide();
                // Afficher seulement les lignes correspondant au statut sélectionné
                $('#Table tbody tr[data-statut="' + selectedStatut + '"]').show();
            }
            
            // Redessiner le tableau DataTable
            table.draw();
        });
        
        // Filtrage combiné (type + statut)
        function applyFilters() {
            var selectedType = $('#filter-type').val();
            var selectedStatut = $('#filter-statut').val();
            
            $('#Table tbody tr').each(function() {
                var row = $(this);
                var rowType = row.data('type');
                var rowStatut = row.data('statut');
                
                var showRow = true;
                
                // Vérifier le filtre type
                if (selectedType !== '' && rowType !== selectedType) {
                    showRow = false;
                }
                
                // Vérifier le filtre statut
                if (selectedStatut !== '' && rowStatut !== selectedStatut) {
                    showRow = false;
                }
                
                if (showRow) {
                    row.show();
                } else {
                    row.hide();
                }
            });
            
            table.draw();
        }
        
        // Appliquer les filtres combinés lors du changement
        $('#filter-type, #filter-statut').on('change', applyFilters);
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection