@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Frais de Chantier</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dqe.index', $contrat->id) }}" class="btn btn-primary">
                <i class="fas fa-list"></i> Gérer les DQE
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Liste des frais de chantier</h5>
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
                                @forelse($debourses->where('type', 'frais_chantier') as $debourse)
                                    <tr>
                                        <td>{{ $debourse->reference }}</td>
                                        <td>{{ $debourse->projet->nom_projet ?? 'N/A' }}</td>
                                        <td>{{ $debourse->contrat->nom_contrat ?? 'N/A' }}</td>
                                        <td>{{ $debourse->dqe->reference ?? 'DQE sans référence' }}</td>
                                        <td>{{ $debourse->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ number_format($debourse->montant_total, 2, ',', ' ') }}</td>
                                        <td>
                                            @if($debourse->statut == 'brouillon')
                                                <span class="badge bg-warning">Brouillon</span>
                                            @elseif($debourse->statut == 'validé')
                                                <span class="badge bg-success">Validé</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($debourse->statut) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('debourses.details', $debourse->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <a href="{{ route('debourses.export', $debourse->id) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun frais de chantier trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section pour générer de nouveaux frais de chantier -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Générer de nouveaux frais de chantier</h5>
                </div>
                <div class="card-body">
                    @if($contrat->dqes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
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
                                                    <form action="{{ route('debourses.generate', $dqe->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-plus"></i> Générer frais de chantier
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
                            <i class="fas fa-info-circle"></i>
                            Aucun DQE disponible pour ce contrat. 
                            <a href="{{ route('dqe.create', $contrat->id) }}" class="alert-link">Créer un DQE</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection