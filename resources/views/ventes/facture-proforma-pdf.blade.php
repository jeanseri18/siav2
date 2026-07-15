@php
if (! function_exists('venteNumberToWords')) {
    function venteNumberToWords($number) {
        $ones = ['', 'Un', 'Deux', 'Trois', 'Quatre', 'Cinq', 'Six', 'Sept', 'Huit', 'Neuf'];
        $tens = ['', 'Dix', 'Vingt', 'Trente', 'Quarante', 'Cinquante', 'Soixante', 'Soixante-dix', 'Quatre-vingt', 'Quatre-vingt-dix'];

        if ($number == 0) {
            return 'Zéro';
        }

        $words = '';
        $number = (int) round($number);

        if ($number >= 1000000) {
            $millions = (int) ($number / 1000000);
            $words .= venteNumberToWords($millions) . ' Million' . ($millions > 1 ? 's' : '');
            $number %= 1000000;
            if ($number > 0) {
                $words .= ' ';
            }
        }

        if ($number >= 1000) {
            $thousands = (int) ($number / 1000);
            $words .= ($thousands === 1 ? 'Mille' : venteNumberToWords($thousands) . ' Mille');
            $number %= 1000;
            if ($number > 0) {
                $words .= ' ';
            }
        }

        if ($number >= 100) {
            $hundred = (int) ($number / 100);
            $words .= ($hundred === 1 ? 'Cent' : $ones[$hundred] . ' Cent');
            $number %= 100;
            if ($number > 0) {
                $words .= ' ';
            }
        }

        if ($number >= 20) {
            $ten = (int) ($number / 10);
            $one = $number % 10;
            if ($one === 1 && $ten === 7) {
                $words .= 'Soixante et Onze';
            } elseif ($one === 1 && $ten === 9) {
                $words .= 'Quatre-vingt-onze';
            } elseif ($one > 0) {
                $words .= $tens[$ten] . '-' . $ones[$one];
            } else {
                $words .= $tens[$ten];
            }
        } elseif ($number >= 10) {
            $words .= match ($number) {
                10 => 'Dix', 11 => 'Onze', 12 => 'Douze', 13 => 'Treize',
                14 => 'Quatorze', 15 => 'Quinze', 16 => 'Seize',
                default => 'Dix-' . $ones[$number - 10],
            };
        } elseif ($number > 0) {
            $words .= $ones[$number];
        }

        return trim($words);
    }
}

$cg = $configGlobal ?? null;
$pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
$company = $pdfBranding['company'] ?? [];
$logoSrc = $pdfBranding['logo_src'] ?? null;
$client = $vente->client;
$fmt = fn ($value, $decimals = 0) => number_format((float) $value, $decimals, ',', ' ');

$numeroProforma = 'FP' . $vente->created_at->format('mY') . '-' . str_pad((string) $vente->id, 5, '0', STR_PAD_LEFT);
$nomClient = $vente->nom_client ?: ($client?->nom_raison_sociale ?? '—');
$adresseBp = $client?->boite_postale ?? '';
$adresseRue = $client?->adresse_localisation ?? '';
$telClient = $client?->telephone ?? '—';
$aLivrerA = $nomClient;
if (filled($vente->commentaire)) {
    $aLivrerA = trim((string) preg_replace('/^A\s*livrer\s*[àa]\s*:\s*/iu', '', trim($vente->commentaire)));
}

$dateFacture = $vente->created_at->format('d/m/Y');
$delaiPaiementRaw = $client?->delai_paiement;
$delaiJours = 30;
if (filled($delaiPaiementRaw) && preg_match('/(\d+)/', (string) $delaiPaiementRaw, $matchDelaiPaiement)) {
    $delaiJours = max(1, (int) $matchDelaiPaiement[1]);
}
$dateEcheance = $vente->created_at->copy()->addDays(max(1, $delaiJours))->format('d/m/Y');

$totalHt = (float) ($vente->total_ht ?? 0);
$eloignementPct = 0;
$montantEloignement = 0;
$totalHtAvecEloignement = $totalHt + $montantEloignement;
$tva = (float) ($vente->tva ?? 0);
$totalTtc = (float) ($vente->total_ttc ?? ($totalHt + $tva));

$lignes = [];
foreach ($vente->articles as $article) {
    $lignes[] = [
        'reference' => $article->reference ?: ('ART-' . str_pad((string) $article->id, 3, '0', STR_PAD_LEFT)),
        'description' => $article->nom,
        'pu_ht' => (float) $article->pivot->prix_unitaire,
        'quantite' => (float) $article->pivot->quantite,
        'montant_ht' => (float) ($article->pivot->sous_total ?? ($article->pivot->prix_unitaire * $article->pivot->quantite)),
    ];
}
foreach ($vente->prestations as $index => $prestation) {
    $lignes[] = [
        'reference' => 'PREST-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
        'description' => $prestation->nom_prestation,
        'pu_ht' => (float) $prestation->prix_unitaire,
        'quantite' => (float) $prestation->quantite,
        'montant_ht' => (float) $prestation->montant_total,
    ];
}

$montantEnLettres = ucfirst(venteNumberToWords($totalTtc)) . ' Francs CFA TTC';

$echeanceLibelle = '—';
if (filled($delaiPaiementRaw)) {
    if (preg_match('/(\d+)/', (string) $delaiPaiementRaw)) {
        $echeanceLibelle = str_contains(strtolower((string) $delaiPaiementRaw), 'jour')
            ? 'Paiement sous ' . trim((string) $delaiPaiementRaw)
            : 'Paiement sous ' . trim((string) $delaiPaiementRaw) . ' jours';
    } else {
        $echeanceLibelle = 'Paiement : ' . trim((string) $delaiPaiementRaw);
    }
}
$reglement = $client?->mode_paiement ?? '—';
$delaiLivraison = '—';
if (filled($vente->devis?->commentaire)) {
    $delaiLivraison = trim((string) $vente->devis->commentaire);
} elseif (filled($vente->commentaire) && ! preg_match('/^A\s*livrer\s*[àa]\s*:/iu', trim((string) $vente->commentaire))) {
    $delaiLivraison = trim((string) $vente->commentaire);
}
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Proforma {{ $numeroProforma }}</title>
    <style>
        @page { margin: 10mm 12mm; }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .bordered td, .bordered th {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
        }
        .brand-logo { display: inline-block; border: 0; vertical-align: middle; }
        .doc-title {
            font-size: 24px;
            font-weight: bold;
            line-height: 1.15;
        }
        .doc-numero {
            font-size: 11.5px;
            font-weight: bold;
            margin-top: 2px;
        }
        .info-box td {
            font-size: 10.5px;
            line-height: 1.55;
            padding: 5px 7px;
        }
        .info-client-line { margin: 0; padding: 0; }
        .addr-indent { padding-left: 14px; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .italic { font-style: italic; }
        .items-table {
            border-collapse: collapse;
            width: 100%;
            margin: 0;
        }
        .items-table thead th {
            background: #c8c8c8;
            border: 1px solid #000;
            border-bottom: 3px double #000;
            padding: 6px 8px;
            font-weight: bold;
            font-size: 10.5px;
            text-align: center;
            vertical-align: middle;
        }
        .items-table thead th.col-left { text-align: left; }
        .items-table tbody td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 7px 8px;
            font-size: 10.5px;
            vertical-align: top;
        }
        .items-table tbody tr.items-spacer td {
            height: 260px;
            padding: 0;
            border-bottom: 1px solid #000;
        }
        .col-num { text-align: right; }
        .invoice-bottom-wrap {
            border-collapse: collapse;
            width: 100%;
            margin: 0;
        }
        .invoice-bottom-wrap > tbody > tr > td {
            padding: 0;
            margin: 0;
            vertical-align: top;
        }
        .footer-section {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .footer-section td {
            vertical-align: top;
            padding: 5px 7px;
        }
        .footer-terms {
            font-size: 10.5px;
            line-height: 1.7;
            padding-right: 12px;
        }
        .footer-totals-wrap { text-align: right; padding-top: 0; vertical-align: top; }
        .footer-totals-block {
            width: 280px;
            margin-left: auto;
            text-align: left;
        }
        .footer-totals {
            border-collapse: collapse;
            font-size: 10.5px;
        }
        .footer-totals td {
            border: 1px solid #000;
            padding: 4px 7px;
            font-size: 10.5px;
        }
        .footer-totals-top {
            width: 185px;
            margin-left: 95px;
            margin-bottom: 5px;
        }
        .footer-totals-bottom {
            width: 280px;
        }
        .footer-totals .total-fcfa {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            width: 95px;
        }
        .footer-closing {
            margin-top: 10px;
            font-size: 10.5px;
            line-height: 1.6;
            text-align: left;
        }
        .footer-closing-amount {
            font-style: italic;
            padding-left: 12px;
        }
        .footer-signature {
            text-align: right;
            padding-top: 18px;
            padding-bottom: 4px;
            font-size: 11.5px;
        }
        .email-link { color: #000; text-decoration: underline; }
    </style>
</head>
<body>

@php
    $companyNom = strtoupper($company['nom'] ?? $pdfBranding['nom_entreprise']);
@endphp

{{-- Ligne 1 : logo | FACTURE PROFORMA --}}
<table cellspacing="0" cellpadding="0" style="margin-bottom: 6px;">
    <tr>
        <td width="60%" valign="middle">
            @include('partials.pdf-logo', ['pdfBranding' => $pdfBranding ?? [], 'logoClass' => 'brand-logo', 'maxWidth' => 210, 'maxHeight' => 100])
        </td>
        <td width="40%" valign="top" align="right" style="text-align: right;">
            <div class="doc-title">FACTURE PROFORMA</div>
            <div class="doc-numero">N° {{ $numeroProforma }}</div>
        </td>
    </tr>
</table>

{{-- Bloc expéditeur / client (encadré 60/40) --}}
<table class="bordered info-box" cellspacing="0" cellpadding="0" style="margin-bottom: 8px;">
    <tr>
        <td width="60%" valign="top" style="border-right: 1px solid #000;">
            <b>{{ $companyNom }}</b><br>
            @if(filled($company['cc'] ?? $cg?->cc))
                C.C N° {{ $company['cc'] ?? $cg->cc }}<br>
            @endif
            {{ $company['localisation'] ?? $cg?->localisation ?? '—' }}<br>
            @if(filled($company['tel1'] ?? $cg?->tel1))
                Tel.: {{ $company['tel1'] ?? $cg->tel1 }}
                @if(filled($company['tel2'] ?? $cg?->tel2))
                    / {{ $company['tel2'] ?? $cg->tel2 }}
                @endif
                <br>
            @endif
            {{ $company['adresse_postale'] ?? $cg?->adresse_postale ?? '—' }}<br>
            @if(filled($company['email'] ?? $cg?->email))
                <span class="email-link">{{ $company['email'] ?? $cg->email }}</span>
            @endif
        </td>
        <td width="40%" valign="top">
            <div class="info-client-line"><b>Facturé à :</b> <b>{{ strtoupper($nomClient) }}</b></div>
            <div class="info-client-line">
                <b>Adresse :</b>
                @if(filled($adresseBp))
                    {{ $adresseBp }}
                @elseif(!filled($adresseRue))
                    —
                @endif
            </div>
            @if(filled($adresseRue))
                <div class="info-client-line addr-indent"><span class="italic">{{ $adresseRue }}</span></div>
            @endif
            <div class="info-client-line"><b>Téléphone :</b> {{ $telClient }}</div>
            <div class="info-client-line"><b>A livrer à :</b> <b>{{ strtoupper($aLivrerA) }}</b></div>
            <div class="info-client-line"><b>Date :</b> {{ $dateFacture }}</div>
            <div class="info-client-line"><b>Echéance :</b> {{ $dateEcheance }}</div>
        </td>
    </tr>
</table>

{{-- Tableau articles + pied (sans espace) --}}
<table class="invoice-bottom-wrap" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <table class="items-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th width="12%" class="col-left">Référence</th>
                        <th width="48%" class="col-left">Description</th>
                        <th width="14%">PU HT</th>
                        <th width="12%">Quantité</th>
                        <th width="14%">Montant HT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lignes as $ligne)
                        <tr>
                            <td class="text-left">{{ $ligne['reference'] }}</td>
                            <td class="text-left">{{ $ligne['description'] }}</td>
                            <td class="col-num">{{ $fmt($ligne['pu_ht']) }}</td>
                            <td class="col-num">{{ $fmt($ligne['quantite'], 1) }}</td>
                            <td class="col-num">{{ $fmt($ligne['montant_ht']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="items-spacer">
                        <td>&nbsp;</td><td></td><td></td><td></td><td></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table class="footer-section" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="58%" class="footer-terms">
                        <b>Échéance :</b> {{ $echeanceLibelle }}<br>
                        <b>Eloignement :</b> {{ $eloignementPct }}%<br>
                        <b>Montant HT + Eloignement :</b><br>
                        <b>Règlement :</b> {{ $reglement }}<br>
                        <b>Delai de livraison :</b> {{ $delaiLivraison }}
                    </td>
                    <td width="42%" class="footer-totals-wrap">
                        <div class="footer-totals-block">
                            <table class="footer-totals footer-totals-top" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width: 50%;">FCFA HT</td>
                                    <td class="text-right" style="width: 50%;">{{ $fmt($totalHt) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $eloignementPct }}%</td>
                                    <td class="text-right">{{ $fmt($montantEloignement) }}</td>
                                </tr>
                                <tr>
                                    <td>FCFA HT</td>
                                    <td class="text-right">{{ $fmt($totalHtAvecEloignement) }}</td>
                                </tr>
                            </table>
                            <table class="footer-totals footer-totals-bottom" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td rowspan="2" class="total-fcfa">TOTAL FCFA</td>
                                    <td style="width: 92px;">TVA 18%</td>
                                    <td class="text-right" style="width: 93px;">{{ $fmt($tva) }}</td>
                                </tr>
                                <tr>
                                    <td class="bold">TTC</td>
                                    <td class="text-right bold">{{ $fmt($totalTtc) }}</td>
                                </tr>
                            </table>
                            <div class="footer-closing">
                                <b><u><i>Arrêter la présente facture proforma à la somme de :</i></u></b><br>
                                <span class="footer-closing-amount">{{ $montantEnLettres }}</span>
                            </div>
                            <div class="footer-signature">
                                <u>Le Gérant</u>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
