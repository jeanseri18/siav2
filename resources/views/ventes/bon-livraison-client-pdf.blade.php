<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de livraison client {{ $bl['numero'] }}</title>
    <style>
        @page { margin: 12mm 10mm; }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11.5px;
            color: #000;
            margin: 20px;
            padding: 0;
        }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .header td {
            border: none;
            vertical-align: top;
            padding: 0;
            font-size: 10.5px;
            line-height: 1.45;
        }
        .w-left  { width: 60%; }
        .w-right { width: 42%; }
        .right-block {
            width: 305px;
            max-width: 305px;
        }
        .logo { display: inline-block; border: 0; }
        .items-table { margin-top: 12px; }
        .items-table thead th {
            background: #e0e0e0;
            border: 1px solid #000;
            padding: 6px 8px;
            font-weight: bold;
            font-size: 10.5px;
            text-align: left;
        }
        .items-table thead th.col-qty { text-align: right; }
        .items-table tbody td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 6px 8px;
            font-size: 10.5px;
        }
        .items-table tbody tr.items-spacer td {
            height: 110px;
            border-bottom: 1px solid #000;
        }
        .items-table tbody tr:last-child td { border-bottom: 1px solid #000; }
        .col-qty { text-align: right; }
    </style>
</head>
<body>
@php
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $company = $pdfBranding['company'] ?? [];
    $logoSrc = $pdfBranding['logo_src'] ?? null;
    $fmtQty = fn ($v) => number_format((float) $v, 0, ',', ' ');
@endphp

<table class="header" cellspacing="0" cellpadding="0">

    {{-- Ligne 1 : Logo (gauche) | Titre + Date (droite) --}}
    <tr>
        <td class="w-left" valign="top">
            @include('partials.pdf-logo', ['pdfBranding' => $pdfBranding ?? [], 'logoClass' => 'logo', 'maxWidth' => 190, 'maxHeight' => 95])
        </td>
        <td class="w-right" valign="top" align="right" style="text-align: right;">
            <table class="right-block" cellspacing="0" cellpadding="0" align="right" style="width: 205px; margin-left: auto;">
                <tr>
                    <td align="right" style="text-align: right;">
                        <b style="font-size: 16px;">Bon de livraison client - {{ $bl['numero'] }}</b><br>
                        <span style="font-size: 11.5px;">Date : {{ $bl['date'] }}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Ligne 2 : Société (gauche) | Livré à (droite) --}}
    <tr>
        <td class="w-left" valign="top" style="padding-top: 6px;">
            <b>{{ strtoupper($company['nom'] ?? $pdfBranding['nom_entreprise']) }}</b><br>
            @if(!empty($company['localisation']))
                {{ $company['localisation'] }}<br>
            @endif
            @if(!empty($company['adresse_postale']))
                {{ $company['adresse_postale'] }}<br>
            @endif
            @if(!empty($company['rccm']))
                RCCM : {{ $company['rccm'] }}<br>
            @endif
            @if(!empty($company['cc']))
                N° CC : {{ $company['cc'] }}<br>
            @endif
            @if(!empty($company['tel1']))
                Tel. : {{ $company['tel1'] }}@if(!empty($company['tel2'])) / {{ $company['tel2'] }}@endif<br>
            @endif
            @if(!empty($company['email']))
                <u>{{ $company['email'] }}</u>
            @endif
        </td>
        <td class="w-right" valign="top" align="right" style="text-align: right; padding-top: 6px;">
            <table class="right-block" cellspacing="0" cellpadding="0" align="right" style="width: 205px; margin-left: auto;">
                <tr>
                    <td align="left" style="text-align: left;">
                        <b><u>Livré à :</u></b><br>
                        {{ $bl['livre_a'] }}<br>
                        @if(!empty($bl['adresse_rue']))
                            {{ $bl['adresse_rue'] }}<br>
                        @endif
                        @if(!empty($bl['adresse_bp']))
                            {{ $bl['adresse_bp'] }}<br>
                        @endif
                        @if(filled($bl['rccm']))
                            <b>RCCM :</b> {{ $bl['rccm'] }}<br>
                        @endif
                        @if(filled($bl['cc']))
                            <b>N° CC :</b> {{ $bl['cc'] }}<br>
                        @endif
                        @if(filled($bl['telephone']))
                            Tel. : {{ $bl['telephone'] }}<br>
                        @endif
                        @if(filled($bl['email']))
                            {{ $bl['email'] }}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2" style="height: 20px; font-size: 1px; line-height: 1px;">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="2" valign="top" align="left">
            <b>Réf. vente :</b> {{ $bl['ref_vente'] }}
            @if(filled($bl['ref_devis']))
                &nbsp;&nbsp;|&nbsp;&nbsp;<b>Réf. devis :</b> {{ $bl['ref_devis'] }}
            @endif
        </td>
    </tr>

    <tr>
        <td class="w-left" valign="top" align="left" style="padding-top: 3px;">
            <b>B.L émis par :</b> {{ $bl['emis_par'] }}
        </td>
        <td class="w-right" valign="top" align="right" style="text-align: right; padding-top: 3px;">
            <table class="right-block" cellspacing="0" cellpadding="0" align="right" style="width: 205px; margin-left: auto;">
                <tr>
                    <td align="left" style="text-align: left;">
                        <b>Lieu de livraison :</b> {{ $bl['lieu_livraison'] }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>

<table class="items-table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th width="8%">N° Ligne</th>
            <th width="14%">Ref. Produit</th>
            <th width="38%">Désignation</th>
            <th width="12%">Unité</th>
            <th width="14%" class="col-qty">Qté commandée</th>
            <th width="14%" class="col-qty">Qté livrée</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bl['lignes'] as $ligne)
            <tr>
                <td>{{ $ligne['numero_ligne'] }}</td>
                <td>{{ $ligne['ref_produit'] }}</td>
                <td>{{ $ligne['designation'] }}</td>
                <td>{{ $ligne['unite'] }}</td>
                <td class="col-qty">{{ $fmtQty($ligne['quantite_commandee']) }}</td>
                <td class="col-qty">{{ $fmtQty($ligne['quantite_livree']) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Aucune ligne sur ce bon de livraison client.</td>
            </tr>
        @endforelse
        <tr class="items-spacer">
            <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td>
        </tr>
    </tbody>
</table>

</body>
</html>
