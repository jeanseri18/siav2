@extends('layouts.app')

@section('content')
<div class="app-content pt-3 p-md-3 p-lg-4">
    <div class="container-xl">
        <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
                <h1 class="app-page-title mb-0">Gestion des Devis</h1>
            </div>
            <div class="col-auto">
                <div class="page-utilities">
                    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                        <div class="col-auto">
                            <a class="app-btn app-btn-primary" href="{{ route('devis.create') }}">
                                <i class="fas fa-plus me-2"></i>Nouveau Devis
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="app-card app-card-orders-table shadow-sm mb-5">
            <div class="app-card-body">
                <div class="table-responsive">
                    <table class="table app-table-hover mb-0 text-left">
                        <thead>
                            <tr>
                                <th class="cell">ID</th>
                                <th class="cell">Client</th>
                                <th class="cell">Date</th>
                                <th class="cell">Total HT</th>
                                <th class="cell">TVA (18%)</th>
                                <th class="cell">Total TTC</th>
                                <th class="cell">Statut</th>
                                <th class="cell">Utilisé</th>
                                <th class="cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devis as $devisItem)
                            <tr>
                                <td class="cell">
                                    <a href="{{ route('devis.show', $devisItem->id) }}" class="text-decoration-none">
                                        #{{ $devisItem->id }}
                                    </a>
                                </td>
                                <td class="cell">{{ $devisItem->client->nom ?? $devisItem->client->nom_raison_sociale ?? 'N/A' }}</td>
                                <td class="cell">{{ $devisItem->created_at->format('d/m/Y') }}</td>
                                <td class="cell">{{ number_format($devisItem->total_ht, 0, ',', ' ') }} FCFA</td>
                                <td class="cell">{{ number_format($devisItem->calculerTVA(), 0, ',', ' ') }} FCFA</td>
                                <td class="cell">{{ number_format($devisItem->calculerTotalTTC(), 0, ',', ' ') }} FCFA</td>
                                <td class="cell">
                                    <span class="badge 
                                        @if($devisItem->statut == 'Validé') bg-success
                                        @elseif($devisItem->statut == 'En attente') bg-warning
                                        @else bg-secondary
                                        @endif">
                                        {{ $devisItem->statut }}
                                    </span>
                                </td>
                                <td class="cell">
                                    @if($devisItem->utilise_pour_vente)
                                        <span class="badge bg-info">Utilisé</span>
                                    @else
                                        <span class="badge bg-light text-dark">Disponible</span>
                                    @endif
                                </td>
                                <td class="cell">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('devis.show', $devisItem->id) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(!$devisItem->utilise_pour_vente)
                                        <a href="{{ route('devis.edit', $devisItem->id) }}" class="btn btn-sm btn-outline-secondary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('devis.destroy', $devisItem->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce devis ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucun devis trouvé.</p>
                                    <a href="{{ route('devis.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Créer le premier devis
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection