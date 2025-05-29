{{-- Page Create - Ajouter une Facture --}}
@extends('layouts.app')

@section('title', 'Ajouter une Facture')
@section('page-title', 'Ajouter une Facture')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('factures.index') }}">Factures</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-invoice me-2"></i>Ajouter une Facture
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('factures.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <div class="app-form-group">
                            <label for="num" class="app-form-label">
                                <i class="fas fa-hashtag me-2"></i>Numéro de Facture
                            </label>
                            <input type="text" name="num" class="app-form-control" required>
                            <div class="app-form-text">Entrez le numéro unique de la facture</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="type" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Type de Sélection
                            </label>
                            <select id="type_select" name="type" class="app-form-select">
                                <option value="">Sélectionner</option>
                                <option value="artisan">Artisan</option>
                                <option value="contrat">Contrat</option>
                                <option value="prestation">Prestation</option>
                            </select>
                            <div class="app-form-text">Sélectionnez le type de facturation</div>
                        </div>
                        
                        <div id="prestation_select" class="app-form-group" style="display: none;">
                            <label for="id_prestation" class="app-form-label">
                                <i class="fas fa-tools me-2"></i>Prestation (optionnel)
                            </label>
                            <select name="id_prestation" class="app-form-select">
                                <option value="">Sélectionner</option>
                                @foreach($prestations as $prestation)
                                    <option value="{{ $prestation->id }}">{{ $prestation->artisan->nom }} - {{ $prestation->montant }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Sélectionnez la prestation associée à cette facture</div>
                        </div>
                        
                        <div id="contrat_select" class="app-form-group" style="display: none;">
                            <label for="id_contrat" class="app-form-label">
                                <i class="fas fa-file-contract me-2"></i>Contrat (optionnel)
                            </label>
                            <select name="id_contrat" class="app-form-select">
                                <option value="">Sélectionner</option>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}">{{ $contrat->nom_contrat }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Sélectionnez le contrat associé à cette facture</div>
                        </div>
                        
                        <div id="artisan_select" class="app-form-group" style="display: none;">
                            <label for="id_artisan" class="app-form-label">
                                <i class="fas fa-user-hard-hat me-2"></i>Artisan
                            </label>
                            <select name="id_artisan" class="app-form-select">
                                @foreach($artisans as $artisan)
                                    <option value="{{ $artisan->id }}">{{ $artisan->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Sélectionnez l'artisan associé à cette facture</div>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="montant_ht" class="app-form-label">
                                        <i class="fas fa-money-bill me-2"></i>Montant HT
                                    </label>
                                    <input type="number" name="montant_ht" class="app-form-control" required>
                                    <div class="app-form-text">Montant hors taxes</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="montant_total" class="app-form-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>Montant Total
                                    </label>
                                    <input type="number" name="montant_total" class="app-form-control" required>
                                    <div class="app-form-text">Montant total avec taxes</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="ca_realise" class="app-form-label">
                                        <i class="fas fa-chart-line me-2"></i>CA Réalisé
                                    </label>
                                    <input type="number" name="ca_realise" class="app-form-control" value="0">
                                    <div class="app-form-text">Chiffre d'affaires réalisé</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="montant_reglement" class="app-form-label">
                                        <i class="fas fa-hand-holding-usd me-2"></i>Montant Réglé
                                    </label>
                                    <input type="number" name="montant_reglement" class="app-form-control" value="0">
                                    <div class="app-form-text">Montant déjà réglé</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_emission" class="app-form-label">
                                        <i class="fas fa-calendar-day me-2"></i>Date d'émission
                                    </label>
                                    <input type="date" name="date_emission" class="app-form-control" required>
                                    <div class="app-form-text">Date d'émission de la facture</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="statut" class="app-form-label">
                                        <i class="fas fa-info-circle me-2"></i>Statut
                                    </label>
                                    <select name="statut" class="app-form-select" required>
                                        <option value="en attente">En attente</option>
                                        <option value="payée">Payée</option>
                                        <option value="annulée">Annulée</option>
                                    </select>
                                    <div class="app-form-text">État actuel de la facture</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('factures.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type_select').addEventListener('change', function() {
        const artisanSelect = document.getElementById('artisan_select');
        const contratSelect = document.getElementById('contrat_select');
        const prestationSelect = document.getElementById('prestation_select');

        // Réinitialiser la sélection des autres champs
        document.querySelector('[name="id_artisan"]').value = "";
        document.querySelector('[name="id_contrat"]').value = "";
        document.querySelector('[name="id_prestation"]').value = "";

        // Masquer tous les champs
        artisanSelect.style.display = 'none';
        contratSelect.style.display = 'none';
        prestationSelect.style.display = 'none';

        // Afficher uniquement le champ sélectionné
        const selectedType = this.value;
        if (selectedType === 'artisan') {
            artisanSelect.style.display = 'block';
        } else if (selectedType === 'contrat') {
            contratSelect.style.display = 'block';
        } else if (selectedType === 'prestation') {
            prestationSelect.style.display = 'block';
        }
    });
</script>
@endpush
@endsection