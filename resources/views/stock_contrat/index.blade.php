@extends('layouts.app')

@section('title', 'Liste des Stocks')
@section('page-title', 'Liste des Stocks')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Stock</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-boxes me-2"></i>Liste des Stocks pour le Projet
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('stock_contrat.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter un produit au stock
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
                        <th>Réf.</th>
                        <th>Famille</th>
                        <th>Sous Famille</th>
                        <th>Réf Fourn.</th>
                        <th>Désignation Article</th>
                        <th>Type</th>
                        <th>Unité</th>
                        <th>Coût Moyen Pondéré</th>
                        <th>Qté Disponible</th>
                        <th>Paiement en Cours</th>
                        <th>Retour Ruche</th>
                        <th>Appro en Cours</th>
                        <th>Retour Appro</th>
                        <th>Transfert Stock In</th>
                        <th>Transfert Stock Out</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td>{{ $stock->article ? $stock->article->reference : '-' }}</td>
                        <td>{{ $stock->article && $stock->article->categorie ? $stock->article->categorie->nom : '-' }}</td>
                        <td>{{ $stock->article && $stock->article->sousCategorie ? $stock->article->sousCategorie->nom : '-' }}</td>
                        <td>{{ $stock->article && $stock->article->fournisseur ? $stock->article->fournisseur->nom : '-' }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-box text-primary"></i>
                                </div>
                                <span>{{ $stock->article ? $stock->article->nom : 'Article introuvable' }}</span>
                            </div>
                        </td>
                        <td>{{ $stock->article ? $stock->article->type : '-' }}</td>
                        <td>{{ $stock->article && $stock->article->unite ? $stock->article->unite->nom : '-' }}</td>
                        <td class="app-fw-bold">{{ $stock->cout_moyen_pondere ? number_format($stock->cout_moyen_pondere, 2) : '0' }}</td>
                        <td class="app-fw-bold">{{ $stock->qte_disponible ?? $stock->quantite }}</td>
                        <td>{{ $stock->paiement_en_cours ?? '0' }}</td>
                        <td>{{ $stock->retour_ruche ?? '0' }}</td>
                        <td>
                            @php
                                $approEnCours = \App\Models\LigneDemandeApprovisionnement::whereHas('demandeApprovisionnement', function($query) use ($contrat_id) {
                                    $query->where('projet_id', $contrat_id)
                                          ->where('statut', 'approuvée');
                                })
                                ->where('article_id', $stock->article_id)
                                ->sum('quantite_demandee');
                            @endphp
                            {{ $approEnCours }}
                        </td>
                        <td>
                            @php
                                $retourHive = \App\Models\TransfertStock::where('id_projet_destination', $contrat_id)
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
                                    ->where('bon_commandes.contrat_id', $contrat_id)
                                    ->where('lignes_bon_commande.article_id', $stock->article_id)
                                    ->where('bon_commandes.statut', 'livrée')
                                    ->sum('lignes_bon_commande.quantite_livree');
                            @endphp
                            {{ $approArrive ?? '0' }}
                        </td>
                        <td>
                            @php
                                $retourAppro = \App\Models\RetourApprovisionnement::whereHas('bonCommande', function($query) use ($contrat_id) {
                                        $query->where('contrat_id', $contrat_id);
                                    })
                                    ->where('article_id', $stock->article_id)
                                    ->where('statut', 'accepté')
                                    ->sum('quantite_retournee');
                            @endphp
                            {{ $retourAppro }}
                        </td>
                        <td>
                            @php
                                $transfertIn = \App\Models\TransfertStock::where('id_projet_destination', $contrat_id)
                                    ->where('article_id', $stock->article_id)
                                    ->where('type_transfert', 'normal')
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertIn }}
                        </td>
                        <td>
                            @php
                                $transfertOut = \App\Models\TransfertStock::where('id_projet_source', $contrat_id)
                                    ->where('article_id', $stock->article_id)
                                    ->where('type_transfert', 'normal')
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertOut }}
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-2">
                                <a href="{{ route('stock_contrat.edit', $stock->id) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stock_contrat.destroy', $stock->id) }}" method="POST" class="delete-form">
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection