{{-- Page Edit - Modifier une Configuration --}}
@extends('layouts.app')

@section('title', 'Modifier une Configuration')
@section('page-title', 'Modifier une Configuration')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('config-global.index') }}">Configurations</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-cog me-2"></i>Modifier la Configuration
                    </h2>
                </div>
                
                <div class="app-card-body">
                    @if(session('error'))
                    <div class="app-alert app-alert-danger">
                        <div class="app-alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="app-alert-content">
                            <div class="app-alert-text">{{ session('error') }}</div>
                        </div>
                        <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                    
                    <form action="{{ route('config-global.update', $configGlobal->id) }}" method="POST" enctype="multipart/form-data" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-form-group">
                            <label for="bu_name" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Business Unit
                            </label>
                            <input type="text" class="app-form-control" value="{{ $configGlobal->businessUnit->nom }}" disabled>
                            <div class="app-form-text">Business Unit associée à cette configuration</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="nom_entreprise" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Nom de l'entreprise
                            </label>
                            <input type="text" class="app-form-control" name="nom_entreprise" value="{{ $configGlobal->nom_entreprise }}">
                            <div class="app-form-text">Nom officiel de l'entreprise</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="localisation" class="app-form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Localisation
                            </label>
                            <input type="text" class="app-form-control" name="localisation" value="{{ $configGlobal->localisation }}">
                            <div class="app-form-text">Localisation de l'entreprise</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="adresse_postale" class="app-form-label">
                                <i class="fas fa-envelope me-2"></i>Adresse postale
                            </label>
                            <textarea class="app-form-control" name="adresse_postale" rows="3">{{ $configGlobal->adresse_postale }}</textarea>
                            <div class="app-form-text">Adresse postale complète</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="rccm" class="app-form-label">
                                        <i class="fas fa-certificate me-2"></i>N° RCCM
                                    </label>
                                    <input type="text" class="app-form-control" name="rccm" value="{{ $configGlobal->rccm }}">
                                    <div class="app-form-text">Numéro RCCM</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="cc" class="app-form-label">
                                        <i class="fas fa-id-card me-2"></i>N° CC
                                    </label>
                                    <input type="text" class="app-form-control" name="cc" value="{{ $configGlobal->cc }}">
                                    <div class="app-form-text">Numéro CC</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="tel1" class="app-form-label">
                                        <i class="fas fa-phone me-2"></i>Téléphone 1
                                    </label>
                                    <input type="text" class="app-form-control" name="tel1" value="{{ $configGlobal->tel1 }}">
                                    <div class="app-form-text">Numéro de téléphone principal</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="app-form-group">
                                    <label for="tel2" class="app-form-label">
                                        <i class="fas fa-phone me-2"></i>Téléphone 2
                                    </label>
                                    <input type="text" class="app-form-control" name="tel2" value="{{ $configGlobal->tel2 }}">
                                    <div class="app-form-text">Numéro de téléphone secondaire</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="email" class="app-form-label">
                                <i class="fas fa-at me-2"></i>Email
                            </label>
                            <input type="email" class="app-form-control" name="email" value="{{ $configGlobal->email }}">
                            <div class="app-form-text">Adresse email de l'entreprise</div>
                        </div>
                        

                        
                        <div class="app-form-group">
                            <label for="logo" class="app-form-label">
                                <i class="fas fa-image me-2"></i>Logo
                            </label>
                            @if($configGlobal->logo)
                                <div class="app-mt-2 app-mb-3">
                                    <img src="{{ asset('storage/' . $configGlobal->logo) }}" alt="Logo actuel" class="img-fluid" style="max-width: 150px; border-radius: var(--border-radius-md); border: 1px solid var(--gray-200);">
                                </div>
                            @endif
                            <input type="file" class="app-form-control" name="logo">
                            <div class="app-form-text">Logo qui apparaîtra sur les documents (format recommandé: PNG ou JPG)</div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('config-global.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à Jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection