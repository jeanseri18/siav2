@extends('layouts.app')

@section('title', 'Liste des Transferts de Stock')
@section('page-title', 'Liste des Transferts de Stock')

@section('breadcrumb')
<li class="breadcrumb-item">Projets</li>
<li class="breadcrumb-item active">Transferts de Stock</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-exchange-alt me-2"></i>Liste des Transferts de Stock
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('transferts.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Effectuer un transfert
                </a>
            </div>
        </div>
        
        @if(session('success'))
        <div class="app-alert app-alert-success">
            <div class="app-alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">{{ session('success') }}</div>
            </div>
            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="app-alert app-alert-danger">
            <div class="app-alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">{{ session('error') }}</div>
            </div>
            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif
        
        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table display">
                <thead>
                    <tr>
                        <th>Projet Source</th>
                        <th>Projet Destination</th>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Date de transfert</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transferts as $transfert)
                    <tr>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-project-diagram text-primary"></i>
                                </div>
                                <span>{{ $transfert->projetSource->nom_projet }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-project-diagram text-success"></i>
                                </div>
                                <span>{{ $transfert->projetDestination->nom_projet }}</span>
                            </div>
                        </td>
                        <td>{{ $transfert->nom_produit }}</td>
                        <td class="app-fw-bold">{{ $transfert->quantite }}</td>
                        <td>{{ $transfert->date_transfert }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Transfert -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content app-modal">
            <div class="app-modal-header">
                <h5 class="app-modal-title" id="transferModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Transférer du Stock
                </h5>
                <button type="button" class="app-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="app-modal-body">
                <form action="{{ route('transferts.store') }}" method="POST" class="app-form" id="transfertForm">
                    @csrf
                    @if(session('projet_id'))
                    <input type="hidden" name="projet_source" value="{{ session('projet_id') }}">
                    @endif
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="projet_source" class="app-form-label">
                                    <i class="fas fa-building me-2"></i>Projet Source
                                </label>
                                <select name="projet_source" id="projet_source" class="app-form-select" required @if(session('projet_id')) disabled @endif>
                                    <option value="">-- Sélectionner le projet source --</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" data-projet-id="{{ $projet->id }}" @if(session('projet_id') == $projet->id) selected @endif>{{ $projet->nom_projet }}</option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">Le projet d'où provient le stock</div>
                            </div>
                        </div>

                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="projet_destination" class="app-form-label">
                                    <i class="fas fa-bullseye me-2"></i>Projet Destination
                                </label>
                                <select name="projet_destination" id="projet_destination" class="app-form-select" required>
                                    <option value="">-- Sélectionner le projet destination --</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" data-projet-id="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">Le projet où le stock sera transféré</div>
                            </div>
                        </div>
                    </div>

                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="article" class="app-form-label">
                                    <i class="fas fa-box me-2"></i>Article
                                </label>
                                <select name="article" id="article" class="app-form-select" required>
                                    <option value="">-- Sélectionner un article --</option>
                                    @foreach($articles as $article)
                                    <option value="{{ $article->id }}">{{ $article->nom }}</option>
                                    @endforeach
                                </select>
                                <div class="app-form-text">L'article à transférer</div>
                            </div>
                        </div>

                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="quantite" class="app-form-label">
                                <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                            </label>
                                <input type="number" name="quantite" id="quantite" class="app-form-control" min="1" required>
                                <div class="app-form-text">Nombre d'unités à transférer</div>
                            </div>
                        </div>
                    </div>

                    <div class="app-form-group">
                        <label for="date_transfert" class="app-form-label">
                            <i class="fas fa-calendar-alt me-2"></i>Date de Transfert
                        </label>
                        <input type="date" name="date_transfert" id="date_transfert" class="app-form-control" value="{{ date('Y-m-d') }}" required>
                        <div class="app-form-text">Date à laquelle le transfert est effectué</div>
                    </div>
                </form>
            </div>
            <div class="app-modal-footer">
                <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="app-btn app-btn-primary" onclick="document.getElementById('transfertForm').submit()">
                    <i class="fas fa-paper-plane me-2"></i>Effectuer le Transfert
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projetSource = document.getElementById('projet_source');
    const projetDestination = document.getElementById('projet_destination');
    
    // Fonction pour filtrer les options
    function filterProjetOptions() {
        const sourceValue = projetSource.value;
        const destinationValue = projetDestination.value;
        
        // Réinitialiser toutes les options (les rendre visibles)
        Array.from(projetSource.options).forEach(option => {
            if (option.value !== '') {
                option.style.display = '';
                option.disabled = false;
            }
        });
        
        Array.from(projetDestination.options).forEach(option => {
            if (option.value !== '') {
                option.style.display = '';
                option.disabled = false;
            }
        });
        
        // Cacher le projet source dans la liste destination
        if (sourceValue) {
            Array.from(projetDestination.options).forEach(option => {
                if (option.value === sourceValue) {
                    option.style.display = 'none';
                    option.disabled = true;
                }
            });
            
            // Si le projet destination sélectionné est le même que la source, le réinitialiser
            if (destinationValue === sourceValue) {
                projetDestination.value = '';
            }
        }
        
        // Cacher le projet destination dans la liste source
        if (destinationValue) {
            Array.from(projetSource.options).forEach(option => {
                if (option.value === destinationValue) {
                    option.style.display = 'none';
                    option.disabled = true;
                }
            });
            
            // Si le projet source sélectionné est le même que la destination, le réinitialiser
            if (sourceValue === destinationValue) {
                projetSource.value = '';
            }
        }
    }
    
    // Appliquer le filtrage immédiatement si un projet source est sélectionné
    if (projetSource.value) {
        filterProjetOptions();
    }
    
    // Écouter les changements sur les deux sélecteurs (uniquement si projet source n'est pas grisé)
    if (!projetSource.disabled) {
        projetSource.addEventListener('change', filterProjetOptions);
    }
    projetDestination.addEventListener('change', filterProjetOptions);
    
    // Réinitialiser le formulaire quand le modal est fermé
    const transferModal = document.getElementById('transferModal');
    transferModal.addEventListener('hidden.bs.modal', function() {
        // Réinitialiser le formulaire
        transferModal.querySelector('form').reset();
        
        // Réafficher toutes les options (uniquement si projet source n'est pas grisé)
        if (!projetSource.disabled) {
            Array.from(projetSource.options).forEach(option => {
                option.style.display = '';
                option.disabled = false;
            });
        }
        
        Array.from(projetDestination.options).forEach(option => {
            option.style.display = '';
            option.disabled = false;
        });
    });
    
    // Définir la date du jour par défaut
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_transfert').value = today;
});
</script>
@endpush

@endsection