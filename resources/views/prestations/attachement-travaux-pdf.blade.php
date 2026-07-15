<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Attachement des travaux N° {{ $attachement['numero_label'] }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #000;
            margin: 12px;
        }
        table { border-collapse: collapse; width: 100%; }
        .bordered td, .bordered th {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }
        .logo { display: inline-block; border: 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .green { color: #38761d; }
        .header-role {
            font-weight: bold;
            text-decoration: underline;
            font-size: 9px;
        }
        .prestataire-nom {
            color: #38761d;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }
        .prestataire-metier {
            color: #38761d;
            font-size: 10px;
            font-weight: bold;
        }
        .title-bar {
            background: #bdd7ee;
            font-weight: bold;
            font-size: 11px;
            padding: 5px 8px;
            border: 1px solid #000;
            margin: 8px 0 6px 0;
        }
        .title-bar-num {
            float: right;
            border: 1px solid #000;
            padding: 2px 8px;
            background: #fff;
        }
        .info-block {
            margin-bottom: 8px;
            line-height: 1.5;
            font-size: 9px;
        }
        .grey-head th { background: #e0e0e0; font-weight: bold; text-align: center; }
        .green-head th { background: #d9ead3; font-weight: bold; text-align: center; }
        .serie-head td {
            background: #d9e2f3;
            font-weight: bold;
            font-size: 8px;
        }
        .serie-total td {
            background: #d9e2f3;
            font-weight: bold;
        }
        .grand-total td {
            background: #f2f2f2;
            font-weight: bold;
        }
        .signature {
            margin-top: 16px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
@php
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $fmt = fn ($v, $d = 0) => number_format((float) $v, $d, ',', ' ');
    $fmtPct = fn ($v) => number_format((float) $v, 2, ',', ' ') . '%';
@endphp

{{-- En-tête maître d'oeuvre / prestataire --}}
<table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 6px;">
    <tr>
        <td width="50%" valign="top">
            @include('partials.pdf-logo', ['pdfBranding' => $pdfBranding ?? [], 'logoClass' => 'logo'])
            <span class="header-role">MAITRE D'OEUVRE</span>
        </td>
        <td width="50%" valign="top" align="right">
            <div class="prestataire-nom">{{ strtoupper($attachement['prestataire']) }}</div>
            <div class="prestataire-metier">{{ $attachement['corps_metier'] }}</div>
            <div class="header-role" style="margin-top: 6px;">PRESTATAIRE</div>
        </td>
    </tr>
</table>

{{-- Barre titre --}}
<div class="title-bar">
    ATTACHEMENT DES TRAVAUX
    <span class="title-bar-num">N° : {{ $attachement['numero_label'] }}</span>
</div>

{{-- Informations projet --}}
<div class="info-block">
    <strong>Titulaire :</strong> {{ strtoupper($attachement['titulaire']) }}<br>
    <strong>Contrat n° :</strong> {{ $attachement['contrat_libelle'] }}
    <span style="float: right;"><strong>Date :</strong> {{ $attachement['date'] }}</span><br>
    <strong>ATTACHEMENT N°{{ $attachement['numero_attachement'] }} :</strong> {{ strtoupper($attachement['attachement_titre']) }}<br>
    <strong>OBJET :</strong> {{ $attachement['objet'] }}<br>
    <strong>Prestataire :</strong> {{ $attachement['prestataire'] }}
</div>

{{-- Tableau principal --}}
<table class="bordered">
    <thead>
        <tr class="grey-head">
            <th rowspan="2" width="7%">N° DES PRIX</th>
            <th rowspan="2" width="24%">DESIGNATION</th>
            <th rowspan="2" width="5%">UNITE</th>
            <th rowspan="2" width="6%">QTES</th>
            <th rowspan="2" width="9%">PRIX UNITAIRE HT</th>
            <th rowspan="2" width="9%">MONTANT TOTAL HT</th>
            <th colspan="3" class="green-head">TAUX D'EXECUTION</th>
            <th colspan="3" class="green-head">MONTANT</th>
        </tr>
        <tr class="green-head">
            <th width="6%">M-1</th>
            <th width="6%">M</th>
            <th width="6%">CUMUL</th>
            <th width="8%">M-1</th>
            <th width="8%">M</th>
            <th width="8%">CUMUL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attachement['series'] as $serie)
            <tr class="serie-head">
                <td colspan="12" class="text-left">
                    SERIE {{ $serie['code'] }} - {{ strtoupper($serie['label']) }}
                </td>
            </tr>
            @foreach($serie['lignes'] as $ligne)
                <tr>
                    <td class="text-center">{{ $ligne['numero_prix'] }}</td>
                    <td class="text-left">{{ $ligne['designation'] }}</td>
                    <td class="text-center">{{ $ligne['unite'] }}</td>
                    <td class="text-center">{{ $fmt($ligne['quantite'], 1) }}</td>
                    <td class="text-right">{{ $fmt($ligne['prix_unitaire']) }}</td>
                    <td class="text-right">{{ $fmt($ligne['montant_total_ht']) }}</td>
                    <td class="text-center">{{ $fmtPct($ligne['taux_m1']) }}</td>
                    <td class="text-center">{{ $fmtPct($ligne['taux_m']) }}</td>
                    <td class="text-center">{{ $fmtPct($ligne['taux_cumul']) }}</td>
                    <td class="text-right">{{ $fmt($ligne['montant_m1']) }}</td>
                    <td class="text-right">{{ $fmt($ligne['montant_m']) }}</td>
                    <td class="text-right">{{ $fmt($ligne['montant_cumul']) }}</td>
                </tr>
            @endforeach
            <tr class="serie-total">
                <td colspan="5" class="text-right">TOTAL SERIE {{ $serie['code'] }}</td>
                <td class="text-right">{{ $fmt($serie['totaux']['montant_total_ht']) }}</td>
                <td colspan="3"></td>
                <td class="text-right">{{ $fmt($serie['totaux']['montant_m1']) }}</td>
                <td class="text-right">{{ $fmt($serie['totaux']['montant_m']) }}</td>
                <td class="text-right">{{ $fmt($serie['totaux']['montant_cumul']) }}</td>
            </tr>
        @endforeach

        <tr class="grand-total">
            <td colspan="5" class="text-right">SOUS TOTAL GENERAL</td>
            <td class="text-right">{{ $fmt($attachement['sous_total_general']['montant_total_ht']) }}</td>
            <td colspan="3"></td>
            <td class="text-right">{{ $fmt($attachement['sous_total_general']['montant_m1']) }}</td>
            <td class="text-right">{{ $fmt($attachement['sous_total_general']['montant_m']) }}</td>
            <td class="text-right">{{ $fmt($attachement['sous_total_general']['montant_cumul']) }}</td>
        </tr>
        <tr class="grand-total">
            <td colspan="10" class="text-right">TOTAL HT</td>
            <td colspan="2" class="text-right">{{ $fmt($attachement['total_ht']) }}</td>
        </tr>
        <tr class="grand-total">
            <td colspan="10" class="text-right">TVA {{ $fmt($attachement['taux_tva'], 0) }}%</td>
            <td colspan="2" class="text-right">{{ $fmt($attachement['montant_tva']) }}</td>
        </tr>
        <tr class="grand-total">
            <td colspan="10" class="text-right">MONTANT TOTAL ACTUALISE TTC</td>
            <td colspan="2" class="text-right">{{ $fmt($attachement['total_ttc']) }}</td>
        </tr>
    </tbody>
</table>

<div class="signature">
    <u>Le Gérant</u>
</div>

</body>
</html>
