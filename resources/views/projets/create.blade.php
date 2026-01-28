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
                        <label for="ref_projet" class="app-form-label">Référence du projet</label>
                        <input type="text" name="ref_projet" id="ref_projet" 
                               class="app-form-control @error('ref_projet') is-invalid @enderror" 
                               value="{{ old('ref_projet') }}" required>
                        @error('ref_projet')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_debut" class="app-form-label">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" 
                               class="app-form-control @error('date_debut') is-invalid @enderror" 
                               value="{{ old('date_debut') }}" required>
                        @error('date_debut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_fin" class="app-form-label">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" 
                               class="app-form-control @error('date_fin') is-invalid @enderror" 
                               value="{{ old('date_fin') }}">
                        @error('date_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
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
                            <input type="checkbox" name="hastva" id="hastva" 
                                   class="app-form-check-input" 
                                   {{ old('hastva') ? 'checked' : '' }}>
                            <label for="hastva" class="app-form-check-label">
                                Facturation client soumise à TVA18%
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="app-form-check">
                            <input type="checkbox" name="tva_achat" id="tva_achat" 
                                   class="app-form-check-input" 
                                   {{ old('tva_achat') ? 'checked' : '' }}>
                            <label for="tva_achat" class="app-form-check-label">
                                Achat fournisseur soumis à TVA18%
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="montant_global" class="app-form-label">Montant global</label>
                        <input type="number" name="montant_global" id="montant_global" 
                               class="app-form-control @error('montant_global') is-invalid @enderror" 
                               value="{{ old('montant_global') }}" 
                               step="0.01" min="0">
                        @error('montant_global')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="chiffre_affaire_global" class="app-form-label">Chiffre d'affaire global</label>
                        <input type="number" name="chiffre_affaire_global" id="chiffre_affaire_global" 
                               class="app-form-control @error('chiffre_affaire_global') is-invalid @enderror" 
                               value="{{ old('chiffre_affaire_global') }}" 
                               step="0.01" min="0">
                        @error('chiffre_affaire_global')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="total_depenses" class="app-form-label">Total Dépenses</label>
                        <input type="number" name="total_depenses" id="total_depenses" 
                               class="app-form-control @error('total_depenses') is-invalid @enderror" 
                               value="{{ old('total_depenses') }}" 
                               step="0.01" min="0">
                        @error('total_depenses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="statut" class="app-form-label">Statut du projet</label>
                        <select name="statut" id="statut" class="app-form-control @error('statut') is-invalid @enderror" required>
                            <option value="en cours" {{ old('statut') == 'en cours' ? 'selected' : '' }}>En cours</option>
                            <option value="terminé" {{ old('statut') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                            <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="bu_id" class="app-form-label">Business Unit</label>
                        <select name="bu_id" id="bu_id" class="app-form-control @error('bu_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une BU</option>
                            @foreach($bus as $bu)
                                <option value="{{ $bu->id }}" {{ old('bu_id') == $bu->id ? 'selected' : '' }}>
                                    {{ $bu->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('bu_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
@endsection