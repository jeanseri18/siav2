@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails du Bénéfice Parent - {{ $parent->reference }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('contrats.benefice.index', $contrat) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                              
                                    <p><strong>Référence:</strong> {{ $parent->reference }}</p>
                                    <p><strong>Type:</strong> {{ $parent->type }}</p>
                                    <p><strong>DQE:</strong> 
                                        @if($parent->dqe)
                                            {{ $parent->dqe->reference }} - {{ $parent->dqe->designation }}
                                        @else
                                            <span class="text-muted">Non défini</span>
                                        @endif
                                    </p>
                                    <p><strong>Statut:</strong> 
                                        <span class="badge badge-{{ $parent->statut === 'validé' ? 'success' : ($parent->statut === 'en_attente' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($parent->statut) }}
                                        </span>
                                    </p>
                              
                        </div>
                        <div class="col-md-6">
                           
                                    <p><strong>Montant Total:</strong> {{ number_format($parent->montant_total, 2, ',', ' ') }} F CFA</p>
                                    <p><strong>Date de création:</strong> {{ $parent->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Date de modification:</strong> {{ $parent->updated_at->format('d/m/Y H:i') }}</p>
                          
                        </div>
                    </div>


                            @if($parent->lignes->isEmpty())
                                <div class="p-4 text-center">
                                    <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Aucune ligne de bénéfice pour ce parent.</p>
                                </div>
                            @else
                                @php
                                    // Organiser les lignes hiérarchiquement
                                    $lignesParCategorie = [];
                                    $lignesParCategorieCount = [];
                                    $lignesParSousCategorieCount = [];
                                    foreach($parent->lignes as $ligne) {
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
                                    <table class="table table-striped mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Catégorie</th>
                                                <th>Sous-catégorie</th>
                                                <th>Rubrique</th>
                                                <th>Désignation</th>
                                                <th>Unité</th>
                                                <th>Qté</th>
                                                <th>P.U. HT</th>
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
                                                <th colspan="7" class="text-right">Total des lignes:</th>
                                                <th>{{ number_format($parent->lignes->sum('montant_ht'), 2, ',', ' ') }} F CFA</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                    
            </div>
        </div>
    </div>
</div>
@endsection