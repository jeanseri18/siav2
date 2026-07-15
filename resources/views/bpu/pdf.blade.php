<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Bordereau des prix unitaires' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 7px;
            color: #111;
            margin: 12px;
        }
        h1 {
            font-size: 14px;
            text-align: center;
            margin: 0 0 14px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 14px;
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        th, td {
            border: 1px solid #333;
            padding: 3px 2px;
            text-align: center;
        }
        th {
            background: #e8e8e8;
            font-weight: bold;
        }
        .text-start {
            text-align: left;
        }
        .cat {
            background: #5eb3f6;
            font-weight: bold;
            font-size: 8px;
        }
        .sc {
            background: #1f384c;
            color: #fff;
            font-weight: bold;
            font-size: 8px;
        }
        .rub {
            background: #3a6b8c;
            color: #fff;
            font-weight: bold;
            font-size: 7px;
        }
    </style>
</head>
<body>
    <h1>{{ $documentTitle ?? 'Bordereau des prix unitaires' }}</h1>

    @foreach ($categories as $categorie)
        <table>
            <tr class="cat">
                <td colspan="16" class="text-start">{{ $categorie->nom }}</td>
            </tr>
            @foreach ($categorie->sousCategories as $sousCategorie)
                <tr class="sc">
                    <td colspan="16" class="text-start">{{ $sousCategorie->nom }}</td>
                </tr>
                @foreach ($sousCategorie->rubriques as $rubrique)
                    <tr class="rub">
                        <td colspan="16" class="text-start">{{ $rubrique->nom }}</td>
                    </tr>
                    <tr>
                        <th>Code</th>
                        <th>Désignation</th>
                        <th>Unité</th>
                        <th>Matériaux</th>
                        <th>T.MO</th>
                        <th>M.O.</th>
                        <th>T.MAT</th>
                        <th>Matériel</th>
                        <th>DS</th>
                        <th>T.FC</th>
                        <th>FC</th>
                        <th>T.FG</th>
                        <th>FG</th>
                        <th>T.BEN</th>
                        <th>Bén.</th>
                        <th>PU HT</th>
                    </tr>
                    @foreach ($rubrique->bpus as $bpu)
                        <tr>
                            <td>{{ $categorie->id }}.{{ $sousCategorie->id }}.{{ $rubrique->id }}.{{ $bpu->id }}</td>
                            <td class="text-start">{{ $bpu->designation }}</td>
                            <td>{{ $bpu->unite }}</td>
                            <td>{{ number_format((float) $bpu->materiaux, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->taux_mo, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->main_oeuvre, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->taux_mat, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->materiel, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->debourse_sec, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->taux_fc, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->frais_chantier, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->taux_fg, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->frais_general, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->taux_benefice, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->marge_nette, 2, ',', ' ') }}</td>
                            <td>{{ number_format((float) $bpu->pu_ht, 2, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </table>
    @endforeach
</body>
</html>
