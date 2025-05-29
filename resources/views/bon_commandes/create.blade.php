@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Nouveau Bon de Commande</h5>
                        <a href="{{ route('bon-commandes.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('bon-commandes.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_commande">Date de commande <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date_commande') is-invalid @enderror" 
                                        id="date_commande" name="date_commande" value="{{ old('date_commande', date('Y-m-d')) }}" required>
                                    @error('date_commande')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_livraison_prevue">Date de livraison prévue</label>
                                    <input type="date" class="form-control @error('date_livraison_prevue') is-invalid @enderror" 
                                        id="date_livraison_prevue" name="date_livraison_prevue" value="{{ old('date_livraison_prevue') }}">
                                    @error('date_livraison_prevue')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fournisseur_id">Fournisseur <span class="text-danger">*</span></label>
                                    <select class="form-control @error('fournisseur_id') is-invalid @enderror" 
                                        id="fournisseur_id" name="fournisseur_id" required>
                                        <option value="">Sélectionner un fournisseur</option>
                                        @foreach($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                {{ $fournisseur->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fournisseur_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="conditions_paiement">Conditions de paiement</label>
                                    <input type="text" class="form-control @error('conditions_paiement') is-invalid @enderror" 
                                        id="conditions_paiement" name="conditions_paiement" value="{{ old('conditions_paiement') }}">
                                    @error('conditions_paiement')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="demande_approvisionnement_id">Demande d'approvisionnement</label>
                                    <select class="form-control @error('demande_approvisionnement_id') is-invalid @enderror" 
                                        id="demande_approvisionnement_id" name="demande_approvisionnement_id">
                                        <option value="">Sélectionner une demande</option>
                                        @foreach($demandesAppro as $demande)
                                            <option value="{{ $demande->id }}" {{ old('demande_approvisionnement_id') == $demande->id ? 'selected' : '' }}>
                                                {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('demande_approvisionnement_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="demande_achat_id">Demande d'achat</label>
                                    <select class="form-control @error('demande_achat_id') is-invalid @enderror" 
                                        id="demande_achat_id" name="demande_achat_id">
                                        <option value="">Sélectionner une demande</option>
                                        @foreach($demandesAchat as $demande)
                                            <option value="{{ $demande->id }}" {{ old('demande_achat_id') == $demande->id ? 'selected' : '' }}>
                                                {{ $demande->reference }} ({{ $demande->date_demande->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('demande_achat_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <hr>
                        <h5>Articles</h5>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="articles_table">
                                <thead>
                                    <tr>
                                        <th>Article <span class="text-danger">*</span></th>
                                        <th>Quantité <span class="text-danger">*</span></th>
                                        <th>Prix unitaire <span class="text-danger">*</span></th>
                                        <th>Total</th>
                                        <th>Commentaire</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control article-select" name="article_id[]" required>
                                                <option value="">Sélectionner un article</option>
                                                @foreach($articles as $article)
                                                    <option value="{{ $article->id }}">
                                                        {{ $article->code }} - {{ $article->designation }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantite" name="quantite[]" 
                                                min="1" value="1" required oninput="calculerLigneTotal(this.closest('tr'))">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control prix_unitaire" 
                                                name="prix_unitaire[]" min="0" value="0" required oninput="calculerLigneTotal(this.closest('tr'))">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control ligne_total" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="commentaire[]">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeLine(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Montant Total:</strong></td>
                                        <td colspan="3" id="montant_total">0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-info" onclick="addArticle()">
                                <i class="fas fa-plus"></i> Ajouter une ligne
                            </button>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
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
            <select class="form-control article-select" name="article_id[]" required>
                <option value="">Sélectionner un article</option>
                @foreach($articles as $article)
                    <option value="{{ $article->id }}">
                        {{ $article->code }} - {{ $article->designation }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control quantite" name="quantite[]" 
                min="1" value="1" required oninput="calculerLigneTotal(this.closest('tr'))">
        </td>
        <td>
            <input type="number" step="0.01" class="form-control prix_unitaire" 
                name="prix_unitaire[]" min="0" value="0" required oninput="calculerLigneTotal(this.closest('tr'))">
        </td>
        <td>
            <input type="text" class="form-control ligne_total" readonly>
        </td>
        <td>
            <input type="text" class="form-control" name="commentaire[]">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeLine(this)">
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
    document.getElementById('montant_total').textContent = montantTotal.toFixed(2);
}

// Ajouter une ligne
function addArticle() {
    const template = document.getElementById('article-row-template');
    const clone = template.content.cloneNode(true);
    
    document.querySelector('#articles_table tbody').appendChild(clone);
    
    // Initialiser Select2 si disponible
    if (typeof $.fn.select2 !== 'undefined') {
        $('#articles_table tbody tr:last-child .article-select').select2();
    }
    
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
                
                // Initialiser Select2 et calculer totaux
                if (typeof $.fn.select2 !== 'undefined') {
                    $('.article-select').select2();
                }
                
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
                
                // Initialiser Select2 et calculer totaux
                if (typeof $.fn.select2 !== 'undefined') {
                    $('.article-select').select2();
                }
                
                document.querySelectorAll('#articles_table tbody tr').forEach(row => {
                    calculerLigneTotal(row);
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
    
    // Calculer les totaux initiaux
    document.querySelectorAll('#articles_table tbody tr').forEach(row => {
        calculerLigneTotal(row);
    });
    
    // Événements pour les selects de demandes
    document.getElementById('demande_approvisionnement_id').addEventListener('change', function() {
        chargerArticlesDemandeAppro(this.value);
    });
    
    document.getElementById('demande_achat_id').addEventListener('change', function() {
        chargerArticlesDemandeAchat(this.value);
    });
});

// Support jQuery si disponible
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.article-select').select2();
    }
});
</script>
@endpush
@endsection