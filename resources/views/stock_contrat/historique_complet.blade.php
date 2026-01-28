@extends('layouts.app')

@section('title', 'Historique Complet des Mouvements de Stock')
@section('page-title', 'Historique Complet des Mouvements de Stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item"><a href="{{ route('stock_contrat.index') }}">Stock</a></li>
<li class="breadcrumb-item active">Historique Complet</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-history me-2"></i>Historique Complet des Mouvements de Stock
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('stock_contrat.historique') }}" class="app-btn app-btn-info app-btn-icon me-2">
                    <i class="fas fa-list"></i> Historique Simple
                </a>
                <a href="{{ route('stock_contrat.index') }}" class="app-btn app-btn-secondary app-btn-icon">
                    <i class="fas fa-arrow-left"></i> Retour au stock
                </a>
            </div>
        </div>

        <div class="app-card-body">
            @if($mouvements->count() > 0 || $demandesRavitaillement->count() > 0)
                <div class="timeline">
                    {{-- Mouvements de stock --}}
                    @foreach($mouvements as $mouvement)
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $mouvement->type_mouvement == 'entree' || $mouvement->type_mouvement == 'retour_chantier' ? 'bg-success' : 'bg-warning' }}">
                                <i class="{{ $mouvement->type_mouvement_icone }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <h5 class="timeline-title">
                                        {{ $mouvement->type_mouvement_libelle }}
                                        @if($mouvement->reference_mouvement)
                                            <span class="badge bg-secondary ms-2">{{ $mouvement->reference_mouvement }}</span>
                                        @endif
                                    </h5>
                                    <small class="text-muted">
                                        {{ $mouvement->created_at->format('d/m/Y à H:i') }}
                                        @if($mouvement->user)
                                            par {{ $mouvement->user->nom }}
                                        @endif
                                    </small>
                                </div>
                                <div class="timeline-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Article:</strong> 
                                                {{ $mouvement->stockProjet->article->designation_article ?? 'N/A' }}
                                            </p>
                                            <p class="mb-1"><strong>Référence:</strong> 
                                                {{ $mouvement->stockProjet->article->ref_article ?? 'N/A' }}
                                            </p>
                                            @if($mouvement->stockProjet->contrat)
                                                <p class="mb-1"><strong>Contrat:</strong> 
                                                    {{ $mouvement->stockProjet->contrat->nom_contrat }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><strong>Quantité:</strong></span>
                                                <span class="badge {{ in_array($mouvement->type_mouvement, ['entree', 'retour_chantier']) ? 'bg-success' : 'bg-warning' }}">
                                                    {{ in_array($mouvement->type_mouvement, ['entree', 'retour_chantier']) ? '+' : '-' }}{{ $mouvement->quantite }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><strong>Stock avant:</strong></span>
                                                <span>{{ $mouvement->quantite_avant }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><strong>Stock après:</strong></span>
                                                <span class="fw-bold">{{ $mouvement->quantite_apres }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($mouvement->commentaires)
                                        <div class="mt-2">
                                            <strong>Commentaires:</strong> {{ $mouvement->commentaires }}
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>Date du mouvement: {{ $mouvement->date_mouvement->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Demandes de ravitaillement --}}
                    @foreach($demandesRavitaillement as $demande)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <h5 class="timeline-title">
                                        Demande de ravitaillement #{{ $demande->reference }}
                                        <span class="badge bg-success ms-2">Approuvée</span>
                                    </h5>
                                    <small class="text-muted">
                                        {{ $demande->created_at->format('d/m/Y à H:i') }}
                                        par {{ $demande->demandeur->name ?? 'N/A' }}
                                    </small>
                                </div>
                                <div class="timeline-body">
                                    <p class="mb-2"><strong>Objet:</strong> {{ $demande->objet }}</p>
                                    @if($demande->description)
                                        <p class="mb-2"><strong>Description:</strong> {{ $demande->description }}</p>
                                    @endif
                                    
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Article</th>
                                                    <th>Quantité ajoutée</th>
                                                    <th>Unité</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($demande->lignes as $ligne)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-box text-primary me-2"></i>
                                                                {{ $ligne->article->designation_article ?? 'Article supprimé' }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">+{{ $ligne->quantite_demandee }}</span>
                                                        </td>
                                                        <td>
                                                            {{ $ligne->uniteMesure->ref ?? $ligne->article->uniteMesure->ref ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($mouvements->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $mouvements->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucun mouvement de stock</h4>
                    <p class="text-muted">Aucun mouvement de stock n'a été enregistré pour ce contrat.</p>
                    <a href="{{ route('stock_contrat.index') }}" class="app-btn app-btn-primary">
                        <i class="fas fa-plus me-2"></i>Commencer à gérer le stock
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.timeline-header {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.timeline-title {
    margin: 0;
    font-size: 1.1rem;
    color: #495057;
}
</style>
@endsection