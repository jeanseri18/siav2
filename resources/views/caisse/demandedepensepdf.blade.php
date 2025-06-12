<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Dépense #{{ $demande->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
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
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($bus && $bus->logo)
            <img src="{{ public_path('storage/' . $bus->logo) }}" alt="Logo" class="logo">
        @endif
        <div class="title">DEMANDE DE DÉPENSE</div>
        <div class="subtitle">{{ $bus->nom }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Numéro:</div>
            <div class="info-value">{{ $demande->id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de création:</div>
            <div class="info-value">{{ $demande->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Statut:</div>
            <div class="info-value">
                @if($demande->statut == 'en attente')
                    <span class="status status-pending">En attente</span>
                @elseif($demande->statut == 'validée')
                    <span class="status status-approved">Validée</span>
                @elseif($demande->statut == 'annulée')
                    <span class="status status-rejected">Annulée</span>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Montant total:</div>
            <div class="info-value">{{ number_format($demande->montant, 2, ',', ' ') }}</div>
        </div>
    </div>

    @php
        // Extraire les informations d'objet, mois et bénéficiaires du motif
        $objet = '';
        $mois = '';
        $beneficiaires = '';
        
        if (preg_match('/Objet:\s*(.*?)\s*-\s*Mois:\s*(.*?)\s*-\s*Bénéficiaires:\s*(.*?)(?=\s*\[|$)/s', $demande->motif, $matches)) {
            $objet = trim($matches[1]);
            $mois = trim($matches[2]);
            $beneficiaires = trim($matches[3]);
        }
    @endphp

    <div class="info-section">
        @if(!empty($objet))
        <div class="info-row">
            <div class="info-label">Objet:</div>
            <div class="info-value">{{ $objet }}</div>
        </div>
        @endif
        
        @if(!empty($mois))
        <div class="info-row">
            <div class="info-label">Mois:</div>
            <div class="info-value">{{ $mois }}</div>
        </div>
        @endif
        
        @if(!empty($beneficiaires))
        <div class="info-row">
            <div class="info-label">Bénéficiaires:</div>
            <div class="info-value">{{ $beneficiaires }}</div>
        </div>
        @endif
        
        @if(empty($objet) && empty($mois) && empty($beneficiaires))
        <div class="info-row">
            <div class="info-label">Motif:</div>
            <div class="info-value">{{ $demande->motif }}</div>
        </div>
        @endif
    </div>

    @php
        // Essayer de décoder les détails des lignes s'ils sont stockés en JSON
        $detailsLignes = [];
        try {
            // Rechercher un pattern JSON dans le motif
            if (preg_match('/\[\{.*?\}\]/s', $demande->motif, $matches)) {
                $jsonPart = $matches[0];
                $detailsLignes = json_decode($jsonPart, true) ?: [];
            }
            
            // Si aucun JSON valide n'est trouvé, essayer de parser le format spécifique
            if (empty($detailsLignes) && preg_match('/\[\{"designation":"(.*?)","quantite":"(.*?)","prix_unitaire":"(.*?)","total":(.*?)\}\]/s', $demande->motif, $matches)) {
                $detailsLignes = [
                    [
                        'designation' => $matches[1],
                        'quantite' => $matches[2],
                        'prix_unitaire' => $matches[3],
                        'total' => $matches[4]
                    ]
                ];
            }
        } catch (\Exception $e) {
            // En cas d'erreur, laisser le tableau vide
        }
    @endphp

    @if(count($detailsLignes) > 0)
    <h3>Détails des lignes</h3>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailsLignes as $ligne)
            <tr>
                <td>{{ $ligne['designation'] }}</td>
                <td>{{ $ligne['quantite'] }}</td>
                <td>{{ number_format($ligne['prix_unitaire'], 2, ',', ' ') }}</td>
                <td>{{ number_format($ligne['total'], 2, ',', ' ') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Total</td>
                <td>{{ number_format($demande->montant, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    <div class="info-section">
        <h3>Informations de validation</h3>
        <div class="info-row">
            <div class="info-label">Créé par:</div>
            <div class="info-value">{{ $demande->user ? $demande->user->name : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de création:</div>
            <div class="info-value">{{ $demande->created_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($demande->statut != 'en attente')
        <div class="info-row">
            <div class="info-label">Date de {{ $demande->statut == 'validée' ? 'validation' : 'annulation' }}:</div>
            <div class="info-value">{{ $demande->updated_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Document généré le {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>