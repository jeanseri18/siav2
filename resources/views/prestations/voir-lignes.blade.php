@extends('layouts.app')

@section('title', 'Lignes de Prestation')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-eye me-2"></i>Lignes de Prestation
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('prestations.index') }}">Prestations</a></li>
                            <li class="breadcrumb-item active">Voir les lignes</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('prestations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de la prestation -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Informations de la prestation
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Titre :</strong> {{ $prestation->prestation_titre }}</p>
                    <p><strong>Contrat :</strong> {{ $prestation->contrat->nom_contrat ?? 'N/A' }}</p>
                    <p><strong>Corps de métier :</strong> {{ $prestation->corpMetier->nom ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Statut :</strong> 
                        <span class="badge 
                            @if($prestation->statut == 'En cours') bg-warning
                            @elseif($prestation->statut == 'Terminée') bg-success
                            @else bg-danger
                            @endif">
                            {{ $prestation->statut }}
                        </span>
                    </p>
                    <p><strong>Montant :</strong> {{ number_format($prestation->montant ?? 0, 0, ',', ' ') }} FCFA</p>
                    <p><strong>Prestataire :</strong> 
                        @if($prestation->artisan)
                            {{ $prestation->artisan->nom }} {{ $prestation->artisan->prenoms }}
                        @elseif($prestation->fournisseur)
                            {{ $prestation->fournisseur->nom_raison_sociale }}
                        @else
                            Non affecté
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lignes de prestation -->
    @if(!empty($lignesParHierarchie))
        <form action="{{ route('prestations.validerPaiements', $prestation->id) }}" method="POST">
            @csrf
            @foreach($lignesParHierarchie as $categorieData)
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-folder me-2"></i>{{ $categorieData['categorie']->nom }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($categorieData['sous_categories'] as $sousCategorieData)
                            <div class="mb-4">
                                <h6 class="bg-info text-white p-2 rounded">
                                    <i class="fas fa-folder-open me-2"></i>{{ $sousCategorieData['sous_categorie']->nom }}
                                </h6>
                                
                                @foreach($sousCategorieData['rubriques'] as $rubriqueData)
                                    <div class="ms-3 mb-3">
                                        <div class="bg-light p-2 mb-2 rounded">
                                            <strong>
                                                <i class="fas fa-file-alt me-2 text-secondary"></i>
                                                {{ $rubriqueData['rubrique']->nom }}
                                            </strong>
                                        </div>
                                        
                                        <!-- Tableau des lignes -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 4%;">ID</th>
                                                        <th style="width: 20%;">Désignation</th>
                                                        <th style="width: 6%;">Unité</th>
                                                        <th style="width: 7%;">Quantité</th>
                                                        <th style="width: 9%;">Coût unitaire</th>
                                                        <th style="width: 9%;">Montant</th>
                                                        <th style="width: 7%;">Taux avanc.</th>
                                                        <th style="width: 8%;">Taux paiement</th>
                                                        <th style="width: 10%;">Montant payé</th>
                                                        <th style="width: 10%;">Montant reste</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalMontant = 0;
                                                        $totalPaye = 0;
                                                        $totalReste = 0;
                                                    @endphp
                                                    
                                                    @foreach($rubriqueData['lignes'] as $ligne)
                                                        @php
                                                            $totalMontant += $ligne->montant;
                                                            $totalPaye += $ligne->montant_paye;
                                                            $totalReste += $ligne->montant_reste;
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $ligne->id }}</td>
                                                            <td>{{ $ligne->designation }}</td>
                                                            <td>{{ $ligne->unite }}</td>
                                                            <td class="text-end">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                                            <td class="text-end">{{ number_format($ligne->cout_unitaire, 2, ',', ' ') }}</td>
                                                            <td class="text-end">
                                                                <strong>{{ number_format($ligne->montant, 2, ',', ' ') }}</strong>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-info">{{ number_format($ligne->taux_avancement, 2) }}%</span>
                                                            </td>
                                                            <td>
                                                                <input type="number" 
                                                                       class="form-control form-control-sm" 
                                                                       name="paiements[{{ $ligne->id }}]" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       max="100"
                                                                       placeholder="%" 
                                                                       style="max-width: 80px;">
                                                            </td>
                                                            <td class="text-end text-success">
                                                                {{ number_format($ligne->montant_paye, 2, ',', ' ') }}
                                                            </td>
                                                            <td class="text-end text-danger">
                                                                {{ number_format($ligne->montant_reste, 2, ',', ' ') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    
                                                    <!-- Total de la rubrique -->
                                                    <tr class="table-secondary fw-bold">
                                                        <td colspan="5" class="text-end">Total {{ $rubriqueData['rubrique']->nom }} :</td>
                                                        <td class="text-end">{{ number_format($totalMontant, 2, ',', ' ') }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-end text-success">{{ number_format($totalPaye, 2, ',', ' ') }}</td>
                                                        <td class="text-end text-danger">{{ number_format($totalReste, 2, ',', ' ') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            
            <!-- Bouton Valider -->
            <div class="text-end">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Valider les paiements
                </button>
            </div>
        </form>
    @else
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucune ligne de prestation trouvée pour cette prestation.
                    <a href="{{ route('prestations.lignes', $prestation->id) }}" class="alert-link">Créer des lignes</a>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des décomptes -->
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-invoice-dollar me-2"></i>Décomptes de la prestation
            </h5>
        </div>
        <div class="card-body">
            @php
                $decomptes = \App\Models\Decompte::where('id_prestation', $prestation->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            @endphp
            
            @if($decomptes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">N°</th>
                                <th style="width: 27%;">Titre</th>
                                <th style="width: 15%;">Date</th>
                                <th style="width: 15%;">Pourcentage</th>
                                <th style="width: 20%;">Montant</th>
                                <th style="width: 15%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalDecomptes = 0; @endphp
                            @foreach($decomptes as $index => $decompte)
                                @php $totalDecomptes += $decompte->montant; @endphp
                                <tr>
                                    <td class="text-center">Décompte {{ $index + 1 }}</td>
                                    <td>{{ $decompte->titre }}</td>
                                    <td>{{ $decompte->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ number_format($decompte->pourcentage, 2) }}%</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($decompte->montant, 2, ',', ' ') }} FCFA</strong>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('prestations.voirDecompte', [$prestation->id, $decompte->id]) }}" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            
                            <!-- Total -->
                            <tr class="table-success fw-bold">
                                <td colspan="4" class="text-end">Total des décomptes :</td>
                                <td class="text-end">{{ number_format($totalDecomptes, 2, ',', ' ') }} FCFA</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucun décompte créé pour cette prestation.
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
