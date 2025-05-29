
@extends('layouts.app')
@section('content')

<div class="dashboard-section business-units">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-building"></i>
            Gestion des Business Units
        </h2>
    </div>
    
    <div class="dashboard-grid">
        <a href="{{ route('bu.index') }}" class="dashboard-card primary">
            <div class="card-icon">
                <i class="fas fa-list-ul"></i>
            </div>
            <div class="card-content">
                <h3>Liste des BU</h3>
                <p>Consulter toutes les Business Units</p>
            </div>

        </a>
        
        <a href="{{ route('bu.create') }}" class="dashboard-card success">
            <div class="card-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="card-content">
                <h3>Nouvelle BU</h3>
                <p>Créer une nouvelle Business Unit</p>
            </div>
       
        </a>
    </div>
</div>

<style>
:root {
    --primary-color: #033765;
    --secondary-color: #0A8CFF;
    --success-color: #28a745;
    --accent-color: #ffffff;
    --gradient-primary: linear-gradient(135deg, #033765 0%, #0A8CFF 100%);
    --gradient-success: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    --shadow-card: 0 10px 30px rgba(3, 55, 101, 0.1);
    --shadow-hover: 0 20px 40px rgba(3, 55, 101, 0.2);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-section.business-units {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 2600px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section.business-units::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(30%, -30%);
    pointer-events: none;
}

.dashboard-section.business-units::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(-30%, 30%);
    pointer-events: none;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-color);
    margin-bottom: 2.5rem;
    text-align: center;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    position: relative;
    z-index: 1;
}

.section-title i {
    margin-right: 1rem;
    background: linear-gradient(45deg, #FFA726, #FF7043);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: buildingPulse 2s ease-in-out infinite;
}

@keyframes buildingPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.dashboard-card {
    background: var(--gradient-card);
    border: none;
    border-radius: var(--border-radius);
    padding: 2.5rem;
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
    min-height: 180px; /* plus compact */
    border: 2px solid transparent;
}

.dashboard-card.primary::before {
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

.dashboard-card.success::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--gradient-success);
    transition: var(--transition);
    z-index: -1;
}

.dashboard-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--shadow-hover);
    color: white;
    border-color: rgba(255,255,255,0.3);
}

.dashboard-card:hover::before {
    left: 0;
}

.card-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    color: var(--secondary-color);
    transition: var(--transition);
    position: relative;
}

.dashboard-card.primary .card-icon {
    color: var(--primary-color);
}

.dashboard-card.success .card-icon {
    color: var(--success-color);
}

.dashboard-card:hover .card-icon {
    color: white;
    transform: scale(1.15) rotateY(360deg);
}

.card-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin-bottom: 1rem;
}

.card-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    line-height: 1.3;
}

.card-content p {
    font-size: 1rem;
    opacity: 0.8;
    margin: 0;
    line-height: 1.5;
}

.card-stats {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    background: rgba(10, 140, 255, 0.1);
    border-radius: 12px;
    margin-top: 1rem;
    transition: var(--transition);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.7;
    margin-top: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.dashboard-card:hover .card-stats {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

.dashboard-card:hover .stat-number {
    color: white;
}

.card-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 25px;
    font-weight: 600;
    margin-top: 1rem;
    transition: var(--transition);
}

.dashboard-card:hover .card-action {
    background: rgba(255,255,255,0.2);
    transform: translateX(5px);
}

.card-action i {
    transition: var(--transition);
}

.dashboard-card:hover .card-action i {
    transform: translateX(3px);
}

/* Animations d'entrée */
.dashboard-card {
    animation: slideInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(50px);
}

.dashboard-card:nth-child(1) { animation-delay: 0.2s; }
.dashboard-card:nth-child(2) { animation-delay: 0.4s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Effet de particules */
.dashboard-card::after {
    content: '';
    position: absolute;
    top: 20px;
    right: 20px;
    width: 8px;
    height: 8px;
    background: var(--secondary-color);
    border-radius: 50%;
    opacity: 0.3;
    animation: particle 3s ease-in-out infinite;
}

@keyframes particle {
    0%, 100% { 
        transform: translate(0, 0) scale(1); 
        opacity: 0.3; 
    }
    50% { 
        transform: translate(-10px, -10px) scale(1.5); 
        opacity: 0.7; 
    }
}

.dashboard-card.success::after {
    background: var(--success-color);
    animation-delay: 1s;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-section.business-units {
        padding: 2rem 1rem;
        margin: 1rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .dashboard-card {
        padding: 2rem;
        min-height: 220px;
    }
    
    .card-icon {
        font-size: 3.5rem;
    }
}

@media (max-width: 480px) {
    .section-title {
        font-size: 1.8rem;
    }
    
    .dashboard-card {
        padding: 1.5rem;
        min-height: 200px;
    }
    
    .card-icon {
        font-size: 3rem;
    }
    
    .card-content h3 {
        font-size: 1.3rem;
    }
}

/* Hover spécial pour mobile */
@media (hover: none) {
    .dashboard-card:active {
        transform: scale(0.98);
    }
}
</style>

@endsection