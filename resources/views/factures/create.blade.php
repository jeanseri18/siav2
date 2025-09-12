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
        <div class="col-md-12">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-invoice me-2"></i>Ajouter une Facture
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <!-- Affichage des erreurs de validation -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation :</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('factures.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <div class="row">
                            <!-- Colonne de gauche pour les décomptes -->
                            <div class="col-md-4">
                                <!-- Décomptes en cours pour l'artisan sélectionné -->
                                <div id="decomptes_artisan" style="display: none;" class="mb-4">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-list-alt"></i> Décomptes en cours (non réglés)</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div id="decomptes_list">
                                                <!-- Les décomptes seront chargés ici via JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Colonne de droite pour le formulaire -->
                            <div class="col-md-8">
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
                                    <input type="number" name="montant_ht" id="montant_ht" class="app-form-control" step="0.01" required>
                                    <div class="app-form-text">Montant hors taxes de la facture</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="tva" class="app-form-label">
                                        <i class="fas fa-percent me-2"></i>TVA (18%)
                                    </label>
                                    <input type="number" name="tva" id="tva" class="app-form-control" step="0.01" readonly>
                                    <div class="app-form-text">Montant de la TVA calculé automatiquement</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="montant_total" class="app-form-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>Montant à régler HT
                                    </label>
                                    <input type="number" name="montant_total" id="montant_total" class="app-form-control" step="0.01" readonly>
                                    <div class="app-form-text">Montant à régler hors taxes</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="montant_ttc" class="app-form-label">
                                        <i class="fas fa-coins me-2"></i>Montant à régler TTC
                                    </label>
                                    <input type="number" name="montant_ttc" id="montant_ttc" class="app-form-control" step="0.01" readonly>
                                    <div class="app-form-text">Montant à régler toutes taxes comprises</div>
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
                                    <label for="decompte" class="app-form-label">
                                        <i class="fas fa-calculator me-2"></i>Décompte (optionnel)
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="decompte" id="decompte" class="app-form-control" value="0">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    <div class="app-form-text">Montant du décompte</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="num_decompte" class="app-form-label">
                                        <i class="fas fa-hashtag me-2"></i>Numéro de Décompte (optionnel)
                                    </label>
                                    <input type="text" name="num_decompte" id="num_decompte" class="app-form-control">
                                    <div class="app-form-text">Numéro de référence du décompte</div>
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
        
        // Masquer les décomptes si on change de type
        document.getElementById('decomptes_artisan').style.display = 'none';
    });
    
    // Gérer la sélection d'artisan pour afficher les décomptes
    document.querySelector('[name="id_artisan"]').addEventListener('change', function() {
        const artisanId = this.value;
        const decomptesDiv = document.getElementById('decomptes_artisan');
        const decomptesList = document.getElementById('decomptes_list');
        
        if (artisanId) {
            // Afficher un loader
            decomptesList.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement des décomptes...</div>';
            decomptesDiv.style.display = 'block';
            
            // Récupérer les décomptes via AJAX
            fetch(`/factures/artisan/${artisanId}/decomptes`)
                .then(response => response.json())
                .then(data => {
                    if (data.decomptes && data.decomptes.length > 0) {
                         let cardsHtml = '';
                         
                         data.decomptes.forEach(decompte => {
                             const resteARegler = parseFloat(decompte.montant_total) - parseFloat(decompte.montant_reglement || 0);
                             cardsHtml += `
                                 <div class="card mb-2 border-left-warning">
                                     <div class="card-body p-2">
                                         <div class="d-flex justify-content-between align-items-center mb-1">
                                             <small class="text-muted">Facture ${decompte.num}</small>
                                             <span class="badge bg-warning text-dark">${decompte.statut}</span>
                                         </div>
                                         <div class="mb-1">
                                             <strong>Décompte ${decompte.num_decompte}</strong>
                                         </div>
                                         <div class="row text-sm">
                                             <div class="col-6">
                                                 <small class="text-muted">Montant:</small><br>
                                                 <strong>${parseFloat(decompte.decompte).toLocaleString('fr-FR', {minimumFractionDigits: 2})} €</strong>
                                             </div>
                                             <div class="col-6">
                                                 <small class="text-muted">Reste:</small><br>
                                                 <strong class="text-danger">${resteARegler.toLocaleString('fr-FR', {minimumFractionDigits: 2})} €</strong>
                                             </div>
                                         </div>
                                         <div class="mt-1">
                                             <small class="text-muted">${new Date(decompte.date_emission).toLocaleDateString('fr-FR')}</small>
                                         </div>
                                     </div>
                                 </div>
                             `;
                         });
                         decomptesList.innerHTML = cardsHtml;
                    } else {
                        decomptesList.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Aucun décompte en cours pour cet artisan.</div>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des décomptes:', error);
                    decomptesList.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement des décomptes.</div>';
                });
        } else {
            decomptesDiv.style.display = 'none';
        }
    });
    
    // Calcul automatique de la TVA et des montants
    document.getElementById('montant_ht').addEventListener('input', function() {
        const montantHT = parseFloat(this.value) || 0;
        const tauxTVA = 0.18; // 18%
        
        // Calculer la TVA
        const tva = montantHT * tauxTVA;
        document.getElementById('tva').value = tva.toFixed(2);
        
        // Calculer le montant à régler HT (même que montant HT pour l'instant)
        document.getElementById('montant_total').value = montantHT.toFixed(2);
        
        // Calculer le montant à régler TTC
        const montantTTC = montantHT + tva;
        document.getElementById('montant_ttc').value = montantTTC.toFixed(2);
    });
</script>
@endpush
@endsection