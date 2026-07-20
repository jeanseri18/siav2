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
            @if(auth()->user()->hasPermission('transferts.stock_projet'))
            <div class="app-card-actions">
                <a href="{{ route('transferts.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Effectuer un transfert
                </a>
            </div>
            @endif
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
                        <th>Statut</th>
                        <th>Actions</th>
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
                        <td>{{ $transfert->article->nom }}</td>
                        <td class="app-fw-bold">{{ $transfert->quantite }}</td>
                        <td>{{ $transfert->date_transfert }}</td>
                        <td>
                            @if(in_array($transfert->id, $recuIds))
                                <span class="badge bg-success">Reçu</span>
                            @else
                                <span class="badge bg-warning text-dark">En transit</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $pid = session('projet_id');
                                $recu = in_array($transfert->id, $recuIds);
                            @endphp
                            @if($recu)
                                <span class="text-muted"><i class="fas fa-check-circle"></i> Terminé</span>
                            @elseif($pid && (int) $pid === (int) $transfert->id_projet_destination)
                                <div class="dropdown">
                                    <button class="app-btn app-btn-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form action="{{ route('transferts.receptionner', $transfert) }}" method="POST" class="mb-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success" onclick="return confirm('Confirmer la réception de ce transfert ?')">
                                                    <i class="fas fa-check me-2"></i>Réceptionner
                                                </button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('transferts.refuser', $transfert) }}" method="POST" class="mb-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Refuser ce transfert ? Le stock sera rétabli sur le projet source.')">
                                                    <i class="fas fa-times me-2"></i>Refuser
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @elseif($pid && (int) $pid === (int) $transfert->id_projet_source)
                                <div class="dropdown">
                                    <button class="app-btn app-btn-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if(auth()->user()->hasPermission('transferts.edit'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('transferts.edit', $transfert) }}">
                                                <i class="fas fa-edit me-2"></i>Modifier
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        @endif
                                        
                                        <li>
                                            <form action="{{ route('transferts.annuler', $transfert) }}" method="POST" class="mb-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-secondary" onclick="return confirm('Annuler ce transfert et rétablir le stock sur le projet source ?')">
                                                    <i class="fas fa-undo me-2"></i>Retour
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <span class="text-muted small">Sélectionnez le projet <strong>source</strong> ou <strong>destination</strong> pour voir les actions.</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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

@endsection