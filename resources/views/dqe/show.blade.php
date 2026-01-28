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

.categorie-header {
    background-color: #0d6efd !important;
    color: white !important;
    font-weight: 700;
}

.sous-categorie-header {
    background-color: #6f42c1 !important;
    color: white !important;
    font-weight: 600;
}

.rubrique-header {
    background-color: #fd7e14 !important;
    color: white !important;
    font-weight: 600;
}

.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.2rem;
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
            <h2>Détail du DQE</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
            <p>Référence : {{ $dqe->reference ?? 'Sans référence' }}</p>
            <p>Statut : 
                @if($dqe->statut == 'brouillon')
                    <span class="badge bg-warning">Brouillon</span>
                @elseif($dqe->statut == 'validé')
                    <span class="badge bg-success">Validé</span>
                @else
                    <span class="badge bg-secondary">Archivé</span>
                @endif
            </p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dqe.edit', $dqe->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Éditer
            </a>
            <a href="{{ route('dqe.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card table-container">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th style="min-width: 80px;">Code</th>
                            <th style="min-width: 300px;">Désignation</th>
                            <th style="min-width: 80px; text-align: center;">Section</th>
                            <th style="min-width: 100px; text-align: center;">Unité</th>
                            <th style="min-width: 120px; text-align: right;">Quantité</th>
                            <th style="min-width: 120px; text-align: right;">PU HT</th>
                            <th style="min-width: 140px; text-align: right;">Montant HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lignesOrganisees as $categorieNom => $categorieData)
                            <!-- En-tête de catégorie -->
                            <tr class="categorie-header">
                                <td colspan="7">
                                    <strong>{{ $categorieNom }}</strong>
                                    @if($categorieData['categorie'])
                                        <small class="ms-2">(ID: {{ $categorieData['categorie']->id }})</small>
                                    @endif
                                </td>
                            </tr>
                            
                            @foreach($categorieData['sousCategories'] as $sousCategorieNom => $sousCategorieData)
                                <!-- En-tête de sous-catégorie -->
                                <tr class="sous-categorie-header">
                                    <td colspan="7">
                                        <strong>&nbsp;&nbsp;&nbsp;{{ $sousCategorieNom }}</strong>
                                        @if($sousCategorieData['sousCategorie'])
                                            <small class="ms-2">(ID: {{ $sousCategorieData['sousCategorie']->id }})</small>
                                        @endif
                                    </td>
                                </tr>
                                
                                @foreach($sousCategorieData['rubriques'] as $rubriqueNom => $rubriqueData)
                                    <!-- En-tête de rubrique -->
                                    <tr class="rubrique-header">
                                        <td colspan="7">
                                            <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $rubriqueNom }}</strong>
                                            @if($rubriqueData['rubrique'])
                                                <small class="ms-2">(ID: {{ $rubriqueData['rubrique']->id }})</small>
                                            @endif
                                        </td>
                                    </tr>
                                    
                                    <!-- Lignes DQE pour cette rubrique -->
                                    @foreach($rubriqueData['lignes'] as $ligne)
                                        <tr>
                                            <td>{{ $ligne->code }}</td>
                                            <td>{{ $ligne->designation }}</td>
                                            <td style="text-align: center;">{{ $ligne->section }}</td>
                                            <td style="text-align: center;">{{ $ligne->unite }}</td>
                                            <td style="text-align: right;">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                            <td style="text-align: right; white-space: nowrap;">{{ number_format($ligne->pu_ht, 2, ',', ' ') }} FCFA</td>
                                            <td style="text-align: right; white-space: nowrap;">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune ligne DQE trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <td colspan="6" style="text-align: right;"><strong>TOTAL HT:</strong></td>
                            <td style="text-align: right; white-space: nowrap;">
                                <strong>{{ number_format($dqe->montant_total_ht, 2, ',', ' ') }} FCFA</strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection