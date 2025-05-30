@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Frais Généraux</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('frais_generaux.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nouveau calcul
            </a>
            <form action="{{ route('frais_generaux.generate') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-calculator"></i> Générer automatiquement (10%)
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5>Informations sur le contrat</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Référence du contrat :</strong> {{ $contrat->ref_contrat }}</p>
                    <p><strong>Client :</strong> {{ $contrat->client->nom ?? 'Non défini' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Montant du contrat :</strong> {{ number_format($montantContrat, 2, ',', ' ') }}</p>
                    <p><strong>Frais généraux standard (10%) :</strong> {{ number_format($montantContrat * 0.10, 2, ',', ' ') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Historique des frais généraux</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Date de calcul</th>
                        <th>Montant de base</th>
                        <th>Pourcentage</th>
                        <th>Montant total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fraisGeneraux as $fraisGeneral)
                        <tr>
<td>
    @if($fraisGeneral->date_calcul instanceof \Carbon\Carbon)
        {{ $fraisGeneral->date_calcul->format('d/m/Y') }}
    @else
        {{ date('d/m/Y', strtotime($fraisGeneral->date_calcul)) }}
    @endif
</td>
                            <td>{{ number_format($fraisGeneral->montant_base, 2, ',', ' ') }}</td>
                            <td>{{ $fraisGeneral->pourcentage }}%</td>
                            <td>{{ number_format($fraisGeneral->montant_total, 2, ',', ' ') }}</td>
                            <td>
                                @if($fraisGeneral->statut == 'brouillon')
                                    <span class="badge bg-warning">Brouillon</span>
                                @elseif($fraisGeneral->statut == 'validé')
                                    <span class="badge bg-success">Validé</span>
                                @else
                                    <span class="badge bg-secondary">Archivé</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('frais_generaux.edit', $fraisGeneral->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Éditer
                                    </a>
                                    <a href="{{ route('frais_generaux.export', $fraisGeneral->id) }}" class="btn btn-sm btn-success ms-1">
                                        <i class="fas fa-file-pdf"></i> Exporter
                                    </a>
                                    <form action="{{ route('frais_generaux.destroy', $fraisGeneral->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ces frais généraux ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger ms-1">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun calcul de frais généraux trouvé pour ce contrat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection