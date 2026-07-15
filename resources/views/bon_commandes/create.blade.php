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
    <x-stock-flux-nav module="bon_commande" context="create" />
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
                                                <i class="fas fa-file-invoice me-2"></i>Demande de cotation
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="app-form-select @error('demande_cotation_id') is-invalid @enderror" 
                                                id="demande_cotation_id" name="demande_cotation_id" required>
                                                <option value="">-- Sélectionner une demande de cotation --</option>
                                                @foreach($demandesCotation as $demande)
                                                    <option value="{{ $demande->id }}" {{ (string) old('demande_cotation_id', request('demande_cotation_id')) === (string) $demande->id ? 'selected' : '' }}>
                                                        {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('demande_cotation_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Cotations terminées, validées, ou en cours avec fournisseur retenu (réponse enregistrée)</div>
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
                                                    <option value="{{ $fournisseur->id }}" {{ (string) old('fournisseur_id', request('fournisseur_id')) === (string) $fournisseur->id ? 'selected' : '' }}>
                                                        {{ $fournisseur->nom }}   {{ $fournisseur->prenoms }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('fournisseur_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Rempli automatiquement avec le fournisseur retenu sur la demande de cotation</div>
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
                                            <div class="app-form-text">Rempli depuis la fiche du fournisseur (modifiable si besoin)</div>
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
                                            <div class="app-form-text">Rempli depuis la fiche du fournisseur (modifiable si besoin)</div>
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
                                            <div class="app-form-text">Projet issu de la demande d’achat / d’approvisionnement</div>
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
                                            <div class="app-form-text">Localisation du projet (secteur, quartier, commune…)</div>
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
                                                    <option value="{{ $demande->id }}" {{ (string) old('demande_approvisionnement_id', request('demande_approvisionnement_id')) === (string) $demande->id ? 'selected' : '' }}>
                                                        {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('demande_approvisionnement_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Référence liée à la demande d’achat (préremplie avec la DC)</div>
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
                                            <div class="app-form-text">Référence liée à la demande de cotation (préremplie)</div>
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
                                    <i class="fas fa-boxes me-2"></i>Articles à commander
                                </h3>
                                <div class="app-card-actions">
                                    <button type="button" class="app-btn app-btn-success app-btn-sm" onclick="addArticle()">
                                        <i class="fas fa-plus"></i> Ajouter une ligne
                                    </button>
                                </div>
                            </div>
                            <div class="app-card-body app-table-responsive">
                                <p class="text-muted small mb-3">Lignes issues de la demande de cotation (articles et quantités) ; le prix unitaire reprend le tarif article en catalogue (modifiable).</p>
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
                                                <select class="app-form-select article-select" name="article_id[]" required>
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
                                                    min="1" value="1" required>
                                            </td>
                                            <td>
                                                <span class="article-unite">-</span>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="app-form-control prix_unitaire" 
                                                    name="prix_unitaire[]" min="0" value="0" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="app-form-control remise" name="remise[]" 
                                                    min="0" max="100" value="0">
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
            <select class="app-form-select article-select" name="article_id[]" required>
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
                min="1" value="1" required>
        </td>
        <td>
            <span class="article-unite">-</span>
        </td>
        <td>
            <input type="number" step="0.01" class="app-form-control prix_unitaire" 
                name="prix_unitaire[]" min="0" value="0" required>
        </td>
        <td>
            <input type="number" step="0.01" class="app-form-control remise" name="remise[]" 
                min="0" max="100" value="0">
        </td>
        <td>
            <input type="number" step="0.01" class="app-form-control ligne_total bg-light" name="ligne_total[]" readonly>
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
const FOURNISSEURS_PAIEMENT_BC = @json($fournisseursPaiementMap);

function fixerModeReglementSelect(select, valeurBrute) {
    if (!select || valeurBrute == null || String(valeurBrute).trim() === '') return;
    const v = String(valeurBrute).trim();
    select.value = v;
    if (select.value === v) return;
    const vl = v.toLowerCase();
    for (let i = 0; i < select.options.length; i++) {
        const o = select.options[i];
        if (!o.value) continue;
        if (o.value.toLowerCase() === vl || o.textContent.trim().toLowerCase() === vl) {
            select.selectedIndex = i;
            return;
        }
    }
}

function fixerDelaiReglementSelect(select, valeur) {
    if (!select) return;
    if (valeur == null || valeur === '') {
        select.value = '';
        return;
    }
    const s = String(valeur).trim();
    const m = s.match(/^(\d+)/);
    const num = m ? m[1] : s;
    select.value = num;
    if (select.value === num) return;
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === num) {
            select.selectedIndex = i;
            return;
        }
    }
}

/** Mode / délai depuis la fiche fournisseur (liste déroulante BC + JSON serveur). */
function appliquerPaiementFournisseurParId(fournisseurId, fournisseurApi) {
    const modeSel = document.getElementById('mode_reglement');
    const delaiSel = document.getElementById('delai_reglement');
    if (!fournisseurId || fournisseurId === '') return;
    const entry = typeof FOURNISSEURS_PAIEMENT_BC !== 'undefined' ? FOURNISSEURS_PAIEMENT_BC[fournisseurId] : null;
    if (entry) {
        fixerModeReglementSelect(modeSel, entry.mode_paiement);
        fixerDelaiReglementSelect(delaiSel, entry.delai_reglement);
    }
    if (fournisseurApi) {
        if (modeSel && !modeSel.value && fournisseurApi.mode_paiement) {
            fixerModeReglementSelect(modeSel, fournisseurApi.mode_paiement);
        }
        if (delaiSel && !delaiSel.value) {
            const dr = fournisseurApi.delai_reglement != null && fournisseurApi.delai_reglement !== ''
                ? fournisseurApi.delai_reglement
                : fournisseurApi.delai_paiement;
            fixerDelaiReglementSelect(delaiSel, dr);
        }
    }
}

/** Parse une valeur saisie (espaces, virgule décimale) pour les champs montants. */
function parseNombreBrut(inputEl) {
    if (!inputEl || inputEl.value == null || inputEl.value === '') return 0;
    const s = String(inputEl.value).replace(/\s/g, '').replace(',', '.');
    const n = parseFloat(s);
    return isNaN(n) ? 0 : n;
}

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
    calculerLigneTotal(row);
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
    
    const quantite = parseNombreBrut(quantiteEl);
    const prix = parseNombreBrut(prixEl);
    const remise = parseNombreBrut(remiseEl);
    
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
        
        const quantite = parseNombreBrut(quantiteEl);
        const prix = parseNombreBrut(prixEl);
        const remise = parseNombreBrut(remiseEl);
        
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
    const setFooter = function (id, text) {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    };
    setFooter('montant_total_ht', montantTotalHT.toFixed(2) + ' FCFA');
    setFooter('remise_totale', remiseTotale.toFixed(2) + ' FCFA');
    setFooter('montant_net_ht', montantNetHT.toFixed(2) + ' FCFA');
    setFooter('tva_montant', tvaMontant.toFixed(2) + ' FCFA');
    setFooter('total_ttc', totalTTC.toFixed(2) + ' FCFA');
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

/** Remplit le tableau articles à partir du payload (DC ou API lignes). */
function remplirLignesDepuisPayload(rows) {
    if (!rows || rows.length === 0) return;
    document.querySelector('#articles_table tbody').innerHTML = '';
    rows.forEach(function (item) {
        const template = document.getElementById('article-row-template');
        const clone = template.content.cloneNode(true);
        const select = clone.querySelector('.article-select');
        select.value = item.article_id;
        updateArticleInfo(select);
        clone.querySelector('.quantite').value = item.quantite != null ? item.quantite : 1;
        clone.querySelector('.prix_unitaire').value = item.prix_unitaire != null ? item.prix_unitaire : 0;
        clone.querySelector('.remise').value = item.remise != null ? item.remise : 0;
        const c = clone.querySelector('input[name="commentaire[]"]');
        if (c) c.value = item.commentaire || '';
        document.querySelector('#articles_table tbody').appendChild(clone);
    });
    updateLineNumbers();
    document.querySelectorAll('#articles_table tbody tr').forEach(function (row) {
        calculerLigneTotal(row);
    });
}

function reinitialiserLignesArticlesVides() {
    document.querySelector('#articles_table tbody').innerHTML = '';
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    document.querySelector('#articles_table tbody').appendChild(clone);
    updateLineNumbers();
    calculerMontantTotal();
}

// Charger les articles d'une demande de cotation (endpoint lignes seules — secours)
function chargerArticlesDemandeCotation(demandeId) {
    if (!demandeId) return;
    fetch(`/demandes-cotation/${demandeId}/articles`)
        .then(response => response.json())
        .then(data => {
            if (!data.length) return;
            const rows = data.map(function (item) {
                return {
                    article_id: item.article_id,
                    quantite: item.quantite,
                    prix_unitaire: item.prix_unitaire != null ? item.prix_unitaire : 0,
                    remise: item.remise != null ? item.remise : 0,
                    commentaire: item.commentaire || item.specifications || ''
                };
            });
            remplirLignesDepuisPayload(rows);
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

function appliquerDonneesDemandeCotation(data) {
    if (!data || !data.success) {
        return;
    }

    const demandeAchatSelect = document.getElementById('demande_achat_id');
    const demandeApproSelect = document.getElementById('demande_approvisionnement_id');
    const fournisseurSelect = document.getElementById('fournisseur_id');
    const projetSelect = document.getElementById('projet_id');
    const lieuLivraison = document.getElementById('lieu_livraison');

    const aDa = data.demande_achat_id != null && String(data.demande_achat_id) !== '';

    if (demandeAchatSelect) {
        demandeAchatSelect.value = aDa ? String(data.demande_achat_id) : '';
        demandeAchatSelect.disabled = !!aDa;
        demandeAchatSelect.style.opacity = aDa ? '0.6' : '1';
    }

    if (demandeApproSelect) {
        if (data.demande_approvisionnement_id != null && String(data.demande_approvisionnement_id) !== '') {
            demandeApproSelect.value = String(data.demande_approvisionnement_id);
        } else {
            demandeApproSelect.value = '';
        }
        demandeApproSelect.disabled = !!aDa;
        demandeApproSelect.style.opacity = aDa ? '0.6' : '1';
    }

    if (projetSelect) {
        if (data.projet_id != null && String(data.projet_id) !== '') {
            projetSelect.value = String(data.projet_id);
        } else {
            projetSelect.value = '';
        }
    }

    if (lieuLivraison) {
        lieuLivraison.value = data.lieu_livraison != null ? String(data.lieu_livraison) : '';
    }

    const fid = data.fournisseur_id != null ? data.fournisseur_id : (data.fournisseur && data.fournisseur.id);
    if (fid != null && fournisseurSelect) {
        fournisseurSelect.value = String(fid);
    }
    appliquerPaiementFournisseurParId(fid != null ? String(fid) : '', data.fournisseur || null);

    if (data.lignes_articles && data.lignes_articles.length) {
        remplirLignesDepuisPayload(data.lignes_articles);
    }
}

function chargerInfosDemandeCotation(cotationId) {
    if (!cotationId) return;
    fetch(`/bon-commandes/demande-achat/${cotationId}`)
        .then(response => response.json())
        .then(data => {
            appliquerDonneesDemandeCotation(data);
        })
        .catch(error => {
            console.error('Erreur lors du chargement de la demande de cotation:', error);
        });
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const articlesTable = document.getElementById('articles_table');
    if (articlesTable) {
        articlesTable.addEventListener('input', function (e) {
            const t = e.target;
            if (!t.closest || !t.closest('tbody')) return;
            if (t.classList.contains('quantite') || t.classList.contains('prix_unitaire') || t.classList.contains('remise')) {
                const row = t.closest('tr');
                if (row) calculerLigneTotal(row);
            }
        });
        articlesTable.addEventListener('change', function (e) {
            const t = e.target;
            if (!t.closest || !t.closest('tbody')) return;
            if (t.classList.contains('article-select')) {
                updateArticleInfo(t);
            }
        });
    }

    const demandeCotationSelect = document.getElementById('demande_cotation_id');
    const demandeAchatSelect = document.getElementById('demande_achat_id');
    const demandeApproSelect = document.getElementById('demande_approvisionnement_id');
    const fournisseurSelectInit = document.getElementById('fournisseur_id');
    if (fournisseurSelectInit) {
        fournisseurSelectInit.addEventListener('change', function () {
            if (this.value) {
                appliquerPaiementFournisseurParId(this.value, null);
            }
        });
    }

    if (demandeCotationSelect && demandeCotationSelect.value) {
        chargerInfosDemandeCotation(demandeCotationSelect.value);
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
    
    if (demandeCotationSelect) {
        demandeCotationSelect.addEventListener('change', function() {
            if (this.value) {
                chargerInfosDemandeCotation(this.value);
            } else {
                const da = document.getElementById('demande_achat_id');
                const dap = document.getElementById('demande_approvisionnement_id');
                const pr = document.getElementById('projet_id');
                const fr = document.getElementById('fournisseur_id');
                const lieu = document.getElementById('lieu_livraison');
                const mode = document.getElementById('mode_reglement');
                const delai = document.getElementById('delai_reglement');
                if (da) { da.value = ''; da.disabled = false; da.style.opacity = '1'; }
                if (dap) { dap.value = ''; dap.disabled = false; dap.style.opacity = '1'; }
                if (pr) pr.value = '';
                if (fr) fr.value = '';
                if (lieu) lieu.value = '';
                if (mode) mode.value = '';
                if (delai) delai.value = '';
                reinitialiserLignesArticlesVides();
            }
        });
    }
    
    // Événements pour les selects de demandes
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