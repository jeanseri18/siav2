@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-receipt"></i>
                        Détails de la Réception {{ $reception->numero_reception }}
                    </h3>
                    <div>
                        <a href="{{ route('receptions.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left"></i>
                            Retour à la liste
                        </a>
                        <a href="{{ route('receptions.show', $reception->bonCommande->id) }}" class="btn btn-info">
                            <i class="fas fa-file-invoice"></i>
                            Voir Bon de Commande
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="fas fa-info-circle"></i>
                                        Informations Réception
                                    </h5>
                                    <p><strong>Numéro:</strong> {{ $reception->numero_reception }}</p>
                                    <p><strong>Date de réception:</strong> {{ $reception->date_reception->format('d/m/Y H:i') }}</p>
                                    @if($reception->numero_bon_livraison)
                                        <p><strong>Bon de livraison:</strong> {{ $reception->numero_bon_livraison }}</p>
                                    @endif
                                    @if($reception->transporteur)
                                        <p><strong>Transporteur:</strong> {{ $reception->transporteur }}</p>
                                    @endif
                                    <p><strong>Statut:</strong> 
                                        <span class="badge {{ $reception->statut_badge_class }}">{{ $reception->statut_formate }}</span>
                                    </p>
                                    <p><strong>Réceptionné par:</strong> {{ $reception->user->name }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title text-info">
                                        <i class="fas fa-file-invoice"></i>
                                        Bon de Commande
                                    </h5>
                                    <p><strong>Référence:</strong> {{ $reception->bonCommande->reference }}</p>
                                    <p><strong>Date commande:</strong> {{ \Carbon\Carbon::parse($reception->bonCommande->date_commande)->format('d/m/Y') }}</p>
                                    <p><strong>Fournisseur:</strong> {{ $reception->bonCommande->fournisseur->nom }}</p>
                                    @if($reception->bonCommande->projet)
                                        <p><strong>Projet:</strong> {{ $reception->bonCommande->projet->nom }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-bar"></i>
                                        Totaux Réception
                                    </h5>
                                    <p class="mb-1"><strong>Quantité totale reçue:</strong> {{ number_format($reception->quantite_totale_recue, 0, ',', ' ') }}</p>
                                    <p class="mb-1"><strong>Montant total reçu:</strong> {{ number_format($reception->montant_total_recu, 0, ',', ' ') }} FCFA</p>
                                    <p class="mb-0"><strong>Progression:</strong> {{ $reception->pourcentage_reception }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Détail des articles reçus -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-boxes"></i>
                                Articles Reçus
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Article</th>
                                            <th>Référence</th>
                                            <th>Quantité Reçue</th>
                                            <th>Quantité Conforme</th>
                                            <th>Quantité Non Conforme</th>
                                            <th>Prix Unitaire</th>
                                            <th>Montant Total</th>
                                            <th>État Article</th>
                                            <th>Lot/Série</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reception->lignes as $ligne)
                                            <tr>
                                                <td>
                                                    <strong>{{ $ligne->article->designation }}</strong>
                                                    @if($ligne->article->description)
                                                        <br><small class="text-muted">{{ Str::limit($ligne->article->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <code>{{ $ligne->article->reference_fournisseur ?? $ligne->article->code }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary fs-6">{{ number_format($ligne->quantite_recue, 0, ',', ' ') }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success fs-6">{{ number_format($ligne->quantite_conforme, 0, ',', ' ') }}</span>
                                                </td>
                                                <td>
                                                    @if($ligne->quantite_non_conforme > 0)
                                                        <span class="badge bg-danger fs-6">{{ number_format($ligne->quantite_non_conforme, 0, ',', ' ') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary fs-6">0</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($ligne->prix_unitaire_recu, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ number_format($ligne->montant_total, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    <span class="badge {{ $ligne->etat_badge_class }}">{{ $ligne->etat_article_formate }}</span>
                                                </td>
                                                <td>
                                                    @if($ligne->numero_lot)
                                                        <small>{{ $ligne->numero_lot }}</small>
                                                        @if($ligne->date_peremption)
                                                            <br><small class="text-muted">Exp: {{ $ligne->date_peremption->format('d/m/Y') }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6">Total</th>
                                            <th>{{ number_format($reception->montant_total_recu, 0, ',', ' ') }} FCFA</th>
                                            <th colspan="2">
                                                <span class="badge {{ $reception->statut_badge_class }} fs-6">{{ $reception->statut_formate }}</span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Observations -->
                    @if($reception->observations)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-sticky-note"></i>
                                            Observations
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $reception->observations }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Historique des réceptions pour ce bon de commande -->
                    @php
                        $autresReceptions = $reception->bonCommande->receptions()->where('id', '!=', $reception->id)->orderBy('date_reception', 'desc')->get();
                    @endphp
                    @if($autresReceptions->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-history"></i>
                                            Autres Réceptions pour ce Bon de Commande
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Numéro</th>
                                                        <th>Date</th>
                                                        <th>Quantité</th>
                                                        <th>Montant</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($autresReceptions as $autreReception)
                                                        <tr>
                                                            <td>{{ $autreReception->numero_reception }}</td>
                                                            <td>{{ $autreReception->date_reception->format('d/m/Y') }}</td>
                                                            <td>{{ number_format($autreReception->quantite_totale_recue, 0, ',', ' ') }}</td>
                                                            <td>{{ number_format($autreReception->montant_total_recu, 0, ',', ' ') }} FCFA</td>
                                                            <td><span class="badge {{ $autreReception->statut_badge_class }}">{{ $autreReception->statut_formate }}</span></td>
                                                            <td>
                                                                <a href="{{ route('receptions.show', $autreReception->id) }}" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
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