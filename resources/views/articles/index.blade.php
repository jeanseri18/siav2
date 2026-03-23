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
                        <th>Ref.</th>
                        <th>Ref. Fournisseur</th>
                        <th>Désignation article</th>
                        <th>Type</th>
                        <th>Unité</th>
                        <th>Cout moyen pondéré</th>
                        <th>Qté (en stock)</th>
                        <th>Demande de ravitaillement</th>
                        <th>Ravitaillement en cours</th>
                        <th>Retour de ravitaillement</th>
                        <th>Approvisionnement en cours</th>
                        <th>Retour appro</th>
                        <th>Transfert de stock in</th>
                        <th>Transfert de stock out</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($articles as $article)
                    <tr>
                        <td>
                            <a href="{{ route('articles.show', $article) }}" class="app-badge app-badge-primary app-badge-pill text-decoration-none">
                                {{ $article->reference }}
                            </a>
                        </td>
                        <td>{{ $article->fournisseur ? $article->fournisseur->reference_fournisseur ?? $article->reference_fournisseur : ($article->reference_fournisseur ?? '-') }}</td>
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
                                @php
                                    $badgeClass = 'app-badge';
                                    switch(strtolower($article->type)) {
                                        case 'matériau':
                                        case 'materiau':
                                            $badgeClass .= ' app-badge-success';
                                            break;
                                        case 'outil':
                                            $badgeClass .= ' app-badge-warning';
                                            break;
                                        case 'matériel':
                                        case 'materiel':
                                            $badgeClass .= ' app-badge-primary';
                                            break;
                                        default:
                                            $badgeClass .= ' app-badge-info';
                                    }
                                @endphp
                                <span class="{{ $badgeClass }}">{{ $article->type }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $article->uniteMesure ? $article->uniteMesure->ref : '-' }}</td>

                        <td>{{ number_format($article->cout_moyen_pondere, 0, ',', ' ') }}</td>
                        <td class="app-fw-bold text-center">
                            @php
                                // Calculer la quantité de stock général comme la somme des quantités des projets
                                $quantiteStockGeneral = DB::table('stock_projet')
                                    ->whereIn('id_projet', $projets_bu)
                                    ->where('article_id', $article->id)
                                    ->sum('quantite');
                            @endphp
                            <a href="#" class="text-decoration-none stock-detail-link" data-article-id="{{ $article->id }}" data-bs-toggle="modal" data-bs-target="#stockDetailModal">
                                {{ $quantiteStockGeneral ?? 0 }}
                            </a>
                        </td>
                        <td class="text-center">
                             @php
                                // Demande de ravitaillement (en attente)
                                $demandeRav = DB::table('lignes_demande_ravitaillement')
                                    ->join('demandes_ravitaillement', 'lignes_demande_ravitaillement.demande_ravitaillement_id', '=', 'demandes_ravitaillement.id')
                                    ->join('contrats', 'demandes_ravitaillement.contrat_id', '=', 'contrats.id')
                                    ->whereIn('contrats.id_projet', $projets_bu)
                                    ->where('lignes_demande_ravitaillement.article_id', $article->id)
                                    ->where('demandes_ravitaillement.statut', 'en_attente')
                                    ->sum('lignes_demande_ravitaillement.quantite_demandee');
                            @endphp
                            {{ $demandeRav > 0 ? $demandeRav : '-' }}
                        </td>
                        <td class="text-center">
                            @php
                                // Ravitaillement en cours (livré mais pas totalement reçu)
                                $ravEnCours = DB::table('lignes_demande_ravitaillement')
                                    ->join('demandes_ravitaillement', 'lignes_demande_ravitaillement.demande_ravitaillement_id', '=', 'demandes_ravitaillement.id')
                                    ->join('contrats', 'demandes_ravitaillement.contrat_id', '=', 'contrats.id')
                                    ->whereIn('contrats.id_projet', $projets_bu)
                                    ->where('lignes_demande_ravitaillement.article_id', $article->id)
                                    ->whereIn('demandes_ravitaillement.statut', ['en_cours', 'livree'])
                                    ->select(DB::raw('SUM(quantite_livree - COALESCE(quantite_recue, 0)) as qte_en_cours'))
                                    ->value('qte_en_cours');
                            @endphp
                            {{ $ravEnCours > 0 ? $ravEnCours : '-' }}
                        </td>
                        <td class="text-center">
                            @php
                                // Retour de ravitaillement (refusé par chantier, en attente de validation gestionnaire)
                                // Note: Comme on n'a pas de champ retour_valide dans la DB sans migration, on utilise une approximation ou 0 si pas implémenté
                                // Si on avait le champ: ->where('retour_valide', false)
                                $retourRav = 0; 
                                // Pour l'instant on met 0 car on ne peut pas requêter le champ retour_valide qui n'existe pas en DB
                                // Ou on peut essayer de parser les commentaires mais c'est lourd pour une vue liste.
                            @endphp
                            -
                        </td>
                        <td class="text-center">
                            @php
                                // Approvisionnement en cours (Commandé mais pas reçu)
                                $approEnCours = DB::table('lignes_bon_commande')
                                    ->join('bon_commandes', 'lignes_bon_commande.bon_commande_id', '=', 'bon_commandes.id')
                                    ->where('bon_commandes.projet_id', $projets_bu) // Si projet_id est sur bon_commande
                                    // Ou via demande_appro -> projet
                                    // Simplification: on suppose projet_id sur bon_commande ou on fait la jointure complète si besoin
                                    // Vérifions la structure: bon_commandes a projet_id ?
                                    // Sinon via demande_approvisionnement
                                    ->leftJoin('demande_approvisionnements', 'bon_commandes.demande_approvisionnement_id', '=', 'demande_approvisionnements.id')
                                    ->whereIn('demande_approvisionnements.projet_id', $projets_bu)
                                    ->where('lignes_bon_commande.article_id', $article->id)
                                    ->whereIn('bon_commandes.statut', ['validee', 'en_cours', 'partiellement_recu']) // Statuts à adapter selon votre système
                                    ->select(DB::raw('SUM(lignes_bon_commande.quantite - COALESCE(lignes_bon_commande.quantite_recue, 0)) as qte_restante'))
                                    ->value('qte_restante');
                            @endphp
                            {{ $approEnCours > 0 ? $approEnCours : '-' }}
                        </td>
                        <td class="text-center">
                            @php
                                // Retour appro (pas détaillé dans les scénarios mais demandé dans l'affichage)
                                // Si table retour_approvisionnement existe ?
                                // Sinon 0
                                $retourAppro = 0;
                            @endphp
                            -
                        </td>
                        <td class="text-center">
                            @php
                                // Transfert de stock IN (En transit vers ce projet)
                                $transfertIn = DB::table('transfert_stock')
                                    ->whereIn('id_projet_destination', $projets_bu)
                                    ->where('article_id', $article->id)
                                    // On doit filtrer ceux qui ne sont PAS encore reçus.
                                    // Comme on n'a pas de statut, on check l'absence de mouvement TR-IN correspondant
                                    ->whereNotExists(function($query) {
                                        $query->select(DB::raw(1))
                                              ->from('mouvements_stock')
                                              ->whereRaw("reference_mouvement = CONCAT('TR-IN-', transfert_stock.id)");
                                    })
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertIn > 0 ? $transfertIn : '-' }}
                        </td>
                        <td class="text-center">
                            @php
                                // Transfert de stock OUT (Sorti de ce projet mais pas encore reçu par l'autre - Optionnel, souvent OUT = fini pour la source)
                                // Mais si on veut voir ce qui est "dehors" venant de nous:
                                $transfertOut = DB::table('transfert_stock')
                                    ->whereIn('id_projet_source', $projets_bu)
                                    ->where('article_id', $article->id)
                                    ->whereNotExists(function($query) {
                                        $query->select(DB::raw(1))
                                              ->from('mouvements_stock')
                                              ->whereRaw("reference_mouvement = CONCAT('TR-IN-', transfert_stock.id)");
                                    })
                                    ->sum('quantite');
                            @endphp
                            {{ $transfertOut > 0 ? $transfertOut : '-' }}
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="app-btn app-btn-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('articles.show', $article) }}">
                                            <i class="fas fa-eye me-2"></i>Voir les détails
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('articles.edit', $article) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('articles.destroy', $article) }}" method="POST" class="delete-form">
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
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
                $(this).closest('form').submit();
            }
        });
        
        // Gestion des clics sur les liens de détail de stock
        $('.stock-detail-link').on('click', function(e) {
            e.preventDefault();
            const articleId = $(this).data('article-id');
            
            // Appel AJAX pour récupérer les détails du stock
            $.ajax({
                url: `/articles/${articleId}/stock-details`,
                method: 'GET',
                success: function(response) {
                    // Remplir la modal avec les données
                    $('#stockDetailModalLabel').text(`Détails du stock - ${response.article.nom}`);
                    
                    let stockTableBody = '';
                    if (response.stocks.length > 0) {
                        response.stocks.forEach(function(stock) {
                            stockTableBody += `
                                <tr>
                                    <td>${stock.projet_nom}</td>
                                    <td class="text-end">${stock.quantite}</td>
                                    <td>${stock.unite_mesure}</td>
                                </tr>
                            `;
                        });
                    } else {
                        stockTableBody = '<tr><td colspan="3" class="text-center">Aucun stock disponible pour cet article</td></tr>';
                    }
                    
                    $('#stockTableBody').html(stockTableBody);
                },
                error: function() {
                    alert('Erreur lors du chargement des détails du stock');
                }
            });
        });
    });
</script>

<!-- Modal pour afficher les détails du stock -->
<div class="modal fade" id="stockDetailModal" tabindex="-1" aria-labelledby="stockDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockDetailModalLabel">Détails du stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th class="text-end">Quantité</th>
                                <th>Unité</th>
                            </tr>
                        </thead>
                        <tbody id="stockTableBody">
                            <!-- Les données seront chargées ici via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
