@extends('layouts.app')

@section('content')
<div class="app-content pt-3 p-md-3 p-lg-4">
    <div class="container-xl">
        <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
                <h1 class="app-page-title mb-0">Créer une Facture de Décompte</h1>
            </div>
            <div class="col-auto">
                <div class="page-utilities">
                    <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                        <div class="col-auto">
                            <a href="{{ route('facture-contrat.show', $factureContrat->id) }}" class="app-btn app-btn-secondary app-btn-icon">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-card app-card-accordion shadow-sm mb-4">
            <div class="app-card-header p-3">
                <h5 class="card-title mb-0">Informations de la Facture Contrat</h5>
            </div>
            <div class="app-card-body p-3">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Numéro:</strong> {{ $factureContrat->dqe->contrat->numero_contrat ?? 'N/A' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Client:</strong> {{ $factureContrat->dqe->contrat->client->nom ?? $factureContrat->dqe->contrat->client->raison_sociale ?? 'Non défini' }}
                    </div>
                    <div class="col-md-3">
                        <strong>Montant Total HT:</strong> {{ number_format($factureContrat->dqe->montant_total_ht ?? 0, 2, ',', ' ') }} F CFA
                    </div>
                    <div class="col-md-3">
                        <strong>Montant à Payer:</strong> {{ number_format($factureContrat->montant_a_payer ?? 0, 2, ',', ' ') }} F CFA
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('facture-contrat.decompte.store', $factureContrat->id) }}" method="POST" id="decompteForm">
            @csrf
            
            {{-- Messages d'erreur --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="app-card app-card-accordion shadow-sm mb-4">
                <div class="app-card-header p-3">
                    <h5 class="card-title mb-0">Informations de la Facture de Décompte</h5>
                </div>
                <div class="app-card-body p-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="numero" class="form-label">Numéro de Facture</label>
                                <input type="text" class="form-control" id="numero" name="numero" value="FD-{{ date('Y') }}-{{ str_pad($factureContrat->facturesDecompte->count() + 1, 4, '0', STR_PAD_LEFT) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="date_facture" class="form-label">Date de Facture</label>
                                <input type="date" class="form-control" id="date_facture" name="date_facture" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="pourcentage_avancement" class="form-label">Pourcentage d'Avancement (%)</label>
                                <input type="number" class="form-control" id="pourcentage_avancement" name="pourcentage_avancement" min="0" max="100" step="0.01" value="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observations" class="form-label">Observations</label>
                                <textarea class="form-control" id="observations" name="observations" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-card app-card-accordion shadow-sm mb-4">
                <div class="app-card-header p-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Lignes DQE - Pourcentages de Réalisation</h5>
                    <button type="button" class="btn btn-sm btn-primary" onclick="applyGlobalPercentage()">
                        <i class="fas fa-percentage"></i> Appliquer un % global
                    </button>
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
                                    <th>Montant Total HT</th>
                                    <th>% Réalisé Cumulé</th>
                                    <th>% Réalisation</th>
                                    <th>Qté Réalisée</th>
                                    <th>Montant Réalisé HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lignesOrganisees as $categorieData)
                                    <tr class="table-warning">
                                        <td colspan="9"><strong>CATÉGORIE: {{ $categorieData['categorie']->nom ?? 'Sans catégorie' }}</strong></td>
                                    </tr>
                                    @foreach($categorieData['sousCategories'] as $sousCategorieData)
                                        <tr class="table-info">
                                            <td colspan="9" style="padding-left: 20px;"><strong>SOUS-CATÉGORIE: {{ $sousCategorieData['sousCategorie']->nom ?? 'Sans sous-catégorie' }}</strong></td>
                                        </tr>
                                        @foreach($sousCategorieData['rubriques'] as $rubriqueData)
                                            <tr class="table-light">
                                                <td colspan="9" style="padding-left: 40px;"><strong>RUBRIQUE: {{ $rubriqueData['rubrique']->nom ?? 'Sans rubrique' }}</strong></td>
                                            </tr>
                                            @foreach($rubriqueData['lignes'] as $ligne)
                                                <tr>
                                                    <td>{{ $ligne->code }}</td>
                                                    <td style="padding-left: 60px;">{{ $ligne->designation }}</td>
                                                    <td>{{ $ligne->unite->nom ?? '' }}</td>
                                                    <td>{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                                    <td>{{ number_format($ligne->pu_ht ?? 0, 2, ',', ' ') }} F CFA</td>
                                                    <td>{{ number_format($ligne->montant_ht ?? 0, 2, ',', ' ') }} F CFA</td>
                                                    <td>
                                                        @php
                                                            // Calculer le cumul des pourcentages déjà réalisés pour cette ligne
                                                            $cumulPourcentage = 0;
                                                            foreach($factureContrat->facturesDecompte as $decompte) {
                                                                if($decompte->statut === 'valide') {
                                                                    foreach($decompte->lignes as $ligneDecompte) {
                                                                        if($ligneDecompte->dqe_ligne_id == $ligne->id) {
                                                                            $cumulPourcentage += $ligneDecompte->pourcentage_realise;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        <span class="badge bg-info" id="cumul_{{ $ligne->id }}">{{ number_format($cumulPourcentage, 2, ',', ' ') }}%</span>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm percentage-input" 
                                                               name="lignes[{{ $ligne->id }}][pourcentage_realise]" 
                                                               min="0" max="100" step="0.01" value="0" 
                                                               data-cumul="{{ $cumulPourcentage }}"
                                                               onchange="updateLigneCalculations(this, {{ $ligne->quantite }}, {{ $ligne->pu_ht ?? 0 }})"
                                                               required>
                                                        <small class="text-muted">Max: {{ number_format(100 - $cumulPourcentage, 2, ',', ' ') }}%</small>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm" 
                                                               name="lignes[{{ $ligne->id }}][quantite_realisee]" 
                                                               min="0" step="0.01" value="0" 
                                                               onchange="updateFromQuantite(this, {{ $ligne->quantite }}, {{ $ligne->pu_ht ?? 0 }})"
                                                               required>
                                                    </td>
                                                    <td>
                                                        <span class="montant-realise" id="montant_{{ $ligne->id }}">0.00 F CFA</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td colspan="8"><strong>TOTAL GÉNÉRAL</strong></td>
                                    <td><strong><span id="totalGeneral">0.00 F CFA</span></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Générer la Facture de Décompte
                    </button>
                    <a href="{{ route('facture-contrat.show', $factureContrat->id) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateLigneCalculations(input, quantiteContractuelle, prixUnitaire) {
    const row = input.closest('tr');
    const quantiteInput = row.querySelector('input[name$="[quantite_realisee]"]');
    const montantSpan = row.querySelector('.montant-realise');
    
    const pourcentage = parseFloat(input.value) || 0;
    const cumulExistant = parseFloat(input.dataset.cumul) || 0;
    const pourcentageMax = 100 - cumulExistant;
    
    // Validation: s'assurer que le pourcentage ne dépasse pas le maximum autorisé
    if (pourcentage > pourcentageMax) {
        alert(`Le pourcentage de réalisation ne peut pas dépasser ${pourcentageMax.toFixed(2)}% (cumul existant: ${cumulExistant.toFixed(2)}%)`);
        input.value = pourcentageMax;
        return;
    }
    
    const quantiteRealisee = (quantiteContractuelle * pourcentage) / 100;
    const montantRealise = quantiteRealisee * prixUnitaire;
    
    quantiteInput.value = quantiteRealisee.toFixed(2);
    montantSpan.textContent = montantRealise.toFixed(2) + ' F CFA';
    
    updateTotalGeneral();
}

function updateFromQuantite(input, quantiteContractuelle, prixUnitaire) {
    const row = input.closest('tr');
    const percentageInput = row.querySelector('input[name$="[pourcentage_realise]"]');
    const montantSpan = row.querySelector('.montant-realise');
    
    const quantiteRealisee = parseFloat(input.value) || 0;
    let pourcentage = (quantiteRealisee / quantiteContractuelle) * 100;
    const cumulExistant = parseFloat(percentageInput.dataset.cumul) || 0;
    const pourcentageMax = 100 - cumulExistant;
    
    // Validation: s'assurer que le pourcentage ne dépasse pas le maximum autorisé
    if (pourcentage > pourcentageMax) {
        alert(`Le pourcentage de réalisation ne peut pas dépasser ${pourcentageMax.toFixed(2)}% (cumul existant: ${cumulExistant.toFixed(2)}%)`);
        pourcentage = pourcentageMax;
        quantiteRealisee = (quantiteContractuelle * pourcentage) / 100;
        input.value = quantiteRealisee.toFixed(2);
    }
    
    const montantRealise = quantiteRealisee * prixUnitaire;
    
    percentageInput.value = pourcentage.toFixed(2);
    montantSpan.textContent = montantRealise.toFixed(2) + ' F CFA';
    
    updateTotalGeneral();
}

function updateTotalGeneral() {
    let total = 0;
    let totalPoids = 0;
    let sommePonderee = 0;
    
    // Calculer le montant total réalisé
    document.querySelectorAll('.montant-realise').forEach(function(span) {
        const montant = parseFloat(span.textContent.replace(' F CFA', '').replace(',', '.')) || 0;
        total += montant;
    });
    
    // Calculer le pourcentage d'avancement global basé sur une moyenne pondérée
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
        const montantCell = row.querySelector('td:nth-child(6)'); // Colonne Montant Total HT
        const percentageInput = row.querySelector('input[name$="[pourcentage_realise]"]');
        
        if (montantCell && percentageInput && !row.classList.contains('table-warning') && !row.classList.contains('table-info') && !row.classList.contains('table-light')) {
            const montantText = montantCell.textContent.replace(' F CFA', '').replace(/[\s,]/g, '');
            const montant = parseFloat(montantText) || 0;
            const pourcentage = parseFloat(percentageInput.value) || 0;
            
            // Utiliser le montant de la ligne comme pondération
            totalPoids += montant;
            sommePonderee += montant * pourcentage;
        }
    });
    
    // Calculer le pourcentage d'avancement global (moyenne pondérée)
    let pourcentageGlobal = 0;
    if (totalPoids > 0) {
        pourcentageGlobal = sommePonderee / totalPoids;
    }
    
    // Mettre à jour le champ pourcentage d'avancement
    document.getElementById('pourcentage_avancement').value = pourcentageGlobal.toFixed(2);
    
    document.getElementById('totalGeneral').textContent = total.toFixed(2) + ' F CFA';
}

function applyGlobalPercentage() {
    const globalPercentage = prompt('Entrez le pourcentage global à appliquer (0-100):', '0');
    if (globalPercentage !== null && !isNaN(globalPercentage)) {
        const percentage = parseFloat(globalPercentage);
        if (percentage >= 0 && percentage <= 100) {
            document.querySelectorAll('input[name$="[pourcentage_realise]"]').forEach(function(input) {
                const cumulExistant = parseFloat(input.dataset.cumul) || 0;
                const pourcentageMax = 100 - cumulExistant;
                const pourcentageAAppliquer = Math.min(percentage, pourcentageMax);
                
                if (pourcentageAAppliquer < percentage) {
                    alert(`Pourcentage réduit à ${pourcentageAAppliquer.toFixed(2)}% pour cette ligne (max autorisé: ${pourcentageMax.toFixed(2)}%)`);
                }
                
                input.value = pourcentageAAppliquer.toFixed(2);
                const row = input.closest('tr');
                const quantiteContractuelle = parseFloat(row.querySelector('td:nth-child(4)').textContent.replace(',', '.'));
                const prixUnitaire = parseFloat(row.querySelector('td:nth-child(5)').textContent.replace(' F CFA', '').replace(/[\s,]/g, ''));
                updateLigneCalculations(input, quantiteContractuelle, prixUnitaire);
            });
        } else {
            alert('Le pourcentage doit être entre 0 et 100');
        }
    }
}

// Calcul initial
updateTotalGeneral();

// Vérifier la soumission du formulaire
document.getElementById('decompteForm').addEventListener('submit', function(e) {
    // Vérifier qu'au moins une ligne a un pourcentage > 0
    const pourcentageInputs = document.querySelectorAll('input[name$="[pourcentage_realise]"]');
    let hasValidPercentage = false;
    
    pourcentageInputs.forEach(function(input) {
        if (parseFloat(input.value) > 0) {
            hasValidPercentage = true;
        }
    });
    
    if (!hasValidPercentage) {
        e.preventDefault();
        alert('Veuillez saisir au moins un pourcentage de réalisation supérieur à 0.');
        return false;
    }
    
    console.log('Formulaire de décompte en cours de soumission...');
});
</script>
@endpush

<style>
.percentage-input {
    width: 80px;
}
</style>
@endsection