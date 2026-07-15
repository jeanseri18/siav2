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
                <a href="{{ route('articles.export.pdf') }}" class="app-btn app-btn-outline-danger app-btn-sm" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-file-pdf me-2"></i>Voir PDF
                </a>
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

                        @php
                            $ind = $stockIndicators[$article->id] ?? [];
                            $qCell = static function ($key) use ($ind) {
                                $v = (float) ($ind[$key] ?? 0);
                                if ($v <= 0) {
                                    return '-';
                                }
                                if (abs($v - round($v)) < 0.000001) {
                                    return number_format((int) round($v), 0, ',', ' ');
                                }

                                return number_format($v, 2, ',', ' ');
                            };
                            $cmpAffiche = (float) ($article->cout_moyen_pondere ?? 0);
                            if ($cmpAffiche <= 0) {
                                $cmpAffiche = (float) ($article->prix_unitaire ?? 0);
                            }
                        @endphp
                        <td>{{ $cmpAffiche > 0 ? number_format($cmpAffiche, 0, ',', ' ') : '0' }}</td>
                        <td class="app-fw-bold text-center">
                            @php
                                $quantiteStockGeneral = DB::table('stock_projet')
                                    ->whereIn('id_projet', $projets_bu)
                                    ->where('article_id', $article->id)
                                    ->sum('quantite');
                            @endphp
                            <a href="#" class="text-decoration-none stock-detail-link" data-article-id="{{ $article->id }}" data-bs-toggle="modal" data-bs-target="#stockDetailModal">
                                {{ $quantiteStockGeneral ?? 0 }}
                            </a>
                        </td>
                        <td class="text-center">{{ $qCell('demande_ravitaillement') }}</td>
                        <td class="text-center">{{ $qCell('ravitaillement_en_cours') }}</td>
                        <td class="text-center">{{ $qCell('retour_ravitaillement') }}</td>
                        <td class="text-center">{{ $qCell('approvisionnement_en_cours') }}</td>
                        <td class="text-center">{{ $qCell('retour_appro') }}</td>
                        <td class="text-center">{{ $qCell('transfert_in') }}</td>
                        <td class="text-center">{{ $qCell('transfert_out') }}</td>
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
