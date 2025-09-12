@extends('layouts.app')

@section('content')
<div class="app-container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="app-mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('fournisseurs.index') }}">Fournisseurs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails du fournisseur</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="app-d-flex app-justify-content-between app-align-items-center app-mb-4">
        <div>
            <h1 class="app-h3 app-mb-1">{{ $fournisseur->nom_raison_sociale }} {{ $fournisseur->prenoms }}</h1>
            <p class="app-text-muted app-mb-0">
                <i class="fas fa-truck app-me-2"></i>
                Fournisseur {{ $fournisseur->categorie }}
            </p>
        </div>
        <div class="app-d-flex app-gap-2">
            <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="app-btn app-btn-warning">
                <i class="fas fa-edit app-me-2"></i>Modifier
            </a>
            <a href="{{ route('fournisseurs.index') }}" class="app-btn app-btn-secondary">
                <i class="fas fa-arrow-left app-me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="app-card app-mb-4">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-info-circle app-me-2"></i>
                        Informations principales
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Nom/Raison sociale</label>
                            <p class="app-mb-0">{{ $fournisseur->nom }}</p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Type de fournisseur</label>
                            <p class="app-mb-0">
                                <span class="badge bg-{{ $fournisseur->type === 'Particulier' ? 'info' : 'success' }}">
                                    {{ $fournisseur->categorie }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Téléphone</label>
                            <p class="app-mb-0">{{ $fournisseur->telephone ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Email</label>
                            <p class="app-mb-0">{{ $fournisseur->email ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-12 app-mb-3">
                            <label class="app-form-label app-fw-bold">Adresse</label>
                            <p class="app-mb-0">{{ $fournisseur->adresse ?? 'Non renseignée' }}</p>
                        </div>
            
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Régime d'imposition</label>
                            <p class="app-mb-0">{{ $fournisseur->regime_imposition }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            @if($fournisseur->contactPersons && $fournisseur->contactPersons->count() > 0)
            <div class="app-card">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-users app-me-2"></i>
                        Personnes de contact
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        @foreach($fournisseur->contactPersons as $contact)
                        <div class="col-md-6 app-mb-3">
                            <div class="border rounded p-3 {{ $contact->contact_principal ? 'border-success bg-light' : '' }}">
                                @if($contact->contact_principal)
                                <div class="app-mb-2">
                                    <span class="badge bg-success">Contact principal</span>
                                </div>
                                @endif
                                <h6 class="app-mb-2">
                                    {{ $contact->civilite }} {{ $contact->nom }} {{ $contact->prenoms }}
                                </h6>
                                @if($contact->fonction)
                                <p class="app-text-muted app-mb-1"><small>{{ $contact->fonction }}</small></p>
                                @endif
                                @if($contact->telephone_1)
                                <p class="app-mb-1"><i class="fas fa-phone app-me-2"></i>{{ $contact->telephone_1 }}</p>
                                @endif
                                @if($contact->email)
                                <p class="app-mb-1"><i class="fas fa-envelope app-me-2"></i>{{ $contact->email }}</p>
                                @endif
                                <span class="badge bg-{{ $contact->statut === 'Actif' ? 'success' : 'secondary' }}">
                                    {{ $contact->statut }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Informations complémentaires -->
        <div class="col-lg-4">
            <div class="app-card app-mb-4">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-cog app-me-2"></i>
                        Informations système
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="app-mb-3">
                        <label class="app-form-label app-fw-bold">Statut</label>
                        <p class="app-mb-0">
                            <span class="badge bg-{{ $fournisseur->statut === 'Actif' ? 'success' : 'secondary' }}">
                                {{ $fournisseur->statut }}
                            </span>
                        </p>
                    </div>
                    <div class="app-mb-3">
                        <label class="app-form-label app-fw-bold">Date de création</label>
                        <p class="app-mb-0">{{ $fournisseur->created_at ? $fournisseur->created_at->format('d/m/Y à H:i') : 'Non disponible' }}</p>
                    </div>
                    <div class="app-mb-0">
                        <label class="app-form-label app-fw-bold">Dernière modification</label>
                        <p class="app-mb-0">{{ $fournisseur->updated_at ? $fournisseur->updated_at->format('d/m/Y à H:i') : 'Non disponible' }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="app-card">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-chart-bar app-me-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="app-mb-3">
                        <label class="app-form-label app-fw-bold">Nombre de contacts</label>
                        <p class="app-mb-0">{{ $fournisseur->contactPersons ? $fournisseur->contactPersons->count() : 0 }}</p>
                    </div>
                    <div class="app-mb-0">
                        <label class="app-form-label app-fw-bold">Contact principal défini</label>
                        <p class="app-mb-0">
                            @if($fournisseur->contactPersons && $fournisseur->contactPersons->where('contact_principal', true)->count() > 0)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-warning">Non</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection