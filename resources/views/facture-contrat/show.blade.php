{{-- Page Show - Détails d'une Facture Contrat --}}
@extends('layouts.app')

@section('title', 'Détails de la Facture Contrat')
@section('page-title', 'Détails de la Facture Contrat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('facture-contrat.index') }}">Factures Contrat</a></li>
<li class="breadcrumb-item active">Détails #{{ $factureContrat->id }}</li>
@endsection

@push('scripts')
<script>
function updatePaiementCalculations() {
    const pourcentage = parseFloat(document.getElementById('pourcentage_paiement').value) || 0;
    // Ici vous pouvez ajouter la logique pour mettre à jour les calculs de paiement
    console.log('Pourcentage de paiement:', pourcentage);
    // Exemple: mettre à jour un champ caché ou faire un appel AJAX
}
</script>
@endpush

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

<div class="app-fade-in">
    <div class="row">
        <div class="col-lg-12">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-contract me-2"></i>Facture Contrat #{{ $factureContrat->id }}
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('facture-contrat.index') }}" class="app-btn app-btn-secondary app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    {{-- Messages flash --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informations générales</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Facture :</strong></td>
                                    <td>{{ $factureContrat->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Référence DQE :</strong></td>
                                    <td>
                                        @if($factureContrat->dqe)
                                            {{ $factureContrat->dqe->reference }}
                                        @else
                                            <span class="text-muted">Non spécifiée</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Contrat :</strong></td>
                                    <td>
                                        @if($factureContrat->dqe && $factureContrat->dqe->contrat)
                                            {{ $factureContrat->dqe->contrat->nom_contrat }}
                                        @else
                                            <span class="text-muted">Non spécifié</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Date de création :</strong></td>
                                    <td>{{ $factureContrat->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dernière modification :</strong></td>
                                    <td>{{ $factureContrat->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pourcentage de paiement :</strong></td>
                                    <td>
                                        <div class="input-group input-group-sm" style="max-width: 200px;">
                                            <input type="number" class="form-control" id="pourcentage_paiement" 
                                                   min="0" max="100" step="0.01" value="0" 
                                                   onchange="updatePaiementCalculations()">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Informations financières</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Montant à payer :</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary fs-6">
                                            {{ number_format($factureContrat->montant_a_payer, 2, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Montant versé :</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-success fs-6">
                                            {{ number_format($factureContrat->montant_verse, 2, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Reste à payer :</strong></td>
                                    @php
                                        $reste = $factureContrat->montant_a_payer - $factureContrat->montant_verse;
                                        $badgeClass = $reste > 0 ? 'bg-warning text-dark' : 'bg-success';
                                    @endphp
                                    <td class="text-end">
                                        <span class="badge {{ $badgeClass }} fs-6">
                                            {{ number_format($reste, 2, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($factureContrat->dqe && !empty($lignesOrganisees))
                    <div class="row">
                        <div class="col-12">
                            <h5>Détails du DQE associé</h5>
                            <div class="card table-container">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 80px;">Code</th>
                                                    <th style="min-width: 300px;">Désignation</th>
                                                    <th style="min-width: 100px; text-align: center;">Unité</th>
                                                    <th style="min-width: 120px; text-align: right;">Quantité</th>
                                                    <th style="min-width: 120px; text-align: right;">PU HT</th>
                                                    <th style="min-width: 140px; text-align: right;">Montant HT</th>
                                                    <th style="min-width: 100px; text-align: center;">% Réalisé</th>


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
                                                                    <td style="text-align: center;">{{ $ligne->unite }}</td>
                                                                    <td style="text-align: right;">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                                                    <td style="text-align: right; white-space: nowrap;">{{ number_format($ligne->pu_ht, 2, ',', ' ') }} FCFA</td>
                                                                    <td style="text-align: right; white-space: nowrap;">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} FCFA</td>
                                                                    <td style="text-align: center;">
                                                                        @php
                                                                            $pourcentageCumule = 0;
                                                                            foreach($factureContrat->facturesDecompte as $decompte) {
                                                                                if($decompte->statut === 'valide') {
                                                                                    foreach($decompte->lignes as $ligneDecompte) {
                                                                                        if($ligneDecompte->dqe_ligne_id == $ligne->id) {
                                                                                            $pourcentageCumule += $ligneDecompte->pourcentage_realise;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        @if($pourcentageCumule > 0)
                                                                            <span class="badge bg-success">{{ number_format($pourcentageCumule, 2, ',', ' ') }}%</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">0%</span>
                                                                        @endif
                                                                    </td>
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
                                                         <strong>{{ number_format($factureContrat->dqe->montant_total_ht ?? 0, 2, ',', ' ') }} FCFA</strong>
                                                     </td>
                                                 </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @elseif($factureContrat->dqe)
                    <div class="row">
                        <div class="col-12">
                            <h5>Informations du DQE associé</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong>Référence :</strong></td>
                                            <td>{{ $factureContrat->dqe->reference ?? 'Non spécifiée' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Libellé :</strong></td>
                                            <td>{{ $factureContrat->dqe->libelle ?? 'Non spécifié' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Unité :</strong></td>
                                            <td>{{ $factureContrat->dqe->unite ?? 'Non spécifiée' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Quantité :</strong></td>
                                            <td>{{ number_format($factureContrat->dqe->quantite ?? 0, 2, ',', ' ') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Prix Unitaire :</strong></td>
                                            <td>{{ number_format($factureContrat->dqe->prix_unitaire ?? 0, 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Montant Total :</strong></td>
                                            <td>{{ number_format($factureContrat->dqe->montant_total ?? 0, 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Bouton pour générer une facture de décompte -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Générer une Facture de Décompte</h5>
                                <a href="{{ route('facture-contrat.decompte.create', $factureContrat->id) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-file-invoice"></i> Générer une facture de décompte
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des factures de décompte -->
                    @if($factureContrat->facturesDecompte && $factureContrat->facturesDecompte->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Factures de Décompte</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Date</th>
                                            <th>Pourcentage d'Avancement</th>
                                            <th>Montant HT</th>
                                            <th>Montant TTC</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($factureContrat->facturesDecompte as $decompte)
                                        <tr>
                                            <td>{{ $decompte->numero }}</td>
                                            <td>{{ $decompte->date_facture->format('d/m/Y') }}</td>
                                            <td>{{ number_format($decompte->pourcentage_avancement, 2, ',', ' ') }} %</td>
                                            <td>{{ number_format($decompte->montant_ht, 2, ',', ' ') }} F CFA</td>
                                            <td>{{ number_format($decompte->montant_ttc, 2, ',', ' ') }} F CFA</td>
                                            <td>
                                                <span class="badge bg-{{ $decompte->statut === 'valide' ? 'success' : ($decompte->statut === 'annule' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($decompte->statut) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('facture-decompte.show', $decompte->id) }}" 
                                                   class="btn btn-sm btn-info" title="Voir la facture">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                @if($decompte->statut === 'brouillon')
                                                <form action="{{ route('facture-decompte.valider', $decompte->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Valider la facture" 
                                                            onclick="return confirm('Êtes-vous sûr de vouloir valider cette facture de décompte ?')">
                                                        <i class="fas fa-check"></i> Valider
                                                    </button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-primary">
                                            <td colspan="3"><strong>TOTAL</strong></td>
                                            <td><strong>{{ number_format($factureContrat->facturesDecompte->sum('montant_ht'), 2, ',', ' ') }} F CFA</strong></td>
                                            <td><strong>{{ number_format($factureContrat->facturesDecompte->sum('montant_ttc'), 2, ',', ' ') }} F CFA</strong></td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Aucune facture de décompte n'a été générée pour cette facture contrat.
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="app-card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <form action="{{ route('facture-contrat.destroy', $factureContrat->id) }}" method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture contrat ? Cette action est irréversible.')" 
                                  style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="app-btn app-btn-danger app-btn-icon">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                        <div>
                            <a href="{{ route('facture-contrat.index') }}" class="app-btn app-btn-secondary app-btn-icon">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
        
      
    </div>
</div>
@endsection