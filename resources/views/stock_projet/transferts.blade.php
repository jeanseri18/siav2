@extends('layouts.app')

@section('title', 'Liste des Transferts de Stock')
@section('page-title', 'Liste des Transferts de Stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item active">Transferts de Stock</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-exchange-alt me-2"></i>Liste des Transferts de Stock
            </h2>
            <div class="app-card-actions">
                <button type="button" class="app-btn app-btn-primary app-btn-icon" data-bs-toggle="modal" data-bs-target="#transfertModal">
                    <i class="fas fa-plus"></i> Effectuer un transfert
                </button>
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

        @if(session('error'))
        <div class="app-alert app-alert-danger">
            <div class="app-alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">{{ session('error') }}</div>
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
                        <th>Projet Source</th>
                        <th>Projet Destination</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Date de transfert</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transferts as $transfert)
                    <tr>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-project-diagram text-primary"></i>
                                </div>
                                <span>{{ $transfert->projetSource->nom_projet }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-project-diagram text-success"></i>
                                </div>
                                <span>{{ $transfert->projetDestination->nom_projet }}</span>
                            </div>
                        </td>
                        <td>{{ $transfert->nom_produit }}</td>
                        <td class="app-fw-bold">{{ $transfert->quantite }}</td>
                        <td>{{ $transfert->date_transfert }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Transfert -->
<div class="modal fade" id="transfertModal" tabindex="-1" aria-labelledby="transfertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content app-modal">
            <div class="app-modal-header">
                <h5 class="app-modal-title" id="transfertModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Transférer du Stock
                </h5>
                <button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="app-modal-body">
                <form action="{{ route('transferts.store') }}" method="POST" class="app-form" id="transfertForm">
                    @csrf
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="id_projet_source" class="app-form-label">
                                    <i class="fas fa-building me-2"></i>Projet Source
                                </label>
                                <select name="id_projet_source" id="id_projet_source" class="app-form-select" required>
                                    <option value="">-- Sélectionner le projet source --</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">Le projet d'où provient le stock</div>
                            </div>
                        </div>

                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="id_projet_destination" class="app-form-label">
                                    <i class="fas fa-bullseye me-2"></i>Projet Destination
                                </label>
                                <select name="id_projet_destination" id="id_projet_destination" class="app-form-select" required>
                                    <option value="">-- Sélectionner le projet destination --</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">Le projet où le stock sera transféré</div>
                            </div>
                        </div>
                    </div>

                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="article_id" class="app-form-label">
                                    <i class="fas fa-box me-2"></i>Article
                                </label>
                                <select name="article_id" id="article_id" class="app-form-select" required>
                                    <option value="">-- Sélectionner un article --</option>
                                    @foreach($articles as $article)
                                    <option value="{{ $article->id }}">{{ $article->nom }}</option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">L'article à transférer</div>
                            </div>
                        </div>

                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="quantite" class="app-form-label">
                                <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                            </label>
                                <input type="number" name="quantite" id="quantite" class="app-form-control" min="1" required>
                                <div class="app-form-text">Nombre d'unités à transférer</div>
                            </div>
                        </div>
                    </div>

                    <div class="app-form-group">
                        <label for="date_transfert" class="app-form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Date de Transfert
                        </label>
                        <input type="date" name="date_transfert" id="date_transfert" class="app-form-control" value="{{ date('Y-m-d') }}" required>
                        <div class="app-form-text">Date à laquelle le transfert est effectué</div>
                    </div>
                </form>
            </div>
            <div class="app-modal-footer">
                <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="app-btn app-btn-primary" onclick="document.getElementById('transfertForm').submit()">
                    <i class="fas fa-paper-plane me-2"></i>Effectuer le Transfert
                </button>
            </div>
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
        
        // Contrôle pour éviter qu'un projet soit à la fois source et destination
        $('#id_projet_source, #id_projet_destination').change(function() {
            const sourceValue = $('#id_projet_source').val();
            const destValue = $('#id_projet_destination').val();
            
            if (sourceValue && destValue && sourceValue === destValue) {
                alert('Le projet source et le projet destination ne peuvent pas être identiques.');
                $(this).val('');
            }
        });
    });
</script>
@endpush
@endsection