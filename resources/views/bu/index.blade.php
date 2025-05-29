@extends('layouts.app')

@section('content')

<div class=" app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-boxes me-2"></i>Liste des BU
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('bu.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter une bu
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
            <table id="stockTable" class="app-table display">
                <thead class="table-primary">
    
                <tr>
                    <th>Nom</th>
                    <th>Secteur</th>
                    <th>Adresse</th>
                    <th>solde caisse</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bus as $bu)
                    <tr>
                        <td>{{ $bu->nom }}</td>
                        <td>{{ $bu->secteur->nom }}</td>
                        <td>{{ $bu->adresse }}</td>
                        <td>{{ $bu->soldecaisse }}</td>
                        <td>{{ ucfirst($bu->statut) }}</td>
                        <td>
                            <a href="{{ route('bu.edit', $bu->id) }}" class="app-btn app-btn-success app-btn-sm app-btn-icon" >                                    <i class="fas fa-exchange-alt"></i>
</a>
                            <form action="{{ route('bu.destroy', $bu->id) }}" method="POST" style="display:inline;"class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn">                                        <i class="fas fa-trash-alt"></i>
</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
</div>
</div>
<style>
    .custom-card {
        border: 1px solid #033765; /* Bordure avec traits */
        border-radius: 8px; /* Coins arrondis */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Légère ombre */
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .custom-card:hover {
        transform: translateY(-5px); /* Animation au survol */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Ombre plus marquée */
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #ddd; /* Bordure en traits pour le tableau */
    }

    .btn-success {
        background-color: #033765;
        border-color: #033765;
    }

    .btn-success:hover {
        background-color: #033765;
        border-color: #033765;
    }
</style>
@endsection


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
        $('#stockTable').DataTable({
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
            order: [[2, 'desc']]
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit du stock ?')) {
                $(this).closest('form').submit();
            }
        });
        
     
    });
</script>
@endpush
