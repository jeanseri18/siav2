@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Débogage - Suivi des tâches</h2>
    
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            Informations de session
        </div>
        <div class="card-body">
            <p><strong>Contrat ID:</strong> {{ session('contrat_id') ?? 'Non défini' }}</p>
            <p><strong>Nom du contrat:</strong> {{ session('contrat_nom') ?? 'Non défini' }}</p>
            <p><strong>Référence:</strong> {{ session('ref_contrat') ?? 'Non défini' }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-success text-white">
            Lots enregistrés ({{ \App\Models\Lot::where('id_contrat', session('contrat_id'))->count() }})
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>ID Contrat</th>
                        <th>Date création</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Lot::where('id_contrat', session('contrat_id'))->get() as $lot)
                    <tr>
                        <td>{{ $lot->id }}</td>
                        <td>{{ $lot->titre }}</td>
                        <td>{{ $lot->id_contrat }}</td>
                        <td>{{ $lot->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-warning text-dark">
            Niveaux enregistrés ({{ \App\Models\Niveau::where('id_contrat', session('contrat_id'))->count() }})
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>ID Lot</th>
                        <th>ID Contrat</th>
                        <th>Date création</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Niveau::where('id_contrat', session('contrat_id'))->get() as $niveau)
                    <tr>
                        <td>{{ $niveau->id }}</td>
                        <td>{{ $niveau->titre_niveau }}</td>
                        <td>{{ $niveau->id_lot }}</td>
                        <td>{{ $niveau->id_contrat }}</td>
                        <td>{{ $niveau->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            Localisations enregistrées ({{ \App\Models\Localisation::where('id_contrat', session('contrat_id'))->count() }})
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>ID Niveau</th>
                        <th>ID Contrat</th>
                        <th>Date création</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Localisation::where('id_contrat', session('contrat_id'))->get() as $localisation)
                    <tr>
                        <td>{{ $localisation->id }}</td>
                        <td>{{ $localisation->titre_localisation }}</td>
                        <td>{{ $localisation->id_niveau }}</td>
                        <td>{{ $localisation->id_contrat }}</td>
                        <td>{{ $localisation->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('taches.index') }}" class="btn btn-secondary">Retour au suivi des tâches</a>
</div>
@endsection
