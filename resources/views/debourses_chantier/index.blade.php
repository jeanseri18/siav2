@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Déboursés Chantier</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('debourses.index', $contrat->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux déboursés
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Section de génération des déboursés chantier -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-plus"></i> Générer un déboursé chantier</h5>
                </div>
                <div class="card-body">
                    @if($contrat->dqes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>DQE</th>
                                        <th>Référence</th>
                                        <th>Date de création</th>
                                        <th>Nombre de lignes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contrat->dqes as $dqe)
                                        <tr>
                                            <td>{{ $dqe->nom ?? 'DQE sans nom' }}</td>
                                            <td>{{ $dqe->reference ?? 'Sans référence' }}</td>
                                            <td>{{ $dqe->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $dqe->lignes->count() }}</td>
                                            <td>
                                                @if($dqe->lignes->count() > 0)
                                                    <form action="{{ route('debourses_chantier.generate', $dqe->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-hard-hat"></i> Générer déboursé chantier
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">Aucune ligne disponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucun DQE disponible pour ce contrat. Veuillez d'abord créer un DQE.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Liste des déboursés chantier</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Projet</th>
                                    <th>Contrat</th>
                                    <th>DQE de référence</th>
                                    <th>Date de génération</th>
                                    <th>Montant total</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deboursesChantier as $debourseChantier)
                                    <tr>
                                        <td>
                                            @if($debourseChantier->reference)
                                                <a href="{{ route('debourses_chantier.details', $debourseChantier->id) }}" class="badge bg-primary text-decoration-none">{{ $debourseChantier->reference }}</a>
                                            @else
                                                <span class="text-muted">Sans référence</span>
                                            @endif
                                        </td>
                                        <td>{{ $debourseChantier->projet->nom_projet ?? 'N/A' }}</td>
                                        <td>{{ $debourseChantier->contrat->nom_contrat ?? 'N/A' }}</td>
                                        <td>{{ $debourseChantier->dqe->reference ?? 'DQE sans référence' }}</td>
                                        <td>{{ $debourseChantier->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($debourseChantier->montant_total, 2, ',', ' ') }}</td>
                                        <td>
                                            @if($debourseChantier->statut == 'brouillon')
                                                <span class="badge bg-warning">Brouillon</span>
                                            @elseif($debourseChantier->statut == 'validé')
                                                <span class="badge bg-success">Validé</span>
                                            @else
                                                <span class="badge bg-secondary">Archivé</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('debourses_chantier.details', $debourseChantier->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Détails
                                                </a>
                                                <a href="{{ route('debourses_chantier.export', $debourseChantier->id) }}" class="btn btn-sm btn-success ms-1">
                                                    <i class="fas fa-file-pdf"></i> Exporter
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun déboursé chantier trouvé pour ce contrat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection