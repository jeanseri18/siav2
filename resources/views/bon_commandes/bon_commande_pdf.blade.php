<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Commande {{ $bc['numero_po'] }}</title>
    <style>
        @page {
            margin: 12mm 10mm;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        table { border-collapse: collapse; width: 100%; }
        .bordered > thead > tr > th,
        .bordered > tbody > tr > td,
        .bordered > tfoot > tr > td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .bordered .footer-left-inner td,
        .bordered .tva-inline td,
        .bordered .conditions-table td {
            border: none !important;
            padding: 0;
        }
        .conditions-table {
            width: 100%;
            table-layout: fixed;
            border: none;
            font-size: 9.5px;
            text-align: left;
        }
        .conditions-table td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
            text-align: left;
        }
        .conditions-table .cond-label {
            width: 48%;
            text-align: left !important;
            padding-right: 4px;
            font-weight: normal;
        }
        .conditions-table .cond-value {
            width: 52%;
            text-align: left !important;
            font-weight: bold;
        }
        .cell-empty {
            min-height: 48px;
        }
        .delivery-grid {
            table-layout: fixed;
        }
        .delivery-grid .delivery-half {
            width: 50%;
            vertical-align: top;
        }
        .delivery-grid .delivery-conditions {
            width: 50%;
            vertical-align: top;
            text-align: left;
        }
        .logo { display: inline-block; border: 0; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .italic { font-style: italic; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .title-po {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .section-label {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
            text-align: left;
        }
        .section-label-center {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
            text-align: center;
        }
        .header-top-table {
            margin-bottom: 4px;
        }
        .header-bottom-table {
            margin-bottom: 0;
        }
        .fournisseur-block {
            text-align: center;
            font-size: 9.5px;
            line-height: 1.45;
        }
        .donneur-block {
            font-size: 9.5px;
            line-height: 1.45;
        }
        .delivery-block {
            font-size: 9.5px;
            line-height: 1.45;
            min-height: 52px;
        }
        .details-head th {
            background: #fff;
            font-weight: bold;
            font-style: italic;
            text-align: center;
            font-size: 9.5px;
            vertical-align: middle;
            padding: 5px 4px;
        }
        .details-table th,
        .details-table td {
            background: #fff;
        }
        .instructions {
            font-size: 8.5px;
            line-height: 1.35;
            margin-top: 8px;
            margin-bottom: 10px;
        }
        .details-title {
            font-size: 12.5px;
            font-style: italic;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0 8px 0;
            padding: 0;
            page-break-before: avoid;
        }
        .details-table {
            page-break-inside: auto;
            table-layout: fixed;
            width: 100%;
        }
        .details-table .col-num { width: 5%; }
        .details-table .col-ref { width: 10%; }
        .details-table .col-des { width: 35%; }
        .details-table .col-unite { width: 8%; }
        .details-table .col-qte { width: 7%; }
        .details-table .col-pu { width: 13%; }
        .details-table .col-remise { width: 7%; }
        .details-table .col-montant { width: 15%; }
        .details-table th.col-unite,
        .details-table td.col-unite,
        .details-table th.col-qte,
        .details-table td.col-qte {
            padding-left: 1px;
            padding-right: 1px;
            word-wrap: break-word;
            overflow: hidden;
        }
        .details-table tbody td {
            font-size: 9.5px;
            padding: 3px 5px;
            vertical-align: middle;
        }
        .details-footer-row,
        .details-footer-totals-row {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .details-footer-row {
            page-break-before: avoid !important;
        }
        .details-footer-row > td,
        .details-footer-totals-row > td {
            vertical-align: middle;
            padding: 3px 6px;
            font-size: 9.5px;
            font-weight: bold;
            background: #fff;
        }
        .details-footer-row > td.footer-left-wrap,
        .details-footer-totals-row > td.footer-left-wrap {
            padding: 0 !important;
            border-left: none !important;
            border-bottom: none !important;
        }
        .footer-left-inner {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .bordered .footer-left-inner td {
            border: none !important;
            padding: 3px 8px;
            font-size: 9.5px;
            font-weight: bold;
            vertical-align: middle;
            height: 22px;
        }
        .footer-left-inner .footer-row-arrete td {
            height: auto;
            min-height: 22px;
            vertical-align: top;
            padding-top: 6px;
        }
        .footer-left-inner .footer-row-lettres td {
            height: auto;
            min-height: 22px;
            text-align: right;
            padding-top: 4px;
            padding-bottom: 4px;
        }
        .footer-left-inner .footer-row-spacer td {
            height: 22px;
        }
        .footer-left-inner .footer-row-visa td,
        .footer-left-inner .footer-row-dg td {
            vertical-align: bottom;
            padding-bottom: 4px;
        }
        .footer-arrete {
            text-decoration: underline;
        }
        .details-footer-totals-row td {
            border-top: 1px solid #bbb;
        }
        .details-footer-row td.totals-label,
        .details-footer-totals-row td.totals-label {
            text-align: left;
        }
        .details-footer-row td.totals-spacer,
        .details-footer-totals-row td.totals-spacer {
            padding: 0;
            width: 7%;
        }
        .details-footer-row td.totals-value,
        .details-footer-totals-row td.totals-value {
            text-align: right;
            border-left: 1px solid #000;
        }
        .details-footer-row td.totals-value-dash,
        .details-footer-totals-row td.totals-value-dash {
            text-align: center;
            border-left: 1px solid #000;
        }
    </style>
</head>
<body>
@php
    $cg = $configGlobal ?? null;
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $company = $bc['company'] ?? $pdfBranding['company'] ?? [];
    $logoSrc = $pdfBranding['logo_src'] ?? null;
    $bc = $bc ?? [];
    $fmt = fn ($v, $d = 0) => number_format((float) $v, $d, ',', ' ');
    $nomCourt = $bc['nom_entreprise'] ?? ($pdfBranding['nom_entreprise'] ?? '');
    $nomLegal = $company['nom'] ?? $nomCourt;
    $adresseComplete = trim(collect([
        $company['localisation'] ?? ($bc['adresse_sia'] ?? null),
        $company['adresse_postale'] ?? null,
    ])->filter()->implode(' - '));
    $remiseAffiche = (float) ($bc['total_remise'] ?? 0) > 0
        ? $fmt($bc['total_remise'])
        : '-';
@endphp

<div class="title-po">BON DE COMMANDE N°. {{ $bc['numero_po'] }}</div>

{{-- Bloc 1 : Donneur d'ordre (65 %) | Fournisseur (35 %) --}}
<table class="bordered header-top-table" cellspacing="0" cellpadding="0">
    <tr>
        <td width="65%" valign="top">
            <div class="section-label-center">DONNEUR D'ORDRE :</div>
            <table width="100%" cellspacing="0" cellpadding="0" class="donneur-block" style="border: none;">
                <tr>
                    <td width="30%" valign="top" style="border: none; padding: 0 4px 0 0;">
                        @include('partials.pdf-logo', ['pdfBranding' => $pdfBranding ?? [], 'logoClass' => 'logo', 'maxWidth' => 190, 'maxHeight' => 95])
                    </td>
                    <td width="70%" valign="top" style="border: none; padding: 0;">
                        <strong>{{ $nomCourt }}</strong><br>
                        @if(filled($nomLegal) && strtoupper(trim($nomLegal)) !== strtoupper(trim($nomCourt)))
                            <strong>{{ strtoupper($nomLegal) }}</strong><br>
                        @endif
                        @if(filled($company['localisation']))
                            {{ $company['localisation'] }}<br>
                        @endif
                        @if(filled($company['adresse_postale']))
                            {{ $company['adresse_postale'] }}<br>
                        @endif
                        @if(filled($company['tel1']))
                            Tel : {{ $company['tel1'] }}@if(filled($company['tel2'])) / {{ $company['tel2'] }}@endif<br>
                        @endif
                        @if(filled($company['email']))
                            Email : {{ $company['email'] }}
                        @endif
                    </td>
                </tr>
            </table>
        </td>
        <td width="35%" valign="top">
            <div class="section-label-center">FOURNISSEUR :</div>
            <div class="fournisseur-block">
                <strong>{{ strtoupper($bc['fournisseur_nom']) }}</strong><br>
                {!! nl2br(e($bc['fournisseur_adresse'])) !!}<br>
                @if($bc['fournisseur_tel'])
                    Tel : {{ $bc['fournisseur_tel'] }}<br>
                @endif
                @if($bc['fournisseur_email'])
                    E-mail: {{ $bc['fournisseur_email'] }}
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- Bloc 2 : Livraison / Facture (gauche) | Conditions (droite) --}}
<table class="bordered header-bottom-table delivery-grid" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="delivery-half delivery-block" valign="top">
            <div class="section-label">VEUILLEZ LIVRER LES MARCHANDISES A :</div>
            {{ $nomCourt }}<br>
            @if(filled($adresseComplete))
                {{ $adresseComplete }}<br>
            @endif
            @if(filled($bc['horaires_ouverture'] ?? null))
                Horaire d'ouverture : {{ $bc['horaires_ouverture'] }}
            @endif
        </td>
        <td width="50%" rowspan="2" class="delivery-conditions" valign="top">
            <div class="section-label">CONDITION DE LIVRAISON :</div>
            <table class="conditions-table" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="cond-label">Date du document :</td>
                    <td class="cond-value">{{ $bc['date_document'] }}</td>
                </tr>
                <tr>
                    <td class="cond-label underline">Nom du projet :</td>
                    <td class="cond-value bold">{{ $bc['nom_projet'] }}</td>
                </tr>
                <tr>
                    <td class="cond-label">N° Document externe :</td>
                    <td class="cond-value">{{ $bc['doc_externe'] }}</td>
                </tr>
                <tr>
                    <td class="cond-label">Conditions de paiement :</td>
                    <td class="cond-value bold">{{ $bc['conditions_paiement'] }}</td>
                </tr>
                <tr>
                    <td class="cond-label">Devise :</td>
                    <td class="cond-value bold">{{ $bc['devise'] }}</td>
                </tr>
                <tr>
                    <td class="cond-label">En référence au contrat N° :</td>
                    <td class="cond-value">{{ $bc['contrat_ref'] ?: ' ' }}</td>
                </tr>
                <tr>
                    <td class="cond-label">Responsable du contrat :</td>
                    <td class="cond-value">{{ $bc['responsable_contrat'] ?: ' ' }}</td>
                </tr>
                <tr>
                    <td class="cond-label">Lieu de livraison :</td>
                    <td class="cond-value bold">{{ $bc['lieu_livraison'] }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="50%" class="delivery-half delivery-block" valign="top">
            <div class="section-label">VEUILLEZ TRANSMETTRE LA FACTURE A :</div>
            {{ $nomCourt }}<br>
            @if(filled($adresseComplete))
                {{ $adresseComplete }}<br>
            @endif
            @if(filled($bc['horaires_ouverture'] ?? null))
                Horaire d'ouverture : {{ $bc['horaires_ouverture'] }}
            @endif
        </td>
    </tr>
</table>

<div class="instructions">
    <span class="underline italic bold">Instructions Générales :</span><br>
    Cette commande est soumise aux conditions générales affectées à ce bon de commande et à la référence légale du contrat indiqué ci-dessus (le cas échéant).<br>
    Veuillez envoyer une confirmation de commande à la personne à contacter pour le bon de commande dans les trois (3) jours ouvrables (à compter de la date du document).<br>
    Veuillez indiquer le numéro du bon de commande sur tous les documents pertinents, y compris la Facture, le(s) bon(s) de livraison, la liste de colisage éventuellement.<br>
    En cas de problème de paiement et / ou de facturation, veuillez contacter {{ $bc['nom_entreprise'] }}@if(filled($bc['email_entreprise'])) par e-mail à l'adresse {{ $bc['email_entreprise'] }}@endif<br>
    Le non-respect des instructions mentionnées ci-dessus pourrait retarder, voir entrainer le non-paiement / règlement des factures.<br>
    Tandis que la mention de la TVA est obligatoire, la signature et le cachet sont requis sur le formulaire de bon de commande pour attester de son authenticité.
</div>

<div class="details-title">DETAILS DE LA COMMANDE :</div>

<table class="bordered details-head details-table" cellspacing="0" cellpadding="0" width="100%">
    <thead>
        <tr>
            <th class="col-num" width="5%">N° Ligne</th>
            <th class="col-ref" width="10%">Ref. Article</th>
            <th class="col-des" width="35%">Designation de l'article</th>
            <th class="col-unite" width="8%">Unité</th>
            <th class="col-qte" width="7%">Qté</th>
            <th class="col-pu" width="13%">Prix unitaire HT</th>
            <th class="col-remise" width="7%">% Remise</th>
            <th class="col-montant" width="15%">Montant HT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bc['lignes'] as $ligne)
            <tr>
                <td class="text-center col-num" width="5%">{{ $ligne['numero_ligne'] }}</td>
                <td class="text-center col-ref" width="10%">{{ $ligne['ref_article'] }}</td>
                <td class="text-left col-des" width="35%">{{ $ligne['designation'] }}</td>
                <td class="text-center col-unite" width="8%">{{ $ligne['unite'] }}</td>
                <td class="text-center col-qte" width="7%">{{ $fmt($ligne['quantite'], 0) }}</td>
                <td class="text-right col-pu" width="13%">{{ $fmt($ligne['prix_unitaire']) }}</td>
                <td class="text-center col-remise" width="7%">{{ $ligne['remise'] > 0 ? $fmt($ligne['remise'], 0) : '' }}</td>
                <td class="text-right col-montant" width="15%">{{ $fmt($ligne['montant_ht']) }}</td>
            </tr>
        @endforeach

        <tr class="details-footer-row">
            <td colspan="3" rowspan="5" class="footer-left-wrap" valign="top">
                <table class="footer-left-inner" cellspacing="0" cellpadding="0">
                    <tr class="footer-row-arrete">
                        <td><span class="footer-arrete">Arrêtée le montant de cette commande à la somme de :</span></td>
                    </tr>
                    <tr class="footer-row-lettres">
                        <td>{{ $bc['montant_lettres'] }}</td>
                    </tr>
                    <tr class="footer-row-spacer">
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="footer-row-visa">
                        <td>Visa Resp. Financier : {{ $bc['date_visa'] }}</td>
                    </tr>
                    <tr class="footer-row-dg">
                        <td>Signature D.G : {{ $bc['date_visa'] }}</td>
                    </tr>
                </table>
            </td>
            <td colspan="3" class="totals-label">Montant Total Hors Taxe</td>
            <td class="totals-spacer">&nbsp;</td>
            <td class="totals-value">{{ $fmt($bc['total_ht_brut']) }}</td>
        </tr>
        <tr class="details-footer-totals-row">
            <td colspan="3" class="totals-label">Montant total Remise</td>
            <td class="totals-spacer">&nbsp;</td>
            <td class="{{ $remiseAffiche === '-' ? 'totals-value-dash' : 'totals-value' }}">{{ $remiseAffiche }}</td>
        </tr>
        <tr class="details-footer-totals-row">
            <td colspan="3" class="totals-label">Montant Total HT Net</td>
            <td class="totals-spacer">&nbsp;</td>
            <td class="totals-value">{{ $fmt($bc['total_ht_net']) }}</td>
        </tr>
        <tr class="details-footer-totals-row">
            <td colspan="3" class="totals-label">TVA {{ $fmt($bc['taux_tva'] ?? 0, 0) }}%</td>
            <td class="totals-spacer">&nbsp;</td>
            <td class="totals-value">{{ $fmt($bc['tva']) }}</td>
        </tr>
        <tr class="details-footer-totals-row">
            <td colspan="3" class="totals-label">Montant TTC</td>
            <td class="totals-spacer">&nbsp;</td>
            <td class="totals-value">{{ $fmt($bc['total_ttc']) }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>
