

{{-- Page Create - Ajouter un produit au stock --}}
@extends('layouts.app')

@section('title', 'Ajouter un produit au stock')
@section('page-title', 'Ajouter un produit au stock')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stock</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-plus-circle me-2"></i>Ajouter un produit au stock
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('stock.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <input type="hidden" name="projet_id" value="{{ session('projet_id') }}">
                        
                        <div class="app-form-group">
                            <label for="article_id" class="app-form-label">
                                <i class="fas fa-box me-2"></i>Sélectionner un article
                            </label>
                            <select class="app-form-select" id="article_id" name="article_id" required>
                                <option value="">-- Sélectionnez un article --</option>
                                @foreach($articles as $article)
                                    <option value="{{ $article->id }}" data-unite-id="{{ $article->unite ? $article->unite->id : '' }}" data-unite-nom="{{ $article->unite ? $article->unite->nom : '' }}">
                                        {{ $article->nom }} - {{ $article->reference }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Sélectionnez l'article à ajouter au stock</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="unite_mesure_id" class="app-form-label">
                                <i class="fas fa-ruler me-2"></i>Unité de mesure
                            </label>
                            <select class="app-form-select" id="unite_mesure_id" name="unite_mesure_id" required>
                                <option value="">-- Sélectionnez une unité --</option>
                                @foreach($uniteMesures as $unite)
                                    <option value="{{ $unite->id }}">
                                        {{ $unite->nom }} ({{ $unite->ref }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Sélectionnez l'unité de mesure pour ce produit</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="quantite" class="app-form-label">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                        </label>
                            <div class="input-group">
                                <input type="number" class="app-form-control" id="quantite" name="quantite" min="1" step="0.01" required>
                                <span class="input-group-text" id="unite-display">Unité</span>
                            </div>
                            <div class="app-form-text">Indiquez la quantité à ajouter</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="date_entree" class="app-form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Date d'entrée
                            </label>
                            <input type="date" class="app-form-control" id="date_entree" name="date_entree" value="{{ date('Y-m-d') }}">
                            <div class="app-form-text">Date de l'entrée en stock</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="commentaire" class="app-form-label">
                                <i class="fas fa-comment-alt me-2"></i>Commentaire
                            </label>
                            <textarea class="app-form-control" id="commentaire" name="commentaire" rows="3" placeholder="Commentaire optionnel..."></textarea>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('stock.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Ajouter au stock
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
