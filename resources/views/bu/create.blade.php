@extends('layouts.app')

@section('content')
<div class="app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-plus-circle me-2"></i>Créer un BU
                    </h2>
                </div>
                <div class="app-card-body">
                    <form action="{{ route('bu.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="app-form-group">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="app-form-group">
                            <label class="form-label">Secteur d'activité</label>
                            <select name="secteur_activite_id" class="form-select" required>
                                @foreach($secteurs as $secteur)
                                    <option value="{{ $secteur->id }}">{{ $secteur->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="app-form-group">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control" required>
                        </div>
                        <div class="app-form-group">
                            <label class="form-label">Numéro RCCM</label>
                            <input type="text" name="numero_rccm" class="form-control" required>
                        </div>
                        <div class="app-form-group">
                            <label class="form-label">Numéro CC</label>
                            <input type="text" name="numero_cc" class="form-control" required>
                        </div>
                        <div class="app-form-group">
                            <label class="form-label">Solde initial (FCFA)</label>
                            <input type="number" name="soldecaisse" class="form-control" step="0.01" min="0" value="0" required>
                            <small class="text-muted">0 est autorisé pour une caisse vide au démarrage.</small>
                        </div>
                        <div class="app-form-group">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                            </select>
                        </div>

                        <x-photo-dropzone
                            name="logo"
                            id="logo"
                            label="Logo"
                            help="Logo de la Business Unit (facultatif). Glissez-déposez ou cliquez."
                        />

                        <div class="app-card-footer">
                            <a href="{{ route('bu.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Créer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
