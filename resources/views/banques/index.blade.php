@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Liste des Banques</h1>
        <a href="{{ route('banques.create') }}" class="btn btn-primary mb-3">Ajouter une banque</a>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($banques as $banque)
                    <tr>
                        <td>{{ $banque->nom }}</td>
                        <td>
                            <a href="{{ route('banques.edit', $banque) }}" class="btn btn-warning">Modifier</a>
                            <form action="{{ route('banques.destroy', $banque) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer cette banque ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
