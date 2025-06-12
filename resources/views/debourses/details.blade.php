@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>
                @if($debourse->type == 'sec')
                    Déboursé Sec
                @elseif($debourse->type == 'main_oeuvre')
                    Déboursé Main d'Œuvre
                @else
                    Frais de Chantier
                @endif
            </h2>
            <h4>Contrat : {{ $debourse->contrat->nom_contrat }}</h4>
            <p>DQE : {{ $debourse->dqe->reference ?? 'Sans référence' }}</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('debourses.index', $debourse->contrat_id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('debourses.export', $debourse->id) }}" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Exporter en PDF
            </a>
            @if($debourse->type == 'sec' && $debourse->statut == 'validé')
                <form action="{{ route('debourses_chantier.generate', $debourse->dqe_id) }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-hard-hat"></i> Créer déboursé chantier
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Détails du déboursé</h5>
                    <span class="badge bg-primary fs-5">Montant total : {{ number_format($debourse->montant_total, 2, ',', ' ') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Désignation</th>
                                    <th>Unité</th>
                                    <th>Quantité</th>
                                    @if($debourse->type == 'sec')
                                        <th>Coût unitaire matériaux</th>
                                        <th>Coût unitaire main d'œuvre</th>
                                        <th>Coût unitaire matériel</th>
                                        <th>Total matériaux</th>
                                        <th>Total main d'œuvre</th>
                                        <th>Total matériel</th>
                                    @elseif($debourse->type == 'main_oeuvre')
                                        <th>Coût unitaire main d'œuvre</th>
                                        <th>Total main d'œuvre</th>
                                    @else
                                        <th>Coût unitaire frais de chantier</th>
                                        <th>Total frais de chantier</th>
                                    @endif
                                    <th>Montant total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($debourse->details as $detail)
                                    <tr>
                                        <td>
                                            @if($debourse->statut == 'brouillon')
                                                <span class="editable-designation-debourse" data-id="{{ $detail->id }}" data-value="{{ $detail->dqeLigne->designation }}" style="cursor: pointer; border-bottom: 1px dashed #007bff;" title="Cliquer pour modifier">
                                                    {{ $detail->dqeLigne->designation }}
                                                </span>
                                            @else
                                                {{ $detail->dqeLigne->designation }}
                                            @endif
                                        </td>
                                        <td>{{ $detail->dqeLigne->unite }}</td>
                                        <td>
                                            @if($debourse->statut == 'brouillon')
                                                <input type="number" name="quantite" value="{{ $detail->dqeLigne->quantite }}" step="0.01" min="0" class="form-control form-control-sm">
                                            @else
                                                {{ $detail->dqeLigne->quantite }}
                                            @endif
                                        </td>
                                        @if($debourse->type == 'sec')
                                            <td>
                                                @if($debourse->statut == 'brouillon')
                                                    <input type="number" name="cout_unitaire_materiaux" value="{{ $detail->cout_unitaire_materiaux ?? $detail->dqeLigne->bpu->materiaux }}" step="0.01" min="0" class="form-control form-control-sm">
                                                @else
                                                    {{ number_format($detail->cout_unitaire_materiaux ?? $detail->dqeLigne->bpu->materiaux, 2, ',', ' ') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($debourse->statut == 'brouillon')
                                                    <input type="number" name="cout_unitaire_main_oeuvre" value="{{ $detail->cout_unitaire_main_oeuvre ?? $detail->dqeLigne->bpu->main_oeuvre }}" step="0.01" min="0" class="form-control form-control-sm">
                                                @else
                                                    {{ number_format($detail->cout_unitaire_main_oeuvre ?? $detail->dqeLigne->bpu->main_oeuvre, 2, ',', ' ') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($debourse->statut == 'brouillon')
                                                    <input type="number" name="cout_unitaire_materiel" value="{{ $detail->cout_unitaire_materiel ?? $detail->dqeLigne->bpu->materiel }}" step="0.01" min="0" class="form-control form-control-sm">
                                                @else
                                                    {{ number_format($detail->cout_unitaire_materiel ?? $detail->dqeLigne->bpu->materiel, 2, ',', ' ') }}
                                                @endif
                                            </td>
                                            <td>{{ number_format($detail->total_materiaux ?? ($detail->dqeLigne->quantite * ($detail->cout_unitaire_materiaux ?? $detail->dqeLigne->bpu->materiaux)), 2, ',', ' ') }}</td>
                                            <td>{{ number_format($detail->total_main_oeuvre ?? ($detail->dqeLigne->quantite * ($detail->cout_unitaire_main_oeuvre ?? $detail->dqeLigne->bpu->main_oeuvre)), 2, ',', ' ') }}</td>
                                            <td>{{ number_format($detail->total_materiel ?? ($detail->dqeLigne->quantite * ($detail->cout_unitaire_materiel ?? $detail->dqeLigne->bpu->materiel)), 2, ',', ' ') }}</td>
                                        @elseif($debourse->type == 'main_oeuvre')
                                            <td>{{ number_format($detail->dqeLigne->bpu->main_oeuvre, 2, ',', ' ') }}</td>
                                            <td>{{ number_format($detail->montant, 2, ',', ' ') }}</td>
                                        @else
                                            <td>{{ number_format($detail->dqeLigne->bpu->frais_chantier, 2, ',', ' ') }}</td>
                                            <td>{{ number_format($detail->montant, 2, ',', ' ') }}</td>
                                        @endif
                                        <td>{{ number_format($detail->montant, 2, ',', ' ') }}</td>
                                        <td>
                                            @if($debourse->statut == 'brouillon')
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Édition en ligne des désignations pour les déboursés
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.editable-designation-debourse').forEach(function(element) {
        element.addEventListener('click', function() {
            const currentValue = this.dataset.value;
            const detailId = this.dataset.id;
            
            const input = document.createElement('input');
            input.type = 'text';
            input.value = currentValue;
            input.className = 'form-control form-control-sm';
            input.style.width = '100%';
            
            const saveBtn = document.createElement('button');
            saveBtn.innerHTML = '<i class="fas fa-check"></i>';
            saveBtn.className = 'btn btn-xs btn-success ms-1';
            saveBtn.type = 'button';
            
            const cancelBtn = document.createElement('button');
            cancelBtn.innerHTML = '<i class="fas fa-times"></i>';
            cancelBtn.className = 'btn btn-xs btn-secondary ms-1';
            cancelBtn.type = 'button';
            
            const container = document.createElement('div');
            container.className = 'd-flex align-items-center';
            container.appendChild(input);
            container.appendChild(saveBtn);
            container.appendChild(cancelBtn);
            
            this.parentNode.replaceChild(container, this);
            input.focus();
            
            const self = this;
            
            function restore() {
                container.parentNode.replaceChild(self, container);
            }
            
            function save() {
                const newValue = input.value.trim();
                if (newValue && newValue !== currentValue) {
                    // Ici, vous pouvez implémenter l'appel AJAX pour sauvegarder
                    self.textContent = newValue;
                    self.dataset.value = newValue;
                    console.log('Sauvegarder designation déboursé:', detailId, newValue);
                }
                restore();
            }
            
            saveBtn.addEventListener('click', save);
            cancelBtn.addEventListener('click', restore);
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') save();
                if (e.key === 'Escape') restore();
            });
        });
    });
});
</script>

@endsection