<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Liste des articles' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 5.5px;
            color: #111;
            margin: 12px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .logo {
            max-width: 90px;
            max-height: 60px;
        }
        h1 {
            font-size: 14px;
            text-align: center;
            margin: 0 0 4px;
        }
        .doc-info {
            text-align: right;
            font-size: 8px;
            line-height: 1.5;
        }
        .doc-info div {
            margin-bottom: 2px;
        }
        table.liste {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }
        table.liste thead {
            display: table-header-group;
        }
        table.liste tr {
            page-break-inside: avoid;
        }
        table.liste th,
        table.liste td {
            border: 1px solid #333;
            padding: 2px 1px;
            text-align: left;
            vertical-align: top;
        }
        table.liste th {
            background: #e8e8e8;
            font-weight: bold;
            text-align: center;
            font-size: 5px;
        }
        .text-center {
            text-align: center;
        }
        .nowrap {
            white-space: nowrap;
        }
        .footer-count {
            margin-top: 8px;
            font-size: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
@php
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $stockIndicators = $stockIndicators ?? [];
    $stockQuantities = $stockQuantities ?? [];
    $editePar = auth()->user()
        ? \Illuminate\Support\Str::before(auth()->user()->email, '@')
        : '—';
    $buNom = $pdfBranding['bu']?->nom ?? '—';
    $refFournisseur = function ($article) {
        if ($article->fournisseur) {
            return $article->fournisseur->reference_fournisseur ?? $article->reference_fournisseur ?? '—';
        }

        return $article->reference_fournisseur ?? '—';
    };
    $formatQty = function ($value) {
        $v = (float) $value;
        if ($v <= 0) {
            return '-';
        }
        if (abs($v - round($v)) < 0.000001) {
            return number_format((int) round($v), 0, ',', ' ');
        }

        return number_format($v, 2, ',', ' ');
    };
    $indicatorQty = function (int $articleId, string $key) use ($stockIndicators, $formatQty) {
        $ind = $stockIndicators[$articleId] ?? [];

        return $formatQty($ind[$key] ?? 0);
    };
    $cmpAffiche = function ($article) {
        $cmp = (float) ($article->cout_moyen_pondere ?? 0);
        if ($cmp <= 0) {
            $cmp = (float) ($article->prix_unitaire ?? 0);
        }

        return $cmp > 0 ? number_format($cmp, 0, ',', ' ') : '0';
    };
@endphp

<table class="header-table">
    <tr>
        <td style="width: 120px;">
            @if(!empty($pdfBranding['logo_absolute_path']))
                <img src="{{ $pdfBranding['logo_absolute_path'] }}" alt="Logo" class="logo">
            @endif
        </td>
        <td>
            <h1>{{ $documentTitle ?? 'Liste des articles' }}</h1>
        </td>
        <td style="width: 180px;">
            <div class="doc-info">
                <div>Date d'édition : {{ now()->format('d/m/Y') }}</div>
                <div>BU : {{ $buNom }}</div>
                <div>Edité par : {{ $editePar }}</div>
            </div>
        </td>
    </tr>
</table>

<table class="liste">
    <thead>
        <tr>
            <th>Ref.</th>
            <th>Ref. Fournisseur</th>
            <th>Désignation article</th>
            <th>Type</th>
            <th>Unité</th>
            <th>Cout moyen pondéré</th>
            <th>Qté (en stock)</th>
            <th>Demande de ravitaillement</th>
            <th>Ravitaillement en cours</th>
            <th>Retour de ravitaillement</th>
            <th>Approvisionnement en cours</th>
            <th>Retour appro</th>
            <th>Transfert de stock in</th>
            <th>Transfert de stock out</th>
        </tr>
    </thead>
    <tbody>
        @forelse($articles as $article)
        <tr>
            <td class="nowrap">{{ $article->reference }}</td>
            <td>{{ $refFournisseur($article) }}</td>
            <td>{{ $article->nom }}</td>
            <td>{{ $article->type ?: '—' }}</td>
            <td class="text-center">{{ $article->uniteMesure?->ref ?? '—' }}</td>
            <td class="text-center">{{ $cmpAffiche($article) }}</td>
            <td class="text-center">{{ (int) round($stockQuantities[$article->id] ?? 0) }}</td>
            <td class="text-center">{{ $indicatorQty($article->id, 'demande_ravitaillement') }}</td>
            <td class="text-center">{{ $indicatorQty($article->id, 'ravitaillement_en_cours') }}</td>
            <td class="text-center">{{ $indicatorQty($article->id, 'retour_ravitaillement') }}</td>
            <td class="text-center">{{ $indicatorQty($article->id, 'approvisionnement_en_cours') }}</td>
            <td class="text-center">{{ $indicatorQty($article->id, 'retour_appro') }}</td>
            <td class="text-center">{{ $indicatorQty($article->id, 'transfert_in') }}</td>
            <td class="text-center">{{ $indicatorQty($article->id, 'transfert_out') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="14" class="text-center">Aucun article enregistré</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer-count">
    Nombre de ligne : {{ str_pad((string) $articles->count(), 2, '0', STR_PAD_LEFT) }}
</div>
</body>
</html>
