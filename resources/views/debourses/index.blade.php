@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Déboursé sec et Frais de Chantier</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dqe.index', $contrat->id) }}" class="btn btn-primary">
                <i class="fas fa-list"></i> Gérer les DQE
            </a>
            <a href="{{ route('debourses_chantier.index', $contrat->id) }}" class="btn btn-info ms-2">
                <i class="fas fa-hard-hat"></i> Déboursés Chantier
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
                    <h5>Liste des déboursés</h5>
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
                                    <th>Type</th>
                                    <th>Date de génération</th>
                                    <th>Montant total</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($debourses as $debourse)
                                    <tr>
                                        <td>
                                            @if($debourse->reference)
                                                <a href="{{ route('debourses.show', $debourse->id) }}" class="badge bg-primary text-decoration-none">{{ $debourse->reference }}</a>
                                            @else
                                                <span class="text-muted">Sans référence</span>
                                            @endif
                                        </td>
                                        <td>{{ $debourse->projet->nom_projet ?? 'N/A' }}</td>
                                        <td>{{ $debourse->contrat->nom_contrat ?? 'N/A' }}</td>
                                        <td>{{ $debourse->dqe->reference ?? 'DQE sans référence' }}</td>
                                        <td>
                                            @if($debourse->type == 'sec')
                                                <span class="badge bg-primary">Déboursé Sec</span>
                                            @elseif($debourse->type == 'main_oeuvre')
                                                <span class="badge bg-info">Déboursé Main d'Œuvre</span>
                                            @else
                                                <span class="badge bg-warning">Frais de Chantier</span>
                                            @endif
                                        </td>
                                        <td>{{ $debourse->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($debourse->montant_total, 2, ',', ' ') }}</td>
                                        <td>
                                            @if($debourse->statut == 'brouillon')
                                                <span class="badge bg-warning">Brouillon</span>
                                            @elseif($debourse->statut == 'validé')
                                                <span class="badge bg-success">Validé</span>
                                            @else
                                                <span class="badge bg-secondary">Archivé</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('debourses.details', $debourse->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Détails
                                                </a>
                                                <a href="{{ route('debourses.export', $debourse->id) }}" class="btn btn-sm btn-success ms-1">
                                                    <i class="fas fa-file-pdf"></i> Exporter
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun déboursé trouvé pour ce contrat.</td>
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