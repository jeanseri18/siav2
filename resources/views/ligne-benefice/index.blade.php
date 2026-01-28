@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lignes de bénéfice - {{ $dqe->code }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('dqe.edit', $dqe) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au DQE
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Rubrique</th>
                                    <th>Désignation</th>
                                    <th>Unité</th>
                                    <th>Quantité</th>
                                    <th>P.U. HT</th>
                                    <th>Montant HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ligneBenefices as $ligneBenefice)
                                <tr>
                                    <td>{{ $ligneBenefice->rubrique->code ?? '-' }}</td>
                                    <td>{{ $ligneBenefice->designation }}</td>
                                    <td>{{ $ligneBenefice->unite }}</td>
                                    <td>{{ number_format($ligneBenefice->quantite, 2, ',', ' ') }}</td>
                                    <td>{{ number_format($ligneBenefice->pu_ht, 2, ',', ' ') }}</td>
                                    <td>{{ number_format($ligneBenefice->montant_ht, 2, ',', ' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection