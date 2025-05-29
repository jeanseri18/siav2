{{-- Page Create - Ajouter une Configuration --}}
@extends('layouts.app')

@section('title', 'Ajouter une Configuration')
@section('page-title', 'Ajouter une Configuration')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('config-global.index') }}">Configurations</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-cog me-2"></i>Ajouter une Configuration
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('config-global.store') }}" method="POST" enctype="multipart/form-data" class="app-form">
                        @csrf
                        
                        <div class="app-form-group">
                            <label for="entete" class="app-form-label">
                                <i class="fas fa-heading me-2"></i>En-tête:
                            </label>
                            <input type="text" name="entete" class="app-form-control" required>
                            <div class="app-form-text">En-tête qui apparaîtra sur les documents</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="numdepatfacture" class="app-form-label">
                                <i class="fas fa-sort-numeric-up me-2"></i>Numéro de départ:
                            </label>
                            <input type="text" name="numdepatfacture" class="app-form-control" required>
                            <div class="app-form-text">Numéro à partir duquel les factures seront numérotées</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="pieddepage" class="app-form-label">
                                <i class="fas fa-paragraph me-2"></i>Pied de page:
                            </label>
                            <textarea name="pieddepage" class="app-form-control" rows="3" required></textarea>
                            <div class="app-form-text">Pied de page qui apparaîtra sur les documents</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="logo" class="app-form-label">
                                <i class="fas fa-image me-2"></i>Logo:
                            </label>
                            <input type="file" name="logo" class="app-form-control">
                            <div class="app-form-text">Logo qui apparaîtra sur les documents (format recommandé: PNG ou JPG)</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="id_bu" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Business Unit:
                            </label>
                            <select name="id_bu" class="app-form-select" required>
                                @foreach($businessUnits as $bu)
                                    <option value="{{ $bu->id }}">{{ $bu->name }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Business Unit associée à cette configuration</div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('config-global.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection