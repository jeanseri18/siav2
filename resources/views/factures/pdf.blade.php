<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture->num }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #033765;
            margin-bottom: 10px;
            text-align: center;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 5px 0;
        }
        .client-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .clear {
            clear: both;
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
        .totals {
            float: right;
            width: 40%;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 5px;
        }
        .totals td:last-child {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status {
            font-weight: bold;
            padding: 5px 10px;
            display: inline-block;
            border-radius: 4px;
        }
        .status-pending {
            background-color: #FFF3CD;
            color: #856404;
        }
        .status-paid {
            background-color: #D4EDDA;
            color: #155724;
        }
        .status-canceled {
            background-color: #F8D7DA;
            color: #721C24;
        }
        .total-highlight {
            font-size: 16px;
            font-weight: bold;
            background-color: #033765;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo.png') }}" alt="Logo">
        <div class="company-info">
            <p><strong>VOTRE ENTREPRISE</strong><br>
            Adresse complète de l'entreprise<br>
            Tel: +XX XX XX XX XX | Email: contact@entreprise.com<br>
            RCCM: XXXXXXXXXXXX | IFU: XXXXXXXXXXXX</p>
        </div>
    </div>

    <div class="invoice-title">FACTURE N° {{ $facture->num }}</div>

    <div class="invoice-details">
        <div class="client-info">
            <h3>Client</h3>
            @if($facture->contrat && $facture->contrat->client)
                <p><strong>{{ $facture->contrat->client->nom_raison_sociale }}</strong><br>
                {{ $facture->contrat->client->adresse }}<br>
                Tel: {{ $facture->contrat->client->telephone }}<br>
                Email: {{ $facture->contrat->client->email }}</p>
@elseif($facture->artisan)
                <p><strong>{{ $facture->artisan->nom }}</strong><br>
                {{ $facture->artisan->adresse }}<br>
                Tel: {{ $facture->artisan->telephone }}</p>
            @else
                <p>Client non spécifié</p>
            @endif
        </div>
        
        <div class="invoice-info">
            <table>
                <tr>
                    <td><strong>Date d'émission:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($facture->date_emission)->format('d/m/Y') }}</td>
                </tr>
                @if($facture->date_reglement && $facture->statut == 'payée')
                <tr>
                    <td><strong>Date de règlement:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($facture->date_reglement)->format('d/m/Y') }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Statut:</strong></td>
                    <td>
                        @if($facture->statut == 'en attente')
                            <span class="status status-pending">En attente</span>
                        @elseif($facture->statut == 'payée')
                            <span class="status status-paid">Payée</span>
                        @elseif($facture->statut == 'annulée')
                            <span class="status status-canceled">Annulée</span>
                        @endif
                    </td>
                </tr>
                @if($facture->contrat)
                <tr>
                    <td><strong>Contrat:</strong></td>
                    <td>{{ $facture->contrat->ref_contrat }}</td>
                </tr>
                @endif
                @if($facture->num_decompte)
                <tr>
                    <td><strong>Décompte N°:</strong></td>
                    <td>{{ $facture->num_decompte }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <div class="clear"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Montant HT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($facture->prestation)
                        {{ $facture->prestation->description }}
                    @elseif($facture->contrat)
                        Services relatifs au contrat {{ $facture->contrat->nom_contrat }}
                        @if($facture->taux_avancement)
                            (Avancement: {{ $facture->taux_avancement }}%)
                        @endif
                    @else
                        Services divers
                    @endif
                </td>
                <td>1</td>
                <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }} CFA</td>
                <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }} CFA</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Montant HT</strong></td>
                <td>{{ number_format($facture->montant_ht, 0, ',', ' ') }} CFA</td>
            </tr>
            <tr>
                <td><strong>TVA (18%)</strong></td>
                <td>{{ number_format($facture->montant_total - $facture->montant_ht, 0, ',', ' ') }} CFA</td>
            </tr>
            <tr class="total-highlight">
                <td><strong>TOTAL TTC</strong></td>
                <td>{{ number_format($facture->montant_total, 0, ',', ' ') }} CFA</td>
            </tr>
            @if($facture->montant_reglement > 0)
            <tr>
                <td><strong>Montant réglé</strong></td>
                <td>{{ number_format($facture->montant_reglement, 0, ',', ' ') }} CFA</td>
            </tr>
            <tr>
                <td><strong>Reste à régler</strong></td>
                <td>{{ number_format($facture->montant_total - $facture->montant_reglement, 0, ',', ' ') }} CFA</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="clear"></div>

    <div class="footer">
        <p>Paiement par virement bancaire :<br>
        Banque: XXXXX | N° de compte: XXXXXXXXXX | IBAN: XXXXXXXXXXXX | BIC: XXXXXXX</p>
        <p>En cas de retard de paiement, une pénalité de X% sera appliquée.<br>
        Pour toute question concernant cette facture, veuillez contacter notre service comptabilité.</p>
        <p>Facture générée le {{ date('d/m/Y') }}</p>
    </div>
</body>
</html>