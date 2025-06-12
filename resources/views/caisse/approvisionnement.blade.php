{{-- Page Approvisionnement de Caisse --}}
@extends('layouts.app')

@section('title', 'Approvisionnement de Caisse')
@section('page-title', 'Approvisionnement de Caisse')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('caisse.brouillard') }}">Caisse</a></li>
<li class="breadcrumb-item active">Approvisionnement</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-money-bill-wave me-2"></i>Approvisionnement de Compte
                    </h2>
                </div>
                <div class="app-card-body">
                    <form action="{{ route('caisse.approvisionnerCaisse') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="bu_id" class="form-label">Business Unit (BU)</label>
                            <select id="bu_id" name="bu_id" class="form-control" required>
                                @foreach(\App\Models\BU::all() as $bu_item)
                                    <option value="{{ $bu_item->id }}" {{ session('selected_bu') == $bu_item->id ? 'selected' : '' }}>{{ $bu_item->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="objet" class="form-label">Objet</label>
                            <input type="text" class="form-control" id="objet" name="motif" required>
                        </div>
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant</label>
                            <input type="number" class="form-control" id="montant" name="montant" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mode de paiement</label>
                            <div class="d-flex">
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="mode_paiement" id="mode_espece" value="espece" checked>
                                    <label class="form-check-label" for="mode_espece">Espèce</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mode_paiement" id="mode_cheque" value="cheque">
                                    <label class="form-check-label" for="mode_cheque">Chèque</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Champs spécifiques au mode de paiement -->
                        <div id="cheque_fields" style="display: none;">
                            <div class="mb-3">
                                <label for="reference_cheque" class="form-label">Référence Chèque</label>
                                <input type="text" class="form-control" id="reference_cheque" name="reference_cheque">
                            </div>
                            <div class="mb-3">
                                <label for="banque_id" class="form-label">Banque</label>
                                <select class="form-select" id="banque_id" name="banque_id">
                                    @foreach(\App\Models\Banque::all() as $banque)
                                        <option value="{{ $banque->id }}">{{ $banque->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div id="espece_fields">
                            <div class="mb-3">
                                <label for="origine_fonds" class="form-label">Origine des fonds</label>
                                <input type="text" class="form-control" id="origine_fonds" name="origine_fonds">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="date_appro" class="form-label">Date</label>
                            <input type="datetime-local" class="form-control" id="date_appro" name="date_appro" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('caisse.brouillard') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // Gestion de l'affichage des champs selon le mode de paiement
        $('input[name="mode_paiement"]').change(function() {
            if ($(this).val() === 'cheque') {
                $('#cheque_fields').show();
                $('#espece_fields').hide();
            } else {
                $('#cheque_fields').hide();
                $('#espece_fields').show();
            }
        });
        
        // Initialiser avec la valeur par défaut
        $('input[name="mode_paiement"]:checked').trigger('change');
        
        // Initialiser la date avec la date et l'heure actuelles
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        $('#date_appro').val(formattedDateTime);
    });
</script>
@endpush
@endsection