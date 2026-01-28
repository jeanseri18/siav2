@extends('layouts.app')



@section('content')

@include('sublayouts.contrat')



<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">Détails du Déboursé Chantier - {{ $parent->ref }}</h3>

                    <div class="card-tools">

                        <a href="{{ route('contrats.debourse-chantier.index', $contrat) }}" class="btn btn-secondary">

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

                            <strong>DQE:</strong>

                            <p>

                                @if($parent->dqe)

                                    {{ $parent->dqe->reference }}

                                @else

                                    <span class="text-muted">Non défini</span>

                                @endif

                            </p>

                        </div>

                        <div class="col-md-3">

                            <strong>Statut:</strong>

                            <p>

                                @if($parent->statut === \App\Models\DebourseChantierParent::STATUT_BROUILLON)

                                    <span class="badge badge-warning">Brouillon</span>

                                @elseif($parent->statut === \App\Models\DebourseChantierParent::STATUT_VALIDE)

                                    <span class="badge badge-success">Validé</span>

                                @elseif($parent->statut === \App\Models\DebourseChantierParent::STATUT_REFUSE)

                                    <span class="badge badge-danger">Refusé</span>

                                @endif

                            </p>

                        </div>

                        <div class="col-md-3">

                            <strong>Date de création:</strong>

                            <p>{{ $parent->created_at ? $parent->created_at->format('d/m/Y H:i') : 'Non définie' }}</p>

                        </div>

                    </div>



                    <div class="row mb-4">

                        <div class="col-md-6">

                            <strong>Montant Total:</strong>

                            <h4>{{ number_format($parent->montant_total, 2, ',', ' ') }} F CFA</h4>

                        </div>

                        <div class="col-md-6">

                            <strong>Dernière mise à jour:</strong>

                            <p>{{ $parent->updated_at ? $parent->updated_at->format('d/m/Y H:i') : 'Non définie' }}</p>

                        </div>

                    </div>



                   

                    @if($parent->dqe && $parent->dqe->lignes && $parent->dqe->lignes->count() > 0)

                    <div class="row mb-4">

                        <div class="col-12">

                            <div class="card">

                                <div class="card-header bg-light">

                                    <h5 class="card-title mb-0">Structure du DQE - Lignes de déboursé</h5>

                                </div>

                                <div class="card-body">

                                    <div class="table-responsive">

                                        <table class="table  table-bordered">

                                            <thead class="thead-light">

                                                <tr>

             

                                                    <th>Désignation</th>

                                                    <th>Unité</th>

                                                    <th>Qté</th>

                                                    <th>PU HT</th>

                                                    <th>Montant HT</th>

                                                    <th>Actions</th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                @php

                                                    // Récupérer toutes les lignes de déboursé existantes

                                                    $lignesDebourse = $parent->lignes ?? collect();

                                                   

                                                    // Grouper les lignes du DQE par catégorie

                                                    $groupedLignes = $parent->dqe->lignes->groupBy(function($ligne) {

                                                        return $ligne->rubrique->sousCategorie->categorie->id ?? 0;

                                                    });

                                                   
                                                @endphp
                                               
                                                @foreach($groupedLignes as $categorieId => $lignesByCategorie)

                                                    @php

                                                        $firstLigne = $lignesByCategorie->first();

                                                        $categorie = $firstLigne->rubrique->sousCategorie->categorie ?? null;

                                                    @endphp

                                                    @if($categorie)

                                                        {{-- Ligne de catégorie avec colspan 9 --}}

                                                        <tr style="background-color: #037DF8FF;">

                                                            <td colspan="6"><strong>{{ $categorie->nom }}</strong></td>

                                                        </tr>

                                                        

                                                        @php

                                                            $groupedBySousCategorie = $lignesByCategorie->groupBy(function($ligne) {

                                                                return $ligne->rubrique->sousCategorie->id ?? 0;

                                                            });

                                                        @endphp

                                                        @foreach($groupedBySousCategorie as $sousCategorieId => $lignesBySousCategorie)

                                                            @php

                                                                $firstLigneSousCategorie = $lignesBySousCategorie->first();

                                                                $sousCategorie = $firstLigneSousCategorie->rubrique->sousCategorie ?? null;

                                                            @endphp

                                                            @if($sousCategorie)

                                                                {{-- Ligne de sous-catégorie avec colspan 9 --}}

                                                                <tr style="background-color: #05488AFF;">

                                                                    <td colspan="6"><strong>{{ $sousCategorie->nom }}</strong></td>

                                                                </tr>

                                                                

                                                                @php

                                                                    // Grouper les lignes DQE par rubrique pour éviter les doublons

                                                                    $groupedByRubrique = $lignesBySousCategorie->groupBy(function($ligneDqe) {

                                                                        return $ligneDqe->rubrique->id ?? $ligneDqe->id;

                                                                    });

                                                                @endphp

                                                                @foreach($groupedByRubrique as $rubriqueId => $lignesByRubrique)

                                                                    @php

                                                                        $rubrique = $lignesByRubrique->first()->rubrique ?? null;

                                                                    @endphp

                                                                    @if($rubrique)

                                                                        {{-- Ligne de rubrique avec colspan 9 --}}

                                                                        <tr style="background-color: #012D42FF;">

                                                                            <td colspan="6"><strong>{{ $rubrique->nom }}</strong></td>

                                                                        </tr>

                                                                        

                                                                        @php

                                                                            // Trouver les lignes de déboursé pour cette rubrique

                                                                            $lignesDebourseRubrique = $lignesDebourse->where('rubrique_id', $rubriqueId);

                                                                            $lignesCount = $lignesDebourseRubrique->count();

                                                                        @endphp

                                                                        @if($lignesCount > 0)

                                                                            {{-- Afficher les lignes de déboursé existantes --}}

                                                                            @foreach($lignesDebourseRubrique as $ligneDebourse)

                                                                                <tr>

                                                                                    <td>{{ $ligneDebourse->designation }}</td>

                                                                                    <td>{{ $ligneDebourse->unite }}</td>

                                                                                    <td>{{ number_format($ligneDebourse->quantite, 2, ',', ' ') }}</td>

                                                                                    <td>{{ number_format($ligneDebourse->pu_ht, 2, ',', ' ') }}</td>

                                                                                    <td>{{ number_format($ligneDebourse->montant_ht, 2, ',', ' ') }}</td>

                                                                                    <td>


                                                                                        <button type="button" class="btn btn-sm btn-danger delete-ligne-btn" data-ligne-id="{{ $ligneDebourse->id }}">Supprimer</button>

                                                                                    </td>

                                                                                </tr>

                                                                            @endforeach

                                                                        @else

                                                                            {{-- Aucune ligne de déboursé n'existe --}}

                                                                            <tr>

                                                                                <td colspan="6" class="text-center text-muted">Aucune ligne de déboursé</td>

                                                                            

                                                                            </tr>

                                                                        @endif

                                                                        

                                                                        {{-- Formulaire d'ajout --}}

                                                                        <tr id="add-form-{{ $rubriqueId }}" style="background-color: #f7f7f7;">

                                                                            <td>

                                                                                <select name="designation_visible" id="designation_{{ $rubriqueId }}" class="form-control form-control-sm article-select" data-rubrique-id="{{ $rubriqueId }}" required>

                                                                                    <option value="">Sélectionner un article</option>

                                                                                    @foreach(\App\Models\Article::with('uniteMesure')->get() as $article)

                                                                                        <option value="{{ $article->nom }}" data-reference="{{ $article->reference }}" data-unite="{{ $article->uniteMesure->nom ?? '' }}" data-prix="{{ $article->prix_unitaire }}">

                                                                                            {{ $article->reference }} - {{ $article->nom }}

                                                                                        </option>

                                                                                    @endforeach

                                                                                </select>

                                                                            </td>

                                                                            <td>

                                                                                <input type="text" name="unite_visible" id="unite_{{ $rubriqueId }}" class="form-control form-control-sm" placeholder="Unité" required>

                                                                            </td>

                                                                            <td>

                                                                                <input type="number" name="quantite_visible" id="quantite_{{ $rubriqueId }}" class="form-control form-control-sm calculate-input" step="0.01" min="0" data-rubrique-id="{{ $rubriqueId }}" placeholder="Quantité" required>

                                                                            </td>

                                                                            <td>

                                                                                <input type="number" name="pu_ht_visible" id="pu_ht_{{ $rubriqueId }}" class="form-control form-control-sm calculate-input" step="0.01" min="0" data-rubrique-id="{{ $rubriqueId }}" placeholder="PU HT" required>

                                                                            </td>

                                                                            <td>

                                                                                <input type="number" name="montant_ht_visible" id="montant_ht_{{ $rubriqueId }}" class="form-control form-control-sm" step="0.01" readonly placeholder="Montant HT">

                                                                            </td>

                                                                            <td>

                                                                                <button type="button" class="btn btn-sm btn-success save-ligne-btn" data-rubrique-id="{{ $rubriqueId }}">Enregistrer</button>

                                                                            </td>

                                                                        </tr>

                                                                    @endif

                                                                @endforeach

                                                            @endif

                                                        @endforeach

                                                    @endif

                                                @endforeach

                                                   
          
                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    @endif



                </div>

            </div>

        </div>

    </div>

</div>

@endsection



<hr>


<!-- 
## ⚙️ JavaScript Corrigé (`@section('scripts')`)



```javascript -->

@yield('scripts')

<script>

    /**

     * Calcule le Montant HT à partir de la Quantité et du Prix Unitaire HT

     * et met à jour les champs VUS.

     */

    function calculateMontantHT(rubriqueId) {

        const quantiteInput = document.getElementById('quantite_' + rubriqueId);

        const puHtInput = document.getElementById('pu_ht_' + rubriqueId);

        const montantHtInputVisible = document.getElementById('montant_ht_' + rubriqueId);

       

        const quantite = parseFloat(quantiteInput.value) || 0;

        const puHt = parseFloat(puHtInput.value) || 0;

        const montantHt = quantite * puHt;

       

        // Mettre à jour le champ visible

        montantHtInputVisible.value = montantHt.toFixed(2);

    }



    /**

     * Initialise les selects d'articles et gère la logique de remplissage des champs (unite, pu_ht).

     * Ceci assure le remplissage de l'Unité et du PU HT DÈS LA SÉLECTION.

     */

    function initializeArticleSelects() {

        document.querySelectorAll('.article-select').forEach(select => {

            const rubriqueId = select.getAttribute('data-rubrique-id');

           

            // Gérer la sélection d'un article avec l'événement change natif

            select.addEventListener('change', function() {

                const selectedOption = this.options[this.selectedIndex];



                // Récupérer les champs visibles

                const uniteInputVisible = document.getElementById('unite_' + rubriqueId);

                const puHtInputVisible = document.getElementById('pu_ht_' + rubriqueId);

               

                if (this.value === '') {

                    // Si aucun article n'est sélectionné, vider les champs (VUS)

                    uniteInputVisible.value = '';

                    puHtInputVisible.value = '';

                    document.getElementById('quantite_' + rubriqueId).value = '';

                } else {

                    // Sinon, remplir avec les données de l'article (VUS)

                    const unite = selectedOption.getAttribute('data-unite') || '';

                    const prix = selectedOption.getAttribute('data-prix') || 0;

                   

                    // CORRECTION: Ces lignes sont cruciales pour que l'unité se remplisse

                    uniteInputVisible.value = unite;

                    puHtInputVisible.value = prix;

                }

               

                // Recalculer le montant HT (met à jour le champ visible montant_ht)

                calculateMontantHT(rubriqueId);

            });

        });

    }



    document.addEventListener('DOMContentLoaded', function() {

       

        // 1. Initialiser les sélecteurs d'articles

        // Timeout maintenu pour être sûr que tout le DOM est prêt, même le Select2/JS

        setTimeout(function() {

            initializeArticleSelects();

        }, 100);



        // 2. Ajouter les écouteurs d'événements pour le calcul automatique

        document.querySelectorAll('.calculate-input').forEach(input => {

            input.addEventListener('input', function() {

                const rubriqueId = this.getAttribute('data-rubrique-id');

                calculateMontantHT(rubriqueId);

            });

        });



        // 3. Gestion de la suppression des lignes
        document.querySelectorAll('.delete-ligne-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const ligneId = this.getAttribute('data-ligne-id');
                
                if (confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')) {
                    // Désactiver le bouton pendant la requête
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
                    
                    // Construire l'URL correctement avec les paramètres
                    const url = '{{ route("contrats.debourse-chantier.lignes.destroy", ["contrat_id", "parent_id", "ligne_id"]) }}'
                        .replace('contrat_id', {{ $contrat->id }})
                        .replace('parent_id', {{ $parent->id }})
                        .replace('ligne_id', ligneId);
                    
                    // Envoyer la requête de suppression via AJAX
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Afficher un message de succès
                            toastr.success(data.message);
                            // Recharger la page pour mettre à jour l'affichage
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Afficher les erreurs
                            toastr.error('Erreur: ' + data.message);
                            // Réactiver le bouton
                            this.disabled = false;
                            this.innerHTML = 'Supprimer';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur AJAX:', error);
                        toastr.error('Erreur lors de la suppression. Veuillez réessayer.');
                        // Réactiver le bouton
                        this.disabled = false;
                        this.innerHTML = 'Supprimer';
                    });
                }
            });
        });

        // 4. Gestion de la soumission AJAX des lignes
        document.querySelectorAll('.save-ligne-btn').forEach(button => {

            button.addEventListener('click', function() {

                const rubriqueId = this.getAttribute('data-rubrique-id');

               

                // Récupérer les valeurs visibles

                const designationVisible = document.getElementById('designation_' + rubriqueId).value;

                const uniteVisible = document.getElementById('unite_' + rubriqueId).value;

                const quantiteVisible = document.getElementById('quantite_' + rubriqueId).value;

                const puHtVisible = document.getElementById('pu_ht_' + rubriqueId).value;

                const montantHtVisible = document.getElementById('montant_ht_' + rubriqueId).value;

               

                // Préparer les données pour AJAX

                const formData = {

                    rubrique_id: rubriqueId,

                    designation: designationVisible,

                    unite: uniteVisible,

                    quantite: quantiteVisible,

                    pu_ht: puHtVisible,

                    montant_ht: montantHtVisible,

                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')

                };

               

                // Validation côté client

                if (!designationVisible || !uniteVisible || !quantiteVisible || !puHtVisible) {

                    toastr.error('Veuillez remplir tous les champs requis.');

                    return;

                }

               

                if (parseFloat(quantiteVisible) <= 0 || parseFloat(puHtVisible) <= 0) {

                    toastr.error('La quantité et le prix unitaire doivent être supérieurs à 0.');

                    return;

                }

               

                // Désactiver le bouton pendant la requête

                this.disabled = true;

                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';

               

                // Envoyer les données via AJAX
                const storeUrl = '{{ route("contrats.debourse-chantier.lignes.store", ["contrat_id", "parent_id"]) }}'
                    .replace('contrat_id', {{ $contrat->id }})
                    .replace('parent_id', {{ $parent->id }});

                fetch(storeUrl, {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')

                    },

                    body: JSON.stringify(formData)

                })

                .then(response => response.json())

                .then(data => {

                    if (data.success) {

                        // Afficher un message de succès

                        toastr.success(data.message);

                        // Recharger la page pour afficher la nouvelle ligne après un court délai

                        setTimeout(() => {

                            window.location.reload();

                        }, 1000);

                    } else {

                        // Afficher les erreurs

                        toastr.error('Erreur: ' + data.message);

                        // Réactiver le bouton

                        this.disabled = false;

                        this.innerHTML = 'Enregistrer';

                    }

                })

                .catch(error => {

                    console.error('Erreur AJAX:', error);

                    toastr.error('Erreur lors de l\'enregistrement. Veuillez réessayer.');

                    // Réactiver le bouton

                    this.disabled = false;

                    this.innerHTML = 'Enregistrer';

                });

            });

        });

    });

</script>

@endyield