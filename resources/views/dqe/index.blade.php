@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-4">Détail Quantitatif Estimatif (DQE)</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dqe.create', $contrat->id) }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nouveau DQE
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateDQEModal">
                <i class="fas fa-magic"></i> Générer depuis BPU
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card table-container"  style="min-height: 600px;>
        <div class="card-body p-2">
            <div class="table-responsive" style="min-height: 600px; overflow-y: auto;">
                <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 120px;">Référence</th>
                        <th style="min-width: 120px;">Date de création</th>
                        <th style="min-width: 130px; text-align: right;">Montant HT</th>
                        <th style="min-width: 130px; text-align: right;">Montant TTC</th>
                        <th style="min-width: 100px; text-align: center;">Statut</th>
                        <th style="min-width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dqes as $dqe)
                        <tr>
                            <td>
                                @if($dqe->reference)
                                    <a href="{{ route('dqe.show', $dqe->id) }}" class="badge bg-primary text-decoration-none">{{ $dqe->reference }}</a>
                                @else
                                    <span class="text-muted">Sans référence</span>
                                @endif
                            </td>
                            <td>{{ $dqe->created_at->format('d/m/Y') }}</td>
                            <td style="text-align: right; white-space: nowrap;">{{ number_format($dqe->montant_total_ht, 2, ',', ' ') }} FCFA</td>
                            <td style="text-align: right; white-space: nowrap;">{{ number_format($dqe->montant_total_ttc, 2, ',', ' ') }} FCFA</td>
                            <td style="text-align: center;">
                                @if($dqe->statut == 'brouillon')
                                    <span class="badge bg-warning">Brouillon</span>
                                @elseif($dqe->statut == 'validé')
                                    <span class="badge bg-success">Validé</span>
                                @else
                                    <span class="badge bg-secondary">Archivé</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('dqe.edit', $dqe->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Éditer
                                    </a>
                                    @if($dqe->statut != 'archivé')
                                        <div class="btn-group ms-1" role="group">
                                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-calculator"></i> Générer
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ route('debourses.generate_sec', $dqe->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-cube"></i> Déboursé sec
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('debourses.generate_frais_chantier', $dqe->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-tools"></i> Frais de chantier
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('debourses.generate_chantier', $dqe->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-hard-hat"></i> Déboursé chantier
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('debourses.generate', $dqe->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-calculator"></i> Tous les déboursés
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                    <form action="{{ route('dqe.destroy', $dqe->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce DQE ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger ms-1">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun DQE trouvé pour ce contrat.</td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour générer un DQE depuis le BPU -->
<div class="modal fade" id="generateDQEModal" tabindex="-1" aria-labelledby="generateDQEModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{ route('dqe.generate', $contrat->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="generateDQEModalLabel">Générer un DQE à partir du BPU</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p>Sélectionnez les éléments du BPU à inclure dans votre DQE :</p>
                    </div>

                    <div class="accordion" id="bpuAccordion">
                        @foreach($categories as $categorie)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $categorie->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $categorie->id }}" aria-expanded="false" aria-controls="collapse{{ $categorie->id }}">
                                        {{ $categorie->nom }}
                                    </button>
                                </h2>
<div id="collapse{{ $categorie->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $categorie->id }}" data-bs-parent="#bpuAccordion">
                                    <div class="accordion-body">
                                        @foreach($categorie->sousCategories as $sousCategorie)
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h5>{{ $sousCategorie->nom }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($sousCategorie->rubriques as $rubrique)
                                                        <h6 class="mt-3">{{ $rubrique->nom }}</h6>
                                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                                            <table class="table table-sm table-bordered mb-0">
                                                                <thead class="sticky-top bg-light">
                                                                    <tr>
                                                                        <th style="width: 50px; min-width: 50px;">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input select-all" type="checkbox" data-rubrique="{{ $rubrique->id }}">
                                                                            </div>
                                                                        </th>
                                                                        <th style="min-width: 200px;">Désignation</th>
                                                                        <th style="min-width: 80px; text-align: center;">Unité</th>
                                                                        <th style="min-width: 120px; text-align: right;">Prix Unitaire HT</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($rubrique->bpus as $bpu)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input bpu-item" type="checkbox" name="bpu_ids[]" value="{{ $bpu->id }}" data-rubrique="{{ $rubrique->id }}">
                                                                                </div>
                                                                            </td>
                                                                            <td style="word-wrap: break-word; max-width: 300px;">{{ $bpu->designation }}</td>
                                                                            <td style="text-align: center;">{{ $bpu->unite }}</td>
                                                                            <td style="text-align: right; white-space: nowrap;">{{ number_format($bpu->pu_ht, 2, ',', ' ') }} FCFA</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Générer le DQE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .table-container {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .table-responsive {
        border-radius: 6px;
    }
    
    .table thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        background-color: #f8f9fa !important;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .table td {
        padding: 8px 12px;
        vertical-align: middle;
    }
    
    .table th {
        padding: 12px;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.875em;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .modal-xl .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .accordion-body .table {
        margin-bottom: 0;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin-bottom: 2px;
            margin-right: 0;
        }
    }
</style>

<script>
    // Sélectionner/désélectionner tous les éléments d'une rubrique
    document.querySelectorAll('.select-all').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const rubriqueId = this.getAttribute('data-rubrique');
            const isChecked = this.checked;
            
            document.querySelectorAll(`.bpu-item[data-rubrique="${rubriqueId}"]`).forEach(item => {
                item.checked = isChecked;
            });
        });
    });
</script>
@endsection