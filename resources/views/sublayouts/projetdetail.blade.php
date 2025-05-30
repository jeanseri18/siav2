
<div class="project-theme">
    <div class="project-current-wrapper">
        <div class="project-header-card">
            <div class="project-info-wrapper">
                <div class="project-icon-wrapper">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="project-details">
                    <h2 class="project-title-text">Projet Actuel</h2>
                    <span class="project-name-text">{{ session('projet_nom') }}</span>
                </div>
            </div>
            <div class="project-status-wrapper">
                <span class="project-status-indicator active"></span>
                <span class="project-status-text">En cours</span>
            </div>
        </div>

        <div class="project-navigation-wrapper">
            <div class="project-nav-grid">
                <a href="#" class="project-nav-card detail">
                    <div class="project-nav-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h4>Détail Projet</h4>
                    <p>Informations complètes</p>
                </a>

                <a href="{{ route('contrats.index') }}" class="project-nav-card contracts">
                    <div class="project-nav-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4>Contrats</h4>
                    <p>Gestion des contrats</p>
                </a>

                <a href="{{ route('documents.index') }}" class="project-nav-card documents">
                    <div class="project-nav-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>Documents</h4>
                    <p>Bibliothèque de fichiers</p>
                </a>

                <a href="{{ route('stock.index') }}" class="project-nav-card stock">
                    <div class="project-nav-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h4>Stock</h4>
                    <p>Inventaire du projet</p>
                </a>

                <a href="{{ route('transferts.index') }}" class="project-nav-card transfers">
                    <div class="project-nav-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h4>Transferts</h4>
                    <p>Mouvements de stock</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de Transfert avec les classes modifiées -->
    <div class="modal fade" id="transfertModal" tabindex="-1" aria-labelledby="transfertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content project-modern-modal">
                <div class="modal-header project-modern-header">
                    <h5 class="modal-title" id="transfertModalLabel">
                        <i class="fas fa-exchange-alt"></i>
                        Transférer du Stock
                    </h5>
                    <button type="button" class="btn-close project-modern-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body project-modern-body">
                    <form action="{{ route('transferts.store') }}" method="POST" class="project-transfer-form">
                        @csrf
                        <div class="form-row">
                            <div class="project-form-group">
                                <label><i class="fas fa-building"></i> Projet Source</label>
                                <select name="id_projet_source" class="form-control project-modern-select" required>
                                    <option value="">Sélectionner le projet source</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="project-form-group">
                                <label><i class="fas fa-bullseye"></i> Projet Destination</label>
                                <select name="id_projet_destination" class="form-control project-modern-select" required>
                                    <option value="">Sélectionner le projet destination</option>
                                    @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}">{{ $projet->nom_projet }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="project-form-group">
                                <label><i class="fas fa-box"></i> Article</label>
                                <select name="article_id" class="form-control project-modern-select" required>
                                    <option value="">Sélectionner un article</option>
                                    @foreach($articles as $article)
                                    <option value="{{ $article->id }}">{{ $article->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="project-form-group">
                                <label><i class="fas fa-sort-numeric-up"></i> Quantité</label>
                                <input type="number" name="quantite" class="form-control project-modern-input"
                                    placeholder="Quantité à transférer" min="1" required>
                            </div>
                        </div>

                        <div class="project-form-group">
                            <label><i class="fas fa-calendar-alt"></i> Date de Transfert</label>
                            <input type="date" name="date_transfert" class="form-control project-modern-input"
                                value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="project-form-actions">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary project-modern-btn">
                                <i class="fas fa-paper-plane"></i> Effectuer le Transfert
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
 /* Encapsulez vos styles spécifiques dans des classes conteneurs */
/* Au lieu de :root, utilisez un préfixe pour vos variables CSS */

.project-theme {
    --project-primary-color: #033765;
    --project-secondary-color: #0A8CFF;
    --project-success-color: #28a745;
    --project-accent-color: #ffffff;
    --project-gradient-primary: linear-gradient(135deg, #033765 0%, #0A8CFF 100%);
    --project-gradient-success: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    --project-gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    --project-shadow-card: 0 10px 30px rgba(3, 55, 101, 0.1);
    --project-shadow-hover: 0 20px 40px rgba(3, 55, 101, 0.2);
    --project-border-radius: 16px;
    --project-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Section Gestion des Projets - Préfixez les classes pour éviter les conflits */
.project-dashboard-section {
    background: var(--project-gradient-primary);
    border-radius: var(--project-border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 2500px;
    box-shadow: var(--project-shadow-card);
    position: relative;
    overflow: hidden;
}

.project-dashboard-section::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    animation: project-rotate 20s linear infinite;
}

@keyframes project-rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.project-section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--project-accent-color);
    margin-bottom: 2.5rem;
    text-align: center;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    position: relative;
    z-index: 1;
}

.project-section-title i {
    margin-right: 1rem;
    background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.project-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    position: relative;
    z-index: 1;
}

.project-dashboard-card {
    background: var(--project-gradient-card);
    border-radius: var(--project-border-radius);
    padding: 2.5rem;
    text-decoration: none;
    color: var(--project-primary-color);
    transition: var(--project-transition);
    box-shadow: var(--project-shadow-card);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-height: 150px;
}

.project-dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    transition: var(--project-transition);
    z-index: -1;
}

.project-dashboard-card.primary::before {
    background: var(--project-gradient-primary);
}

.project-dashboard-card.success::before {
    background: var(--project-gradient-success);
}

.project-dashboard-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--project-shadow-hover);
    color: white;
}

.project-dashboard-card:hover::before {
    left: 0;
}

.project-card-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    transition: var(--project-transition);
}

.project-dashboard-card.primary .project-card-icon {
    color: var(--project-primary-color);
}

.project-dashboard-card.success .project-card-icon {
    color: var(--project-success-color);
}

.project-dashboard-card:hover .project-card-icon {
    color: white;
    transform: scale(1.15) rotateY(360deg);
}

.project-card-content h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.8rem;
}

.project-card-content p {
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
    max-width: 2600px;
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

