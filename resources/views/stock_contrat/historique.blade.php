@extends('layouts.app')

@section('title', 'Historique des mouvements de stock')
@section('page-title', 'Historique des mouvements de stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item"><a href="{{ route('stock_contrat.index') }}">Stock</a></li>
<li class="breadcrumb-item active">Historique</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-history me-2"></i>Historique des mouvements de stock
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('stock_contrat.index') }}" class="app-btn app-btn-secondary app-btn-icon">
                    <i class="fas fa-arrow-left"></i> Retour au stock
                </a>
            </div>
        </div>

        <div class="app-card-body">
            @if($demandesRavitaillement->count() > 0)
                <div class="timeline">
                    @foreach($demandesRavitaillement as $demande)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success">
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
                                                                {{ $ligne->article->nom ?? 'Article supprimé' }}
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
                                    
                                    @if($demande->approbateur)
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            Approuvé par {{ $demande->approbateur->name }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun mouvement de stock</h5>
                    <p class="text-muted">Aucune demande de ravitaillement approuvée pour ce contrat.</p>
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
    background: #dee2e6;
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
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.timeline-header {
    border-bottom: 1px solid #dee2e6;
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