<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de versement artisan — Décompte {{ $fiche['numero_decompte_label'] }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11.5px;
            color: #000;
            margin: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .bordered td,
        .bordered th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .decompte-header-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin: 0 0 3px 0;
        }
        .decompte-header-table td {
            padding: 6px 8px;
            vertical-align: top;
            font-size: 11.5px;
            line-height: 1.45;
        }
        .decompte-header-table .cell-divider {
            border-right: 1px solid #000;
        }
        .decompte-header-table .col-65 { width: 65%; }
        .decompte-header-table .col-35 { width: 35%; }
        .decompte-header-table .col-54 { width: 54%; }
        .decompte-row3-wrap {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 3px 0;
        }
        .decompte-row3-wrap td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .decompte-row3-number {
            width: 11%;
            padding: 6px 8px;
            font-size: 27px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .decompte-row3-content {
            width: 89%;
        }
        .decompte-row3-content .decompte-header-table td {
            padding: 3px 4px 3px 3px;
        }
        .decompte-row3-content .row3-meta-table td {
            padding-left: 0;
        }
        .decompte-header-wrap {
            margin-bottom: 8px;
        }
        .row3-meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .row3-meta-table td {
            border: none;
            padding: 0;
            font-size: 11.5px;
            line-height: 1.5;
            vertical-align: top;
        }
        .row3-meta-label {
            white-space: nowrap;
            padding-right: 8px;
        }
        .cell-title {
            font-weight: bold;
            text-decoration: underline;
            text-align: center;
            margin-bottom: 8px;
            font-size: 11.5px;
        }
        .donneur-inner {
            width: 100%;
            border-collapse: collapse;
        }
        .donneur-inner td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .logo-cell {
            width: 120px;
            padding-right: 10px;
        }
        .logo {
            display: inline-block;
            border: 0;
        }
        .company-block {
            font-size: 11.5px;
            line-height: 1.45;
        }
        .prestataire-box {
            text-align: center;
            line-height: 1.45;
        }
        .prestataire-name {
            font-size: 13.5px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .info-line {
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .decompte-main-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 3px;
        }
        .decompte-main-table th,
        .decompte-main-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            font-size: 11.5px;
        }
        .decompte-main-table thead th {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .decompte-main-table .libelle-stack {
            padding-left: 8px;
        }
        .decompte-main-table .body-line {
            line-height: 1.55;
            min-height: 14px;
        }
        .decompte-main-table .body-line-section {
            font-weight: bold;
            text-decoration: underline;
        }
        .decompte-main-table .body-line-item {
            padding-left: 4px;
        }
        .decompte-main-table .taux-cell {
            text-align: center;
            vertical-align: middle;
        }
        .decompte-main-table .inner-wrap {
            padding: 0;
        }
        .decompte-inner-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .decompte-inner-grid td {
            border: none;
            padding: 4px 6px;
            line-height: 1.55;
            min-height: 14px;
            font-size: 11.5px;
            vertical-align: top;
        }
        .decompte-main-table .totals-label {
            background: #d9d9d9;
            font-weight: bold;
            text-align: right;
        }
        .decompte-main-table .totals-value {
            background: #fff;
        }
        .decompte-main-table tr.totals-row td.totals-left-empty {
            border-left: none !important;
            border-bottom: none !important;
            border-right: none !important;
            border-top: none !important;
            background: #fff;
        }
        .decompte-main-table tbody tr.totals-row:nth-child(2) td.totals-left-empty {
            border-top: 1px solid #000 !important;
        }
        .decompte-paiement-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-top: 0;
        }
        .decompte-paiement-table th,
        .decompte-paiement-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-size: 11.5px;
            vertical-align: middle;
        }
        .decompte-paiement-table thead th {
            background: #fff;
            font-weight: bold;
        }
        .grey-head th {
            background: #d9d9d9;
            font-weight: bold;
            text-align: center;
            font-size: 10.5px;
        }
        .totals td {
            border: 1px solid #000;
            padding: 4px 8px;
        }
        .signature {
            margin-top: 24px;
            text-align: right;
            font-size: 12.5px;
        }
        .signature u {
            font-weight: bold;
        }
    </style>
</head>
<body>
@php
    $cg = $configGlobal ?? null;
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $fmt = function ($value, $decimals = 0) {
        $n = (float) $value;
        if ($decimals === 0 && abs($n - round($n)) < 0.00001) {
            return number_format($n, 0, ',', ' ');
        }
        return number_format($n, $decimals, ',', ' ');
    };
    $fmtPct = fn ($value) => number_format((float) $value, 2, ',', ' ') . '%';

    $company = $pdfBranding['company'] ?? [];
    $logoSrc = $pdfBranding['logo_src'] ?? null;
    $companyNom = $company['nom'] ?? $pdfBranding['nom_entreprise'];
    $nomCourt = $pdfBranding['nom_entreprise'];
    $raisonSociale = null;
    if (filled($companyNom) && strtoupper(trim($companyNom)) !== strtoupper(trim($nomCourt))) {
        $raisonSociale = $companyNom;
    }
    $telEntreprise = $company['tel1'] ?? $cg?->tel1 ?? '';
    if (filled($telEntreprise)) {
        $telEntreprise = trim(explode('/', (string) $telEntreprise)[0]);
        $telEntreprise = trim((string) preg_replace('/^\(\+225\)\s*/', '', $telEntreprise));
    }

    $artisan = $prestation->artisan;
    $corpsMetier = $prestation->corpMetier?->nom ?? $artisan?->fonction ?? '—';
    $nomArtisan = $artisan
        ? trim(($artisan->nom ?? '') . ' ' . ($artisan->prenoms ?? ''))
        : ($prestation->fournisseur?->nom_raison_sociale ?? '—');
    $localisationArtisan = $artisan?->localisation ?? $prestation->fournisseur?->adresse_localisation ?? '';
    $bpArtisan = $artisan?->boite_postale ?? $prestation->fournisseur?->boite_postale ?? '';
    $telArtisan = $artisan?->tel1 ?? $prestation->fournisseur?->telephone ?? '—';
    $numeroDecompteAffiche = str_pad((string) ($fiche['numero_decompte'] ?? 1), 2, '0', STR_PAD_LEFT);

    $bodyLines = [];
    $bodyLines[] = ['type' => 'section', 'libelle' => '1. TRAVAUX EXECUTES :'];

    if (count($fiche['lignes_travaux'] ?? []) > 0) {
        foreach ($fiche['lignes_travaux'] as $ligneTravaux) {
            $bodyLines[] = [
                'type' => 'full',
                'libelle' => $ligneTravaux['libelle'],
                'unite' => $ligneTravaux['unite'],
                'quantite' => $fmt($ligneTravaux['quantite'], 0),
                'prix_unitaire' => $fmt($ligneTravaux['prix_unitaire']),
                'montant' => $fmt($ligneTravaux['montant']),
            ];
        }
    } else {
        $bodyLines[] = [
            'type' => 'full',
            'libelle' => 'Travaux exécutés',
            'unite' => 'Ff',
            'quantite' => '1',
            'prix_unitaire' => $fmt($decompte->montant),
            'montant' => $fmt($decompte->montant),
        ];
    }

    $bodyLines[] = [
        'type' => 'amount_only',
        'libelle' => 'Travaux Supplémentaires',
        'montant' => $fmt($fiche['travaux_supplementaires']),
    ];

    $bodyLines[] = ['type' => 'section', 'libelle' => '3. RETENUES :'];
    $bodyLines[] = [
        'type' => 'full',
        'libelle' => 'Retenue de garantie ' . $fmt($fiche['taux_garantie'], 0) . '%',
        'unite' => 'Ff',
        'quantite' => '1',
        'prix_unitaire' => $fmt($fiche['retenue']['montant']),
        'montant' => $fmt($fiche['retenue']['montant']),
    ];
    $bodyLines[] = [
        'type' => 'amount_only',
        'libelle' => 'Pénalités (Mals façons)',
        'montant' => $fmt($fiche['montant_penalites']),
    ];
    $bodyLines[] = [
        'type' => 'amount_only',
        'libelle' => 'PPSI',
        'montant' => $fmt($fiche['montant_ppsi']),
    ];

    $bodyLines[] = ['type' => 'section', 'libelle' => '4. RECUPERATION AVANCES APPRO :'];
    $bodyLines[] = [
        'type' => 'amount_only',
        'libelle' => 'Récupération Avance Diverses',
        'montant' => $fmt($fiche['montant_recuperation_avances']),
    ];
@endphp

{{-- En-tête décompte : 3 tableaux empilés (effet double bordure) --}}
<div class="decompte-header-wrap">

{{-- Tableau 1 : Donneur d'ordre / Prestataire --}}
<table class="decompte-header-table" cellspacing="0" cellpadding="0">
    <tr>
        <td class="col-65 cell-divider">
            <div class="cell-title">DONNEUR D'ORDRE :</div>
            <table class="donneur-inner" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="logo-cell" valign="top">
                        @include('partials.pdf-logo', ['pdfBranding' => $pdfBranding ?? [], 'logoClass' => 'logo', 'maxWidth' => 190, 'maxHeight' => 95])
                    </td>
                    <td valign="top" class="company-block">
                        <strong>{{ $nomCourt }}</strong><br>
                        @if(filled($raisonSociale))
                            <strong>{{ $raisonSociale }}</strong><br>
                        @endif
                        @if(filled($company['localisation'] ?? $cg?->localisation))
                            {{ $company['localisation'] ?? $cg->localisation }}<br>
                        @endif
                        @if(filled($company['adresse_postale'] ?? $cg?->adresse_postale))
                            {{ $company['adresse_postale'] ?? $cg->adresse_postale }}<br>
                        @endif
                        @if(filled($telEntreprise))
                            Tel : {{ $telEntreprise }}<br>
                        @endif
                        @if(filled($company['email'] ?? $cg?->email))
                            Email : {{ $company['email'] ?? $cg->email }}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
        <td class="col-35 prestataire-box">
            <div class="cell-title">PRESTATAIRE / ARTISAN</div>
            <div class="prestataire-name">{{ strtoupper($nomArtisan) }}</div>
            @if(filled($localisationArtisan))
                {{ $localisationArtisan }}<br>
            @endif
            @if(filled($bpArtisan))
                {{ $bpArtisan }}<br>
            @endif
            @if($telArtisan !== '—')
                Tel : {{ $telArtisan }}<br>
            @endif
            CORPS DE METIER : <strong>{{ strtoupper($corpsMetier) }}</strong>
        </td>
    </tr>
</table>

{{-- Tableau 2 : Projet --}}
<table class="decompte-header-table" cellspacing="0" cellpadding="0">
    <tr>
        <td class="col-65">
            <div class="info-line">PROJET : {{ $fiche['projet'] }}</div>
            <div class="info-line">CONTRAT : {{ $fiche['contrat'] }}</div>
            <div class="info-line">LOCALISATION : {{ $fiche['localisation'] }}</div>
        </td>
        <td class="col-35">
            <div class="info-line">DATE DEBUT : {{ $fiche['date_debut'] }}</div>
            <div class="info-line">DELAI D'EXECUTION : {{ $fiche['delai_execution'] }}</div>
            <div class="info-line">% RETENUE DE GARANTIE : {{ $fmt($fiche['taux_garantie'], 0) }}%</div>
        </td>
    </tr>
</table>

{{-- Tableau 3 : Numéro / Montants / Décompte --}}
<table class="decompte-row3-wrap" cellspacing="0" cellpadding="0">
    <tr>
        <td class="decompte-row3-number">{{ $numeroDecompteAffiche }}</td>
        <td class="decompte-row3-content">
            <table class="decompte-header-table" cellspacing="0" cellpadding="0" style="margin-bottom: 0;">
                <tr>
                    <td class="col-54 cell-divider">
                        Montant Contrat HT : {{ $fmt($fiche['montant_contrat_ht']) }}<br>
                        Montant Avenant HT : {{ $fiche['montant_avenant_ht'] !== null ? $fmt($fiche['montant_avenant_ht']) : '' }}<br>
                        Montant Total Contrat HT : {{ $fmt($fiche['montant_total_contrat_ht']) }}
                    </td>
                    <td class="col-35">
                        <table class="row3-meta-table" cellspacing="0" cellpadding="0">
                            <tr>
                                <td colspan="2">Décompte N° : {{ $fiche['numero_decompte_label'] }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">Date d'émission : {{ $fiche['date_emission'] }}</td>
                            </tr>
                            <tr>
                                <td class="row3-meta-label">Saisi Par :</td>
                                <td>{{ $fiche['saisi_par'] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</div>

{{-- Tableau principal --}}
<table class="decompte-main-table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th width="28%">LIBELLE</th>
            <th width="12%">Taux d'avancement<br>précédent</th>
            <th width="12%">Taux d'avancement</th>
            <th width="8%">Unité</th>
            <th width="8%">Quantité</th>
            <th width="16%">Prix unitaire HT</th>
            <th width="16%">MontantHT</th>
        </tr>
    </thead>
    <tbody>
        <tr class="decompte-body-row">
            <td class="libelle-stack" valign="top">
                @foreach($bodyLines as $line)
                    <div class="body-line {{ $line['type'] === 'section' ? 'body-line-section' : 'body-line-item' }}">{{ $line['libelle'] }}</div>
                @endforeach
            </td>
            <td class="taux-cell" valign="middle">{{ $fmtPct($fiche['retenue']['taux_precedent']) }}</td>
            <td class="taux-cell" valign="middle">{{ $fmtPct($fiche['retenue']['taux_actuel']) }}</td>
            <td colspan="4" class="inner-wrap" valign="top">
                <table class="decompte-inner-grid" cellspacing="0" cellpadding="0">
                    <colgroup>
                        <col width="16.67%">
                        <col width="16.67%">
                        <col width="33.33%">
                        <col width="33.33%">
                    </colgroup>
                    @foreach($bodyLines as $line)
                        <tr>
                            @if($line['type'] === 'section')
                                <td colspan="4">&nbsp;</td>
                            @elseif($line['type'] === 'amount_only')
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ $line['montant'] }}</td>
                            @else
                                <td class="text-center">{{ $line['unite'] }}</td>
                                <td class="text-center">{{ $line['quantite'] }}</td>
                                <td class="text-right">{{ $line['prix_unitaire'] }}</td>
                                <td class="text-right">{{ $line['montant'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>

        <tr class="totals-row">
            <td colspan="3" class="totals-left-empty"></td>
            <td colspan="3" class="totals-label">TOTAL HT à régler</td>
            <td class="text-right bold totals-value">{{ $fmt($fiche['total_ht_regler']) }}</td>
        </tr>
        <tr class="totals-row">
            <td colspan="3" class="totals-left-empty"></td>
            <td colspan="3" class="totals-label">TVA {{ $fmt($fiche['taux_tva'], 0) }}%</td>
            <td class="text-right totals-value">{{ $fmt($fiche['montant_tva'], 1) }}</td>
        </tr>
        <tr class="totals-row">
            <td colspan="3" class="totals-left-empty"></td>
            <td colspan="3" class="totals-label">TOTAL Net TTC à régler</td>
            <td class="text-right bold totals-value">{{ $fmt($fiche['total_net_ttc'], 1) }}</td>
        </tr>
    </tbody>
</table>

{{-- Récapitulatif paiement --}}
<table class="decompte-paiement-table" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th>Mode de paiement</th>
            <th>Total décomptes perçus</th>
            <th>Total retenue de garantie</th>
            <th>Reste à percevoir</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $fiche['mode_paiement'] }}</td>
            <td>{{ $fmt($fiche['total_decomptes_percus']) }}</td>
            <td>{{ $fmt($fiche['total_retenue_garantie']) }}</td>
            <td>{{ $fmt($fiche['reste_a_percevoir']) }}</td>
        </tr>
    </tbody>
</table>

<div class="signature">
    <u>Signature prestataire</u>
</div>

</body>
</html>
