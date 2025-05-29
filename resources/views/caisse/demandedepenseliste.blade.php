{{-- Page Index - Liste des Demandes de Dépenses --}}
@extends('layouts.app')

@section('title', 'Demandes de Dépenses')
@section('page-title', 'Demandes de Dépenses')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('caisse.brouillard') }}">Caisse</a></li>
<li class="breadcrumb-item active">Demandes de Dépenses</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-money-bill-wave me-2"></i>Liste des Demandes de Dépenses - {{ $bus->nom }}
            </h2>

        </div>

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandes as $demande)
                        <tr>
                            <td>{{ $demande->created_at }}</td>
                            <td class="app-fw-bold">{{ $demande->montant }}</td>
                            <td>{{ $demande->motif }}</td>
                            <td>
                                @if($demande->statut == 'en attente')
                                    <span class="app-badge app-badge-warning app-badge-pill">
                                        <i class="fas fa-clock me-1"></i> En attente
                                    </span>
                                @elseif($demande->statut == 'validée')
                                    <span class="app-badge app-badge-success app-badge-pill">
                                        <i class="fas fa-check-circle me-1"></i> Validée
                                    </span>
                                @elseif($demande->statut == 'annulée')
                                    <span class="app-badge app-badge-danger app-badge-pill">
                                        <i class="fas fa-times-circle me-1"></i> Annulée
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="app-d-flex app-gap-2">
                                    @if($demande->statut == 'en attente')
                                        <form action="{{ route('caisse.validerDemandeDepense', $demande->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="app-btn app-btn-success app-btn-sm app-btn-icon">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('caisse.annulerDemandeDepense', $demande->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon">
                                                <i class="fas fa-times"></i>
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