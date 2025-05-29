@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ajouter une Unité de Mesure</h1>
    <a href="{{ route('unite-mesures.index') }}" class="btn btn-secondary mb-3">Retour</a>

    <form action="{{ route('unite-mesures.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nom de l'Unité</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ref</label>
            <input type="text" name="ref" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>
@endsection
