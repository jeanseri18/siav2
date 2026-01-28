<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Cotation - {{ $demandeCotation->reference }}</title>
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
            border-bottom: 2px solid #28a745;
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
            color: #28a745;
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
        .status-ongoing {
            background-color: #CCE5FF;
            color: #004085;
        }
        .status-completed {
            background-color: #D4EDDA;
            color: #155724;
        }
        .status-cancelled {
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
        .fournisseur-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        .fournisseur-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        .fournisseur-item {
            background-color: white;
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            @if($demandeCotation->user && $demandeCotation->user->bus && $demandeCotation->user->bus->logo)
                <img src="{{ public_path('storage/' . $demandeCotation->user->bus->logo) }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="company-info">
            <div class="title">DEMANDE DE COTATION</div>
            <div class="subtitle">{{ $demandeCotation->user && $demandeCotation->user->bus ? $demandeCotation->user->bus->nom : 'Entreprise' }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>Informations de la demande</h3>
            <div class="info-row">
                <div class="info-label">Référence:</div>
                <div class="info-value">{{ $demandeCotation->reference }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de demande:</div>
                <div class="info-value">{{ $demandeCotation->date_demande->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date d'expiration:</div>
                <div class="info-value">{{ $demandeCotation->date_expiration->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut:</div>
                <div class="info-value">
                    @if($demandeCotation->statut == 'en cours')
                        <span class="status status-ongoing">En cours</span>
                    @elseif($demandeCotation->statut == 'terminée')
                        <span class="status status-completed">Terminée</span>
                    @elseif($demandeCotation->statut == 'annulée')
                        <span class="status status-cancelled">Annulée</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>Demande liée</h3>
            @if($demandeCotation->demandeAchat)
            <div class="info-row">
                <div class="info-label">Demande d'achat:</div>
                <div class="info-value">{{ $demandeCotation->demandeAchat->reference }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Projet:</div>
                <div class="info-value">{{ $demandeCotation->demandeAchat->projet ? $demandeCotation->demandeAchat->projet->nom_projet : 'N/A' }}</div>
            </div>
            @else
            <p>Aucune demande d'achat liée</p>
            @endif
        </div>
    </div>

    @if($demandeCotation->description)
    <div class="info-section">
        <h3>Description</h3>
        <p>{{ $demandeCotation->description }}</p>
    </div>
    @endif

    <div class="fournisseur-section">
        <h3>Fournisseurs consultés</h3>
        <div class="fournisseur-list">
            @foreach($demandeCotation->fournisseurs as $fournisseurDemande)
            <div class="fournisseur-item">
                <strong>{{ $fournisseurDemande->fournisseur->nom_raison_sociale }}</strong>
                @if($fournisseurDemande->fournisseur->telephone)
                <br>Tél: {{ $fournisseurDemande->fournisseur->telephone }}
                @endif
                @if($fournisseurDemande->fournisseur->email)
                <br>Email: {{ $fournisseurDemande->fournisseur->email }}
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <h3>Articles demandés</h3>
    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Article</th>
                <th class="text-center">Quantité</th>
                <th>Unité</th>
                <th>Spécifications</th>
            </tr>
        </thead>
        <tbody>
            @foreach($demandeCotation->lignes as $ligne)
            <tr>
                <td>{{ $ligne->designation }}</td>
                <td>{{ $ligne->article ? $ligne->article->nom : 'N/A' }}</td>
                <td class="text-center">{{ $ligne->quantite }}</td>
                <td>{{ $ligne->unite_mesure }}</td>
                <td>{{ $ligne->specifications ?: 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($demandeCotation->conditions_generales)
    <div class="info-section">
        <h3>Conditions générales</h3>
        <p>{{ $demandeCotation->conditions_generales }}</p>
    </div>
    @endif

    <div class="validation-section">
        <h3>Informations de validation</h3>
        <div class="info-row">
            <div class="info-label">Créé par:</div>
            <div class="info-value">{{ $demandeCotation->user ? $demandeCotation->user->nom : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de création:</div>
            <div class="info-value">{{ $demandeCotation->created_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($demandeCotation->statut != 'en cours')
        <div class="info-row">
            <div class="info-label">Dernière modification:</div>
            <div class="info-value">{{ $demandeCotation->updated_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Document généré le {{ date('d/m/Y H:i') }}</p>
        @if($demandeCotation->user && $demandeCotation->user->bus)
        <p>{{ $demandeCotation->user->bus->nom }}</p>
        @endif
    </div>
</body>
</html>