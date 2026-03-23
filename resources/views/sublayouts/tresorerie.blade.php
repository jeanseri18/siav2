@extends('layouts.app')
@section('content')

<div class="dashboard-section tresorerie">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-coins"></i>
            Gestion de la Trésorerie
        </h2>
    </div>

    <div class="dashboard-grid">
        <a href="{{ route('sublayouts_caisse') }}" class="dashboard-card primary">
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="card-content">
                <h3>Gestion de la caisse</h3>
                <p>Caisse, dépenses, approvisionnements</p>
            </div>
        </a>

        <a href="{{ route('sublayouts_banque') }}" class="dashboard-card info">
            <div class="card-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="card-content">
                <h3>Banque</h3>
                <p>Gérer les banques et les informations de compte</p>
            </div>
        </a>

        <a href="{{ route('bu-budget.index') }}" class="dashboard-card success">
            <div class="card-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="card-content">
                <h3>Budget BU</h3>
                <p>Budget annuel (références / paramètres / valeurs)</p>
            </div>
        </a>
    </div>
</div>

<style>
:root {
    --primary-color: #033d71;
    --secondary-color: #0A8CFF;
    --success-color: #28a745;
    --info-color: #17a2b8;
    --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    --gradient-info: linear-gradient(135deg, var(--info-color) 0%, #20c997 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    --shadow-card: 0 10px 30px rgba(3, 55, 101, 0.12);
    --shadow-hover: 0 20px 40px rgba(3, 55, 101, 0.22);
    --border-radius: 16px;
    --transition: all 0.25s ease-in-out;
}

.dashboard-section.tresorerie {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 1400px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section.tresorerie::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    pointer-events: none;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 2.5rem;
    text-align: center;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
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
.dashboard-card.info::before { background: var(--gradient-info); }

.dashboard-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--shadow-hover);
    color: white;
    border-color: rgba(255,255,255,0.35);
}

.dashboard-card:hover::before {
    left: 0;
}

.dashboard-card:hover * {
    color: white;
}

.card-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    transition: var(--transition);
}

.dashboard-card.primary .card-icon { color: var(--primary-color); }
.dashboard-card.info .card-icon { color: var(--info-color); }

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
    .dashboard-section.tresorerie {
        padding: 2rem 1rem;
        margin: 1rem;
    }

    .section-title {
        font-size: 2rem;
        margin-bottom: 2rem;
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
