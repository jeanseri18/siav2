@extends('layouts.app')

@section('title', 'Liste des Communes')
@section('page-title', 'Liste des Communes')

@section('breadcrumb')
<li class="breadcrumb-item active">Communes</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-building me-2"></i>Liste des Communes
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('communes.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter une commune
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
            <table id="communesTable" class="app-table display">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Ville</th>
                        <th>Code</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($communes as $commune)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <span>{{ $commune->nom }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="app-badge app-badge-info app-badge-pill">
                                <i class="fas fa-city me-1"></i> {{ $commune->ville->nom }}
                            </span>
                        </td>
                        <td>{{ $commune->code ?? '-' }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="app-btn app-btn-outline-primary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('communes.edit', $commune->id) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('communes.destroy', $commune->id) }}" method="POST" class="delete-form">
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Configuration DataTable
        $('#communesTable').DataTable({
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
            
            if (confirm('Voulez-vous supprimer cette commune ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection