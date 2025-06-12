@extends('layouts.app')

@section('title', 'Détails de l\'Employé')
@section('page-title', 'Détails de l\'Employé')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('employes.index') }}">Employés</a></li>
<li class="breadcrumb-item active">{{ $employe->prenom }} {{ $employe->nom }}</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-4">
            <div class="app-card">
                <div class="app-card-body text-center">
                    @if($employe->photo)
                        <img src="{{ asset('storage/' . $employe->photo) }}" alt="Photo" class="rounded-circle mb-3" width="120" height="120">
                    @else
                        <div class="avatar-initials mx-auto mb-3" style="width: 120px; height: 120px; font-size: 48px;">
                            {{ strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)) }}
                        </div>
                    @endif
                    
                    <h4 class="mb-1">{{ $employe->prenom }} {{ $employe->nom }}</h4>
                    
                    @php
                        $roleLabels = [
                            'chef_projet' => 'Chef de Projet',
                            'conducteur_travaux' => 'Conducteur de Travaux',
                            'chef_chantier' => 'Chef de Chantier',
                            'comptable' => 'Comptable',
                            'magasinier' => 'Magasinier',
                            'acheteur' => 'Acheteur',
                            'controleur_gestion' => 'Contrôleur de Gestion',
                            'secretaire' => 'Secrétaire',
                            'chauffeur' => 'Chauffeur',
                            'gardien' => 'Gardien',
                            'employe' => 'Employé',
                            'admin' => 'Administrateur',
                            'dg' => 'Directeur Général'
                        ];
                        $roleClass = match($employe->role) {
                            'admin', 'dg' => 'danger',
                            'chef_projet', 'conducteur_travaux' => 'primary',
                            'chef_chantier', 'comptable' => 'success',
                            'magasinier', 'acheteur' => 'info',
                            default => 'secondary'
                        };
                    @endphp
                    
                    <span class="badge bg-{{ $roleClass }} mb-2">
                        {{ $roleLabels[$employe->role] ?? ucfirst($employe->role) }}
                    </span>
                    
                    @if($employe->poste)
                        <p class="text-muted mb-2">{{ $employe->poste }}</p>
                    @endif
                    
                    <span class="badge bg-{{ $employe->status === 'actif' ? 'success' : 'danger' }} mb-3">
                        {{ ucfirst($employe->status) }}
                    </span>
                    
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('employes.edit', $employe) }}" class="app-btn app-btn-warning app-btn-sm">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <a href="{{ route('employes.index') }}" class="app-btn app-btn-secondary app-btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Statistiques rapides -->
            @if($employe->date_embauche)
            <div class="app-card mt-3">
                <div class="app-card-header">
                    <h6 class="app-card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="app-card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-primary mb-1">{{ $employe->anciennete }}</h5>
                                <small class="text-muted">Années d'ancienneté</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success mb-1">{{ $employe->age ?? '-' }}</h5>
                            <small class="text-muted">Âge</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Détails complets -->
        <div class="col-lg-8">
            <!-- Informations personnelles -->
            <div class="app-card mb-3">
                <div class="app-card-header">
                    <h6 class="app-card-title mb-0">
                        <i class="fas fa-user me-2"></i>Informations Personnelles
                    </h6>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p class="mb-0">{{ $employe->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Téléphone:</label>
                            <p class="mb-0">{{ $employe->telephone ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de Naissance:</label>
                            <p class="mb-0">
                                @if($employe->date_naissance)
                                    {{ $employe->date_naissance->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sexe:</label>
                            <p class="mb-0">
                                @if($employe->sexe === 'M')
                                    Masculin
                                @elseif($employe->sexe === 'F')
                                    Féminin
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Lieu de Naissance:</label>
                            <p class="mb-0">{{ $employe->lieu_naissance ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nationalité:</label>
                            <p class="mb-0">{{ $employe->nationalite ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Situation Matrimoniale:</label>
                            <p class="mb-0">
                                @if($employe->situation_matrimoniale)
                                    {{ ucfirst($employe->situation_matrimoniale) }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Adresse:</label>
                            <p class="mb-0">{{ $employe->adresse ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informations professionnelles -->
            <div class="app-card mb-3">
                <div class="app-card-header">
                    <h6 class="app-card-title mb-0">
                        <i class="fas fa-briefcase me-2"></i>Informations Professionnelles
                    </h6>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date d'Embauche:</label>
                            <p class="mb-0">
                                @if($employe->date_embauche)
                                    {{ $employe->date_embauche->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Salaire:</label>
                            <p class="mb-0">
                                @if($employe->salaire)
                                    {{ number_format($employe->salaire, 0, ',', ' ') }} FCFA
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Numéro CNSS:</label>
                            <p class="mb-0">{{ $employe->numero_cnss ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Documents d'identité -->
            <div class="app-card mb-3">
                <div class="app-card-header">
                    <h6 class="app-card-title mb-0">
                        <i class="fas fa-id-card me-2"></i>Documents d'Identité
                    </h6>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Numéro CNI:</label>
                            <p class="mb-0">{{ $employe->numero_cni ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Numéro Passeport:</label>
                            <p class="mb-0">{{ $employe->numero_passeport ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Projets associés -->
            @if($employe->role === 'chef_projet' || $employe->role === 'conducteur_travaux')
            <div class="app-card">
                <div class="app-card-header">
                    <h6 class="app-card-title mb-0">
                        <i class="fas fa-project-diagram me-2"></i>Projets Associés
                    </h6>
                </div>
                <div class="app-card-body">
                    @php
                        $projets = collect();
                        if($employe->role === 'chef_projet') {
                            $projets = $employe->projetsChefProjet;
                        } elseif($employe->role === 'conducteur_travaux') {
                            $projets = $employe->projetsConducteurTravaux;
                        }
                    @endphp
                    
                    @if($projets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Nom du Projet</th>
                                        <th>Client</th>
                                        <th>Statut</th>
                                        <th>Rôle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projets as $projet)
                                    <tr>
                                        <td>{{ $projet->ref_projet }}</td>
                                        <td>{{ $projet->nom_projet }}</td>
                                        <td>{{ $projet->client }}</td>
                                        <td>
                                            <span class="badge bg-{{ $projet->statut === 'en_cours' ? 'success' : 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $projet->statut)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($employe->role === 'chef_projet')
                                                Chef de Projet
                                            @else
                                                Conducteur de Travaux
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun projet associé pour le moment.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection