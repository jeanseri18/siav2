@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Édition du DQE</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
            <p>Référence : {{ $dqe->reference ?? 'Sans référence' }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dqe.index', $contrat->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addLineModal">
                <i class="fas fa-plus-circle"></i> Ajouter une ligne
            </button>
            @if($dqe->statut == 'brouillon')
                <form action="{{ route('debourses.generate', $dqe->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calculator"></i> Générer déboursés
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Informations générales</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dqe.update', $dqe->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reference">Référence</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference', $dqe->reference) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="statut">Statut</label>
                                    <select class="form-control" id="statut" name="statut">
                                        <option value="brouillon" {{ $dqe->statut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                        <option value="validé" {{ $dqe->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                        <option value="archivé" {{ $dqe->statut == 'archivé' ? 'selected' : '' }}>Archivé</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $dqe->notes) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Lignes du DQE</h5>
                    <div>
                        <span class="badge bg-primary">Montant total HT : {{ number_format($dqe->montant_total_ht, 2, ',', ' ') }}</span>
                        <span class="badge bg-success">Montant total TTC : {{ number_format($dqe->montant_total_ttc, 2, ',', ' ') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Désignation</th>
                                    <th>Quantité</th>
                                    <th>Unité</th>
                                    <th>Prix Unitaire HT</th>
                                    <th>Montant HT</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dqe->lignes as $ligne)
                                    <tr>
                                        <td>{{ $ligne->designation }}</td>
                                        <td>
                                            @if($dqe->statut == 'brouillon')
                                                <form action="{{ route('dqe.lines.update', [$dqe->id, $ligne->id]) }}" method="POST" class="d-flex">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" name="quantite" value="{{ $ligne->quantite }}" style="width: 100px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @else
                                                {{ $ligne->quantite }}
                                            @endif
                                        </td>
                                        <td>{{ $ligne->unite }}</td>
                                        <td>{{ number_format($ligne->pu_ht, 2, ',', ' ') }}</td>
                                        <td>{{ number_format($ligne->montant_ht, 2, ',', ' ') }}</td>
                                        <td>
                                            @if($dqe->statut == 'brouillon')
                                                <form action="{{ route('dqe.lines.delete', [$dqe->id, $ligne->id]) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune ligne dans ce DQE.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter une ligne -->
<div class="modal fade" id="addLineModal" tabindex="-1" aria-labelledby="addLineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLineModalLabel">Ajouter une ligne au DQE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr class="bg-light">
                                                                    <th>Désignation</th>
                                                                    <th>Unité</th>
                                                                    <th>Prix Unitaire HT</th>
                                                                    <th style="width: 200px;">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($rubrique->bpus as $bpu)
                                                                    <tr>
                                                                        <td>{{ $bpu->designation }}</td>
                                                                        <td>{{ $bpu->unite }}</td>
                                                                        <td>{{ number_format($bpu->pu_ht, 2, ',', ' ') }}</td>
                                                                        <td>
                                                                            <form action="{{ route('dqe.lines.add', $dqe->id) }}" method="POST" class="d-flex">
                                                                                @csrf
                                                                                <input type="hidden" name="bpu_id" value="{{ $bpu->id }}">
                                                                                <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" name="quantite" value="1" placeholder="Qté">
                                                                                <button type="submit" class="btn btn-sm btn-primary ms-2">
                                                                                    <i class="fas fa-plus"></i> Ajouter
                                                                                </button>
                                                                            </form>
                                                                        </td>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection