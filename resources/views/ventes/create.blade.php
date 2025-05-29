@extends('layouts.app')

@section('content')
<br>
<br>
<div class="container">
    <h1 class="mb-4">Nouvelle Vente</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('ventes.store') }}" method="POST">
                @csrf

                <!-- Sélection du client -->
                <div class="mb-3">
                    <label for="client_id" class="form-label">Client :</label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">Sélectionner un client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->prenoms }}</option>
                        @endforeach
                    </select>
                </div>

                <h3>Articles :</h3>

                <!-- Zone dynamique pour les articles -->
                <div id="articles-container" class="mb-3"></div>

                <!-- Bouton pour ajouter un article -->
                <button type="button" id="add-article-btn" class="btn btn-success mb-3">+ Ajouter un article</button>

                <!-- Affichage du total -->
                <div class="text-end">
                    <h4>Total : <span id="total-price">0</span> FCFA</h4>
                </div>

                <button type="submit" class="btn btn-primary">Valider la vente</button>
            </form>
        </div>
    </div>
</div>

<script>
    let articles = @json($articles);
    let articlesContainer = document.getElementById("articles-container");
    let articleIndex = 0;

    document.getElementById("add-article-btn").addEventListener("click", function () {
        let div = document.createElement("div");
        div.classList.add("article-item", "d-flex", "align-items-center", "mb-2", "gap-2");

        div.innerHTML = `
            <select name="articles[${articleIndex}][id]" class="form-select me-2 article-select" required>
                <option value="">Sélectionner un article</option>
                ${articles.map(article => `<option value="${article.id}" data-price="${article.prix_unitaire}">${article.nom} - ${article.prix_unitaire} FCFA</option>`).join('')}
            </select>
            <input type="number" name="articles[${articleIndex}][quantite]" class="form-control me-2 quantity-input" min="1" value="1" required>
            <span class="article-price text-end fw-bold px-3 py-1 border border-secondary rounded" style="min-width: 150px;">0 FCFA</span>
            <button type="button" class="btn btn-danger remove-article">X</button>
        `;

        articlesContainer.appendChild(div);
        articleIndex++;

        updateTotal(); // Met à jour le total

        // Ajouter l'événement de suppression
        div.querySelector(".remove-article").addEventListener("click", function () {
            div.remove();
            updateTotal();
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

        document.getElementById("total-price").textContent = total.toLocaleString() + " FCFA";
    }
</script>
@endsection
