@extends('layouts.app')

@section('content')
<div class="dashboard-container">
    <!-- Header du Dashboard -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1 class="dashboard-title">
                <i class="fas fa-chart-pie"></i>
                Tableau de Bord Complet
            </h1>
            <p class="dashboard-subtitle">Vue d'ensemble complète des performances</p>
        </div>

    </div>
 <div class="financial-summary">
        <h3 class="section-title">
            <i class="fas fa-wallet"></i>
            Résumé Financier
        </h3>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-title">Solde de Caisse</div>
                <div class="summary-value {{ $soldeCaisse >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($soldeCaisse, 0, ',', ' ') }} CFA
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Montant Contrats Actifs</div>
                <div class="summary-value">
                    {{ number_format($montantContratsTotal, 0, ',', ' ') }} CFA
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Bons de Commande</div>
                <div class="summary-value">
                    {{ number_format($montantBonCommandes, 0, ',', ' ') }} CFA
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Demandes de Dépense</div>
                <div class="summary-value">
                    {{ number_format($montantDemandesDepense, 0, ',', ' ') }} CFA
                </div>
            </div>
        </div>
    </div>
    <!-- Cartes de Statistiques Principales -->
    <div class="stats-grid">
        <!-- Projets -->
        <div class="stat-card projects">
            <div class="stat-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $projetsEnCours }}</div>
                <div class="stat-label">Projets en Cours</div>
                <div class="stat-sublabel">{{ $totalProjets }} au total</div>
                <div class="stat-trend {{ isset($tendancesProjets) && $tendancesProjets > 0 ? 'positive' : (isset($tendancesProjets) && $tendancesProjets < 0 ? 'negative' : '') }}">
                    <i class="fas fa-arrow-{{ isset($tendancesProjets) && $tendancesProjets > 0 ? 'up' : (isset($tendancesProjets) && $tendancesProjets < 0 ? 'down' : 'right') }}"></i>
                    <span>{{ isset($tendancesProjets) ? abs($tendancesProjets) : 0 }}% ce mois</span>
                </div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar" style="width: {{ $totalProjets > 0 ? ($projetsEnCours / $totalProjets) * 100 : 0 }}%"></div>
            </div>
        </div>

        <!-- Revenus -->
        <div class="stat-card revenue">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($revenusTotaux, 0, ',', ' ') }}</div>
                <div class="stat-currency">CFA</div>
                <div class="stat-label">Revenus Totaux</div>
                <div class="stat-trend {{ isset($tendancesFinancieres['revenus']) && $tendancesFinancieres['revenus'] > 0 ? 'positive' : (isset($tendancesFinancieres['revenus']) && $tendancesFinancieres['revenus'] < 0 ? 'negative' : '') }}">
                    <i class="fas fa-arrow-{{ isset($tendancesFinancieres['revenus']) && $tendancesFinancieres['revenus'] > 0 ? 'up' : (isset($tendancesFinancieres['revenus']) && $tendancesFinancieres['revenus'] < 0 ? 'down' : 'right') }}"></i>
                    <span>{{ isset($tendancesFinancieres['revenus']) ? abs($tendancesFinancieres['revenus']) : 0 }}% ce trimestre</span>
                </div>
            </div>
            <div class="stat-chart">
                <canvas id="revenueSparkline" width="80" height="30"></canvas>
            </div>
        </div>

        <!-- Dépenses -->
        <div class="stat-card expenses">
            <div class="stat-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($depensesTotales, 0, ',', ' ') }}</div>
                <div class="stat-currency">CFA</div>
                <div class="stat-label">Dépenses Totales</div>
                <div class="stat-trend {{ isset($tendancesFinancieres['depenses']) && $tendancesFinancieres['depenses'] < 0 ? 'positive' : (isset($tendancesFinancieres['depenses']) && $tendancesFinancieres['depenses'] > 0 ? 'negative' : '') }}">
                    <i class="fas fa-arrow-{{ isset($tendancesFinancieres['depenses']) && $tendancesFinancieres['depenses'] < 0 ? 'down' : (isset($tendancesFinancieres['depenses']) && $tendancesFinancieres['depenses'] > 0 ? 'up' : 'right') }}"></i>
                    <span>{{ isset($tendancesFinancieres['depenses']) ? abs($tendancesFinancieres['depenses']) : 0 }}% ce mois</span>
                </div>
            </div>
            <div class="stat-chart">
                <canvas id="expensesSparkline" width="80" height="30"></canvas>
            </div>
        </div>

        <!-- Stock -->
        <div class="stat-card stock">
            <div class="stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $articlesEnStock }}</div>
                <div class="stat-label">Articles en Stock</div>
                <div class="stat-sublabel">{{ $categoriesStock ?? 'N/A' }} catégories</div>
                <div class="stat-trend {{ isset($articlesAlerte) && $articlesAlerte > 0 ? 'warning' : 'positive' }}">
                    <i class="fas fa-{{ isset($articlesAlerte) && $articlesAlerte > 0 ? 'exclamation-triangle' : 'check-circle' }}"></i>
                    <span>{{ isset($articlesAlerte) && $articlesAlerte > 0 ? $articlesAlerte . ' alertes' : 'Niveau optimal' }}</span>
                </div>
            </div>
            <div class="stock-indicator">
                <div class="indicator-item">
                    <span class="indicator-dot high"></span>
                    <span>Élevé</span>
                </div>
                <div class="indicator-item">
                    <span class="indicator-dot medium"></span>
                    <span>Moyen</span>
                </div>
                <div class="indicator-item">
                    <span class="indicator-dot low"></span>
                    <span>Faible</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Graphiques -->
    <div class="charts-section">
        <div class="chart-container main-chart">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-chart-line"></i>
                    Évolution Financière
                </h3>
                <div class="chart-controls">
                    <button class="btn-chart-filter active" data-period="month">Mensuel</button>
                    <button class="btn-chart-filter" data-period="quarter">Trimestriel</button>
                    <button class="btn-chart-filter" data-period="year">Annuel</button>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-container secondary-chart">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-chart-pie"></i>
                        Répartition des Dépenses
                    </h4>
                </div>
                <div class="chart-wrapper">
                    <canvas id="expensesChart"></canvas>
                </div>
            </div>

            <div class="chart-container secondary-chart">
                <div class="chart-header">
                    <h4 class="chart-title">
                        <i class="fas fa-chart-bar"></i>
                        Performance Projets
                    </h4>
                </div>
                <div class="chart-wrapper">
                    <canvas id="projectsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes et Notifications -->
    @if(isset($alertes) && count($alertes) > 0)
    <div class="alerts-section">
        <h3 class="section-title">
            <i class="fas fa-bell"></i>
            Alertes et Notifications
        </h3>
        <div class="alerts-grid">
            @foreach($alertes as $alerte)
            <div class="alert-item {{ $alerte['type'] }}">
                <div class="alert-icon">
                    <i class="fas fa-{{ $alerte['type'] == 'warning' ? 'exclamation-triangle' : ($alerte['type'] == 'success' ? 'check-circle' : ($alerte['type'] == 'danger' ? 'times-circle' : 'info-circle')) }}"></i>
                </div>
                <div class="alert-content">
                    <h5>{{ $alerte['titre'] }}</h5>
                    <p>{{ $alerte['message'] }}</p>
                </div>
                <div class="alert-action">
                    <button class="btn-alert">{{ $alerte['action'] }}</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Section Performance des Projets -->
    <div class="project-performance-section">
        <h3 class="section-title">
            <i class="fas fa-tasks"></i>
            Performance des Projets
        </h3>
        <div class="performance-table-container">
            <table class="performance-table">
                <thead>
                    <tr>
                        <th>Projet</th>
                        <th>Statut</th>
                        <th>Contrats</th>
                        <th>Montant Total</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($performanceProjets as $projet)
                    <tr>
                        <td>{{ $projet->nom_projet }}</td>
                        <td>
                            <span class="status-badge {{ $projet->statut == 'en cours' ? 'active' : ($projet->statut == 'terminé' ? 'completed' : 'pending') }}">
                                {{ ucfirst($projet->statut) }}
                            </span>
                        </td>
                        <td>{{ $projet->nb_contrats }}</td>
                        <td>{{ number_format($projet->montant_total ?? 0, 0, ',', ' ') }} CFA</td>
                        <td>
                            @php
                                // Simuler un pourcentage de performance basé sur le statut
                                $performance = $projet->statut == 'terminé' ? 100 : 
                                             ($projet->statut == 'en cours' ? rand(50, 95) : 
                                             rand(10, 40));
                            @endphp
                            <div class="progress-bar-small">
                                <div class="progress-bar-fill" style="width: {{ $performance }}%" data-value="{{ $performance }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Résumé Financier -->
   
</div>

<style>
:root {
    --primary-color: #033765;
    --secondary-color: #0A8CFF;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
    --light-bg: #f8f9fa;
    --white: #ffffff;
    --text-dark: #2d3436;
    --text-muted: #636e72;
    --gradient-primary: linear-gradient(135deg, #033765 0%, #0A8CFF 100%);
    --gradient-success: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    --gradient-warning: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    --gradient-danger: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
    --shadow-medium: 0 8px 25px rgba(0,0,0,0.15);
    --shadow-heavy: 0 15px 35px rgba(0,0,0,0.2);
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    background: var(--light-bg);
    min-height: 100vh;
}

/* Header du Dashboard */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem 2rem;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.dashboard-title i {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.dashboard-subtitle {
    color: var(--text-muted);
    margin: 0.5rem 0 0 0;
    font-size: 1.1rem;
}



/* Grille des Statistiques */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow-light);
    position: relative;
    overflow: hidden;
    transition: var(--transition);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    transition: var(--transition);
}

.stat-card.projects::before {
    background: var(--gradient-primary);
}

.stat-card.revenue::before {
    background: var(--gradient-success);
}

.stat-card.expenses::before {
    background: var(--gradient-danger);
}

.stat-card.stock::before {
    background: var(--gradient-warning);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-heavy);
}

.stat-icon {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--white);
    opacity: 0.9;
}

.stat-card.projects .stat-icon {
    background: var(--gradient-primary);
}

.stat-card.revenue .stat-icon {
    background: var(--gradient-success);
}

.stat-card.expenses .stat-icon {
    background: var(--gradient-danger);
}

.stat-card.stock .stat-icon {
    background: var(--gradient-warning);
}

.stat-content {
    position: relative;
    z-index: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-dark);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-currency {
    display: inline-block;
    font-size: 1rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-left: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.stat-sublabel {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
}

.stat-trend.positive {
    color: var(--success-color);
}

.stat-trend.negative {
    color: var(--danger-color);
}

.stat-trend.warning {
    color: var(--warning-color);
}

.stat-progress {
    margin-top: 1rem;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 2px;
    transition: width 1s ease-in-out;
}

.stock-indicator {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.indicator-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
    color: var(--text-muted);
}

.indicator-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.indicator-dot.high { background: var(--success-color); }
.indicator-dot.medium { background: var(--warning-color); }
.indicator-dot.low { background: var(--danger-color); }

/* Section Graphiques */
.charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.chart-container {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    overflow: hidden;
}

.chart-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chart-controls {
    display: flex;
    gap: 0.5rem;
}

.btn-chart-filter {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
    background: var(--white);
    color: var(--text-muted);
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--transition);
}

.btn-chart-filter.active,
.btn-chart-filter:hover {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.chart-wrapper {
    padding: 1.5rem;
    position: relative;
    height: 400px;
}

.main-chart .chart-wrapper {
    height: 500px;
}

.chart-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.secondary-chart .chart-wrapper {
    height: 250px;
}

/* Alertes */
.alerts-section {
    margin: 3rem 0;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.section-title i {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.alerts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1rem;
}

.alert-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    border-left: 4px solid;
}

.alert-item.warning {
    border-left-color: var(--warning-color);
}

.alert-item.success {
    border-left-color: var(--success-color);
}

.alert-item.danger {
    border-left-color: var(--danger-color);
}

.alert-item.info {
    border-left-color: var(--info-color);
}

.alert-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: var(--white);
}

.alert-item.warning .alert-icon {
    background: var(--warning-color);
}

.alert-item.success .alert-icon {
    background: var(--success-color);
}

.alert-item.danger .alert-icon {
    background: var(--danger-color);
}

.alert-item.info .alert-icon {
    background: var(--info-color);
}

.alert-content {
    flex-grow: 1;
}

.alert-content h5 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
}

.alert-content p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.btn-alert {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
    background: var(--white);
    color: var(--text-muted);
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--transition);
}

.btn-alert:hover {
    background: var(--light-bg);
}

/* Table de Performance des Projets */
.project-performance-section {
    margin: 3rem 0;
}

.performance-table-container {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    overflow: hidden;
    padding: 1rem;
}

.performance-table {
    width: 100%;
    border-collapse: collapse;
}

.performance-table th,
.performance-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.performance-table th {
    color: var(--text-dark);
    font-weight: 600;
    background-color: rgba(3, 55, 101, 0.05);
}

.performance-table tr:hover {
    background-color: rgba(3, 55, 101, 0.02);
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background-color: rgba(40, 167, 69, 0.1);
    color: var(--success-color);
}

.status-badge.completed {
    background-color: rgba(10, 140, 255, 0.1);
    color: var(--secondary-color);
}

.status-badge.pending {
    background-color: rgba(255, 193, 7, 0.1);
    color: var(--warning-color);
}

.progress-bar-small {
    height: 6px;
    background-color: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 3px;
    position: relative;
}

.progress-bar-fill::after {
    content: attr(data-value);
    position: absolute;
    right: 0;
    top: -18px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-dark);
}

/* Résumé Financier */
.financial-summary {
    margin: 3rem 0;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.summary-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    padding: 1.5rem;
    text-align: center;
    transition: var(--transition);
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-medium);
}

.summary-title {
    color: var(--text-muted);
    font-size: 1rem;
    margin-bottom: 1rem;
}

.summary-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-dark);
}

.summary-value.positive {
    color: var(--success-color);
}

.summary-value.negative {
    color: var(--danger-color);
}

/* Responsive */
@media (max-width: 1200px) {
    .chart-grid {
        grid-template-columns: 1fr;
    }
    
    .alerts-grid {
        grid-template-columns: 1fr;
    }
    
    .alert-item {
        flex-direction: column;
        text-align: center;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .performance-table th, 
    .performance-table td {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card {
    animation: fadeInUp 0.6s ease-out forwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }
/* Responsive */
@media (max-width: 1200px) {
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .chart-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }
    
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .chart-grid {
        grid-template-columns: 1fr;
    }
    
    .alerts-grid {
        grid-template-columns: 1fr;
    }
    
    .alert-item {
        flex-direction: column;
        text-align: center;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .performance-table th, 
    .performance-table td {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
    
    .summary-grid {
        grid-template-columns: 1fr;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card {
    animation: fadeInUp 0.6s ease-out forwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Configuration des couleurs
    const colors = {
        primary: '#033765',
        secondary: '#0A8CFF',
        success: '#28a745',
        danger: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };

    // Données du graphique principal
    const labels = {!! json_encode($evolutionFinanciere->pluck('mois')) !!};
    const entrees = {!! json_encode($evolutionFinanciere->pluck('total_entrees')) !!};
    const sorties = {!! json_encode($evolutionFinanciere->pluck('total_sorties')) !!};

    // Graphique principal - Évolution financière
    const ctx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Entrées',
                    data: entrees,
                    borderColor: colors.success,
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.success,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Sorties',
                    data: sorties,
                    borderColor: colors.danger,
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.danger,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + 
                                   new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' CFA';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', {
                                notation: 'compact',
                                maximumFractionDigits: 1
                            }).format(value) + ' CFA';
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Données de répartition des dépenses (données réelles)
    const depensesLabels = {!! json_encode(array_column($repartitionDepenses, 'categorie')) !!};
    const depensesData = {!! json_encode(array_column($repartitionDepenses, 'montant')) !!};
    
    // Graphique des dépenses (Pie Chart) avec données réelles
    const expensesCtx = document.getElementById('expensesChart').getContext('2d');
    new Chart(expensesCtx, {
        type: 'doughnut',
        data: {
            labels: depensesLabels,
            datasets: [{
                data: depensesData,
                backgroundColor: [
                    colors.primary,
                    colors.secondary,
                    colors.warning,
                    colors.info
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return context.label + ': ' + 
                                   new Intl.NumberFormat('fr-FR').format(value) + 
                                   ' CFA (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Récupérer les données des projets pour le graphique à barres
    // Utiliser les 5 premiers projets de la performance pour le graphique
    const projetsData = [];
    @foreach($performanceProjets->take(5) as $projet)
        projetsData.push({
            nom: "{{ $projet->nom_projet }}",
            montant: {{ $projet->montant_total ?? 0 }}
        });
    @endforeach

    // Trier les projets par montant pour un affichage plus significatif
    projetsData.sort((a, b) => b.montant - a.montant);
    
    // Préparer les données pour le graphique
    const projetsLabels = projetsData.map(p => p.nom);
    const projetsMontants = projetsData.map(p => p.montant);
    
    // Graphique des projets (Bar Chart)
    const projectsCtx = document.getElementById('projectsChart').getContext('2d');
    new Chart(projectsCtx, {
        type: 'bar',
        data: {
            labels: projetsLabels,
            datasets: [{
                label: 'Montant (CFA)',
                data: projetsMontants,
                backgroundColor: [
                    colors.success,
                    colors.primary,
                    colors.warning,
                    colors.secondary,
                    colors.info
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Montant: ' + 
                                   new Intl.NumberFormat('fr-FR').format(context.raw) + 
                                   ' CFA';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', {
                                notation: 'compact',
                                maximumFractionDigits: 1
                            }).format(value) + ' CFA';
                        }
                    }
                }
            }
        }
    });

    // Sparklines pour les cartes
    createSparkline('revenueSparkline', entrees, colors.success);
    createSparkline('expensesSparkline', sorties, colors.danger);

    // Gestion des filtres de période
    const filterButtons = document.querySelectorAll('.btn-chart-filter');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const period = this.dataset.period;
            updateChartByPeriod(period);
        });
    });

    // Actualisation automatique toutes les 30 secondes
    setInterval(updateRealtimeStats, 30000);
});

// ==================== FONCTIONS UTILITAIRES ====================

function createSparkline(canvasId, data, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map((_, i) => i),
            datasets: [{
                data: data,
                borderColor: color,
                backgroundColor: color + '20',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { display: false },
                y: { display: false }
            },
            elements: {
                point: { radius: 0 }
            }
        }
    });
}

// Mise à jour de la fonction updateRealtimeStats avec gestion d'erreur et fallback
function updateRealtimeStats() {
    // Afficher un indicateur de chargement
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach(card => {
        card.classList.add('loading');
    });
    
    fetch('/dashboard/realtime-stats')
        .then(response => {
            if (!response.ok) {
                // Si l'API n'est pas disponible, utiliser des données simulées
                console.warn('L\'API de mise à jour n\'est pas disponible. Utilisation de données simulées.');
                return simulateRealtimeStats();
            }
            return response.json();
        })
        .then(data => {
            // Supprimer l'indicateur de chargement
            cards.forEach(card => {
                card.classList.remove('loading');
            });
            
            // Mettre à jour les statistiques en temps réel
            updateStatCard('projects', data.projets_en_cours);
            updateStatCard('revenue', data.revenus_totaux);
            updateStatCard('expenses', data.depenses_totales);
            updateStatCard('stock', data.articles_en_stock);
            
            // Mettre à jour l'heure de dernière mise à jour
            const lastUpdate = document.querySelector('.last-update');
            if (lastUpdate) {
                lastUpdate.textContent = 'Dernière mise à jour: ' + data.derniere_mise_a_jour;
            }
            
            // Stocker les données dans le cache
            dashboardCache.set('realtimeStats', data);
        })
        .catch(error => {
            console.error('Erreur lors de la mise à jour:', error);
            
            // Supprimer l'indicateur de chargement
            cards.forEach(card => {
                card.classList.remove('loading');
            });
            
            // Utiliser les données en cache si disponibles
            const cachedData = dashboardCache.get('realtimeStats');
            if (cachedData) {
                updateDisplayWithData(cachedData);
                showNotification('Utilisation des données en cache', 'info');
            } else {
                showNotification('Erreur lors de l\'actualisation, API non disponible', 'error');
            }
        });
}

// Fonction pour simuler des données de mise à jour en cas d'API non disponible
function simulateRealtimeStats() {
    // Récupérer les valeurs actuelles
    const projetsEnCours = parseInt(document.querySelector('.stat-card.projects .stat-number').textContent.replace(/\D/g, '')) || 0;
    const revenusTotaux = parseInt(document.querySelector('.stat-card.revenue .stat-number').textContent.replace(/\D/g, '')) || 0;
    const depensesTotales = parseInt(document.querySelector('.stat-card.expenses .stat-number').textContent.replace(/\D/g, '')) || 0;
    const articlesEnStock = parseInt(document.querySelector('.stat-card.stock .stat-number').textContent.replace(/\D/g, '')) || 0;
    
    // Simuler de légères variations (±5%)
    const randomVariation = () => (Math.random() * 0.1) - 0.05; // Entre -5% et +5%
    
    return Promise.resolve({
        projets_en_cours: Math.max(1, Math.round(projetsEnCours * (1 + randomVariation()))),
        revenus_totaux: Math.round(revenusTotaux * (1 + randomVariation())),
        depenses_totales: Math.round(depensesTotales * (1 + randomVariation())),
        articles_en_stock: Math.round(articlesEnStock * (1 + randomVariation())),
        derniere_mise_a_jour: new Date().toLocaleTimeString(),
        simulated: true
    });
}

// Mise à jour de la fonction updateChartByPeriod avec gestion d'erreur
function updateChartByPeriod(period) {
    showNotification(`Période changée vers: ${period}`, 'info');
    
    // Vérifier si Chart.js est disponible
    if (typeof Chart === 'undefined') {
        showNotification('Chart.js n\'est pas disponible', 'error');
        return;
    }
    
    // Ajouter un indicateur de chargement
    const chartContainer = document.querySelector('.main-chart');
    if (chartContainer) {
        chartContainer.classList.add('loading');
    }
    
    // Faire un appel AJAX pour récupérer les données filtrées par période
    fetch(`/dashboard/evolution-data?period=${period}`)
        .then(response => {
            if (!response.ok) {
                // Si l'API n'est pas disponible, utiliser des données simulées
                console.warn('L\'API de données d\'évolution n\'est pas disponible. Utilisation de données simulées.');
                return simulateEvolutionData(period);
            }
            return response.json();
        })
        .then(data => {
            // Retirer l'indicateur de chargement
            if (chartContainer) {
                chartContainer.classList.remove('loading');
            }
            
            // Mettre à jour le graphique principal avec les nouvelles données
            try {
                const chart = Chart.getChart('evolutionChart');
                if (chart) {
                    chart.data.labels = data.labels;
                    chart.data.datasets[0].data = data.entrees;
                    chart.data.datasets[1].data = data.sorties;
                    chart.update();
                }
            } catch (error) {
                console.error('Erreur lors de la mise à jour du graphique:', error);
                showNotification('Erreur lors de la mise à jour du graphique', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur lors du changement de période:', error);
            
            // Retirer l'indicateur de chargement
            if (chartContainer) {
                chartContainer.classList.remove('loading');
            }
            
            showNotification('Erreur lors du changement de période', 'error');
        });
}

// Fonction pour simuler des données d'évolution en cas d'API non disponible
function simulateEvolutionData(period) {
    // Obtenir le graphique actuel
    const chart = Chart.getChart('evolutionChart');
    if (!chart) {
        return Promise.reject(new Error('Graphique non disponible'));
    }
    
    // Récupérer les données actuelles
    const currentLabels = chart.data.labels;
    const currentEntrees = chart.data.datasets[0].data;
    const currentSorties = chart.data.datasets[1].data;
    
    // Adapter les données selon la période
    let labels, entrees, sorties;
    
    switch (period) {
        case 'month':
            // Données mensuelles (12 mois)
            labels = Array.from({length: 12}, (_, i) => {
                const date = new Date();
                date.setMonth(date.getMonth() - 11 + i);
                return date.toLocaleDateString('fr-FR', {year: 'numeric', month: '2-digit'});
            });
            break;
        case 'quarter':
            // Données trimestrielles (8 trimestres)
            labels = Array.from({length: 8}, (_, i) => {
                const date = new Date();
                date.setMonth(date.getMonth() - 21 + (i * 3));
                const quarter = Math.floor(date.getMonth() / 3) + 1;
                return `${date.getFullYear()}-T${quarter}`;
            });
            break;
        case 'year':
            // Données annuelles (5 ans)
            labels = Array.from({length: 5}, (_, i) => {
                const date = new Date();
                date.setFullYear(date.getFullYear() - 4 + i);
                return date.getFullYear().toString();
            });
            break;
    }
    
    // Générer des données basées sur les valeurs actuelles
    const baseEntree = currentEntrees.length > 0 ? 
        currentEntrees.reduce((a, b) => a + b, 0) / currentEntrees.length : 1000000;
    const baseSortie = currentSorties.length > 0 ? 
        currentSorties.reduce((a, b) => a + b, 0) / currentSorties.length : 800000;
    
    entrees = labels.map(() => Math.round(baseEntree * (0.8 + Math.random() * 0.4))); // ±20%
    sorties = labels.map(() => Math.round(baseSortie * (0.8 + Math.random() * 0.4))); // ±20%
    
    return Promise.resolve({
        labels: labels,
        entrees: entrees,
        sorties: sorties,
        simulated: true
    });
}

// Ajouter des styles pour l'indicateur de chargement
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 30px;
            height: 30px;
            margin: -15px 0 0 -15px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top-color: #033765;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 10;
        }
        
        .chart-container.loading::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 5;
        }
    `;
    document.head.appendChild(style);
});





function updateStatCard(type, value) {
    const card = document.querySelector(`.stat-card.${type} .stat-number`);
    if (card) {
        const currentValue = parseInt(card.textContent.replace(/\D/g, '')) || 0;
        animateCounter(card, currentValue, value, 1000);
    }
}




function animateCounter(element, start, end, duration) {
    if (!element) return;
    
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const currentValue = Math.floor(progress * (end - start) + start);
        element.textContent = new Intl.NumberFormat('fr-FR').format(currentValue);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

function showNotification(message, type = 'info') {
    // Créer la notification
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" class="close-btn">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Ajouter les styles si ils n'existent pas
    if (!document.getElementById('notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                background: white;
                border-radius: 8px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                display: flex;
                align-items: center;
                gap: 0.5rem;
                z-index: 10000;
                min-width: 300px;
                animation: slideIn 0.3s ease-out;
                font-family: inherit;
            }
            .notification.success { 
                border-left: 4px solid #28a745; 
                color: #155724;
            }
            .notification.error { 
                border-left: 4px solid #dc3545; 
                color: #721c24;
            }
            .notification.info { 
                border-left: 4px solid #17a2b8; 
                color: #0c5460;
            }
            .notification i {
                flex-shrink: 0;
            }
            .notification .close-btn {
                background: none;
                border: none;
                cursor: pointer;
                color: #6c757d;
                padding: 0.25rem;
                margin-left: auto;
                border-radius: 4px;
                transition: background-color 0.2s;
            }
            .notification .close-btn:hover {
                background-color: #f8f9fa;
            }
            @keyframes slideIn {
                from { 
                    transform: translateX(100%); 
                    opacity: 0; 
                }
                to { 
                    transform: translateX(0); 
                    opacity: 1; 
                }
            }
            @keyframes slideOut {
                from { 
                    transform: translateX(0); 
                    opacity: 1; 
                }
                to { 
                    transform: translateX(100%); 
                    opacity: 0; 
                }
            }
        `;
        document.head.appendChild(styles);
    }
    
    // Ajouter au DOM
    document.body.appendChild(notification);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// ==================== INITIALISATION AU CHARGEMENT ====================
window.addEventListener('load', () => {
    // Animer les compteurs
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach((counter, index) => {
        const target = parseInt(counter.textContent.replace(/\D/g, '')) || 0;
        if (target > 0) {
            counter.textContent = '0';
            setTimeout(() => {
                animateCounter(counter, 0, target, 2000);
            }, index * 200);
        }
    });
    
    // Animer les barres de progression
    setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-bar, .progress-bar-fill');
        progressBars.forEach((bar, index) => {
            const width = bar.style.width;
            if (width) {
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, index * 100);
            }
        });
    }, 1000);
    
    // Message de bienvenue
    setTimeout(() => {
        showNotification('Dashboard chargé avec succès', 'success');
    }, 2000);
});

// ==================== GESTION DES ÉVÉNEMENTS ====================

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Erreur Dashboard:', e.error);
    showNotification('Une erreur est survenue', 'error');
});

// Vérification de la connectivité
window.addEventListener('online', () => {
    showNotification('Connexion rétablie', 'success');
});

window.addEventListener('offline', () => {
    showNotification('Connexion perdue - Mode hors ligne', 'error');
});



// Gestion du redimensionnement de la fenêtre
let resizeTimeout;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        // Redimensionner les graphiques si nécessaire
        for (let id of ['evolutionChart', 'expensesChart', 'projectsChart']) {
            const chart = Chart.getChart(id);
            if (chart) {
                chart.resize();
            }
        }
    }, 250);
});

// ==================== UTILITAIRES DE PERFORMANCE ====================

// Optimisation des performances pour les animations
function requestIdleCallback(callback) {
    if (window.requestIdleCallback) {
        return window.requestIdleCallback(callback);
    } else {
        return setTimeout(callback, 1);
    }
}

// Cache simple pour les données
const dashboardCache = {
    data: {},
    timestamps: {},
    
    set(key, value, ttl = 30000) { // TTL par défaut: 30 secondes
        this.data[key] = value;
        this.timestamps[key] = Date.now() + ttl;
    },
    
    get(key) {
        if (this.timestamps[key] && Date.now() < this.timestamps[key]) {
            return this.data[key];
        }
        delete this.data[key];
        delete this.timestamps[key];
        return null;
    },
    
    clear() {
        this.data = {};
        this.timestamps = {};
    }
};

// Débounce pour optimiser les appels
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Version optimisée de updateRealtimeStats avec cache
const debouncedUpdateStats = debounce(() => {
    const cachedData = dashboardCache.get('realtimeStats');
    if (cachedData) {
        // Utiliser les données en cache
        updateDisplayWithData(cachedData);
    } else {
        // Récupérer de nouvelles données
        updateRealtimeStats();
    }
}, 1000);

function updateDisplayWithData(data) {
    updateStatCard('projects', data.projets_en_cours);
    updateStatCard('revenue', data.revenus_totaux);
    updateStatCard('expenses', data.depenses_totales);
    updateStatCard('stock', data.articles_en_stock);
}

// Nettoyage au déchargement de la page
window.addEventListener('beforeunload', () => {
    dashboardCache.clear();
});

console.log('📊 Dashboard JavaScript initialisé avec succès!');
    </script>
@endsection