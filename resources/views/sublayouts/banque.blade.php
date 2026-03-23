@extends('layouts.app')
@section('content')

<div class="dashboard-section banque">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-university"></i>
            Banque
        </h2>
        <div class="section-subtitle">
            <span class="app-badge app-badge-info app-badge-pill">BU: {{ session('selected_bu') }}</span>
        </div>
    </div>

    <div class="dashboard-grid">
        <a href="{{ route('banques.index') }}" class="dashboard-card primary">
            <div class="card-icon">
                <i class="fas fa-landmark"></i>
            </div>
            <div class="card-content">
                <h3>Banques</h3>
                <p>Créer et gérer les banques de la BU</p>
            </div>
        </a>

        <a href="{{ route('banque.mouvements.create') }}" class="dashboard-card info">
            <div class="card-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="card-content">
                <h3>Renseigner une opération</h3>
                <p>Entrée, sortie, chèque, espèce, virement</p>
            </div>
        </a>

        <a href="{{ route('banque.mouvements.index') }}" class="dashboard-card warning">
            <div class="card-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="card-content">
                <h3>Liste des mouvements</h3>
                <p>Consulter toutes les opérations de la BU</p>
            </div>
        </a>

        <a href="{{ route('banque.soldes.index') }}" class="dashboard-card success">
            <div class="card-icon">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="card-content">
                <h3>Comparer les soldes</h3>
                <p>Prévisionnel vs réel, par banque</p>
            </div>
        </a>
    </div>
</div>

<style>
:root {
    --primary-color: #033d71;
    --secondary-color: #0A8CFF;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    --gradient-success: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
    --gradient-warning: linear-gradient(135deg, var(--warning-color) 0%, #ff9800 100%);
    --gradient-info: linear-gradient(135deg, var(--info-color) 0%, #20c997 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    --shadow-card: 0 10px 30px rgba(3, 55, 101, 0.12);
    --shadow-hover: 0 20px 40px rgba(3, 55, 101, 0.22);
    --border-radius: 16px;
    --transition: all 0.25s ease-in-out;
}

.dashboard-section.banque {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 1400px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section.banque::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    pointer-events: none;
}

.section-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2.5rem;
    position: relative;
    z-index: 1;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    text-align: center;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    margin: 0;
}

.section-title i {
    margin-right: 1rem;
    background: linear-gradient(45deg, #FFD700, #FFA500);
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
    border-radius: var(--border-radius);
    padding: 2.5rem 2rem;
    text-decoration: none;
    color: var(--primary-color);
    transition: var(--transition);
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-height: 260px;
    border: 2px solid transparent;
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

.dashboard-card.primary::before { background: var(--gradient-primary); }
.dashboard-card.success::before { background: var(--gradient-success); }
.dashboard-card.info::before { background: var(--gradient-info); }
.dashboard-card.warning::before { background: var(--gradient-warning); }

.dashboard-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--shadow-hover);
    color: white;
    border-color: rgba(255,255,255,0.35);
}

.dashboard-card:hover::before { left: 0; }
.dashboard-card:hover * { color: white; }

.card-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    transition: var(--transition);
}

.dashboard-card.primary .card-icon { color: var(--primary-color); }
.dashboard-card.success .card-icon { color: var(--success-color); }
.dashboard-card.info .card-icon { color: var(--info-color); }
.dashboard-card.warning .card-icon { color: var(--warning-color); }

.card-content h3 {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0 0 0.75rem 0;
}

.card-content p {
    font-size: 1rem;
    opacity: 0.85;
    margin: 0;
}

@media (max-width: 768px) {
    .dashboard-section.banque {
        padding: 2rem 1rem;
        margin: 1rem;
    }

    .section-title {
        font-size: 2rem;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .dashboard-card {
        padding: 2rem 1.5rem;
        min-height: 240px;
    }
}
</style>

@endsection
