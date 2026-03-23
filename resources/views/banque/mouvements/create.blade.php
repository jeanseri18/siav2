@extends('layouts.app')

@section('title', 'Nouveau mouvement bancaire')
@section('page-title', 'Nouveau mouvement bancaire')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('sublayouts_banque') }}">Banque</a></li>
<li class="breadcrumb-item"><a href="{{ route('banque.mouvements.index') }}">Mouvements</a></li>
<li class="breadcrumb-item active">Nouveau</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-plus-circle me-2"></i>Créer un mouvement
            </h2>
            <div class="app-card-actions">
                <span class="app-badge app-badge-info app-badge-pill">BU: {{ session('selected_bu') }}</span>
            </div>
        </div>

        <div class="app-card-body">
            <form method="POST" action="{{ route('banque.mouvements.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="banque_id" class="form-label">Banque</label>
                        <select name="banque_id" id="banque_id" class="form-select @error('banque_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une banque</option>
                            @foreach($banques as $banque)
                            <option value="{{ $banque->id }}" {{ old('banque_id') == $banque->id ? 'selected' : '' }}>
                                {{ $banque->nom }}
                            </option>
                            @endforeach
                        </select>
                        @error('banque_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="entree" {{ old('type', 'entree') === 'entree' ? 'selected' : '' }}>Entrée</option>
                            <option value="sortie" {{ old('type') === 'sortie' ? 'selected' : '' }}>Sortie</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="mode" class="form-label">Mode</label>
                        <select name="mode" id="mode" class="form-select @error('mode') is-invalid @enderror" required>
                            <option value="virement" {{ old('mode', 'virement') === 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="cheque" {{ old('mode') === 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="espece" {{ old('mode') === 'espece' ? 'selected' : '' }}>Espèce</option>
                        </select>
                        @error('mode')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="montant" class="form-label">Montant</label>
                        <input type="number" step="0.01" min="0" name="montant" id="montant" value="{{ old('montant') }}" class="form-control @error('montant') is-invalid @enderror" required>
                        @error('montant')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="date_operation" class="form-label">Date opération</label>
                        <input type="date" name="date_operation" id="date_operation" value="{{ old('date_operation', now()->toDateString()) }}" class="form-control @error('date_operation') is-invalid @enderror" required>
                        @error('date_operation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="numero_piece" class="form-label">Numéro pièce (chèque, virement…)</label>
                        <input type="text" name="numero_piece" id="numero_piece" value="{{ old('numero_piece') }}" class="form-control @error('numero_piece') is-invalid @enderror">
                        @error('numero_piece')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4" id="cheque_barre_group" style="display: none;">
                        <label class="form-label d-block">Chèque</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cheque_barre" id="cheque_barre_oui" value="1" {{ old('cheque_barre', '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="cheque_barre_oui">Chèque barré</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cheque_barre" id="cheque_barre_non" value="0" {{ old('cheque_barre', '0') === '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="cheque_barre_non">Chèque non barré</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                        <input type="text" name="beneficiaire" id="beneficiaire" value="{{ old('beneficiaire') }}" class="form-control @error('beneficiaire') is-invalid @enderror">
                        @error('beneficiaire')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="libelle" class="form-label">Libellé</label>
                        <input type="text" name="libelle" id="libelle" value="{{ old('libelle') }}" class="form-control @error('libelle') is-invalid @enderror">
                        @error('libelle')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" value="1" id="est_passe" name="est_passe" {{ old('est_passe') ? 'checked' : '' }}>
                            <label class="form-check-label" for="est_passe">
                                Déjà passé (réel)
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="date_passage" class="form-label">Date passage</label>
                        <input type="date" name="date_passage" id="date_passage" value="{{ old('date_passage') }}" class="form-control @error('date_passage') is-invalid @enderror">
                        @error('date_passage')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                    <a href="{{ route('banque.mouvements.index') }}" class="app-btn app-btn-secondary">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modeEl = document.getElementById('mode');
        const numeroPieceEl = document.getElementById('numero_piece');
        const chequeBarreGroup = document.getElementById('cheque_barre_group');
        const chequeBarreOuiEl = document.getElementById('cheque_barre_oui');
        const chequeBarreNonEl = document.getElementById('cheque_barre_non');

        function refreshModeUI() {
            const mode = modeEl ? modeEl.value : '';

            const isEspece = mode === 'espece';
            const isCheque = mode === 'cheque';

            if (numeroPieceEl) {
                numeroPieceEl.disabled = isEspece;
                if (isEspece) {
                    numeroPieceEl.value = '';
                }
            }

            if (chequeBarreGroup) {
                chequeBarreGroup.style.display = isCheque ? '' : 'none';
            }
            const radios = [chequeBarreOuiEl, chequeBarreNonEl].filter(Boolean);
            radios.forEach((el) => {
                el.disabled = !isCheque;
            });
            if (!isCheque && chequeBarreNonEl) {
                chequeBarreNonEl.checked = true;
            }
        }

        if (modeEl) {
            modeEl.addEventListener('change', refreshModeUI);
        }
        refreshModeUI();
    });
</script>
@endpush
