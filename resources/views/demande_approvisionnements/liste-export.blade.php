<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Liste des demandes d\'approvisionnement' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
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
            padding: 4px 3px;
            text-align: left;
            vertical-align: top;
        }
        table.liste th {
            background: #e8e8e8;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
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
    $afficher = function ($valeur, string $defaut = '—') {
        return filled($valeur) ? $valeur : $defaut;
    };
    $formatDate = function ($date) use ($afficher) {
        if (blank($date)) {
            return $afficher(null);
        }

        try {
            return \Carbon\Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable) {
            return (string) $date;
        }
    };
    $formatDateHeure = function ($date) use ($afficher) {
        if (blank($date)) {
            return $afficher(null);
        }

        try {
            return \Carbon\Carbon::parse($date)->format('d/m/Y H:i');
        } catch (\Throwable) {
            return (string) $date;
        }
    };
    $nomUtilisateur = function ($user) use ($afficher) {
        if (! $user) {
            return $afficher(null);
        }

        $nom = trim(($user->prenom ?? '').' '.($user->nom ?? ''));

        return $nom !== '' ? $nom : $afficher($user->email);
    };
    $dateApprobRej = function ($demande) use ($formatDateHeure, $afficher) {
        if (! in_array($demande->statut, ['approuvée', 'rejetée'], true)) {
            return $afficher(null);
        }

        return $formatDateHeure($demande->updated_at);
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
            <h1>{{ $documentTitle ?? 'Liste des demandes d\'approvisionnement' }}</h1>
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
            <th>Date</th>
            <th>Projet</th>
            <th>Demandeur</th>
            <th>Nb Articles</th>
            <th>Statut</th>
            <th>Approuvé/Rejeté par</th>
            <th>Date approb/Rej</th>
        </tr>
    </thead>
    <tbody>
        @forelse($demandes as $demande)
        <tr>
            <td class="nowrap">{{ $demande->reference }}</td>
            <td class="text-center nowrap">{{ $formatDate($demande->date_demande) }}</td>
            <td>{{ $demande->projet?->nom_projet ?? 'N/A' }}</td>
            <td>{{ $nomUtilisateur($demande->user) }}</td>
            <td class="text-center">{{ $demande->lignes_count ?? $demande->lignes->count() }}</td>
            <td class="text-center">{{ ucfirst($demande->statut ?? '—') }}</td>
            <td>{{ $nomUtilisateur($demande->approbateur) }}</td>
            <td class="text-center nowrap">{{ $dateApprobRej($demande) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Aucune demande d'approvisionnement enregistrée</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer-count">
    Nombre de ligne : {{ str_pad((string) $demandes->count(), 2, '0', STR_PAD_LEFT) }}
</div>
</body>
</html>
