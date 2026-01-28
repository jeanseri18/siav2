@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Demande de Ravitaillement - {{ $demandeRavitaillement->reference }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('demandes-ravitaillement.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                        
                        @if($demandeRavitaillement->statut === 'en_attente')
                            <a href="{{ route('demandes-ravitaillement.edit', $demandeRavitaillement) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif



                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informations générales</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Référence:</strong></td>
                                            <td>{{ $demandeRavitaillement->reference }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Objet:</strong></td>
                                            <td>{{ $demandeRavitaillement->objet }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                @php
                                                    $statusClass = match($demandeRavitaillement->statut) {
                                                        'en_attente' => 'warning',
                                                        'approuvee' => 'success',
                                                        'rejetee' => 'danger',
                                                        'en_cours' => 'info',
                                                        'livree' => 'primary',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">{{ $demandeRavitaillement->statut_label }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Priorité:</strong></td>
                                            <td>
                                                @php
                                                    $priorityClass = match($demandeRavitaillement->priorite) {
                                                        'urgente' => 'danger',
                                                        'haute' => 'warning',
                                                        'normale' => 'info',
                                                        'basse' => 'secondary',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $priorityClass }}">{{ $demandeRavitaillement->priorite_label }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date de demande:</strong></td>
                                            <td>{{ $demandeRavitaillement->date_demande ? $demandeRavitaillement->date_demande->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        @if($demandeRavitaillement->date_livraison_souhaitee)
                                            <tr>
                                                <td><strong>Date livraison souhaitée:</strong></td>
                                                <td>{{ $demandeRavitaillement->date_livraison_souhaitee->format('d/m/Y') }}</td>
                                            </tr>
                                        @endif
                                        @if($demandeRavitaillement->date_livraison_effective)
                                            <tr>
                                                <td><strong>Date livraison effective:</strong></td>
                                                <td>{{ $demandeRavitaillement->date_livraison_effective->format('d/m/Y') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Intervenants</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                                <td><strong>Contrat:</strong></td>
                                                <td>
                                                    @if($demandeRavitaillement->contrat)
                                                        {{ $demandeRavitaillement->contrat->ref_contrat }}
                                                        @if($demandeRavitaillement->contrat->client)
                                                            <br><small class="text-muted">{{ $demandeRavitaillement->contrat->client->nom_raison_sociale }} {{ $demandeRavitaillement->contrat->client->prenoms }}</small>
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Projet:</strong></td>
                                                <td>
                                                    @if($demandeRavitaillement->contrat && $demandeRavitaillement->contrat->projet)
                                                        {{ $demandeRavitaillement->contrat->projet->nom }}
                                                    @else
                                                        Aucun projet
                                                    @endif
                                                </td>
                                            </tr>
                                        <tr>
                                            <td><strong>Demandeur:</strong></td>
                                            <td>
                                                @if($demandeRavitaillement->demandeur)
                                                    {{ $demandeRavitaillement->demandeur->nom }} {{ $demandeRavitaillement->demandeur->prenom }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        @if($demandeRavitaillement->approbateur)
                                            <tr>
                                                <td><strong>Approbateur:</strong></td>
                                                <td>{{ $demandeRavitaillement->approbateur->nom }} {{ $demandeRavitaillement->approbateur->prenom }}</td>
                                            </tr>
                                        @endif
                                        
                                        @if($demandeRavitaillement->montant_reel)
                                            <tr>
                                                <td><strong>Montant réel:</strong></td>
                                                <td><strong>{{ number_format($demandeRavitaillement->montant_reel, 0, ',', ' ') }} FCFA</strong></td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Articles demandés -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Articles demandés</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Article</th>
                                                    <th>Quantité demandée</th>
                                                    <th>Quantité approuvée</th>
                                                    <th>Quantité livrée</th>
                                                    <th>Unité</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($demandeRavitaillement->lignes as $ligne)
                                                    <tr>
                                                        <td>
                                                            @if($ligne->article)
                                                                {{ $ligne->article->nom }}
                                                                @if($ligne->article->reference)
                                                                    <br><small class="text-muted">Réf: {{ $ligne->article->reference }}</small>
                                                                @endif
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format($ligne->quantite_demandee, 3) }}</td>
                                                        <td>
                                                            @if($ligne->quantite_approuvee)
                                                                {{ number_format($ligne->quantite_approuvee, 3) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($ligne->quantite_livree)
                                                                {{ number_format($ligne->quantite_livree, 3) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($ligne->uniteMesure)
                                                                {{ $ligne->uniteMesure->nom }}
                                                            @elseif($ligne->article && $ligne->article->uniteMesure)
                                                                {{ $ligne->article->uniteMesure->nom }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">Aucun article trouvé</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commentaires et motif de rejet -->
                    @if($demandeRavitaillement->commentaires || $demandeRavitaillement->motif_rejet)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Commentaires</h5>
                                    </div>
                                    <div class="card-body">
                                        @if($demandeRavitaillement->commentaires)
                                            <div class="mb-3">
                                                <strong>Commentaires:</strong>
                                                <p class="mb-0">{{ $demandeRavitaillement->commentaires }}</p>
                                            </div>
                                        @endif
                                        
                                        @if($demandeRavitaillement->motif_rejet)
                                            <div class="alert alert-danger">
                                                <strong>Motif de rejet:</strong>
                                                <p class="mb-0">{{ $demandeRavitaillement->motif_rejet }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action pour marquer comme livrée -->
                    @if($demandeRavitaillement->statut === 'approuvee')
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Marquer comme livrée</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('demandes-ravitaillement.marquer-livree', $demandeRavitaillement) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="date_livraison_effective" class="form-label">Date de livraison effective <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="date_livraison_effective" name="date_livraison_effective" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="montant_reel" class="form-label">Montant réel (FCFA)</label>
                                                        <input type="number" class="form-control" id="montant_reel" name="montant_reel" step="0.01" min="0" placeholder="Montant réel payé">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="commentaires_livraison" class="form-label">Commentaires</label>
                                                        <textarea class="form-control" id="commentaires_livraison" name="commentaires" rows="2" placeholder="Commentaires sur la livraison..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary" onclick="return confirm('Êtes-vous sûr de vouloir marquer cette demande comme livrée ?')">
                                                <i class="fas fa-truck"></i> Marquer comme livrée
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions d'approbation/rejet -->
                    @if($demandeRavitaillement->statut === 'en_attente')
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Actions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <form action="{{ route('demandes-ravitaillement.approuver', $demandeRavitaillement) }}" method="POST">
                                                    @csrf
                                                    <div class="form-group mb-3">
                                                        <label for="commentaires_approbation" class="form-label">Commentaires d'approbation</label>
                                                        <textarea class="form-control" id="commentaires_approbation" name="commentaires" rows="3" placeholder="Commentaires optionnels..."></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir approuver cette demande ?')">
                                                        <i class="fas fa-check"></i> Approuver
                                                    </button>
                                                </form>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <form action="{{ route('demandes-ravitaillement.rejeter', $demandeRavitaillement) }}" method="POST">
                                                    @csrf
                                                    <div class="form-group mb-3">
                                                        <label for="motif_rejet" class="form-label">Motif de rejet <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="motif_rejet" name="motif_rejet" rows="3" placeholder="Veuillez préciser le motif du rejet..." required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')">
                                                        <i class="fas fa-times"></i> Rejeter
                                                    </button>
                                                </form>
                                            </div>
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

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush