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
                    <i class="fas fa-plus"></i> Ajouter un article
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
                        <th>Réf.</th>
                        <th>Catégorie</th>
                        <th>Sous Catégorie</th>
                        <!-- <th>Réf Fourn.</th> -->
                        <th>Désignation Article</th>
                        <th>Type</th>
                        <th>Unité</th>
                        <!-- <th>Coût Moyen Pondéré</th> -->
                        <th>Qté Disponible</th>
                        <!-- <th>Paiement en Cours</th> -->
                        <!-- <th>Retour Ruche</th> -->
                        <!-- <th>Appro en Cours</th>
                        <th>Retour Appro</th> -->
                        <!-- <th>Transfert Stock In</th>
                        <th>Transfert Stock Out</th> -->
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td>
                            @if($stock->article && $stock->article->reference)
                                <a href="{{ route('articles.show', $stock->article) }}" class="badge bg-primary text-decoration-none">{{ $stock->article->reference }}</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $stock->article && $stock->article->categorie ? $stock->article->categorie->nom : '-' }}</td>
                        <td>{{ $stock->article && $stock->article->sousCategorie ? $stock->article->sousCategorie->nom : '-' }}</td>
                        <!-- <td>{{ $stock->article && $stock->article->fournisseur ? $stock->article->fournisseur->nom_raison_sociale . ' ' . $stock->article->fournisseur->prenoms : '-' }}</td> -->
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                                <span>{{ $stock->article ? $stock->article->nom : 'Article introuvable' }}</span>
                            </div>
                        </td>
                        <td>{{ $stock->article ? $stock->article->type : '-' }}</td>
                        <td>{{ $stock->article->uniteMesure->ref }}</td>
                        <!-- <td class="app-fw-bold">{{ $stock->cout_moyen_pondere ? number_format($stock->cout_moyen_pondere, 2) : '0' }}</td> -->
                        <td class="app-fw-bold">{{ $stock->qte_disponible ?? $stock->quantite }}</td>
                        <!-- <td>{{ $stock->paiement_en_cours ?? '0' }}</td> -->
                        <!-- <td>{{ $stock->retour_ruche ?? '0' }}</td> -->
                        <!-- <td>
                            @php
                                $approEnCours = \App\Models\LigneDemandeApprovisionnement::whereHas('demandeApprovisionnement', function($query) use ($projet_id) {
                                    $query->where('projet_id', $projet_id)
                                          ->where('statut', 'approuvée');
                                })
                                ->where('article_id', $stock->article_id)
                                ->sum('quantite_demandee');
                            @endphp
                            {{ $approEnCours }}
                        </td>
                        <td>
                            @php
                                $retourHive = \App\Models\TransfertStock::where('id_projet_destination', $projet_id)
                                    ->where('article_id', $stock->article_id)
                                    ->where('type_transfert', 'retour_hive')
                                    ->sum('quantite');
                            @endphp
                            {{ $retourHive }}
                        </td>
                        <td>
                            @php
                                $approArrive = \DB::table('lignes_bon_commande')
                                    ->join('bon_commandes', 'lignes_bon_commande.bon_commande_id', '=', 'bon_commandes.id')
                                    ->leftJoin('demande_approvisionnements', 'bon_commandes.demande_approvisionnement_id', '=', 'demande_approvisionnements.id')
                                    ->leftJoin('demande_achats', 'bon_commandes.demande_achat_id', '=', 'demande_achats.id')
                                    ->where(function($query) use ($projet_id) {
                                        $query->where('demande_approvisionnements.projet_id', $projet_id)
                                              ->orWhere('demande_achats.projet_id', $projet_id);
                                    })
                                    ->where('lignes_bon_commande.article_id', $stock->article_id)
                                    ->where('bon_commandes.statut', 'livrée')
                                    ->sum('lignes_bon_commande.quantite_livree');
                            @endphp
                            {{ $approArrive ?? '0' }}
                        </td> -->
                        <!-- <td>
                            @php
                                $retourAppro = \App\Models\RetourApprovisionnement::where('projet_id', $projet_id)
                                    ->where('article_id', $stock->article_id)
                                    ->where('statut', 'accepté')
                                    ->sum('quantite_retournee');
                            @endphp
                            {{ $retourAppro }}
                        </td> -->
                        <!-- <td>
                            @php
                                $transfertIn = \App\Models\TransfertStock::where('id_projet_destination', $projet_id)
                                    ->where('article_id', $stock->article_id)
                                    ->where('type_transfert', 'normal')
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertIn }}
                        </td>
                        <td>
                            @php
                                $transfertOut = \App\Models\TransfertStock::where('id_projet_source', $projet_id)
                                    ->where('article_id', $stock->article_id)
                                    ->where('type_transfert', 'normal')
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertOut }}
                        </td>-->
                        <td> 
                            <div class="dropdown">
                                <button class="app-btn app-btn-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('stock.show', $stock->id) }}">
                                            <i class="fas fa-eye me-2"></i>Voir les détails
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('stock.edit', $stock->id) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#transfertModal" data-stock-id="{{ $stock->id }}" data-article-id="{{ $stock->article_id }}" data-article-name="{{ $stock->article ? $stock->article->nom : 'Article' }}">
                                            <i class="fas fa-exchange-alt me-2"></i>Transférer
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('stock.destroy', $stock->id) }}" method="POST" class="delete-form">
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cet article du stock ?')) {
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