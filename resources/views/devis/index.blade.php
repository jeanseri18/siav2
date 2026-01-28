@extends('layouts.app')

@section('title', 'Liste des Devis')
@section('page-title', 'Liste des Devis')

@section('breadcrumb')
<li class="breadcrumb-item active">Devis</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-invoice me-2"></i>Liste des Devis
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('devis.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Nouveau Devis
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="app-alert app-alert-success">
            <div class="app-alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="app-alert-content">
                <div class="app-alert-text">{{ session('success') }}</div>
            </div>
            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table display">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Créé par</th>
                        <th>Total HT</th>
                        <th>TVA (18%)</th>
                        <th>Total TTC</th>
                        <th>Statut</th>
                        <th>Utilisé</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($devis as $devisItem)
                    <tr>
                        <td>
                            <a href="{{ route('devis.show', $devisItem->id) }}" class="app-btn-link">
                                <strong>{{ $devisItem->ref_devis ?? '#' . $devisItem->id }}</strong>
                            </a>
                        </td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <span>{{ $devisItem->client->nom ?? $devisItem->client->nom_raison_sociale ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>{{ $devisItem->created_at->format('d/m/Y') }}</td>
                        <td>{{ $devisItem->user ? $devisItem->user->name : 'Utilisateur inconnu' }}</td>
                        <td>{{ number_format($devisItem->total_ht, 0, ',', ' ') }} FCFA</td>
                        <td>{{ number_format($devisItem->calculerTVA(), 0, ',', ' ') }} FCFA</td>
                        <td><strong>{{ number_format($devisItem->calculerTotalTTC(), 0, ',', ' ') }} FCFA</strong></td>
                        <td>
                            <span class="app-badge app-badge-{{ $devisItem->statut == 'Validé' ? 'success' : ($devisItem->statut == 'En attente' ? 'warning' : 'secondary') }} app-badge-pill">
                                <i class="fas fa-{{ $devisItem->statut == 'Validé' ? 'check' : ($devisItem->statut == 'En attente' ? 'clock' : 'times') }} me-1"></i>
                                {{ $devisItem->statut }}
                            </span>
                        </td>
                        <td>
                            @if($devisItem->utilise_pour_vente)
                                <span class="app-badge app-badge-info app-badge-pill">
                                    <i class="fas fa-check me-1"></i>Utilisé
                                </span>
                            @else
                                <span class="app-badge app-badge-light app-badge-pill">
                                    <i class="fas fa-circle me-1"></i>Disponible
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="app-btn app-btn-secondary app-btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('devis.show', $devisItem->id) }}">
                                            <i class="fas fa-eye me-2"></i>Voir les détails
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item" onclick="printDevis({{ $devisItem->id }})">
                                            <i class="fas fa-print me-2"></i>Imprimer le devis
                                        </button>
                                    </li>
                                    @if(!$devisItem->utilise_pour_vente)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('devis.edit', $devisItem->id) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    @endif
                                    @if($devisItem->statut == 'En attente' && !$devisItem->utilise_pour_vente)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('devis.approve', $devisItem->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-success" style="border: none; background: none; cursor: pointer;">
                                                <i class="fas fa-check me-2"></i>Approuver
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('devis.reject', $devisItem->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; cursor: pointer;">
                                                <i class="fas fa-times me-2"></i>Rejeter
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @if(!$devisItem->utilise_pour_vente)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('devis.destroy', $devisItem->id) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger delete-btn" style="border: none; background: none;">
                                                <i class="fas fa-trash-alt me-2"></i>Supprimer
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
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
        // Configuration DataTable
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
        
        // Confirmation de suppression
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer ce devis ?')) {
                $(this).closest('form').submit();
            }
        });
        
        // Confirmation pour approbation/rejet
        $('form[action*="approve"], form[action*="reject"]').submit(function(e) {
            const action = $(this).attr('action').includes('approve') ? 'approuver' : 'rejeter';
            const message = `Êtes-vous sûr de vouloir ${action} ce devis ?`;
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
            // Permettre la soumission normale du formulaire
            return true;
        });
        
    });
    
    // Fonction pour imprimer le devis (définie dans le scope global)
    function printDevis(devisId) {
        // Ouvrir une nouvelle fenêtre avec le contenu du devis formaté pour l'impression
        const printWindow = window.open('{{ url("/devis") }}/' + devisId + '/print', '_blank', 'width=800,height=600');
        
        // Alternative : si vous ne voulez pas ouvrir une nouvelle fenêtre, vous pouvez utiliser :
        // window.open('{{ url("/devis") }}/' + devisId + '/print', '_blank');
    }
</script>
@endpush
@endsection