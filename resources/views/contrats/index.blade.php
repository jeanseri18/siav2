{{-- Page Index - Liste des Contrats --}}
@extends('layouts.app')

@section('title', 'Gestion des Contrats')
@section('page-title', 'Gestion des Contrats')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item active">Contrats</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class=" app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-contract me-2"></i>Liste des Contrats
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('contrats.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter un nouveau contrat
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
                        <th>Référence</th>
                        <th>Nom du contrat</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Statut</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contrats as $contrat)
                        <tr>
                            <td>
                                <div class="app-d-flex app-align-items-center app-gap-2">
                                    <div class="item-icon">
                                        <i class="fas fa-hashtag text-primary"></i>
                                    </div>
                                    <span>{{ $contrat->ref_contrat }}</span>
                                </div>
                            </td>
                            <td>{{ $contrat->nom_contrat }}</td>
                            <td>{{ $contrat->date_debut }}</td>
                            <td>{{ $contrat->date_fin }}</td>
                            <td>
                                @if($contrat->statut == 'en cours')
                                    <span class="app-badge app-badge-warning app-badge-pill">
                                        <i class="fas fa-spinner me-1"></i> En cours
                                    </span>
                                @elseif($contrat->statut == 'terminé')
                                    <span class="app-badge app-badge-success app-badge-pill">
                                        <i class="fas fa-check-circle me-1"></i> Terminé
                                    </span>
                                @elseif($contrat->statut == 'annulé')
                                    <span class="app-badge app-badge-danger app-badge-pill">
                                        <i class="fas fa-ban me-1"></i> Annulé
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="app-d-flex app-gap-2">
                                    <a href="{{ route('contrats.edit', $contrat->id) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('contrats.destroy', $contrat->id) }}" method="POST" class="delete-form" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('contrats.show', $contrat->id) }}" class="app-btn app-btn-info app-btn-sm app-btn-icon">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection