{{-- Page Show - Profil Utilisateur --}}
@extends('layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('breadcrumb')
<li class="breadcrumb-item active">Mon Profil</li>
@endsection

@section('content')
<div class="container app-fade-in">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-user me-2"></i>Informations Personnelles
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('profile.edit') }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-user me-2"></i>Nom complet
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $user->nom }}
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-envelope me-2"></i>Adresse email
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $user->email }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-user-tag me-2"></i>Rôle
                                </label>
                                <div class="app-form-control bg-light">
                                    @php
                                        $roleClass = '';
                                        $roleIcon = '';
                                        switch($user->role) {
                                            case 'admin':
                                                $roleClass = 'danger';
                                                $roleIcon = 'user-shield';
                                                break;
                                            case 'dg':
                                                $roleClass = 'primary';
                                                $roleIcon = 'crown';
                                                break;
                                            case 'chefprojet':
                                                $roleClass = 'info';
                                                $roleIcon = 'user-tie';
                                                break;
                                            case 'utilisateur':
                                                $roleClass = 'secondary';
                                                $roleIcon = 'user';
                                                break;
                                            default:
                                                $roleClass = 'light';
                                                $roleIcon = 'question-circle';
                                        }
                                    @endphp
                                    <span class="app-badge app-badge-{{ $roleClass }} app-badge-pill">
                                        <i class="fas fa-{{ $roleIcon }} me-1"></i> 
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-toggle-on me-2"></i>Statut
                                </label>
                                <div class="app-form-control bg-light">
                                    @if($user->status === 'actif')
                                        <span class="app-badge app-badge-success app-badge-pill">
                                            <i class="fas fa-check-circle me-1"></i> Actif
                                        </span>
                                    @else
                                        <span class="app-badge app-badge-danger app-badge-pill">
                                            <i class="fas fa-times-circle me-1"></i> Inactif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-calendar-plus me-2"></i>Membre depuis
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $user->created_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                        <div class="app-form-col-6">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <i class="fas fa-clock me-2"></i>Dernière modification
                                </label>
                                <div class="app-form-control bg-light">
                                    {{ $user->updated_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Sécurité -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-shield-alt me-2"></i>Sécurité
                    </h3>
                    <div class="app-card-actions">
                        <a href="{{ route('profile.edit-password') }}" class="app-btn app-btn-primary app-btn-sm app-btn-icon">
                            <i class="fas fa-key"></i> Changer le mot de passe
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <div class="app-d-flex app-align-items-center app-gap-3">
                        <div class="app-d-flex app-align-items-center app-gap-2">
                            <i class="fas fa-lock fa-2x text-success"></i>
                            <div>
                                <h6 class="mb-1">Mot de passe</h6>
                                <small class="text-muted">Dernière modification: {{ $user->updated_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel latéral -->
        <div class="col-md-4">
            <!-- Photo de profil -->
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-camera me-2"></i>Photo de Profil
                    </h3>
                </div>
                <div class="app-card-body text-center">
                    @if($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" alt="Photo de profil" 
                             class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        @php
                            $initials = collect(explode(' ', $user->nom))->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))->join('');
                        @endphp
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 150px; height: 150px; font-size: 3rem; font-weight: bold;">
                            {{ $initials }}
                        </div>
                    @endif
                    
                    <h5 class="app-fw-bold">{{ $user->nom }}</h5>
                    <p class="text-muted">{{ ucfirst($user->role) }}</p>
                    
                    <div class="app-d-grid app-gap-2 mt-3">
                        <a href="{{ route('profile.edit') }}" class="app-btn app-btn-outline-primary app-btn-sm">
                            <i class="fas fa-edit me-2"></i>Modifier la photo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h3>
                </div>
                <div class="app-card-body">
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-calendar-day me-2"></i>Compte créé depuis
                        </span>
                        <span class="app-fw-bold">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">
                            <i class="fas fa-envelope-open me-2"></i>Email vérifié
                        </span>
                        @if($user->email_verified_at)
                            <span class="app-badge app-badge-success app-badge-pill">
                                <i class="fas fa-check me-1"></i>Oui
                            </span>
                        @else
                            <span class="app-badge app-badge-warning app-badge-pill">
                                <i class="fas fa-clock me-1"></i>En attente
                            </span>
                        @endif
                    </div>
                    <div class="app-d-flex app-justify-content-between app-align-items-center">
                        <span class="text-muted">
                            <i class="fas fa-sign-in-alt me-2"></i>Dernière connexion
                        </span>
                        <span class="app-fw-bold">Maintenant</span>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-bolt me-2"></i>Actions Rapides
                    </h3>
                </div>
                <div class="app-card-body app-d-grid app-gap-2">
                    <a href="{{ route('profile.edit') }}" class="app-btn app-btn-warning w-100">
                        <i class="fas fa-edit me-2"></i>Modifier le profil
                    </a>
                    <a href="{{ route('profile.edit-password') }}" class="app-btn app-btn-primary w-100">
                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="app-btn app-btn-danger w-100" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                            <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection