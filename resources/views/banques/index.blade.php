@extends('layouts.app')

@section('title', 'Liste des Banques')
@section('page-title', 'Liste des Banques')

@section('breadcrumb')
<li class="breadcrumb-item active">Banques</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-university me-2"></i>Liste des Banques
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('banques.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter une banque
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
            <table id="Table" class="app-table display">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Code Banque</th>
                        <th>Code Guichet</th>
                        <th>Numéro de Compte</th>
                        <th>Clé RIB</th>
                        <th>IBAN</th>
                        <th>Code SWIFT</th>
                        <th>Domiciliation</th>
                        <th>Téléphone</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banques as $banque)
                    <tr>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-university text-primary"></i>
                                </div>
                                <span><strong>{{ $banque->nom }}</strong></span>
                            </div>
                        </td>
                        <td>
                            <span class="app-badge app-badge-secondary app-badge-pill">
                                {{ $banque->code_banque }}
                            </span>
                        </td>
                        <td>
                            <span class="app-badge app-badge-info app-badge-pill">
                                {{ $banque->code_guichet }}
                            </span>
                        </td>
                        <td>
                            <code class="text-muted">{{ $banque->numero_compte }}</code>
                        </td>
                        <td>
                            <span class="app-badge app-badge-light app-badge-pill">
                                {{ $banque->cle_rib }}
                            </span>
                        </td>
                        <td>
                            @if($banque->iban)
                            <code class="text-primary">{{ $banque->iban }}</code>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($banque->code_swift)
                            <span class="app-badge app-badge-primary app-badge-pill">
                                {{ $banque->code_swift }}
                            </span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $banque->domiciliation }}</td>
                        <td>
                            @if($banque->telephone)
                            <a href="tel:{{ $banque->telephone }}" class="app-btn-link">
                                <i class="fas fa-phone-alt me-1"></i>{{ $banque->telephone }}
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="app-btn app-btn-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('banques.edit', $banque) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('banques.destroy', $banque) }}" method="POST" class="delete-form">
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette banque ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection
