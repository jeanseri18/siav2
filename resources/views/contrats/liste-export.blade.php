<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Liste des contrats' }}</title>
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
    $nomClient = function ($client) use ($afficher) {
        if (! $client) {
            return $afficher(null);
        }

        $nom = trim(($client->nom_raison_sociale ?? $client->nom ?? '').' '.($client->prenoms ?? ''));

        return $nom !== '' ? $nom : $afficher(null);
    };
    $nomEmploye = function ($user) use ($afficher) {
        if (! $user) {
            return 'Non assigné';
        }

        $nom = trim(($user->prenom ?? '').' '.($user->nom ?? ''));

        return $nom !== '' ? $nom : $afficher(null);
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
            <h1>{{ $documentTitle ?? 'Liste des contrats' }}</h1>
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
            <th>Nom du contrat</th>
            <th>B.U</th>
            <th>Nom du projet</th>
            <th>Client</th>
            <th>Date création</th>
            <th>Date début</th>
            <th>Date de fin</th>
            <th>Secteur d'activité</th>
            <th>Chef chantier</th>
            <th>Type de travaux</th>
            <th>TVA</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contrats as $contrat)
        <tr>
            <td class="nowrap">{{ $contrat->ref_contrat }}</td>
            <td>{{ $contrat->nom_contrat }}</td>
            <td class="text-center">{{ $contrat->projet?->bu?->nom ?? $buNom }}</td>
            <td>{{ $contrat->projet?->nom_projet ?? $afficher($contrat->nom_projet) }}</td>
            <td>{{ $nomClient($contrat->client) }}</td>
            <td class="text-center nowrap">{{ $formatDate($contrat->created_at) }}</td>
            <td class="text-center nowrap">{{ $formatDate($contrat->date_debut) }}</td>
            <td class="text-center nowrap">{{ $contrat->date_fin ? $formatDate($contrat->date_fin) : 'Non spécifiée' }}</td>
            <td>{{ $contrat->projet?->secteurActivite?->nom ?? 'Secteur non défini' }}</td>
            <td>{{ $nomEmploye($contrat->chefChantier) }}</td>
            <td>{{ $afficher($contrat->type_travaux) }}</td>
            <td class="text-center">{{ ($contrat->tva_18 ?? true) ? 'Oui' : 'Non' }}</td>
            <td class="text-center">{{ ucfirst($contrat->statut ?? '—') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="13" class="text-center">Aucun contrat enregistré</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer-count">
    Nombre de lignes : {{ str_pad((string) $contrats->count(), 2, '0', STR_PAD_LEFT) }}
</div>
</body>
</html>
