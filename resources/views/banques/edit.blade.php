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
            <div class="mb-3">
                <label for="code_banque" class="form-label">Code Banque</label>
                <input type="text" name="code_banque" class="form-control" value="{{ $banque->code_banque }}">
            </div>
            <div class="mb-3">
                <label for="code_guichet" class="form-label">Code Guichet</label>
                <input type="text" name="code_guichet" class="form-control" value="{{ $banque->code_guichet }}">
            </div>
            <div class="mb-3">
                <label for="numero_compte" class="form-label">Numéro de Compte</label>
                <input type="text" name="numero_compte" class="form-control" value="{{ $banque->numero_compte }}">
            </div>
            <div class="mb-3">
                <label for="cle_rib" class="form-label">Clé RIB</label>
                <input type="text" name="cle_rib" class="form-control" value="{{ $banque->cle_rib }}">
            </div>
            <div class="mb-3">
                <label for="iban" class="form-label">IBAN</label>
                <input type="text" name="iban" class="form-control" value="{{ $banque->iban }}">
            </div>
            <div class="mb-3">
                <label for="code_swift" class="form-label">Code SWIFT</label>
                <input type="text" name="code_swift" class="form-control" value="{{ $banque->code_swift }}">
            </div>
            <div class="mb-3">
                <label for="domiciliation" class="form-label">Domiciliation</label>
                <input type="text" name="domiciliation" class="form-control" value="{{ $banque->domiciliation }}">
            </div>
            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="text" name="telephone" class="form-control" value="{{ $banque->telephone }}">
            </div>
            <button type="submit" class="btn btn-success">Mettre à jour</button>
        </form>
    </div>
@endsection
