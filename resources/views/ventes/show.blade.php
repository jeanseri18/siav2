{{-- Page Show - Détails de la Vente --}}
@extends('layouts.app')

@section('title', 'Détails de la vente #' . $vente->id)
@section('page-title', 'Détails de la vente #' . $vente->id)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('ventes.index') }}">Ventes</a></li>
<li class="breadcrumb-item active">Détails #{{ $vente->id }}</li>
@endsection

@section('content')
@include('sublayouts.vente')

<div class="container app-fade-in">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-receipt me-2"></i>Vente #{{ str_pad($vente->id, 4, '0', STR_PAD_LEFT) }}
                    </h2>
                    <div class="app-card-actions">
                        @php
                            $statutClass = '';
                            $statutIcon = '';
                            switch($vente->statut) {
                                case 'Payée':
                                    $statutClass = 'success';
                                    $statutIcon = 'check-circle';
                                    break;
                                case 'En attente':
                                    $statutClass = 'warning';
                                    $statutIcon = 'clock';
                                    break;
                                case 'Annulée':
                                    $statutClass = 'danger';
                                    $statutIcon = 'times-circle';
                                    break;
                                default:
                                    $statutClass = 'secondary';
                                    $statutIcon = 'question-circle';
                            }
                        @endphp
                        <span class="app-badge app-badge-{{ $statutClass }} app-badge-pill app-badge-lg">
                            <i class="fas fa-{{ $statutIcon }} me-1"></i> {{ $vente->statut }}
                        </span>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-user me-2"></i>Client
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $vente->client->prenoms }}
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Date de création
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $vente->created_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles vendus -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-boxes me-2"></i>Articles vendus
                    </h3>
                </div>
                <div class="app-card-body app-table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th class="text-center">Quantité</th>
                                <th class="text-end">Prix Unitaire</th>
                                <th class="text-end">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vente->articles as $article)
                            <tr>
                                <td>
                                    <div class="app-d-flex app-align-items-center app-gap-2">
                                        <div class="item-icon">
                                            <i class="fas fa-box text-primary"></i>
                                        </div>
                                        <span>{{ $article->nom }}</span>
                                    </div>
                                </td>
                                <td class="text-center app-fw-bold">
                                    <span class="app-badge app-badge-light">
                                        {{ $article->pivot->quantite }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    {{ number_format($article->pivot->prix_unitaire, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-end app-fw-bold text-success">
                                    {{ number_format($article->pivot->sous_total, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="3" class="text-end app-fw-bold">
                                    <i class="fas fa-calculator me-2"></i>Total de la vente :
                                </td>
                                <td class="text-end app-fw-bold text-success h5">
                                    {{ number_format($vente->total, 0, ',', ' ') }} FCFA
                                </td>
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
                            <i class="fas fa-shopping-basket me-2"></i>Articles
                        </span>
                        <span class="app-fw-bold">{{ $vente->articles->count() }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantité totale
                        </span>
                        <span class="app-fw-bold">{{ $vente->articles->sum('pivot.quantite') }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center">
                        <span class="text-muted">
                            <i class="fas fa-coins me-2"></i>Montant total
                        </span>
                        <span class="app-fw-bold text-success h5">
                            {{ number_format($vente->total, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h3>
                </div>
                <div class="app-card-body app-d-grid app-gap-2">
                    @if($vente->statut !== 'Payée')
                    <form action="{{ route('ventes.updateStatus', $vente->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="app-btn app-btn-success w-100">
                            <i class="fas fa-check me-2"></i>Valider la vente
                        </button>
                    </form>
                    @else
                    <button class="app-btn app-btn-secondary w-100" disabled>
                        <i class="fas fa-check-circle me-2"></i>Vente déjà validée
                    </button>
                    @endif
                    
                    <button class="app-btn app-btn-info w-100" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer la facture
                    </button>
                    
                    <a href="{{ route('ventes.index') }}" class="app-btn app-btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .app-card-actions,
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
    }
    
    body {
        background: white !important;
    }
}
</style>
@endpush
@endsection