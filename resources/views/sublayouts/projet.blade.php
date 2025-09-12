@extends('layouts.app')
@section('content')

<div class="dashboard-section projects">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-project-diagram"></i>
            Gestion des Projets
        </h2>
    </div>
    
    <div class="dashboard-grid">
        <a href="{{ route('projets.index') }}" class="dashboard-card primary">
            <div class="card-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="card-content">
                <h3>Liste des Projets</h3>
                <p>Consulter tous les projets actifs</p>
            </div>
        </a>
        
        <a href="{{ route('projets.create') }}" class="dashboard-card success">
            <div class="card-icon">
                <i class="fas fa-plus-square"></i>
            </div>
            <div class="card-content">
                <h3>Nouveau Projet</h3>
                <p>Créer un nouveau projet</p>
            </div>
        </a>
        
        <a href="{{ route('contrats.all') }}" class="dashboard-card contracts">
            <div class="card-icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="card-content">
                <h3>Liste des Contrats</h3>
                <p>Consulter tous les contrats</p>
            </div>
        </a>
        
        <button type="button" class="dashboard-card warning" data-bs-toggle="modal" data-bs-target="#selectProjectModal" style="border: none; cursor: pointer;">
            <div class="card-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="card-content">
                <h3>Nouveau Contrat</h3>
                <p>Créer un nouveau contrat</p>
            </div>
        </button>
    </div>
</div>

<style>
 /* Encapsulez vos styles spécifiques dans des classes conteneurs */
/* Au lieu de :root, utilisez un préfixe pour vos variables CSS */

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
    --gradient-contracts: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%);
    --gradient-card: linear-gradient(135deg, var(--white, #ffffff) 0%, #f8f9ff 100%);
    --shadow-card: var(--shadow-md, 0 0.5rem 1rem rgba(0, 0, 0, 0.15));
    --shadow-hover: var(--shadow-lg, 0 1rem 3rem rgba(0, 0, 0, 0.175));
    --border-radius: var(--border-radius-lg, 1rem);
    --transition: var(--transition-base, all 0.2s ease-in-out);
}

/* Section Gestion des Projets - Préfixez les classes pour éviter les conflits */
.dashboard-section.projects {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 1400px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section.projects::before {
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
    position: relative;
    z-index: 1;
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
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    position: relative;
    z-index: 1;
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
    background: linear-gradient(135deg, var(--info-color) 0%, #20c997 100%);
}

.dashboard-card.secondary::before {
    background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
}

.dashboard-card.contracts::before {
    background: var(--gradient-contracts);
}

.dashboard-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--shadow-hover);
    color: white;
}

.dashboard-card:hover::before {
    left: 0;
}

.card-icon {
    font-size: 3.5rem;
    margin-bottom: 1.5rem;
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

.dashboard-card.contracts .card-icon {
    color: #4CAF50;
}

.dashboard-card:hover .card-icon {
    color: white;
    transform: scale(1.15) rotateY(360deg);
}

.card-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
}

.card-content p {
    opacity: 0.8;
    margin-bottom: 1.5rem;
}

.project-card-stats, 
.project-card-action {
    margin-top: auto;
}

.project-card-stats {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem;
    background: rgba(10, 140, 255, 0.1);
    border-radius: 12px;
    transition: var(--project-transition);
}

.project-stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--project-primary-color);
}

.project-stat-label {
    font-size: 0.9rem;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Projet Actuel */
.project-current-wrapper {
    margin: 2rem auto;
    max-width: 1200px;
    padding: 0 1rem;
}

.project-header-card {
    background: var(--project-gradient-card);
    border-radius: var(--project-border-radius);
    padding: 2rem;
    box-shadow: var(--project-shadow-card);
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.project-header-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--project-gradient-primary);
}

.project-info-wrapper {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.project-icon-wrapper {
    background: var(--project-gradient-primary);
    color: white;
    padding: 1rem;
    border-radius: 50%;
    font-size: 1.5rem;
}

.project-title-text {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--project-primary-color);
    margin: 0 0 0.5rem 0;
}

.project-name-text {
    font-size: 1.2rem;
    color: var(--project-secondary-color);
    font-weight: 600;
}

.project-status-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.project-status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    animation: project-pulse 2s infinite;
}

@keyframes project-pulse {
    0% { transform: scale(0.95); opacity: 0.7; }
    50% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.7; }
}

.project-status-indicator.active {
    background: var(--project-success-color);
}

.project-status-text {
    font-weight: 600;
    color: var(--project-success-color);
}

.project-navigation-wrapper {
    background: white;
    border-radius: var(--project-border-radius);
    padding: 2rem;
    box-shadow: var(--project-shadow-card);
}

.project-nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.project-nav-card {
    background: var(--project-gradient-card);
    border-radius: 12px;
    padding: 1.5rem;
    text-decoration: none;
    color: var(--project-primary-color);
    transition: var(--project-transition);
    text-align: center;
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}

.project-nav-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    transition: var(--project-transition);
    z-index: -1;
}

.project-nav-card.detail::before { background: linear-gradient(135deg, #2196F3 0%, #21CBF3 100%); }
.project-nav-card.contracts::before { background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%); }
.project-nav-card.documents::before { background: linear-gradient(135deg, #FF9800 0%, #FFC107 100%); }
.project-nav-card.stock::before { background: linear-gradient(135deg, #9C27B0 0%, #BA68C8 100%); }
.project-nav-card.transfers::before { background: linear-gradient(135deg, #E91E63 0%, #F06292 100%); }

.project-nav-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--project-shadow-hover);
    color: white;
    border-color: rgba(255,255,255,0.3);
}

.project-nav-card:hover::before {
    left: 0;
}

.project-nav-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    transition: var(--project-transition);
}

.project-nav-card:hover .project-nav-icon {
    transform: scale(1.1);
}

.project-nav-card h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.project-nav-card p {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0;
}

/* Modal Transfert */
.project-modern-modal {
    border: none;
    border-radius: var(--project-border-radius);
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.project-modern-header {
    background: var(--project-gradient-primary);
    color: white;
    border: none;
    padding: 1.5rem 2rem;
}

.project-modern-header h5 {
    margin: 0;
    font-weight: 600;
}

.project-modern-close {
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.project-modern-body {
    padding: 2rem;
}

.project-transfer-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.project-form-group {
    margin-bottom: 1.5rem;
}

.project-form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--project-primary-color);
}

.project-form-group label i {
    margin-right: 0.5rem;
    color: var(--project-secondary-color);
}

.project-modern-select, 
.project-modern-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--project-transition);
    background: #f8f9fa;
}

.project-modern-select:focus, 
.project-modern-input:focus {
    outline: none;
    border-color: var(--project-secondary-color);
    background: white;
    box-shadow: 0 0 0 3px rgba(10, 140, 255, 0.1);
}

.project-form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.project-modern-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: var(--project-transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .project-dashboard-section {
        padding: 2rem 1rem;
        margin: 1rem;
    }
    
    .project-dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .project-header-card {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .project-nav-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    
    .project-transfer-form .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Modal de sélection de projet pour créer un contrat -->
<div class="modal fade" id="selectProjectModal" tabindex="-1" aria-labelledby="selectProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content project-modern-modal">
            <div class="modal-header project-modern-header">
                <h5 class="modal-title" id="selectProjectModalLabel">
                    <i class="fas fa-project-diagram"></i>
                    Sélectionner un projet pour le contrat
                </h5>
                <button type="button" class="btn-close project-modern-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body project-modern-body">
                <form action="{{ route('projets.select-for-contract') }}" method="POST" id="selectProjectForm">
                    @csrf
                    <div class="project-form-group">
                        <label for="projet_id">
                            <i class="fas fa-folder-open"></i>
                            Choisir le projet :
                        </label>
                        <select name="projet_id" id="projet_id" class="project-modern-select" required>
                            <option value="">-- Sélectionner un projet --</option>
                            @php
                                $projets = \App\Models\Projet::all();
                            @endphp
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}">
                                    {{ $projet->ref_projet }} - {{ $projet->nom_projet }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="project-form-actions">
                        <button type="button" class="btn btn-secondary project-modern-btn" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary project-modern-btn">
                            <i class="fas fa-check"></i>
                            Continuer vers la création
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection