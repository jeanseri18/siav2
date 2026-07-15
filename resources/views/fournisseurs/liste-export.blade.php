<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Liste des fournisseurs' }}</title>
    <style>
        body {
            font-family: {{ !empty($printMode) ? 'Arial, Helvetica, sans-serif' : 'DejaVu Sans, sans-serif' }};
            font-size: 7px;
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
    $editePar = auth()->user()
        ? \Illuminate\Support\Str::before(auth()->user()->email, '@')
        : '—';
    $buNom = $pdfBranding['bu']->nom ?? '—';
    $afficher = function ($valeur, string $defaut = 'Non renseigné') {
        return filled($valeur) ? $valeur : $defaut;
    };
    $afficherF = function ($valeur, string $defaut = 'Non renseignée') {
        return filled($valeur) ? $valeur : $defaut;
    };
    $nomRaisonSociale = function ($fournisseur) {
        return trim(($fournisseur->nom_raison_sociale ?? '').' '.($fournisseur->prenoms ?? '')) ?: '—';
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

@if(!empty($printMode))
<div class="toolbar no-print">
    <button type="button" onclick="window.print()">Imprimer</button>
    <a href="{{ route('fournisseurs.index') }}">Retour à la liste</a>
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
            <h1>{{ $documentTitle ?? 'Liste des fournisseurs' }}</h1>
            @if($fournisseurs->count())
            <div class="meta">{{ $fournisseurs->count() }} fournisseur(s)</div>
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
        @forelse($fournisseurs as $fournisseur)
        <tr>
            <td class="nowrap">{{ $fournisseur->code }}</td>
            <td>{{ $nomRaisonSociale($fournisseur) }}</td>
            <td>{{ $afficher($fournisseur->categorie) }}</td>
            <td>{{ $afficher($fournisseur->secteur_activite) }}</td>
            <td class="text-center">{{ $formatDelai($fournisseur->delai_paiement) }}</td>
            <td class="text-center">{{ $afficher($fournisseur->mode_paiement) }}</td>
            <td>{{ $afficher($fournisseur->regime_imposition) }}</td>
            <td>{{ $afficher($fournisseur->n_rccm) }}</td>
            <td>{{ $afficher($fournisseur->n_cc) }}</td>
            <td>{{ $afficherF($fournisseur->adresse_localisation) }}</td>
            <td>{{ $afficherF($fournisseur->boite_postale) }}</td>
            <td>{{ $afficher($fournisseur->email) }}</td>
            <td>{{ $afficher($fournisseur->telephone) }}</td>
            <td class="text-center">{{ $fournisseur->bus->nom ?? $buNom }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="14" class="text-center">Aucun fournisseur enregistré</td>
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
