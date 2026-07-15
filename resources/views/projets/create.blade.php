@extends('layouts.app')

@section('title', 'Créer un Projet')
@section('page-title', 'Créer un Projet')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item active">Créer</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-plus me-2"></i>Créer un nouveau projet
            </h2>
        </div>
        
        <form action="{{ route('projets.store') }}" method="POST" class="app-card-body">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="client" class="app-form-label">Client</label>
                        <select name="client" id="client" class="app-form-control @error('client') is-invalid @enderror" required>
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom_raison_sociale }}
                                </option>
                            @endforeach
                        </select>
                        @error('client')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="secteur_activite_id" class="app-form-label">Secteur d'activité</label>
                        <select name="secteur_activite_id" id="secteur_activite_id" class="app-form-control @error('secteur_activite_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un secteur</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}" {{ old('secteur_activite_id') == $secteur->id ? 'selected' : '' }}>
                                    {{ $secteur->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('secteur_activite_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <span class="app-form-label d-block">Référence du projet</span>
                        <p class="app-form-control bg-light text-muted small mb-0 py-2 px-3 rounded border" style="cursor: default;">
                            <i class="fas fa-magic me-1 text-secondary"></i>
                            Générée automatiquement à l’enregistrement (format&nbsp;: <code>Prj_AAAAMMJJhhmmss</code>).
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom_projet" class="app-form-label">Nom du projet</label>
                        <input type="text" name="nom_projet" id="nom_projet" 
                               class="app-form-control @error('nom_projet') is-invalid @enderror" 
                               value="{{ old('nom_projet') }}" required>
                        @error('nom_projet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                Les <strong>dates de début et de fin du projet</strong> ne se saisissent pas ici : elles sont calculées automatiquement à partir des dates des <strong>contrats</strong> (plus ancienne date de début, plus récente date de fin).
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="chef_projet_id" class="app-form-label">Chef de projet</label>
                        <select name="chef_projet_id" id="chef_projet_id" class="app-form-control @error('chef_projet_id') is-invalid @enderror">
                            <option value="">Sélectionner un chef de projet</option>
                            @foreach($chefs as $chef)
                                <option value="{{ $chef->id }}" {{ old('chef_projet_id') == $chef->id ? 'selected' : '' }}>
                                    {{ $chef->prenom }} {{ $chef->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('chef_projet_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="conducteur_travaux_id" class="app-form-label">Conducteur de travaux</label>
                        <select name="conducteur_travaux_id" id="conducteur_travaux_id" class="app-form-control @error('conducteur_travaux_id') is-invalid @enderror">
                            <option value="">Sélectionner un conducteur</option>
                            @foreach($conducteurs as $conducteur)
                                <option value="{{ $conducteur->id }}" {{ old('conducteur_travaux_id') == $conducteur->id ? 'selected' : '' }}>
                                    {{ $conducteur->prenom }} {{ $conducteur->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('conducteur_travaux_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="app-form-check">
                            <input type="checkbox" name="hastva" id="hastva" value="1"
                                   class="app-form-check-input" 
                                   {{ old('hastva') ? 'checked' : '' }}>
                            <label for="hastva" class="app-form-check-label">
                                Facturation client soumise à TVA18%
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="app-form-check">
                            <input type="checkbox" name="tva_achat" id="tva_achat" value="1"
                                   class="app-form-check-input" 
                                   {{ old('tva_achat') ? 'checked' : '' }}>
                            <label for="tva_achat" class="app-form-check-label">
                                Achat fournisseur soumis à TVA18%
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <p class="small text-muted mb-2">
                        <i class="fas fa-calculator me-1"></i>
                        Ces montants sont <strong>calculés automatiquement</strong> (contrats, facturation, bons de commande) — non saisis à la création.
                    </p>
                    <div class="mb-3">
                        <label class="app-form-label">Montant global</label>
                        <input type="text" class="app-form-control bg-light text-muted" value="—" readonly tabindex="-1" aria-readonly="true" style="cursor: not-allowed;">
                    </div>
                    <div class="mb-3">
                        <label class="app-form-label">Chiffre d'affaire global</label>
                        <input type="text" class="app-form-control bg-light text-muted" value="—" readonly tabindex="-1" aria-readonly="true" style="cursor: not-allowed;">
                    </div>
                    <div class="mb-3">
                        <label class="app-form-label">Total dépenses</label>
                        <input type="text" class="app-form-control bg-light text-muted" value="—" readonly tabindex="-1" aria-readonly="true" style="cursor: not-allowed;">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="statut" class="app-form-label">Statut du projet</label>
                        <select name="statut" id="statut" class="app-form-control @error('statut') is-invalid @enderror" required>
                            <option value="non débuté" {{ old('statut', 'non débuté') == 'non débuté' ? 'selected' : '' }}>Non débuté</option>
                            <option value="en cours" {{ old('statut') == 'en cours' ? 'selected' : '' }}>En cours</option>
                            <option value="terminé" {{ old('statut') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                            <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="alert alert-light border mb-3 py-2 px-3" role="status">
                        <i class="fas fa-building me-2 text-primary"></i>
                        <strong>BU :</strong> {{ $buCourante->nom ?? '—' }}
                        <span class="d-block small text-muted mt-1 mb-0">Le projet est rattaché à la BU sélectionnée à la connexion.</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pays_id" class="app-form-label">Pays</label>
                        <select name="pays_id" id="pays_id" class="app-form-control @error('pays_id') is-invalid @enderror">
                            <option value="">Sélectionner un pays</option>
                            @foreach($pays as $pay)
                                <option value="{{ $pay->id }}" {{ old('pays_id') == $pay->id ? 'selected' : '' }}>
                                    {{ $pay->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('pays_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="ville_id" class="app-form-label">Ville</label>
                        <select name="ville_id" id="ville_id" class="app-form-control @error('ville_id') is-invalid @enderror">
                            <option value="">Sélectionner une ville</option>
                            @foreach($villes as $ville)
                                <option value="{{ $ville->id }}" {{ old('ville_id') == $ville->id ? 'selected' : '' }}>
                                    {{ $ville->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('ville_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="commune_id" class="app-form-label">Commune</label>
                        <select name="commune_id" id="commune_id" class="app-form-control @error('commune_id') is-invalid @enderror">
                            <option value="">Sélectionner une commune</option>
                        </select>
                        @error('commune_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="secteur_id" class="app-form-label">Secteur <span class="text-muted small">(localisation)</span></label>
                        <input type="hidden" name="quartier_id" id="quartier_id" value="{{ old('quartier_id') }}">
                        <select name="secteur_id" id="secteur_id" class="app-form-control @error('secteur_id') is-invalid @enderror">
                            <option value="">Sélectionner un secteur</option>
                        </select>
                        @error('secteur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="description" class="app-form-label">Description détaillée</label>
                        <textarea name="description" id="description" 
                                  class="app-form-control @error('description') is-invalid @enderror" 
                                  rows="4" placeholder="Décrivez le projet en détail...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="app-card-footer">
                <div class="app-d-flex app-justify-content-between">
                    <a href="{{ route('projets.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Créer le projet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $locationInit = [
        'ville_id' => old('ville_id'),
        'commune_id' => old('commune_id'),
        'secteur_id' => old('secteur_id'),
        'quartier_id' => old('quartier_id'),
    ];
@endphp
@include('projets.partials.location-cascade-scripts')
@include('projets.partials.client-secteur-sync')
@endsection