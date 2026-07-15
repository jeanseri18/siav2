{{-- Créer un contrat — aligné sur le formulaire projet --}}
@extends('layouts.app')

@section('title', 'Créer un contrat')
@section('page-title', 'Créer un contrat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Créer</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in">
    <div class="app-card shadow-sm border-0">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-contract me-2"></i>Créer un nouveau contrat
            </h2>
        </div>

        <form action="{{ route('contrats.store') }}" method="POST" class="app-card-body">
            @csrf
            <input type="hidden" id="projet_id_hidden" name="projet_id_hidden" value="{{ session('projet_id') }}">

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                La <strong>référence du contrat</strong> est <strong>générée automatiquement</strong> à l’enregistrement. Le <strong>montant</strong> peut être complété ou mis à jour après <strong>validation du DQE</strong>.
            </div>

            {{-- Client --}}
            <div class="app-card border-start border-4 mb-4 shadow-sm" style="border-color: #033d71 !important;">
                <div class="app-card-header py-2 px-3" style="background-color: rgba(3, 61, 113, 0.08);">
                    <h5 class="mb-0 fw-semibold" style="color: #033d71;">
                        <i class="fas fa-user-tie me-2"></i>Client
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_id" class="app-form-label">N° client</label>
                                <select name="client_id" id="client_id" class="app-form-control @error('client_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner un client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom_raison_sociale && $client->prenoms ? $client->nom_raison_sociale . ' ' . $client->prenoms : ($client->nom_raison_sociale ?? $client->prenoms) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom_client" class="app-form-label">Nom du client</label>
                                <input type="text" class="app-form-control" id="nom_client" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delai_paiement" class="app-form-label">Délai de paiement (jours)</label>
                                <input type="text" class="app-form-control bg-light" id="delai_paiement" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="secteur_activite" class="app-form-label">Secteur d’activité</label>
                                <input type="text" class="app-form-control bg-light" id="secteur_activite" readonly tabindex="-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contrat --}}
            <div class="app-card border-start border-4 mb-4 shadow-sm" style="border-color: #033d71 !important;">
                <div class="app-card-header py-2 px-3" style="background-color: rgba(3, 61, 113, 0.08);">
                    <h5 class="mb-0 fw-semibold" style="color: #033d71;">
                        <i class="fas fa-file-signature me-2"></i>Contrat
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom_contrat" class="app-form-label">Nom du contrat</label>
                                <input type="text" name="nom_contrat" id="nom_contrat" class="app-form-control @error('nom_contrat') is-invalid @enderror"
                                       value="{{ old('nom_contrat') }}" required>
                                @error('nom_contrat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_travaux" class="app-form-label">Type de travaux</label>
                                <select name="type_travaux" id="type_travaux" class="app-form-control @error('type_travaux') is-invalid @enderror" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach ($typeTravaux as $type)
                                        <option value="{{ $type->nom }}" {{ old('type_travaux') == $type->nom ? 'selected' : '' }}>{{ $type->nom }}</option>
                                    @endforeach
                                </select>
                                @error('type_travaux')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_debut" class="app-form-label">Date de début</label>
                                <input type="date" name="date_debut" id="date_debut" class="app-form-control @error('date_debut') is-invalid @enderror"
                                       value="{{ old('date_debut') }}" required>
                                @error('date_debut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_fin" class="app-form-label">Date de fin</label>
                                <input type="date" name="date_fin" id="date_fin" class="app-form-control @error('date_fin') is-invalid @enderror"
                                       value="{{ old('date_fin') }}">
                                @error('date_fin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-semibold mt-2 mb-3" style="color: #033d71;">
                        <i class="fas fa-hard-hat me-2"></i>Chef de chantier
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="chef_chantier_id" class="app-form-label">Nom et prénom</label>
                                <select name="chef_chantier_id" id="chef_chantier_id" class="app-form-control @error('chef_chantier_id') is-invalid @enderror">
                                    <option value="">Sélectionner</option>
                                    @foreach ($chefsChantier as $chef)
                                        <option value="{{ $chef->id }}"
                                            data-email="{{ e($chef->email ?? '') }}"
                                            data-telephone="{{ e($chef->telephone ?? '') }}"
                                            {{ old('chef_chantier_id') == $chef->id ? 'selected' : '' }}>
                                            {{ $chef->nom }} {{ $chef->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('chef_chantier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="chef_email" class="app-form-label">E-mail</label>
                                <input type="email" class="app-form-control bg-light" id="chef_email" readonly tabindex="-1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="chef_telephone" class="app-form-label">Téléphone</label>
                                <input type="text" class="app-form-control bg-light" id="chef_telephone" readonly tabindex="-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Financier --}}
            <div class="app-card border-start border-4 mb-4 shadow-sm" style="border-color: #033d71 !important;">
                <div class="app-card-header py-2 px-3" style="background-color: rgba(3, 61, 113, 0.08);">
                    <h5 class="mb-0 fw-semibold" style="color: #033d71;">
                        <i class="fas fa-coins me-2"></i>Paramètres financiers
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tva_18" class="app-form-label">Facturation client soumise à TVA 18&nbsp;%</label>
                                <select class="app-form-control" id="tva_18" name="tva_18">
                                    <option value="1" {{ old('tva_18', '1') == '1' ? 'selected' : '' }}>Oui</option>
                                    <option value="0" {{ old('tva_18') === '0' || old('tva_18') === 0 ? 'selected' : '' }}>Non</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="taux_garantie" class="app-form-label">Retenue de garantie (%)</label>
                                <input type="number" step="0.01" name="taux_garantie" id="taux_garantie"
                                       class="app-form-control @error('taux_garantie') is-invalid @enderror"
                                       value="{{ old('taux_garantie') }}" required>
                                @error('taux_garantie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="retenue_decennale" class="app-form-label">Retenue décennale (%)</label>
                                <input type="number" step="0.01" name="retenue_decennale" id="retenue_decennale" class="app-form-control @error('retenue_decennale') is-invalid @enderror"
                                       value="{{ old('retenue_decennale', 0) }}">
                                @error('retenue_decennale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="avance_demarrage" class="app-form-label">Avance de démarrage (FCFA)</label>
                                <input type="number" step="0.01" name="avance_demarrage" id="avance_demarrage" class="app-form-control @error('avance_demarrage') is-invalid @enderror"
                                       value="{{ old('avance_demarrage', 0) }}">
                                @error('avance_demarrage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="montant" class="app-form-label">Montant du contrat (FCFA)</label>
                                <input type="number" step="0.01" name="montant" id="montant" class="app-form-control @error('montant') is-invalid @enderror"
                                       value="{{ old('montant') }}" placeholder="Optionnel — DQE">
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="mb-3 w-100">
                                <label for="statut" class="app-form-label">Statut</label>
                                <select class="app-form-control @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                    <option value="non débuté" {{ old('statut', 'non débuté') == 'non débuté' ? 'selected' : '' }}>Non débuté</option>
                                    <option value="en cours" {{ old('statut') == 'en cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="terminé" {{ old('statut') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                                    <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
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
@endsection

@push('scripts')
<script>
$(function () {
    $('#client_id').on('change', function () {
        var clientId = $(this).val();
        if (!clientId) {
            $('#nom_client, #delai_paiement, #secteur_activite').val('');
            return;
        }
        $.get('/api/clients/' + clientId)
            .done(function (data) {
                $('#nom_client').val(data.nom_raison_sociale || data.prenoms || '');
                $('#delai_paiement').val(data.delai_paiement != null ? data.delai_paiement : '');
                $('#secteur_activite').val(data.secteur_activite || '');
            })
            .fail(function () {
                $('#nom_client, #delai_paiement, #secteur_activite').val('');
            });
    });

    function remplirContactChefChantier() {
        var $sel = $('#chef_chantier_id');
        var $opt = $sel.find('option:selected');
        if (!$sel.val()) {
            $('#chef_email, #chef_telephone').val('');
            return;
        }
        $('#chef_email').val($opt.attr('data-email') || '');
        $('#chef_telephone').val($opt.attr('data-telephone') || '');
    }
    $('#chef_chantier_id').on('change', remplirContactChefChantier);
    remplirContactChefChantier();

    var projetId = $('#projet_id_hidden').val();
    if (projetId) {
        var clientSelect = $('#client_id');
        $.get('/contrats/projet/' + projetId + '/clients')
            .done(function (clients) {
                clientSelect.empty().append('<option value=\"\">Sélectionner un client</option>');
                if (clients.length > 0) {
                    clients.forEach(function (client) {
                        var name = (client.nom_raison_sociale && client.prenoms)
                            ? client.nom_raison_sociale + ' ' + client.prenoms
                            : (client.nom_raison_sociale || client.prenoms || '');
                        clientSelect.append($('<option>', { value: client.id, text: name, selected: true }));
                    });
                    clientSelect.trigger('change');
                }
            });
    }
});
</script>
@endpush
