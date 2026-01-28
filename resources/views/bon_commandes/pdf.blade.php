<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Commande - {{ $bonCommande->reference }}</title>
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
            border-bottom: 2px solid #007bff;
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
            color: #007bff;
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
        .status-confirmed {
            background-color: #D4EDDA;
            color: #155724;
        }
        .status-cancelled {
            background-color: #F8D7DA;
            color: #721C24;
        }
        .status-delivered {
            background-color: #CCE5FF;
            color: #004085;
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
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
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
    </style>
</head>
<body>
    <div class="header">
        <div>
            @if($bonCommande->user && $bonCommande->user->bus && $bonCommande->user->bus->logo)
                <img src="{{ public_path('storage/' . $bonCommande->user->bus->logo) }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="company-info">
            <div class="title">BON DE COMMANDE</div>
            <div class="subtitle">{{ $bonCommande->user && $bonCommande->user->bus ? $bonCommande->user->bus->nom : 'Entreprise' }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h3>Informations de la commande</h3>
            <div class="info-row">
                <div class="info-label">Référence:</div>
                <div class="info-value">{{ $bonCommande->reference }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de commande:</div>
                <div class="info-value">{{ $bonCommande->date_commande->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date de livraison:</div>
                <div class="info-value">{{ $bonCommande->date_livraison_prevue ? $bonCommande->date_livraison_prevue->format('d/m/Y') : 'Non spécifiée' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut:</div>
                <div class="info-value">
                    @if($bonCommande->statut == 'en attente')
                        <span class="status status-pending">En attente</span>
                    @elseif($bonCommande->statut == 'confirmée')
                        <span class="status status-confirmed">Confirmée</span>
                    @elseif($bonCommande->statut == 'annulée')
                        <span class="status status-cancelled">Annulée</span>
                    @elseif($bonCommande->statut == 'livrée')
                        <span class="status status-delivered">Livrée</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>Fournisseur</h3>
            <div class="info-row">
                <div class="info-label">Nom:</div>
                <div class="info-value">{{ $bonCommande->fournisseur ? $bonCommande->fournisseur->nom_raison_sociale : 'N/A' }}</div>
            </div>
            @if($bonCommande->fournisseur && $bonCommande->fournisseur->adresse)
            <div class="info-row">
                <div class="info-label">Adresse:</div>
                <div class="info-value">{{ $bonCommande->fournisseur->adresse }}</div>
            </div>
            @endif
            @if($bonCommande->fournisseur && $bonCommande->fournisseur->telephone)
            <div class="info-row">
                <div class="info-label">Téléphone:</div>
                <div class="info-value">{{ $bonCommande->fournisseur->telephone }}</div>
            </div>
            @endif
        </div>
    </div>

    @if($bonCommande->demandeApprovisionnement || $bonCommande->demandeAchat)
    <div class="info-section">
        <h3>Demandes liées</h3>
        @if($bonCommande->demandeApprovisionnement)
        <div class="info-row">
            <div class="info-label">Demande d'approvisionnement:</div>
            <div class="info-value">{{ $bonCommande->demandeApprovisionnement->reference }}</div>
        </div>
        @endif
        @if($bonCommande->demandeAchat)
        <div class="info-row">
            <div class="info-label">Demande d'achat:</div>
            <div class="info-value">{{ $bonCommande->demandeAchat->reference }}</div>
        </div>
        @endif
    </div>
    @endif

    <h3>Articles commandés</h3>
    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th class="text-center">Quantité</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-center">% Remise</th>
                <th class="text-right">Montant HT</th>
                <th class="text-center">Qté livrée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bonCommande->lignes as $ligne)
            <tr>
                <td>{{ $ligne->article ? $ligne->article->nom : 'Article supprimé' }}</td>
                <td class="text-center">{{ $ligne->quantite }}</td>
                <td class="text-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} FCFA</td>
                <td class="text-center">{{ $ligne->remise ?? 0 }}%</td>
                <td class="text-right">
                    @php
                        $montantBrut = $ligne->quantite * $ligne->prix_unitaire;
                        $montantRemise = $montantBrut * (($ligne->remise ?? 0) / 100);
                        $montantFinal = $montantBrut - $montantRemise;
                    @endphp
                    @if($ligne->remise > 0)
                        <span style="text-decoration: line-through; color: #999;">{{ number_format($montantBrut, 2, ',', ' ') }} FCFA</span><br>
                        {{ number_format($montantFinal, 2, ',', ' ') }} FCFA
                    @else
                        {{ number_format($montantFinal, 2, ',', ' ') }} FCFA
                    @endif
                </td>
                <td class="text-center">{{ $ligne->quantite_livree }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">Total général</td>
                <td class="text-right">{{ number_format($bonCommande->montant_total, 2, ',', ' ') }} FCFA</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    @if($bonCommande->conditions_paiement)
    <div class="info-section">
        <h3>Conditions de paiement</h3>
        <p>{{ $bonCommande->conditions_paiement }}</p>
    </div>
    @endif

    @if($bonCommande->notes)
    <div class="info-section">
        <h3>Notes</h3>
        <p>{{ $bonCommande->notes }}</p>
    </div>
    @endif

    <div class="validation-section">
        <h3>Informations de validation</h3>
        <div class="info-row">
            <div class="info-label">Créé par:</div>
            <div class="info-value">{{ $bonCommande->user ? $bonCommande->user->nom_complet : 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Date de création:</div>
            <div class="info-value">{{ $bonCommande->created_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($bonCommande->statut != 'en attente')
        <div class="info-row">
            <div class="info-label">Dernière modification:</div>
            <div class="info-value">{{ $bonCommande->updated_at->format('d/m/Y H:i') }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Document généré le {{ date('d/m/Y H:i') }}</p>
        @if($bonCommande->user && $bonCommande->user->bus)
        <p>{{ $bonCommande->user->bus->nom }}</p>
        @endif
    </div>
</body>
</html>