@extends('layouts.app')

@section('title', 'Liste des Villes')
@section('page-title', 'Liste des Villes')

@section('breadcrumb')
<li class="breadcrumb-item active">Villes</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-city me-2"></i>Liste des Villes
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('villes.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter une ville
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
            <table id="villesTable" class="app-table display">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Pays</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($villes as $ville)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-city text-primary"></i>
                                </div>
                                <span>{{ $ville->nom }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="app-badge app-badge-info app-badge-pill">
                                <i class="fas fa-globe me-1"></i> {{ $ville->pays->nom }}
                            </span>
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('villes.edit', $ville->id) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('villes.destroy', $ville->id) }}" method="POST" class="delete-form">
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
        $('#villesTable').DataTable({
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
            
            if (confirm('Voulez-vous supprimer cette ville ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection