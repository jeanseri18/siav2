
{{-- Page Edit - Modifier un produit du stock --}}
@extends('layouts.app')

@section('title', 'Modifier un produit du stock')
@section('page-title', 'Modifier un produit du stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stock</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-edit me-2"></i>Modifier le produit: {{ $stock->article->nom }}
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('stock.update', $stock->id) }}" method="POST" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-form-group">
                            <label for="article_id" class="app-form-label">
                                <i class="fas fa-box me-2"></i>Article
                            </label>
                            <select class="app-form-select" id="article_id" name="article_id" required>
                                <option value="">-- Sélectionnez un article --</option>
                                @foreach($articles as $article)
                                    <option value="{{ $article->id }}" {{ $article->id == $stock->article_id ? 'selected' : '' }} 
                                        data-unite-id="{{ $article->unite ? $article->unite->id : '' }}" data-unite-nom="{{ $article->unite ? $article->unite->nom : '' }}">
                                        {{ $article->nom }} - {{ $article->reference }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Article actuellement en stock</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="unite_mesure_id" class="app-form-label">
                                <i class="fas fa-ruler me-2"></i>Unité de mesure
                            </label>
                            <select class="app-form-select" id="unite_mesure_id" name="unite_mesure_id" required>
                                <option value="">-- Sélectionnez une unité --</option>
                                @foreach($uniteMesures as $unite)
                                    <option value="{{ $unite->id }}" {{ ($stock->unite_mesure_id == $unite->id) ? 'selected' : '' }}>
                                        {{ $unite->nom }} ({{ $unite->ref }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Unité de mesure pour ce produit</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="quantite" class="app-form-label">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                        </label>
                            <div class="input-group">
                                <input type="number" class="app-form-control" id="quantite" name="quantite" value="{{ $stock->quantite }}" min="0" step="0.01" required>
                                <span class="input-group-text" id="unite-display">
                                    {{ $stock->uniteMesure ? $stock->uniteMesure->nom : ($stock->article->unite ? $stock->article->unite->nom : 'Unité') }}
                                </span>
                            </div>
                            <div class="app-form-text">Quantité actuellement en stock</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="date_modification" class="app-form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Date de modification
                            </label>
                            <input type="date" class="app-form-control" id="date_modification" name="date_modification" value="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="app-form-group">
                            <label for="motif" class="app-form-label">
                                <i class="fas fa-comment-alt me-2"></i>Motif de modification
                            </label>
                            <select class="app-form-select" id="motif" name="motif">
                                <option value="ajustement">Ajustement d'inventaire</option>
                                <option value="retour">Retour de produit</option>
                                <option value="perte">Perte ou détérioration</option>
                                <option value="autre">Autre raison</option>
                            </select>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="commentaire" class="app-form-label">
                                <i class="fas fa-comment-dots me-2"></i>Commentaire
                            </label>
                            <textarea class="app-form-control" id="commentaire" name="commentaire" rows="3" placeholder="Commentaire sur la modification...">{{ $stock->commentaire }}</textarea>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('stock.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Historique des mouvements -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-history me-2"></i>Historique des mouvements
                    </h3>
                </div>
                <div class="app-card-body app-table-responsive">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Exemple d'entrées d'historique -->
                            <tr>
                                <td>{{ date('d/m/Y') }}</td>
                                <td>
                                    <span class="app-badge app-badge-info">
                                        <i class="fas fa-edit me-1"></i> Modification
                                    </span>
                                </td>
                                <td>-</td>
                                <td>{{ auth()->user()->name ?? 'Utilisateur' }}</td>
                            </tr>
                            <tr>
                                <td>{{ date('d/m/Y', strtotime('-3 days')) }}</td>
                                <td>
                                    <span class="app-badge app-badge-success">
                                        <i class="fas fa-plus-circle me-1"></i> Entrée
                                    </span>
                                </td>
                                <td>+{{ $stock->quantite }}</td>
                                <td>{{ auth()->user()->name ?? 'Utilisateur' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Afficher l'unité correspondante à l'article sélectionné
        $('#article_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            const uniteId = selectedOption.data('unite-id');
            const uniteNom = selectedOption.data('unite-nom');
            
            // Mettre à jour l'affichage de l'unité
            $('#unite-display').text(uniteNom || 'Unité');
            
            // Pré-sélectionner l'unité de mesure si elle existe
            if (uniteId) {
                $('#unite_mesure_id').val(uniteId);
            } else {
                $('#unite_mesure_id').val('');
            }
        });
        
        // Mettre à jour l'affichage quand l'unité de mesure change
        $('#unite_mesure_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            const uniteText = selectedOption.text();
            if (uniteText && uniteText !== '-- Sélectionnez une unité --') {
                const uniteNom = uniteText.split(' (')[0]; // Extraire le nom sans la référence
                $('#unite-display').text(uniteNom);
            } else {
                $('#unite-display').text('Unité');
            }
        });
    });
</script>
@endpush
@endsection