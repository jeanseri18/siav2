@extends('layouts.app')

@section('title', 'Nouveau Transfert de Stock')
@section('page-title', 'Nouveau Transfert de Stock')

@section('breadcrumb')
<li class="breadcrumb-item">Projets</li>
<li class="breadcrumb-item"><a href="{{ route('transferts.index') }}">Transferts de Stock</a></li>
<li class="breadcrumb-item active">Nouveau Transfert</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-exchange-alt me-2"></i>Effectuer un Transfert de Stock
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('transferts.store') }}" method="POST" class="app-form" id="transfertForm">
                @csrf
                
                @if(session('projet_id'))
                <input type="hidden" name="projet_source" value="{{ session('projet_id') }}">
                @endif
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="projet_source" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Projet Source
                            </label>
                            <select name="projet_source" id="projet_source" class="app-form-select" required @if(session('projet_id')) disabled @endif>
                                <option value="">-- Sélectionner le projet source --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" data-projet-id="{{ $projet->id }}" @if(session('projet_id') == $projet->id) selected @endif>{{ $projet->nom_projet }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Le projet d'où provient le stock</div>
                        </div>
                    </div>

                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="projet_destination" class="app-form-label">
                                <i class="fas fa-bullseye me-2"></i>Projet Destination
                            </label>
                            <select name="projet_destination" id="projet_destination" class="app-form-select" required>
                                <option value="">-- Sélectionner le projet destination --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" data-projet-id="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Le projet où le stock sera transféré</div>
                        </div>
                    </div>
                </div>

                <div class="app-form-group">
                    <label for="date_transfert" class="app-form-label">
                        <i class="fas fa-calendar-alt me-2"></i>Date de Transfert
                    </label>
                    <input type="date" name="date_transfert" id="date_transfert" class="app-form-control" value="{{ date('Y-m-d') }}" required>
                    <div class="app-form-text">Date à laquelle le transfert est effectué</div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>Articles à transférer
                    </h5>
                    <button type="button" class="app-btn app-btn-primary app-btn-sm" id="add-article-btn">
                        <i class="fas fa-plus me-1"></i>Ajouter un article
                    </button>
                </div>

                <div id="articles-container">
                    <div class="article-item mb-3 border rounded p-3" data-index="0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label class="app-form-label">
                                        <i class="fas fa-box me-2"></i>Article
                                    </label>
                                    <select name="items[0][article_id]" class="app-form-select article-select" required>
                                        <option value="">-- Sélectionner un article --</option>
                                        @foreach($articles as $article)
                                        <option value="{{ $article->id }}">{{ $article->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="app-form-group">
                                    <label class="app-form-label">
                                        <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                                    </label>
                                    <input type="number" name="items[0][quantite]" class="app-form-control quantite-input" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="app-btn app-btn-danger app-btn-sm remove-article-btn" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="app-form-actions mt-4">
                    <a href="{{ route('transferts.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Effectuer le Transfert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let articleIndex = 1;
    const articlesContainer = document.getElementById('articles-container');
    const addArticleBtn = document.getElementById('add-article-btn');

    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-article-btn');
        removeButtons.forEach((btn, index) => {
            btn.style.display = removeButtons.length > 1 ? 'block' : 'none';
        });
    }

    function addArticleItem() {
        const articleItem = document.createElement('div');
        articleItem.className = 'article-item mb-3 border rounded p-3';
        articleItem.setAttribute('data-index', articleIndex);
        
        articleItem.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-box me-2"></i>Article
                        </label>
                        <select name="items[${articleIndex}][article_id]" class="app-form-select article-select" required>
                            <option value="">-- Sélectionner un article --</option>
                            @foreach($articles as $article)
                            <option value="{{ $article->id }}">{{ $article->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                        </label>
                        <input type="number" name="items[${articleIndex}][quantite]" class="app-form-control quantite-input" min="1" required>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="app-btn app-btn-danger app-btn-sm remove-article-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        articlesContainer.appendChild(articleItem);
        articleIndex++;
        updateRemoveButtons();
    }

    function removeArticleItem(button) {
        const articleItem = button.closest('.article-item');
        articleItem.remove();
        updateRemoveButtons();
    }

    addArticleBtn.addEventListener('click', addArticleItem);

    articlesContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-article-btn')) {
            removeArticleItem(e.target.closest('.remove-article-btn'));
        }
    });

    // Gestion de la soumission du formulaire
    document.getElementById('transfertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const articles = document.querySelectorAll('.article-item');
        let isValid = true;
        
        articles.forEach((article, index) => {
            const articleSelect = article.querySelector('.article-select');
            const quantiteInput = article.querySelector('.quantite-input');
            
            if (!articleSelect.value || !quantiteInput.value || quantiteInput.value < 1) {
                isValid = false;
                articleSelect.classList.add('is-invalid');
                quantiteInput.classList.add('is-invalid');
            } else {
                articleSelect.classList.remove('is-invalid');
                quantiteInput.classList.remove('is-invalid');
            }
        });
        
        if (isValid) {
            form.submit();
        } else {
            alert('Veuillez remplir tous les champs des articles.');
        }
    });

    // Filtrage des projets (empêcher de sélectionner le même projet)
    const projetSource = document.getElementById('projet_source');
    const projetDestination = document.getElementById('projet_destination');
    
    function filterProjetOptions() {
        const sourceValue = projetSource.value;
        const destinationValue = projetDestination.value;
        
        Array.from(projetSource.options).forEach(option => {
            if (option.value !== '') {
                option.style.display = '';
                option.disabled = false;
            }
        });
        
        Array.from(projetDestination.options).forEach(option => {
            if (option.value !== '') {
                option.style.display = '';
                option.disabled = false;
            }
        });
        
        if (sourceValue) {
            Array.from(projetDestination.options).forEach(option => {
                if (option.value === sourceValue) {
                    option.style.display = 'none';
                    option.disabled = true;
                }
            });
            
            if (destinationValue === sourceValue) {
                projetDestination.value = '';
            }
        }
        
        if (destinationValue) {
            Array.from(projetSource.options).forEach(option => {
                if (option.value === destinationValue) {
                    option.style.display = 'none';
                    option.disabled = true;
                }
            });
            
            if (sourceValue === destinationValue) {
                projetSource.value = '';
            }
        }
    }
    
    if (!projetSource.disabled) {
        projetSource.addEventListener('change', filterProjetOptions);
    }
    projetDestination.addEventListener('change', filterProjetOptions);
});
</script>
@endpush

@endsection