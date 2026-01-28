<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture INV007 - SCI IVOIRE II</title>
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
                SCI IVOIRE II<br>
                N° Compte client : CLU00010<br>
                N° CC : 1509999 H<br>
                Téléphone : 27 22 44 09 25<br>
                Adresse : Abidjan, Cocody Lycée technique<br>
                Boîte postale : 01 BP 4387 01
            </td>
            <td class="right-col">
                <strong>N° de la facture :</strong> INV007<br>
                <strong>Date de facturation :</strong> 15/08/2023<br>
                <strong>Délai de règlement :</strong> 30 jours<br>
                <strong>Échéance :</strong> 20/09/2023
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
                <td>TVX réalisés suivant avancement - Décompte N°1</td>
                <td>45,38%</td>
                <td>1</td>
                <td>3 786 450</td>
                <td>0</td>
                <td>3 786 450</td>
            </tr>
            <tr>
                <td>002</td>
                <td>Estimation travaux supplémentaires</td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr style="color: red;">
                <td colspan="7" style="text-align: left; padding-left: 10px;">
                    <strong>À Déduire :</strong>
                </td>
            </tr>
            <tr style="color: red;">
                <td>003</td>
                <td>Retenues de Garantie 7%</td>
                <td></td>
                <td>1</td>
                <td>265 052</td>
                <td>0</td>
                <td>265 052</td>
            </tr>
            <tr style="color: red;">
                <td>004</td>
                <td>Pénalités Partielle de Retard</td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr style="color: red;">
                <td>005</td>
                <td>Amortissement Avance de démarrage (16,66%)</td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr style="color: red;">
                <td>006</td>
                <td>Remboursement acompte perçu</td>
                <td></td>
                <td>1</td>
                <td>275 000</td>
                <td>0</td>
                <td>275 000</td>
            </tr>
        </tbody>
    </table>

    <table class="total-table">
        <tr>
            <td>Montant Total HT</td>
            <td>3 246 399</td>
        </tr>
        <tr>
            <td>TVA (18%)</td>
            <td>584 352</td>
        </tr>
        <tr class="total-line">
            <td><strong>TOTAL TTC</strong></td>
            <td><strong>3 830 750</strong></td>
        </tr>
    </table>

    <div class="amount-words">
        <strong>Arrêter la présente facture à la somme de :</strong><br>
        Trois Millions Huit Cent Trente mille Sept Cent cinquante Francs CFA TTC
    </div>

    <div class="signature">
        Le Gérant<br><br><br>
        _________________________
    </div>

</body>
</html>