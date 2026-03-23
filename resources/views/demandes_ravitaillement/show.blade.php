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

                    <!-- Action pour Livrer (Gestionnaire) -->
                    @if($demandeRavitaillement->statut === 'approuvee' || $demandeRavitaillement->statut === 'en_cours')
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0"><i class="fas fa-truck me-2"></i>Livraison (Gestionnaire)</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('demandes-ravitaillement.livrer', $demandeRavitaillement) }}" method="POST">
                                            @csrf
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Article</th>
                                                            <th>Qté Approuvée</th>
                                                            <th>Déjà Livré</th>
                                                            <th>A Livrer</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($demandeRavitaillement->lignes as $ligne)
                                                            <tr>
                                                                <td>{{ $ligne->article->nom }}</td>
                                                                <td>{{ $ligne->quantite_approuvee }}</td>
                                                                <td>{{ $ligne->quantite_livree }}</td>
                                                                <td>
                                                                    <input type="hidden" name="lignes[{{ $loop->index }}][id]" value="{{ $ligne->id }}">
                                                                    <input type="number" name="lignes[{{ $loop->index }}][quantite_a_livrer]" class="form-control" 
                                                                           value="{{ max(0, $ligne->quantite_approuvee - $ligne->quantite_livree) }}" min="0" step="0.001">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Date de livraison</label>
                                                        <input type="date" name="date_livraison" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Commentaires</label>
                                                        <textarea name="commentaires" class="form-control" rows="1"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save me-2"></i>Enregistrer la livraison
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action pour Réceptionner (Chef Chantier) -->
                    @if($demandeRavitaillement->statut === 'livree' || $demandeRavitaillement->statut === 'en_cours')
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0"><i class="fas fa-check-circle me-2"></i>Réception (Chef Chantier)</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('demandes-ravitaillement.receptionner', $demandeRavitaillement) }}" method="POST">
                                            @csrf
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Article</th>
                                                            <th>Qté Livrée (Total)</th>
                                                            <th>Qté Reçue (Physique)</th>
                                                            <th>Motif (si écart)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($demandeRavitaillement->lignes as $ligne)
                                                            @if($ligne->quantite_livree > 0)
                                                                <tr>
                                                                    <td>{{ $ligne->article->nom }}</td>
                                                                    <td>{{ $ligne->quantite_livree }}</td>
                                                                    <td>
                                                                        <input type="hidden" name="lignes[{{ $loop->index }}][id]" value="{{ $ligne->id }}">
                                                                        <input type="number" name="lignes[{{ $loop->index }}][quantite_recue]" class="form-control" 
                                                                               value="{{ $ligne->quantite_livree }}" min="0" step="0.001">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="lignes[{{ $loop->index }}][motif_retour]" class="form-control" placeholder="Motif si refus...">
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="form-label">Date de réception</label>
                                                <input type="date" name="date_reception" class="form-control" value="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-check me-2"></i>Confirmer la réception
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action pour Valider Retour (Gestionnaire) - S'affiche toujours pour permettre correction manuelle -->
                    @if($demandeRavitaillement->statut === 'livree' || $demandeRavitaillement->statut === 'en_cours')
                         <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="card-title mb-0"><i class="fas fa-undo me-2"></i>Valider Retour Stock (Gestionnaire)</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small">Utilisez ce formulaire pour réintégrer des articles refusés au stock.</p>
                                        <form action="{{ route('demandes-ravitaillement.valider-retour', $demandeRavitaillement) }}" method="POST">
                                            @csrf
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Article</th>
                                                            <th>Quantité à réintégrer</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($demandeRavitaillement->lignes as $ligne)
                                                            <tr>
                                                                <td>{{ $ligne->article->nom }}</td>
                                                                <td>
                                                                    <input type="hidden" name="lignes[{{ $loop->index }}][article_id]" value="{{ $ligne->article_id }}">
                                                                    <input type="number" name="lignes[{{ $loop->index }}][quantite_retour]" class="form-control form-control-sm" 
                                                                           value="0" min="0" step="0.001">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="fas fa-save me-2"></i>Valider le retour
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