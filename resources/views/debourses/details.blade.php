@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>
                @if($debourse->type == 'sec')
                    Déboursé Sec
                @elseif($debourse->type == 'main_oeuvre')
                    Déboursé Main d'Œuvre
                @else
                    Frais de Chantier
                @endif
            </h2>
            <h4>Contrat : {{ $debourse->contrat->nom_contrat }}</h4>
            <p>DQE : {{ $debourse->dqe->reference ?? 'Sans référence' }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('debourses.index', $debourse->contrat_id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('debourses.export', $debourse->id) }}" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Exporter en PDF
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Détails du déboursé</h5>
                    <span class="badge bg-primary fs-5">Montant total : {{ number_format($debourse->montant_total, 2, ',', ' ') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Désignation</th>
                                    <th>Quantité</th>
                                    <th>Unité</th>
                                    @if($debourse->type == 'sec')
                                        <th>Matériaux</th>
                                        <th>Main d'Œuvre</th>
                                        <th>Matériel</th>
                                    @elseif($debourse->type == 'main_oeuvre')
                                        <th>Main d'Œuvre Unitaire</th>
                                    @else
                                        <th>Frais de Chantier Unitaire</th>
                                    @endif
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debourse->details as $detail)
                                    <tr>
                                        <td>{{ $detail->dqeLigne->designation }}</td>
                                        <td>{{ $detail->dqeLigne->quantite }}</td>
                                        <td>{{ $detail->dqeLigne->unite }}</td>
                                        @if($debourse->type == 'sec')
                                            <td>{{ number_format($detail->dqeLigne->bpu->materiaux, 2, ',', ' ') }}</td>
                                            <td>{{ number_format($detail->dqeLigne->bpu->main_oeuvre, 2, ',', ' ') }}</td>
                                            <td>{{ number_format($detail->dqeLigne->bpu->materiel, 2, ',', ' ') }}</td>
                                        @elseif($debourse->type == 'main_oeuvre')
                                            <td>{{ number_format($detail->dqeLigne->bpu->main_oeuvre, 2, ',', ' ') }}</td>
                                        @else
                                            <td>{{ number_format($detail->dqeLigne->bpu->frais_chantier, 2, ',', ' ') }}</td>
                                        @endif
                                        <td>{{ number_format($detail->montant, 2, ',', ' ') }}</td>
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