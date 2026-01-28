{{-- Page Create - Nouveau Bon de Commande --}}
@extends('layouts.app')

@section('title', 'Nouveau Bon de Commande')
@section('page-title', 'Nouveau Bon de Commande')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bon-commandes.index') }}">Bons de Commande</a></li>
<li class="breadcrumb-item active">Nouveau</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-invoice me-2"></i>Nouveau Bon de Commande
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('bon-commandes.index') }}" class="app-btn app-btn-secondary app-btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="app-card-body">
                    <form action="{{ route('bon-commandes.store') }}" method="POST" class="app-form">
                        @csrf

                        <!-- Informations générales -->
                        <div class="app-card mb-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                                </h3>
                            </div>
                            <div class="app-card-body">
                                <div class="app-form row">
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="date_commande" class="app-form-label">
                                                <i class="fas fa-calendar-alt me-2"></i>Date de commande
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="app-form-control @error('date_commande') is-invalid @enderror" 
                                                id="date_commande" name="date_commande" value="{{ old('date_commande', date('Y-m-d')) }}" required>
                                            @error('date_commande')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Date à laquelle la commande est passée</div>
                                        </div>
                                    </div>
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="date_livraison_prevue" class="app-form-label">
                                                <i class="fas fa-truck me-2"></i>Date de livraison prévue
                                            </label>
                                            <input type="date" class="app-form-control @error('date_livraison_prevue') is-invalid @enderror" 
                                                id="date_livraison_prevue" name="date_livraison_prevue" value="{{ old('date_livraison_prevue') }}">
                                            @error('date_livraison_prevue')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Date prévue pour la livraison</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="app-form row">
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="demande_cotation_id" class="app-form-label">
                                                <i class="fas fa-file-invoice me-2"></i>Demande de cotation terminée
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="app-form-select @error('demande_cotation_id') is-invalid @enderror" 
                                                id="demande_cotation_id" name="demande_cotation_id" required>
                                                <option value="">-- Sélectionner une demande de cotation --</option>
                                                @foreach($demandesCotation as $demande)
                                                    <option value="{{ $demande->id }}" {{ old('demande_cotation_id') == $demande->id ? 'selected' : '' }}>
                                                        {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('demande_cotation_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Demande de cotation terminée à transformer en bon de commande</div>
                                        </div>
                                    </div>
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="fournisseur_id" class="app-form-label">
                                                <i class="fas fa-building me-2"></i>Fournisseur
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="app-form-select @error('fournisseur_id') is-invalid @enderror" 
                                                id="fournisseur_id" name="fournisseur_id" required>
                                                <option value="">-- Sélectionner un fournisseur --</option>
                                                @foreach($fournisseurs as $fournisseur)
                                                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                        {{ $fournisseur->nom }}   {{ $fournisseur->prenoms }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('fournisseur_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Fournisseur auprès duquel commander</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="app-form row">
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="mode_reglement" class="app-form-label">
                                                <i class="fas fa-credit-card me-2"></i>Mode de règlement
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="app-form-select @error('mode_reglement') is-invalid @enderror" 
                                                id="mode_reglement" name="mode_reglement" required>
                                                <option value="">-- Sélectionner un mode de règlement --</option>
                                                @foreach($modesPaiement as $mode)
                                                    <option value="{{ $mode->nom }}" {{ old('mode_reglement') == $mode->nom ? 'selected' : '' }}>
                                                        {{ $mode->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('mode_reglement')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Mode de règlement convenu</div>
                                        </div>
                                    </div>
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="delai_reglement" class="app-form-label">
                                                <i class="fas fa-clock me-2"></i>Délai de règlement
                                            </label>
                                            <select class="app-form-select @error('delai_reglement') is-invalid @enderror" 
                                                id="delai_reglement" name="delai_reglement">
                                                <option value="">-- Sélectionner un délai --</option>
                                                <option value="0" {{ old('delai_reglement') == '0' ? 'selected' : '' }}>Comptant</option>
                                                <option value="15" {{ old('delai_reglement') == '15' ? 'selected' : '' }}>15 jours</option>
                                                <option value="30" {{ old('delai_reglement') == '30' ? 'selected' : '' }}>30 jours</option>
                                                <option value="45" {{ old('delai_reglement') == '45' ? 'selected' : '' }}>45 jours</option>
                                                <option value="60" {{ old('delai_reglement') == '60' ? 'selected' : '' }}>60 jours</option>
                                                <option value="90" {{ old('delai_reglement') == '90' ? 'selected' : '' }}>90 jours</option>
                                            </select>
                                            @error('delai_reglement')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Délai accordé pour le règlement</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="app-form row">
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="projet_id" class="app-form-label">
                                                <i class="fas fa-project-diagram me-2"></i>Projet
                                            </label>
                                            <select class="app-form-select @error('projet_id') is-invalid @enderror" 
                                                id="projet_id" name="projet_id">
                                                <option value="">-- Sélectionner un projet --</option>
                                                @foreach($projets as $projet)
                                                    <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                                        {{ $projet->nom_projet }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('projet_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Projet auquel rattacher la commande</div>
                                        </div>
                                    </div>
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="lieu_livraison" class="app-form-label">
                                                <i class="fas fa-map-marker-alt me-2"></i>Lieu de livraison
                                            </label>
                                            <input type="text" class="app-form-control @error('lieu_livraison') is-invalid @enderror" 
                                                id="lieu_livraison" name="lieu_livraison" value="{{ old('lieu_livraison') }}" 
                                                placeholder="Adresse de livraison">
                                            @error('lieu_livraison')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Adresse où livrer la commande</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="app-form row">
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="demande_approvisionnement_id" class="app-form-label">
                                                <i class="fas fa-clipboard-list me-2"></i>Demande d'approvisionnement
                                            </label>
                                            <select class="app-form-select @error('demande_approvisionnement_id') is-invalid @enderror" 
                                                id="demande_approvisionnement_id" name="demande_approvisionnement_id">
                                                <option value="">-- Sélectionner une demande --</option>
                                                @foreach($demandesAppro as $demande)
                                                    <option value="{{ $demande->id }}" {{ old('demande_approvisionnement_id') == $demande->id ? 'selected' : '' }}>
                                                        {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('demande_approvisionnement_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Demande d'approvisionnement liée</div>
                                        </div>
                                    </div>
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="demande_achat_id" class="app-form-label">
                                                <i class="fas fa-shopping-cart me-2"></i>Demande d'achat
                                            </label>
                                            <select class="app-form-select @error('demande_achat_id') is-invalid @enderror" 
                                                id="demande_achat_id" name="demande_achat_id">
                                                <option value="">-- Sélectionner une demande --</option>
                                                @foreach($demandesAchat as $demande)
                                                    <option value="{{ $demande->id }}" {{ old('demande_achat_id') == $demande->id ? 'selected' : '' }}>
                                                        {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('demande_achat_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Demande d'achat liée</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="app-form-group">
                                    <label for="notes" class="app-form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Notes
                                    </label>
                                    <textarea class="app-form-control @error('notes') is-invalid @enderror" 
                                        id="notes" name="notes" rows="3" placeholder="Notes et commentaires...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="app-form-text">Informations complémentaires</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Articles -->
                        <div class="app-card mb-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-boxes me-2"></i>Articles à Commander
                                </h3>
                                <div class="app-card-actions">
                                    <button type="button" class="app-btn app-btn-success app-btn-sm" onclick="addArticle()">
                                        <i class="fas fa-plus"></i> Ajouter une ligne
                                    </button>
                                </div>
                            </div>
                            <div class="app-card-body app-table-responsive">
                                <table class="app-table" id="articles_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 8%;">N° Ligne</th>
                                            <th style="width: 12%;">Ref article</th>
                                            <th style="width: 20%;">Article <span class="text-danger">*</span></th>
                                            <th style="width: 10%;">Quantité <span class="text-danger">*</span></th>
                                            <th style="width: 8%;">Unité</th>
                                            <th style="width: 10%;">Prix unitaire <span class="text-danger">*</span></th>
                                            <th style="width: 8%;">% Remise</th>
                                            <th style="width: 10%;">Montant HT</th>
                                            <th style="width: 10%;">Commentaire</th>
                                            <th style="width: 4%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="ligne-numero">1</span>
                                            </td>
                                            <td>
                                                <span class="article-reference">-</span>
                                            </td>
                                            <td>
                                                <select class="app-form-select article-select" name="article_id[]" required onchange="updateArticleInfo(this)">
                                                    <option value="">-- Sélectionner un article --</option>
                                                    @foreach($articles as $article)
                                                        <option value="{{ $article->id }}" data-reference="{{ $article->reference }}" data-unite="{{ $article->uniteMesure->ref ?? 'Unité' }}">
                                                            {{ $article->reference }} - {{ $article->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="app-form-control quantite" name="quantite[]" 
                                                    min="1" value="1" required oninput="calculerLigneTotal(this.closest('tr'))">
                                            </td>
                                            <td>
                                                <span class="article-unite">-</span>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="app-form-control prix_unitaire" 
                                                    name="prix_unitaire[]" min="0" value="0" required oninput="calculerLigneTotal(this.closest('tr'))">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="app-form-control remise" name="remise[]" 
                                                    min="0" max="100" value="0" oninput="calculerLigneTotal(this.closest('tr'))">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="app-form-control ligne_total" name="ligne_total[]" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="app-form-control" name="commentaire[]" placeholder="Commentaire...">
                                            </td>
                                            <td>
                                                <button type="button" class="app-btn app-btn-danger app-btn-sm" onclick="removeLine(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td colspan="7" class="text-end app-fw-bold">
                                                <i class="fas fa-calculator me-2"></i>Montant total HT :
                                            </td>
                                            <td colspan="3" class="app-fw-bold text-success h5" id="montant_total_ht">0.00</td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="7" class="text-end app-fw-bold">
                                                Remise :
                                            </td>
                                            <td colspan="3" class="app-fw-bold text-warning h5" id="remise_totale">0.00</td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="7" class="text-end app-fw-bold">
                                                Montant net HT :
                                            </td>
                                            <td colspan="3" class="app-fw-bold text-info h5" id="montant_net_ht">0.00</td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="7" class="text-end app-fw-bold">
                                                TVA (18%) :
                                            </td>
                                            <td colspan="3" class="app-fw-bold text-secondary h5" id="tva_montant">0.00</td>
                                        </tr>
                                        <tr class="table-active">
                                            <td colspan="7" class="text-end app-fw-bold">
                                                <i class="fas fa-money-bill-wave me-2"></i>Total TTC :
                                            </td>
                                            <td colspan="3" class="app-fw-bold text-success h5" id="total_ttc">0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="app-card-footer">
                            <a href="{{ route('bon-commandes.index') }}" class="app-btn app-btn-secondary">
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

<!-- Template pour nouvelles lignes -->
<template id="article-row-template">
    <tr>
        <td>
            <span class="ligne-numero">1</span>
        </td>
        <td>
            <span class="article-reference">-</span>
        </td>
        <td>
            <select class="app-form-select article-select" name="article_id[]" required onchange="updateArticleInfo(this)">
                <option value="">-- Sélectionner un article --</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}" data-reference="{{ $article->reference }}" data-unite="{{ $article->uniteMesure->ref ?? 'Unité' }}">
                        {{ $article->reference }} - {{ $article->nom }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="app-form-control quantite" name="quantite[]" 
                min="1" value="1" required oninput="calculerLigneTotal(this.closest('tr'))">
        </td>
        <td>
            <span class="article-unite">-</span>
        </td>
        <td>
            <input type="number" step="0.01" class="app-form-control prix_unitaire" 
                name="prix_unitaire[]" min="0" value="0" required oninput="calculerLigneTotal(this.closest('tr'))">
        </td>
        <td>
            <input type="number" step="0.01" class="app-form-control remise" name="remise[]" 
                min="0" max="100" value="0" oninput="calculerLigneTotal(this.closest('tr'))">
        </td>
        <td>
            <input type="text" class="app-form-control ligne_total bg-light" readonly>
        </td>
        <td>
            <input type="text" class="app-form-control" name="commentaire[]" placeholder="Commentaire...">
        </td>
        <td>
            <button type="button" class="app-btn app-btn-danger app-btn-sm" onclick="removeLine(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
// Fonction pour mettre à jour les informations de l'article
function updateArticleInfo(selectElement) {
    const row = selectElement.closest('tr');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (selectedOption.value) {
        const reference = selectedOption.getAttribute('data-reference');
        const unite = selectedOption.getAttribute('data-unite');
        
        row.querySelector('.article-reference').textContent = reference || '-';
        row.querySelector('.article-unite').textContent = unite || '-';
    } else {
        row.querySelector('.article-reference').textContent = '-';
        row.querySelector('.article-unite').textContent = '-';
    }
}

// Fonction pour calculer le total d'une ligne
function calculerLigneTotal(row) {
    const quantiteEl = row.querySelector('.quantite');
    const prixEl = row.querySelector('.prix_unitaire');
    const remiseEl = row.querySelector('.remise');
    const totalEl = row.querySelector('.ligne_total');
    
    // Vérifier que tous les éléments existent
    if (!quantiteEl || !prixEl || !remiseEl || !totalEl) {
        return;
    }
    
    const quantite = parseFloat(quantiteEl.value) || 0;
    const prix = parseFloat(prixEl.value) || 0;
    const remise = parseFloat(remiseEl.value) || 0;
    
    // Calcul: Montant HT = (PU x Qté) - (PU x Qté x % Remise / 100)
    const montantBrut = quantite * prix;
    const montantRemise = montantBrut * (remise / 100);
    const montantHT = montantBrut - montantRemise;
    
    totalEl.value = montantHT.toFixed(2);
    
    // Recalculer le montant total
    calculerMontantTotal();
}

// Fonction pour calculer le montant total
function calculerMontantTotal() {
    let montantTotalHT = 0;
    let remiseTotale = 0;
    
    document.querySelectorAll('#articles_table tbody tr').forEach(function(row) {
        const quantiteEl = row.querySelector('.quantite');
        const prixEl = row.querySelector('.prix_unitaire');
        const remiseEl = row.querySelector('.remise');
        
        // Vérifier que tous les éléments existent
        if (!quantiteEl || !prixEl || !remiseEl) {
            return;
        }
        
        const quantite = parseFloat(quantiteEl.value) || 0;
        const prix = parseFloat(prixEl.value) || 0;
        const remise = parseFloat(remiseEl.value) || 0;
        
        const montantBrut = quantite * prix;
        const montantRemiseLigne = montantBrut * (remise / 100);
        const montantHT = montantBrut - montantRemiseLigne;
        
        montantTotalHT += montantHT;
        remiseTotale += montantRemiseLigne;
    });
    
    const montantNetHT = montantTotalHT;
    const tvaMontant = montantNetHT * 0.18; // TVA 18%
    const totalTTC = montantNetHT + tvaMontant;
    
    // Mise à jour des affichages
    document.getElementById('montant_total_ht').textContent = montantTotalHT.toFixed(2) + ' FCFA';
    document.getElementById('remise_totale').textContent = remiseTotale.toFixed(2) + ' FCFA';
    document.getElementById('montant_net_ht').textContent = montantNetHT.toFixed(2) + ' FCFA';
    document.getElementById('tva_montant').textContent = tvaMontant.toFixed(2) + ' FCFA';
    document.getElementById('total_ttc').textContent = totalTTC.toFixed(2) + ' FCFA';
}

// Fonction pour mettre à jour la numérotation des lignes
function updateLineNumbers() {
    const rows = document.querySelectorAll('#articles_table tbody tr');
    rows.forEach((row, index) => {
        const numeroSpan = row.querySelector('.ligne-numero');
        if (numeroSpan) {
            numeroSpan.textContent = index + 1;
        }
    });
}

// Ajouter une ligne
function addArticle() {
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    
    document.querySelector('#articles_table tbody').appendChild(clone);
    
    // Mettre à jour la numérotation
    updateLineNumbers();
    
    // Calculer le total de la nouvelle ligne
    const newRow = document.querySelector('#articles_table tbody tr:last-child');
    calculerLigneTotal(newRow);
}

// Supprimer une ligne
function removeLine(button) {
    const tbody = document.querySelector('#articles_table tbody');
    const rowCount = tbody.querySelectorAll('tr').length;
    
    if (rowCount > 1) {
        button.closest('tr').remove();
        updateLineNumbers();
        calculerMontantTotal();
    } else {
        alert('Vous devez avoir au moins une ligne d\'article');
    }
}

// Charger les articles d'une demande de cotation terminée
function chargerArticlesDemandeCotation(demandeId) {
    if (!demandeId) return;
    
    fetch(`/demandes-cotation/${demandeId}/articles`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Vider le tableau
                document.querySelector('#articles_table tbody').innerHTML = '';
                
                // Ajouter les articles
                data.forEach((item, index) => {
                    const template = document.getElementById('article-row-template');
                    const clone = template.content.cloneNode(true);
                    
                    // Sélectionner l'article
                    const select = clone.querySelector('.article-select');
                    select.value = item.article_id;
                    
                    // Mettre à jour les informations de l'article
                    const selectedOption = select.options[select.selectedIndex];
                    if (selectedOption) {
                        const reference = selectedOption.getAttribute('data-reference');
                        const unite = selectedOption.getAttribute('data-unite');
                        clone.querySelector('.article-reference').textContent = reference || '-';
                        clone.querySelector('.article-unite').textContent = unite || '-';
                    }
                    
                    // Remplir les champs
                    clone.querySelector('.quantite').value = item.quantite || 1;
                    clone.querySelector('.prix_unitaire').value = item.prix_unitaire || 0;
                    clone.querySelector('.remise').value = item.remise || 0;
                    clone.querySelector('input[name="commentaire[]"]').value = item.commentaire || '';
                    
                    document.querySelector('#articles_table tbody').appendChild(clone);
                });
                
                // Mettre à jour la numérotation et calculer les totaux
                updateLineNumbers();
                document.querySelectorAll('#articles_table tbody tr').forEach(row => {
                    calculerLigneTotal(row);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Charger les articles d'une demande d'approvisionnement
function chargerArticlesDemandeAppro(demandeId) {
    if (!demandeId) return;
    
    fetch(`/demandes-approvisionnement/${demandeId}/articles`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Vider le tableau
                document.querySelector('#articles_table tbody').innerHTML = '';
                
                // Ajouter les articles
                data.forEach(item => {
                    const template = document.getElementById('article-row-template');
                    const clone = template.content.cloneNode(true);
                    
                    // Sélectionner l'article
                    const select = clone.querySelector('.article-select');
                    select.value = item.article_id;
                    
                    // Mettre à jour les informations de l'article
                    updateArticleInfo(select);
                    
                    // Remplir les champs
                    clone.querySelector('.quantite').value = item.quantite_approuvee || item.quantite_demandee;
                    clone.querySelector('.prix_unitaire').value = 0;
                    clone.querySelector('.remise').value = 0;
                    clone.querySelector('input[name="commentaire[]"]').value = item.commentaire || '';
                    
                    document.querySelector('#articles_table tbody').appendChild(clone);
                });
                
                // Mettre à jour la numérotation et calculer les totaux
                updateLineNumbers();
                document.querySelectorAll('#articles_table tbody tr').forEach(row => {
                    calculerLigneTotal(row);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Charger les articles d'une demande d'achat
function chargerArticlesDemandeAchat(demandeId) {
    if (!demandeId) return;
    
    fetch(`/demandes-achat/${demandeId}/articles`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                // Vider le tableau
                document.querySelector('#articles_table tbody').innerHTML = '';
                
                // Ajouter les articles
                data.forEach(item => {
                    const template = document.getElementById('article-row-template');
                    const clone = template.content.cloneNode(true);
                    
                    // Sélectionner l'article
                    const select = clone.querySelector('.article-select');
                    select.value = item.article_id;
                    
                    // Mettre à jour les informations de l'article
                    updateArticleInfo(select);
                    
                    // Remplir les champs
                    clone.querySelector('.quantite').value = item.quantite;
                    clone.querySelector('.prix_unitaire').value = item.prix_estime || 0;
                    clone.querySelector('.remise').value = 0;
                    clone.querySelector('input[name="commentaire[]"]').value = item.commentaire || '';
                    
                    document.querySelector('#articles_table tbody').appendChild(clone);
                });
                
                // Mettre à jour la numérotation et calculer les totaux
                updateLineNumbers();
                document.querySelectorAll('#articles_table tbody tr').forEach(row => {
                    calculerLigneTotal(row);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si une demande de cotation est déjà sélectionnée au chargement
    const demandeCotationSelect = document.getElementById('demande_cotation_id');
    const demandeAchatSelect = document.getElementById('demande_achat_id');
    const demandeApproSelect = document.getElementById('demande_approvisionnement_id');
    
    if (demandeCotationSelect && demandeCotationSelect.value) {
        // Griser les champs si une demande de cotation est déjà sélectionnée
        if (demandeAchatSelect) {
            demandeAchatSelect.disabled = true;
            demandeAchatSelect.style.opacity = '0.6';
        }
        if (demandeApproSelect) {
            demandeApproSelect.disabled = true;
            demandeApproSelect.style.opacity = '0.6';
        }
    }
    
    // Calculer les totaux initiaux seulement s'il y a des lignes
    const existingRows = document.querySelectorAll('#articles_table tbody tr');
    if (existingRows.length > 0) {
        existingRows.forEach(row => {
            calculerLigneTotal(row);
        });
    }
    
    // Événement pour la demande de cotation
    const demandeCotationSelect = document.getElementById('demande_cotation_id');
    if (demandeCotationSelect) {
        demandeCotationSelect.addEventListener('change', function() {
            if (this.value) {
                // Charger la demande d'achat liée automatiquement
                console.log('Chargement de la demande d\'achat pour la cotation ID:', this.value);
                fetch(`/bon-commandes/demande-achat/${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Réponse API:', data);
                        const demandeAchatSelect = document.getElementById('demande_achat_id');
                        const demandeApproSelect = document.getElementById('demande_approvisionnement_id');
                        const fournisseurSelect = document.getElementById('fournisseur_id');
                        const modeReglementSelect = document.getElementById('mode_reglement');
                        const delaiReglementSelect = document.getElementById('delai_reglement');
                        
                        if (data.success && demandeAchatSelect) {
                            console.log('Sélection de la demande d\'achat ID:', data.demande_achat_id);
                            demandeAchatSelect.value = data.demande_achat_id;
                            
                            // Remplir automatiquement la demande d'approvisionnement si elle existe
                            if (data.demande_approvisionnement_id && demandeApproSelect) {
                                console.log('Sélection de la demande d\'approvisionnement ID:', data.demande_approvisionnement_id);
                                demandeApproSelect.value = data.demande_approvisionnement_id;
                            } else if (demandeApproSelect) {
                                demandeApproSelect.value = '';
                            }
                            
                            // Remplir automatiquement le projet si il existe
                            const projetSelect = document.getElementById('projet_id');
                            if (data.projet_id && projetSelect) {
                                console.log('Sélection du projet ID:', data.projet_id);
                                projetSelect.value = data.projet_id;
                            } else if (projetSelect) {
                                projetSelect.value = '';
                            }
                            
                            // Mettre à jour le fournisseur si disponible
                            if (data.fournisseur && fournisseurSelect) {
                                console.log('Sélection du fournisseur ID:', data.fournisseur.id);
                                fournisseurSelect.value = data.fournisseur.id;
                                
                                // Mettre à jour le mode de règlement
                                if (data.fournisseur.mode_paiement && modeReglementSelect) {
                                    console.log('Sélection du mode de règlement:', data.fournisseur.mode_paiement);
                                    modeReglementSelect.value = data.fournisseur.mode_paiement;
                                }
                                
                                // Mettre à jour le délai de règlement
                                if (data.fournisseur.delai_paiement && delaiReglementSelect) {
                                    console.log('Sélection du délai de règlement:', data.fournisseur.delai_paiement);
                                    delaiReglementSelect.value = data.fournisseur.delai_paiement;
                                }
                            }
                            
                            // Griser automatiquement les champs demande d'achat et demande d'approvisionnement
                            if (demandeAchatSelect) {
                                demandeAchatSelect.disabled = true;
                                demandeAchatSelect.style.opacity = '0.6';
                            }
                            if (demandeApproSelect) {
                                demandeApproSelect.disabled = true;
                                demandeApproSelect.style.opacity = '0.6';
                            }
                        } else {
                            console.log('Aucune demande d\'achat liée trouvée ou élément non trouvé');
                            if (demandeAchatSelect) demandeAchatSelect.value = '';
                            if (demandeApproSelect) demandeApproSelect.value = '';
                        }
                        
                        // Réactiver les champs si aucune demande de cotation n'est sélectionnée
                        if (!this.value) {
                            if (demandeAchatSelect) {
                                demandeAchatSelect.disabled = false;
                                demandeAchatSelect.style.opacity = '1';
                            }
                            if (demandeApproSelect) {
                                demandeApproSelect.disabled = false;
                                demandeApproSelect.style.opacity = '1';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement de la demande d\'achat:', error);
                        const demandeAchatSelect = document.getElementById('demande_achat_id');
                        const demandeApproSelect = document.getElementById('demande_approvisionnement_id');
                        const projetSelect = document.getElementById('projet_id');
                        if (demandeAchatSelect) demandeAchatSelect.value = '';
                        if (demandeApproSelect) demandeApproSelect.value = '';
                        if (projetSelect) projetSelect.value = '';
                        
                        // Réactiver les champs en cas d'erreur
                        if (demandeAchatSelect) {
                            demandeAchatSelect.disabled = false;
                            demandeAchatSelect.style.opacity = '1';
                        }
                        if (demandeApproSelect) {
                            demandeApproSelect.disabled = false;
                            demandeApproSelect.style.opacity = '1';
                        }
                    });
                
                // Charger les articles de la demande de cotation
                chargerArticlesDemandeCotation(this.value);
            }
        });
    }
    
    // Événements pour les selects de demandes
    const demandeApproSelect = document.getElementById('demande_approvisionnement_id');
    if (demandeApproSelect) {
        demandeApproSelect.addEventListener('change', function() {
            if (this.value) {
                const demandeCotationSelect = document.getElementById('demande_cotation_id');
                const demandeAchatSelect = document.getElementById('demande_achat_id');
                
                // Si une demande d'approvisionnement est sélectionnée manuellement, dégriser et vider les autres champs
                if (demandeCotationSelect) {
                    demandeCotationSelect.value = '';
                    demandeCotationSelect.disabled = false;
                    demandeCotationSelect.style.opacity = '1';
                }
                if (demandeAchatSelect) {
                    demandeAchatSelect.value = '';
                    demandeAchatSelect.disabled = false;
                    demandeAchatSelect.style.opacity = '1';
                }
                
                chargerArticlesDemandeAppro(this.value);
            }
        });
    }
    
    const demandeAchatSelect = document.getElementById('demande_achat_id');
    if (demandeAchatSelect) {
        demandeAchatSelect.addEventListener('change', function() {
            if (this.value) {
                // Vérifier si cette demande d'achat est liée à une demande de cotation
                const demandeCotationSelect = document.getElementById('demande_cotation_id');
                const demandeApproSelect = document.getElementById('demande_approvisionnement_id');
                
                // Vider seulement la demande d'approvisionnement
                if (demandeApproSelect) demandeApproSelect.value = '';
                
                // Si une demande d'achat est sélectionnée manuellement, dégriser et vider les autres champs
                if (demandeCotationSelect) {
                    demandeCotationSelect.value = '';
                    demandeCotationSelect.disabled = false;
                    demandeCotationSelect.style.opacity = '1';
                }
                if (demandeApproSelect) {
                    demandeApproSelect.disabled = false;
                    demandeApproSelect.style.opacity = '1';
                }
                
                chargerArticlesDemandeAchat(this.value);
            }
        });
    }
});
</script>
@endpush
@endsection