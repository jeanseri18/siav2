{{-- Page Index - Brouillard de Caisse --}}
@extends('layouts.app')

@section('title', 'Brouillard de Caisse')
@section('page-title', 'Brouillard de Caisse')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('caisse.brouillard') }}">Caisse</a></li>
<li class="breadcrumb-item active">Brouillard</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-money-check-alt me-2"></i>Brouillard de Caisse de {{ $bus->nom }}
            </h2>
            <!-- <div class="app-card-actions">
                <a href=" " class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-file-export"></i> Exporter
                </a>
            </div> -->
        </div>

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Motif</th>
                        <th>Solde Cumulé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brouillardCaisse as $item)
                        <tr>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                @if($item->type == 'entrée')
                                    <span class="app-badge app-badge-success app-badge-pill">
                                        <i class="fas fa-arrow-up me-1"></i> Entrée
                                    </span>
                                @else
                                    <span class="app-badge app-badge-danger app-badge-pill">
                                        <i class="fas fa-arrow-down me-1"></i> Sortie
                                    </span>
                                @endif
                            </td>
                            <td class="app-fw-bold">{{ $item->montant }}</td>
                            <td>{{ $item->motif }}</td>
                            <td>
                                <span class="app-badge app-badge-info app-badge-pill">
                                    {{ $item->solde_cumule }}
                                </span>
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