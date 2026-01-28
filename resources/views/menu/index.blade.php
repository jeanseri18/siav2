<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #033d71 0%,#033d71 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }

        .header-logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--card-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: var(--card-color);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .card:hover .card-icon {
            transform: rotateY(360deg);
            border-radius: 50%;
        }

        .card-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .card p {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .card-btn {
            background: var(--card-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .card-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .card-btn i {
            transition: transform 0.3s ease;
        }

        .card:hover .card-btn i {
            transform: translateX(5px);
        }

        /* Couleurs spécifiques */
        .card-projets {
            --card-color: #3b82f6;
        }

        .card-stock {
            --card-color: #10b981;
        }

        .card-commercial {
            --card-color: #06b6d4;
        }

        .card-acces {
            --card-color: #f59e0b;
        }

        .card-utils {
            --card-color: #6366f1;
        }

        .card-tableau {
            --card-color: #ef4444;
        }

        .card-tresorerie {
            --card-color: #8b5cf6;
        }

        .card-guides {
            --card-color: #64748b;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('Logo_XBTP_Png/Logo_Blanc.png') }}" alt="XBTP" class="header-logo">
            <h1>Menu Principal</h1>
            <p>Accédez rapidement à tous vos outils de gestion</p>
        </div>

        <div class="grid">
            <!-- Gestion de Projets -->
            <div class="card card-projets">
                <div class="card-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h3>Gestion de Projets</h3>
                <p>Gérer les projets, les contrats et les ressources associées</p>
                <button class="card-btn" onclick="navigate('sublayouts_projet')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Gestion de Stock -->
            <div class="card card-stock">
                <div class="card-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3>Gestion de Stock</h3>
                <p>Gérer les articles, les mouvements de stock et les inventaires</p>
                <button class="card-btn" onclick="navigate('sublayouts_article')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Gestion Commerciale -->
            <div class="card card-commercial">
                <div class="card-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Gestion Commerciale</h3>
                <p>Gérer les clients, les fournisseurs et les transactions commerciales</p>
                <button class="card-btn" onclick="navigate('sublayouts_vente')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Accès Privilèges -->
            <div class="card card-acces">
                <div class="card-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Accès & Privilèges</h3>
                <p>Gérer les utilisateurs, les rôles et les permissions</p>
                <button class="card-btn" onclick="navigate('sublayouts_user')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Utilitaires -->
            <div class="card card-utils">
                <div class="card-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>Utilitaires</h3>
                <p>Outils et configurations système</p>
                <button class="card-btn" onclick="navigate('sublayouts_until')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Tableau de Bord -->
            <div class="card card-tableau">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Tableau de Bord</h3>
                <p>Statistiques et rapports</p>
                <button class="card-btn" onclick="navigate('statistiques.index')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Gestion de la Trésorerie -->
            <div class="card card-tresorerie">
                <div class="card-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <h3>Gestion de la Trésorerie</h3>
                <p>Gérer la comptabilité et les opérations financières</p>
                <button class="card-btn" onclick="navigate('sublayouts_caisse')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- Guides Utilisateurs -->
            <div class="card card-guides">
                <div class="card-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Guides Utilisateurs</h3>
                <p>Documentation et guides d'utilisation du système</p>
                <button class="card-btn" onclick="navigate('guides.index')">
                    Accéder <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        function navigate(route) {
            console.log('Navigation vers:', route);
            // Navigation vers les routes Laravel
            const routes = {
                'projets.index': '/projets',
                'articles.index': '/articles',
                'clients.index': '/clients',
                'users.index': '/users',
                'statistiques.index': '/statistiques'
            };
            
            if (routes[route]) {
                window.location.href = routes[route];
            } else {
                // Pour les routes non définies, essayer d'utiliser la route directement
                window.location.href = route.startsWith('/') ? route : '/' + route;
            }
        }
    </script>
</body>
</html>