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
                                            <label for="fournisseur_id" class="app-form-label">
                                                <i class="fas fa-building me-2"></i>Fournisseur
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="app-form-select @error('fournisseur_id') is-invalid @enderror" 
                                                id="fournisseur_id" name="fournisseur_id" required>
                                                <option value="">-- Sélectionner un fournisseur --</option>
                                                @foreach($fournisseurs as $fournisseur)
                                                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                        {{ $fournisseur->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('fournisseur_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Fournisseur auprès duquel commander</div>
                                        </div>
                                    </div>
                                    <div class="app-form col-6">
                                        <div class="app-form-group">
                                            <label for="conditions_paiement" class="app-form-label">
                                                <i class="fas fa-credit-card me-2"></i>Conditions de paiement
                                            </label>
                                            <input type="text" class="app-form-control @error('conditions_paiement') is-invalid @enderror" 
                                                id="conditions_paiement" name="conditions_paiement" value="{{ old('conditions_paiement') }}" 
                                                placeholder="Ex: 30 jours net">
                                            @error('conditions_paiement')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Modalités de paiement convenues</div>
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
                                            <th style="width: 30%;">Article <span class="text-danger">*</span></th>
                                            <th style="width: 15%;">Quantité <span class="text-danger">*</span></th>
                                            <th style="width: 15%;">Prix unitaire <span class="text-danger">*</span></th>
                                            <th style="width: 15%;">Total</th>
                                            <th style="width: 20%;">Commentaire</th>
                                            <th style="width: 5%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="app-form-select article-select" name="article_id[]" required>
                                                    <option value="">-- Sélectionner un article --</option>
                                                    @foreach($articles as $article)
                                                        <option value="{{ $article->id }}">
                                                            {{ $article->code }} - {{ $article->designation }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="app-form-control quantite" name="quantite[]" 
                                                    min="1" value="1" required oninput="calculerLigneTotal(this.closest('tr'))">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="app-form-control prix_unitaire" 
                                                    name="prix_unitaire[]" min="0" value="0" required oninput="calculerLigneTotal(this.closest('tr'))">
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
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td colspan="3" class="text-end app-fw-bold">
                                                <i class="fas fa-calculator me-2"></i>Montant Total :
                                            </td>
                                            <td colspan="3" class="app-fw-bold text-success h5" id="montant_total">0.00</td>
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
            <select class="app-form-select article-select" name="article_id[]" required>
                <option value="">-- Sélectionner un article --</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}">
                        {{ $article->code }} - {{ $article->designation }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="app-form-control quantite" name="quantite[]" 
                min="1" value="1" required oninput="calculerLigneTotal(this.closest('tr'))">
        </td>
        <td>
            <input type="number" step="0.01" class="app-form-control prix_unitaire" 
                name="prix_unitaire[]" min="0" value="0" required oninput="calculerLigneTotal(this.closest('tr'))">
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
// Fonction pour calculer le total d'une ligne
function calculerLigneTotal(row) {
    const quantite = parseFloat(row.querySelector('.quantite').value) || 0;
    const prix = parseFloat(row.querySelector('.prix_unitaire').value) || 0;
    const total = quantite * prix;
    row.querySelector('.ligne_total').value = total.toFixed(2);
    
    // Recalculer le montant total
    calculerMontantTotal();
}

// Fonction pour calculer le montant total
function calculerMontantTotal() {
    let montantTotal = 0;
    document.querySelectorAll('.ligne_total').forEach(function(element) {
        montantTotal += parseFloat(element.value) || 0;
    });
    document.getElementById('montant_total').textContent = montantTotal.toFixed(2) + ' FCFA';
}

// Ajouter une ligne
function addArticle() {
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    
    document.querySelector('#articles_table tbody').appendChild(clone);
    
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
        calculerMontantTotal();
    } else {
        alert('Vous devez avoir au moins une ligne d\'article');
    }
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
                    
                    // Remplir les champs
                    clone.querySelector('.quantite').value = item.quantite_approuvee || item.quantite_demandee;
                    clone.querySelector('.prix_unitaire').value = 0;
                    clone.querySelector('input[name="commentaire[]"]').value = item.commentaire || '';
                    
                    document.querySelector('#articles_table tbody').appendChild(clone);
                });
                
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
                    
                    // Remplir les champs
                    clone.querySelector('.quantite').value = item.quantite;
                    clone.querySelector('.prix_unitaire').value = item.prix_estime || 0;
                    clone.querySelector('input[name="commentaire[]"]').value = item.commentaire || '';
                    
                    document.querySelector('#articles_table tbody').appendChild(clone);
                });
                
                document.querySelectorAll('#articles_table tbody tr').forEach(row => {
                    calculerLigneTotal(row);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Calculer les totaux initiaux
    document.querySelectorAll('#articles_table tbody tr').forEach(row => {
        calculerLigneTotal(row);
    });
    
    // Événements pour les selects de demandes
    document.getElementById('demande_approvisionnement_id').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('demande_achat_id').value = '';
            chargerArticlesDemandeAppro(this.value);
        }
    });
    
    document.getElementById('demande_achat_id').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('demande_approvisionnement_id').value = '';
            chargerArticlesDemandeAchat(this.value);
        }
    });
});
</script>
@endpush
@endsection