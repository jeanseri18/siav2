@extends('layouts.app')

@section('title', 'Détail artisan')
@section('page-title', 'Détail artisan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('artisans.index') }}">Artisans</a></li>
<li class="breadcrumb-item active">{{ $artisan->nom }}</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header app-d-flex app-flex-wrap app-justify-content-between app-align-items-center app-gap-3">
            <div>
                <h2 class="app-card-title app-mb-1">
                    <i class="fas fa-hard-hat me-2"></i>{{ $artisan->civilite }} {{ $artisan->nom }}@if($artisan->prenoms) {{ $artisan->prenoms }} @endif
                </h2>
                <p class="app-text-muted app-mb-0 small">
                    <span class="badge bg-secondary me-2">{{ $artisan->reference }}</span>
                    @if($artisan->fonction)
                        <i class="fas fa-briefcase me-1"></i>{{ $artisan->fonction }}
                    @endif
                </p>
            </div>
            <div class="app-d-flex app-gap-2">
                <a href="{{ route('artisans.edit', $artisan->id) }}" class="app-btn app-btn-warning app-btn-sm">
                    <i class="fas fa-edit me-1"></i>Modifier
                </a>
                <a href="{{ route('artisans.index') }}" class="app-btn app-btn-secondary app-btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                </a>
            </div>
        </div>

        <div class="app-card-body">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="app-form-section-title app-mb-3">
                        <i class="fas fa-id-card me-2"></i>Identité & pièce
                    </h3>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 app-text-muted">Civilité</dt>
                        <dd class="col-sm-8">{{ $artisan->civilite ?? '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Nom</dt>
                        <dd class="col-sm-8">{{ $artisan->nom }}</dd>
                        <dt class="col-sm-4 app-text-muted">Prénoms</dt>
                        <dd class="col-sm-8">{{ $artisan->prenoms ?: '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Type de pièce</dt>
                        <dd class="col-sm-8">{{ $artisan->type_piece ?? '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">N° pièce</dt>
                        <dd class="col-sm-8">{{ $artisan->numero_piece ?? '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Date de naissance</dt>
                        <dd class="col-sm-8">{{ $artisan->date_naissance ? \Carbon\Carbon::parse($artisan->date_naissance)->format('d/m/Y') : '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Nationalité</dt>
                        <dd class="col-sm-8">{{ $artisan->nationalite ?: '—' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <h3 class="app-form-section-title app-mb-3">
                        <i class="fas fa-address-book me-2"></i>Contact & activité
                    </h3>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 app-text-muted">Corps de métier</dt>
                        <dd class="col-sm-8">{{ $artisan->fonction ?: '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Localisation</dt>
                        <dd class="col-sm-8">{{ $artisan->localisation ?: '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Téléphone</dt>
                        <dd class="col-sm-8">{{ $artisan->tel1 ?? $artisan->telephone ?? '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Tél. 2</dt>
                        <dd class="col-sm-8">{{ $artisan->tel2 ?: '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">E-mail</dt>
                        <dd class="col-sm-8">@if($artisan->mail)<a href="mailto:{{ $artisan->mail }}">{{ $artisan->mail }}</a>@else — @endif</dd>
                        <dt class="col-sm-4 app-text-muted">PPSI</dt>
                        <dd class="col-sm-8">
                            @if($artisan->ppsi)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="text-muted">Non</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6">
                    <h3 class="app-form-section-title app-mb-3">
                        <i class="fas fa-landmark me-2"></i>Informations administratives
                    </h3>
                    <dl class="row mb-0">
                        <dt class="col-sm-4 app-text-muted">RCC</dt>
                        <dd class="col-sm-8">{{ $artisan->rcc ?: '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">RCCM</dt>
                        <dd class="col-sm-8">{{ $artisan->rccm ?: '—' }}</dd>
                        <dt class="col-sm-4 app-text-muted">Boîte postale</dt>
                        <dd class="col-sm-8">{{ $artisan->boite_postale ?: '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
