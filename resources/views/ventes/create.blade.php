{{-- Page Create - Nouvelle Vente --}}
@extends('layouts.app')

@section('title', 'Nouvelle Vente')
@section('page-title', 'Nouvelle Vente')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('ventes.index') }}">Ventes</a></li>
<li class="breadcrumb-item active">Nouvelle Vente</li>
@endsection

@section('content')

<div class=" app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-shopping-cart me-2"></i>Nouvelle Vente
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('ventes.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <!-- Sélection du client -->
                        <div class="app-form-group">
                            <label for="client_id" class="app-form-label">
                                <i class="fas fa-user me-2"></i>Client
                            </label>
                            <select name="client_id" id="client_id" class="app-form-select" required>
                                <option value="">-- Sélectionner un client --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->prenoms }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Choisissez le client pour cette vente</div>
                        </div>

                        <!-- Section Articles -->
                        <div class="app-card mt-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-boxes me-2"></i>Articles
                                </h3>
                                <div class="app-card-actions">
                                    <button type="button" id="add-article-btn" class="app-btn app-btn-success app-btn-sm">
                                        <i class="fas fa-plus me-2"></i>Ajouter un article
                                    </button>
                                </div>
                            </div>
                            
                            <div class="app-card-body">
                                <!-- Zone dynamique pour les articles -->
                                <div id="articles-container" class="app-gap-3">
                                    <div class="text-center text-muted py-4" id="no-articles-message">
                                        <i class="fas fa-box-open fa-3x mb-3"></i>
                                        <p>Aucun article ajouté. Cliquez sur "Ajouter un article" pour commencer.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Affichage du total -->
                        <div class="app-card mt-4">
                            <div class="app-card-body">
                                <div class="app-d-flex app-justify-content-between app-align-items-center">
                                    <h4 class="mb-0">Total de la vente :</h4>
                                    <h3 class="mb-0 text-primary">
                                        <i class="fas fa-coins me-2"></i>
                                        <span id="total-price">0</span> FCFA
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <div class="app-card-footer">
                            <a href="{{ route('ventes.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-check me-2"></i>Valider la vente
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
    let articles = @json($articles);
    let articlesContainer = document.getElementById("articles-container");
    let noArticlesMessage = document.getElementById("no-articles-message");
    let articleIndex = 0;

    document.getElementById("add-article-btn").addEventListener("click", function () {
        // Masquer le message "aucun article"
        noArticlesMessage.style.display = 'none';
        
        let div = document.createElement("div");
        div.classList.add("article-item", "app-card", "mb-3");

        div.innerHTML = `
            <div class="app-card-body">
                <div class="app-form row">
                    <div class="app-form  col-md-6">
                        <div class="app-form-group">
                            <label class="app-form-label">
                                <i class="fas fa-box me-2"></i>Article
                            </label>
                            <select name="articles[${articleIndex}][id]" class="app-form-select article-select" required>
                                <option value="">-- Sélectionner un article --</option>
                                ${articles.map(article => `<option value="${article.id}" data-price="${article.prix_unitaire}">${article.nom} - ${article.prix_unitaire} FCFA</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="app-form- col-3">
                        <div class="app-form-group">
                            <label class="app-form-label">
                                <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                            </label>
                            <input type="number" name="articles[${articleIndex}][quantite]" class="app-form-control quantity-input" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="app-form- col-2">
                        <div class="app-form-group">
                            <label class="app-form-label">Sous-total</label>
                            <div class="app-form-control article-price bg-light text-end fw-bold">0 FCFA</div>
                        </div>
                    </div>
                    <div class="app-form- col-1">
                        <div class="app-form-group">
                            <label class="app-form-label">&nbsp;</label>
                            <button type="button" class="app-btn app-btn-danger app-btn-sm remove-article w-100">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        articlesContainer.appendChild(div);
        articleIndex++;

        updateTotal();

        // Ajouter l'événement de suppression
        div.querySelector(".remove-article").addEventListener("click", function () {
            div.remove();
            updateTotal();
            
            // Afficher le message si aucun article
            if (articlesContainer.children.length === 1) {
                noArticlesMessage.style.display = 'block';
            }
        });

        // Mise à jour du sous-total affiché
        let select = div.querySelector(".article-select");
        let quantityInput = div.querySelector(".quantity-input");
        let priceSpan = div.querySelector(".article-price");

        function updateSubtotal() {
            let selectedOption = select.options[select.selectedIndex];
            let price = parseFloat(selectedOption.dataset.price || 0);
            let quantity = parseInt(quantityInput.value) || 1;
            let subtotal = price * quantity;
            priceSpan.textContent = subtotal.toLocaleString() + " FCFA";
            updateTotal();
        }

        select.addEventListener("change", updateSubtotal);
        quantityInput.addEventListener("input", updateSubtotal);
    });

    function updateTotal() {
        let total = 0;

        document.querySelectorAll(".article-item").forEach(function (item) {
            let select = item.querySelector(".article-select");
            let quantityInput = item.querySelector(".quantity-input");

            let selectedOption = select.options[select.selectedIndex];
            let price = parseFloat(selectedOption.dataset.price || 0);
            let quantity = parseInt(quantityInput.value) || 1;

            total += price * quantity;
        });

        document.getElementById("total-price").textContent = total.toLocaleString();
    }
</script>
@endpush
@endsection