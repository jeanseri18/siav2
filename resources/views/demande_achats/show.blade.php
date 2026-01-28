{{-- Page Show - Détails d'une Demande d'Achat --}}
@extends('layouts.app')

@section('title', 'Détails d\'une Demande d\'Achat')
@section('page-title', 'Détails d\'une Demande d\'Achat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-achats.index') }}">Demandes d'Achat</a></li>
<li class="breadcrumb-item active">{{ $demandeAchat->reference }}</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="app-card" style="background: var(--primary); color: var(--white); margin-bottom: var(--spacing-lg);">
        <div class="app-card-body">
            <h2 class="app-fw-bold app-mb-3">Détails de la Demande d'Achat</h2>
            <div class="app-d-flex app-gap-3">
                <a href="{{ route('demande-achats.index') }}" class="app-btn" 
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
                    <h3 class="app-fw-bold">DEMANDE D'ACHAT</h3>
                    <h4 class="app-fw-medium">{{ $demandeAchat->reference }}</h4>
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
                                    <td>{{ $demandeAchat->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de la demande</th>
                                    <td>{{ $demandeAchat->date_demande->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Date besoin</th>
                                    <td>{{ $demandeAchat->date_besoin ? $demandeAchat->date_besoin->format('d/m/Y') : 'Non spécifiée' }}</td>
                                </tr>
                                <tr>
                                    <th>Projet</th>
                                    <td>{{ $demandeAchat->projet ? $demandeAchat->projet->nom_projet : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Priorité</th>
                                    <td>
                                        @if($demandeAchat->priorite == 'basse')
                                            <span class="app-badge app-badge-success app-badge-pill">
                                                <i class="fas fa-arrow-down me-1"></i> Basse
                                            </span>
                                        @elseif($demandeAchat->priorite == 'normale')
                                            <span class="app-badge app-badge-info app-badge-pill">
                                                <i class="fas fa-minus me-1"></i> Normale
                                            </span>
                                        @elseif($demandeAchat->priorite == 'haute')
                                            <span class="app-badge app-badge-warning app-badge-pill">
                                                <i class="fas fa-arrow-up me-1"></i> Haute
                                            </span>
                                        @elseif($demandeAchat->priorite == 'urgente')
                                            <span class="app-badge app-badge-danger app-badge-pill">
                                                <i class="fas fa-exclamation-circle me-1"></i> Urgente
                                            </span>
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
                                <i class="fas fa-user-check me-2"></i>Validation et Suivi
                            </h3>
                        </div>
                        <div class="app-card-body app-table-responsive">
                            <table class="app-table">
                                <tr>
                                    <th style="width: 40%;">Statut</th>
                                    <td>
                                        @if($demandeAchat->statut == 'en attente')
                                            <span class="app-badge app-badge-warning app-badge-pill">
                                                <i class="fas fa-clock me-1"></i> En attente
                                            </span>
                                        @elseif($demandeAchat->statut == 'approuvée')
                                            <span class="app-badge app-badge-success app-badge-pill">
                                                <i class="fas fa-check-circle me-1"></i> Approuvée
                                            </span>
                                        @elseif($demandeAchat->statut == 'rejetée')
                                            <span class="app-badge app-badge-danger app-badge-pill">
                                                <i class="fas fa-times-circle me-1"></i> Rejetée
                                            </span>
                                        @elseif($demandeAchat->statut == 'traitée')
                                            <span class="app-badge app-badge-secondary app-badge-pill">
                                                <i class="fas fa-check me-1"></i> Traitée
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Demandeur</th>
                                    <td>{{ $demandeAchat->user ? $demandeAchat->user->nom : 'N/A' }}</td>
                                </tr>
                                @if($demandeAchat->statut == 'approuvée' || $demandeAchat->statut == 'rejetée')
                                    <tr>
                                        <th>Approuvé/Rejeté par</th>
                                        <td>{{ $demandeAchat->approbateur ? $demandeAchat->approbateur->name : 'N/A' }}</td>
                                    </tr>
                                @endif
                                @if($demandeAchat->statut == 'rejetée')
                                    <tr>
                                        <th>Motif du rejet</th>
                                        <td>{{ $demandeAchat->motif_rejet }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Créé le</th>
                                    <td>{{ $demandeAchat->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($demandeAchat->description)
                <div class="app-card app-mb-4">
                    <div class="app-card-header">
                        <h3 class="app-card-title">
                            <i class="fas fa-align-left me-2"></i>Description
                        </h3>
                    </div>
                    <div class="app-card-body">
                        <p>{{ $demandeAchat->description }}</p>
                    </div>
                </div>
            @endif
            
            <div class="app-card app-mb-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-boxes me-2"></i>Articles demandés
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
                                <th>Prix estimé</th>
                                <th>Montant estimé</th>
                                <th>Spécifications</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeAchat->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->article ? $ligne->article->reference : 'N/A' }}</td>
                                    <td>{{ $ligne->designation }}</td>
                                    <td class="app-fw-bold">{{ $ligne->quantite }}</td>
                                    <td>{{ $ligne->unite_mesure }}</td>
                                    <td>{{ $ligne->prix_estime ? number_format($ligne->prix_estime, 0, ',', ' ') . ' CFA' : 'N/A' }}</td>
                                    <td class="app-fw-bold">{{ $ligne->prix_estime ? number_format($ligne->prix_estime * $ligne->quantite, 0, ',', ' ') . ' CFA' : 'N/A' }}</td>
                                    <td>{{ $ligne->specifications }}</td>
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
                            @if($demandeAchat->statut == 'en attente')
                                <a href="{{ route('demande-achats.edit', $demandeAchat) }}" class="app-btn app-btn-warning app-btn-icon">
                                    <i class="fas fa-edit me-2"></i> Modifier
                                </a>
                                <button type="button" class="app-btn app-btn-danger app-btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i> Supprimer
                                </button>
                            @endif
                        </div>
                        <div>
                            @if($demandeAchat->statut == 'en attente' && in_array(Auth::user()->role, ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg']))
                                <form action="{{ route('demande-achats.approve', $demandeAchat) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="app-btn app-btn-success app-btn-icon" onclick="return confirm('Êtes-vous sûr de vouloir approuver cette demande?')">
                                        <i class="fas fa-check me-2"></i> Approuver
                                    </button>
                                </form>
                                <button type="button" class="app-btn app-btn-danger app-btn-icon" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="fas fa-times me-2"></i> Rejeter
                                </button>
                            @endif
                            
                            @if($demandeAchat->statut == 'approuvée')
                                <a href="{{ route('demande-cotations.create', ['demande_achat_id' => $demandeAchat->id]) }}" class="app-btn app-btn-primary app-btn-icon">
                                    <i class="fas fa-file-invoice me-2"></i> Créer demande de cotation
                                </a>
                                <a href="{{ route('bon-commandes.create', ['demande_achat_id' => $demandeAchat->id]) }}" class="app-btn app-btn-info app-btn-icon">
                                    <i class="fas fa-shopping-cart me-2"></i> Créer bon de commande
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                <p>Êtes-vous sûr de vouloir supprimer cette demande d'achat ?</p>
                <p class="app-fw-bold">Cette action est irréversible.</p>
            </div>
            <div class="app-modal-footer">
                <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form action="{{ route('demande-achats.destroy', $demandeAchat) }}" method="POST">
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

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="app-modal">
            <div class="app-modal-header">
                <h3 class="app-modal-title" id="rejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i>Rejeter la demande
                </h3>
                <button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('demande-achats.reject', $demandeAchat) }}" method="POST">
                @csrf
                <div class="app-modal-body">
                    <div class="app-form-group">
                        <label for="motif_rejet" class="app-form-label">
                            <i class="fas fa-comment-alt me-2"></i>Motif du rejet <span class="text-danger">*</span>
                        </label>
                        <textarea name="motif_rejet" id="motif_rejet" class="app-form-control" rows="3" required></textarea>
                        <div class="app-form-text">Veuillez indiquer la raison du rejet de cette demande.</div>
                    </div>
                </div>
                <div class="app-modal-footer">
                    <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="app-btn app-btn-danger">
                        <i class="fas fa-ban me-2"></i>Rejeter
                    </button>
                </div>
            </form>
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