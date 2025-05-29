{{-- Page Show - Détails d'une Demande de Cotation --}}
@extends('layouts.app')

@section('title', 'Demande de Cotation ' . $demandeCotation->reference)
@section('page-title', 'Demande de Cotation ' . $demandeCotation->reference)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-cotations.index') }}">Demandes de Cotation</a></li>
<li class="breadcrumb-item active">{{ $demandeCotation->reference }}</li>
@endsection

@section('content')

<div class="container app-fade-in" id="printable-content">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <!-- En-tête de la demande -->
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-invoice me-2"></i>DEMANDE DE COTATION
                    </h2>
                    <div class="app-card-actions">
                        @php
                            $statutClass = '';
                            $statutIcon = '';
                            switch($demandeCotation->statut) {
                                case 'en cours':
                                    $statutClass = 'warning';
                                    $statutIcon = 'spinner';
                                    break;
                                case 'terminée':
                                    $statutClass = 'success';
                                    $statutIcon = 'check-circle';
                                    break;
                                case 'annulée':
                                    $statutClass = 'danger';
                                    $statutIcon = 'ban';
                                    break;
                                default:
                                    $statutClass = 'secondary';
                                    $statutIcon = 'question-circle';
                            }
                        @endphp
                        <span class="app-badge app-badge-{{ $statutClass }} app-badge-pill app-badge-lg">
                            <i class="fas fa-{{ $statutIcon }} me-1"></i> {{ ucfirst($demandeCotation->statut) }}
                        </span>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <div class="text-center mb-4">
                        <h3 class="app-fw-bold">{{ $demandeCotation->reference }}</h3>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-hashtag me-2"></i>Référence
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $demandeCotation->reference }}
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-calendar me-2"></i>Date de la demande
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $demandeCotation->date_demande->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-calendar-times me-2"></i>Date d'expiration
                                </label>
                                <div class="app-form-control bg-light">
                                    @php
                                        $isExpired = $demandeCotation->date_expiration->isPast();
                                        $isNearExpiry = $demandeCotation->date_expiration->diffInDays(now()) <= 3;
                                    @endphp
                                    <span class="app-badge app-badge-{{ $isExpired ? 'danger' : ($isNearExpiry ? 'warning' : 'light') }}">
                                        {{ $demandeCotation->date_expiration->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-user me-2"></i>Créé par
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $demandeCotation->user ? $demandeCotation->user->name : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-shopping-cart me-2"></i>Demande d'achat liée
                                </label>
                                <div class="app-form-control bg-light">
                                    @if($demandeCotation->demandeAchat)
                                        <a href="{{ route('demande-achats.show', $demandeCotation->demandeAchat) }}" class="text-decoration-none">
                                            {{ $demandeCotation->demandeAchat->reference }}
                                        </a>
                                    @else
                                        <span class="app-badge app-badge-light">N/A</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-calendar-plus me-2"></i>Créé le
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $demandeCotation->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($demandeCotation->description)
            <!-- Description -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-align-left me-2"></i>Description
                    </h3>
                </div>
                <div class="app-card-body">
                    <p class="mb-0">{{ $demandeCotation->description }}</p>
                </div>
            </div>
            @endif
            
            @if($demandeCotation->conditions_generales)
            <!-- Conditions générales -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-gavel me-2"></i>Conditions générales
                    </h3>
                </div>
                <div class="app-card-body">
                    <p class="mb-0">{{ $demandeCotation->conditions_generales }}</p>
                </div>
            </div>
            @endif
            
            <!-- Articles demandés -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-box me-2"></i>Articles demandés
                    </h3>
                </div>
                <div class="app-card-body app-table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Désignation</th>
                                <th class="text-center">Quantité</th>
                                <th>Unité de mesure</th>
                                <th>Spécifications</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeCotation->lignes as $ligne)
                            <tr>
                                <td>
                                    <span class="app-badge app-badge-light">
                                        {{ $ligne->article ? $ligne->article->reference : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="app-d-flex app-align-items-center app-gap-2">
                                        <div class="item-icon">
                                            <i class="fas fa-box text-primary"></i>
                                        </div>
                                        <span>{{ $ligne->designation }}</span>
                                    </div>
                                </td>
                                <td class="text-center app-fw-bold">
                                    <span class="app-badge app-badge-info">
                                        {{ $ligne->quantite }}
                                    </span>
                                </td>
                                <td>{{ $ligne->unite_mesure }}</td>
                                <td>{{ $ligne->specifications ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Fournisseurs consultés -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-truck me-2"></i>Fournisseurs consultés
                    </h3>
                </div>
                <div class="app-card-body app-table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Fournisseur</th>
                                <th class="text-center">Répondu</th>
                                <th>Date réponse</th>
                                <th class="text-end">Montant total</th>
                                <th class="text-center">Retenu</th>
                                <th class="no-print" style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeCotation->fournisseurs as $fournisseur)
                            <tr>
                                <td>
                                    <div class="app-d-flex app-align-items-center app-gap-2">
                                        <div class="item-icon">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <span>{{ $fournisseur->fournisseur->nom_raison_sociale }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($fournisseur->repondu)
                                        <span class="app-badge app-badge-success app-badge-pill">
                                            <i class="fas fa-check me-1"></i> Oui
                                        </span>
                                    @else
                                        <span class="app-badge app-badge-danger app-badge-pill">
                                            <i class="fas fa-times me-1"></i> Non
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $fournisseur->date_reponse ? $fournisseur->date_reponse->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-end app-fw-bold text-success">
                                    @if($fournisseur->montant_total)
                                        <i class="fas fa-coins me-1"></i>
                                        {{ number_format($fournisseur->montant_total, 0, ',', ' ') }} FCFA
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($fournisseur->retenu)
                                        <span class="app-badge app-badge-success app-badge-pill">
                                            <i class="fas fa-check me-1"></i> Oui
                                        </span>
                                    @else
                                        <span class="app-badge app-badge-secondary app-badge-pill">
                                            <i class="fas fa-minus me-1"></i> Non
                                        </span>
                                    @endif
                                </td>
                                <td class="no-print">
                                    @if($demandeCotation->statut == 'en cours')
                                        @if(!$fournisseur->repondu)
                                            <button type="button" class="app-btn app-btn-primary app-btn-sm" data-bs-toggle="modal" data-bs-target="#responseModal{{ $fournisseur->id }}">
                                                <i class="fas fa-reply me-1"></i> Enregistrer réponse
                                            </button>
                                        @else
                                            @if(!$fournisseur->retenu)
                                                <form action="{{ route('demande-cotations.select-fournisseur', ['demandeCotation' => $demandeCotation->id, 'fournisseurDemandeCotation' => $fournisseur->id]) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="app-btn app-btn-success app-btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir retenir ce fournisseur?')">
                                                        <i class="fas fa-check me-1"></i> Retenir
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
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
                            <i class="fas fa-box me-2"></i>Articles
                        </span>
                        <span class="app-fw-bold">{{ $demandeCotation->lignes->count() }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-truck me-2"></i>Fournisseurs
                        </span>
                        <span class="app-fw-bold">{{ $demandeCotation->fournisseurs->count() }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-reply me-2"></i>Réponses reçues
                        </span>
                        <span class="app-fw-bold">{{ $demandeCotation->fournisseurs->where('repondu', true)->count() }}</span>
                    </div>
                    @php
                        $fournisseurRetenu = $demandeCotation->fournisseurs->where('retenu', true)->first();
                    @endphp
                    @if($fournisseurRetenu)
                    <div class="app-d-flex app-justify-content-between app-align-items-center">
                        <span class="text-muted">
                            <i class="fas fa-check-circle me-2"></i>Fournisseur retenu
                        </span>
                        <span class="app-fw-bold text-success">{{ $fournisseurRetenu->fournisseur->nom }}</span>
                    </div>
                    @endif
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
                    <a href="{{ route('demande-cotations.index') }}" class="app-btn app-btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>
                    
                    <button class="app-btn app-btn-info w-100" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                    
                    @if($demandeCotation->statut == 'en cours')
                    <a href="{{ route('demande-cotations.edit', $demandeCotation) }}" class="app-btn app-btn-warning w-100">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    
                    <form action="{{ route('demande-cotations.terminate', $demandeCotation) }}" method="POST">
                        @csrf
                        <button type="submit" class="app-btn app-btn-success w-100" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette demande de cotation?')">
                            <i class="fas fa-check me-2"></i>Terminer
                        </button>
                    </form>
                    
                    <button type="button" class="app-btn app-btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                    
                    <form action="{{ route('demande-cotations.cancel', $demandeCotation) }}" method="POST">
                        @csrf
                        <button type="submit" class="app-btn app-btn-outline-danger w-100" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande de cotation?')">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                    </form>
                    @endif
                    
                    @if($demandeCotation->statut == 'terminée' && $fournisseurRetenu)
                    <a href="{{ route('bon-commandes.create', ['fournisseur_id' => $fournisseurRetenu->fournisseur_id]) }}" class="app-btn app-btn-primary w-100">
                        <i class="fas fa-shopping-cart me-2"></i>Créer bon de commande
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals pour chaque fournisseur -->
@foreach($demandeCotation->fournisseurs as $fournisseur)
<!-- Modal pour enregistrer la réponse du fournisseur -->
<div class="modal fade" id="responseModal{{ $fournisseur->id }}" tabindex="-1" aria-labelledby="responseModalLabel{{ $fournisseur->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content app-modal">
            <div class="app-modal-header">
                <h5 class="app-modal-title" id="responseModalLabel{{ $fournisseur->id }}">
                    <i class="fas fa-reply me-2"></i>Réponse de {{ $fournisseur->fournisseur->nom_raison_sociale }}
                </h5>
                <button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('demande-cotations.save-fournisseur-response', ['demandeCotation' => $demandeCotation->id, 'fournisseurDemandeCotation' => $fournisseur->id]) }}" method="POST">
                @csrf
                <div class="app-modal-body">
                    <div class="app-form-group">
                        <label for="date_reponse" class="app-form-label">
                            <i class="fas fa-calendar me-2"></i>Date de réponse <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date_reponse" id="date_reponse" class="app-form-control" value="{{ date('Y-m-d') }}" required>
                        <div class="app-form-text">Date à laquelle le fournisseur a répondu</div>
                    </div>
                    <div class="app-form-group">
                        <label for="montant_total" class="app-form-label">
                            <i class="fas fa-money-bill-wave me-2"></i>Montant total (FCFA) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="montant_total" id="montant_total" class="app-form-control" min="0" step="1" required placeholder="Montant en FCFA">
                        <div class="app-form-text">Montant total proposé par le fournisseur</div>
                    </div>
                    <div class="app-form-group">
                        <label for="commentaire" class="app-form-label">
                            <i class="fas fa-comment-alt me-2"></i>Commentaire
                        </label>
                        <textarea name="commentaire" id="commentaire" class="app-form-control" rows="3" placeholder="Commentaire sur la réponse..."></textarea>
                        <div class="app-form-text">Commentaire ou observations</div>
                    </div>
                </div>
                <div class="app-modal-footer">
                    <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

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
                <p>Êtes-vous sûr de vouloir supprimer cette demande de cotation ?</p>
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
                <form action="{{ route('demande-cotations.destroy', $demandeCotation) }}" method="POST" style="display: inline;">
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