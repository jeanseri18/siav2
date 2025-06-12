@extends('layouts.app')

@section('title', 'Liste des Articles')
@section('page-title', 'Liste des Articles')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_article') }}">stock</a></li>

<li class="breadcrumb-item active">Articles</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-boxes me-2"></i>Liste des Articles
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('articles.create') }}" class="app-btn app-btn-primary app-btn-icon">
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
            <table id="Table" class="app-table display">
                <thead>
                    <tr>
                        <th>REF.</th>
                        <th>FAMILLE</th>
                        <th>SOUS FAMILLE</th>
                        <th>REF FOURN.</th>
                        <th>DESIGNATION ARTICLE</th>
                        <th>TYPE</th>
                        <th>UNITE</th>
                        <th>COUT MOYEN PONDERE</th>
                        <th>QTE DISPO</th>
                        <th>PAY EN COURS</th>
                        <th>RETOUR HIVE</th>
                        <th>APPRO ARRIVE</th>
                        <th>RETOUR APPRO</th>
                        <th>TRANSFERT DE STOCK IN</th>
                        <th>TRANSFERT DE STOCK OUT</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($articles as $article)
                    <tr>
                        <td>
                            <span class="app-badge app-badge-primary app-badge-pill">
                                {{ $article->reference }}
                            </span>
                        </td>
                        <td>{{ $article->categorie ? $article->categorie->nom : '-' }}</td>
                        <td>{{ $article->sousCategorie ? $article->sousCategorie->nom : '-' }}</td>
                        <td>{{ $article->fournisseur ? $article->fournisseur->nom_raison_sociale : '-' }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-box-open text-primary"></i>
                                </div>
                                <span>{{ $article->nom }}</span>
                            </div>
                        </td>
                        <td>
                            @if($article->type)
                                <span class="app-badge app-badge-info">{{ $article->type }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $article->unite_mesure }}</td>
                        <td>{{ number_format($article->cout_moyen_pondere, 0, ',', ' ') }}</td>
                        <td class="app-fw-bold">{{ $article->quantite_stock }}</td>
                        <td class="text-center">
                            @php
                                // Calculer PAY EN COURS pour tous les projets de la BU
                                $payEnCours = DB::table('ligne_demande_approvisionnement')
                                    ->join('demande_approvisionnements', 'ligne_demande_approvisionnement.demande_id', '=', 'demande_approvisionnements.id')
                                    ->whereIn('demande_approvisionnements.projet_id', $projets_bu)
                                    ->where('ligne_demande_approvisionnement.article_id', $article->id)
                                    ->where('demande_approvisionnements.statut', 'approuvée')
                                    ->sum('ligne_demande_approvisionnement.quantite_demandee');
                            @endphp
                            {{ $payEnCours ?? 0 }}
                        </td>
                        <td class="text-center">
                            @php
                                // Calculer RETOUR HIVE pour tous les projets de la BU
                                $retourHive = DB::table('transfert_stock')
                                    ->whereIn('id_projet_destination', $projets_bu)
                                    ->where('article_id', $article->id)
                                    ->where('type_transfert', 'retour_hive')
                                    ->sum('quantite');
                            @endphp
                            {{ $retourHive ?? 0 }}
                        </td>
                        <td class="text-center">
                            @php
                                // Calculer APPRO ARRIVE pour tous les projets de la BU
                                $approArrive = DB::table('lignes_bon_commande')
                                    ->join('bon_commandes', 'lignes_bon_commande.bon_commande_id', '=', 'bon_commandes.id')
                                    ->whereIn('bon_commandes.projet_id', $projets_bu)
                                    ->where('lignes_bon_commande.article_id', $article->id)
                                    ->where('bon_commandes.statut', 'livrée')
                                    ->sum('lignes_bon_commande.quantite_livree');
                            @endphp
                            {{ $approArrive ?? 0 }}
                        </td>
                        <td class="text-center">
                            @php
                                // Calculer RETOUR APPRO pour tous les projets de la BU
                                $retourAppro = DB::table('retour_approvisionnement')
                                    ->whereIn('projet_id', $projets_bu)
                                    ->where('article_id', $article->id)
                                    ->where('statut', 'accepté')
                                    ->sum('quantite_retournee');
                            @endphp
                            {{ $retourAppro ?? 0 }}
                        </td>
                        <td class="text-center">
                            @php
                                // Calculer TRANSFERT DE STOCK IN pour tous les projets de la BU
                                $transfertIn = DB::table('transfert_stock')
                                    ->whereIn('id_projet_destination', $projets_bu)
                                    ->where('article_id', $article->id)
                                    ->where('type_transfert', 'normal')
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertIn ?? 0 }}
                        </td>
                        <td class="text-center">
                            @php
                                // Calculer TRANSFERT DE STOCK OUT pour tous les projets de la BU
                                $transfertOut = DB::table('transfert_stock')
                                    ->whereIn('id_projet_source', $projets_bu)
                                    ->where('article_id', $article->id)
                                    ->where('type_transfert', 'normal')
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertOut ?? 0 }}
                        </td>
                        <td>
                            <div class="app-d-flex app-gap-1">
                                <a href="{{ route('articles.show', $article) }}" class="app-btn app-btn-info app-btn-sm app-btn-icon" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('articles.edit', $article) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('articles.destroy', $article) }}" method="POST" class="delete-form" style="display:inline;">
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection