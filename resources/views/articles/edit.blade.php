@extends('layouts.app')

@section('title', 'Modifier un Article')
@section('page-title', 'Modifier un Article')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_article') }}">Articles</a></li>
<li class="breadcrumb-item active">Modifier</li>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorieSelect = document.getElementById('categorie_id');
    const sousCategorieSelect = document.getElementById('sous_categorie_id');
    const originalSousCategories = Array.from(sousCategorieSelect.options);
    const selectedSousCategorie = "{{ $article->sous_categorie_id }}";
    
    function filterSousCategories() {
        const selectedCategorie = categorieSelect.value;
        
        // Réinitialiser les options
        sousCategorieSelect.innerHTML = '<option value="">Aucune</option>';
        
        if (selectedCategorie) {
            // Filtrer et ajouter les sous-catégories correspondantes
            originalSousCategories.forEach(option => {
                if (option.value === '' || option.dataset.categorie === selectedCategorie) {
                    sousCategorieSelect.appendChild(option.cloneNode(true));
                }
            });
            
            // Restaurer la sélection si elle existe toujours
            if (selectedSousCategorie) {
                const option = sousCategorieSelect.querySelector(`option[value="${selectedSousCategorie}"]`);
                if (option) {
                    sousCategorieSelect.value = selectedSousCategorie;
                }
            }
        }
    }
    
    // Filtrer au chargement de la page
    filterSousCategories();
    
    // Filtrer quand la catégorie change
    categorieSelect.addEventListener('change', filterSousCategories);
});
</script>
@endpush

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorieSelect = document.getElementById('categorie_id');
    const sousCategorieSelect = document.getElementById('sous_categorie_id');
    const originalSousCategories = Array.from(sousCategorieSelect.options);
    
    function filterSousCategories() {
        const selectedCategorie = categorieSelect.value;
        const selectedSousCategorie = sousCategorieSelect.value;
        
        // Réinitialiser les options
        sousCategorieSelect.innerHTML = '<option value="">Aucune</option>';
        
        if (selectedCategorie) {
            // Filtrer et ajouter les sous-catégories correspondantes
            originalSousCategories.forEach(option => {
                if (option.value === '' || option.dataset.categorie === selectedCategorie) {
                    const newOption = option.cloneNode(true);
                    // Garder la sélection si elle correspond à la catégorie actuelle
                    if (option.value === selectedSousCategorie) {
                        newOption.selected = true;
                    }
                    sousCategorieSelect.appendChild(newOption);
                }
            });
        }
    }
    
    // Filtrer au chargement de la page
    filterSousCategories();
    
    // Filtrer quand la catégorie change
    categorieSelect.addEventListener('change', filterSousCategories);
});
</script>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">

        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier l'Article: {{ $article->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">

         @if ($errors->any())
            <div class="app-alert app-alert-danger">
                <div class="app-alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="app-alert-content">
                    <div class="app-alert-text">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            <form action="{{ route('articles.update', $article) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="categorie_id" class="app-form-label">
                                <i class="fas fa-layer-group me-2"></i>Catégorie
                            </label>
                            <select name="categorie_id" id="categorie_id" class="app-form-select" required>
                                @foreach ($categories as $categorie)
                                    <option value="{{ $categorie->id }}" {{ $article->categorie_id == $categorie->id ? 'selected' : '' }}>
                                        {{ $categorie->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Catégorie principale de l'article</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="sous_categorie_id" class="app-form-label">
                                <i class="fas fa-sitemap me-2"></i>Sous-catégorie
                            </label>
                            <select name="sous_categorie_id" id="sous_categorie_id" class="app-form-select">
                                <option value="">Aucune</option>
                                @foreach ($sousCategories as $sousCategorie)
                                    <option value="{{ $sousCategorie->id }}" data-categorie="{{ $sousCategorie->categorie_id }}" {{ $article->sous_categorie_id == $sousCategorie->id ? 'selected' : '' }}>
                                        {{ $sousCategorie->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Sous-catégorie optionnelle</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="reference" class="app-form-label">
                                <i class="fas fa-hashtag me-2"></i>Référence
                            </label>
                            <input type="text" name="reference" id="reference" class="app-form-control" value="{{ old('reference', $article->reference) }}" readonly>
                            <div class="app-form-text">Référence unique de l'article (non modifiable)</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-font me-2"></i>Désignation
                            </label>
                            <input type="text" name="nom" id="nom" class="app-form-control" value="{{ old('nom', $article->nom) }}" required>
                            <div class="app-form-text">Nom complet ou description de l'article</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="unite_mesure" class="app-form-label">
                                <i class="fas fa-ruler me-2"></i>Unité de mesure
                            </label>
                            <select name="unite_mesure" id="unite_mesure" class="app-form-select">
                                <option value="">Sélectionner une unité</option>
                                @foreach ($uniteMesures as $uniteMesure)
                                    <option value="{{ $uniteMesure->id }}" {{ $article->unite_mesure == $uniteMesure->id ? 'selected' : '' }}>
                                        {{ $uniteMesure->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Unité de mesure de l'article</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                    <div class="app-form-group">
                        <label for="reference_fournisseur" class="app-form-label">
                            <i class="fas fa-building me-2"></i>Fournisseur
                        </label>
                        <select name="reference_fournisseur" id="reference_fournisseur" class="app-form-select">
                            <option value="">-- Sélectionner un fournisseur --</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" 
                                    {{ (old('reference_fournisseur', $article->reference_fournisseur) == $fournisseur->id) ? 'selected' : '' }}>
                                    {{ $fournisseur->nom_raison_sociale }}                                     {{ $fournisseur->prenoms }}
                                </option>
                            @endforeach
                        </select>
                        <div class="app-form-text">Fournisseur de l'article (optionnel)</div>
                    </div>
                </div>
                
                <div class="app-form-col">
                    <div class="app-form-group">
                        <label for="type" class="app-form-label">
                            <i class="fas fa-tag me-2"></i>Type
                        </label>
                        <select name="type" id="type" class="app-form-select">
                            <option value="">-- Sélectionner un type --</option>
                            <option value="Matériau" {{ old('type', $article->type) == 'Matériau' ? 'selected' : '' }}>Matériau</option>
                            <option value="Outil" {{ old('type', $article->type) == 'Outil' ? 'selected' : '' }}>Outil</option>
                            <option value="Matériel" {{ old('type', $article->type) == 'Matériel' ? 'selected' : '' }}>Matériel</option>
                        </select>
                        <div class="app-form-text">Type d'article (optionnel)</div>
                    </div>
                </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="quantite_stock" class="app-form-label">
                                <i class="fas fa-warehouse me-2"></i>Quantité Stock
                            </label>
                            <input type="number" name="quantite_stock" id="quantite_stock" class="app-form-control" value="{{ old('quantite_stock', $article->quantite_stock) }}" readonly>
                            <div class="app-form-text">Quantité actuellement en stock (non modifiable directement)</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="prix_unitaire" class="app-form-label">
                                <i class="fas fa-tags me-2"></i>Prix Unitaire
                            </label>
                            <div class="input-group">
                                <input type="number" name="prix_unitaire" id="prix_unitaire" class="app-form-control" value="{{ old('prix_unitaire', $article->prix_unitaire) }}" readonly>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="app-form-text">Prix unitaire de l'article (non modifiable directement)</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('articles.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-warning">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection