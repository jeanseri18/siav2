<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis {{ $devi->ref_devis ?? '#' . $devi->id }} - {{ $pdfBranding['nom_entreprise'] ?? 'Entreprise' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            margin: 40px;
            font-size: 12px;
            color: #000;
        }
        /* DomPDF gère mal display:flex — mise en page en tableaux uniquement */
        .currency-banner {
            margin-bottom: 16px;
            padding: 10px 12px;
            border: 1px solid #333;
            background-color: #f9f9f9;
            font-size: 11px;
        }
        .logo {
            display: inline-block;
            border: 0;
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
        table.items thead {
            display: table-header-group;
        }
        table.items tbody {
            display: table-row-group;
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
@php
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $cg = $configGlobal ?? $pdfBranding['config'] ?? null;
    $company = $pdfBranding['company'] ?? [];
    $nomClient = $devi->client
        ? ($devi->client->nom ?? $devi->client->nom_raison_sociale ?? '—')
        : '—';
    $tauxTvaDevis = (float) $devi->total_ht > 0
        ? round(((float) $devi->tva / (float) $devi->total_ht) * 100, 2)
        : 18;
    $delaiValiditeJours = 30;
    if ($devi->client && filled($devi->client->delai_paiement) && preg_match('/(\d+)/', (string) $devi->client->delai_paiement, $matchDelai)) {
        $delaiValiditeJours = max(1, (int) $matchDelai[1]);
    }
    $dateValidite = $devi->created_at->copy()->addDays($delaiValiditeJours);
    $logoSrc = $pdfBranding['logo_src'] ?? null;
@endphp

    {{-- En-tête : tableau (évite flex / DomPDF) --}}
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 28px;">
        <tr>
            <td width="55%" valign="top">
                @include('partials.pdf-logo', ['pdfBranding' => $pdfBranding ?? [], 'logoClass' => 'logo'])
                <br><strong>{{ $pdfBranding['nom_entreprise'] }}</strong>
            </td>
            <td width="45%" valign="top" align="right">
                <div class="title">Devis - {{ $devi->ref_devis ?? '#' . $devi->id }}</div>
                <div class="date">
                    Date : {{ $devi->created_at->format('d.m.Y') }}<br>
                    Date de validité : {{ $dateValidite->format('d.m.Y') }}<br>
                    <strong>Devise :</strong> FCFA (Franc CFA BCEAO)
                </div>
            </td>
        </tr>
    </table>

    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 24px;">
        <tr>
            <td width="48%" valign="top">
                <strong>{{ $pdfBranding['nom_entreprise'] }}</strong><br>
                @if(filled($company['localisation'] ?? $cg?->localisation))
                    {{ $company['localisation'] ?? $cg->localisation }}<br>
                @endif
                @if(filled($company['adresse_postale'] ?? $cg?->adresse_postale))
                    {{ $company['adresse_postale'] ?? $cg->adresse_postale }}<br>
                @endif
                @if($cg && $cg->tel1)
                    {{ $cg->tel1 }}<br>
                @endif
                @if($cg && $cg->email)
                    {{ $cg->email }}<br>
                @endif
                @if($cg && $cg->rccm)
                    RCCM: {{ $cg->rccm }}<br>
                @endif
                @if($cg && $cg->cc)
                    N° CC : {{ $cg->cc }}<br>
                @endif
            </td>
            <td width="4%"></td>
            <td width="48%" valign="top" align="right">
                <strong>{{ $nomClient }}</strong><br>
                @if($devi->client)
                    @if(filled($devi->client->adresse_localisation))
                        {{ $devi->client->adresse_localisation }}<br>
                    @endif
                    @if(filled($devi->client->boite_postale))
                        {{ $devi->client->boite_postale }}<br>
                    @endif
                @endif
            </td>
        </tr>
    </table>

    @if($cg && ($cg->rccm || $cg->cc))
        <div class="rccm-section">
            @if($cg->rccm)
                RCCM: {{ $cg->rccm }}<br>
            @endif
            @if($cg->cc)
                N° CC : {{ $cg->cc }}
            @endif
        </div>
    @endif

    <div class="currency-banner">
        <strong>Montants exprimés en francs CFA (FCFA).</strong> Une unité correspond à un franc CFA. TVA au taux de {{ number_format($tauxTvaDevis, 2, ',', ' ') }}&nbsp;%.
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th>Date</th>
                <th>Qté</th>
                <th>Unité</th>
                <th class="text-right">Devise</th>
                <th class="text-right">Prix unitaire HT</th>
                <th class="text-right">TVA</th>
                <th class="text-right">Montant TTC</th>
            </tr>
        </thead>
        <tbody>
            @forelse($devi->articles as $article)
                @php
                    $quantite = (float) ($article->pivot->quantite ?? 0);
                    $puHt = (float) ($article->pivot->prix_unitaire_ht ?? 0);
                    $montantHtLigne = (float) ($article->pivot->montant_total ?? ($puHt * $quantite));
                    $montantTvaLigne = $montantHtLigne * ($tauxTvaDevis / 100);
                    $montantTtcLigne = $montantHtLigne + $montantTvaLigne;
                    $uniteRef = $article->uniteMesure ? $article->uniteMesure->ref : 'pcs';
                    $libelle = $article->nom ?? $article->reference ?? 'Article #' . $article->id;
                @endphp
                <tr>
                    <td>{{ $libelle }}</td>
                    <td>{{ $devi->created_at->format('d.m.Y') }}</td>
                    <td>{{ number_format($quantite, 2, ',', ' ') }}</td>
                    <td>{{ $uniteRef }}</td>
                    <td class="text-right">FCFA</td>
                    <td class="text-right">{{ number_format($puHt, 0, ',', ' ') }} FCFA</td>
                    <td class="text-right">{{ number_format($tauxTvaDevis, 2, ',', ' ') }} %</td>
                    <td class="text-right">{{ number_format($montantTtcLigne, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 16px;">Aucune ligne d’article sur ce devis.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="total-table">
        <tr>
            <td>Total HT <span style="font-weight:normal;">(FCFA)</span></td>
            <td class="text-right">{{ number_format((float) $devi->total_ht, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>TVA {{ number_format($tauxTvaDevis, 0, ',', ' ') }}% <span style="font-weight:normal;">(FCFA)</span></td>
            <td class="text-right">{{ number_format((float) $devi->tva, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="total-line">
            <td><strong>Total TTC</strong> <span style="font-weight:normal;">(FCFA)</span></td>
            <td class="text-right"><strong>{{ number_format((float) $devi->total_ttc, 0, ',', ' ') }} FCFA</strong></td>
        </tr>
    </table>

    <div class="footer">
        <strong>Conditions générales :</strong> Devis valable {{ $delaiValiditeJours }} jours à compter de la date d'émission.<br>
        @if($devi->client && filled($devi->client->mode_paiement))
            Paiement : {{ $devi->client->mode_paiement }}. Toute commande implique l'acceptation de nos conditions générales de vente.
        @else
            Paiement à réception de facture. Toute commande implique l'acceptation de nos conditions générales de vente.
        @endif
    </div>

</body>
</html>
