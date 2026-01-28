
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<!-- Chrome Style Contract Navigation -->
<div class="chrome-nav-section">
    <div class="chrome-tab-bar">
        <div class="chrome-tabs-container">
            <div class="chrome-tab {{ request()->routeIs('contrats.show') ? 'active' : '' }}">
                <i class="fas fa-info-circle"></i>
                <span>Détails</span>
                <a href="{{ route('contrats.show', session('contrat_id')) }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('bpu.*') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i>
                <span>BPU</span>
                <a href="{{ route('bpu.index') }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('dqe.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>DQE</span>
                <a href="{{ route('dqe.index') }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('contrats.debourse-sec.*') ? 'active' : '' }}">
                <i class="fas fa-hammer"></i>
                <span>Déboursé Sec</span>
                <a href="{{ route('contrats.debourse-sec.index', session('contrat_id')) }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('contrats.frais-chantier.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i>
                <span>Frais de chantier</span>
                <a href="{{ route('contrats.frais-chantier.index', session('contrat_id')) }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('contrats.debourse-chantier.*') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i>
                <span>Déboursé chantier</span>
                <a href="{{ route('contrats.debourse-chantier.index', session('contrat_id')) }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('contrats.frais-generaux.*') ? 'active' : '' }}">
                <i class="fas fa-hard-hat"></i>
                <span>Frais généraux</span>
                <a href="{{ route('contrats.frais-generaux.index', session('contrat_id')) }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('contrats.benefice.*') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i>
                <span>Bénéfice</span>
                <a href="{{ route('contrats.benefice.index', session('contrat_id')) }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('stock_contrat.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                <span>Stock</span>
                <a href="{{ route('stock_contrat.index') }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('demandes-ravitaillement.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i>
                <span>Ravitaillement</span>
                <a href="{{ route('demandes-ravitaillement.index') }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('facture-contrat.*') ? 'active' : '' }}">
                <i class="fas fa-file-contract"></i>
                <span>Facture Contrat</span>
                <a href="{{ route('facture-contrat.index') }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('document_contrat.*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i>
                <span>Documents</span>
                <a href="{{ route('document_contrat.index') }}"></a>
            </div>
            <div class="chrome-tab {{ request()->routeIs('prestations.*') ? 'active' : '' }}">
                <i class="fas fa-hammer"></i>
                <span>Artisan</span>
                <a href="{{ route('prestations.index') }}"></a>
            </div>
        </div>
        <div class="chrome-contract-info">
            <span class="contract-name">{{ session('contrat_nom') }}</span>
            <span class="contract-ref">{{ session('ref_contrat') }}</span>
            <span class="status-indicator active"></span>
        </div>
    </div>
</div>

<style>
/* Chrome Style Navigation */
.chrome-nav-section {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(10px);
}

.chrome-tab-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 1rem;
    max-width: 100%;
    margin: 0 auto;
}

.chrome-tabs-container {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    overflow-x: auto;
    scrollbar-width: thin;
    padding: 0.25rem 0;
}

.chrome-tabs-container::-webkit-scrollbar {
    height: 4px;
}

.chrome-tabs-container::-webkit-scrollbar-track {
    background: transparent;
}

.chrome-tabs-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.chrome-tab {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0;
    border-bottom: none;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: max-content;
    font-size: 0.85rem;
    color: #495057;
    text-decoration: none;
    white-space: nowrap;
}

.chrome-tab:hover {
    background: #dee2e6;
    transform: translateY(-1px);
}

.chrome-tab.active {
    background: #ffffff;
    color: #033d71;
    font-weight: 600;
    box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
    border-color: #033d71;
    border-bottom: 2px solid #ffffff;
    margin-bottom: -1px;
}

.chrome-tab i {
    font-size: 0.9rem;
    opacity: 0.8;
}

.chrome-tab.active i {
    opacity: 1;
    color: #033d71;
}

.chrome-tab a {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    text-decoration: none;
    color: inherit;
}

.chrome-contract-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 0.8rem;
    margin-left: 1rem;
    white-space: nowrap;
}

.chrome-contract-info .contract-name {
    font-weight: 600;
    color: #212529;
}

.chrome-contract-info .contract-ref {
    color: #6c757d;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28a745;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

@media (max-width: 768px) {
    .chrome-tab-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .chrome-tabs-container {
        width: 100%;
        overflow-x: auto;
    }
    
    .chrome-tab {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }
    
    .chrome-contract-info {
        margin-left: 0;
        margin-top: 0.5rem;
        width: 100%;
        justify-content: space-between;
    }
}
</style>
