@extends('layouts.app')

@section('content')

<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid black;
        padding: 10px;
    }
    tr {
        border-bottom: 1px solid black;
    }
    @media print {
        .no-print {
            display: none;
        }
        body {
            font-size: 10px;
        }
    }
</style>

<div class="container-fluid">
    <div class="no-print mb-3">
        <button class="btn btn-primary" onclick="window.print()">Imprimer</button>
        <a href="{{ route('bpu.index') }}" class="btn btn-secondary">Retour</a>
    </div>

    <h2 class="mb-4 text-center">Bordereau des Prix Unitaires</h2>

    @foreach ($categories as $categorie)
        <table width="100%" class="text-center mt-4" border="1" bordercolor="black">
            <tr bgcolor="#5EB3F6" height="40px">
                <td colspan="10">
                    <h4 class="text-start text-uppercase">{{ $categorie->nom }}</h4>
                </td>
            </tr>

            @foreach ($categorie->sousCategories as $sousCategorie)
                <tr bgcolor="#1F384C" class="text-white" height="40px">
                    <td colspan="10">
                        <h5 class="text-start text-uppercase">{{ $sousCategorie->nom }}</h5>
                    </td>
                </tr>

                @foreach ($sousCategorie->rubriques as $rubrique)
                    <tr bgcolor="#3A6B8C" class="text-white" height="40px">
                        <td colspan="10">
                            <h6 class="text-start text-uppercase">{{ $rubrique->nom }}</h6>
                        </td>
                    </tr>

                    <tr>
                        <th>Code</th>
                        <th>Designation</th>
                        <th>Unité</th>
                        <th>Materiaux</th>
                        <th>Main d'oeuvre</th>
                        <th>Materiel</th>
                        <th>Deboursé sec</th>
                        <th>Frais</th>
                        <th>Prix HT</th>
                        <th>Prix TTC</th>
                    </tr>
                    
                    @foreach ($rubrique->bpus as $bpu)
                        <tr>
                            <td>{{ $categorie->id }}.{{ $sousCategorie->id }}.{{ $rubrique->id }}.{{ $bpu->id }}</td>
                            <td class="text-start">{{ $bpu->designation }}</td>
                            <td>{{ $bpu->unite }}</td>
                            <td>{{ number_format($bpu->materiaux, 0, ',', ' ') }}</td>
                            <td>{{ number_format($bpu->main_oeuvre, 0, ',', ' ') }}</td>
                            <td>{{ number_format($bpu->materiel, 0, ',', ' ') }}</td>
                            <td>{{ number_format($bpu->debourse_sec, 0, ',', ' ') }}</td>
                            <td>{{ number_format($bpu->frais_chantier + $bpu->frais_general, 0, ',', ' ') }}</td>
                            <td>{{ number_format($bpu->pu_ht, 0, ',', ' ') }}</td>
                            <td>{{ number_format($bpu->pu_ttc, 0, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </table>
        <br>
    @endforeach
</div>
@endsection