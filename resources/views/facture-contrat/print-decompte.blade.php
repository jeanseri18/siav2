@php
function numberToWords($number) {
    $ones = ['', 'Un', 'Deux', 'Trois', 'Quatre', 'Cinq', 'Six', 'Sept', 'Huit', 'Neuf'];
    $tens = ['', 'Dix', 'Vingt', 'Trente', 'Quarante', 'Cinquante', 'Soixante', 'Soixante-dix', 'Quatre-vingt', 'Quatre-vingt-dix'];
    $hundreds = ['', 'Cent', 'Deux cents', 'Trois cents', 'Quatre cents', 'Cinq cents', 'Six cents', 'Sept cents', 'Huit cents', 'Neuf cents'];
    
    if ($number == 0) return 'Zéro';
    
    $words = '';
    $number = intval($number);
    
    if ($number >= 1000000) {
        $millions = intval($number / 1000000);
        $words .= numberToWords($millions) . ' Million';
        if ($millions > 1) $words .= 's';
        $number %= 1000000;
        if ($number > 0) $words .= ' ';
    }
    
    if ($number >= 1000) {
        $thousands = intval($number / 1000);
        if ($thousands == 1) {
            $words .= 'Mille';
        } else {
            $words .= numberToWords($thousands) . ' Mille';
        }
        $number %= 1000;
        if ($number > 0) $words .= ' ';
    }
    
    if ($number >= 100) {
        $hundred = intval($number / 100);
        if ($hundred == 1) {
            $words .= 'Cent';
        } else {
            $words .= $ones[$hundred] . ' Cent';
        }
        $number %= 100;
        if ($number > 0) $words .= ' ';
    }
    
    if ($number >= 20) {
        $ten = intval($number / 10);
        $one = $number % 10;
        if ($one == 1 && $ten == 7) {
            $words .= 'Soixante et Onze';
        } elseif ($one == 1 && $ten == 9) {
            $words .= 'Quatre-vingt-onze';
        } elseif ($one > 0) {
            $words .= $tens[$ten] . '-' . $ones[$one];
        } else {
            $words .= $tens[$ten];
        }
    } elseif ($number >= 10) {
        if ($number == 10) $words .= 'Dix';
        elseif ($number == 11) $words .= 'Onze';
        elseif ($number == 12) $words .= 'Douze';
        elseif ($number == 13) $words .= 'Treize';
        elseif ($number == 14) $words .= 'Quatorze';
        elseif ($number == 15) $words .= 'Quinze';
        elseif ($number == 16) $words .= 'Seize';
        else $words .= 'Dix-' . $ones[$number - 10];
    } elseif ($number > 0) {
        $words .= $ones[$number];
    }
    
    return $words;
}
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $factureDecompte->numero }} - {{ $factureDecompte->factureContrat->dqe->contrat->client->nom }}</title>
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
                {{ $factureDecompte->factureContrat->dqe->contrat->client->nom }}<br>
                N° Compte client : {{ $factureDecompte->factureContrat->dqe->contrat->client->code_client ?? 'CLU00010' }}<br>
                N° CC : {{ $factureDecompte->factureContrat->dqe->contrat->numero_contrat }}<br>
                Téléphone : {{ $factureDecompte->factureContrat->dqe->contrat->client->telephone ?? '27 22 44 09 25' }}<br>
                Adresse : {{ $factureDecompte->factureContrat->dqe->contrat->client->adresse ?? 'Abidjan, Cocody' }}<br>
                Boîte postale : {{ $factureDecompte->factureContrat->dqe->contrat->client->boite_postale ?? '01 BP 4387 01' }}
            </td>
            <td class="right-col">
                <strong>N° de la facture :</strong> {{ $factureDecompte->numero }}<br>
                <strong>Date de facturation :</strong> {{ $factureDecompte->date_facture->format('d/m/Y') }}<br>
                <strong>Délai de règlement :</strong> 30 jours<br>
                <strong>Échéance :</strong> {{ $factureDecompte->date_facture->addDays(30)->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Désignation</th>
                <th>TAUX</th>
                <th>Qté</th>
                <th>PU HT</th>
                <th>Remise</th>
                <th>Montant Total HT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>001</td>
                <td>TVX réalisés suivant avancement - Décompte {{ $factureDecompte->numero }}</td>
                <td>{{ number_format($factureDecompte->pourcentage_avancement, 2, ',', ' ') }}%</td>
                <td>1</td>
                <td>{{ number_format($factureDecompte->montant_ht, 2, ',', ' ') }}</td>
                <td>0</td>
                <td>{{ number_format($factureDecompte->montant_ht, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="total-table">
        <tr>
            <td>Montant Total HT</td>
            <td>{{ number_format($factureDecompte->montant_ht, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td>TVA (18%)</td>
            <td>{{ number_format($factureDecompte->montant_ht * 0.18, 2, ',', ' ') }}</td>
        </tr>
        <tr class="total-line">
            <td><strong>TOTAL TTC</strong></td>
            <td><strong>{{ number_format($factureDecompte->montant_ttc, 2, ',', ' ') }}</strong></td>
        </tr>
    </table>

    <div class="amount-words">
        <strong>Arrêter la présente facture à la somme de :</strong><br>
        {{ ucfirst($factureDecompte->montant_en_lettres ?? numberToWords($factureDecompte->montant_ttc)) }} Francs CFA TTC
    </div>

    <div class="signature">
        Le Gérant<br><br><br>
        _________________________
    </div>

</body>
</html>