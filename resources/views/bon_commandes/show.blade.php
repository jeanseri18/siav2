{{-- Page Show - Détails du Bon de Commande --}}
@extends('layouts.app')

@section('title', 'Bon de Commande ' . $bonCommande->reference)
@section('page-title', 'Bon de Commande ' . $bonCommande->reference)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bon-commandes.index') }}">Bons de Commande</a></li>
<li class="breadcrumb-item active">{{ $bonCommande->reference }}</li>
@endsection

@section('content')

<div class="container app-fade-in" id="printable-content">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <!-- En-tête du bon de commande -->
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-invoice me-2"></i>BON DE COMMANDE
                    </h2>
                    <div class="app-card-actions">
                        @php
                            $statutClass = '';
                            $statutIcon = '';
                            switch($bonCommande->statut) {
                                case 'en attente':
                                    $statutClass = 'warning';
                                    $statutIcon = 'clock';
                                    break;
                                case 'confirmée':
                                    $statutClass = 'info';
                                    $statutIcon = 'check-circle';
                                    break;
                                case 'livrée':
                                    $statutClass = 'success';
                                    $statutIcon = 'truck';
                                    break;
                                case 'annulée':
                                    $statutClass = 'danger';
                                    $statutIcon = 'times-circle';
                                    break;
                                default:
                                    $statutClass = 'secondary';
                                    $statutIcon = 'question-circle';
                            }
                        @endphp
                        <span class="app-badge app-badge-{{ $statutClass }} app-badge-pill app-badge-lg">
                            <i class="fas fa-{{ $statutIcon }} me-1"></i> {{ ucfirst($bonCommande->statut) }}
                        </span>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <div class="text-center mb-4">
                        <h3 class="app-fw-bold">{{ $bonCommande->reference }}</h3>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-hashtag me-2"></i>Référence
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $bonCommande->reference }}
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Date de commande
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $bonCommande->date_commande->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-truck me-2"></i>Date de livraison prévue
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $bonCommande->date_livraison_prevue ? $bonCommande->date_livraison_prevue->format('d/m/Y') : 'Non spécifiée' }}
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-building me-2"></i>Fournisseur
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $bonCommande->fournisseur ? $bonCommande->fournisseur->nom_raison_sociale : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-credit-card me-2"></i>Conditions de paiement
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $bonCommande->conditions_paiement ?: 'Non spécifiées' }}
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-user me-2"></i>Créé par
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $bonCommande->user ? $bonCommande->user->name : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-clipboard-list me-2"></i>Demande d'appro liée
                                </label>
                                <div class="app-form-control bg-light">
                                    @if($bonCommande->demandeApprovisionnement)
                                        <a href="{{ route('demande-approvisionnements.show', $bonCommande->demandeApprovisionnement) }}" class="text-decoration-none">
                                            {{ $bonCommande->demandeApprovisionnement->reference }}
                                        </a>
                                    @else
                                        Aucune
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-shopping-cart me-2"></i>Demande d'achat liée
                                </label>
                                <div class="app-form-control bg-light">
                                    @if($bonCommande->demandeAchat)
                                        <a href="{{ route('demande-achats.show', $bonCommande->demandeAchat) }}" class="text-decoration-none">
                                            {{ $bonCommande->demandeAchat->reference }}
                                        </a>
                                    @else
                                        Aucune
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($bonCommande->notes)
            <!-- Notes -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </h3>
                </div>
                <div class="app-card-body">
                    <p class="mb-0">{{ $bonCommande->notes }}</p>
                </div>
            </div>
            @endif
            
            <!-- Articles commandés -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-boxes me-2"></i>Articles commandés
                    </h3>
                </div>
                <div class="app-card-body app-table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Désignation</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-end">Prix unitaire</th>
                                <th class="text-end">Montant</th>
                                @if($bonCommande->statut == 'livrée')
                                    <th class="text-center">Quantité livrée</th>
                                @endif
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonCommande->lignes as $ligne)
                            <tr>
                                <td>
                                    <span class="app-badge app-badge-light">
                                        {{ $ligne->article->reference }}
                                    </span>
                                </td>
                                <td>
                                    <div class="app-d-flex app-align-items-center app-gap-2">
                                        <div class="item-icon">
                                            <i class="fas fa-box text-primary"></i>
                                        </div>
                                        <span>{{ $ligne->article->nom }}</span>
                                    </div>
                                </td>
                                <td class="text-center app-fw-bold">
                                    <span class="app-badge app-badge-info">
                                        {{ $ligne->quantite }} {{ $ligne->article->unite_mesure }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-end app-fw-bold text-success">
                                    {{ number_format($ligne->quantite * $ligne->prix_unitaire, 0, ',', ' ') }} FCFA
                                </td>
                                @if($bonCommande->statut == 'livrée')
                                <td class="text-center">
                                    <span class="app-badge app-badge-success">
                                        {{ $ligne->quantite_livree }} {{ $ligne->article->unite_mesure }}
                                    </span>
                                </td>
                                @endif
                                <td>{{ $ligne->commentaire ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <th colspan="{{ $bonCommande->statut == 'livrée' ? '6' : '5' }}" class="text-end">
                                    <i class="fas fa-calculator me-2"></i>Total général :
                                </th>
                                <th class="text-end text-success h5">
                                    {{ number_format($bonCommande->montant_total, 0, ',', ' ') }} FCFA
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel latéral -->
        <div class="col-md-4">
            <!-- Résumé -->
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-chart-pie me-2"></i>Résumé
                    </h3>
                </div>
                <div class="app-card-body">
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-boxes me-2"></i>Articles
                        </span>
                        <span class="app-fw-bold">{{ $bonCommande->lignes->count() }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantité totale
                        </span>
                        <span class="app-fw-bold">{{ $bonCommande->lignes->sum('quantite') }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-calendar-plus me-2"></i>Créé le
                        </span>
                        <span class="app-fw-bold">{{ $bonCommande->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center">
                        <span class="text-muted">
                            <i class="fas fa-coins me-2"></i>Montant total
                        </span>
                        <span class="app-fw-bold text-success h5">
                            {{ number_format($bonCommande->montant_total, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="app-card mt-4 no-print">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h3>
                </div>
                <div class="app-card-body app-d-grid app-gap-2">
                    <a href="{{ route('bon-commandes.index') }}" class="app-btn app-btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>
                    
                    <button class="app-btn app-btn-info w-100" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                    
                    @if($bonCommande->statut == 'en attente')
                    <a href="{{ route('bon-commandes.edit', $bonCommande) }}" class="app-btn app-btn-warning w-100">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    
                    <form action="{{ route('bon-commandes.confirm', $bonCommande) }}" method="POST">
                        @csrf
                        <button type="submit" class="app-btn app-btn-success w-100">
                            <i class="fas fa-check me-2"></i>Confirmer
                        </button>
                    </form>
                    
                    <button type="button" class="app-btn app-btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                    @endif
                    
                    @if($bonCommande->statut == 'confirmée')
                    <button type="button" class="app-btn app-btn-primary w-100" data-bs-toggle="modal" data-bs-target="#livrerModal">
                        <i class="fas fa-truck me-2"></i>Marquer comme livré
                    </button>
                    @endif
                    
                    @if($bonCommande->statut != 'livrée' && $bonCommande->statut != 'annulée')
                    <form action="{{ route('bon-commandes.cancel', $bonCommande) }}" method="POST">
                        @csrf
                        <button type="submit" class="app-btn app-btn-outline-danger w-100" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce bon de commande?')">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content app-modal">
            <div class="app-modal-header">
                <h5 class="app-modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="app-modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce bon de commande ?</p>
                <div class="app-alert app-alert-warning">
                    <div class="app-alert-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="app-alert-content">
                        <div class="app-alert-text">Cette action est irréversible.</div>
                    </div>
                </div>
            </div>
            <div class="app-modal-footer">
                <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form action="{{ route('bon-commandes.destroy', $bonCommande) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="app-btn app-btn-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de livraison -->
<div class="modal fade" id="livrerModal" tabindex="-1" aria-labelledby="livrerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content app-modal">
            <div class="app-modal-header">
                <h5 class="app-modal-title" id="livrerModalLabel">
                    <i class="fas fa-truck me-2"></i>Marquer comme livré
                </h5>
                <button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('bon-commandes.livrer', $bonCommande) }}" method="POST">
                @csrf
                <div class="app-modal-body">
                    <p>Veuillez spécifier les quantités livrées pour chaque article :</p>
                    
                    <div class="app-table-responsive">
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Désignation</th>
                                    <th class="text-center">Quantité commandée</th>
                                    <th class="text-center">Quantité livrée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bonCommande->lignes as $index => $ligne)
                                <tr>
                                    <td>
                                        <span class="app-badge app-badge-light">
                                            {{ $ligne->article->reference }}
                                        </span>
                                    </td>
                                    <td>{{ $ligne->article->nom }}</td>
                                    <td class="text-center">
                                        <span class="app-badge app-badge-info">
                                            {{ $ligne->quantite }} {{ $ligne->article->unite_mesure }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="quantite_livree[{{ $index }}]" class="app-form-control text-center" 
                                            min="0" max="{{ $ligne->quantite }}" value="{{ $ligne->quantite }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="app-modal-footer">
                    <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="app-btn app-btn-success">
                        <i class="fas fa-check me-2"></i>Confirmer la livraison
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .no-print,
    .breadcrumb,
    .app-btn,
    .col-md-4 {
        display: none !important;
    }
    
    .col-md-8 {
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .app-card {
        border: none !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    
    body {
        background: white !important;
    }
    
    .app-table {
        font-size: 12px;
    }
    
    .app-badge {
        border: 1px solid #000;
        color: #000 !important;
        background-color: transparent !important;
    }
}
</style>
@endpush
@endsection