@extends('layouts.app')

@section('title', 'Rapprochement bancaire')
@section('page-title', 'Rapprochement bancaire')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_banque') }}">Banque</a></li>
<li class="breadcrumb-item active">Rapprochement</li>
@endsection

@section('content')
<div class="app-fade-in">
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

    <div class="app-card mb-3">
        <div class="app-card-header">
            <h2 class="app-card-title"><i class="fas fa-filter me-2"></i>Actions & filtres</h2>
            <div class="app-card-actions">
                <a href="{{ route('banque.mouvements.create') }}" class="app-btn app-btn-outline-primary app-btn-sm">
                    <i class="fas fa-plus me-2"></i>Renseigner une opération
                </a>
                <a href="{{ route('banque.soldes.index') }}" class="app-btn app-btn-outline-success app-btn-sm">
                    <i class="fas fa-balance-scale me-2"></i>Comparer les soldes
                </a>
                <a href="{{ route('banque.mouvements.index') }}" class="app-btn app-btn-secondary app-btn-sm">
                    Liste mouvements
                </a>
            </div>
        </div>
        <div class="app-card-body">
            <form method="GET" action="{{ route('banque.rapprochement.index') }}" class="d-flex gap-2 align-items-end">
                <div class="flex-grow-1">
                    <label for="banque_id" class="form-label">Banque</label>
                    <select name="banque_id" id="banque_id" class="form-select">
                        <option value="">Toutes</option>
                        @foreach($banques as $banque)
                        <option value="{{ $banque->id }}" {{ (string)$banqueId === (string)$banque->id ? 'selected' : '' }}>
                            {{ $banque->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="app-btn app-btn-primary">Appliquer</button>
                <a href="{{ route('sublayouts_banque') }}" class="app-btn app-btn-secondary">Retour</a>
            </form>
        </div>
    </div>

    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title"><i class="fas fa-check-double me-2"></i>Opérations à rapprocher</h2>
            <div class="app-card-actions">
                <span class="app-badge app-badge-info app-badge-pill">BU: {{ session('selected_bu') }}</span>
            </div>
        </div>
        <div class="app-card-body app-table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Banque</th>
                        <th>Type</th>
                        <th>Mode</th>
                        <th>Montant</th>
                        <th>Pièce</th>
                        <th>Bénéficiaire</th>
                        <th>Valider</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mouvements as $mvt)
                    <tr>
                        <td>{{ $mvt->date_operation?->format('d/m/Y') }}</td>
                        <td>{{ $mvt->banque?->nom }}</td>
                        <td>
                            <span class="app-badge {{ $mvt->type === 'entree' ? 'app-badge-success' : 'app-badge-danger' }} app-badge-pill">
                                {{ $mvt->type === 'entree' ? 'Entrée' : 'Sortie' }}
                            </span>
                        </td>
                        <td>{{ ucfirst($mvt->mode) }}</td>
                        <td>{{ number_format((float) $mvt->montant, 0, ',', ' ') }}</td>
                        <td>{{ $mvt->numero_piece ?: '-' }}</td>
                        <td>{{ $mvt->beneficiaire ?: '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('banque.rapprochement.toggle', $mvt) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="est_passe" value="1">
                                <button type="submit" class="app-btn app-btn-outline-success app-btn-sm">
                                    Passé
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Aucune opération en attente de rapprochement</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
