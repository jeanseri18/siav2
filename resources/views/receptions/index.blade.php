@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-truck"></i>
                        Gestion des Réceptions
                    </h3>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($bonCommandes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Référence</th>
                                        <th>Fournisseur</th>
                                        <th>Date Commande</th>
                                        <th>Date Livraison Prévue</th>
                                        <th>Statut</th>
                                        <th>Progression</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bonCommandes as $bonCommande)
                                        @php
                                            $totalQuantite = $bonCommande->lignes->sum('quantite');
                                            $totalRecue = $bonCommande->lignes->sum('quantite_recue');
                                            $pourcentage = $totalQuantite > 0 ? round(($totalRecue / $totalQuantite) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $bonCommande->reference }}</strong>
                                            </td>
                                            <td>{{ $bonCommande->fournisseur->nom ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($bonCommande->date_livraison_prevue)
                                                    {{ \Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->format('d/m/Y') }}
                                                    @if(\Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->isPast())
                                                        <span class="badge bg-warning ms-1">En retard</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Non définie</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pourcentage == 0)
                                                    <span class="badge bg-secondary">En attente</span>
                                                @elseif($pourcentage < 100)
                                                    <span class="badge bg-warning">Partielle</span>
                                                @else
                                                    <span class="badge bg-success">Complète</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $pourcentage == 100 ? 'bg-success' : ($pourcentage > 0 ? 'bg-warning' : 'bg-secondary') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $pourcentage }}%" 
                                                         aria-valuenow="{{ $pourcentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ $pourcentage }}%
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $totalRecue }}/{{ $totalQuantite }} articles</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('receptions.show', $bonCommande->id) }}" 
                                                       class="btn btn-info btn-sm" 
                                                       title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($pourcentage < 100)
                                                        <a href="{{ route('receptions.create', $bonCommande->id) }}" 
                                                           class="btn btn-success btn-sm" 
                                                           title="Effectuer réception">
                                                            <i class="fas fa-truck-loading"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    <a href="{{ route('receptions.history', $bonCommande->id) }}" 
                                                       class="btn btn-secondary btn-sm" 
                                                       title="Historique">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun bon de commande en attente de réception</h5>
                            <p class="text-muted">Tous les bons de commande ont été entièrement reçus ou aucun bon de commande n'est validé.</p>
                            <a href="{{ route('bon-commandes.index') }}" class="btn btn-primary">
                                <i class="fas fa-file-invoice"></i>
                                Voir les bons de commande
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    background-color: #e9ecef;
    border-radius: 0.375rem;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
</style>
@endsection