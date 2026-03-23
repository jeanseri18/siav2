@extends('layouts.app')

@section('title', 'Comparer les soldes')
@section('page-title', 'Comparer les soldes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_banque') }}">Banque</a></li>
<li class="breadcrumb-item active">Soldes</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card mb-3">
        <div class="app-card-header">
            <h2 class="app-card-title"><i class="fas fa-filter me-2"></i>Filtres</h2>
            <div class="app-card-actions">
                <a href="{{ route('banque.mouvements.create') }}" class="app-btn app-btn-outline-primary app-btn-sm">
                    <i class="fas fa-plus me-2"></i>Renseigner une opération
                </a>
                <a href="{{ route('banque.rapprochement.index') }}" class="app-btn app-btn-outline-warning app-btn-sm">
                    <i class="fas fa-check-double me-2"></i>Rapprochement
                </a>
                <a href="{{ route('banque.mouvements.index') }}" class="app-btn app-btn-secondary app-btn-sm">
                    Liste mouvements
                </a>
            </div>
        </div>
        <div class="app-card-body">
            <form method="GET" action="{{ route('banque.soldes.index') }}" class="d-flex gap-2 align-items-end">
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

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title"><i class="fas fa-eye me-2"></i>Prévisionnel</h2>
                </div>
                <div class="app-card-body">
                    <div class="d-flex justify-content-between">
                        <span>Solde initial</span>
                        <strong>{{ number_format((float) $totals['solde_initial'], 0, ',', ' ') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Entrées</span>
                        <strong>{{ number_format((float) $totals['entrees_prev'], 0, ',', ' ') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Sorties</span>
                        <strong>{{ number_format((float) $totals['sorties_prev'], 0, ',', ' ') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Solde</span>
                        <strong>{{ number_format((float) $totals['solde_prev'], 0, ',', ' ') }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title"><i class="fas fa-receipt me-2"></i>Réel</h2>
                </div>
                <div class="app-card-body">
                    <div class="d-flex justify-content-between">
                        <span>Solde initial</span>
                        <strong>{{ number_format((float) $totals['solde_initial'], 0, ',', ' ') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Entrées</span>
                        <strong>{{ number_format((float) $totals['entrees_reel'], 0, ',', ' ') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Sorties</span>
                        <strong>{{ number_format((float) $totals['sorties_reel'], 0, ',', ' ') }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Solde</span>
                        <strong>{{ number_format((float) $totals['solde_reel'], 0, ',', ' ') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title"><i class="fas fa-balance-scale me-2"></i>Détail par banque</h2>
            <div class="app-card-actions">
                <span class="app-badge app-badge-info app-badge-pill">BU: {{ session('selected_bu') }}</span>
            </div>
        </div>
        <div class="app-card-body app-table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Banque</th>
                        <th>Solde initial</th>
                        <th>Solde prévisionnel</th>
                        <th>Solde réel</th>
                        <th>Écart (prév - réel)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        <td>{{ $row['banque']->nom }}</td>
                        <td>{{ number_format((float) $row['solde_initial'], 0, ',', ' ') }}</td>
                        <td>{{ number_format((float) $row['solde_prev'], 0, ',', ' ') }}</td>
                        <td>{{ number_format((float) $row['solde_reel'], 0, ',', ' ') }}</td>
                        <td>{{ number_format((float) ($row['solde_prev'] - $row['solde_reel']), 0, ',', ' ') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Aucune banque trouvée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
