@extends('layouts.app')

@section('content')
<div class="app-content pt-3 p-md-3 p-lg-4">
    <div class="container-xl">
        
        <!-- Messages Flash -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
                <h1 class="app-page-title mb-0">Facture de Décompte #{{ $factureDecompte->numero }}</h1>
            </div>
            <div class="col-auto">
                <div class="page-utilities">
                    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                        <div class="col-auto">
                            <a href="{{ route('facture-contrat.show', $factureDecompte->facture_contrat_id) }}" class="app-btn app-btn-secondary app-btn-icon">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                        <div class="col-auto">
                            <button onclick="printFacture()" class="app-btn app-btn-primary app-btn-icon">
                                <i class="fas fa-print"></i> Imprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card app-card-accordion shadow-sm mb-4">
            <div class="app-card-header p-3">
                <h5 class="card-title mb-0">Informations Générales</h5>
            </div>
            <div class="app-card-body p-3">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Numéro:</strong> {{ $factureDecompte->numero }}
                    </div>
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ $factureDecompte->date_facture->format('d/m/Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Pourcentage d'Avancement:</strong> {{ number_format($factureDecompte->pourcentage_avancement, 2, ',', ' ') }} %
                    </div>
                    <div class="col-md-3">
                        <strong>Statut:</strong>
                        <span class="badge bg-{{ $factureDecompte->statut === 'valide' ? 'success' : ($factureDecompte->statut === 'annule' ? 'danger' : 'warning') }}">
                            {{ ucfirst($factureDecompte->statut) }}
                        </span>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <strong>Contrat:</strong> {{ $factureDecompte->factureContrat->dqe->contrat->numero_contrat }}
                    </div>
                    <div class="col-md-4">
                        <strong>Client:</strong> {{ $factureDecompte->factureContrat->dqe->contrat->client->nom }}
                    </div>
                    <div class="col-md-4">
                        <strong>Montant Restant:</strong> {{ number_format(($factureDecompte->factureContrat->montant_a_payer ?? 0) - ($factureDecompte->factureContrat->montant_verse ?? 0), 2, ',', ' ') }} F CFA
                    </div>
                </div>
                @if($factureDecompte->observations)
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Observations:</strong><br>
                        {{ $factureDecompte->observations }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="app-card app-card-accordion shadow-sm mb-4">
            <div class="app-card-header p-3">
                <h5 class="card-title mb-0">Récapitulatif Financier</h5>
            </div>
            <div class="app-card-body p-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body text-center">
                                <h6 class="card-title text-primary">Montant HT</h6>
                                <br>
                                <h4 class="text-primary">{{ number_format($factureDecompte->montant_ht, 2, ',', ' ') }} F CFA</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body text-center">
                                <h6 class="card-title text-info">Montant TTC</h6>
                                 <br>
                                <h4 class="text-info">{{ number_format($factureDecompte->montant_ttc, 2, ',', ' ') }} F CFA</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h6 class="card-title text-success">Montant Total de la Période</h6>
                               <br>  <h4 class="text-success">{{ number_format($factureDecompte->montant_ht, 2, ',', ' ') }} F CFA</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body text-center">
                                <h6 class="card-title text-warning">Montant Versé Total</h6>
                               <br>  <h4 class="text-warning">{{ number_format($factureDecompte->factureContrat->montant_verse, 2, ',', ' ') }} F CFA</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card app-card-accordion shadow-sm mb-4">
            <div class="app-card-header p-3">
                <h5 class="card-title mb-0">Détail des Lignes DQE</h5>
            </div>
            <div class="app-card-body p-3">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Code</th>
                                <th>Désignation</th>
                                <th>Unité</th>
                                <th>Quantité Contractuelle</th>
                                <th>Prix Unitaire HT</th>
                                <th>Quantité Réalisée</th>
                                <th>% Réalisation</th>
                                <th>Montant Réalisé HT</th>
                                <th>Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($factureDecompte->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->dqeLigne->code }}</td>
                                    <td>{{ $ligne->dqeLigne->designation }}</td>
                                    <td>{{ $ligne->dqeLigne->unite }}</td>
                                    <td>{{ number_format($ligne->dqeLigne->quantite, 2, ',', ' ') }}</td>
                                    <td>{{ number_format($ligne->dqeLigne->prix_unitaire_ht, 2, ',', ' ') }} F CFA</td>
                                    <td>{{ number_format($ligne->quantite_realisee, 2, ',', ' ') }}</td>
                                    <td>{{ number_format($ligne->pourcentage_realise, 2, ',', ' ') }} %</td>
                                    <td><strong>{{ number_format($ligne->montant_ht, 2, ',', ' ') }} F CFA</strong></td>
                                    <td>{{ $ligne->observations ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Aucune ligne dans cette facture de décompte</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="7"><strong>TOTAL HT</strong></td>
                                <td><strong>{{ number_format($factureDecompte->montant_ht, 2, ',', ' ') }} F CFA</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="app-card app-card-accordion shadow-sm mb-4 d-print-none">
            <div class="app-card-header p-3">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="app-card-body p-3">
                <div class="row">
                    <div class="col-auto">
                        <a href="{{ route('facture-contrat.show', $factureDecompte->facture_contrat_id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la facture contrat
                        </a>
                    </div>
                    <div class="col-auto">
                        <button onclick="printFacture()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                    @if($factureDecompte->statut === 'brouillon')
                    <div class="col-auto">
                        <form action="{{ route('facture-decompte.valider', $factureDecompte->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Valider la facture
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .d-print-none { display: none !important; }
    .app-header, .app-sidebar { display: none !important; }
    .app-content { margin-left: 0 !important; }
}
</style>

<script>
function printFacture() {
    // Ouvrir une nouvelle fenêtre avec la vue d'impression
    const printWindow = window.open('{{ route("facture-decompte.print", $factureDecompte->id) }}', '_blank', 'width=800,height=600');
    
    // Attendre que la fenêtre se charge puis imprimer
    printWindow.onload = function() {
        printWindow.print();
    };
}
</script>
@endsection