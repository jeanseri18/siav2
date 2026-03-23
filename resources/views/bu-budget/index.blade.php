@extends('layouts.app')

@section('title', 'Budget Prévisionnel - BU')
@section('page-title', 'Budget Prévisionnel - BU')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_tresorerie') }}">Trésorerie</a></li>
<li class="breadcrumb-item active">Budget BU</li>
@endsection

@section('content')
<div class="app-fade-in">
    @if($errors->any())
    <div class="app-alert app-alert-danger">
        <div class="app-alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="app-alert-content">
            <div class="app-alert-text">
                <ul class="m-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="app-card mb-3">
        <div class="app-card-header">
            <h2 class="app-card-title"><i class="fas fa-plus me-2"></i>Créer / ouvrir un budget</h2>
            <div class="app-card-actions">
                <span class="app-badge app-badge-info app-badge-pill">BU: {{ session('selected_bu') }}</span>
            </div>
        </div>
        <div class="app-card-body">
            <form method="POST" action="{{ route('bu-budget.store') }}" class="d-flex gap-2 align-items-end">
                @csrf
                <div>
                    <label for="annee" class="form-label">Année</label>
                    <input type="number" min="2000" max="2100" name="annee" id="annee" value="{{ old('annee', now()->format('Y')) }}" class="form-control" required>
                </div>
                <button type="submit" class="app-btn app-btn-primary">Ouvrir</button>
                <a href="{{ route('sublayouts_tresorerie') }}" class="app-btn app-btn-secondary">Retour</a>
            </form>
        </div>
    </div>

    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title"><i class="fas fa-list me-2"></i>Budgets disponibles</h2>
        </div>
        <div class="app-card-body app-table-responsive">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Année</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                    <tr>
                        <td><strong>{{ $budget->annee }}</strong></td>
                        <td>
                            <a href="{{ route('bu-budget.show', $budget) }}" class="app-btn app-btn-outline-primary app-btn-sm">
                                Ouvrir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">Aucun budget</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
