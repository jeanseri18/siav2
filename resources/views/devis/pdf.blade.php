<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis {{ $devi->ref_devis ?? '#' . $devi->id }} - SIA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            font-size: 12px;
            color: #000;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .logo {
            width: 110px;
        }
        .title {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
        }
        .date {
            text-align: right;
            font-size: 14px;
        }
        .info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .info-left, .info-right {
            width: 48%;
        }
    
        .info-left strong, .info-right strong {
            font-size: 16px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            width: 100%;
        }
        .info-section .info-left {
            width: 40%;
        }
        .info-section .info-right {
            width: 100%;
            text-align: right;
        }
        .rccm-section {
            text-align: right;
            margin-bottom: 20px;
            font-size: 13px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items th {
            background-color: #e0e0e0;
            padding: 10px;
            text-align: left;
            border: 1px solid #000;
        }
        table.items td {
            padding: 12px 10px;
            border: 1px solid #000;
        }
        .total-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 14px;
        }
        .total-table td {
            padding: 8px 15px;
            border: 1px solid #000;
        }
        .total-line {
            font-weight: bold;
            background-color: #f0f0f0;
            font-size: 16px;
        }
        .text-right { text-align: right; }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>
<body>

    <div class="header">
        <div>
            @if(isset($configGlobal) && $configGlobal->logo)
                <img src="{{ asset('storage/' . $configGlobal->logo) }}" alt="Logo {{ $configGlobal->nom_entreprise ?? 'SIA' }}" class="logo">
            @else
                <img src="https://via.placeholder.com/140x140/003087/ffffff?text=SIA" alt="Logo SIA" class="logo">
            @endif
            <br><strong>{{ $configGlobal->nom_entreprise ?? 'SOCIÉTÉ D\'INGÉNIERIE EN AFRIQUE' }}</strong>
        </div>
        <div>
            <div class="title">Devis - {{ $devi->ref_devis ?? '#' . $devi->id }}</div>
            <div class="date">
                Date : {{ $devi->created_at->format('d.m.Y') }}<br>
                Date de validité : {{ $devi->created_at->addDays(30)->format('d.m.Y') }}
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-left">
            <strong>{{ $configGlobal->nom_entreprise ?? 'SOCIÉTÉ D\'INGÉNIERIE EN AFRIQUE' }}</strong><br>
            {{ $configGlobal->localisation ?? 'Bingerville, cité colombe 1 Ilot 20, lot 134' }}<br>
            {{ $configGlobal->adresse_postale ?? '18 BP 737 ABIDJAN 18 ABIDJAN' }}<br>
            @if($configGlobal && $configGlobal->tel1)
                {{ $configGlobal->tel1 }}<br>
            @endif
            @if($configGlobal && $configGlobal->email)
                {{ $configGlobal->email }}<br>
            @endif
            @if($configGlobal && $configGlobal->rccm)
                RCCM: {{ $configGlobal->rccm }}<br>
            @endif
            @if($configGlobal && $configGlobal->cc)
                N° CC : {{ $configGlobal->cc }}<br>
            @endif
        </div>
        <div class="info-right">
            <strong>{{ $devi->client->prenoms ?? $devi->client->nom_raison_sociale }}</strong><br>
            @if($devi->client->adresse)
                {{ $devi->client->adresse }}<br>
            @endif
            @if($devi->client->ville)
                {{ $devi->client->ville }}<br>
            @endif
            @if($devi->client->bp)
                {{ $devi->client->bp }}<br>
            @endif
      
        </div>
    </div>
    @if($configGlobal && ($configGlobal->rccm || $configGlobal->cc))
        <div class="rccm-section">
            @if($configGlobal && $configGlobal->rccm)
                RCCM: {{ $configGlobal->rccm }}<br>
            @endif
            @if($configGlobal && $configGlobal->cc)
                N° CC : {{ $configGlobal->cc }}
            @endif
        </div>
    @endif

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th>Date</th>
                <th>Qté</th>
                <th>Unité</th>
                <th class="text-right">Prix unitaire</th>
                <th class="text-right">TVA</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalHT = 0;
                $totalTVA = 0;
                $totalTTC = 0;
            @endphp
            @foreach($devi->articles as $article)
                @php
                    $montantHT = $article->prix_unitaire * $article->quantite;
                    $montantTVA = $montantHT * ($article->taux_tva / 100);
                    $montantTTC = $montantHT + $montantTVA;
                    $totalHT += $montantHT;
                    $totalTVA += $montantTVA;
                    $totalTTC += $montantTTC;
                @endphp
                <tr>
                    <td>{{ $article->article->nom ?? $article->description }}</td>
                    <td>{{ $article->created_at->format('d.m.Y') }}</td>
                    <td>{{ number_format($article->quantite, 2, ',', ' ') }}</td>
                    <td>{{ $article->unite ?? 'pcs' }}</td>
                    <td class="text-right">{{ number_format($article->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($article->taux_tva, 2, ',', ' ') }} %</td>
                    <td class="text-right">{{ number_format($montantTTC, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-table">
        <tr>
            <td>Total HT TVA {{ $devi->articles->first()->taux_tva ?? 18 }}%</td>
            <td class="text-right">{{ number_format($totalHT, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>TVA {{ $devi->articles->first()->taux_tva ?? 18 }}%</td>
            <td class="text-right">{{ number_format($totalTVA, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="total-line">
            <td><strong>Total TTC</strong></td>
            <td class="text-right"><strong>{{ number_format($totalTTC, 0, ',', ' ') }} FCFA</strong></td>
        </tr>
    </table>

    <div class="footer">
        <strong>Conditions générales :</strong> Devis valable 30 jours à compter de la date d'émission.<br>
        Paiement à réception de facture. Toute commande implique l'acceptation de nos conditions générales de vente.
    </div>

</body>
</html>