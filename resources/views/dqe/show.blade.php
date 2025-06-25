@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<style>
.table-container {
    border-radius: 0.375rem;
    overflow: hidden;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.table .table-primary td {
    background-color: #cfe2ff !important;
    font-weight: 600;
}

.table .table-info td {
    background-color: #d1ecf1 !important;
    font-weight: 500;
}

.table .table-warning td {
    background-color: #fff3cd !important;
    font-weight: 500;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table th, .table td {
        padding: 0.5rem 0.25rem;
    }
}
</style>

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

            <div class="card table-container">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Lignes du DQE</h5>
                    <div>
                        <span class="badge bg-primary">Montant total HT : {{ number_format($dqe->montant_total_ht, 2, ',', ' ') }} FCFA</span>
                        <span class="badge bg-success">Montant total TTC : {{ number_format($dqe->montant_total_ttc, 2, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(isset($lignesOrganisees) && count($lignesOrganisees) > 0)
                        <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th style="min-width: 120px;">Section</th>
                                        <th style="min-width: 250px;">Désignation</th>
                                        <th style="min-width: 80px;">Unité</th>
                                        <th style="min-width: 100px;">Quantité</th>
                                        <th style="min-width: 140px;">Prix Unitaire HT</th>
                                        <th style="min-width: 140px;">Montant HT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lignesOrganisees as $categorieNom => $categorieData)
                                        <!-- Affichage Catégorie -->
                                        <tr class="table-primary">
                                            <td colspan="6">
                                                <strong><i class="fas fa-folder"></i> {{ $categorieNom }}</strong>
                                            </td>
                                        </tr>
                                        
                                        @foreach($categorieData['sousCategories'] as $sousCategorieNom => $sousCategorieData)
                                            <!-- Affichage Sous-catégorie -->
                                            <tr class="table-info">
                                                <td colspan="6" style="padding-left: 30px;">
                                                    <strong><i class="fas fa-folder-open"></i> {{ $sousCategorieNom }}</strong>
                                                </td>
                                            </tr>
                                            
                                            @foreach($sousCategorieData['rubriques'] as $rubriqueNom => $rubriqueData)
                                                <!-- Affichage Rubrique -->
                                                <tr class="table-warning">
                                                    <td colspan="6" style="padding-left: 60px;">
                                                        <strong><i class="fas fa-list"></i> {{ $rubriqueNom }}</strong>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Affichage des lignes de cette rubrique -->
                                                @foreach($rubriqueData['lignes'] as $ligne)
                                                    <tr style="padding-left: 90px;">
                                                        <td style="padding-left: 90px; white-space: nowrap;">{{ $ligne->section ?? 'N/A' }}</td>
                                                        <td style="word-wrap: break-word; max-width: 300px;">{{ $ligne->designation }}</td>
                                                        <td style="text-align: center;">{{ $ligne->unite }}</td>
                                                        <td style="text-align: center;">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                                        <td style="text-align: right; white-space: nowrap;">{{ number_format($ligne->pu_ht, 2, ',', ' ') }} FCFA</td>
                                                        <td style="text-align: right; white-space: nowrap; font-weight: bold;">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} FCFA</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
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