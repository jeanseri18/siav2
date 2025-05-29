@extends('layouts.app')

@section('content')
<br>
<br>
    <h1>Liste des Ventes</h1>
    <a href="{{ route('ventes.create') }}" class="btn btn-primary">Nouvelle Vente</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventes as $vente)
                <tr>
                    <td>{{ $vente->id }}</td>
                    <td>{{ $vente->client->nom_raison_sociale }}</td>
                    <td>{{ number_format($vente->total, 2) }} €</td>
                    <td>{{ $vente->statut }}</td>
                    <td>
                        <a href="{{ route('ventes.show', $vente) }}" class="btn btn-info">Détails</a>
                        <form action="{{ route('ventes.destroy', $vente) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
