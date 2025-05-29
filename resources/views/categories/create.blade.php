{{-- Page Create - Créer une catégorie --}}
@extends('layouts.app')

@section('title', 'Créer une catégorie')
@section('page-title', 'Créer une catégorie')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Catégories</a></li>
<li class="breadcrumb-item active">Créer</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-6">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-tag me-2"></i>Créer une catégorie
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('categories.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Nom de la catégorie
                            </label>
                            <input type="text" name="nom" id="nom" class="app-form-control" required>
                            <div class="app-form-text">Entrez le nom de la nouvelle catégorie</div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('categories.index') }}" class="app-btn app-btn-secondary">
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