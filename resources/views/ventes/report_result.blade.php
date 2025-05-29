@extends('layouts.app')

@section('content')
<div class="container">
    @include('sublayouts.vente')
    <br>
    <h1>Résultats du Rapport des Ventes</h1>

    <!-- Bouton d'exportation PDF 
    <a href="{{ route('ventes.report.pdf', ['ventes' => $ventes]) }}" class="btn btn-success mb-3">Exporter en PDF</a>
    -->
    @if($ventes->isEmpty())
        <p>Aucune vente trouvée pour les critères sélectionnés.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Vente</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Articles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventes as $vente)
                    <tr>
                        <td>{{ $vente->id }}</td>
                        <td>{{ $vente->client->prenoms }}</td>
                        <td>{{ $vente->created_at->format('d-m-Y') }}</td>
                        <td>{{ $vente->total }} FCFA</td>
                        <td>
                            <ul>
                                @foreach($vente->articles as $article)
                                    <li>{{ $article->nom }} ({{ $article->pivot->quantite }})</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
