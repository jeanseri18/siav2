{{-- Page caisse / historique --}}
@extends('layouts.app')

@section('title', 'Caisse')
@section('page-title', 'Caisse')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('caisse.brouillard') }}">Caisse</a></li>
<li class="breadcrumb-item active">Brouillard</li>
<!-- Modal Saisir Dépense -->
<div class="modal fade" id="saisirDepenseModal" tabindex="-1" aria-labelledby="saisirDepenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saisirDepenseModalLabel">Saisir une Dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('caisse.saisirDepense') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="motif" class="form-label">Motif de la dépense</label>
                        <input type="text" class="form-control" id="motif" name="motif" required>
                    </div>
                    <div class="mb-3">
                        <label for="montant" class="form-label">Montant</label>
                        <input type="number" class="form-control" id="montant" name="montant" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_operation" class="form-label">Date de l'opération</label>
                        <input type="date" class="form-control" id="date_operation" name="date_operation"
                               value="{{ old('date_operation', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optionnel)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Saisir la Dépense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Demander une Dépense -->
<div class="modal fade" id="demanderDepenseModal" tabindex="-1" aria-labelledby="demanderDepenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="demanderDepenseModalLabel">Demander une Dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('caisse.demandeDepense') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="motif_demande" class="form-label">Motif de la demande</label>
                        <input type="text" class="form-control" id="motif_demande" name="motif" required>
                    </div>
                    <div class="mb-3">
                        <label for="montant_demande" class="form-label">Montant demandé</label>
                        <input type="number" class="form-control" id="montant_demande" name="montant" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="responsable_hierarchique" class="form-label">Responsable hiérarchique</label>
                        <select class="form-control" id="responsable_hierarchique" name="responsable_hierarchique_id" required>
                            <option value="">Choisir un responsable</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id }}">{{ $responsable->prenom }} {{ $responsable->nom }} ({{ $responsable->poste }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="justification" class="form-label">Justification</label>
                        <textarea class="form-control" id="justification" name="justification" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">Envoyer la Demande</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}
</script>

@endsection

@section('content')

@php
    $canManageBrouillard = in_array(Auth::user()->role, ['admin', 'dg', 'caissier', 'chef_projet', 'conducteur_travaux', 'controleur_caisse'], true);
@endphp

<div class="app-fade-in">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif
    <div class="d-flex justify-content-between mb-3">
        <h3 class="mb-0">Gestion de Caisse</h3>
        <a href="{{ route('caisse.approvisionnement') }}" class="app-btn app-btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Nouvel Approvisionnement
        </a>
    </div>
    
    <!-- Tableau de bord -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-chart-line me-2"></i>Tableau de Bord - {{ $bus->nom }}
                    </h2>
                </div>
                <div class="app-card-body">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-5 g-3 mb-1 caisse-kpi-row">
                        <div class="col d-flex flex-column">
                            <div class="text-center p-3 border rounded dashboard-kpi-card h-100 flex-grow-1 d-flex flex-column justify-content-center" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#modalSoldeDebutMois" title="Dernier solde enregistré avant le 1er du mois">
                                <h6 class="text-muted mb-2 small text-uppercase fw-semibold">Caisse au début du mois</h6>
                                <div class="h4 fw-bold text-info mb-2">{{ number_format((float)$soldeDebutMois, 2, ',', ' ') }}</div>
                                <small class="text-muted mt-auto">Voir le détail</small>
                            </div>
                        </div>
                        <div class="col d-flex flex-column">
                            <div class="text-center p-3 border rounded dashboard-kpi-card h-100 flex-grow-1 d-flex flex-column justify-content-center" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#modalSoldeActuel" title="Tous les mouvements de caisse">
                                <h6 class="text-muted mb-2 small text-uppercase fw-semibold">Solde caisse actuel</h6>
                                <div class="h4 fw-bold text-primary mb-2">{{ number_format((float)$soldeActuel, 2, ',', ' ') }}</div>
                                <small class="text-muted mt-auto">Voir le détail</small>
                            </div>
                            @if($canManageBrouillard)
                                <form action="{{ route('caisse.brouillard.remiseAZero') }}" method="POST" class="mt-2" onsubmit="return confirm('Une écriture d\'ajustement sera ajoutée au brouillard pour ramener le solde à zéro (l\'historique est conservé). Continuer ?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100 text-wrap">
                                        Remettre le solde à zéro
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="col d-flex flex-column">
                            <div class="text-center p-3 border rounded dashboard-kpi-card h-100 flex-grow-1 d-flex flex-column justify-content-center" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#modalSortiesMois" title="Sorties et dépenses enregistrées ce mois-ci">
                                <h6 class="text-muted mb-2 small text-uppercase fw-semibold">Dépenses du mois</h6>
                                <div class="h4 fw-bold text-danger mb-2">{{ number_format((float)$totalSortiesMois, 2, ',', ' ') }}</div>
                                <small class="text-muted mt-auto">Voir le détail</small>
                            </div>
                        </div>
                        <div class="col d-flex flex-column">
                            <div class="text-center p-3 border rounded dashboard-kpi-card h-100 flex-grow-1 d-flex flex-column justify-content-center" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#modalApproMois" title="Entrées d’approvisionnement du mois">
                                <h6 class="text-muted mb-2 small text-uppercase fw-semibold">Total appro. mois</h6>
                                <div class="h4 fw-bold text-success mb-2">{{ number_format((float)$totalApproMois, 2, ',', ' ') }}</div>
                                <small class="text-muted mt-auto">Voir le détail</small>
                            </div>
                        </div>
                        <div class="col d-flex flex-column">
                            <div class="text-center p-3 border rounded dashboard-kpi-card h-100 flex-grow-1 d-flex flex-column justify-content-center" role="button" tabindex="0" data-bs-toggle="modal" data-bs-target="#modalSoldeMois" title="Écart : solde actuel − caisse au début du mois">
                                <h6 class="text-muted mb-2 small text-uppercase fw-semibold">Variation du mois</h6>
                                <div class="h4 fw-bold {{ ($soldeActuel - $soldeDebutMois) >= 0 ? 'text-success' : 'text-danger' }} mb-2">{{ number_format((float)($soldeActuel - $soldeDebutMois), 2, ',', ' ') }}</div>
                                <small class="text-muted mt-auto">Voir le détail</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Formulaire d'approvisionnement de caisse -->
        <div class="col-md-6 mb-4">
            <div class="app-card">
                <div class="app-card-header d-flex justify-content-between align-items-center">
                    <h2 class="app-card-title">
                        <i class="fas fa-money-bill-wave me-2"></i>Approvisionnement Rapide
                    </h2>
                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseApproForm" aria-expanded="true" aria-controls="collapseApproForm">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="app-card-body collapse show" id="collapseApproForm">
                    <form action="{{ route('caisse.approvisionnerCaisse') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="bu_id" class="form-label">Business Unit (BU)</label>
                            <select id="bu_id" name="bu_id" class="form-control" required>
                                @foreach(\App\Models\BU::all() as $bu_item)
                                    <option value="{{ $bu_item->id }}" {{ session('selected_bu') == $bu_item->id ? 'selected' : '' }}>{{ $bu_item->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="objet" class="form-label">Objet</label>
                            <input type="text" class="form-control" id="objet" name="motif" required>
                        </div>
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant</label>
                            <input type="number" class="form-control" id="montant" name="montant" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mode de paiement</label>
                            <div class="d-flex">
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="mode_paiement" id="mode_espece" value="espece" checked>
                                    <label class="form-check-label" for="mode_espece">Espèce</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mode_paiement" id="mode_cheque" value="cheque">
                                    <label class="form-check-label" for="mode_cheque">Chèque</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Champs spécifiques au mode de paiement -->
                        <div id="cheque_fields" style="display: none;">
                            <div class="mb-3">
                                <label for="reference_cheque" class="form-label">Référence Chèque</label>
                                <input type="text" class="form-control" id="reference_cheque" name="reference_cheque">
                            </div>
                            <div class="mb-3">
                                <label for="banque_id" class="form-label">Banque</label>
                                <select class="form-select" id="banque_id" name="banque_id">
                                    @foreach(\App\Models\Banque::where('bu_id', session('selected_bu'))->get() as $banque)
                                        <option value="{{ $banque->id }}">{{ $banque->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div id="espece_fields">
                            <div class="mb-3">
                                <label for="origine_fonds" class="form-label">Origine des fonds</label>
                                <input type="text" class="form-control" id="origine_fonds" name="origine_fonds">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_appro" class="form-label">Date</label>
                            <input type="datetime-local" class="form-control" id="date_appro" name="date_appro" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="col-md-6 mb-4">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-tools me-2"></i>Actions Rapides
                    </h2>
                </div>
                <div class="app-card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('caisse.demande-liste') }}" class="app-btn app-btn-outline-primary w-100">
                                <i class="fas fa-list me-2"></i>Demandes de Dépense
                            </a>
                        </div>
                        <div class="col-6">
                            <button class="app-btn app-btn-outline-warning w-100" onclick="openModal('saisirDepenseModal')">
                                <i class="fas fa-minus-circle me-2"></i>Saisir Dépense
                            </button>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('caisse.approvisionnement') }}" class="app-btn app-btn-outline-success w-100">
                                <i class="fas fa-plus-circle me-2"></i>Approvisionner Caisse
                            </a>
                        </div>
                        <div class="col-6">
                            <button class="app-btn app-btn-outline-info w-100" onclick="openModal('demanderDepenseModal')">
                                <i class="fas fa-hand-paper me-2"></i>Demander une Dépense
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Dernière ligne (tri date opération): {{ $brouillardCaisse->first() ? $brouillardCaisse->first()->date_affichage : 'Aucune' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-money-check-alt me-2"></i>{{ $bus->nom }}
            </h2>
        </div>

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Devise</th>
                        <th>Motif</th>
                        <th>Solde Cumulé</th>
                        @if($canManageBrouillard)
                        <th class="text-end">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($brouillardCaisse as $item)
                        <tr>
                            <td>{{ $item->date_affichage }}</td>
                            <td>
                                @if(\App\Models\BrouillardCaisse::estTypeEntree($item->type))
                                    <span class="app-badge app-badge-success app-badge-pill">
                                        <i class="fas fa-arrow-up me-1"></i> Entrée
                                    </span>
                                @else
                                    <span class="app-badge app-badge-danger app-badge-pill">
                                        <i class="fas fa-arrow-down me-1"></i> Sortie
                                    </span>
                                @endif
                            </td>
                            <td class="app-fw-bold">{{ number_format((float) $item->montant, 2, ',', ' ') }} FCFA</td>
                            <td><span class="text-muted small">FCFA</span></td>
                            <td>{{ $item->motif }}</td>
                            <td>
                                <span class="app-badge app-badge-info app-badge-pill">
                                    {{ number_format((float) $item->solde_cumule, 2, ',', ' ') }} FCFA
                                </span>
                            </td>
                            @if($canManageBrouillard)
                            <td class="text-end text-nowrap position-relative">
                                <div class="dropdown">
                                    <button class="app-btn app-btn-outline-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-auto-close="true" aria-expanded="false" title="Actions">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li>
                                            <button type="button" class="dropdown-item js-open-edit-brouillard" data-edit="{{ json_encode(['id' => $item->id, 'type' => $item->type, 'montant' => (float) $item->montant, 'motif' => $item->motif], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}">
                                                <i class="fas fa-edit me-2"></i>Modifier
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('caisse.brouillard.destroy', $item) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette transaction ? Les soldes seront recalculés.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modaux détail KPI tableau de bord --}}
<div class="modal fade" id="modalSoldeDebutMois" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Caisse au début du mois — détail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Valeur = dernier solde cumulé enregistré avant le {{ $debutMois->format('d/m/Y') }} (00:00).</p>
                @if($ligneReferenceDebutMois)
                    <div class="table-responsive">
                        <table class="table table-sm app-table mb-0">
                            <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Motif</th><th>Solde cumulé</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td>{{ $ligneReferenceDebutMois->date_affichage }}</td>
                                    <td>{{ $ligneReferenceDebutMois->type }}</td>
                                    <td>{{ number_format((float) $ligneReferenceDebutMois->montant, 2, ',', ' ') }}</td>
                                    <td>{{ $ligneReferenceDebutMois->motif }}</td>
                                    <td>{{ number_format((float) $ligneReferenceDebutMois->solde_cumule, 2, ',', ' ') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mb-0 text-muted">Aucune écriture avant cette date — solde de début considéré comme 0.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSoldeActuel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Solde caisse actuel — historique complet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light"><tr><th>Date</th><th>Type</th><th>Montant</th><th>Motif</th><th>Solde cumulé</th></tr></thead>
                        <tbody>
                            @forelse($brouillardChrono as $row)
                                <tr>
                                    <td>{{ $row->date_affichage }}</td>
                                    <td>{{ \App\Models\BrouillardCaisse::estTypeEntree($row->type) ? 'Entrée' : 'Sortie' }}</td>
                                    <td>{{ number_format((float) $row->montant, 2, ',', ' ') }}</td>
                                    <td>{{ $row->motif }}</td>
                                    <td>{{ number_format((float) $row->solde_cumule, 2, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Aucun mouvement</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSortiesMois" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dépenses du mois (total {{ number_format((float) $totalSortiesMois, 2, ',', ' ') }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light"><tr><th>Date</th><th>Montant</th><th>Motif</th><th>Solde cumulé</th></tr></thead>
                        <tbody>
                            @forelse($sortiesMoisListe as $row)
                                <tr>
                                    <td>{{ $row->date_affichage }}</td>
                                    <td>{{ number_format((float) $row->montant, 2, ',', ' ') }}</td>
                                    <td>{{ $row->motif }}</td>
                                    <td>{{ number_format((float) $row->solde_cumule, 2, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Aucune sortie ce mois-ci</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalApproMois" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Total appro. mois ({{ number_format((float) $totalApproMois, 2, ',', ' ') }})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light"><tr><th>Date</th><th>Montant</th><th>Motif</th><th>Solde cumulé</th></tr></thead>
                        <tbody>
                            @forelse($entreesMoisListe as $row)
                                <tr>
                                    <td>{{ $row->date_affichage }}</td>
                                    <td>{{ number_format((float) $row->montant, 2, ',', ' ') }}</td>
                                    <td>{{ $row->motif }}</td>
                                    <td>{{ number_format((float) $row->solde_cumule, 2, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Aucune entrée ce mois-ci</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSoldeMois" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Variation du mois ({{ number_format((float)($soldeActuel - $soldeDebutMois), 2, ',', ' ') }}) — écritures du mois</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Le solde affiché sur la carte « Solde caisse actuel » correspond au dernier solde cumulé ; la « Variation du mois » = solde actuel − caisse au début du mois. Ci-dessous : toutes les lignes du brouillard depuis le {{ $debutMois->format('d/m/Y') }}.</p>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light"><tr><th>Date</th><th>Type</th><th>Montant</th><th>Motif</th><th>Solde cumulé</th></tr></thead>
                        <tbody>
                            @forelse($mouvementsMoisChrono as $row)
                                <tr>
                                    <td>{{ $row->date_affichage }}</td>
                                    <td>{{ \App\Models\BrouillardCaisse::estTypeEntree($row->type) ? 'Entrée' : 'Sortie' }}</td>
                                    <td>{{ number_format((float) $row->montant, 2, ',', ' ') }}</td>
                                    <td>{{ $row->motif }}</td>
                                    <td>{{ number_format((float) $row->solde_cumule, 2, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Aucune écriture ce mois-ci</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($canManageBrouillard)
<div class="modal fade" id="editBrouillardModal" tabindex="-1" aria-labelledby="editBrouillardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBrouillardForm" method="POST" action="#">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBrouillardModalLabel">Modifier l'écriture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_brouillard_type" class="form-label">Type</label>
                        <select name="type" id="edit_brouillard_type" class="form-select" required>
                            <option value="Entrée">Entrée</option>
                            <option value="Sortie">Sortie</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_brouillard_montant" class="form-label">Montant (FCFA)</label>
                        <input type="number" name="montant" id="edit_brouillard_montant" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_brouillard_motif" class="form-label">Motif</label>
                        <textarea name="motif" id="edit_brouillard_motif" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.css" rel="stylesheet">
<style>
.dashboard-kpi-card { cursor: pointer; transition: box-shadow .15s ease, border-color .15s ease; }
.dashboard-kpi-card:hover { box-shadow: 0 .25rem .75rem rgba(3, 61, 113, .12); border-color: var(--primary, #033d71) !important; }
.caisse-kpi-row > .col { min-width: 0; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialisation de DataTables
        $('#Table').DataTable({
            responsive: true,
            dom: '<"dt-header"Bf>rt<"dt-footer"ip>',
            order: [[0, 'desc']],
            columnDefs: [
                @if($canManageBrouillard)
                { responsivePriority: 1, targets: [0, 1, -1] },
                @else
                { responsivePriority: 1, targets: [0, 1] },
                @endif
            ],
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-file-export"></i> Exporter',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                },
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i> Colonnes'
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            }
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');

        @if($canManageBrouillard)
        const editForm = document.getElementById('editBrouillardForm');
        const editBaseUrl = @json(url('caisse/brouillard'));
        $(document).on('click', '.js-open-edit-brouillard', function (e) {
            e.preventDefault();
            const raw = this.getAttribute('data-edit');
            if (!raw) return;
            let d;
            try {
                d = JSON.parse(raw);
            } catch (err) {
                console.error('data-edit JSON invalide', err, raw);
                return;
            }
            editForm.action = editBaseUrl + '/' + encodeURIComponent(d.id);
            document.getElementById('edit_brouillard_type').value = d.type;
            document.getElementById('edit_brouillard_montant').value = d.montant;
            document.getElementById('edit_brouillard_motif').value = d.motif;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editBrouillardModal')).show();
        });
        @endif
        
        // Gestion de l'affichage des champs selon le mode de paiement
        $('input[name="mode_paiement"]').change(function() {
            if ($(this).val() === 'cheque') {
                $('#cheque_fields').show();
                $('#espece_fields').hide();
            } else {
                $('#cheque_fields').hide();
                $('#espece_fields').show();
            }
        });
        
        // Initialiser avec la valeur par défaut
        $('input[name="mode_paiement"]:checked').trigger('change');
        
        // Initialiser la date avec la date et l'heure actuelles
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        $('#date_appro').val(formattedDateTime);
    });
</script>
@endpush
@endsection
