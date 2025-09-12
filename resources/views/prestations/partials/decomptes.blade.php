<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="fas fa-calculator me-2"></i>
            Décomptes de la prestation : {{ $prestation->prestation_titre }}
        </h5>
    </div>
</div>

<!-- Résumé par type de compte -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h6 class="card-title">Matériel</h6>
                <h4 class="text-primary">{{ number_format($totauxParType['materiel'], 0, ',', ' ') }}</h4>
                <small class="text-muted">FCFA</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="card-title">Main d'œuvre</h6>
                <h4 class="text-success">{{ number_format($totauxParType['main_oeuvre'], 0, ',', ' ') }}</h4>
                <small class="text-muted">FCFA</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h6 class="card-title">Transport</h6>
                <h4 class="text-warning">{{ number_format($totauxParType['transport'], 0, ',', ' ') }}</h4>
                <small class="text-muted">FCFA</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <h6 class="card-title">Autres</h6>
                <h4 class="text-info">{{ number_format($totauxParType['autres'], 0, ',', ' ') }}</h4>
                <small class="text-muted">FCFA</small>
            </div>
        </div>
    </div>
</div>

<!-- Total général -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-body text-center">
                <h5 class="card-title">Total Général</h5>
                <h2 class="text-warning">{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</h2>
                @if($prestation->montant > 0)
                    @php
                        $pourcentageBudget = ($totalGeneral / $prestation->montant) * 100;
                    @endphp
                    <p class="mb-0">
                        {{ number_format($pourcentageBudget, 1) }}% du budget total ({{ number_format($prestation->montant, 0, ',', ' ') }} FCFA)
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Liste détaillée des comptes -->
@if($comptes->count() > 0)
    <div class="row">
        <div class="col-12">
            <h6 class="mb-3">Détail des comptes</h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-end">Montant</th>
                            <!-- <th>Créé par</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comptes as $compte)
                            <tr>
                                <td>{{ $compte->date_compte->format('d/m/Y') }}</td>
                                <td>
                                    @switch($compte->type_compte)
                                        @case('materiel')
                                            <span class="badge bg-primary">Matériel</span>
                                            @break
                                        @case('main_oeuvre')
                                            <span class="badge bg-success">Main d'œuvre</span>
                                            @break
                                        @case('transport')
                                            <span class="badge bg-warning">Transport</span>
                                            @break
                                        @case('autres')
                                            <span class="badge bg-info">Autres</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>{{ $compte->description }}</td>
                                <td class="text-end fw-bold">{{ number_format($compte->montant, 0, ',', ' ') }} FCFA</td>
                                <!-- <td>
                                    <small class="text-muted">
                                        {{ $compte->createdBy->name ?? 'Utilisateur supprimé' }}
                                    </small>
                                </td> -->
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="3" class="text-end">Total :</th>
                            <th class="text-end">{{ number_format($totalGeneral, 0, ',', ' ') }} FCFA</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h5>Aucun compte enregistré</h5>
                <p class="mb-0">Cette prestation n'a encore aucune dépense enregistrée.</p>
            </div>
        </div>
    </div>
@endif

<!-- Graphique de répartition (optionnel) -->
@if($comptes->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <h6 class="mb-3">Répartition des dépenses</h6>
            <div class="progress" style="height: 30px;">
                @if($totauxParType['materiel'] > 0)
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ ($totauxParType['materiel'] / $totalGeneral) * 100 }}%"
                         title="Matériel: {{ number_format($totauxParType['materiel'], 0, ',', ' ') }} FCFA">
                        @if(($totauxParType['materiel'] / $totalGeneral) * 100 > 10)
                            Matériel
                        @endif
                    </div>
                @endif
                @if($totauxParType['main_oeuvre'] > 0)
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ ($totauxParType['main_oeuvre'] / $totalGeneral) * 100 }}%"
                         title="Main d'œuvre: {{ number_format($totauxParType['main_oeuvre'], 0, ',', ' ') }} FCFA">
                        @if(($totauxParType['main_oeuvre'] / $totalGeneral) * 100 > 10)
                            Main d'œuvre
                        @endif
                    </div>
                @endif
                @if($totauxParType['transport'] > 0)
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: {{ ($totauxParType['transport'] / $totalGeneral) * 100 }}%"
                         title="Transport: {{ number_format($totauxParType['transport'], 0, ',', ' ') }} FCFA">
                        @if(($totauxParType['transport'] / $totalGeneral) * 100 > 10)
                            Transport
                        @endif
                    </div>
                @endif
                @if($totauxParType['autres'] > 0)
                    <div class="progress-bar bg-info" role="progressbar" 
                         style="width: {{ ($totauxParType['autres'] / $totalGeneral) * 100 }}%"
                         title="Autres: {{ number_format($totauxParType['autres'], 0, ',', ' ') }} FCFA">
                        @if(($totauxParType['autres'] / $totalGeneral) * 100 > 10)
                            Autres
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif