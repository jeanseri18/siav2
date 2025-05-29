{{-- Page Index - Liste des Documents --}}
@extends('layouts.app')

@section('title', 'Gestion des Documents')
@section('page-title', 'Gestion des Documents')

@section('breadcrumb')
<li class="breadcrumb-item active">Documents</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="container app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-alt me-2"></i>Liste des Documents
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('documents.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter un Document
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
                        <th>Nom</th>
                        <th>Fichier</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                        <tr>
                            <td>
                                <div class="app-d-flex app-align-items-center app-gap-2">
                                    <div class="item-icon">
                                        <i class="fas fa-file-alt text-primary"></i>
                                    </div>
                                    <span>{{ $document->nom }}</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $document->chemin) }}" target="_blank" class="app-btn app-btn-info app-btn-sm app-btn-icon">
                                    <i class="fas fa-eye me-1"></i> Voir
                                </a>
                            </td>
                            <td>
                                <div class="app-d-flex app-gap-2">
                                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="delete-form" onsubmit="return confirm('Voulez-vous supprimer ce document ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon">
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
    });
</script>
@endpush
@endsection