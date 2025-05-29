{{-- Page Edit - Modifier un Corps de Métier --}}
@extends('layouts.app')

@section('title', 'Modifier un Corps de Métier')
@section('page-title', 'Modifier un Corps de Métier')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('corpsmetiers.index') }}">Corps de Métier</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-6">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-edit me-2"></i>Modifier un Corps de Métier
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('corpsmetiers.update', $corpsMetier->id) }}" method="POST" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Nom
                            </label>
                            <input type="text" name="nom" class="app-form-control" value="{{ $corpsMetier->nom }}" required>
                            <div class="app-form-text">Nom du corps de métier</div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('corpsmetiers.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection