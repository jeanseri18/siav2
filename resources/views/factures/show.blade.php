{{-- Page Show - Détail d'une facture --}}
@extends('layouts.app')

@section('title', 'Détail de la facture #' . $facture->num)
@section('page-title', 'Détail de la facture')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('factures.index') }}">Factures</a></li>
<li class="breadcrumb-item active">Détail</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="container app-fade-in">
    <div class="row">
        <div class="col-md-8">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Facture N° {{ $facture->num }}
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('factures.generatePDF', $facture->id) }}" class="app-btn app-btn-primary app-btn-icon" target="_blank">
                            <i class="fas fa-file-pdf"></i> Télécharger PDF
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="app-fw-bold">Informations générales</h4>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Numéro</strong></td>
                                        <td>{{ $facture->num }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date d'émission</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($facture->date_emission)->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Statut</strong></td>
                                        <td>
                                            @if($facture->statut == 'en attente')
                                                <span class="app-badge app-badge-warning app-badge-pill">
                                                    <i class="fas fa-clock me-1"></i> En attente
                                                </span>
                                            @elseif($facture->statut == 'payée')
                                                <span class="app-badge app-badge-success app-badge-pill">
                                                    <i class="fas fa-check-circle me-1"></i> Payée
                                                </span>
                                            @elseif($facture->statut == 'annulée')
                                                <span class="app-badge app-badge-danger app-badge-pill">
                                                    <i class="fas fa-times-circle me-1"></i> Annulée
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($facture->statut == 'payée' && $facture->date_reglement)
                                        <tr>
                                            <td><strong>Date de règlement</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($facture->date_reglement)->format('d/m/Y') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4 class="app-fw-bold">Montants</h4>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Montant HT</strong></td>
                                        <td>{{ number_format($facture->montant_ht, 2, ',', ' ') }} CFA</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TVA (18%)</strong></td>
                                        <td>{{ number_format($facture->montant_total - $facture->montant_ht, 2, ',', ' ') }} CFA</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Montant Total</strong></td>
                                        <td>{{ number_format($facture->montant_total, 2, ',', ' ') }} CFA</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Montant Réglé</strong></td>
                                        <td>{{ number_format($facture->montant_reglement, 2, ',', ' ') }} CFA</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reste à Régler</strong></td>
                                        <td>
                                            <span class="app-badge app-badge-{{ $facture->reste_a_regler > 0 ? 'warning' : 'success' }} app-badge-pill">
                                                {{ number_format($facture->reste_a_regler, 2, ',', ' ') }} CFA
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="app-fw-bold">Entités liées</h4>
                            <table class="table table-borderless">
                                <tbody>
                                    @if($facture->contrat)
                                        <tr>
                                            <td><strong>Contrat</strong></td>
                                            <td>
                                                <a href="{{ route('contrats.show', $facture->contrat->id) }}">
                                                    {{ $facture->contrat->nom_contrat }} ({{ $facture->contrat->ref_contrat }})
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    
                                    @if($facture->prestation)
                                        <tr>
                                            <td><strong>Prestation</strong></td>
                                            <td>
                                                <a href="{{ route('prestations.show', $facture->prestation->id) }}">
                                                    {{ $facture->prestation->description }} - {{ number_format($facture->prestation->montant, 2, ',', ' ') }} CFA
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    
                                    @if($facture->artisan)
                                        <tr>
                                            <td><strong>Artisan</strong></td>
                                            <td>
                                                <a href="{{ route('artisans.show', $facture->artisan->id) }}">
                                                    {{ $facture->artisan->nom }} ({{ $facture->artisan->telephone }})
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    
                                    @if($facture->num_decompte)
                                        <tr>
                                            <td><strong>Décompte N°</strong></td>
                                            <td>{{ $facture->num_decompte }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($facture->taux_avancement)
                                        <tr>
                                            <td><strong>Taux d'avancement</strong></td>
                                            <td>{{ $facture->taux_avancement }}%</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('factures.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>
                    <a href="{{ route('factures.edit', $facture->id) }}" class="app-btn app-btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('factures.changeStatus', $facture->id) }}" method="POST" class="mb-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-form-group mb-3">
                            <label for="statut" class="app-form-label">Changer le statut</label>
                            <select class="app-form-select" id="statut" name="statut" required>
                                <option value="en attente" {{ $facture->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                <option value="payée" {{ $facture->statut == 'payée' ? 'selected' : '' }}>Payée</option>
                                <option value="annulée" {{ $facture->statut == 'annulée' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        
                        <div class="app-form-group mb-3" id="montant_reglement_group" style="{{ $facture->statut != 'payée' ? 'display: none;' : '' }}">
                            <label for="montant_reglement" class="app-form-label">Montant réglé</label>
                            <input type="number" step="0.01" min="0" class="app-form-control" id="montant_reglement" name="montant_reglement" value="{{ $facture->montant_reglement ?? $facture->montant_total }}">
                        </div>
                        
                        <button type="submit" class="app-btn app-btn-primary app-btn-block">
                            <i class="fas fa-save me-2"></i>Enregistrer le changement
                        </button>
                    </form>
                    
                    <div class="app-d-flex app-flex-column app-gap-2">
                        <a href="{{ route('factures.generatePDF', $facture->id) }}" class="app-btn app-btn-info app-btn-block">
                            <i class="fas fa-file-pdf me-2"></i>Télécharger PDF
                        </a>
                        
                        <button type="button" class="app-btn app-btn-danger app-btn-block" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette facture ?
                <p class="text-danger mt-3">
                    <strong>Attention :</strong> Cette action est irréversible et supprimera définitivement la facture n° {{ $facture->num }}.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('factures.destroy', $facture->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="app-btn app-btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Afficher/masquer le champ de montant réglé en fonction du statut
        $('#statut').change(function() {
            if ($(this).val() === 'payée') {
                $('#montant_reglement_group').show();
            } else {
                $('#montant_reglement_group').hide();
            }
        });
    });
</script>
@endpush
@endsection