{{-- Page Create - Ajouter un Document --}}
@extends('layouts.app')

@section('title', 'Ajouter un Document')
@section('page-title', 'Ajouter un Document')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
@include('sublayouts.projetdetail')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow" style="background: var(--gray-100);">
                <div class="app-card-header">
                    <h2 class="app-card-title" style="color: var(--primary);">
                        <i class="fas fa-file me-2"></i>Ajouter un Document
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="app-form">
                        @csrf
                        
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Nom
                            </label>
                            <input type="text" name="nom" class="app-form-control" required>
                            <div class="app-form-text">Nom du document</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="fichier" class="app-form-label">
                                <i class="fas fa-file-upload me-2"></i>Fichier
                            </label>
                            <input type="file" name="fichier" class="app-form-control" required>
                            <div class="app-form-text">Sélectionnez le fichier à télécharger (PDF, Word, Excel, etc.)</div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('documents.index') }}" class="app-btn app-btn-secondary">
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