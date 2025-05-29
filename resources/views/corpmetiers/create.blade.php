{{-- Page Create - Ajouter un Corps de Métier --}}
@extends('layouts.app')

@section('title', 'Ajouter un Corps de Métier')
@section('page-title', 'Ajouter un Corps de Métier')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('corpsmetiers.index') }}">Corps de Métier</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-6">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-hard-hat me-2"></i>Ajouter un Corps de Métier
                    </h2>
                </div>
                
                <div class="app-card-body">
                    @if ($errors->any())
                    <div class="app-alert app-alert-danger">
                        <div class="app-alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="app-alert-content">
                            <div class="app-alert-text">
                                @foreach ($errors->all() as $error)
                                    <p class="app-mb-1">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                    
                    <form action="{{ route('corpsmetiers.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Nom
                            </label>
                            <input type="text" name="nom" class="app-form-control" required>
                            <div class="app-form-text">Nom du corps de métier</div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('corpsmetiers.index') }}" class="app-btn app-btn-secondary">
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