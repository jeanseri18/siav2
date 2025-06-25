@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-truck-loading"></i>
                        Effectuer une Réception
                    </h3>
                    <a href="{{ route('receptions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- Informations du bon de commande -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Informations de la Commande</h5>
                                    <p><strong>Référence:</strong> {{ $bonCommande->reference }}</p>
                                    <p><strong>Fournisseur:</strong> {{ $bonCommande->fournisseur->nom }}</p>
                                    <p><strong>Date de commande:</strong> {{ \Carbon\Carbon::parse($bonCommande->date_commande)->format('d/m/Y') }}</p>
                                    @if($bonCommande->date_livraison_prevue)
                                        <p><strong>Date de livraison prévue:</strong> {{ \Carbon\Carbon::parse($bonCommande->date_livraison_prevue)->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Progression Globale</h5>
                                    @php
                                        $totalQuantite = $bonCommande->lignes->sum('quantite');
                                        $totalRecue = $bonCommande->lignes->sum('quantite_recue');
                                        $pourcentage = $totalQuantite > 0 ? round(($totalRecue / $totalQuantite) * 100, 1) : 0;
                                    @endphp
                                    <div class="progress mb-2" style="height: 25px;">
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: {{ $pourcentage }}%" 
                                             aria-valuenow="{{ $pourcentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $pourcentage }}%
                                        </div>
                                    </div>
                                    <p class="mb-0">{{ $totalRecue }}/{{ $totalQuantite }} articles reçus</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulaire de réception -->
                    <form action="{{ route('receptions.store') }}" method="POST" id="receptionForm">
                        @csrf
                        <input type="hidden" name="bon_commande_id" value="{{ $bonCommande->id }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="date_reception" class="form-label">Date de réception *</label>
                                <input type="date" 
                                       class="form-control @error('date_reception') is-invalid @enderror" 
                                       id="date_reception" 
                                       name="date_reception" 
                                       value="{{ old('date_reception', date('Y-m-d')) }}" 
                                       required>
                                @error('date_reception')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes de réception</label>
                            <textarea class="form-control" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Commentaires sur la livraison, état des articles, etc.">{{ old('notes') }}</textarea>
                        </div>
                        
                        <!-- Articles à recevoir -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-boxes"></i>
                                    Articles à recevoir
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Article</th>
                                                <th>Quantité Commandée</th>
                                                <th>Quantité Déjà Reçue</th>
                                                <th>Quantité Restante</th>
                                                <th>Quantité à Recevoir</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lignesEnAttente as $index => $ligne)
                                                @php
                                                    $quantiteRestante = $ligne->quantite - $ligne->quantite_recue;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $ligne->article->designation }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $ligne->article->reference_fournisseur ?? $ligne->article->code }}</small>
                                                        <input type="hidden" name="ligne_id[]" value="{{ $ligne->id }}">
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $ligne->quantite }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ $ligne->quantite_recue }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ $quantiteRestante }}</span>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control quantite-input @error('quantite_recue.'.$index) is-invalid @enderror" 
                                                               name="quantite_recue[]" 
                                                               min="1" 
                                                               max="{{ $quantiteRestante }}" 
                                                               value="{{ old('quantite_recue.'.$index, $quantiteRestante) }}" 
                                                               data-max="{{ $quantiteRestante }}" 
                                                               style="width: 100px;">
                                                        @error('quantite_recue.'.$index)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary btn-max" 
                                                                data-target="quantite_recue_{{ $index }}" 
                                                                data-max="{{ $quantiteRestante }}">
                                                            Max
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('receptions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i>
                                Enregistrer la Réception
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Boutons "Max" pour remplir automatiquement la quantité maximale
    document.querySelectorAll('.btn-max').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const max = this.getAttribute('data-max');
            const row = this.closest('tr');
            const input = row.querySelector('.quantite-input');
            input.value = max;
        });
    });
    
    // Validation des quantités
    document.querySelectorAll('.quantite-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max'));
            const value = parseInt(this.value);
            
            if (value > max) {
                this.value = max;
                this.classList.add('is-invalid');
                
                // Afficher un message d'erreur temporaire
                let feedback = this.parentNode.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentNode.appendChild(feedback);
                }
                feedback.textContent = `La quantité ne peut pas dépasser ${max}`;
                
                setTimeout(() => {
                    this.classList.remove('is-invalid');
                    if (feedback) feedback.remove();
                }, 3000);
            }
        });
    });
    
    // Validation du formulaire
    document.getElementById('receptionForm').addEventListener('submit', function(e) {
        let hasError = false;
        
        document.querySelectorAll('.quantite-input').forEach(function(input) {
            const value = parseInt(input.value);
            const max = parseInt(input.getAttribute('max'));
            
            if (value <= 0 || value > max) {
                hasError = true;
                input.classList.add('is-invalid');
            }
        });
        
        if (hasError) {
            e.preventDefault();
            alert('Veuillez vérifier les quantités saisies.');
        }
    });
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.table th {
    border-top: none;
    font-weight: 600;
}

.quantite-input {
    text-align: center;
}

.btn-max {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.progress {
    background-color: rgba(255, 255, 255, 0.2);
}
</style>
@endsection