{{-- Page Index - Brouillard de Caisse --}}
@extends('layouts.app')

@section('title', 'Brouillard de Caisse')
@section('page-title', 'Brouillard de Caisse')

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

<div class="app-fade-in">
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
                    <div class="row">
                        <div class="col-md-2 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Solde Début Mois</h6>
                                <div class="h4 fw-bold text-info">{{ number_format((float)$soldeDebutMois, 2, ',', ' ') }}</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Solde Actuel</h6>
                                <div class="h4 fw-bold text-primary">{{ number_format((float)$soldeActuel, 2, ',', ' ') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Total Sorties Mois</h6>
                                <div class="h4 fw-bold text-danger">{{ number_format((float)$totalSortiesMois, 2, ',', ' ') }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Total Approvisionnements</h6>
                                <div class="h4 fw-bold text-success">{{ number_format((float)$totalApproMois, 2, ',', ' ') }}</div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">Solde Mois</h6>
                                <div class="h4 fw-bold {{ ($soldeActuel - $soldeDebutMois) >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format((float)($soldeActuel - $soldeDebutMois), 2, ',', ' ') }}</div>
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
                                    @foreach(\App\Models\Banque::all() as $banque)
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
                        <small class="text-muted">Dernière mise à jour: {{ $brouillardCaisse->first() ? $brouillardCaisse->first()->created_at->format('d/m/Y H:i') : 'Jamais' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-money-check-alt me-2"></i>Brouillard de Caisse de {{ $bus->nom }}
            </h2>
        </div>

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Motif</th>
                        <th>Solde Cumulé</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brouillardCaisse as $item)
                        <tr>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                @if($item->type == 'Entrée')
                                    <span class="app-badge app-badge-success app-badge-pill">
                                        <i class="fas fa-arrow-up me-1"></i> Entrée
                                    </span>
                                @else
                                    <span class="app-badge app-badge-danger app-badge-pill">
                                        <i class="fas fa-arrow-down me-1"></i> Sortie
                                    </span>
                                @endif
                            </td>
                            <td class="app-fw-bold">{{ $item->montant }}</td>
                            <td>{{ $item->motif }}</td>
                            <td>
                                <span class="app-badge app-badge-info app-badge-pill">
                                    {{ $item->solde_cumule }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.css" rel="stylesheet">
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