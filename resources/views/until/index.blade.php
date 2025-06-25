@extends('layouts.app')

@section('title', 'Utilitaires')
@section('page-title', 'Utilitaires')



@section('content')


<div class="container-fluid py-4">
    <!-- Header avec recherche et filtres -->
    <div class="utilities-header card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="utilities-title">
                        <i class="fas fa-tools me-2"></i>Centre d'Utilitaires
                    </h2>
                    <p class="text-muted">Accédez rapidement aux outils de configuration et aux modules du système</p>
                </div>
                <div class="col-md-6">
                    <div class="utilities-search">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchUtilities" placeholder="Rechercher un outil..." aria-label="Rechercher">
                            <button class="btn btn-outline-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation par catégories -->
    <div class="utilities-categories mb-4">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-category="all">Tous</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="projects">Projets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="inventory">Inventaire</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="contacts">Contacts</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="finance">Finance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="locations">Localisations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-category="other">Autres</a>
            </li>
        </ul>
    </div>

    @php 
    $links = [
        [
            'route' => 'projets.index', 
            'icon' => 'fa-folder-open', 
            'text' => 'Projets', 
            'description' => 'Gérer tous les projets',
            'category' => 'projects',
            'color' => 'primary'
        ],
        [
            'route' => 'secteur_activites.index', 
            'icon' => 'fa-building', 
            'text' => 'Secteurs d\'activité', 
            'description' => 'Gérer les secteurs d\'activité',
            'category' => 'projects',
            'color' => 'primary'
        ],
        [
            'route' => 'categories.index', 
            'icon' => 'fa-tag', 
            'text' => 'Catégories', 
            'description' => 'Gérer les catégories',
            'category' => 'inventory',
            'color' => 'success'
        ],
        [
            'route' => 'sous_categories.index', 
            'icon' => 'fa-list-ul', 
            'text' => 'Sous-catégories', 
            'description' => 'Gérer les sous-catégories',
            'category' => 'inventory',
            'color' => 'success'
        ],
        [
            'route' => 'articles.index', 
            'icon' => 'fa-boxes', 
            'text' => 'Articles', 
            'description' => 'Gérer les articles et produits',
            'category' => 'inventory',
            'color' => 'success'
        ],
        [
            'route' => 'bu.create', 
            'icon' => 'fa-city', 
            'text' => 'Business Units', 
            'description' => 'Gérer les unités d\'affaires',
            'category' => 'projects',
            'color' => 'primary'
        ],
        [
            'route' => 'contrats.all', 
            'icon' => 'fa-file-contract', 
            'text' => 'Liste des contrats', 
            'description' => 'Afficher la liste des contrats de tous les projets',
            'category' => 'projects',
            'color' => 'primary'
        ],
        [
            'route' => 'clients.index', 
            'icon' => 'fa-users', 
            'text' => 'Clients', 
            'description' => 'Gérer la base clients',
            'category' => 'contacts',
            'color' => 'info'
        ],
        [
            'route' => 'fournisseurs.index', 
            'icon' => 'fa-truck', 
            'text' => 'Fournisseurs', 
            'description' => 'Gérer les fournisseurs',
            'category' => 'contacts',
            'color' => 'info'
        ],
        [
            'route' => 'artisans.index', 
            'icon' => 'fa-hard-hat', 
            'text' => 'Artisans', 
            'description' => 'Gérer les artisans',
            'category' => 'contacts',
            'color' => 'info'
        ],
        [
            'route' => 'employes.index', 
            'icon' => 'fa-user-tie', 
            'text' => 'Employés', 
            'description' => 'Gérer les employés de l\'entreprise',
            'category' => 'contacts',
            'color' => 'info'
        ],
        [
            'route' => 'corpsmetiers.index', 
            'icon' => 'fa-hammer', 
            'text' => 'Corps de Métier', 
            'description' => 'Gérer les corps de métier',
            'category' => 'other',
            'color' => 'secondary'
        ],
        [
            'route' => 'pays.index', 
            'icon' => 'fa-globe', 
            'text' => 'Pays', 
            'description' => 'Gérer les pays',
            'category' => 'locations',
            'color' => 'warning'
        ],
        [
            'route' => 'villes.index', 
            'icon' => 'fa-city', 
            'text' => 'Villes', 
            'description' => 'Gérer les villes',
            'category' => 'locations',
            'color' => 'warning'
        ],
        [
            'route' => 'communes.index', 
            'icon' => 'fa-building', 
            'text' => 'Communes', 
            'description' => 'Gérer les communes',
            'category' => 'locations',
            'color' => 'warning'
        ],
        [
            'route' => 'quartiers.index', 
            'icon' => 'fa-map-marked-alt', 
            'text' => 'Quartiers', 
            'description' => 'Gérer les quartiers',
            'category' => 'locations',
            'color' => 'warning'
        ],
        [
            'route' => 'secteurs.index', 
            'icon' => 'fa-sitemap', 
            'text' => 'Secteurs', 
            'description' => 'Gérer les secteurs',
            'category' => 'locations',
            'color' => 'warning'
        ],
        [
            'route' => 'type-travaux.index', 
            'icon' => 'fa-cogs', 
            'text' => 'Types de travaux', 
            'description' => 'Gérer les types de travaux',
            'category' => 'projects',
            'color' => 'primary'
        ],
        [
            'route' => 'unite-mesures.index', 
            'icon' => 'fa-ruler-combined', 
            'text' => 'Unités de mesure', 
            'description' => 'Gérer les unités de mesure',
            'category' => 'inventory',
            'color' => 'success'
        ],
        [
            'route' => 'regime-impositions.index', 
            'icon' => 'fa-balance-scale', 
            'text' => 'Régimes d\'imposition', 
            'description' => 'Gérer les régimes fiscaux',
            'category' => 'finance',
            'color' => 'danger'
        ],
        [
            'route' => 'banques.index', 
            'icon' => 'fa-university', 
            'text' => 'Banques', 
            'description' => 'Gérer les banques',
            'category' => 'finance',
            'color' => 'danger'
        ],
        [
            'route' => 'references.index', 
            'icon' => 'fa-bookmark', 
            'text' => 'Références', 
            'description' => 'Gérer les références',
            'category' => 'other',
            'color' => 'secondary'
        ],
        [
            'route' => 'monnaies.index', 
            'icon' => 'fa-coins', 
            'text' => 'Monnaies', 
            'description' => 'Gérer les devises',
            'category' => 'finance',
            'color' => 'danger'
        ],
        [
            'route' => 'modes_de_paiement.index', 
            'icon' => 'fa-credit-card', 
            'text' => 'Modes de paiement', 
            'description' => 'Gérer les modes de paiement',
            'category' => 'finance',
            'color' => 'danger'
        ],
        [
            'route' => 'bpu.indexuntil', 
            'icon' => 'fa-file-invoice-dollar', 
            'text' => 'Bordereau de prix unitaire', 
            'description' => 'Gérer les prix unitaires',
            'category' => 'finance',
            'color' => 'danger'
        ],
    ]; 
    @endphp

    <!-- Grille des utilitaires -->
    <div class="row g-4 utilities-grid">
        @foreach ($links as $link)
            <div class="col-xl-3 col-lg-4 col-md-6 utility-item" data-category="{{ $link['category'] }}">
                <div class="card utility-card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <div class="utility-icon-wrapper text-{{ $link['color'] }} mb-3">
                            <i class="fas {{ $link['icon'] }}"></i>
                        </div>
                        <h4 class="utility-title">{{ $link['text'] }}</h4>
                        <p class="utility-description text-muted">{{ $link['description'] }}</p>
                        <div class="mt-auto text-end">
                            <a href="{{ route($link['route']) }}" class="btn btn-sm btn-outline-{{ $link['color'] }}">
                                Accéder <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Message si aucun résultat -->
    <div class="no-results text-center py-5 d-none">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h3>Aucun résultat trouvé</h3>
        <p class="text-muted">Essayez de modifier votre recherche ou sélectionnez une autre catégorie.</p>
        <button class="btn btn-outline-primary mt-2" onclick="resetSearch()">Réinitialiser la recherche</button>
    </div>
</div>

<style>
    .utilities-header {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-radius: 15px;
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
    }

    .utilities-title {
        color: #033765;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .utilities-search .form-control {
        border-radius: 30px 0 0 30px;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e9ecef;
    }

    .utilities-search .btn {
        border-radius: 0 30px 30px 0;
        padding: 0.75rem 1.5rem;
    }

    .utilities-categories {
        padding: 0.5rem 0;
    }

    .utilities-categories .nav-pills {
        gap: 0.5rem;
    }

    .utilities-categories .nav-link {
        color: #6c757d;
        border-radius: 30px;
        padding: 0.5rem 1.25rem;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .utilities-categories .nav-link.active {
        background-color: #033765;
        color: white;
        box-shadow: 0 4px 10px rgba(3, 55, 101, 0.2);
    }

    .utility-card {
        border-radius: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .utility-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .utility-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(3, 55, 101, 0.1);
        margin-bottom: 1rem;
    }

    .utility-icon-wrapper i {
        font-size: 1.75rem;
    }

    .text-primary .utility-icon-wrapper {
        background-color: rgba(3, 55, 101, 0.1);
    }

    .text-success .utility-icon-wrapper {
        background-color: rgba(40, 167, 69, 0.1);
    }

    .text-info .utility-icon-wrapper {
        background-color: rgba(23, 162, 184, 0.1);
    }

    .text-warning .utility-icon-wrapper {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .text-danger .utility-icon-wrapper {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .text-secondary .utility-icon-wrapper {
        background-color: rgba(108, 117, 125, 0.1);
    }

    .utility-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2d3436;
    }

    .utility-description {
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    /* Animation d'apparition */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .utility-item {
        animation: fadeInUp 0.4s ease-out forwards;
    }

    .utility-item:nth-child(1) { animation-delay: 0.05s; }
    .utility-item:nth-child(2) { animation-delay: 0.1s; }
    .utility-item:nth-child(3) { animation-delay: 0.15s; }
    .utility-item:nth-child(4) { animation-delay: 0.2s; }
    .utility-item:nth-child(5) { animation-delay: 0.25s; }
    .utility-item:nth-child(6) { animation-delay: 0.3s; }
    .utility-item:nth-child(7) { animation-delay: 0.35s; }
    .utility-item:nth-child(8) { animation-delay: 0.4s; }

    /* Responsivité */
    @media (max-width: 768px) {
        .utilities-header .row {
            flex-direction: column;
        }
        
        .utilities-search {
            margin-top: 1rem;
        }
        
        .utilities-categories {
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
        
        .utilities-categories .nav-pills {
            flex-wrap: nowrap;
            padding-bottom: 0.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtrage par catégorie
        const categoryLinks = document.querySelectorAll('.utilities-categories .nav-link');
        const utilityItems = document.querySelectorAll('.utility-item');
        const noResults = document.querySelector('.no-results');
        
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Mise à jour des liens actifs
                categoryLinks.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.getAttribute('data-category');
                
                // Filtrer les éléments
                let visibleCount = 0;
                
                utilityItems.forEach(item => {
                    if (category === 'all' || item.getAttribute('data-category') === category) {
                        item.style.display = '';
                        // Réinitialiser puis réappliquer l'animation
                        item.style.animation = 'none';
                        item.offsetHeight; // Forcer un reflow
                        item.style.animation = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Afficher le message "aucun résultat" si nécessaire
                if (visibleCount === 0) {
                    noResults.classList.remove('d-none');
                } else {
                    noResults.classList.add('d-none');
                }
            });
        });
        
        // Recherche en temps réel
        const searchInput = document.getElementById('searchUtilities');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            // Réinitialiser le filtre de catégorie
            if (searchTerm) {
                categoryLinks.forEach(link => link.classList.remove('active'));
                document.querySelector('[data-category="all"]').classList.add('active');
            }
            
            utilityItems.forEach(item => {
                const title = item.querySelector('.utility-title').textContent.toLowerCase();
                const description = item.querySelector('.utility-description').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Afficher le message "aucun résultat" si nécessaire
            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        });
    });
    
    // Fonction pour réinitialiser la recherche
    function resetSearch() {
        const searchInput = document.getElementById('searchUtilities');
        searchInput.value = '';
        
        // Simuler un événement input pour déclencher la recherche
        const event = new Event('input', {
            bubbles: true,
            cancelable: true,
        });
        
        searchInput.dispatchEvent(event);
        
        // Réactiver le filtre "Tous"
        document.querySelector('[data-category="all"]').click();
    }
</script>

@endsection