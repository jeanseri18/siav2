{{-- Page Show - Détails d'une Demande de Cotation --}}
@extends('layouts.app')

@section('title', 'Détails d\'une Demande de Cotation')
@section('page-title', 'Détails d\'une Demande de Cotation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-cotations.index') }}">Demandes de Cotation</a></li>
<li class="breadcrumb-item active">{{ $demandeCotation->reference }}</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="app-card" style="background: var(--primary); color: var(--white); margin-bottom: var(--spacing-lg);">
        <div class="app-card-body">
            <h2 class="app-fw-bold app-mb-3">Détails de la Demande de Cotation</h2>
            <div class="app-d-flex app-gap-3">
                <a href="{{ route('demande-cotations.index') }}" class="app-btn" 
                   style="background: var(--primary-light); color: var(--white); width: 200px;">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
                <a href="javascript:window.print()" class="app-btn" 
                   style="background: var(--primary-light); color: var(--white); width: 200px;">
                    <i class="fas fa-print me-2"></i>Imprimer
                </a>
            </div>
        </div>
    </div>

    <div class="app-card app-hover-shadow" id="printable-content">
        <div class="app-card-body">
            <div class="row">
                <div class="col-md-12 app-text-center app-mb-4">
                    <h3 class="app-fw-bold">DEMANDE DE COTATION</h3>
                    <h4 class="app-fw-medium">{{ $demandeCotation->reference }}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="app-card app-mb-4">
                        <div class="app-card-header">
                            <h3 class="app-card-title">
                                <i class="fas fa-info-circle me-2"></i>Informations Générales
                            </h3>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <table class="app-table">
                                <tr>
                                    <th style="width: 40%;">Référence</th>
                                    <td>{{ $demandeCotation->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de la demande</th>
                                    <td>{{ $demandeCotation->date_demande->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Date d'expiration</th>
                                    <td>{{ $demandeCotation->date_expiration->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Demande d'achat liée</th>
                                    <td>
                                        @if($demandeCotation->demandeAchat)
                                            <a href="{{ route('demande-achats.show', $demandeCotation->demandeAchat) }}" class="app-btn app-btn-link">
                                                {{ $demandeCotation->demandeAchat->reference }}
                                            </a>
                                        @else
                                            <span class="app-badge app-badge-light">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="app-card app-mb-4">
                        <div class="app-card-header">
                            <h3 class="app-card-title">
                                <i class="fas fa-user-check me-2"></i>Suivi et État
                            </h3>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <table class="app-table">
                                <tr>
                                    <th style="width: 40%;">Statut</th>
                                    <td>
                                        @if($demandeCotation->statut == 'en cours')
                                            <span class="app-badge app-badge-warning app-badge-pill">
                                                <i class="fas fa-spinner me-1"></i> En cours
                                            </span>
                                        @elseif($demandeCotation->statut == 'terminée')
                                            <span class="app-badge app-badge-success app-badge-pill">
                                                <i class="fas fa-check-circle me-1"></i> Terminée
                                            </span>
                                        @elseif($demandeCotation->statut == 'annulée')
                                            <span class="app-badge app-badge-danger app-badge-pill">
                                                <i class="fas fa-ban me-1"></i> Annulée
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Créé par</th>
                                    <td>{{ $demandeCotation->user ? $demandeCotation->user->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Créé le</th>
                                    <td>{{ $demandeCotation->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre de fournisseurs</th>
                                    <td>
                                        <span class="app-badge app-badge-info app-badge-pill">
                                            {{ $demandeCotation->fournisseurs->count() }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($demandeCotation->description)
                <div class="app-card app-mb-4">
                    <div class="app-card-header">
                        <h3 class="app-card-title">
                            <i class="fas fa-align-left me-2"></i>Description
                        </h3>
                    </div>
                    <div class="app-card-body">
                        <p>{{ $demandeCotation->description }}</p>
                    </div>
                </div>
            @endif
            
            @if($demandeCotation->conditions_generales)
                <div class="app-card app-mb-4">
                    <div class="app-card-header">
                        <h3 class="app-card-title">
                            <i class="fas fa-gavel me-2"></i>Conditions générales
                        </h3>
                    </div>
                    <div class="app-card-body">
                        <p>{{ $demandeCotation->conditions_generales }}</p>
                    </div>
                </div>
            @endif
            
            <div class="app-card app-mb-4">
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
                                <th>Quantité</th>
                                <th>Unité de mesure</th>
                                <th>Spécifications</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeCotation->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->article ? $ligne->article->reference : 'N/A' }}</td>
                                    <td>{{ $ligne->designation }}</td>
                                    <td class="app-fw-bold">{{ $ligne->quantite }}</td>
                                    <td>{{ $ligne->unite_mesure }}</td>
                                    <td>{{ $ligne->specifications }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="app-card app-mb-4">
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
                                <th>Répondu</th>
                                <th>Date réponse</th>
                                <th>Montant total</th>
                                <th>Retenu</th>
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
                                    <td>
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
                                    <td class="app-fw-bold">{{ $fournisseur->montant_total ? number_format($fournisseur->montant_total, 0, ',', ' ') . ' CFA' : 'N/A' }}</td>
                                    <td>
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
            
            <div class="row app-mt-4 no-print">
                <div class="col-md-12">
                    <div class="app-d-flex app-justify-content-between">
                        <div>
                            @if($demandeCotation->statut == 'en cours')
                                <a href="{{ route('demande-cotations.edit', $demandeCotation) }}" class="app-btn app-btn-warning app-btn-icon">
                                    <i class="fas fa-edit me-2"></i> Modifier
                                </a>
                                <button type="button" class="app-btn app-btn-danger app-btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i> Supprimer
                                </button>
                            @endif
                        </div>
                        <div>
                            @if($demandeCotation->statut == 'en cours')
                                <form action="{{ route('demande-cotations.terminate', $demandeCotation) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="app-btn app-btn-success app-btn-icon" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette demande de cotation?')">
                                        <i class="fas fa-check me-2"></i> Terminer
                                    </button>
                                </form>
                                <form action="{{ route('demande-cotations.cancel', $demandeCotation) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="app-btn app-btn-danger app-btn-icon" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande de cotation?')">
                                        <i class="fas fa-times me-2"></i> Annuler
                                    </button>
                                </form>
                            @endif
                            
                            @if($demandeCotation->statut == 'terminée')
                                @php
                                    $fournisseurRetenu = $demandeCotation->fournisseurs->where('retenu', true)->first();
                                @endphp
                                
                                @if($fournisseurRetenu)
                                    <a href="{{ route('bon-commandes.create', ['fournisseur_id' => $fournisseurRetenu->fournisseur_id]) }}" class="app-btn app-btn-primary app-btn-icon">
                                        <i class="fas fa-shopping-cart me-2"></i> Créer bon de commande
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
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
        <div class="app-modal">
            <div class="app-modal-header">
                <h3 class="app-modal-title" id="responseModalLabel{{ $fournisseur->id }}">
                    <i class="fas fa-reply me-2"></i>Réponse de {{ $fournisseur->fournisseur->nom_raison_sociale }}
                </h3>
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
                    </div>
                    <div class="app-form-group">
                        <label for="montant_total" class="app-form-label">
                            <i class="fas fa-money-bill-wave me-2"></i>Montant total (CFA) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="montant_total" id="montant_total" class="app-form-control" min="0" step="1" required>
                    </div>
                    <div class="app-form-group">
                        <label for="commentaire" class="app-form-label">
                            <i class="fas fa-comment-alt me-2"></i>Commentaire
                        </label>
                        <textarea name="commentaire" id="commentaire" class="app-form-control" rows="3"></textarea>
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
        <div class="app-modal">
            <div class="app-modal-header">
                <h3 class="app-modal-title" id="deleteModalLabel">
                    <i class="fas fa-trash-alt me-2"></i>Confirmer la suppression
                </h3>
<button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="app-modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette demande de cotation ?</p>
                <p class="app-fw-bold">Cette action est irréversible.</p>
            </div>
            <div class="app-modal-footer">
                <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form action="{{ route('demande-cotations.destroy', $demandeCotation) }}" method="POST">
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

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            padding: 0;
            margin: 0;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        
        .app-card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .app-table {
            width: 100%;
        }
        
        .app-badge {
            border: 1px solid #000;
            color: #000 !important;
            background-color: transparent !important;
        }
    }
</style>
@endsection