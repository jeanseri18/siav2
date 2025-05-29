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
                            <input type="text" class="app-form-control" value="{{ $configGlobal->businessUnit->name }}" disabled>
                            <div class="app-form-text">Business Unit associée à cette configuration</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="entete" class="app-form-label">
                                <i class="fas fa-heading me-2"></i>Entête
                            </label>
                            <input type="text" class="app-form-control" name="entete" value="{{ $configGlobal->entete }}" required>
                            <div class="app-form-text">En-tête qui apparaîtra sur les documents</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="numdepatfacture" class="app-form-label">
                                <i class="fas fa-sort-numeric-up me-2"></i>Numéro Départ Facture
                            </label>
                            <input type="text" class="app-form-control" name="numdepatfacture" value="{{ $configGlobal->numdepatfacture }}" required>
                            <div class="app-form-text">Numéro à partir duquel les factures seront numérotées</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="pieddepage" class="app-form-label">
                                <i class="fas fa-paragraph me-2"></i>Pied de Page
                            </label>
                            <textarea class="app-form-control" name="pieddepage" rows="3" required>{{ $configGlobal->pieddepage }}</textarea>
                            <div class="app-form-text">Pied de page qui apparaîtra sur les documents</div>
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