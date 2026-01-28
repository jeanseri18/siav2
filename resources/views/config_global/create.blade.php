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
                            <label for="logo" class="app-form-label">
                                <i class="fas fa-image me-2"></i>Logo:
                            </label>
                            <input type="file" name="logo" class="app-form-control">
                            <div class="app-form-text">Logo qui apparaîtra sur les documents (format recommandé: PNG ou JPG)</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="nom_entreprise" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Nom de l'entreprise:
                            </label>
                            <input type="text" name="nom_entreprise" class="app-form-control">
                            <div class="app-form-text">Nom officiel de l'entreprise</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="localisation" class="app-form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Localisation:
                            </label>
                            <input type="text" name="localisation" class="app-form-control">
                            <div class="app-form-text">Localisation de l'entreprise</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="adresse_postale" class="app-form-label">
                                <i class="fas fa-envelope me-2"></i>Adresse postale:
                            </label>
                            <textarea name="adresse_postale" class="app-form-control" rows="3"></textarea>
                            <div class="app-form-text">Adresse postale complète</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="rccm" class="app-form-label">
                                        <i class="fas fa-certificate me-2"></i>N° RCCM:
                                    </label>
                                    <input type="text" name="rccm" class="app-form-control">
                                    <div class="app-form-text">Numéro RCCM</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="cc" class="app-form-label">
                                        <i class="fas fa-id-card me-2"></i>N° CC:
                                    </label>
                                    <input type="text" name="cc" class="app-form-control">
                                    <div class="app-form-text">Numéro CC</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="tel1" class="app-form-label">
                                        <i class="fas fa-phone me-2"></i>Téléphone 1:
                                    </label>
                                    <input type="text" name="tel1" class="app-form-control">
                                    <div class="app-form-text">Numéro de téléphone principal</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="tel2" class="app-form-label">
                                        <i class="fas fa-phone me-2"></i>Téléphone 2:
                                    </label>
                                    <input type="text" name="tel2" class="app-form-control">
                                    <div class="app-form-text">Numéro de téléphone secondaire</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="email" class="app-form-label">
                                <i class="fas fa-at me-2"></i>Email:
                            </label>
                            <input type="email" name="email" class="app-form-control">
                            <div class="app-form-text">Adresse email de l'entreprise</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="id_bu" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Business Unit:
                            </label>
                            <select name="id_bu" class="app-form-select" required>
                                @foreach($businessUnits as $bu)
                                    <option value="{{ $bu->id }}">{{ $bu->nom }}</option>
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