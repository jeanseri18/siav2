<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Liste des employés' }}</title>
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
    $roleLabels = $roleLabels ?? [];
    $editePar = auth()->user()
        ? \Illuminate\Support\Str::before(auth()->user()->email, '@')
        : '—';
    $buNom = $pdfBranding['bu']->nom ?? '—';
    $afficher = function ($valeur, string $defaut = 'Non renseigné') {
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
    $civilite = function ($sexe) {
        return match ($sexe) {
            'F' => 'Madame',
            'M' => 'Monsieur',
            default => 'Non renseigné',
        };
    };
    $sexeLibelle = function ($sexe) {
        return match ($sexe) {
            'F' => 'Féminin',
            'M' => 'Masculin',
            default => 'Non renseigné',
        };
    };
    $roleLibelle = function ($role) use ($roleLabels) {
        if (blank($role)) {
            return 'Non renseigné';
        }

        return $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', (string) $role));
    };
@endphp

@if(!empty($printMode))
<div class="toolbar no-print">
    <button type="button" onclick="window.print()">Imprimer</button>
    <a href="{{ route('employes.index') }}">Retour à la liste</a>
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
            <h1>{{ $documentTitle ?? 'Liste des employés' }}</h1>
            @if($employes->count())
            <div class="meta">{{ $employes->count() }} employé(s)</div>
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
            <th>N° Employé</th>
            <th>Civilité</th>
            <th>Nom</th>
            <th>Prénoms</th>
            <th>Date de naissance</th>
            <th>Nationalité</th>
            <th>Email</th>
            <th>Situation matrimoniale</th>
            <th>Sexe</th>
            <th>Fonction</th>
            <th>Rôle</th>
            <th>Date d'embauche</th>
            <th>N° Tel</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @forelse($employes as $employe)
        <tr>
            <td class="text-center nowrap">{{ str_pad((string) $employe->id, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="text-center">{{ $civilite($employe->sexe) }}</td>
            <td>{{ $afficher($employe->nom) }}</td>
            <td>{{ $afficher($employe->prenom) }}</td>
            <td class="text-center nowrap">{{ $formatDate($employe->date_naissance) }}</td>
            <td>{{ $afficher($employe->nationalite) }}</td>
            <td>{{ $afficher($employe->email) }}</td>
            <td>{{ $afficher($employe->situation_matrimoniale ? ucfirst($employe->situation_matrimoniale) : null) }}</td>
            <td class="text-center">{{ $sexeLibelle($employe->sexe) }}</td>
            <td>{{ $afficher($employe->poste) }}</td>
            <td>{{ $roleLibelle($employe->role) }}</td>
            <td class="text-center nowrap">{{ $formatDate($employe->date_embauche) }}</td>
            <td>{{ $afficher($employe->telephone) }}</td>
            <td class="text-center">{{ ucfirst($afficher($employe->status)) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="14" class="text-center">Aucun employé enregistré</td>
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
