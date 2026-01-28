@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Frais de chantier - {{ $dqe->code }}</h3>
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
                                @foreach($fraisChantiers as $fraisChantier)
                                <tr>
                                    <td>{{ $fraisChantier->rubrique->code ?? '-' }}</td>
                                    <td>{{ $fraisChantier->designation }}</td>
                                    <td>{{ $fraisChantier->unite }}</td>
                                    <td>{{ number_format($fraisChantier->quantite, 2, ',', ' ') }}</td>
                                    <td>{{ number_format($fraisChantier->pu_ht, 2, ',', ' ') }}</td>
                                    <td>{{ number_format($fraisChantier->montant_ht, 2, ',', ' ') }}</td>
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