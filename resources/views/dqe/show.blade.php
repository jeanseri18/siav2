@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Détails du DQE</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
            <p>Référence : {{ $dqe->reference ?? 'Sans référence' }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dqe.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('dqe.edit', $dqe->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Informations générales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Référence</strong></label>
                                <p class="form-control-plaintext">{{ $dqe->reference ?? 'Sans référence' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Statut</strong></label>
                                <p class="form-control-plaintext">
                                    @if($dqe->statut == 'brouillon')
                                        <span class="badge bg-warning">Brouillon</span>
                                    @elseif($dqe->statut == 'validé')
                                        <span class="badge bg-success">Validé</span>
                                    @elseif($dqe->statut == 'archivé')
                                        <span class="badge bg-secondary">Archivé</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><strong>Date de création</strong></label>
                                <p class="form-control-plaintext">{{ $dqe->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($dqe->notes)
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>Notes</strong></label>
                                    <p class="form-control-plaintext">{{ $dqe->notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Lignes du DQE</h5>
                    <div>
                        <span class="badge bg-primary">Montant total HT : {{ number_format($dqe->montant_total_ht, 2, ',', ' ') }} FCFA</span>
                        <span class="badge bg-success">Montant total TTC : {{ number_format($dqe->montant_total_ttc, 2, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($dqe->lignes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Section</th>
                                        <th>Désignation</th>
                                        <th>Unité</th>
                                        <th>Quantité</th>
                                        <th>Prix Unitaire HT</th>
                                        <th>Montant HT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dqe->lignes as $ligne)
                                        <tr>
                                            <td>{{ $ligne->section ?? 'N/A' }}</td>
                                            <td>{{ $ligne->designation }}</td>
                                            <td>{{ $ligne->unite }}</td>
                                            <td>{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                            <td>{{ number_format($ligne->pu_ht, 2, ',', ' ') }} FCFA</td>
                                            <td>{{ number_format($ligne->montant_ht, 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-info">
                                        <th colspan="5" class="text-end">Total HT :</th>
                                        <th>{{ number_format($dqe->montant_total_ht, 2, ',', ' ') }} FCFA</th>
                                    </tr>
                                    <tr class="table-success">
                                        <th colspan="5" class="text-end">Total TTC :</th>
                                        <th>{{ number_format($dqe->montant_total_ttc, 2, ',', ' ') }} FCFA</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Aucune ligne n'a été ajoutée à ce DQE.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection