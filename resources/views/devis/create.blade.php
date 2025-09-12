@extends('layouts.app')

@section('content')
<div class="app-content pt-3 p-md-3 p-lg-4">
    <div class="container-xl">
        <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
                <h1 class="app-page-title mb-0">Nouveau Devis</h1>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="app-card app-card-settings shadow-sm p-4">
            <div class="app-card-body">
                <form action="{{ route('devis.store') }}" method="POST" id="devisForm" class="app-form">
                    @csrf
                    
                    <!-- Sélection du client -->
                    <div class="app-form-group">
                        <label for="client_id" class="app-form-label">
                            <i class="fas fa-user me-2"></i>Client *
                        </label>
                        <select name="client_id" id="client_id" class="app-form-select @error('client_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom ?? $client->nom_raison_sociale }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informations client -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label for="numero_client" class="app-form-label">Numéro Client *</label>
                                <input type="text" name="numero_client" id="numero_client" class="app-form-control @error('numero_client') is-invalid @enderror" value="{{ old('numero_client') }}" required>
                                @error('numero_client')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="app-form-group">
                                <label for="nom_client" class="app-form-label">Nom Client *</label>
                                <input type="text" name="nom_client" id="nom_client" class="app-form-control @error('nom_client') is-invalid @enderror" value="{{ old('nom_client') }}" required>
                                @error('nom_client')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="app-d-flex app-justify-content-between app-align-items-center">
                                        <h5 class="mb-0">Total HT :</h5>
                                        <h4 class="mb-0 text-info">
                                            <span id="total-ht">0</span> FCFA
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="app-d-flex app-justify-content-between app-align-items-center">
                                        <h5 class="mb-0">TVA (18%) :</h5>
                                        <h4 class="mb-0 text-warning">
                                            <span id="total-tva">0</span> FCFA
                                        </h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="app-d-flex app-justify-content-between app-align-items-center">
                                        <h5 class="mb-0">Total TTC :</h5>
                                        <h4 class="mb-0 text-success">
                                            <i class="fas fa-coins me-2"></i>
                                            <span id="total-ttc">0</span> FCFA
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="app-card-footer">
                        <a href="{{ route('devis.index') }}" class="app-btn app-btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="app-btn app-btn-primary">
                            <i class="fas fa-check me-2"></i>Créer le devis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let articles = @json($articles);
    let clients = @json($clients);
    let articlesContainer = document.getElementById("articles-container");
    let noArticlesMessage = document.getElementById("no-articles-message");
    let articleIndex = 0;

    // Gestion de la sélection du client pour remplir automatiquement les champs
    document.getElementById('client_id').addEventListener('change', function() {
        const clientId = this.value;
        const numeroClientInput = document.getElementById('numero_client');
        const nomClientInput = document.getElementById('nom_client');
        
        if (clientId) {
            const selectedClient = clients.find(client => client.id == clientId);
            if (selectedClient) {
                numeroClientInput.value = selectedClient.numero_client || selectedClient.id;
                nomClientInput.value = selectedClient.nom || selectedClient.nom_raison_sociale || '';
            }
        } else {
            numeroClientInput.value = '';
            nomClientInput.value = '';
        }
    });

    document.getElementById("add-article-btn").addEventListener("click", function () {
        // Masquer le message "aucun article"
        noArticlesMessage.style.display = 'none';
        
        let div = document.createElement("div");
        div.classList.add("article-item", "app-card", "mb-3");

        div.innerHTML = `
            <div class="app-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="app-form-group">
                            <label class="app-form-label">
                                <i class="fas fa-box me-2"></i>Article
                            </label>
                            <select name="articles[${articleIndex}][id]" class="app-form-select article-select" required>
                                <option value="">-- Sélectionner un article --</option>
                                ${articles.map(article => `<option value="${article.id}" data-price="${article.cout_moyen_pondere || article.prix_unitaire}" data-unite="${article.unite_mesure || 'Unité'}">${article.nom}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="app-form-group">
                            <label class="app-form-label">Unité</label>
                            <div class="app-form-control bg-light article-unite">-</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="app-form-group">
                            <label class="app-form-label">
                                <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                            </label>
                            <input type="number" name="articles[${articleIndex}][quantite]" class="app-form-control quantity-input" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="app-form-group">
                            <label class="app-form-label">
                                <i class="fas fa-euro-sign me-2"></i>Prix Unitaire HT
                            </label>
                            <input type="number" name="articles[${articleIndex}][prix_unitaire]" class="app-form-control price-input" min="0" step="0.01" placeholder="Prix HT" required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="app-form-group">
                            <label class="app-form-label">Montant Total</label>
                            <div class="app-form-control bg-light article-price">0 FCFA</div>
                        </div>
                    </div>
                    <div class="col-md-1">
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
        let priceInput = div.querySelector(".price-input");
        let priceSpan = div.querySelector(".article-price");
        let uniteSpan = div.querySelector(".article-unite");

        function updateSubtotal() {
            let selectedOption = select.options[select.selectedIndex];
            let unite = selectedOption.dataset.unite || '-';
            let price = parseFloat(priceInput.value) || 0;
            let quantity = parseInt(quantityInput.value) || 1;
            let subtotal = price * quantity;
            
            uniteSpan.textContent = unite;
            priceSpan.textContent = subtotal.toLocaleString() + " FCFA";
            updateTotal();
        }

        function fillDefaultPrice() {
            let selectedOption = select.options[select.selectedIndex];
            let defaultPrice = parseFloat(selectedOption.dataset.price || 0);
            if (defaultPrice > 0 && !priceInput.value) {
                priceInput.value = defaultPrice;
                updateSubtotal();
            }
        }

        select.addEventListener("change", function() {
            fillDefaultPrice();
            updateSubtotal();
        });
        quantityInput.addEventListener("input", updateSubtotal);
        priceInput.addEventListener("input", updateSubtotal);
    });

    function updateTotal() {
        let totalHT = 0;

        document.querySelectorAll(".article-item").forEach(function (item) {
            let select = item.querySelector(".article-select");
            let quantityInput = item.querySelector(".quantity-input");
            
            if (select && quantityInput) {
                let selectedOption = select.options[select.selectedIndex];
                let price = parseFloat(selectedOption.dataset.price || 0);
                let quantity = parseInt(quantityInput.value) || 1;
                totalHT += price * quantity;
            }
        });

        let tva = totalHT * 0.18;
        let totalTTC = totalHT + tva;

        document.getElementById("total-ht").textContent = totalHT.toLocaleString();
        document.getElementById("total-tva").textContent = tva.toLocaleString();
        document.getElementById("total-ttc").textContent = totalTTC.toLocaleString();
    }
</script>
@endpush
@endsection