@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Déboursés Secs du Contrat - {{ $contrat->nom_contrat }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('contrats.show', $contrat) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au contrat
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($debourseSecParents->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucun déboursé sec trouvé pour ce contrat.
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
                                    @foreach($debourseSecParents as $parent)
                                    <tr>
                                        <td>{{ $parent->ref }}</td>
                                        <td>{{ $parent->dqe->reference ?? 'N/A' }}</td>
                                        <td>{{ number_format($parent->montant_total, 2, ',', ' ') }} F CFA</td>
                                        <td>
                                            @if($parent->statut === \App\Models\DebourseSecParent::STATUT_BROUILLON)
                                                <span class="badge badge-warning">Brouillon</span>
                                            @elseif($parent->statut === \App\Models\DebourseSecParent::STATUT_VALIDE)
                                                <span class="badge badge-success">Validé</span>
                                            @elseif($parent->statut === \App\Models\DebourseSecParent::STATUT_REFUSE)
                                                <span class="badge badge-danger">Refusé</span>
                                            @endif
                                        </td>
                                        <td>{{ $parent->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('contrats.debourse-sec.parent.show', [$contrat, $parent]) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Détails
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <!-- Total Général -->
                                    <tr class="table-dark font-weight-bold">
                                        <td colspan="3" class="text-right">Total Général des Déboursés Secs :</td>
                                        <td colspan="3">{{ number_format($debourseSecParents->sum('montant_total'), 2, ',', ' ') }} F CFA</td>
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