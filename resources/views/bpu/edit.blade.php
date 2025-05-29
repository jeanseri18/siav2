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
        
        <div class="row mb-3">
            <div class="col">
                <label for="designation" class="form-label">Désignation</label>
                <input type="text" name="designation" class="form-control" value="{{ $bpu->designation }}" required>
            </div>
            <div class="col">
                <label for="qte" class="form-label">Quantité</label>
                <input type="number" step="0.01" name="qte" class="form-control" value="{{ $bpu->qte }}" required>
            </div>
            <div class="col">
                <label for="materiaux" class="form-label">Matériaux</label>
                <input type="number" step="0.01" name="materiaux" class="form-control" value="{{ $bpu->materiaux }}" required>
            </div>
            <div class="col">
                <label for="main_oeuvre" class="form-label">Main d'œuvre</label>
                <input type="number" step="0.01" name="main_oeuvre" class="form-control" value="{{ $bpu->main_oeuvre }}" required>
            </div>
            <div class="col">
                <label for="materiel" class="form-label">Matériel</label>
                <input type="number" step="0.01" name="materiel" class="form-control" value="{{ $bpu->materiel }}" required>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col">
<label for="unite" class="form-label">Unité</label>
                <select name="unite" class="form-control" required>
                    <option value="" style="font-size: 12px;">Choisir</option>
                    @foreach ($uniteMesures as $unite)
                        <option value="{{ $unite->ref }}" @if($bpu->unite == $unite->ref) selected @endif>
                            {{ $unite->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        {{-- Les autres champs sont calculés automatiquement, donc pas modifiables ici --}}
        <div class="alert alert-info mt-3">
            💡 Les champs "Déboursé sec", "Frais de chantier", "Frais généraux", "Marge nette", "PU HT" et "PU TTC" seront calculés automatiquement.
        </div>
        
        <div class="text-end mt-4">
            <a href="{{ route('bpu.index') }}" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection