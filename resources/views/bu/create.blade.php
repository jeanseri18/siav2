@extends('layouts.app')

@section('content')
 <div class=" app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-plus-circle me-2"></i>Créer un BU   </h2>
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
                <select name="secteur_activite_id" class="form-control" required>
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
                <label class="form-label">Solde initial</label>
                <input type="text" name="soldecaisse" class="form-control" required>
            </div>
            <div class="app-form-select">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-control">
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </select>
            </div>
                                    <div class="app-card-footer">
        <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Créer</button>
        </form>
    </div>
    </div>
@endsection
