{{-- Page Show - Détails d'un article en stock contrat --}}
@extends('layouts.app')

@section('title', 'Détails de l\'article en stock')
@section('page-title', 'Détails de l\'article en stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item"><a href="{{ route('stock_contrat.index') }}">Stock</a></li>
<li class="breadcrumb-item active">Détails</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-container">
    <div class="app-card">
        <div class="app-card-header">
            <h3 class="app-card-title">
                <i class="fas fa-box me-2"></i>{{ $stock->article->designation_article }}
            </h3>
            <div class="app-card-actions">
                <a href="{{ route('stock_contrat.edit', $stock->id) }}" class="app-btn app-btn-warning">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <a href="{{ route('stock_contrat.index') }}" class="app-btn app-btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
        
        <div class="app-card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="app-info-group">
                        <h5 class="app-info-title">
                            <i class="fas fa-info-circle me-2"></i>Informations de l'article
                        </h5>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Référence :</label>
                            <span class="app-info-value">{{ $stock->article->ref_article }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Désignation :</label>
                            <span class="app-info-value">{{ $stock->article->designation_article }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Catégorie :</label>
                            <span class="app-info-value">{{ $stock->article->categorie->nom_categorie ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Sous-catégorie :</label>
                            <span class="app-info-value">{{ $stock->article->sousCategorie->nom_sous_categorie ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Fournisseur :</label>
                            <span class="app-info-value">{{ $stock->article->fournisseur->nom ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Référence fournisseur :</label>
                            <span class="app-info-value">{{ $stock->article->ref_fournisseur ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Type :</label>
                            <span class="app-info-value">{{ $stock->article->type }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="app-info-group">
                        <h5 class="app-info-title">
                            <i class="fas fa-warehouse me-2"></i>Informations du stock
                        </h5>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Contrat :</label>
                            <span class="app-info-value">{{ $stock->projet->nom_projet ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Quantité disponible :</label>
                            <span class="app-info-value app-badge app-badge-success">
                                {{ number_format($stock->quantite, 2, ',', ' ') }}
                            </span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Unité de mesure :</label>
                            <span class="app-info-value">{{ $stock->uniteMesure->nom_unite ?? $stock->article->unite->nom_unite ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Coût moyen pondéré :</label>
                            <span class="app-info-value">
                                {{ number_format($stock->article->cout_moyen_pondere ?? 0, 2, ',', ' ') }} €
                            </span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Valeur totale :</label>
                            <span class="app-info-value app-badge app-badge-info">
                                {{ number_format(($stock->article->cout_moyen_pondere ?? 0) * $stock->quantite, 2, ',', ' ') }} €
                            </span>
                        </div>
                        
                        <div class="app-info-item">
                            <label class="app-info-label">Dernière mise à jour :</label>
                            <span class="app-info-value">{{ $stock->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($stock->article->description)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="app-info-group">
                        <h5 class="app-info-title">
                            <i class="fas fa-file-text me-2"></i>Description
                        </h5>
                        <p class="app-info-description">{{ $stock->article->description }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="app-card-footer">
            <div class="app-card-actions">
                <button type="button" class="app-btn app-btn-primary" data-bs-toggle="modal" data-bs-target="#transfertModal" 
                        onclick="populateTransfertModal({{ $stock->id }}, '{{ $stock->article->designation_article }}', {{ $stock->quantite }})">
                    <i class="fas fa-exchange-alt me-2"></i>Transférer
                </button>
                
                <a href="{{ route('stock_contrat.edit', $stock->id) }}" class="app-btn app-btn-warning">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                
                <form action="{{ route('stock_contrat.destroy', $stock->id) }}" method="POST" class="d-inline" 
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article du stock ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="app-btn app-btn-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal de transfert --}}
<div class="modal fade" id="transfertModal" tabindex="-1" aria-labelledby="transfertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content app-modal">
            <div class="app-modal-header">
                <h5 class="app-modal-title" id="transfertModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Transférer du Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="app-modal-body">
                <form action="{{ route('transferts.store') }}" method="POST" class="app-form" id="transfertForm">
                    @csrf
                    <input type="hidden" name="article_id" id="transfertArticleId">
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-building me-2"></i>Contrat Destination</label>
                                <select name="id_projet_destination" class="app-form-select" required>
                                    <option value="">Sélectionner le contrat destination</option>
                                    @foreach(\App\Models\Contrat::all() as $contrat)
                                    <option value="{{ $contrat->id }}">{{ $contrat->nom_contrat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-box me-2"></i>Article</label>
                                <input type="text" id="transfertArticleNom" class="app-form-input" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-sort-numeric-up me-2"></i>Quantité à transférer</label>
                                <input type="number" name="quantite" id="transfertQuantite" class="app-form-input" min="1" required>
                                <small class="app-form-help">Quantité disponible: <span id="quantiteDisponible"></span></small>
                            </div>
                        </div>
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label"><i class="fas fa-calendar me-2"></i>Date de transfert</label>
                                <input type="date" name="date_transfert" class="app-form-input" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label class="app-form-label"><i class="fas fa-comment me-2"></i>Commentaires</label>
                        <textarea name="commentaires" class="app-form-textarea" rows="3" placeholder="Commentaires optionnels..."></textarea>
                    </div>
                    
                    <div class="app-modal-actions">
                        <button type="button" class="app-btn app-btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="app-btn app-btn-primary">
                            <i class="fas fa-exchange-alt me-2"></i>Transférer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function populateTransfertModal(articleId, articleNom, quantiteDisponible) {
    document.getElementById('transfertArticleId').value = articleId;
    document.getElementById('transfertArticleNom').value = articleNom;
    document.getElementById('quantiteDisponible').textContent = quantiteDisponible;
    document.getElementById('transfertQuantite').max = quantiteDisponible;
}
</script>
@endsection