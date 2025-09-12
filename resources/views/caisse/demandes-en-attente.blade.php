{{-- Page des Demandes en Attente --}}
@extends('layouts.app')

@section('title', 'Demandes en Attente')
@section('page-title', 'Demandes en Attente d\'Approbation')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('caisse.brouillard') }}">Caisse</a></li>
<li class="breadcrumb-item"><a href="{{ route('caisse.demande-liste') }}">Demandes de Dépenses</a></li>
<li class="breadcrumb-item active">En Attente</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-clock me-2"></i>Demandes en Attente d'Approbation
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('caisse.demande-liste') }}" class="app-btn app-btn-outline-primary">
                    <i class="fas fa-list me-1"></i> Toutes les demandes
                </a>
            </div>
        </div>

        <div class="app-card-body">
            @if($demandesEnAttente->count() > 0)
                <div class="app-table-responsive">
                    <table id="TableAttente" class="app-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Demandeur</th>
                                <th>BU</th>
                                <th>Montant</th>
                                <th>Motif</th>
                                <th>Statut</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandesEnAttente as $demande)
                                <tr>
                                    <td>{{ $demande->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $demande->user ? $demande->user->nom . ' ' . $demande->user->prenom : 'N/A' }}</td>
                                    <td>{{ $demande->bu ? $demande->bu->nom : 'N/A' }}</td>
                                    <td class="app-fw-bold">{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $demande->motif }}</td>
                                    <td>
                                        @if($demande->statut == 'en_attente_responsable')
                                            <span class="app-badge app-badge-warning app-badge-pill">
                                                <i class="fas fa-user-clock me-1"></i> En attente responsable
                                            </span>
                                        @elseif($demande->statut == 'approuve_responsable')
                                            <span class="app-badge app-badge-info app-badge-pill">
                                                <i class="fas fa-user-check me-1"></i> En attente RAF
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="app-d-flex app-gap-1">
                                            <!-- Bouton PDF -->
                                            <a href="{{ route('caisse.voirDemandeDepensePDF', $demande->id) }}" target="_blank" class="app-btn app-btn-primary app-btn-sm app-btn-icon" title="Voir en PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            
                                            <!-- Boutons d'approbation pour responsable -->
                                            @if($demande->statut == 'en_attente_responsable' && $demande->responsable_hierarchique_id == Auth::id())
                                                <button type="button" class="app-btn app-btn-success app-btn-sm" onclick="approuverDemande({{ $demande->id }}, 'responsable', 'approuver')" title="Approuver">
                                                    <i class="fas fa-check"></i> Approuver
                                                </button>
                                                <button type="button" class="app-btn app-btn-danger app-btn-sm" onclick="approuverDemande({{ $demande->id }}, 'responsable', 'rejeter')" title="Rejeter">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            @endif
                                            
                                            <!-- Boutons d'approbation pour RAF -->
                                            @if($demande->statut == 'approuve_responsable' && in_array(Auth::user()->role, ['admin', 'dg']))
                                                <button type="button" class="app-btn app-btn-success app-btn-sm" onclick="approuverDemande({{ $demande->id }}, 'raf', 'approuver')" title="Approuver RAF">
                                                    <i class="fas fa-check-double"></i> Approuver
                                                </button>
                                                <button type="button" class="app-btn app-btn-danger app-btn-sm" onclick="approuverDemande({{ $demande->id }}, 'raf', 'rejeter')" title="Rejeter RAF">
                                                    <i class="fas fa-times"></i> Rejeter
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune demande en attente</h4>
                    <p class="text-muted">Il n'y a actuellement aucune demande en attente de votre approbation.</p>
                    <a href="{{ route('caisse.demande-liste') }}" class="app-btn app-btn-primary">
                        <i class="fas fa-list me-1"></i> Voir toutes les demandes
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'approbation -->
<div class="modal fade" id="approbationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approbationModalTitle">Approbation de la demande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approbationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="action" id="approbationAction">
                    <div class="mb-3">
                        <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
                        <textarea class="form-control" name="commentaire" id="commentaire" rows="3" placeholder="Ajoutez un commentaire..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn" id="approbationSubmitBtn">Confirmer</button>
                </div>
            </form>
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
        $('#TableAttente').DataTable({
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
            },
            order: [[0, 'desc']] // Trier par date décroissante
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
    });
    
    // Fonction pour gérer les approbations
    function approuverDemande(demandeId, type, action) {
        const modal = new bootstrap.Modal(document.getElementById('approbationModal'));
        const form = document.getElementById('approbationForm');
        const title = document.getElementById('approbationModalTitle');
        const submitBtn = document.getElementById('approbationSubmitBtn');
        const actionInput = document.getElementById('approbationAction');
        
        // Configurer le formulaire selon le type et l'action
        if (type === 'responsable') {
            form.action = `{{ url('caisse/approuver-responsable') }}/${demandeId}`;
            if (action === 'approuver') {
                title.textContent = 'Approuver la demande (Responsable)';
                submitBtn.textContent = 'Approuver';
                submitBtn.className = 'btn btn-success';
            } else {
                title.textContent = 'Rejeter la demande (Responsable)';
                submitBtn.textContent = 'Rejeter';
                submitBtn.className = 'btn btn-danger';
            }
        } else if (type === 'raf') {
            form.action = `{{ url('caisse/approuver-raf') }}/${demandeId}`;
            if (action === 'approuver') {
                title.textContent = 'Approuver la demande (RAF)';
                submitBtn.textContent = 'Approuver';
                submitBtn.className = 'btn btn-success';
            } else {
                title.textContent = 'Rejeter la demande (RAF)';
                submitBtn.textContent = 'Rejeter';
                submitBtn.className = 'btn btn-danger';
            }
        }
        
        actionInput.value = action;
        document.getElementById('commentaire').value = '';
        modal.show();
    }
</script>
@endpush
@endsection