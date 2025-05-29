@extends('layouts.app')
@section('content')


<div class="dashboard-section stocks">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-boxes"></i>
            Gestion des Stocks et Approvisionnements
        </h2>
    </div>
    
    <div class="dashboard-grid">
        <a href="{{ route('articles.index') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <div class="card-content">
                <h3>Stock</h3>
                <p>Consultez l'état des stocks</p>
            </div>
            <div class="card-badge">
                <span>Inventaire</span>
            </div>
        </a>
        
        <a href="{{ route('articles.create') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-plus-square"></i>
            </div>
            <div class="card-content">
                <h3>Nouveau Article</h3>
                <p>Ajouter un nouvel article</p>
            </div>
            <div class="card-badge success">
                <span>Créer</span>
            </div>
        </a>
        
        <a href="{{ route('demande-approvisionnements.index') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-truck-loading"></i>
            </div>
            <div class="card-content">
                <h3>Demandes d'Approvisionnement</h3>
                <p>Gérer les demandes de réapprovisionnement</p>
            </div>
            <div class="card-badge warning">
                <span>En cours</span>
            </div>
        </a>
        
        <a href="{{ route('bon-commandes.index') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="card-content">
                <h3>Bons de Commande</h3>
                <p>Consulter et gérer les commandes</p>
            </div>
            <div class="card-badge info">
                <span>Commandes</span>
            </div>
        </a>
        
        <a href="{{ route('demande-achats.index') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-content">
                <h3>Demandes d'Achat</h3>
                <p>Traiter les demandes d'achat</p>
            </div>
            <div class="card-badge primary">
                <span>Achats</span>
            </div>
        </a>
        
        <a href="{{ route('demande-cotations.index') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="card-content">
                <h3>Demandes de Cotation</h3>
                <p>Gérer les demandes et comparaisons</p>
            </div>
            <div class="card-badge secondary">
                <span>Cotations</span>
            </div>
        </a>
    </div>
</div>

<style>
:root {
    --primary-color: #033765;
    --secondary-color: #0A8CFF;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --accent-color: #ffffff;
    --gradient-primary: linear-gradient(135deg, #033765 0%, #0A8CFF 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    --shadow-card: 0 10px 30px rgba(3, 55, 101, 0.1);
    --shadow-hover: 0 20px 40px rgba(3, 55, 101, 0.2);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-section.stocks {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 1400px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section.stocks::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
    pointer-events: none;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-color);
    margin-bottom: 2.5rem;
    text-align: center;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.section-title i {
    margin-right: 1rem;
    background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.dashboard-card {
    background: var(--gradient-card);
    border: none;
    border-radius: var(--border-radius);
    padding: 2rem;
    text-decoration: none;
    color: var(--primary-color);
    transition: var(--transition);
    box-shadow: var(--shadow-card);
    cursor: pointer;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-height: 200px;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--gradient-primary);
    transition: var(--transition);
    z-index: -1;
}

.dashboard-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-hover);
    color: white;
}

.dashboard-card:hover::before {
    left: 0;
}

.card-icon {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    color: var(--secondary-color);
    transition: var(--transition);
}

.dashboard-card:hover .card-icon {
    color: white;
    transform: scale(1.1) rotateY(180deg);
}

.card-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.card-content h3 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.card-content p {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0;
    line-height: 1.4;
}

.card-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--secondary-color);
    color: white;
    transition: var(--transition);
}

.card-badge.success {
    background: var(--success-color);
}

.card-badge.warning {
    background: var(--warning-color);
    color: #333;
}

.card-badge.info {
    background: var(--info-color);
}

.card-badge.primary {
    background: var(--primary-color);
}

.card-badge.secondary {
    background: #6c757d;
}

.dashboard-card:hover .card-badge {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Animation pour les cartes */
.dashboard-card {
    animation: slideInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(30px);
}

.dashboard-card:nth-child(1) { animation-delay: 0.1s; }
.dashboard-card:nth-child(2) { animation-delay: 0.2s; }
.dashboard-card:nth-child(3) { animation-delay: 0.3s; }
.dashboard-card:nth-child(4) { animation-delay: 0.4s; }
.dashboard-card:nth-child(5) { animation-delay: 0.5s; }
.dashboard-card:nth-child(6) { animation-delay: 0.6s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Effet de survol spécial pour chaque carte */
.dashboard-card:nth-child(1):hover { --hover-color: #FF6B6B; }
.dashboard-card:nth-child(2):hover { --hover-color: #4ECDC4; }
.dashboard-card:nth-child(3):hover { --hover-color: #45B7D1; }
.dashboard-card:nth-child(4):hover { --hover-color: #96CEB4; }
.dashboard-card:nth-child(5):hover { --hover-color: #FFEAA7; }
.dashboard-card:nth-child(6):hover { --hover-color: #DDA0DD; }

.dashboard-card:hover::before {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--hover-color, var(--secondary-color)) 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-section.stocks {
        padding: 2rem 1rem;
        margin: 1rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .dashboard-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }
    
    .dashboard-card {
        padding: 1.5rem;
        min-height: 180px;
    }
    
    .card-icon {
        font-size: 3rem;
    }
}

@media (max-width: 480px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .section-title i {
        display: block;
        margin-bottom: 0.5rem;
    }
}

/* Effets visuels supplémentaires */
.dashboard-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--secondary-color);
    transform: scaleX(0);
    transition: var(--transition);
}

.dashboard-card:hover::after {
    transform: scaleX(1);
}

/* Indicateurs de statut */
.card-badge::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    margin-right: 4px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

@endsection