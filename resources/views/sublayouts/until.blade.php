<!-- Section Utilitaire -->
 @extends('layouts.app')
@section('content')

<div class="dashboard-section utilities">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-tools"></i>
            Utilitaire
        </h2>
    </div>
    
    <div class="dashboard-grid">
        <a href="{{ route('until') }}" class="dashboard-card import">
            <div class="card-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div class="card-content">
                <h3>Importer des Données</h3>
                <p>Importation massive de données</p>
            </div>
           
        </a>
        
        <a href="{{ route('config-global.index') }}" class="dashboard-card config">
            <div class="card-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="card-content">
                <h3>Configuration Globale</h3>
                <p>Paramètres système</p>
            </div>
            
        </a>
    </div>
</div>
<style>
:root {
    --primary-color: #033765;
    --secondary-color: #0A8CFF;
    --success-color: #28a745;
    --info-color: #17a2b8;
    --warning-color: #ffc107;
    --accent-color: #ffffff;
    --gradient-primary: linear-gradient(135deg, #033765 0%, #0A8CFF 100%);
    --gradient-success: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    --gradient-info: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
    --gradient-utilities: linear-gradient(135deg, #033765 0%, #0A8CFF 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    --shadow-card: 0 10px 30px rgba(3, 55, 101, 0.1);
    --shadow-hover: 0 20px 40px rgba(3, 55, 101, 0.2);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Styles communs pour toutes les sections */
.dashboard-section {
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 2600px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section::before {
    content: '';
    position: absolute;
    pointer-events: none;
}

/* Section Utilitaire */
.dashboard-section.utilities {
    background: var(--gradient-utilities);
}

.dashboard-section.utilities::before {
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 50%);
    animation: rotate 30s linear infinite;
}

/* Section Utilisateurs */
.dashboard-section.users {
    background: var(--gradient-primary);
}

.dashboard-section.users::before {
    bottom: 0;
    left: 0;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 60%);
    border-radius: 50%;
    transform: translate(-30%, 30%);
}

/* Section Ventes */
.dashboard-section.sales {
    background: var(--gradient-success);
}

.dashboard-section.sales::before {
    top: 0;
    right: 0;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(50%, -50%);
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
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
    transition: var(--transition);
}

.utilities .section-title i {
    background: linear-gradient(45deg, #FF6B9D, #C44569);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.users .section-title i {
    background: linear-gradient(45deg, #74b9ff, #0984e3);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.sales .section-title i {
    background: linear-gradient(45deg, #00b894, #00cec9);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.sales .dashboard-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.dashboard-card {
    background: var(--gradient-card);
    border-radius: var(--border-radius);
    padding: 2.5rem;
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
    min-height: 150px;
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
.dashboard-card.import::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.dashboard-card.config::before { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }

.dashboard-card:hover {
    transform: translateY(-8px) scale(1.02);
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
    transition: var(--transition);
}

.dashboard-card.primary .card-icon { color: var(--primary-color); }
.dashboard-card.success .card-icon { color: var(--success-color); }
.dashboard-card.info .card-icon { color: var(--info-color); }
.dashboard-card.import .card-icon { color: #667eea; }
.dashboard-card.config .card-icon { color: #f5576c; }

.dashboard-card:hover .card-icon {
    color: white;
    transform: scale(1.15) rotateY(360deg);
}

.card-content {
    flex-grow: 1;
    margin-bottom: 1.5rem;
}

.card-content h3 {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
    line-height: 1.3;
}

.card-content p {
    opacity: 0.8;
    margin: 0;
    line-height: 1.5;
}

.card-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(103, 58, 183, 0.1);
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: var(--transition);
}

.dashboard-card:hover .card-badge {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

.card-stats {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.2rem;
    background: rgba(10, 140, 255, 0.1);
    border-radius: 12px;
    transition: var(--transition);
}

.stat-number {
    font-size: 2.2rem;
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
    gap: 0.8rem;
    padding: 0.8rem 1.5rem;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 25px;
    font-weight: 600;
    transition: var(--transition);
}

.dashboard-card:hover .card-action {
    background: rgba(255,255,255,0.2);
    transform: translateX(5px);
}

.card-features {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(23, 162, 184, 0.1);
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: var(--transition);
}

.dashboard-card:hover .feature-item {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

.feature-item i {
    font-size: 0.9rem;
    color: var(--info-color);
    transition: var(--transition);
}

.dashboard-card:hover .feature-item i {
    color: white;
}

/* Animations d'entrée */
.dashboard-card {
    animation: slideInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(50px);
}

.dashboard-card:nth-child(1) { animation-delay: 0.1s; }
.dashboard-card:nth-child(2) { animation-delay: 0.2s; }
.dashboard-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Effets spéciaux pour les cartes utilitaires */
.utilities .dashboard-card::after {
    content: '';
    position: absolute;
    top: 10px;
    right: 10px;
    width: 6px;
    height: 6px;
    background: linear-gradient(45deg, #FF6B9D, #C44569);
    border-radius: 50%;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 1; }
}

/* Effets pour les cartes utilisateurs */
.users .dashboard-card {
    position: relative;
}

.users .dashboard-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gradient-primary);
    transform: scaleX(0);
    transition: var(--transition);
}

.users .dashboard-card:hover::after {
    transform: scaleX(1);
}

/* Effets pour les cartes ventes */
.sales .dashboard-card {
    background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
}

.sales .dashboard-card::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 15px;
    width: 30px;
    height: 30px;
    background: radial-gradient(circle, rgba(40, 167, 69, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 3s infinite;
}

@keyframes pulse {
    0%, 100% { 
        transform: scale(1); 
        opacity: 0.7; 
    }
    50% { 
        transform: scale(1.5); 
        opacity: 0.3; 
    }
}

/* Responsive Design */
@media (max-width: 992px) {
    .dashboard-section {
        margin: 1.5rem auto;
        padding: 2.5rem 1.5rem;
    }
    
    .section-title {
        font-size: 2.2rem;
    }
    
    .dashboard-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .dashboard-section {
        margin: 1rem;
        padding: 2rem 1rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }
    
    .dashboard-card {
        padding: 2rem;
        min-height: 250px;
    }
    
    .card-icon {
        font-size: 3.5rem;
    }
    
    .card-content h3 {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .section-title {
        font-size: 1.8rem;
        margin-bottom: 2rem;
    }
    
    .section-title i {
        display: block;
        margin-bottom: 0.5rem;
        margin-right: 0;
    }
    
    .dashboard-card {
        padding: 1.5rem;
        min-height: 220px;
    }
    
    .card-icon {
        font-size: 3rem;
    }
    
    .card-features {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .feature-item {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }
}

/* Hover effects for touch devices */
@media (hover: none) {
    .dashboard-card:active {
        transform: scale(0.98);
    }
    
    .dashboard-card:active::before {
        left: 0;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .dashboard-card {
        border: 2px solid var(--primary-color);
    }
    
    .card-badge, .card-stats, .card-action, .feature-item {
        border: 1px solid rgba(0,0,0,0.2);
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .dashboard-card,
    .card-icon,
    .card-badge,
    .card-stats,
    .card-action,
    .feature-item {
        animation: none;
        transition: none;
    }
    
    .dashboard-card:hover {
        transform: none;
    }
}

/* Dark mode support (if needed) */
@media (prefers-color-scheme: dark) {
    :root {
        --gradient-card: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    }
    
    .dashboard-card {
        color: #e2e8f0;
    }
    
    .card-content p {
        opacity: 0.9;
    }
}

/* Print styles */
@media print {
    .dashboard-section {
        box-shadow: none;
        margin: 1rem 0;
        break-inside: avoid;
    }
    
    .dashboard-card {
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .dashboard-card::before,
    .dashboard-card::after {
        display: none;
    }
}

/* Focus styles for accessibility */
.dashboard-card:focus {
    outline: 3px solid var(--secondary-color);
    outline-offset: 2px;
}

.dashboard-card:focus:not(:focus-visible) {
    outline: none;
}

/* Loading states */
.dashboard-card.loading {
    pointer-events: none;
    opacity: 0.7;
}

.dashboard-card.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    border: 2px solid var(--primary-color);
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    transform: translate(-50%, -50%);
}

@keyframes spin {
    to { transform: translate(-50%, -50%) rotate(360deg); }
}
</style>@endsection