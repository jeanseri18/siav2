<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Liste des artisans' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 6.5px;
            color: #111;
            margin: 16px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 14px;
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
            font-size: 16px;
            text-align: center;
            margin: 0 0 4px;
        }
        .doc-info {
            text-align: right;
            font-size: 9px;
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
            padding: 3px 2px;
            text-align: left;
            vertical-align: top;
        }
        table.liste th {
            background: #e8e8e8;
            font-weight: bold;
            text-align: center;
            font-size: 6px;
        }
        .text-center {
            text-align: center;
        }
        .nowrap {
            white-space: nowrap;
        }
        .footer-count {
            margin-top: 10px;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
@php
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $editePar = auth()->user()
        ? \Illuminate\Support\Str::before(auth()->user()->email, '@')
        : '—';
    $buNom = $pdfBranding['bu']?->nom ?? '—';
    $afficher = function ($valeur, string $defaut = 'Non renseigné') {
        return filled($valeur) ? $valeur : $defaut;
    };
    $afficherF = function ($valeur, string $defaut = 'Non renseignée') {
        return filled($valeur) ? $valeur : $defaut;
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
            <h1>{{ $documentTitle ?? 'Liste des artisans' }}</h1>
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
            <th>Reference</th>
            <th>Civilité</th>
            <th>Nom</th>
            <th>Prénoms</th>
            <th>Type de pièce</th>
            <th>ID Pièce</th>
            <th>Corps de métier</th>
            <th>Nationalité</th>
            <th>Localisation</th>
            <th>Téléphone</th>
            <th>N° RCCM</th>
            <th>N° CC</th>
            <th>Mail</th>
            <th>PPSI</th>
        </tr>
    </thead>
    <tbody>
        @forelse($artisans as $artisan)
        <tr>
            <td class="nowrap">{{ $artisan->reference }}</td>
            <td class="text-center">{{ $afficher($artisan->civilite) }}</td>
            <td>{{ $afficher($artisan->nom) }}</td>
            <td>{{ $afficherF($artisan->prenoms) }}</td>
            <td class="text-center">{{ $afficher($artisan->type_piece) }}</td>
            <td class="nowrap">{{ $afficher($artisan->numero_piece) }}</td>
            <td>{{ $afficher($artisan->fonction) }}</td>
            <td>{{ $afficher($artisan->nationalite) }}</td>
            <td>{{ $afficherF($artisan->localisation) }}</td>
            <td class="nowrap">{{ $afficher($artisan->tel1) }}</td>
            <td>{{ $afficher($artisan->rccm) }}</td>
            <td>{{ $afficher($artisan->rcc) }}</td>
            <td>{{ $afficher($artisan->mail) }}</td>
            <td class="text-center">{{ $artisan->ppsi ? 'Oui' : 'Non' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="14" class="text-center">Aucun artisan enregistré</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer-count">
    Nombre de lignes : {{ str_pad((string) $artisans->count(), 2, '0', STR_PAD_LEFT) }}
</div>
</body>
</html>
