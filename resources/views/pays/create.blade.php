@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Ajouter un Pays</h1>
    <a href="{{ route('pays.index') }}" class="btn btn-secondary mb-3">Retour</a>

    <form action="{{ route('pays.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nom du Pays</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>
@endsection
