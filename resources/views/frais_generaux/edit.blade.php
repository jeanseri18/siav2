@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Modifier les frais généraux</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('frais_generaux.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('frais_generaux.update', $fraisGeneral->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="montant_base">Montant de base</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="montant_base" name="montant_base" value="{{ old('montant_base', $fraisGeneral->montant_base) }}" required>
                            <small class="form-text text-muted">Montant sur lequel les frais généraux sont calculés.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="pourcentage">Pourcentage (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="pourcentage" name="pourcentage" value="{{ old('pourcentage', $fraisGeneral->pourcentage) }}" required>
                            <small class="form-text text-muted">Pourcentage standard : 10%</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="statut">Statut</label>
                            <select class="form-control" id="statut" name="statut">
                                <option value="brouillon" {{ $fraisGeneral->statut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                <option value="validé" {{ $fraisGeneral->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                <option value="archivé" {{ $fraisGeneral->statut == 'archivé' ? 'selected' : '' }}>Archivé</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $fraisGeneral->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>Aperçu du calcul</h5>
                                <p><strong>Montant de base :</strong> <span id="preview_montant_base">{{ number_format($fraisGeneral->montant_base, 2, ',', ' ') }}</span></p>
                                <p><strong>Pourcentage :</strong> <span id="preview_pourcentage">{{ $fraisGeneral->pourcentage }}</span>%</p>
                                <p><strong>Montant total des frais généraux :</strong> <span id="preview_montant_total">{{ number_format($fraisGeneral->montant_total, 2, ',', ' ') }}</span></p>
                            </div>
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
</div>

<script>
    // Mise à jour de l'aperçu en temps réel
    document.addEventListener('DOMContentLoaded', function() {
        const montantBaseInput = document.getElementById('montant_base');
        const pourcentageInput = document.getElementById('pourcentage');
        const previewMontantBase = document.getElementById('preview_montant_base');
        const previewPourcentage = document.getElementById('preview_pourcentage');
        const previewMontantTotal = document.getElementById('preview_montant_total');

        function updatePreview() {
            const montantBase = parseFloat(montantBaseInput.value) || 0;
            const pourcentage = parseFloat(pourcentageInput.value) || 0;
            const montantTotal = montantBase * (pourcentage / 100);

            previewMontantBase.textContent = formatNumber(montantBase);
            previewPourcentage.textContent = pourcentage.toFixed(2);
            previewMontantTotal.textContent = formatNumber(montantTotal);
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number);
        }

        montantBaseInput.addEventListener('input', updatePreview);
        pourcentageInput.addEventListener('input', updatePreview);

        // Initialisation de l'aperçu
        updatePreview();
    });
</script>
@endsection