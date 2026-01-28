{{-- Page Create - Créer un contrat --}}
@extends('layouts.app')

@section('title', 'Créer un contrat')
@section('page-title', 'Créer un contrat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Créer</li>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Charger les informations du client sélectionné
    $('#client_id').change(function() {
        var clientId = $(this).val();
        if (clientId) {
            $.ajax({
                url: '/api/clients/' + clientId,
                method: 'GET',
                success: function(data) {
                    $('#nom_client').val(data.nom_raison_sociale || data.prenoms || '');
                    $('#delai_paiement').val(data.delai_paiement || '');
                    $('#secteur_activite').val(data.secteur_activite || '');
                    

                },
                error: function() {
                    // Réinitialiser les champs en cas d'erreur
                    $('#nom_client').val('');
                    $('#delai_paiement').val('');
                    $('#secteur_activite').val('');
                }
            });
        } else {
            // Réinitialiser les champs si aucun client sélectionné
            $('#nom_client').val('');
            $('#delai_paiement').val('');
            $('#secteur_activite').val('');
        }
    });
    
    // Charger les informations du chef chantier sélectionné
    $('#chef_chantier_id').change(function() {
        var chefId = $(this).val();
        if (chefId) {
            $.ajax({
                url: '/api/employes/' + chefId,
                method: 'GET',
                success: function(data) {
                    $('#chef_email').val(data.email || '');
                    $('#chef_telephone').val(data.telephone || '');
                },
                error: function() {
                    $('#chef_email').val('');
                    $('#chef_telephone').val('');
                }
            });
        } else {
            $('#chef_email').val('');
            $('#chef_telephone').val('');
        }
    });
});
</script>

<style>
.content-card {
    background: #fff;
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.content-card .card-header {
    padding: 1rem 1.25rem;
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.content-card .card-header .card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #5a5c69;
}

.content-card .card-body {
    padding: 1.25rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #5a5c69;
}

.form-label i {
    margin-right: 0.5rem;
    width: 16px;
    text-align: center;
}

.form-control, .form-select {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 0.9rem;
    line-height: 1.5;
    color: #6e707e;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    color: #6e707e;
    background-color: #fff;
    border-color: #bac8f3;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-control[readonly] {
    background-color: #eaecf4;
    opacity: 1;
}

.bg-light {
    background-color: #f8f9fc !important;
}

.border {
    border: 1px solid #e3e6f0 !important;
}

.rounded {
    border-radius: 0.35rem !important;
}

.p-2 {
    padding: 0.5rem !important;
}

.mt-4 {
    margin-top: 1.5rem !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

.text-muted {
    color: #858796 !important;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -0.75rem;
    margin-left: -0.75rem;
}

.col-md-4, .col-md-6 {
    position: relative;
    width: 100%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .col-md-4, .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
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
                        <i class="fas fa-file-contract me-2"></i>Créer un nouveau contrat
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
                    
                    <form action="{{ route('contrats.store') }}" method="POST" class="app-form">
                        @csrf
                        <input type="hidden" id="projet_id_hidden" name="projet_id_hidden" value="{{ session('projet_id') }}">
                        
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
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">
                                                    {{ $client->nom_raison_sociale && $client->prenoms ? $client->nom_raison_sociale . ' ' . $client->prenoms : ($client->nom_raison_sociale ?? $client->prenoms) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="nom_client" class="form-label">
                                            <i class="fas fa-user"></i>Nom du client
                                        </label>
                                        <input type="text" class="form-control" id="nom_client" readonly>
                                    </div>
                                    

                                    
                                    <div class="form-group">
                                        <label for="delai_paiement" class="form-label">
                                            <i class="fas fa-clock"></i>Délai de paiement
                                        </label>
                                        <input type="text" class="form-control" id="delai_paiement" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="secteur_activite" class="form-label">
                                            <i class="fas fa-industry"></i>Secteur d'activité
                                        </label>
                                        <input type="text" class="form-control" id="secteur_activite" readonly>
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
                                        <input type="text" class="form-control" id="ref_contrat" name="ref_contrat" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="nom_contrat" class="form-label">
                                            <i class="fas fa-file-signature"></i>Nom du Contrat
                                        </label>
                                        <input type="text" class="form-control" id="nom_contrat" name="nom_contrat" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="type_travaux" class="form-label">
                                            <i class="fas fa-hard-hat"></i>Type du travaux
                                        </label>
                                        <select name="type_travaux" id="type_travaux" class="form-select" required>
                                            <option value="">Sélectionner un type Travaux</option>
                                            @foreach ($typeTravaux as $type)
                                                <option value="{{ $type->nom }}">{{ $type->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="date_debut" class="form-label">
                                            <i class="fas fa-calendar-alt"></i>Date de début
                                        </label>
                                        <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="date_fin" class="form-label">
                                            <i class="fas fa-calendar-check"></i>Date de fin
                                        </label>
                                        <input type="date" class="form-control" id="date_fin" name="date_fin">
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
                                            @foreach ($chefsChantier as $chef)
                                                <option value="{{ $chef->id }}">
                                                    {{ $chef->nom }} {{ $chef->prenom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="chef_email" class="form-label">
                                            <i class="fas fa-envelope"></i>Email
                                        </label>
                                        <input type="email" class="form-control" id="chef_email" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="chef_telephone" class="form-label">
                                            <i class="fas fa-phone"></i>Téléphone
                                        </label>
                                        <input type="text" class="form-control" id="chef_telephone" readonly>
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
                                            <option value="1">Oui</option>
                                            <option value="0">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="taux_garantie" class="form-label">
                                            <i class="fas fa-shield-alt"></i>Retenues de garantie (%)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="taux_garantie" name="taux_garantie" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="retenue_decennale" class="form-label">
                                            <i class="fas fa-shield-alt"></i>Retenue décennale (%)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="retenue_decennale" name="retenue_decennale" value="0">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="avance_demarrage" class="form-label">
                                            <i class="fas fa-money-check-alt"></i>Avance de démarrage
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="avance_demarrage" name="avance_demarrage" value="0">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="montant" class="form-label">
                                            <i class="fas fa-money-bill-wave"></i>Mt du contrat (CFA)
                                        </label>
                                        <input type="number" step="0.01" class="form-control" id="montant" name="montant" placeholder="Sera mis à jour après validation du DQE">
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
                                            <option value="en cours">En cours</option>
                                            <option value="terminé">Terminé</option>
                                            <option value="annulé">Annulé</option>
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
                                <i class="fas fa-save me-2"></i>Créer le contrat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection