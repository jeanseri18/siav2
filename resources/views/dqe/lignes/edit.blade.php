@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Modifier Ligne DQE</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('contrats.show', $contrat->id) }}">Contrat {{ $contrat->code }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dqe.edit', $dqe->id) }}">DQE</a></li>
                        <li class="breadcrumb-item active">Modifier Ligne</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modifier la ligne DQE</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dqe.lignes.update', $ligne->id) }}" method="POST" id="editLigneForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rubrique_id" class="form-label">Rubrique</label>
                                    <select class="form-select @error('rubrique_id') is-invalid @enderror" id="rubrique_id" name="rubrique_id" required>
                                        <option value="">Sélectionner une rubrique</option>
                                        @if($ligne->rubrique)
                                            <option value="{{ $ligne->rubrique->id }}" selected>
                                                {{ $ligne->rubrique->code }} - {{ $ligne->rubrique->designation }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('rubrique_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Désignation</label>
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                                           id="designation" name="designation" value="{{ old('designation', $ligne->designation) }}" required>
                                    @error('designation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unite" class="form-label">Unité</label>
                                    <input type="text" class="form-control @error('unite') is-invalid @enderror" 
                                           id="unite" name="unite" value="{{ old('unite', $ligne->unite) }}" required>
                                    @error('unite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité</label>
                                    <input type="number" class="form-control @error('quantite') is-invalid @enderror" 
                                           id="quantite" name="quantite" value="{{ old('quantite', $ligne->quantite) }}" 
                                           step="0.01" min="0" required>
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="pu_ht" class="form-label">Prix Unitaire HT</label>
                                    <input type="number" class="form-control @error('pu_ht') is-invalid @enderror" 
                                           id="pu_ht" name="pu_ht" value="{{ old('pu_ht', $ligne->pu_ht) }}" 
                                           step="0.01" min="0" required>
                                    @error('pu_ht')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Montant HT</label>
                                    <input type="text" class="form-control" id="montant_ht" value="{{ number_format($ligne->montant_ht, 2, ',', ' ') }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="{{ route('dqe.edit', $dqe->id) }}" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Calcul automatique du montant HT
    function calculateMontant() {
        const quantite = parseFloat(document.getElementById('quantite').value) || 0;
        const pu_ht = parseFloat(document.getElementById('pu_ht').value) || 0;
        const montant = quantite * pu_ht;
        document.getElementById('montant_ht').value = montant.toFixed(2).replace('.', ',');
    }
    
    document.getElementById('quantite').addEventListener('input', calculateMontant);
    document.getElementById('pu_ht').addEventListener('input', calculateMontant);
    
    // Soumission du formulaire en AJAX
    document.getElementById('editLigneForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirection vers la page d'édition du DQE
                window.location.href = '{{ route('dqe.edit', $dqe->id) }}';
            } else {
                alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la modification.');
        });
    });
</script>
@endpush
@endsection