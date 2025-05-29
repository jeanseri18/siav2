{{-- Page Show - Détails d'un Document --}}
@extends('layouts.app')

@section('title', 'Détails du Document')
@section('page-title', 'Détails du Document')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
<li class="breadcrumb-item active">{{ $document->nom }}</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-file-alt me-2"></i>Détails du Document
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-tag me-2"></i>Nom:
                        </label>
                        <div class="app-form-control-static">{{ $document->nom }}</div>
                    </div>
                    
                    <div class="app-form-group">
                        <label class="app-form-label">
                            <i class="fas fa-file me-2"></i>Fichier:
                        </label>
                        <div class="app-form-control-static">
                            <a href="{{ asset('storage/' . $document->chemin) }}" class="app-btn app-btn-info app-btn-icon" target="_blank">
                                <i class="fas fa-eye me-2"></i>Voir le document
                            </a>
                        </div>
                    </div>
                    
                    <div class="app-card-footer">
                        <a href="{{ route('documents.index') }}" class="app-btn app-btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection