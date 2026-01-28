@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="container">
    <h3>Modifier une ligne BPU</h3>
    
    <form action="{{ route('bpus.update', $bpu->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="{{ request('from') == 'until' ? 'bpu.indexuntil' : 'bpu.index' }}">
        
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Saisie des données BPU :</label>
                <div class="d-flex gap-3 align-items-center flex-wrap" style="font-size: 15px;">
                    <div class="d-flex flex-column">
                        <small>Désignation</small>
                        <input type="text" name="designation" class="form-control form-control-sm" value="{{ $bpu->designation }}" style="width: 240px; font-size: 15px; height: 40px;" required>
                    </div>
                    <div class="d-flex flex-column">
                        <small>Unité</small>
                        <select name="unite" class="form-select form-select-sm" style="width: 120px; font-size: 15px; height: 40px;" required>
                            <option value="" style="font-size: 15px;">Choisir</option>
                            @foreach ($uniteMesures as $unite)
                                <option value="{{ $unite->ref }}" @if($bpu->unite == $unite->ref) selected @endif>
                                    {{ $unite->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex flex-column">
                        <small>Matériaux</small>
                        <input type="number" name="materiaux" class="form-control form-control-sm" value="{{ $bpu->materiaux }}" step="0.01" style="width: 120px; font-size: 15px; height: 40px;" required>
                    </div>
                    <div class="d-flex flex-column">
                        <small>T.MO (%)</small>
                        <input type="number" name="taux_mo" class="form-control form-control-sm" value="{{ $bpu->taux_mo ?? 0 }}" step="0.01" style="width: 105px; font-size: 15px; height: 40px;">
                    </div>
                    <div class="d-flex flex-column">
                        <small>T.MAT (%)</small>
                        <input type="number" name="taux_mat" class="form-control form-control-sm" value="{{ $bpu->taux_mat ?? 0 }}" step="0.01" style="width: 110px; font-size: 15px; height: 40px;">
                    </div>
                    <div class="d-flex flex-column">
                        <small>T.FC (%)</small>
                        <input type="number" name="taux_fc" class="form-control form-control-sm" value="{{ $bpu->taux_fc ?? 0 }}" step="0.01" style="width: 105px; font-size: 15px; height: 40px;">
                    </div>
                    <div class="d-flex flex-column">
                        <small>T.FG (%)</small>
                        <input type="number" name="taux_fg" class="form-control form-control-sm" value="{{ $bpu->taux_fg ?? 0 }}" step="0.01" style="width: 105px; font-size: 15px; height: 40px;">
                    </div>
                    <div class="d-flex flex-column">
                        <small>T.BEN (%)</small>
                        <input type="number" name="taux_benefice" class="form-control form-control-sm" value="{{ $bpu->taux_benefice ?? 0 }}" step="0.01" style="width: 110px; font-size: 15px; height: 40px;">
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Affichage des valeurs calculées --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <h6>Valeurs calculées automatiquement :</h6>
                <div class="d-flex gap-3 align-items-center flex-wrap" style="font-size: 11px; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                    <div class="text-center">
                        <small class="text-muted">Main d'œuvre</small><br>
                        <span class="fw-bold" id="main_oeuvre_calc">{{ number_format($bpu->main_oeuvre, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">Matériel</small><br>
                        <span class="fw-bold" id="materiel_calc">{{ number_format($bpu->materiel, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">DS</small><br>
                        <span class="fw-bold" id="debourse_sec_calc">{{ number_format($bpu->debourse_sec, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">FC</small><br>
                        <span class="fw-bold" id="frais_chantier_calc">{{ number_format($bpu->frais_chantier, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">FG</small><br>
                        <span class="fw-bold" id="frais_generaux_calc">{{ number_format($bpu->frais_general, 2) }}</span>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">Bénéfice</small><br>
                        <span class="fw-bold" id="benefice_calc">{{ number_format($bpu->marge_nette, 2) }}</span>
                    </div>
                    <div class="text-center border-start ps-3">
                        <small class="text-muted">PU HT</small><br>
                        <span class="fw-bold text-primary" style="font-size: 14px;" id="pu_ht_calc">{{ number_format($bpu->pu_ht, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            💡 Les valeurs ci-dessus sont calculées automatiquement selon les formules BPU et se mettent à jour en temps réel.
        </div>
        
        <div class="text-end mt-4">
            <a href="{{ route('bpu.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/bpu-calculator.js') }}"></script>
@endsection