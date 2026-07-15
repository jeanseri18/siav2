<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Ventes - {{ date('d/m/Y') }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
            background: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #033d71;
        }

        .logo { display: inline-block; border: 0; }

        .company-info {
            text-align: right;
        }

        .header h1 {
            color: #033d71;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header .subtitle {
            color: #6c757d;
            font-size: 14px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .info-block {
            flex: 1;
        }

        .info-block h3 {
            color: #495057;
            font-size: 14px;
            margin-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 3px;
        }

        .info-block p {
            margin: 3px 0;
            font-size: 11px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }

        .stat-card .icon {
            font-size: 18px;
            margin-bottom: 8px;
            color: #033d71;
        }

        .stat-card .value {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 3px;
        }

        .stat-card .label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
        }

        .table-container {
            margin-bottom: 20px;
        }

        .table-title {
            background: #033d71;
            color: white;
            padding: 10px 15px;
            margin: 0;
            font-size: 14px;
            border-radius: 5px 5px 0 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0 0 5px 5px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table th {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            color: #495057;
        }

        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 10px;
            vertical-align: top;
        }

        .table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .table tbody tr:hover {
            background: #e3f2fd;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 10px;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .badge-light {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .total-row {
            background: #033d71 !important;
            color: white;
            font-weight: bold;
        }

        .total-row td {
            border-color: #033d71;
        }

        .articles-list {
            max-width: 200px;
        }

        .article-item {
            display: inline-block;
            background: #e9ecef;
            padding: 2px 6px;
            margin: 1px;
            border-radius: 3px;
            font-size: 9px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .no-data .icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        @media print {
            body {
                margin: 10px;
            }
            
            .table {
                font-size: 9px;
            }
            
            .stat-card {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- En-tête du rapport -->
    <div class="header">
        <div>
            @if(isset($bus) && $bus && $bus->logo)
                @php
                    $legacyLogoPath = public_path('storage/' . $bus->logo);
                    $legacyLogoDims = \App\Support\PdfBranding::logoDisplaySize(is_file($legacyLogoPath) ? $legacyLogoPath : null);
                @endphp
                <img
                    src="{{ $legacyLogoPath }}"
                    alt="Logo"
                    class="logo"
                    width="{{ $legacyLogoDims['width'] }}"
                    height="{{ $legacyLogoDims['height'] }}"
                    style="width: {{ $legacyLogoDims['width'] }}px; height: {{ $legacyLogoDims['height'] }}px;"
                >
            @endif
        </div>
        <div class="company-info">
            <h1>📊 Rapport des Ventes</h1>
            <div class="subtitle">Généré le {{ date('d/m/Y à H:i') }}</div>
            @if(isset($bus) && $bus)
                <div>{{ $bus->nom }}</div>
            @endif
        </div>
    </div>

    <!-- Informations sur les filtres appliqués -->
    <div class="info-section">
        <div class="info-block">
            <h3>🔍 Critères de recherche</h3>
            <p><strong>Période :</strong> {{ request('date_debut') ? date('d/m/Y', strtotime(request('date_debut'))) : 'Toutes' }} - {{ request('date_fin') ? date('d/m/Y', strtotime(request('date_fin'))) : 'Toutes' }}</p>
            <p><strong>Client :</strong> {{ request('client_id') ? $clients->find(request('client_id'))->prenoms ?? 'Inconnu' : 'Tous les clients' }}</p>
            <p><strong>Article :</strong> {{ request('article_id') ? $articles->find(request('article_id'))->nom ?? 'Inconnu' : 'Tous les articles' }}</p>
            <p><strong>Statut :</strong> {{ request('statut') ?: 'Tous les statuts' }}</p>
        </div>
        <div class="info-block">
            <h3>📋 Résumé</h3>
            <p><strong>Nombre de ventes :</strong> {{ $ventes->count() }}</p>
            <p><strong>Total général :</strong> {{ number_format($ventes->sum('total'), 0, ',', ' ') }} FCFA</p>
            <p><strong>Moyenne par vente :</strong> {{ $ventes->count() > 0 ? number_format($ventes->avg('total'), 0, ',', ' ') : 0 }} FCFA</p>
            <p><strong>Articles vendus :</strong> {{ $ventes->sum(function($vente) { return $vente->articles->sum('pivot.quantite'); }) }}</p>
        </div>
    </div>

    @if($ventes->isEmpty())
        <!-- Aucune donnée -->
        <div class="no-data">
            <div class="icon">🔍</div>
            <h3>Aucune vente trouvée</h3>
            <p>Aucune vente ne correspond aux critères sélectionnés.</p>
        </div>
    @else
        <!-- Statistiques en grille -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">🛒</div>
                <div class="value">{{ $ventes->count() }}</div>
                <div class="label">Ventes</div>
            </div>
            <div class="stat-card">
                <div class="icon">💰</div>
                <div class="value">{{ number_format($ventes->sum('total'), 0, ',', ' ') }}</div>
                <div class="label">FCFA Total</div>
            </div>
            <div class="stat-card">
                <div class="icon">📊</div>
                <div class="value">{{ $ventes->count() > 0 ? number_format($ventes->avg('total'), 0, ',', ' ') : 0 }}</div>
                <div class="label">FCFA Moyenne</div>
            </div>
            <div class="stat-card">
                <div class="icon">📦</div>
                <div class="value">{{ $ventes->sum(function($vente) { return $vente->articles->sum('pivot.quantite'); }) }}</div>
                <div class="label">Articles vendus</div>
            </div>
        </div>

        <!-- Tableau détaillé -->
        <div class="table-container">
            <h3 class="table-title">📋 Détail des Ventes</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 8%;">ID Vente</th>
                        <th style="width: 20%;">Client</th>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 10%;">Statut</th>
                        <th style="width: 35%;">Articles vendus</th>
                        <th style="width: 15%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventes as $vente)
                    <tr>
                        <td class="text-center font-weight-bold">
                            #{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>{{ $vente->client->prenoms }}</td>
                        <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            @php
                                $statutClass = '';
                                switch($vente->statut) {
                                    case 'Payée':
                                        $statutClass = 'badge-success';
                                        break;
                                    case 'En attente':
                                        $statutClass = 'badge-warning';
                                        break;
                                    case 'Annulée':
                                        $statutClass = 'badge-danger';
                                        break;
                                    default:
                                        $statutClass = 'badge-light';
                                }
                            @endphp
                            <span class="badge {{ $statutClass }}">{{ $vente->statut }}</span>
                        </td>
                        <td class="articles-list">
                            @foreach($vente->articles as $article)
                                <span class="article-item">
                                    {{ $article->nom }} ({{ $article->pivot->quantite }})
                                </span>
                            @endforeach
                        </td>
                        <td class="text-right font-weight-bold">
                            {{ number_format($vente->total, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" class="text-right font-weight-bold">
                            💰 TOTAL GÉNÉRAL :
                        </td>
                        <td class="text-right font-weight-bold">
                            {{ number_format($ventes->sum('total'), 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Analyse par statut -->
        <div class="table-container">
            <h3 class="table-title">📈 Répartition par Statut</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Statut</th>
                        <th class="text-center">Nombre de ventes</th>
                        <th class="text-right">Montant total</th>
                        <th class="text-right">Pourcentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statutStats = $ventes->groupBy('statut')->map(function($group) {
                            return [
                                'count' => $group->count(),
                                'total' => $group->sum('total'),
                                'percentage' => round(($group->sum('total') / $ventes->sum('total')) * 100, 1)
                            ];
                        });
                    @endphp
                    @foreach($statutStats as $statut => $stats)
                    <tr>
                        <td>
                            @php
                                $statutClass = '';
                                switch($statut) {
                                    case 'Payée':
                                        $statutClass = 'badge-success';
                                        break;
                                    case 'En attente':
                                        $statutClass = 'badge-warning';
                                        break;
                                    case 'Annulée':
                                        $statutClass = 'badge-danger';
                                        break;
                                    default:
                                        $statutClass = 'badge-light';
                                }
                            @endphp
                            <span class="badge {{ $statutClass }}">{{ $statut }}</span>
                        </td>
                        <td class="text-center">{{ $stats['count'] }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</td>
                        <td class="text-right">{{ $stats['percentage'] }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Top articles vendus -->
        @php
            $topArticles = collect();
            foreach($ventes as $vente) {
                foreach($vente->articles as $article) {
                    $key = $article->id;
                    if($topArticles->has($key)) {
                        $topArticles[$key]['quantite'] += $article->pivot->quantite;
                        $topArticles[$key]['total'] += $article->pivot->sous_total;
                    } else {
                        $topArticles[$key] = [
                            'nom' => $article->nom,
                            'quantite' => $article->pivot->quantite,
                            'total' => $article->pivot->sous_total
                        ];
                    }
                }
            }
            $topArticles = $topArticles->sortByDesc('quantite')->take(10);
        @endphp

        @if($topArticles->count() > 0)
        <div class="table-container">
            <h3 class="table-title">🏆 Top 10 des Articles les Plus Vendus</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 5%;">Rang</th>
                        <th style="width: 45%;">Article</th>
                        <th style="width: 20%;" class="text-center">Quantité vendue</th>
                        <th style="width: 30%;" class="text-right">Chiffre d'affaires</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topArticles as $index => $article)
                    <tr>
                        <td class="text-center font-weight-bold">
                            @if($index == 0)
                                🥇
                            @elseif($index == 1)
                                🥈
                            @elseif($index == 2)
                                🥉
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>{{ $article['nom'] }}</td>
                        <td class="text-center font-weight-bold">{{ $article['quantite'] }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($article['total'], 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    @endif

    <!-- Pied de page -->
    <div class="footer">
        <p>
            📄 Rapport généré automatiquement le {{ date('d/m/Y à H:i') }} | 
            🏢 Système de Gestion des Ventes | 
            📧 Pour toute question, contactez l'administrateur
        </p>
        <p style="margin-top: 5px; font-size: 9px;">
            Ce document contient {{ $ventes->count() }} vente(s) pour un montant total de {{ number_format($ventes->sum('total'), 0, ',', ' ') }} FCFA
        </p>
    </div>
</body>
</html>