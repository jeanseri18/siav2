{{-- Page Edit - Modifier un contrat --}}
@extends('layouts.app')

@section('title', 'Modifier un contrat')
@section('page-title', 'Modifier un contrat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Récupérer l'ID du projet depuis le champ caché
    const projetId = $('#projet_id_hidden').val();
    
    if (projetId) {
        // Charger automatiquement le client du projet
        loadProjectClient(projetId);
    }
    
    function loadProjectClient(projetId) {
        const clientSelect = $('#client_id');
        
        if (projetId) {
            // Appel AJAX pour récupérer le client du projet
            $.ajax({
                url: `/contrats/projet/${projetId}/clients`,
                type: 'GET',
                success: function(clients) {
                    clientSelect.empty();
                    clientSelect.append('<option value="">Sélectionner un client</option>');
                    
                    if (clients.length > 0) {
                        clients.forEach(function(client) {
                            const clientName = client.nom_raison_sociale && client.prenoms 
                                ? client.nom_raison_sociale + ' ' + client.prenoms 
                                : (client.nom_raison_sociale || client.prenoms || '');
                            
                            clientSelect.append(`<option value="${client.id}" selected>${clientName}</option>`);
                        });
                        
                        // Désactiver le champ client car il est automatiquement défini par le projet
                        clientSelect.prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement du client du projet:', error);
                    clientSelect.prop('disabled', false);
                }
            });
        }
    }
});
</script>
@endpush

@section('content')
@include('sublayouts.projetdetail')

<div class=" app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-edit me-2"></i>Modifier le contrat : {{ $contrat->nom_contrat }}
                    </h2>
                </div>
                
                <div class="app-card-body">
                    @if ($errors->any())
                    <div class="app-alert app-alert-danger">
                        <div class="app-alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="app-alert-content">
                            <div class="app-alert-text">
                                @foreach ($errors->all() as $error)
                                    <p class="app-mb-1">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                    
                    <form action="{{ route('contrats.update', $contrat->id) }}" method="POST" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Zone Client -->
                        <div class="content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user"></i>Zone Client
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="client_id" class="form-label">
                                            <i class="fas fa-hashtag"></i>N° Client
                                        </label>
                                        <select name="client_id" id="client_id" class="form-select" required>
                                            <option value="">Sélectionner un client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" {{ old('client_id', $contrat->client_id) == $client->id ? 'selected' : '' }}>
                                                    {{ $client->nom_raison_sociale ?? '' }} {{ $client->prenoms ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="nom_client" class="form-label">
                                            <i class="fas fa-user"></i>Nom du client
                                        </label>
                                        <input type="text" class="form-control" id="nom_client" value="{{ $contrat->client->nom_raison_sociale ?? $contrat->client->prenoms ?? '' }}" readonly>
                                    </div>
                                    

                                    
                                    <div class="form-group">
                                        <label for="delai_paiement" class="form-label">
                                            <i class="fas fa-clock"></i>Délai de paiement
                                        </label>
                                        <input type="text" class="form-control" id="delai_paiement" value="{{ $contrat->client->delai_paiement ?? '' }}" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="secteur_activite" class="form-label">
                                            <i class="fas fa-industry"></i>Secteur d'activité
                                        </label>
                                        <input type="text" class="form-control" id="secteur_activite" value="{{ $contrat->client->secteur_activite ?? '' }}" readonly>
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                        
                        <!-- Zone Contrat -->
                        <div class="content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-file-contract"></i>Zone Contrat
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="ref_contrat" class="form-label">
                                            <i class="fas fa-hashtag"></i>N° Contrat
                                        </label>
                                        <input type="text" class="form-control" id="ref_contrat" name="ref_contrat" value="{{ old('ref_contrat', $contrat->ref_contrat ?? '') }}" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="nom_contrat" class="form-label">
                                            <i class="fas fa-file-signature"></i>Nom du Contrat
                                        </label>
                                        <input type="text" class="form-control" id="nom_contrat" name="nom_contrat" value="{{ old('nom_contrat', $contrat->nom_contrat) }}" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="type_travaux" class="form-label">
                                            <i class="fas fa-hard-hat"></i>Type du travaux
                                        </label>
                                        <select name="type_travaux" id="type_travaux" class="form-select" required>
                                            <option value="">Sélectionner un type de travaux</option>
                                            @foreach ($typeTravaux as $type)
                                                <option value="{{ $type->nom }}" {{ old('type_travaux', $contrat->type_travaux) == $type->nom ? 'selected' : '' }}>
                                                    {{ $type->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="date_debut" class="form-label">
                                            <i class="fas fa-calendar-alt"></i>Date de début
                                        </label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ old('date_debut', $contrat->date_debut) }}" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="date_fin" class="form-label">
                                            <i class="fas fa-calendar-check"></i>Date de fin
                                        </label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ old('date_fin', $contrat->date_fin) }}">
                                    </div>
                                </div>
                                
                                <!-- Chef chantier -->
                                <h5 class="mt-4 mb-3">
                                    <i class="fas fa-user-tie"></i>Chef chantier
                                </h5>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="chef_chantier_id" class="form-label">
                                            <i class="fas fa-user"></i>Nom et Prénoms
                                        </label>
                                        <select name="chef_chantier_id" id="chef_chantier_id" class="form-select">
                                            <option value="">Sélectionner un chef de chantier</option>
                                            @foreach($chefsChantier as $chef)
                                                <option value="{{ $chef->id }}" {{ old('chef_chantier_id', $contrat->chef_chantier_id) == $chef->id ? 'selected' : '' }}>
                                                    {{ $chef->nom }} {{ $chef->prenom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="chef_email" class="form-label">
                                            <i class="fas fa-envelope"></i>Email
                                        </label>
                                        <input type="email" class="form-control" id="chef_email" value="{{ $contrat->chefChantier->email ?? '' }}" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="chef_telephone" class="form-label">
                                            <i class="fas fa-phone"></i>Téléphone
                                        </label>
                                        <input type="text" class="form-control" id="chef_telephone" value="{{ $contrat->chefChantier->telephone ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Zone Financière -->
                        <div class="content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-money-bill-wave"></i>Zone Financière
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="tva_18" class="form-label">
                                            <i class="fas fa-percentage"></i>Facturation client soumise à TVA18%
                                        </label>
                                        <select class="form-select" id="tva_18" name="tva_18">
                                            <option value="1" {{ old('tva_18', $contrat->tva_18 ?? 1) == 1 ? 'selected' : '' }}>Oui</option>
                                            <option value="0" {{ old('tva_18', $contrat->tva_18 ?? 1) == 0 ? 'selected' : '' }}>Non</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="taux_garantie" class="form-label">
                                            <i class="fas fa-shield-alt"></i>Retenues de garantie (%)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="taux_garantie" name="taux_garantie" value="{{ old('taux_garantie', $contrat->taux_garantie) }}" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="retenue_decennale" class="form-label">
                                            <i class="fas fa-shield-alt"></i>Retenue décennale (%)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="retenue_decennale" name="retenue_decennale" value="{{ old('retenue_decennale', $contrat->retenue_decennale ?? 0) }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="avance_demarrage" class="form-label">
                                            <i class="fas fa-money-check-alt"></i>Avance de démarrage
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="avance_demarrage" name="avance_demarrage" value="{{ old('avance_demarrage', $contrat->avance_demarrage ?? 0) }}">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="montant" class="form-label">
                                            <i class="fas fa-money-bill-wave"></i>Mt du contrat (CFA)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="montant" name="montant" value="{{ old('montant', $contrat->montant) }}" placeholder="Sera mis à jour après validation du DQE">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Information système -->
                        <div class="content-card mb-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle"></i>Information système
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="statut" class="form-label">
                                            <i class="fas fa-info-circle"></i>Statut du contrat
                                        </label>
                                        <select class="form-select" id="statut" name="statut" required>
                                            <option value="en cours" {{ old('statut', $contrat->statut) == 'en cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="terminé" {{ old('statut', $contrat->statut) == 'terminé' ? 'selected' : '' }}>Terminé</option>
                                            <option value="annulé" {{ old('statut', $contrat->statut) == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('contrats.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection