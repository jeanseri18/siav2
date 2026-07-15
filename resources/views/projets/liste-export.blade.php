<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Liste des projets' }}</title>
    <style>
        body {
            font-family: {{ !empty($printMode) ? 'Arial, Helvetica, sans-serif' : 'DejaVu Sans, sans-serif' }};
            font-size: 8px;
            color: #111;
            margin: 16px;
        }
        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
            align-items: center;
        }
        .toolbar a,
        .toolbar button {
            padding: 8px 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            color: #111;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
        }
        .toolbar button {
            background: #033d71;
            border-color: #033d71;
            color: #fff;
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
        .meta {
            text-align: center;
            font-size: 9px;
            color: #555;
            margin-bottom: 14px;
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
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 8px;
            }
        }
    </style>
</head>
<body>
@php
    $pdfBranding = $pdfBranding ?? \App\Support\PdfBranding::forBu(null);
    $formatDate = function ($date) {
        if (blank($date)) {
            return '—';
        }
        try {
            return Carbon\Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable) {
            return (string) $date;
        }
    };
    $nomClient = function ($projet) {
        if ($projet->clientFournisseur) {
            return trim(($projet->clientFournisseur->nom_raison_sociale ?? '').' '.($projet->clientFournisseur->prenoms ?? ''));
        }

        return $projet->client ?: '—';
    };
    $nomEmploye = function ($user) {
        if (! $user) {
            return 'Non assigné';
        }

        return trim(($user->prenom ?? '').' '.($user->nom ?? ''));
    };
    $editePar = auth()->user()
        ? \Illuminate\Support\Str::before(auth()->user()->email, '@')
        : '—';
    $buNom = $pdfBranding['bu']->nom ?? '—';
@endphp

@if(!empty($printMode))
<div class="toolbar no-print">
    <button type="button" onclick="window.print()">Imprimer</button>
    <a href="{{ route('projets.index') }}">Retour à la liste</a>
</div>
@endif

<table class="header-table">
    <tr>
        <td style="width: 120px;">
            @if(!empty($pdfBranding['logo_absolute_path']))
                <img src="{{ $pdfBranding['logo_absolute_path'] }}" alt="Logo" class="logo">
            @endif
        </td>
        <td>
            <h1>{{ $documentTitle ?? 'Liste des projets' }}</h1>
            @if($projets->count())
            <div class="meta">{{ $projets->count() }} projet(s)</div>
            @endif
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
            <th>B.U</th>
            <th>Nom du projet</th>
            <th>Client</th>
            <th>Date création</th>
            <th>Date début</th>
            <th>Date de fin</th>
            <th>Secteur d'activité</th>
            <th>Chef de projet</th>
            <th>Conducteur de travaux</th>
            <th>TVA</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projets as $projet)
        <tr>
            <td class="nowrap">{{ $projet->ref_projet }}</td>
            <td>{{ $projet->bu->nom ?? '—' }}</td>
            <td>{{ $projet->nom_projet }}</td>
            <td>{{ $nomClient($projet) }}</td>
            <td class="text-center nowrap">{{ $formatDate($projet->date_creation) }}</td>
            <td class="text-center nowrap">{{ $formatDate($projet->date_debut) }}</td>
            <td class="text-center nowrap">{{ $projet->date_fin ? $formatDate($projet->date_fin) : 'Non spécifiée' }}</td>
            <td>{{ $projet->secteurActivite->nom ?? 'Secteur non défini' }}</td>
            <td>{{ $nomEmploye($projet->chefProjet) }}</td>
            <td>{{ $nomEmploye($projet->conducteurTravaux) }}</td>
            <td class="text-center">{{ $projet->hastva ? 'Oui' : 'Non' }}</td>
            <td class="text-center">{{ ucfirst($projet->statut ?? '—') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="12" class="text-center">Aucun projet enregistré</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if(!empty($printMode))
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
@endif
</body>
</html>
