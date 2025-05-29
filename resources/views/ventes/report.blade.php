@extends('layouts.app')

@section('content')
<div class="container">
@include('sublayouts.vente')
<br>
    <h1>Rapport des Ventes</h1>
    
    <!-- Formulaire pour les filtres -->
    <form action="{{ route('ventes.report.generate') }}" method="POST">
        @csrf
        
        <!-- Sélectionner un client -->
        <div class="mb-3">
            <label for="client_id" class="form-label">Client :</label>
            <select name="client_id" id="client_id" class="form-select">
                <option value="">Sélectionner un client</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->prenoms }}</option>
                @endforeach
            </select>
        </div>

        <!-- Sélectionner un article -->
        <div class="mb-3">
            <label for="article_id" class="form-label">Article :</label>
            <select name="article_id" id="article_id" class="form-select">
                <option value="">Sélectionner un article</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}">{{ $article->nom }}</option>
                @endforeach
            </select>
        </div>

        <!-- Sélectionner une période -->
        <div class="mb-3">
            <label for="date_debut" class="form-label">Date de début :</label>
            <input type="date" name="date_debut" id="date_debut" class="form-control">
        </div>

        <div class="mb-3">
            <label for="date_fin" class="form-label">Date de fin :</label>
            <input type="date" name="date_fin" id="date_fin" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Générer le rapport</button>
    </form>
</div>
@endsection
