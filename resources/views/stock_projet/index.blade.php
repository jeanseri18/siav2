{{-- Page Index - Liste des stocks --}}
@extends('layouts.app')

@section('title', 'Gestion du Stock')
@section('page-title', 'Gestion du Stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item active">Stock</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class=" app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-boxes me-2"></i>Inventaire du Projet
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('stock.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter un produit
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
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Référence</th>
                        <th>Quantité</th>
                        <th>Unité</th>
                        <th>Statut</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                                <span>{{ $stock->article ? $stock->article->nom : 'Article introuvable' }}</span>
                            </div>
                        </td>
                        <td>{{ $stock->article ? $stock->article->reference : '-' }}</td>
                        <td class="app-fw-bold">{{ $stock->quantite }}</td>
                        <td>{{ $stock->article && $stock->article->unite ? $stock->article->unite->nom : '-' }}</td>
                        <td>
                            @php
                                $quantiteMin = $stock->article ? $stock->article->quantite_min : 0;
                                $statut = '';
                                if ($stock->quantite <= 0) {
                                    $statut = 'danger';
                                    $statutText = 'Épuisé';
                                    $statutIcon = 'exclamation-circle';
                                } elseif ($stock->quantite <= $quantiteMin) {
                                    $statut = 'warning';
                                    $statutText = 'Stock faible';
                                    $statutIcon = 'exclamation-triangle';
                                } else {
                                    $statut = 'success';
                                    $statutText = 'En stock';
                                    $statutIcon = 'check-circle';
                                }
                            @endphp
                            <span class="app-badge app-badge-{{ $statut }} app-badge-pill">
                                <i class="fas fa-{{ $statutIcon }} me-1"></i> {{ $statutText }}
                            </span>
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('stock.edit', $stock->id) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button type="button" class="app-btn app-btn-success app-btn-sm app-btn-icon" data-bs-toggle="modal" data-bs-target="#transfertModal" data-stock-id="{{ $stock->id }}" data-article-id="{{ $stock->article_id }}" data-article-name="{{ $stock->article ? $stock->article->nom : 'Article' }}">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                
                                <form action="{{ route('stock.destroy', $stock->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn">
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
                    <input type="hidden" name="article_id" id="transfertArticleId">
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-building me-2"></i>Projet Source</label>
                                <select name="id_projet_source" class="app-form-select" required>
                                    <option value="">Sélectionner le projet source</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" {{ session('projet_id') == $projet->id ? 'selected' : '' }}>
                                        {{ $projet->nom_projet }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">Le projet d'où provient le stock</div>
                            </div>
                        </div>

                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-bullseye me-2"></i>Projet Destination</label>
                                <select name="id_projet_destination" class="app-form-select" required>
                                    <option value="">Sélectionner le projet destination</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" {{ session('projet_id') == $projet->id ? 'disabled' : '' }}>
                                        {{ $projet->nom_projet }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">Le projet où le stock sera transféré</div>
                            </div>
                        </div>
                    </div>

                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-box me-2"></i>Article</label>
                                <div class="app-form-control" id="selectedArticle" style="background-color: var(--gray-100);">
                                    Article sélectionné
                                </div>
                                <div class="app-form-text">L'article à transférer</div>
                            </div>
                        </div>

                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-sort-numeric-up me-2"></i>Quantité</label>
                                <input type="number" name="quantite" class="app-form-control" placeholder="Quantité à transférer" min="1" required>
                                <div class="app-form-text">Nombre d'unités à transférer</div>
                            </div>
                        </div>
                    </div>

                    <div class="app-form-group">
                        <label class="app-form-label"><i class="fas fa-calendar-alt me-2"></i>Date de Transfert</label>
                        <input type="date" name="date_transfert" class="app-form-control" value="{{ date('Y-m-d') }}" required>
                        <div class="app-form-text">Date à laquelle le transfert est effectué</div>
                    </div>

                    <div class="app-form-group">
                        <label class="app-form-label"><i class="fas fa-comment-alt me-2"></i>Commentaire</label>
                        <textarea name="commentaire" class="app-form-control" rows="3" placeholder="Commentaire optionnel sur ce transfert..."></textarea>
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
        
        // Modal de transfert
        $('#transfertModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const articleId = button.data('article-id');
            const articleName = button.data('article-name');
            
            const modal = $(this);
            modal.find('#transfertArticleId').val(articleId);
            modal.find('#selectedArticle').text(articleName);
        });
    });
</script>
@endpush
@endsection