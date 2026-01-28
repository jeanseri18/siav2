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
        <a href="{{ route('articles.index') }}" class="dashboard-card primary">
            <div class="card-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <div class="card-content">
                <h3>Stock</h3>
                <p>Consultez l'état des stocks</p>
            </div>
        </a>
        
        <a href="{{ route('articles.create') }}" class="dashboard-card success">
            <div class="card-icon">
                <i class="fas fa-plus-square"></i>
            </div>
            <div class="card-content">
                <h3>Nouveau Article</h3>
                <p>Ajouter un nouvel article</p>
            </div>
        </a>
        
        <a href="{{ route('demande-approvisionnements.index') }}" class="dashboard-card warning">
            <div class="card-icon">
                <i class="fas fa-truck-loading"></i>
            </div>
            <div class="card-content">
                <h3>Demandes d'Approvisionnement</h3>
                <p>Gérer les demandes de réapprovisionnement</p>
            </div>
        </a>
        
        <a href="{{ route('bon-commandes.index') }}" class="dashboard-card info">
            <div class="card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="card-content">
                <h3>Bons de Commande</h3>
                <p>Consulter et gérer les commandes</p>
            </div>
        </a>
        
        <a href="{{ route('demande-achats.index') }}" class="dashboard-card primary">
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-content">
                <h3>Demandes d'Achat</h3>
                <p>Traiter les demandes d'achat</p>
            </div>
        </a>
        
        <a href="{{ route('demande-cotations.index') }}" class="dashboard-card secondary">
            <div class="card-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="card-content">
                <h3>Demandes de Cotation</h3>
                <p>Gérer les demandes et comparaisons</p>
            </div>
        </a>
        
        <a href="{{ route('receptions.index') }}" class="dashboard-card success">
            <div class="card-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="card-content">
                <h3>Réception</h3>
                <p>Gérer les réceptions d'articles livrés</p>
            </div>
        </a>

        <a href="{{ route('fournisseurs.index') }}" class="dashboard-card info">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-content">
                <h3>Fournisseurs</h3>
                <p>Afficher la liste des fournisseurs</p>
            </div>
        </a>

        
    </div>
</div>

<style>
:root {
    /* Variables harmonisées avec app.blade.php */
    --primary-color: var(--primary, #033d71);
    --secondary-color: var(--primary-light, #0A8CFF);
    --success-color: var(--success, #28a745);
    --warning-color: var(--warning, #ffc107);
    --info-color: var(--info, #17a2b8);
    --accent-color: var(--white, #ffffff);
    --gradient-primary: linear-gradient(135deg, var(--primary, #033d71) 0%, var(--primary-light, #0A8CFF) 100%);
    --gradient-success: linear-gradient(135deg, var(--success, #28a745) 0%, #20c997 100%);
    --gradient-warning: linear-gradient(135deg, var(--warning, #ffc107) 0%, #fd7e14 100%);
    --gradient-info: linear-gradient(135deg, var(--info, #17a2b8) 0%, #20c997 100%);
    --gradient-secondary: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    --gradient-card: linear-gradient(135deg, var(--white, #ffffff) 0%, #f8f9ff 100%);
    --shadow-card: var(--shadow-md, 0 0.5rem 1rem rgba(0, 0, 0, 0.15));
    --shadow-hover: var(--shadow-lg, 0 1rem 3rem rgba(0, 0, 0, 0.175));
    --border-radius: var(--border-radius-lg, 1rem);
    --transition: var(--transition-base, all 0.2s ease-in-out);
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
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-lg, 1.5rem);
    position: relative;
    z-index: 1;
}

.dashboard-card {
    background: var(--gradient-card);
    border: none;
    border-radius: var(--border-radius);
    padding: var(--spacing-lg, 1.5rem);
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
    min-height: 280px;
    max-height: 320px;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    transition: var(--transition);
    z-index: -1;
}

.dashboard-card.primary::before {
    background: var(--gradient-primary);
}

.dashboard-card.success::before {
    background: var(--gradient-success);
}

.dashboard-card.warning::before {
    background: var(--gradient-warning);
}

.dashboard-card.info::before {
    background: var(--gradient-info);
}

.dashboard-card.secondary::before {
    background: var(--gradient-secondary);
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
    transition: var(--transition);
}

.dashboard-card.primary .card-icon {
    color: var(--primary-color);
}

.dashboard-card.success .card-icon {
    color: var(--success-color);
}

.dashboard-card.warning .card-icon {
    color: var(--warning-color);
}

.dashboard-card.info .card-icon {
    color: var(--info-color);
}

.dashboard-card.secondary .card-icon {
    color: #6c757d;
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
.dashboard-card:nth-child(7) { animation-delay: 0.7s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
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