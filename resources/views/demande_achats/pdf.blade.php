<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'Achat - {{ $demandeAchat->reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 120px;
            max-height: 80px;
        }
        .company-info {
            text-align: right;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .info-value {
            flex: 1;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        .status-pending {
            background-color: #FFF3CD;
            color: #856404;
        }
        .status-approved {
            background-color: #D4EDDA;
            color: #155724;
        }
        .status-rejected {
            background-color: #F8D7DA;
            color: #721C24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .validation-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-final {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #dc3545;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            @if($demandeAchat->user && $demandeAchat->user->bus && $demandeAchat->user->bus->logo)
                <img src="{{ public_path('storage/' . $demandeAchat->user->bus->logo) }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="company-info">
            <div class="title">DEMANDE D'ACHAT</div>
            <div class="subtitle">{{ $demandeAchat->user && $demandeAchat->user->bus ? $demandeAchat->user->bus->nom : 'Entreprise' }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>Informations de la demande</h3>
            <div class="info-row">
                <div class="info-label">Référence:</div>
                <div class="info-value">{{ $demandeAchat->reference }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de demande:</div>
                <div class="info-value">{{ $demandeAchat->date_demande->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date requise:</div>
                <div class="info-value">{{ $demandeAchat->date_requise->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut:</div>
                <div class="info-value">
                    @if($demandeAchat->statut == 'en attente')
                        <span class="status status-pending">En attente</span>
                    @elseif($demandeAchat->statut == 'approuvée')
                        <span class="status status-approved">Approuvée</span>
                    @elseif($demandeAchat->statut == 'rejetée')
                        <span class="status status-rejected">Rejetée</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Priorité:</div>
                <div class="info-value">{{ ucfirst($demandeAchat->priorite) }}</div>
            </div>
        </div>

        <div class="info-section">
            <h3>Informations du projet</h3>
            @if($demandeAchat->projet)
            <div class="info-row">
                <div class="info-label">Projet:</div>
                <div class="info-value">{{ $demandeAchat->projet->nom_projet }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Code projet:</div>
                <div class="info-value">{{ $demandeAchat->projet->code_projet }}</div>
            </div>
            @else
            <p>Aucun projet associé</p>
            @endif
        </div>
    </div>

    @if($demandeAchat->description)
    <div class="info-section">
        <h3>Description</h3>
        <p>{{ $demandeAchat->description }}</p>
    </div>
    @endif

    @if($demandeAchat->justification)
    <div class="info-section">
        <h3>Justification</h3>
        <p>{{ $demandeAchat->justification }}</p>
    </div>
    @endif

    <h3>Articles demandés</h3>
    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th>Description</th>
                <th class="text-center">Quantité</th>
                <th>Unité</th>
                <th class="text-right">Prix estimé</th>
                <th class="text-right">Total estimé</th>
                <th>Spécifications</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGeneral = 0; @endphp
            @foreach($demandeAchat->lignes as $ligne)
            @php 
                $total = $ligne->quantite * $ligne->prix_estime;
                $totalGeneral += $total;
            @endphp
            <tr>
                <td>{{ $ligne->article ? $ligne->article->nom : $ligne->designation }}</td>
                <td>{{ $ligne->description ?: 'N/A' }}</td>
                <td class="text-center">{{ $ligne->quantite }}</td>
                <td>{{ $ligne->unite_mesure }}</td>
                <td class="text-right">{{ number_format($ligne->prix_estime, 0, ',', ' ') }} FCFA</td>
                <td class="text-right">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                <td>{{ $ligne->specifications ?: 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row total-final">
            <span>TOTAL ESTIMÉ:</span>
            <span>{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    @if($demandeAchat->commentaire)
    <div class="info-section">
        <h3>Commentaires</h3>
        <p>{{ $demandeAchat->commentaire }}</p>
    </div>
    @endif

    <div class="validation-section">
        <h3>Informations de validation</h3>
        <div class="info-row">
            <div class="info-label">Demandé par:</div>
            <div class="info-value">{{ $demandeAchat->user ? $demandeAchat->user->name : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de création:</div>
            <div class="info-value">{{ $demandeAchat->created_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($demandeAchat->approved_by)
        <div class="info-row">
            <div class="info-label">Approuvé par:</div>
            <div class="info-value">{{ $demandeAchat->approbateur ? $demandeAchat->approbateur->name : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date d'approbation:</div>
            <div class="info-value">{{ $demandeAchat->approved_at ? $demandeAchat->approved_at->format('d/m/Y H:i') : 'N/A' }}</div>
        </div>
        @endif
        @if($demandeAchat->statut != 'en attente')
        <div class="info-row">
            <div class="info-label">Dernière modification:</div>
            <div class="info-value">{{ $demandeAchat->updated_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Document généré le {{ date('d/m/Y H:i') }}</p>
        @if($demandeAchat->user && $demandeAchat->user->bus)
        <p>{{ $demandeAchat->user->bus->nom }}</p>
        @endif
    </div>
</body>
</html>