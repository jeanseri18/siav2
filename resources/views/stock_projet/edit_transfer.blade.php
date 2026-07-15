@extends('layouts.app')

@section('title', 'Modifier un transfert')
@section('page-title', 'Modifier un transfert')

@section('breadcrumb')
<li class="breadcrumb-item">Projets</li>
<li class="breadcrumb-item"><a href="{{ route('transferts.index') }}">Transferts de Stock</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le transfert (projet source : {{ $transfert->projetSource->nom_projet }})
            </h2>
        </div>

        <div class="app-card-body">
            <p class="text-muted mb-4">
                Article : <strong>{{ $transfert->article->nom }}</strong>
            </p>

            <form action="{{ route('transferts.update', $transfert) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')

                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="projet_destination" class="app-form-label">
                                <i class="fas fa-bullseye me-2"></i>Projet destination
                            </label>
                            <select name="projet_destination" id="projet_destination" class="app-form-select @error('projet_destination') is-invalid @enderror" required>
                                <option value="">— Choisir —</option>
                                @foreach($projets as $projet)
                                    @if((int)$projet->id !== (int)$transfert->id_projet_source)
                                        <option value="{{ $projet->id }}" @selected(old('projet_destination', $transfert->id_projet_destination) == $projet->id)>
                                            {{ $projet->nom_projet }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('projet_destination')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="quantite" class="app-form-label">
                                <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                            </label>
                            <input type="number" name="quantite" id="quantite" class="app-form-control @error('quantite') is-invalid @enderror"
                                   min="1" required value="{{ old('quantite', $transfert->quantite) }}">
                            @error('quantite')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="date_transfert" class="app-form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Date du transfert
                            </label>
                            <input type="date" name="date_transfert" id="date_transfert" class="app-form-control @error('date_transfert') is-invalid @enderror"
                                   required value="{{ old('date_transfert', \Carbon\Carbon::parse($transfert->date_transfert)->format('Y-m-d')) }}">
                            @error('date_transfert')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="app-form-actions mt-4">
                    <a href="{{ route('transferts.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
