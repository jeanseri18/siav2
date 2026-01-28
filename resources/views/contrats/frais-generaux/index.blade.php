@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Frais Généraux du Contrat - {{ $contrat->nom_contrat }}</h3>
                    <div class="card-tools">
                        <form action="{{ route('contrats.frais-generaux.store', $contrat) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> Créer document
                            </button>
                        </form>
                        <a href="{{ route('contrats.show', $contrat) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au contrat
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($fraisGenerauxParents->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucun frais général trouvé pour ce contrat.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Référence</th>
                                        <th>DQE</th>
                                        <th>Montant Total</th>
                                        <th>Statut</th>
                                        <th>Date de création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fraisGenerauxParents as $parent)
                                    <tr>
                                        <td>
                                            @if($parent->type === \App\Models\FraisGenerauxParent::TYPE_PREVISIONNEL)
                                                <span class="badge bg-info">{{ $parent->ref }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $parent->ref }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $parent->dqe->reference ?? 'N/A' }}</td>
                                        <td>{{ number_format($parent->montant_total, 2, ',', ' ') }} F CFA</td>
                                        <td>
                                            <span class="badge bg-{{ $parent->statut === 'validé' ? 'success' : ($parent->statut === 'en_attente' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($parent->statut) }}
                                            </span>
                                        </td>
                                        <td>{{ $parent->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('contrats.frais-generaux.show', [$contrat, $parent]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Détails
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <!-- Total Général -->
                                    <tr class="table-dark font-weight-bold">
                                        <td colspan="3" class="text-right">Total Général des Frais Généraux :</td>
                                        <td colspan="3">{{ number_format($fraisGenerauxParents->sum('montant_total'), 2, ',', ' ') }} F CFA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection