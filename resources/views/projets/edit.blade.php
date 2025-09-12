@extends('layouts.app')

@section('title', 'Modifier un Projet')
@section('page-title', 'Modifier un Projet')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Projet: {{ $projet->nom_projet }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('projets.update', $projet->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="date_creation" class="app-form-label">
                                <i class="fas fa-calendar-plus me-2"></i>Date de création
                            </label>
                            <input type="date" id="date_creation" name="date_creation" value="{{ $projet->date_creation }}" class="app-form-control" required>
                            <div class="app-form-text">Date à laquelle le projet a été créé</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom_projet" class="app-form-label">
                                <i class="fas fa-project-diagram me-2"></i>Nom du Projet
                            </label>
                            <input type="text" id="nom_projet" name="nom_projet" value="{{ $projet->nom_projet }}" class="app-form-control" required>
                            <div class="app-form-text">Nom complet du projet</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-group">
                    <label for="description" class="app-form-label">
                        <i class="fas fa-align-left me-2"></i>Description
                    </label>
                    <textarea id="description" name="description" class="app-form-control" rows="3">{{ $projet->description }}</textarea>
                    <div class="app-form-text">Description détaillée du projet</div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="date_debut" class="app-form-label">
                                <i class="fas fa-play-circle me-2"></i>Date de début
                            </label>
                            <input type="date" id="date_debut" name="date_debut" value="{{ $projet->date_debut }}" class="app-form-control" required>
                            <div class="app-form-text">Date de démarrage du projet</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="date_fin" class="app-form-label">
                                <i class="fas fa-flag-checkered me-2"></i>Date de fin
                            </label>
                            <input type="date" id="date_fin" name="date_fin" value="{{ $projet->date_fin }}" class="app-form-control">
                            <div class="app-form-text">Date prévue de fin du projet (optionnel)</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="client" class="app-form-label">
                                <i class="fas fa-user-tie me-2"></i>Client
                            </label>
                            <select id="client" name="client" class="app-form-select" required>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $projet->client == $client->prenoms ? 'selected' : '' }}>{{ $client->nom_raison_sociale }}{{ $client->prenoms }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Client pour lequel le projet est réalisé</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="secteur_activite_id" class="app-form-label">
                                <i class="fas fa-industry me-2"></i>Secteur d'activité
                            </label>
                            <select id="secteur_activite_id" name="secteur_activite_id" class="app-form-select" required>
                                @foreach($secteurs as $secteur)
                                    <option value="{{ $secteur->id }}" {{ $projet->secteur_activite_id == $secteur->id ? 'selected' : '' }}>{{ $secteur->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Secteur d'activité concerné par le projet</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="chef_projet_id" class="app-form-label">
                                <i class="fas fa-user-tie me-2"></i>Chef de Projet
                            </label>
                            <select id="chef_projet_id" name="chef_projet_id" class="app-form-select">
                                <option value="">-- Sélectionnez un chef de projet --</option>
                                @foreach($chefsProjet as $chef)
                                    <option value="{{ $chef->id }}" {{ $projet->chef_projet_id == $chef->id ? 'selected' : '' }}>
                                        {{ $chef->prenom }} {{ $chef->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Chef de projet responsable</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="conducteur_travaux_id" class="app-form-label">
                                <i class="fas fa-hard-hat me-2"></i>Conducteur de travaux
                            </label>
                            <select id="conducteur_travaux_id" name="conducteur_travaux_id" class="app-form-select">
                                <option value="">-- Sélectionnez un conducteur de travaux --</option>
                                @foreach($conducteursTravaux as $conducteur)
                                    <option value="{{ $conducteur->id }}" {{ $projet->conducteur_travaux_id == $conducteur->id ? 'selected' : '' }}>
                                        {{ $conducteur->prenom }} {{ $conducteur->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Responsable de la conduite des travaux</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="hastva" class="app-form-label">
                                <i class="fas fa-percent me-2"></i>TVA applicable ?
                            </label>
                            <select id="hastva" name="hastva" class="app-form-select">
                                <option value="0" {{ !$projet->hastva ? 'selected' : '' }}>Non</option>
                                <option value="1" {{ $projet->hastva ? 'selected' : '' }}>Oui</option>
                            </select>
                            <div class="app-form-text">Application de la TVA sur ce projet</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <!-- Colonne vide pour l'alignement -->
                    </div>
                </div>

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="statut" class="app-form-label">
                                <i class="fas fa-tasks me-2"></i>Statut
                            </label>
                            <select id="statut" name="statut" class="app-form-select" required>
                                <option value="en cours" {{ $projet->statut == 'en cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminé" {{ $projet->statut == 'terminé' ? 'selected' : '' }}>Terminé</option>
                                <option value="annulé" {{ $projet->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                            </select>
                            <div class="app-form-text">État actuel du projet</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="bu_id" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Business Unit (BU)
                            </label>
                            <select id="bu_id" name="bu_id" class="app-form-select" required>
                                @foreach($bus as $bu)
                                    <option value="{{ $bu->id }}" {{ $projet->bu_id == $bu->id ? 'selected' : '' }}>{{ $bu->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Unité commerciale responsable du projet</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('projets.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-warning">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection