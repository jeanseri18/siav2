
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Section Contrat - CSS Isolé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<!-- Section Contrat Header -->
<div class="contract-section">
    <div class="contract-header">
        <div class="contract-info-card">
            <div class="contract-icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="contract-details">
    <h2 class="contract-title">Contrat</h2>
    <div class="contract-meta">
        <span class="contract-name">{{ session('contrat_nom') }}</span>
        <span class="contract-ref">{{ session('ref_contrat') }}</span>
    </div>
</div>
            <div class="contract-status">
                <span class="status-badge active">Actif</span>
            </div>
        </div>
    </div>

    <!-- Navigation des liens contrat -->
    <div class="contract-navigation">
        <div class="nav-container">
            <a href="{{ route('contrats.show', session('contrat_id')) }}" class="nav-item">
                <i class="fas fa-info-circle"></i>
                <span>Détails</span>
            </a>
            <a href="{{ route('bpu.index') }}" class="nav-item">
                <i class="fas fa-calculator"></i>
                <span>BPU</span>
            </a>
<a href="{{ route('dqe.index') }}" class="nav-item">
    <i class="fas fa-chart-bar"></i>
    <span>DQE</span>
</a>

<a href="{{ route('debourses.sec') }}" class="nav-item">
    <i class="fas fa-hammer"></i>
    <span>Déboursé sec</span>
</a>
<a href="{{ route('debourses.main_oeuvre') }}" class="nav-item">
    <i class="fas fa-users"></i>
    <span>Déboursé main d'œuvre</span>
</a>
<a href="{{ route('debourses.frais_chantier') }}" class="nav-item">
    <i class="fas fa-percentage"></i>
    <span>Frais de chantier</span>
</a>
<a href="{{ route('debourses_chantier.index', session('contrat_id')) }}" class="nav-item">
    <i class="fas fa-hard-hat"></i>
    <span>Déboursé chantier</span>
</a>
<a href="{{ route('frais_generaux.index') }}" class="nav-item">
    <i class="fas fa-calculator"></i>
    <span>Frais généraux</span>
</a>
<a href="{{ route('stock_contrat.index') }}" class="nav-item">
    <i class="fas fa-boxes"></i>
    <span>Stock</span>
</a>
            <a href="#" class="nav-item">
                <i class="fas fa-truck"></i>
                <span>Demande de Ravitaillement</span>
            </a>
            <a href="{{ route('factures.index') }}" class="nav-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Facturation</span>
            </a>

            <a href="{{ route('document_contrat.index') }}" class="nav-item">
                <i class="fas fa-folder"></i>
                <span>Documents</span>
            </a>
            <a href="{{ route('prestations.index') }}" class="nav-item">
                <i class="fas fa-hammer"></i>
                <span>Artisan</span>
            </a>
        </div>
    </div>

    <hr class="divider"/>
</div>

<style>
/* SOLUTION 1: Encapsulation avec une classe conteneur */
.contract-section {
    /* Variables harmonisées avec app.blade.php */
    --contract-primary-color: var(--primary, #033765);
    --contract-secondary-color: var(--primary-light, #0A8CFF);
    --contract-accent-color: var(--white, #ffffff);
    --contract-success-color: var(--success, #28a745);
    --contract-gradient-primary: linear-gradient(135deg, var(--primary, #033765) 0%, var(--primary-light, #0A8CFF) 100%);
    --contract-gradient-card: linear-gradient(135deg, var(--white, #ffffff) 0%, #f8f9ff 100%);
    --contract-shadow-card: var(--shadow-md, 0 0.5rem 1rem rgba(0, 0, 0, 0.15));
    --contract-shadow-hover: var(--shadow-lg, 0 1rem 3rem rgba(0, 0, 0, 0.175));
    --contract-border-radius: var(--border-radius-lg, 1rem);
    --contract-transition: var(--transition-base, all 0.2s ease-in-out);
}

.contract-section .contract-header {
    margin: 2rem auto;
    max-width:2600px;
    padding: 0 1rem;
}

.contract-section .contract-info-card {
    background: var(--contract-gradient-primary);
    border-radius: var(--contract-border-radius);
    padding: 2rem;
    box-shadow: var(--contract-shadow-card);
    display: flex;
    align-items: center;
    gap: 2rem;
    position: relative;
    overflow: hidden;
    color: white;
}

.contract-section .contract-info-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.contract-section .contract-icon {
    background: rgba(255,255,255,0.15);
    padding: 1.5rem;
    border-radius: 50%;
    backdrop-filter: blur(10px);
}

.contract-section .contract-icon i {
    font-size: 2.5rem;
    color: white;
}

.contract-section .contract-details {
    flex-grow: 1;
}

.contract-section .contract-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.contract-section .contract-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.contract-section .contract-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: rgba(255,255,255,0.95);
}

.contract-section .contract-ref {
    font-size: 1rem;
    color: rgba(255,255,255,0.8);
    font-family: 'Courier New', monospace;
    background: rgba(255,255,255,0.1);
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    display: inline-block;
}

.contract-section .contract-status {
    align-self: flex-start;
}

.contract-section .status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.contract-section .status-badge.active {
    background: var(--contract-success-color);
    color: white;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.3);
    animation: contract-pulse 2s infinite;
}

@keyframes contract-pulse {
    0%, 100% { box-shadow: 0 0 20px rgba(40, 167, 69, 0.3); }
    50% { box-shadow: 0 0 30px rgba(40, 167, 69, 0.5); }
}

.contract-section .contract-navigation {
    margin: 1rem auto 2rem;
    max-width: 2600px;
    padding: 0 1rem;
}

.contract-section .nav-container {
    background: white;
    border-radius: var(--contract-border-radius);
    padding: var(--spacing-lg, 1.5rem);
    box-shadow: var(--contract-shadow-card);
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-md, 1rem);
    justify-content: center;
}

.contract-section .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--spacing-sm, 0.5rem);
    padding: var(--spacing-md, 1rem) var(--spacing-lg, 1.5rem);
    background: var(--contract-gradient-card);
    border: 2px solid transparent;
    border-radius: 12px;
    text-decoration: none;
    color: var(--contract-primary-color);
    font-weight: 600;
    font-size: 0.9rem;
    transition: var(--contract-transition);
    position: relative;
    overflow: hidden;
    min-width: 120px;
    max-width: 160px;
    text-align: center;
}

.contract-section .nav-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--contract-gradient-primary);
    transition: var(--contract-transition);
    z-index: -1;
}

.contract-section .nav-item:hover {
    transform: translateY(-3px);
    box-shadow: var(--contract-shadow-hover);
    color: white;
    border-color: rgba(255,255,255,0.3);
}

.contract-section .nav-item:hover::before {
    left: 0;
}

.contract-section .nav-item i {
    font-size: 1.5rem;
    color: var(--contract-secondary-color);
    transition: var(--contract-transition);
}

.contract-section .nav-item:hover i {
    color: white;
    transform: scale(1.1);
}

.contract-section .nav-item span {
    transition: var(--contract-transition);
}

/* Effet spécial pour certains liens */
.contract-section .nav-item:nth-child(odd) {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
}

.contract-section .nav-item:nth-child(1):hover::before { background: linear-gradient(135deg, #2196F3 0%, #21CBF3 100%); }
.contract-section .nav-item:nth-child(2):hover::before { background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%); }
.contract-section .nav-item:nth-child(3):hover::before { background: linear-gradient(135deg, #FF9800 0%, #FFC107 100%); }
.contract-section .nav-item:nth-child(4):hover::before { background: linear-gradient(135deg, #E91E63 0%, #F06292 100%); }
.contract-section .nav-item:nth-child(5):hover::before { background: linear-gradient(135deg, #9C27B0 0%, #BA68C8 100%); }
.contract-section .nav-item:nth-child(6):hover::before { background: linear-gradient(135deg, #607D8B 0%, #90A4AE 100%); }
.contract-section .nav-item:nth-child(7):hover::before { background: linear-gradient(135deg, #795548 0%, #A1887F 100%); }
.contract-section .nav-item:nth-child(8):hover::before { background: linear-gradient(135deg, #009688 0%, #4DB6AC 100%); }
.contract-section .nav-item:nth-child(9):hover::before { background: linear-gradient(135deg, #3F51B5 0%, #7986CB 100%); }
.contract-section .nav-item:nth-child(10):hover::before { background: linear-gradient(135deg, #FF5722 0%, #FF8A65 100%); }

/* Animation d'entrée */
.contract-section .nav-item {
    animation: contract-slideInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(30px);
}

.contract-section .nav-item:nth-child(1) { animation-delay: 0.1s; }
.contract-section .nav-item:nth-child(2) { animation-delay: 0.15s; }
.contract-section .nav-item:nth-child(3) { animation-delay: 0.2s; }
.contract-section .nav-item:nth-child(4) { animation-delay: 0.25s; }
.contract-section .nav-item:nth-child(5) { animation-delay: 0.3s; }
.contract-section .nav-item:nth-child(6) { animation-delay: 0.35s; }
.contract-section .nav-item:nth-child(7) { animation-delay: 0.4s; }
.contract-section .nav-item:nth-child(8) { animation-delay: 0.45s; }
.contract-section .nav-item:nth-child(9) { animation-delay: 0.5s; }
.contract-section .nav-item:nth-child(10) { animation-delay: 0.55s; }

@keyframes contract-slideInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .contract-section .contract-info-card {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .contract-section .contract-title {
        font-size: 1.8rem;
    }
    
    .contract-section .contract-name {
        font-size: 1.1rem;
    }
    
    .contract-section .nav-container {
        padding: 1rem;
        gap: 0.8rem;
    }
    
    .contract-section .nav-item {
        padding: 0.8rem 1rem;
        min-width: 80px;
        font-size: 0.8rem;
    }
    
    .contract-section .nav-item i {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .contract-section .nav-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.8rem;
    }
    
    .contract-section .nav-item {
        min-width: unset;
    }
    
    .contract-section .contract-meta {
        align-items: center;
    }
}

/* Ligne de séparation moderne */
.contract-section .divider {
    margin: 2rem auto;
    max-width: 1200px;
    height: 3px;
    background: linear-gradient(90deg, transparent 0%, var(--contract-primary-color) 50%, transparent 100%);
    border: none;
    border-radius: 2px;
}
</style>

</body>
</html>
