@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Modifier la Banque</h1>
        <form action="{{ route('banques.update', $banque) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="{{ $banque->nom }}" required>
            </div>
            <button type="submit" class="btn btn-success">Mettre à jour</button>
        </form>
    </div>
@endsection
