@extends('layouts.app')

@section('content')
<div class="app-content pt-3 p-md-3 p-lg-4">
    <div class="container-xl">
        <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
                <h1 class="app-page-title mb-0">Détails du Devis #{{ $devis->id }}</h1>
            </div>
            <div class="col-auto">
                <div class="page-utilities">
                    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                        <div class="col-auto">
                            <a href="{{ route('devis.edit', $devis->id) }}" class="app-btn app-btn-primary">
                                <i class="fas fa-edit me-2"></i>Modifier
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('devis.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Informations du devis -->
            <div class="col-12 col-lg-6">
                <div class="app-card app-card-basic">
                    <div class="app-card-header">
                        <div class="app-card-header-inner">
                            <h3 class="app-card-title">
                                <i class="fas fa-info-circle me-2"></i>Informations du Devis
                            </h3>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="app-info-item">
                                    <span class="app-info-label">ID :</span>
                                    <span class="app-info-value">#{{ $devis->id }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="app-info-item">
                                    <span class="app-info-label">Date de création :</span>
                                    <span class="app-info-value">{{ $devis->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="app-info-item">
                                    <span class="app-info-label">Statut :</span>
                                    <span class="app-info-value">
                                        @if($devis->utilise)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Utilisé
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>En attente
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @if($devis->utilise && $devis->vente)
                            <div class="col-12">
                                <div class="app-info-item">
                                    <span class="app-info-label">Vente associée :</span>
                                    <span class="app-info-value">
                                        <a href="{{ route('ventes.show', $devis->vente->id) }}" class="text-primary">
                                            <i class="fas fa-shopping-cart me-1"></i>Vente #{{ $devis->vente->id }}
                                        </a>
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations du client -->
            <div class="col-12 col-lg-6">
                <div class="app-card app-card-basic">
                    <div class="app-card-header">
                        <div class="app-card-header-inner">
                            <h3 class="app-card-title">
                                <i class="fas fa-user me-2"></i>Client
                            </h3>
                        </div>
                    </div>
                    <div class="app-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="app-info-item">
                                    <span class="app-info-label">Nom :</span>
                                    <span class="app-info-value">{{ $devis->client->nom ?? $devis->client->nom_raison_sociale }}</span>
                                </div>
                            </div>
                            @if($devis->client->email)
                            <div class="col-12">
                                <div class="app-info-item">
                                    <span class="app-info-label">Email :</span>
                                    <span class="app-info-value">
                                        <a href="mailto:{{ $devis->client->email }}" class="text-primary">
                                            <i class="fas fa-envelope me-1"></i>{{ $devis->client->email }}
                                        </a>
                                    </span>
                                </div>
                            </div>
                            @endif
                            @if($devis->client->telephone)
                            <div class="col-12">
                                <div class="app-info-item">
                                    <span class="app-info-label">Téléphone :</span>
                                    <span class="app-info-value">
                                        <a href="tel:{{ $devis->client->telephone }}" class="text-primary">
                                            <i class="fas fa-phone me-1"></i>{{ $devis->client->telephone }}
                                        </a>
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles du devis -->
        <div class="app-card mt-4">
            <div class="app-card-header">
                <div class="app-card-header-inner">
                    <h3 class="app-card-title">
                        <i class="fas fa-boxes me-2"></i>Articles ({{ $devis->articles->count() }})
                    </h3>
                </div>
            </div>
            <div class="app-card-body">
                @if($devis->articles->count() > 0)
                    <div class="table-responsive">
                        <table class="table app-table-hover mb-0 text-left">
                            <thead>
                                <tr>
                                    <th class="cell">Article</th>
                                    <th class="cell">Unité</th>
                                    <th class="cell">Quantité</th>
                                    <th class="cell">Prix Unitaire HT</th>
                                    <th class="cell">Montant Total HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($devis->articles as $article)
                                <tr>
                                    <td class="cell">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-box text-muted me-2"></i>
                                            <div>
                                                <div class="fw-bold">{{ $article->nom }}</div>
                                                @if($article->description)
                                                    <div class="text-muted small">{{ Str::limit($article->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="cell">
                                        <span class="badge bg-light text-dark">{{ $article->unite_mesure ?? 'Unité' }}</span>
                                    </td>
                                    <td class="cell">
                                        <span class="fw-bold">{{ $article->pivot->quantite }}</span>
                                    </td>
                                    <td class="cell">
                                        <span class="text-success fw-bold">{{ number_format($article->pivot->prix_unitaire, 0, ',', ' ') }} FCFA</span>
                                    </td>
                                    <td class="cell">
                                        <span class="text-primary fw-bold">{{ number_format($article->pivot->prix_unitaire * $article->pivot->quantite, 0, ',', ' ') }} FCFA</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p>Aucun article dans ce devis.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Totaux -->
        <div class="app-card mt-4">
            <div class="app-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="app-d-flex app-justify-content-between app-align-items-center">
                            <h5 class="mb-0">Total HT :</h5>
                            <h4 class="mb-0 text-info">
                                {{ number_format($devis->total_ht, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="app-d-flex app-justify-content-between app-align-items-center">
                            <h5 class="mb-0">TVA (18%) :</h5>
                            <h4 class="mb-0 text-warning">
                                {{ number_format($devis->tva, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="app-d-flex app-justify-content-between app-align-items-center">
                            <h5 class="mb-0">Total TTC :</h5>
                            <h4 class="mb-0 text-success">
                                <i class="fas fa-coins me-2"></i>
                                {{ number_format($devis->total_ttc, 0, ',', ' ') }} FCFA
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection