{{-- Page Index - Liste des Factures Contrat --}}
@extends('layouts.app')

@section('title', 'Gestion des Factures Contrat')
@section('page-title', 'Gestion des Factures Contrat')

@section('breadcrumb')
<li class="breadcrumb-item active">Factures Contrat</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-contract me-2"></i>Liste des Factures Contrat
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('dqe.index') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Générer une facture contrat
                </a>
            </div>
        </div>
        
        <div class="app-card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="facturesContratTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Référence DQE</th>
                            <th>Contrat</th>
                            <th>Montant à payer</th>
                            <th>Montant versé</th>
                            <th>Reste à payer</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturesContrat as $facture)
                        <tr>
                            <td>{{ $facture->id }}</td>
                            <td>
                                @if($facture->dqe)
                                    {{ $facture->dqe->reference }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($facture->dqe && $facture->dqe->contrat)
                                    {{ $facture->dqe->contrat->nom_contrat }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ number_format($facture->montant_a_payer, 2, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($facture->montant_verse, 2, ',', ' ') }} FCFA</td>
                            <td>
                                @php
                                    $reste = $facture->montant_a_payer - $facture->montant_verse;
                                    $class = $reste > 0 ? 'text-warning' : 'text-success';
                                @endphp
                                <span class="{{ $class }}">
                                    {{ number_format($reste, 2, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td>{{ $facture->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('facture-contrat.show', $facture->id) }}" class="btn btn-sm btn-info" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('facture-contrat.destroy', $facture->id) }}" method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture contrat ?')" 
                                          style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-info-circle me-2"></i>Aucune facture contrat trouvée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialiser DataTable
    $('#facturesContratTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json"
        },
        order: [[0, 'desc']], // Trier par ID décroissant
        pageLength: 25
    });

    // Confirmation de suppression
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        
        if (confirm('Êtes-vous sûr de vouloir supprimer cette facture contrat ?')) {
            $(this).closest('form').submit();
        }
    });
});
</script>
@endpush