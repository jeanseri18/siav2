@extends('layouts.app')

@section('title', 'Créer un Contrat')
@section('page-title', 'Créer un Contrat')

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-plus me-2"></i>Créer un nouveau contrat
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('contrats.all') }}" class="app-btn app-btn-secondary app-btn-icon">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="app-alert app-alert-success">
            <div class="app-alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">{{ session('success') }}</div>
            </div>
            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        @if($errors->any())
        <div class="app-alert app-alert-danger">
            <div class="app-alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">Veuillez corriger les erreurs suivantes :</div>
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="app-card-body">
            <form action="{{ route('contrats.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="projet_id" class="form-label">Projet <span class="text-danger">*</span></label>
                            <select class="form-select" id="projet_id" name="projet_id" required>
                                <option value="">Sélectionner un projet</option>
                                @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>{{ $projet->nom_projet }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nom_contrat" class="form-label">Nom du contrat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom_contrat" name="nom_contrat" value="{{ old('nom_contrat') }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_debut" class="form-label">Date de début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ old('date_debut') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_fin" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="montant" name="montant" step="0.01" value="{{ old('montant') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="en cours" {{ old('statut') == 'en cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminé" {{ old('statut') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                                <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type_travaux" class="form-label">Type de travaux <span class="text-danger">*</span></label>
                            <select class="form-select" id="type_travaux" name="type_travaux" required>
                                <option value="">Sélectionner un type de travaux</option>
                                @foreach($typeTravaux as $type)
                                    <option value="{{ $type->nom }}" {{ old('type_travaux') == $type->nom ? 'selected' : '' }}>
                                        {{ $type->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="taux_garantie" class="form-label">Taux de garantie (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="taux_garantie" name="taux_garantie" step="0.01" value="{{ old('taux_garantie') }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                    <select class="form-select" id="client_id" name="client_id" required>
                        <option value="">Sélectionner un client</option>
                        <!-- Les clients seront chargés dynamiquement selon le projet sélectionné -->
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('contrats.all') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Créer le contrat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Charger les clients selon le projet sélectionné
    $(document).on('change', '#projet_id', function() {
        const projetId = $(this).val();
        const clientSelect = $('#client_id');
        
        console.log('Projet sélectionné:', projetId);
        
        clientSelect.html('<option value="">Chargement...</option>');
        
        if (projetId) {
            const ajaxUrl = `/contrats/projet/${projetId}/clients`;
            console.log('URL AJAX:', ajaxUrl);
            
            // Appel AJAX pour récupérer les clients du projet
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                beforeSend: function() {
                    console.log('AJAX démarré...');
                },
                success: function(clients) {
                    console.log('AJAX réussi, clients reçus:', clients);
                    clientSelect.html('<option value="">Sélectionner un client</option>');
                    
                    clients.forEach(function(client) {
                        const nomComplet = client.prenoms ? `${client.nom_raison_sociale} ${client.prenoms}` : client.nom_raison_sociale;
                        const selected = '{{ old("client_id") }}' == client.id ? 'selected' : '';
                        clientSelect.append(`<option value="${client.id}" ${selected}>${nomComplet}</option>`);
                    });
                    
                    // Si il n'y a qu'un seul client (le client du projet), le sélectionner automatiquement
                    if (clients.length === 1) {
                        clientSelect.val(clients[0].id);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    clientSelect.html('<option value="">Erreur de chargement</option>');
                }
            });
        } else {
            clientSelect.html('<option value="">Sélectionner un client</option>');
        }
    });
    
    // Déclencher le chargement des clients si un projet est déjà sélectionné (cas de validation d'erreur)
    if ($('#projet_id').val()) {
        $('#projet_id').trigger('change');
    }
});
</script>
@endpush