@extends('layouts.app')

@section('title', 'Liste des Quartiers')
@section('page-title', 'Liste des Quartiers')

@section('breadcrumb')
<li class="breadcrumb-item active">Quartiers</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-map-marker-alt me-2"></i>Liste des Quartiers
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('quartiers.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter un quartier
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
            <table id="quartiersTable" class="app-table display">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Commune</th>
                        <th>Ville</th>
                        <th>Code</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quartiers as $quartier)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <span>{{ $quartier->nom }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="app-badge app-badge-info app-badge-pill">
                                <i class="fas fa-building me-1"></i> {{ $quartier->commune->nom }}
                            </span>
                        </td>
                        <td>
                            <span class="app-badge app-badge-success app-badge-pill">
                                <i class="fas fa-city me-1"></i> {{ $quartier->commune->ville->nom }}
                            </span>
                        </td>
                        <td>{{ $quartier->code ?? '-' }}</td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('quartiers.edit', $quartier->id) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('quartiers.destroy', $quartier->id) }}" method="POST" class="delete-form">
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Configuration DataTable
        $('#quartiersTable').DataTable({
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
            
            if (confirm('Voulez-vous supprimer ce quartier ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection