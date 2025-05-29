@extends('layouts.app')

@section('content')
<br>
<br>
    <div class="container">
        <h1>Détails de la vente #{{ $vente->id }}</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Client : {{ $vente->client->prenoms }}</h5><br>
                <p class="card-text"><strong>Total :</strong> {{ number_format($vente->total, 2) }} CFA</p>
                <p class="card-text"><strong>Statut :</strong> {{ $vente->statut }}</p>
                <p class="card-text"><strong>Date de création :</strong> {{ $vente->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <h3 class="mt-4">Articles vendus</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vente->articles as $article)
                    <tr>
                        <td>{{ $article->nom }}</td>
                        <td>{{ $article->pivot->quantite }}</td>
                        <td>{{ number_format($article->pivot->prix_unitaire, 2) }} CFA</td>
                        <td>{{ number_format($article->pivot->sous_total, 2) }} CFA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($vente->statut !== 'Payée')
                    <form action="{{ route('ventes.updateStatus', $vente->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">Valider la vente</button>
                    </form>
                @else
                    <button class="btn btn-secondary" disabled>Vente déjà validée</button>
                @endif
        <a href="{{ route('ventes.index') }}" class="btn btn-secondary">Retour</a>
    </div>
@endsection
