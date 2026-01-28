@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails des Frais de Chantier - {{ $parent->ref }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('contrats.frais-chantier.index', $contrat) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>Référence:</strong>
                            <p>{{ $parent->ref }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Type:</strong>
                            <p>{{ ucfirst($parent->type) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>DQE:</strong>
                            <p>
                                @if($parent->dqe)
                                    {{ $parent->dqe->reference }} - {{ $parent->dqe->designation }}
                                @else
                                    <span class="text-muted">Non défini</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <strong>Statut:</strong>
                            <p>
                                @if($parent->statut === \App\Models\FraisChantierParent::STATUT_BROUILLON)
                                    <span class="badge badge-warning">Brouillon</span>
                                @elseif($parent->statut === \App\Models\FraisChantierParent::STATUT_VALIDE)
                                    <span class="badge badge-success">Validé</span>
                                @elseif($parent->statut === \App\Models\FraisChantierParent::STATUT_REFUSE)
                                    <span class="badge badge-danger">Refusé</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Montant Total:</strong>
                            <h4>{{ number_format($parent->montant_total, 2, ',', ' ') }} F CFA</h4>
                        </div>
                        <div class="col-md-6">
                            <strong>Date de création:</strong>
                            <p>{{ $parent->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($parent->fraisChantiers->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucune ligne de frais de chantier trouvée.
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Lignes des frais de chantier</h4>
                        @if($parent->type === 'réalisé')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLigneModal">
                                <i class="fas fa-plus"></i> Ajouter une ligne
                            </button>
                        @endif
                    </div>
                    
                    @if(!$parent->fraisChantiers->isEmpty())
                        @if($parent->type === 'réalisé')
                            {{-- Affichage direct des lignes pour les documents réalisés --}}
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Désignation</th>
                                            <th>Unité</th>
                                            <th>Qté</th>
                                            <th>P.U HT</th>
                                            <th>Montant HT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($parent->fraisChantiers as $ligne)
                                            <tr>
                                                <td>{{ $ligne->designation }}</td>
                                                <td>{{ $ligne->unite }}</td>
                                                <td>{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                                <td>{{ number_format($ligne->pu_ht, 2, ',', ' ') }}</td>
                                                <td>{{ number_format($ligne->montant_ht, 2, ',', ' ') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="4" class="text-right font-weight-bold">Total général :</td>
                                            <td class="font-weight-bold">{{ number_format($parent->fraisChantiers->sum('montant_ht'), 2, ',', ' ') }} F CFA</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            {{-- Affichage hiérarchique pour les autres types --}}
                            @php
                                // Organiser les lignes hiérarchiquement
                                $lignesParCategorie = [];
                                $lignesParCategorieCount = [];
                                $lignesParSousCategorieCount = [];
                                foreach($parent->fraisChantiers as $ligne) {
                                    $rubrique = $ligne->rubrique;
                                    if($rubrique) {
                                        $sousCategorie = $rubrique->sousCategorie;
                                        if($sousCategorie) {
                                            $categorie = $sousCategorie->categorie;
                                            if($categorie) {
                                                $lignesParCategorie[$categorie->nom][$sousCategorie->nom][$rubrique->nom][] = $ligne;
                                                
                                                // Compter les lignes par catégorie
                                                if (!isset($lignesParCategorieCount[$categorie->nom])) {
                                                    $lignesParCategorieCount[$categorie->nom] = 0;
                                                }
                                                $lignesParCategorieCount[$categorie->nom]++;
                                                
                                                // Compter les lignes par sous-catégorie
                                                $sousCategorieKey = $categorie->nom . '|' . $sousCategorie->nom;
                                                if (!isset($lignesParSousCategorieCount[$sousCategorieKey])) {
                                                    $lignesParSousCategorieCount[$sousCategorieKey] = 0;
                                                }
                                                $lignesParSousCategorieCount[$sousCategorieKey]++;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Sous-catégorie</th>
                                            <th>Rubrique</th>
                                            <th>Désignation</th>
                                            <th>Unité</th>
                                            <th>Qté</th>
                                            <th>P.U HT</th>
                                            <th>Montant HT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $currentCategorie = null;
                                            $currentSousCategorie = null;
                                            $currentRubrique = null;
                                        @endphp
                                        @foreach($lignesParCategorie as $categorieNom => $sousCategories)
                                            @foreach($sousCategories as $sousCategorieNom => $rubriques)
                                                @foreach($rubriques as $rubriqueNom => $lignes)
                                                    @foreach($lignes as $index => $ligne)
                                                        <tr>
                                                            @php
                                                                $showCategorie = ($currentCategorie !== $categorieNom);
                                                                $showSousCategorie = ($currentSousCategorie !== $sousCategorieNom) || $showCategorie;
                                                                $showRubrique = ($currentRubrique !== $rubriqueNom) || $showSousCategorie;
                                                                
                                                                if ($showCategorie) {
                                                                    $currentCategorie = $categorieNom;
                                                                    $currentSousCategorie = null;
                                                                    $currentRubrique = null;
                                                                }
                                                                if ($showSousCategorie) {
                                                                    $currentSousCategorie = $sousCategorieNom;
                                                                    $currentRubrique = null;
                                                                }
                                                                if ($showRubrique) {
                                                                    $currentRubrique = $rubriqueNom;
                                                                }
                                                            @endphp
                                                            @if($showCategorie)
                                                                <td rowspan="{{ $lignesParCategorieCount[$categorieNom] }}" class="font-weight-bold bg-light">{{ $categorieNom }}</td>
                                                            @endif
                                                            @if($showSousCategorie)
                                                                @php
                                                                    $sousCategorieKey = $categorieNom . '|' . $sousCategorieNom;
                                                                @endphp
                                                                <td rowspan="{{ $lignesParSousCategorieCount[$sousCategorieKey] }}" class="font-weight-bold">{{ $sousCategorieNom }}</td>
                                                            @endif
                                                            @if($showRubrique)
                                                                <td rowspan="{{ count($lignes) }}" class="font-italic">{{ $rubriqueNom }}</td>
                                                            @endif
                                                            <td>{{ $ligne->designation }}</td>
                                                            <td>{{ $ligne->unite }}</td>
                                                            <td>{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                                            <td>{{ number_format($ligne->pu_ht, 2, ',', ' ') }}</td>
                                                            <td>{{ number_format($ligne->montant_ht, 2, ',', ' ') }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="7" class="text-right font-weight-bold">Total général :</td>
                                            <td class="font-weight-bold">{{ number_format($parent->fraisChantiers->sum('montant_ht'), 2, ',', ' ') }} F CFA</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Modal pour ajouter une ligne -->
<div class="modal fade" id="addLigneModal" tabindex="-1" aria-labelledby="addLigneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLigneModalLabel">Ajouter une ligne de frais de chantier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addLigneForm">
                    @csrf
                    <div class="form-group">
                        <label for="designation">Désignation</label>
                        <input type="text" class="form-control" id="designation" name="designation" required>
                    </div>
                    <div class="form-group">
                        <label for="unite">Unité</label>
                        <input type="text" class="form-control" id="unite" name="unite" required>
                    </div>
                    <div class="form-group">
                        <label for="quantite">Quantité</label>
                        <input type="number" class="form-control" id="quantite" name="quantite" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="pu_ht">Prix unitaire HT</label>
                        <input type="number" class="form-control" id="pu_ht" name="pu_ht" step="0.01" min="0" required>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveLigneBtn">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {


    // Soumission du formulaire
    $('#saveLigneBtn').click(function() {
        var formData = $('#addLigneForm').serialize();
        
        $.ajax({
            url: '{{ route("contrats.frais-chantier.lignes.store", [$contrat, $parent]) }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    // Fermer le modal
                    $('#addLigneModal').modal('hide');
                    
                    // Afficher le message de succès
                    toastr.success(response.message);
                    
                    // Recharger la page pour afficher la nouvelle ligne
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                toastr.error(response.message || 'Une erreur est survenue.');
            }
        });
    });

    // Réinitialiser le formulaire quand le modal est fermé
    $('#addLigneModal').on('hidden.bs.modal', function() {
        $('#addLigneForm')[0].reset();
    });
    
    // Initialiser le modal Bootstrap 5
    const addLigneModal = new bootstrap.Modal(document.getElementById('addLigneModal'));
});
</script>
@endpush