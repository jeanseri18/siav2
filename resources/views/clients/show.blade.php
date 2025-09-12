@extends('layouts.app')

@section('content')
<div class="app-container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="app-mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails du client</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="app-d-flex app-justify-content-between app-align-items-center app-mb-4">
        <div>
            <h1 class="app-h3 app-mb-1">{{ $client->nom }}</h1>
            <p class="app-text-muted app-mb-0">
                <i class="fas fa-user-tie app-me-2"></i>
                Client {{ $client->categorie }}
            </p>
        </div>
        <div class="app-d-flex app-gap-2">
            <a href="{{ route('clients.edit', $client->id) }}" class="app-btn app-btn-warning">
                <i class="fas fa-edit app-me-2"></i>Modifier
            </a>
            <a href="{{ route('clients.index') }}" class="app-btn app-btn-secondary">
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
                            <p class="app-mb-0">{{ $client->nom_raison_sociale }} {{ $client->prenoms }}</p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Type de client</label>
                            <p class="app-mb-0">
                                <span class="badge bg-{{ $client->type === 'Particulier' ? 'info' : 'primary' }}">
                                    {{ $client->categorie }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Téléphone</label>
                            <p class="app-mb-0">{{ $client->telephone ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Email</label>
                            <p class="app-mb-0">{{ $client->email ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-12 app-mb-3">
                            <label class="app-form-label app-fw-bold">Adresse</label>
                            <p class="app-mb-0">{{ $client->adresse ?? 'Non renseignée' }}</p>
                        </div>

                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Régime d'imposition</label>
                            <p class="app-mb-0">{{ $client->regime_imposition}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            @if($client->contactPersons && $client->contactPersons->count() > 0)
            <div class="app-card">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-users app-me-2"></i>
                        Personnes de contact
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        @foreach($client->contactPersons as $contact)
                        <div class="col-md-6 app-mb-3">
                            <div class="border rounded p-3 {{ $contact->contact_principal ? 'border-primary bg-light' : '' }}">
                                @if($contact->contact_principal)
                                <div class="app-mb-2">
                                    <span class="badge bg-primary">Contact principal</span>
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
                            <span class="badge bg-{{ $client->statut === 'Actif' ? 'success' : 'secondary' }}">
                                {{ $client->statut }}
                            </span>
                        </p>
                    </div>
                    <div class="app-mb-3">
                        <label class="app-form-label app-fw-bold">Date de création</label>
                        <p class="app-mb-0">{{ $client->created_at ? $client->created_at->format('d/m/Y à H:i') : 'Non disponible' }}</p>
                    </div>
                    <div class="app-mb-0">
                        <label class="app-form-label app-fw-bold">Dernière modification</label>
                        <p class="app-mb-0">{{ $client->updated_at ? $client->updated_at->format('d/m/Y à H:i') : 'Non disponible' }}</p>
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
                        <p class="app-mb-0">{{ $client->contactPersons ? $client->contactPersons->count() : 0 }}</p>
                    </div>
                    <div class="app-mb-0">
                        <label class="app-form-label app-fw-bold">Contact principal défini</label>
                        <p class="app-mb-0">
                            @if($client->contactPersons && $client->contactPersons->where('contact_principal', true)->count() > 0)
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