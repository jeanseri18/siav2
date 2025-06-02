{{-- Page Index - Liste des clients --}}
@extends('layouts.app')

@section('title', 'Gestion des Clients')
@section('page-title', 'Gestion des Clients')

@section('breadcrumb')
<li class="breadcrumb-item active">Clients</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-users me-2"></i>Liste des Clients
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('clients.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-user-plus"></i> Ajouter un Client
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
            <table id="Table" class="app-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Prénoms</th>
                        <th>Délai paiement</th>
                        <th>Mode paiement</th>
                        <th>Régime</th>
                        <th>Adresse</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Type</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                    <tr>
                         <td>{{ $client->code }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <span>{{ $client->nom_raison_sociale }}</span>
                            </div>
                        </td>
                        <td>{{ $client->prenoms }}</td>
                        <td>{{ $client->delai_paiement }} jours</td>
                        <td>{{ $client->mode_paiement }}</td>
                        <td>{{ $client->regime_imposition }}</td>
                        <td>{{ $client->adresse_localisation }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->telephone }}</td>
                        <td>
                            @if($client->type == 'Particulier')
                                <span class="app-badge app-badge-info app-badge-pill">
                                    <i class="fas fa-user me-1"></i> Particulier
                                </span>
                            @else
                                <span class="app-badge app-badge-primary app-badge-pill">
                                    <i class="fas fa-building me-1"></i> Entreprise
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('clients.edit', $client) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="delete-form" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn">
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
    $(document).ready(function() {
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce client ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection