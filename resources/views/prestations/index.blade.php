@extends('layouts.app')

@section('title', 'Liste des Prestations')
@section('page-title', 'Liste des Prestations')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Prestations</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-tools me-2"></i>Liste des Prestations
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('prestations.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter une prestation
                </a>
            </div>
        </div>

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table display">
                <thead>
                    <tr>
                        <th>Artisan</th>
                        <th>Contrat</th>
                        <th>Prestation</th>
                        <th>Montant</th>
                        <th>Avancement</th>
                        <th>Statut</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestations as $prestation)
                    <tr>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-hard-hat text-primary"></i>
                                </div>
                                <span>{{ $prestation->artisan ? $prestation->artisan->nom : 'Non assigné' }}</span>
                            </div>
                        </td>
                        <td>{{ $prestation->contrat->nom_contrat }}</td>
                        <td>{{ $prestation->prestation_titre }}</td>
                        <td>{{ number_format($prestation->montant, 2, ',', ' ') }} FCFA</td>
                        <td>
                            @if($prestation->taux_avancement)
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                    style="width: {{ $prestation->taux_avancement }}%" 
                                    aria-valuenow="{{ $prestation->taux_avancement }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $prestation->taux_avancement }}%
                                </div>
                            </div>
                            @else
                            0%
                            @endif
                        </td>
                        <td>
                            <span class="app-badge app-badge-{{ $prestation->statut == 'En cours' ? 'warning' : ($prestation->statut == 'Terminée' ? 'success' : 'danger') }} app-badge-pill">
                                {{ $prestation->statut }}
                            </span>
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('prestations.edit', $prestation->id) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('prestations.destroy', $prestation->id) }}" method="POST" class="delete-form">
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette prestation ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection