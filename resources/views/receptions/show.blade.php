@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i>
                        Détails de la Réception - {{ $bonCommande->reference }}
                    </h3>
                    <div>
                        <a href="{{ route('receptions.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i>
                            Retour
                        </a>
                        @php
                            $totalQuantite = $bonCommande->lignes->sum('quantite');
                            $totalRecue = $bonCommande->lignes->sum('quantite_recue');
                            $pourcentage = $totalQuantite > 0 ? round(($totalRecue / $totalQuantite) * 100, 1) : 0;
                        @endphp
                        @if($pourcentage < 100)
                            <a href="{{ route('receptions.create', $bonCommande->id) }}" class="btn btn-success">
                                <i class="fas fa-truck-loading"></i>
                                Effectuer Réception
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="fas fa-file-invoice"></i>
                                        Informations Commande
                                    </h5>
                                    <p><strong>Référence:</strong> {{ $bonCommande->reference }}</p>
                                    <p><strong>Date de commande:</strong> {{ \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') }}</p>
                                    @if($bonCommande->date_livraison_prevue)
                                        <p><strong>Date de livraison prévue:</strong> 
                                            {{ \Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->format('d/m/Y') }}
                                            @if(\Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->isPast() && $pourcentage < 100)
                                                <span class="badge bg-warning ms-1">En retard</span>
                                            @endif
                                        </p>
                                    @endif
                                    <p><strong>Statut:</strong> 
                                        @if($bonCommande->statut == 'reçu')
                                            <span class="badge bg-success">Reçu</span>
                                        @elseif($pourcentage > 0)
                                            <span class="badge bg-warning">Partiellement reçu</span>
                                        @else
                                            <span class="badge bg-secondary">En attente</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-info">
                                        <i class="fas fa-building"></i>
                                        Fournisseur
                                    </h5>
                                    <p><strong>Nom:</strong> {{ $bonCommande->fournisseur->nom }}</p>
                                    @if($bonCommande->fournisseur->telephone)
                                        <p><strong>Téléphone:</strong> {{ $bonCommande->fournisseur->telephone }}</p>
                                    @endif
                                    @if($bonCommande->fournisseur->email)
                                        <p><strong>Email:</strong> {{ $bonCommande->fournisseur->email }}</p>
                                    @endif
                                    @if($bonCommande->fournisseur->adresse)
                                        <p><strong>Adresse:</strong> {{ $bonCommande->fournisseur->adresse }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-pie"></i>
                                        Progression Globale
                                    </h5>
                                    <div class="progress mb-3" style="height: 25px; background-color: rgba(255,255,255,0.2);">
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: {{ $pourcentage }}%" 
                                             aria-valuenow="{{ $pourcentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $pourcentage }}%
                                        </div>
                                    </div>
                                    <p class="mb-1"><strong>Articles reçus:</strong> {{ $totalRecue }}/{{ $totalQuantite }}</p>
                                    <p class="mb-0"><strong>Montant total:</strong> {{ number_format($bonCommande->montant_total, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Détail des articles -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-boxes"></i>
                                Détail des Articles
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Article</th>
                                            <th>Référence</th>
                                            <th>Quantité Commandée</th>
                                            <th>Quantité Reçue</th>
                                            <th>Quantité Restante</th>
                                            <th>Prix Unitaire</th>
                                            <th>Montant Total</th>
                                            <th>Statut</th>
                                            <th>Progression</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bonCommande->lignes as $ligne)
                                            @php
                                                $quantiteRestante = $ligne->quantite - $ligne->quantite_recue;
                                                $pourcentageLigne = $ligne->quantite > 0 ? round(($ligne->quantite_recue / $ligne->quantite) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $ligne->article->nom }}</strong>
                                                    @if($ligne->article->description)
                                                        <br><small class="text-muted">{{ Str::limit($ligne->article->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <code>{{ $ligne->article->reference_fournisseur ?? $ligne->article->reference }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary fs-6">{{ $ligne->quantite }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success fs-6">{{ $ligne->quantite_recue }}</span>
                                                </td>
                                                <td>
                                                    @if($quantiteRestante > 0)
                                                        <span class="badge bg-warning fs-6">{{ $quantiteRestante }}</span>
                                                    @else
                                                        <span class="badge bg-secondary fs-6">0</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ number_format($ligne->quantite * $ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    @if($pourcentageLigne == 100)
                                                        <span class="badge bg-success">Complet</span>
                                                    @elseif($pourcentageLigne > 0)
                                                        <span class="badge bg-warning">Partiel</span>
                                                    @else
                                                        <span class="badge bg-secondary">En attente</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px; min-width: 100px;">
                                                        <div class="progress-bar {{ $pourcentageLigne == 100 ? 'bg-success' : ($pourcentageLigne > 0 ? 'bg-warning' : 'bg-secondary') }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $pourcentageLigne }}%" 
                                                             aria-valuenow="{{ $pourcentageLigne }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ $pourcentageLigne }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6">Total</th>
                                            <th>{{ number_format($bonCommande->montant_total, 0, ',', ' ') }} FCFA</th>
                                            <th colspan="2">
                                                @if($pourcentage == 100)
                                                    <span class="badge bg-success fs-6">Réception Complète</span>
                                                @elseif($pourcentage > 0)
                                                    <span class="badge bg-warning fs-6">Réception Partielle ({{ $pourcentage }}%)</span>
                                                @else
                                                    <span class="badge bg-secondary fs-6">En Attente de Réception</span>
                                                @endif
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes et conditions -->
                    @if($bonCommande->notes || $bonCommande->conditions_paiement)
                        <div class="row mt-4">
                            @if($bonCommande->notes)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="fas fa-sticky-note"></i>
                                                Notes
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $bonCommande->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($bonCommande->conditions_paiement)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="fas fa-credit-card"></i>
                                                Conditions de Paiement
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0">{{ $bonCommande->conditions_paiement }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}

code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
}
</style>
@endsection