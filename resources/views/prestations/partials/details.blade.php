<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Informations générales</h6>
        <table class="table table-borderless">
            <tr>
                <td class="fw-semibold">Titre :</td>
                <td>{{ $prestation->prestation_titre }}</td>
            </tr>
            <tr>
                <td class="fw-semibold">Contrat :</td>
                <td>{{ $prestation->contrat->nom ?? 'Non défini' }}</td>
            </tr>
            <tr>
                <td class="fw-semibold">Corps de métier :</td>
                <td>{{ $prestation->corpMetier->nom ?? 'Non défini' }}</td>
            </tr>
            <tr>
                <td class="fw-semibold">Montant :</td>
                <td class="text-success fw-bold">{{ number_format($prestation->montant, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="fw-semibold">Taux d'avancement :</td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $prestation->taux_avancement }}%" aria-valuenow="{{ $prestation->taux_avancement }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $prestation->taux_avancement }}%
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-semibold">Statut :</td>
                <td>
                    @if($prestation->statut == 'en_cours')
                        <span class="badge bg-warning">En cours</span>
                    @elseif($prestation->statut == 'termine')
                        <span class="badge bg-success">Terminé</span>
                    @elseif($prestation->statut == 'en_attente')
                        <span class="badge bg-secondary">En attente</span>
                    @else
                        <span class="badge bg-info">{{ ucfirst($prestation->statut) }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Artisan assigné</h6>
        @if($prestation->artisan)
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="card-title mb-2">
                        <i class="fas fa-user me-2"></i>
                        {{ $prestation->artisan->nom }} {{ $prestation->artisan->prenom }}
                    </h6>
                    
                    <p class="card-text mb-1">
                        <i class="fas fa-phone me-2"></i>
                        {{ $prestation->artisan->tel1 ?? 'Non renseigné' }}
                    </p>
                    <p class="card-text mb-1">
                        <i class="fas fa-envelope me-2"></i>
                        {{ $prestation->artisan->mail ?? 'Non renseigné' }}
                    </p>
                    <p class="card-text mb-0">
                        <i class="fas fa-tools me-2"></i>
                        {{ $prestation->artisan->fonction ?? 'Corps de métier non défini' }}
                    </p>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Aucun artisan assigné à cette prestation
            </div>
        @endif
        
        <h6 class="fw-bold mb-3 mt-4">Résumé financier</h6>
        @if($prestation->comptes->count() > 0)
            @php
                $totalComptes = $prestation->comptes->sum('montant');
                $pourcentageDepense = $prestation->montant > 0 ? ($totalComptes / $prestation->montant) * 100 : 0;
            @endphp
            <div class="card border-info">
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Total dépensé :</strong> 
                        <span class="text-danger">{{ number_format($totalComptes, 0, ',', ' ') }} FCFA</span>
                    </p>
                    <p class="mb-2">
                        <strong>Reste à dépenser :</strong> 
                        <span class="text-success">{{ number_format($prestation->montant - $totalComptes, 0, ',', ' ') }} FCFA</span>
                    </p>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ min($pourcentageDepense, 100) }}%">
                            {{ number_format($pourcentageDepense, 1) }}% du budget
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Aucune dépense enregistrée pour cette prestation
            </div>
        @endif
    </div>
</div>

@if($prestation->detail)
    <div class="row mt-4">
        <div class="col-12">
            <h6 class="fw-bold mb-3">Description détaillée</h6>
            <div class="card">
                <div class="card-body">
                    <p class="mb-0">{{ $prestation->detail }}</p>
                </div>
            </div>
        </div>
    </div>
@endif