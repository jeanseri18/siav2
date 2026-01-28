@extends('layouts.app')
@section('content')

<div class="dashboard-section finances">
    <div class="section-header">
        <h2 class="section-title">
            <i class="fas fa-coins"></i>
            Gestion des Finances
        </h2>
    </div>
    
    <div class="dashboard-grid">
        <a href="{{ route('caisse.brouillard') }}" class="dashboard-card primary">
            <div class="card-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="card-content">
                <h3>Brouillard de Caisse</h3>
                <p>Consultez l'historique des transactions</p>
            </div>
        </a>
        
        <a href="{{ route('caisse.demande-liste') }}" class="dashboard-card info">
            <div class="card-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            <div class="card-content">
                <h3>Demandes de Dépense</h3>
                <p>Gérez vos demandes de dépense</p>
            </div>
        </a>
        
        <button class="dashboard-card success" onclick="openModal('saisirDepenseModal')">
            <div class="card-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="card-content">
                <h3>Saisir Dépense</h3>
                <p>Enregistrer une nouvelle dépense</p>
            </div>
        </button>
        
        <button class="dashboard-card warning" onclick="openModal('approvisionnerModal')">
            <div class="card-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="card-content">
                <h3>Approvisionner Caisse</h3>
                <p>Ajouter des fonds à la caisse</p>
            </div>
        </button>
        
        <button class="dashboard-card secondary" onclick="openModal('demanderDepenseModal')">
            <div class="card-icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="card-content">
                <h3>Demander une Dépense</h3>
                <p>Faire une demande de dépense</p>
            </div>
        </button>
        
        @if(in_array(Auth::user()->role, ['chef_projet', 'conducteur_travaux', 'admin', 'dg']))
        <a href="{{ route('caisse.demandesEnAttente') }}" class="dashboard-card secondary">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3>Approuver Demandes</h3>
                <p>Approuver les demandes de dépense en attente</p>
            </div>
        </a>
        @endif
    </div>
</div>

<!-- Modals améliorés -->
<div id="saisirDepenseModal" class="modal-modern">
    <div class="modal-content-modern modal-lg">
        <div class="modal-header-modern">
            <h2><i class="fas fa-plus-circle"></i> Saisir Dépense</h2>
            <button class="modal-close" onclick="closeModal('saisirDepenseModal')">&times;</button>
        </div>
        <div class="modal-body-modern">
            <form action="{{ route('caisse.saisirDepense', $bus->id) }}" method="POST" class="modern-form">
                @csrf
                <div class="form-group">
                    <label for="montant"><i class="fas fa-money-bill-wave"></i> Montant</label>
                    <input type="number" id="montant" name="montant" class="form-control-modern" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="motif"><i class="fas fa-comment"></i> Motif</label>
                    <input type="text" id="motif" name="motif" class="form-control-modern" placeholder="Motif de la dépense" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('caisse.approvisionnement') }}" class="btn-modern btn-secondary-modern">
                        <i class="fas fa-external-link-alt"></i> Formulaire détaillé
                    </a>
                    <button type="submit" class="btn-modern btn-primary-modern">
                        <i class="fas fa-check"></i> Valider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="approvisionnerModal" class="modal-modern">
    <div class="modal-content-modern modal-lg">
        <div class="modal-header-modern">
            <h2><i class="fas fa-wallet"></i> Approvisionner Caisse</h2>
            <button class="modal-close" onclick="closeModal('approvisionnerModal')">&times;</button>
        </div>
        <div class="modal-body-modern">
            <form action="{{ route('caisse.approvisionnerCaisse') }}" method="POST" class="modern-form">
                @csrf
                <div class="form-group">
                    <label for="bu_id_appro"><i class="fas fa-building"></i> Business Unit (BU)</label>
                    <select id="bu_id_appro" name="bu_id" class="form-control-modern" required>
                        @foreach(\App\Models\BU::all() as $bu_item)
                            <option value="{{ $bu_item->id }}" {{ session('selected_bu') == $bu_item->id ? 'selected' : '' }}>{{ $bu_item->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="montant_appro"><i class="fas fa-money-bill-wave"></i> Montant</label>
                    <input type="number" id="montant_appro" name="montant" class="form-control-modern" placeholder="0.00" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="motif_appro"><i class="fas fa-comment"></i> Motif</label>
                    <input type="text" id="motif_appro" name="motif" class="form-control-modern" placeholder="Motif de l'approvisionnement" required>
                </div>
                <div class="form-group">
                    <label for="mode_paiement"><i class="fas fa-money-check"></i> Mode de paiement</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="mode_cheque" name="mode_paiement" value="cheque" onchange="togglePaiementFields()" checked>
                            <label for="mode_cheque">Chèque</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="mode_espece" name="mode_paiement" value="espece" onchange="togglePaiementFields()">
                            <label for="mode_espece">Espèce</label>
                        </div>
                    </div>
                </div>
                
                <!-- Champs pour le paiement par chèque -->
                <div id="cheque_fields">
                    <div class="form-group">
                        <label for="banque"><i class="fas fa-university"></i> Banque</label>
                        <select id="banque" name="banque_id" class="form-control-modern" required>
                            <option value="">Sélectionner une banque</option>
                            @foreach(\App\Models\Banque::all() as $banque)
                                <option value="{{ $banque->id }}">{{ $banque->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reference_cheque"><i class="fas fa-hashtag"></i> Référence du chèque</label>
                        <input type="text" id="reference_cheque" name="reference_cheque" class="form-control-modern" placeholder="Numéro du chèque">
                    </div>
                </div>
                
                <!-- Champs pour le paiement en espèce -->
                <div id="espece_fields" style="display: none;">
                    <div class="form-group">
                        <label for="origine_fonds"><i class="fas fa-money-bill-wave"></i> Origine des fonds</label>
                        <input type="text" id="origine_fonds" name="origine_fonds" class="form-control-modern" placeholder="Origine des fonds">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="date_appro"><i class="fas fa-calendar-alt"></i> Date</label>
                    <input type="datetime-local" id="date_appro" name="date_appro" class="form-control-modern" value="{{ date('Y-m-d\TH:i') }}" required>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('caisse.approvisionnement') }}" class="btn-modern btn-secondary-modern">
                        <i class="fas fa-external-link-alt"></i> Formulaire détaillé
                    </a>
                    <button type="submit" class="btn-modern btn-primary-modern">
                        <i class="fas fa-check"></i> Valider
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="demanderDepenseModal" class="modal-modern">
    <div class="modal-content-modern modal-lg">
        <div class="modal-header-modern">
            <h2><i class="fas fa-hand-holding-usd"></i> Demander Dépense</h2>
            <button class="modal-close" onclick="closeModal('demanderDepenseModal')">&times;</button>
        </div>
        <div class="modal-body-modern">
            <form action="{{ route('caisse.demandeDepense') }}" method="POST" class="modern-form" id="demandeDepenseForm">
                @csrf
                <div class="form-group">
                    <label for="mois_demande"><i class="fas fa-calendar-alt"></i> Mois</label>
                    <select id="mois_demande" name="mois" class="form-control-modern" required>
                        <option value="Janvier">Janvier</option>
                        <option value="Février">Février</option>
                        <option value="Mars">Mars</option>
                        <option value="Avril">Avril</option>
                        <option value="Mai">Mai</option>
                        <option value="Juin">Juin</option>
                        <option value="Juillet">Juillet</option>
                        <option value="Août">Août</option>
                        <option value="Septembre">Septembre</option>
                        <option value="Octobre">Octobre</option>
                        <option value="Novembre">Novembre</option>
                        <option value="Décembre">Décembre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="objet_demande"><i class="fas fa-tag"></i> Objet</label>
                    <input type="text" id="objet_demande" name="objet" class="form-control-modern" placeholder="Objet de la demande" required>
                </div>
                <div class="form-group">
                    <label for="beneficiaires_demande"><i class="fas fa-users"></i> Bénéficiaires</label>
                    <input type="text" id="beneficiaires_demande" name="beneficiaires" class="form-control-modern" placeholder="Bénéficiaires">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-list"></i> Lignes de dépense</label>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="lignes_depense_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Désignation</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="lignes_depense_body">
                                <tr id="ligne_template">
                                    <td><span class="ligne-numero">1</span></td>
                                    <td>
                                        <input type="text" name="lignes[0][designation]" class="form-control-modern designation-input" placeholder="Désignation" required>
                                    </td>
                                    <td>
                                        <input type="number" name="lignes[0][quantite]" class="form-control-modern quantite-input" placeholder="Qté" value="1" min="1" required onchange="calculerTotal(this)">
                                    </td>
                                    <td>
                                        <input type="number" name="lignes[0][prix_unitaire]" class="form-control-modern prix-input" placeholder="0.00" step="0.01" required onchange="calculerTotal(this)">
                                    </td>
                                    <td>
                                        <span class="ligne-total">0.00</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn-modern btn-danger-modern btn-sm" onclick="supprimerLigne(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td colspan="2">
                                        <span id="total_general">0.00</span>
                                        <input type="hidden" name="montant_total" id="montant_total" value="0">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn-modern btn-info-modern" onclick="ajouterLigne()">
                        <i class="fas fa-plus"></i> Ajouter une ligne
                    </button>
                </div>
                
                <div class="form-group">
                    <label for="date_emission"><i class="fas fa-calendar-alt"></i> Date d'émission</label>
                    <input type="datetime-local" id="date_emission" name="date_emission" class="form-control-modern" value="{{ date('Y-m-d\TH:i') }}" required>
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
    --gradient-info: linear-gradient(135deg, var(--info, #17a2b8) 0%, #20c997 100%);
    --gradient-secondary: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    --gradient-card: linear-gradient(135deg, var(--white, #ffffff) 0%, #f8f9ff 100%);
    --shadow-card: var(--shadow-md, 0 0.5rem 1rem rgba(0, 0, 0, 0.15));
    --shadow-hover: var(--shadow-lg, 0 1rem 3rem rgba(0, 0, 0, 0.175));
    --border-radius: var(--border-radius-lg, 1rem);
    --transition: var(--transition-base, all 0.2s ease-in-out);
}

/* Styles pour les groupes de boutons radio */
.radio-group {
    display: flex;
    gap: 15px;
    margin-top: 5px;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Styles pour le tableau des lignes de dépense */
.table-responsive {
    margin-bottom: 15px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    border: 1px solid #e0e0e0;
}

.table th {
    background-color: #f5f7fa;
    font-weight: 600;
    text-align: left;
}

.table tbody tr:hover {
    background-color: #f9fafc;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.875rem;
}

/* Style pour la modal large */
.modal-lg {
    max-width: 1000px;
    width: 95%;
}

.dashboard-section.finances {
    background: var(--gradient-primary);
    border-radius: var(--border-radius);
    padding: 3rem 2rem;
    margin: 2rem auto;
    max-width: 1400px;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}

.dashboard-section.finances::before {
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
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-lg, 1.5rem);
    position: relative;
    z-index: 1;
}

.dashboard-card {
    background: var(--gradient-card);
    border: none;
    border-radius: var(--border-radius);
    padding: var(--spacing-lg, 1.5rem);
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
    min-height: 280px;
    max-height: 320px;
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
    background: var(--gradient-info);
}

.dashboard-card.secondary::before {
    background: var(--gradient-secondary);
}

.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-hover);
    color: white !important;
}

.dashboard-card:hover::before {
    left: 0;
}

.dashboard-card:hover * {
    color: white !important;
}

.dashboard-card:hover h3,
.dashboard-card:hover p {
    color: white !important;
}

.card-icon {
    font-size: 3.5rem;
    margin-bottom: 1rem;
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
    overflow-y: auto;
}

.modal-content-modern {
    background: white;
    margin: 5% auto;
    border-radius: var(--border-radius);
    width: 95%;
    max-width: 700px;
    max-height: 90vh;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease-out;
    overflow: hidden;
    display: flex;
    flex-direction: column;
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
    overflow-y: auto;
    max-height: calc(90vh - 80px);
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

.btn-secondary-modern {
    background: #f8f9fa;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-secondary-modern:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(3, 55, 101, 0.15);
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

function togglePaiementFields() {
    const chequeModeSelected = document.getElementById('mode_cheque').checked;
    const chequeFields = document.getElementById('cheque_fields');
    const especeFields = document.getElementById('espece_fields');
    
    if (chequeModeSelected) {
        chequeFields.style.display = 'block';
        especeFields.style.display = 'none';
    } else {
        chequeFields.style.display = 'none';
        especeFields.style.display = 'block';
    }
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

// Initialiser les champs de paiement au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // S'assurer que les champs appropriés sont affichés selon le mode de paiement sélectionné
    if (document.getElementById('mode_cheque')) {
        togglePaiementFields();
    }
});

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

// Fonction pour basculer entre les champs de paiement par chèque et en espèce
function togglePaiementFields() {
    const chequeModeSelected = document.getElementById('mode_cheque').checked;
    const chequeFields = document.getElementById('cheque_fields');
    const especeFields = document.getElementById('espece_fields');
    
    if (chequeModeSelected) {
        chequeFields.style.display = 'block';
        especeFields.style.display = 'none';
        document.getElementById('reference_cheque').setAttribute('required', 'required');
        document.getElementById('origine_fonds').removeAttribute('required');
    } else {
        chequeFields.style.display = 'none';
        especeFields.style.display = 'block';
        document.getElementById('reference_cheque').removeAttribute('required');
        document.getElementById('origine_fonds').setAttribute('required', 'required');
    }
}

// Compteur pour les lignes de dépense
let ligneCounter = 0;

// Fonction pour ajouter une nouvelle ligne de dépense
function ajouterLigne() {
    ligneCounter++;
    const tbody = document.getElementById('lignes_depense_body');
    const template = document.getElementById('ligne_template');
    const newRow = template.cloneNode(true);
    
    // Mettre à jour l'ID et les attributs de la nouvelle ligne
    newRow.id = 'ligne_' + ligneCounter;
    newRow.querySelector('.ligne-numero').textContent = tbody.children.length + 1;
    
    // Mettre à jour les noms des champs pour qu'ils soient uniques
    const inputs = newRow.querySelectorAll('input');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace(/\[\d+\]/, '[' + ligneCounter + ']'));
            input.value = '';
        }
    });
    
    // Réinitialiser les valeurs par défaut
    newRow.querySelector('.quantite-input').value = '1';
    newRow.querySelector('.ligne-total').textContent = '0.00';
    
    // Ajouter la nouvelle ligne au tableau
    tbody.appendChild(newRow);
    
    // Recalculer le total général
    calculerTotalGeneral();
}

// Fonction pour supprimer une ligne de dépense
function supprimerLigne(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('lignes_depense_body');
    
    // Ne pas supprimer s'il ne reste qu'une seule ligne
    if (tbody.children.length > 1) {
        row.remove();
        
        // Mettre à jour les numéros de ligne
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            row.querySelector('.ligne-numero').textContent = index + 1;
        });
        
        // Recalculer le total général
        calculerTotalGeneral();
    } else {
        alert('Vous devez conserver au moins une ligne de dépense.');
    }
}

// Fonction pour calculer le total d'une ligne
function calculerTotal(input) {
    const row = input.closest('tr');
    const quantite = parseFloat(row.querySelector('.quantite-input').value) || 0;
    const prix = parseFloat(row.querySelector('.prix-input').value) || 0;
    const total = quantite * prix;
    
    row.querySelector('.ligne-total').textContent = total.toFixed(2);
    
    // Recalculer le total général
    calculerTotalGeneral();
}

// Fonction pour calculer le total général
function calculerTotalGeneral() {
    let total = 0;
    const totals = document.querySelectorAll('.ligne-total');
    
    totals.forEach(element => {
        total += parseFloat(element.textContent) || 0;
    });
    
    document.getElementById('total_general').textContent = total.toFixed(2);
    document.getElementById('montant_total').value = total;
}

// Initialiser les fonctions au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser l'affichage des champs de paiement
    togglePaiementFields();
    
    // Initialiser le calcul du total pour la première ligne
    const premiereLigne = document.getElementById('ligne_template');
    if (premiereLigne) {
        const prixInput = premiereLigne.querySelector('.prix-input');
        if (prixInput) {
            prixInput.addEventListener('input', function() {
                calculerTotal(this);
            });
        }
        
        const quantiteInput = premiereLigne.querySelector('.quantite-input');
        if (quantiteInput) {
            quantiteInput.addEventListener('input', function() {
                calculerTotal(this);
            });
        }
    }
});
</script>
@endsection