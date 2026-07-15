<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Liste des clients' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
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
            font-size: 6.5px;
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
    $nomRaisonSociale = function ($client) {
        return trim(($client->nom_raison_sociale ?? '').' '.($client->prenoms ?? '')) ?: '—';
    };
    $formatDelai = function ($delai) use ($afficher) {
        if (blank($delai)) {
            return $afficher(null);
        }
        if (strtoupper((string) $delai) === 'CASH') {
            return 'CASH jours';
        }

        return $delai.' jours';
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
            <h1>{{ $documentTitle ?? 'Liste des clients' }}</h1>
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
            <th>Référence</th>
            <th>Nom / Raison sociale</th>
            <th>Type</th>
            <th>Secteur d'activité</th>
            <th>Délai paiement</th>
            <th>Mode paiement</th>
            <th>Régime</th>
            <th>N° RCCM</th>
            <th>N° CC</th>
            <th>Adresse</th>
            <th>B.P</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>B.U</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
        <tr>
            <td class="nowrap">{{ $client->code }}</td>
            <td>{{ $nomRaisonSociale($client) }}</td>
            <td>{{ $afficher($client->categorie) }}</td>
            <td>{{ $afficher($client->secteur_activite) }}</td>
            <td class="text-center">{{ $formatDelai($client->delai_paiement) }}</td>
            <td class="text-center">{{ $afficher($client->mode_paiement) }}</td>
            <td>{{ $afficher($client->regime_imposition) }}</td>
            <td>{{ $afficher($client->n_rccm) }}</td>
            <td>{{ $afficher($client->n_cc) }}</td>
            <td>{{ $afficherF($client->adresse_localisation) }}</td>
            <td>{{ $afficherF($client->boite_postale) }}</td>
            <td>{{ $afficher($client->email) }}</td>
            <td>{{ $afficher($client->telephone) }}</td>
            <td class="text-center">{{ $client->bus?->nom ?? $buNom }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="14" class="text-center">Aucun client enregistré</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer-count">
    Nombre de lignes : {{ str_pad((string) $clients->count(), 2, '0', STR_PAD_LEFT) }}
</div>
</body>
</html>
