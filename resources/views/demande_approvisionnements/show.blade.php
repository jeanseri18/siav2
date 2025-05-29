{{-- Page Show - Détails d'une Demande d'Approvisionnement --}}
@extends('layouts.app')

@section('title', 'Détails d\'une Demande d\'Approvisionnement')
@section('page-title', 'Détails d\'une Demande d\'Approvisionnement')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('demande-approvisionnements.index') }}">Demandes d'Approvisionnement</a></li>
<li class="breadcrumb-item active">{{ $demandeApprovisionnement->reference }}</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="app-card" style="background: var(--primary); color: var(--white); margin-bottom: var(--spacing-lg);">
        <div class="app-card-body">
            <h2 class="app-fw-bold app-mb-3">Détails de la Demande d'Approvisionnement</h2>
            <div class="app-d-flex app-gap-3">
                <a href="{{ route('demande-approvisionnements.index') }}" class="app-btn" 
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
                    <h3 class="app-fw-bold">DEMANDE D'APPROVISIONNEMENT</h3>
                    <h4 class="app-fw-medium">{{ $demandeApprovisionnement->reference }}</h4>
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
                                    <td>{{ $demandeApprovisionnement->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Date de la demande</th>
                                    <td>{{ $demandeApprovisionnement->date_demande->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Projet</th>
                                    <td>{{ $demandeApprovisionnement->projet ? $demandeApprovisionnement->projet->nom_projet : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Demandeur</th>
                                    <td>{{ $demandeApprovisionnement->user ? $demandeApprovisionnement->user->name : 'N/A' }}</td>
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
                                        @if($demandeApprovisionnement->statut == 'en attente')
                                            <span class="app-badge app-badge-warning app-badge-pill">
                                                <i class="fas fa-clock me-1"></i> En attente
                                            </span>
                                        @elseif($demandeApprovisionnement->statut == 'approuvée')
                                            <span class="app-badge app-badge-success app-badge-pill">
                                                <i class="fas fa-check-circle me-1"></i> Approuvée
                                            </span>
                                        @elseif($demandeApprovisionnement->statut == 'rejetée')
                                            <span class="app-badge app-badge-danger app-badge-pill">
                                                <i class="fas fa-times-circle me-1"></i> Rejetée
                                            </span>
                                        @elseif($demandeApprovisionnement->statut == 'terminée')
                                            <span class="app-badge app-badge-secondary app-badge-pill">
                                                <i class="fas fa-check me-1"></i> Terminée
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @if($demandeApprovisionnement->statut == 'approuvée' || $demandeApprovisionnement->statut == 'rejetée')
                                    <tr>
                                        <th>Approuvé/Rejeté par</th>
                                        <td>{{ $demandeApprovisionnement->approbateur ? $demandeApprovisionnement->approbateur->name : 'N/A' }}</td>
                                    </tr>
                                @endif
                                @if($demandeApprovisionnement->statut == 'rejetée')
                                    <tr>
                                        <th>Motif du rejet</th>
                                        <td>{{ $demandeApprovisionnement->motif_rejet }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Créé le</th>
                                    <td>{{ $demandeApprovisionnement->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($demandeApprovisionnement->description)
                <div class="app-card app-mb-4">
                    <div class="app-card-header">
                        <h3 class="app-card-title">
                            <i class="fas fa-align-left me-2"></i>Description
                        </h3>
                    </div>
                    <div class="app-card-body">
                        <p>{{ $demandeApprovisionnement->description }}</p>
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
                                <th>Quantité demandée</th>
                                @if($demandeApprovisionnement->statut == 'approuvée')
                                    <th>Quantité approuvée</th>
                                @endif
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandeApprovisionnement->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->article->reference }}</td>
                                    <td>{{ $ligne->article->nom }}</td>
                                    <td class="app-fw-bold">{{ $ligne->quantite_demandee }} {{ $ligne->article->unite_mesure }}</td>
                                    @if($demandeApprovisionnement->statut == 'approuvée')
                                        <td class="app-fw-bold">{{ $ligne->quantite_approuvee }} {{ $ligne->article->unite_mesure }}</td>
                                    @endif
                                    <td>{{ $ligne->commentaire }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($demandeApprovisionnement->statut == 'en attente')
                <div class="row app-mt-4 no-print">
                    <div class="col-md-12">
                        <div class="app-d-flex app-justify-content-between">
                            <div>
                                <a href="{{ route('demande-approvisionnements.edit', $demandeApprovisionnement) }}" class="app-btn app-btn-warning app-btn-icon">
                                    <i class="fas fa-edit me-2"></i> Modifier
                                </a>
                                <button type="button" class="app-btn app-btn-danger app-btn-icon" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i> Supprimer
                                </button>
                            </div>
                            <div>
                                <button type="button" class="app-btn app-btn-success app-btn-icon" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="fas fa-check me-2"></i> Approuver
                                </button>
                                <button type="button" class="app-btn app-btn-danger app-btn-icon" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="fas fa-times me-2"></i> Rejeter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
                <p>Êtes-vous sûr de vouloir supprimer cette demande d'approvisionnement ?</p>
                <p class="app-fw-bold">Cette action est irréversible.</p>
            </div>
            <div class="app-modal-footer">
                <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form action="{{ route('demande-approvisionnements.destroy', $demandeApprovisionnement) }}" method="POST">
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

<!-- Modal d'approbation -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="app-modal">
            <div class="app-modal-header">
                <h3 class="app-modal-title" id="approveModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Approuver la demande
                </h3>
                <button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('demande-approvisionnements.approve', $demandeApprovisionnement) }}" method="POST">
                @csrf
                <div class="app-modal-body">
                    <p>Veuillez spécifier les quantités approuvées pour chaque article :</p>
                    
                    <div class="app-table-responsive">
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Désignation</th>
                                    <th>Quantité demandée</th>
                                    <th>Quantité approuvée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($demandeApprovisionnement->lignes as $index => $ligne)
                                    <tr>
                                        <td>{{ $ligne->article->reference }}</td>
                                        <td>{{ $ligne->article->nom }}</td>
                                        <td>{{ $ligne->quantite_demandee }} {{ $ligne->article->unite_mesure }}</td>
                                        <td>
                                            <input type="number" name="quantite_approuvee[{{ $index }}]" class="app-form-control" 
                                                min="0" max="{{ $ligne->quantite_demandee }}" value="{{ $ligne->quantite_demandee }}">
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
                        <i class="fas fa-check me-2"></i>Approuver
                    </button>
                </div>
            </form>
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
            <form action="{{ route('demande-approvisionnements.reject', $demandeApprovisionnement) }}" method="POST">
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