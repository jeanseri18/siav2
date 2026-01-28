<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $vente->id }} - {{ $vente->client->prenoms ?? $vente->nom_client }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #000;
        }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-title { font-size: 28px; font-weight: bold; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .info-table td { padding: 5px 0; }
        .left-col { width: 50%; vertical-align: top; }
        .right-col { width: 50%; text-align: right; vertical-align: top; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items th, table.items td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        table.items th {
            background-color: #f0f0f0;
        }
        .total-table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }
        .total-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: right;
        }
        .total-line {
            font-weight: bold;
            background-color: #e0e0e0;
        }
        .amount-words {
            margin-top: 30px;
            font-style: italic;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        .red { color: red; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="invoice-title">FACTURE</h1>
    </div>

    <table class="info-table">
        <tr>
            <td class="left-col">
                <strong>FACTURER À :</strong><br>
                {{ $vente->client->prenoms ?? $vente->nom_client }}<br>
                @if($vente->client)
                    @if($vente->client->adresse)
                        {{ $vente->client->adresse }}<br>
                    @endif
                    @if($vente->client->ville)
                        {{ $vente->client->ville }}<br>
                    @endif
                    @if($vente->client->bp)
                        {{ $vente->client->bp }}<br>
                    @endif
                    @if($vente->client->telephone)
                        {{ $vente->client->telephone }}<br>
                    @endif
                @endif
            </td>
            <td class="right-col">
                <strong>N° de la facture :</strong> {{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}<br>
                <strong>Date de facturation :</strong> {{ $vente->created_at->format('d/m/Y') }}<br>
                <strong>Statut :</strong> {{ $vente->statut }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Désignation</th>
                <th>Qté</th>
                <th>PU HT</th>
                <th>Total HT</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalHT = 0;
                $index = 1;
            @endphp
            @foreach($vente->articles as $article)
                @php
                    $sousTotal = $article->pivot->prix_unitaire * $article->pivot->quantite;
                    $totalHT += $sousTotal;
                @endphp
                <tr>
                    <td>{{ str_pad($index++, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $article->nom }}</td>
                    <td>{{ $article->pivot->quantite }}</td>
                    <td>{{ number_format($article->pivot->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
            
            @foreach($vente->prestations as $prestation)
                @php
                    $sousTotal = $prestation->prix_unitaire * $prestation->quantite;
                    $totalHT += $sousTotal;
                @endphp
                <tr>
                    <td>{{ str_pad($index++, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $prestation->nom_prestation }}</td>
                    <td>{{ $prestation->quantite }}</td>
                    <td>{{ number_format($prestation->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-table">
        <tr>
            <td>Total HT</td>
            <td>{{ number_format($totalHT, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>TVA (18%)</td>
            <td>{{ number_format($vente->tva ?? 0, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="total-line">
            <td><strong>TOTAL TTC</strong></td>
            <td><strong>{{ number_format($vente->total_ttc ?? $vente->total, 0, ',', ' ') }} FCFA</strong></td>
        </tr>
    </table>

    <div class="amount-words">
        <strong>Arrêter la présente facture à la somme de :</strong><br>
        {{ ucfirst($vente->total_en_lettres ?? 'Montant non spécifié') }}
    </div>

    <div class="signature">
        Le Gérant<br><br><br>
        _________________________
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>