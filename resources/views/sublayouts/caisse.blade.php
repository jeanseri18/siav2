@extends('layouts.app')
@section('content')<div class="dashboard-section finances">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-coins"></i>
            Gestion des Finances
        </h2>
    </div>
    
    <div class="dashboard-grid">
        <a href="{{ route('caisse.brouillard') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="card-content">
                <h3>Brouillard de Caisse</h3>
                <p>Consultez l'historique des transactions</p>
            </div>
        </a>
        
        <a href="{{ route('caisse.demande-liste') }}" class="dashboard-card">
            <div class="card-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            <div class="card-content">
                <h3>Demandes de Dépense</h3>
                <p>Gérez vos demandes de dépense</p>
            </div>
        </a>
        
        <button class="dashboard-card" onclick="openModal('saisirDepenseModal')">
            <div class="card-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="card-content">
                <h3>Saisir Dépense</h3>
                <p>Enregistrer une nouvelle dépense</p>
            </div>
        </button>
        
        <button class="dashboard-card" onclick="openModal('approvisionnerModal')">
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="card-content">
                <h3>Approvisionner Caisse</h3>
                <p>Ajouter des fonds à la caisse</p>
            </div>
        </button>
        
        <button class="dashboard-card" onclick="openModal('demanderDepenseModal')">
            <div class="card-icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="card-content">
                <h3>Demander une Dépense</h3>
                <p>Faire une demande de dépense</p>
            </div>
        </button>
    </div>
</div>

<!-- Modals améliorés -->
<div id="saisirDepenseModal" class="modal-modern">
    <div class="modal-content-modern">
        <div class="modal-header-modern">
            <h2><i class="fas fa-plus-circle"></i> Saisir Dépense</h2>
            <button class="modal-close" onclick="closeModal('saisirDepenseModal')">&times;</button>
        </div>
        <div class="modal-body-modern">
            <form action="{{ route('caisse.saisirDepense', $bus->id) }}" method="POST" class="modern-form">
                @csrf
                <div class="form-group">
                    <label for="montant"><i class="fas fa-euro-sign"></i> Montant</label>
                    <input type="number" id="montant" name="montant" class="form-control-modern" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="motif"><i class="fas fa-comment"></i> Motif</label>
                    <input type="text" id="motif" name="motif" class="form-control-modern" placeholder="Motif de la dépense" required>
                </div>
                <button type="submit" class="btn-modern btn-primary-modern">
                    <i class="fas fa-check"></i> Valider
                </button>
            </form>
        </div>
    </div>
</div>

<div id="approvisionnerModal" class="modal-modern">
    <div class="modal-content-modern">
        <div class="modal-header-modern">
            <h2><i class="fas fa-wallet"></i> Approvisionner Caisse</h2>
            <button class="modal-close" onclick="closeModal('approvisionnerModal')">&times;</button>
        </div>
        <div class="modal-body-modern">
            <form action="{{ route('caisse.approvisionnerCaisse', $bus->id) }}" method="POST" class="modern-form">
                @csrf
                <div class="form-group">
                    <label for="montant_appro"><i class="fas fa-euro-sign"></i> Montant</label>
                    <input type="number" id="montant_appro" name="montant" class="form-control-modern" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="motif_appro"><i class="fas fa-comment"></i> Motif</label>
                    <input type="text" id="motif_appro" name="motif" class="form-control-modern" placeholder="Motif de l'approvisionnement" required>
                </div>
                <button type="submit" class="btn-modern btn-primary-modern">
                    <i class="fas fa-check"></i> Valider
                </button>
            </form>
        </div>
    </div>
</div>

<div id="demanderDepenseModal" class="modal-modern">
    <div class="modal-content-modern">
        <div class="modal-header-modern">
            <h2><i class="fas fa-hand-holding-usd"></i> Demander Dépense</h2>
            <button class="modal-close" onclick="closeModal('demanderDepenseModal')">&times;</button>
        </div>
        <div class="modal-body-modern">
            <form action="{{ route('caisse.demandeDepense', $bus->id) }}" method="POST" class="modern-form">
                @csrf
                <div class="form-group">
                    <label for="montant_demande"><i class="fas fa-euro-sign"></i> Montant</label>
                    <input type="number" id="montant_demande" name="montant" class="form-control-modern" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="motif_demande"><i class="fas fa-comment"></i> Motif</label>
                    <input type="text" id="motif_demande" name="motif" class="form-control-modern" placeholder="Motif de la demande" required>
                </div>
                <button type="submit" class="btn-modern btn-primary-modern">
                    <i class="fas fa-paper-plane"></i> Envoyer la demande
                </button>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #033765;
    --secondary-color: #0A8CFF;
    --accent-color: #ffffff;
    --gradient-primary: linear-gradient(135deg, #033765 0%, #0A8CFF 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    --shadow-card: 0 10px 30px rgba(3, 55, 101, 0.1);
    --shadow-hover: 0 20px 40px rgba(3, 55, 101, 0.2);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-section.finances {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 2600px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section.finances::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
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
    background: linear-gradient(45deg, #FFD700, #FFA500);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.dashboard-card {
    background: var(--gradient-card);
    border: none;
    border-radius: var(--border-radius);
    padding: 2rem;
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
}

.dashboard-card::before {
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

.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
    color: white;
}

.dashboard-card:hover::before {
    left: 0;
}

.card-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--secondary-color);
    transition: var(--transition);
}

.dashboard-card:hover .card-icon {
    color: white;
    transform: scale(1.1);
}

.card-content h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.card-content p {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0;
}

/* Styles des modals */
.modal-modern {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(3, 55, 101, 0.8);
    backdrop-filter: blur(5px);
}

.modal-content-modern {
    background: white;
    margin: 5% auto;
    border-radius: var(--border-radius);
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease-out;
    overflow: hidden;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header-modern {
    background: var(--gradient-primary);
    color: white;
    padding: 1.5rem 2rem;
    position: relative;
}

.modal-header-modern h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.modal-close {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: white;
    font-size: 1.8rem;
    cursor: pointer;
    transition: var(--transition);
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-50%) rotate(90deg);
}

.modal-body-modern {
    padding: 2rem;
}

.modern-form .form-group {
    margin-bottom: 1.5rem;
}

.modern-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--primary-color);
}

.modern-form label i {
    margin-right: 0.5rem;
    color: var(--secondary-color);
}

.form-control-modern {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
    background: #f8f9fa;
}

.form-control-modern:focus {
    outline: none;
    border-color: var(--secondary-color);
    background: white;
    box-shadow: 0 0 0 3px rgba(10, 140, 255, 0.1);
}

.btn-modern {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    justify-content: center;
}

.btn-primary-modern {
    background: var(--gradient-primary);
    color: white;
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(3, 55, 101, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-section.finances {
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
        padding: 1.5rem;
    }
}
</style>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = "block";
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
    document.body.style.overflow = 'auto';
}

// Fermer le modal en cliquant en dehors
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal-modern');
    modals.forEach(function(modal) {
        if (event.target === modal) {
            modal.style.display = "none";
            document.body.style.overflow = 'auto';
        }
    });
}

// Fermer avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal-modern');
        modals.forEach(function(modal) {
            if (modal.style.display === 'block') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});
</script>
@endsection