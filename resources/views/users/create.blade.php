@extends('layouts.app')

@section('title', 'Créer un Utilisateur')
@section('page-title', 'Créer un Utilisateur')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">Utilisateurs</a></li>
<li class="breadcrumb-item active">Créer</li>
@endsection

@section('content')

<div class="app-fade-in">
    <form action="{{ route('users.store') }}" method="POST" class="app-form">
        @csrf
        
        <div class="row">
            <div class="col-md-6">
                <div class="app-card">
                    <div class="app-card-header">
                        <h2 class="app-card-title">
                            <i class="fas fa-user-plus me-2"></i>Créer un Utilisateur
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
                                        {{ $error }}<br>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endif
                        
                        <div class="app-form-group">
                            <label for="name" class="app-form-label">
                                <i class="fas fa-user me-2"></i>Nom
                            </label>
                            <input type="text" name="name" id="name" class="app-form-control" required>
                            <div class="app-form-text">Nom complet de l'utilisateur</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="email" class="app-form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" name="email" id="email" class="app-form-control" required>
                            <div class="app-form-text">Adresse email de l'utilisateur</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="password" class="app-form-label">
                                <i class="fas fa-lock me-2"></i>Mot de passe
                            </label>
                            <input type="password" name="password" id="password" class="app-form-control" required>
                            <div class="app-form-text">Mot de passe sécurisé</div>
                        </div>
                        
                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="role" class="app-form-label">
                                        <i class="fas fa-user-tag me-2"></i>Rôle
                                    </label>
                                    <select name="role" id="role" class="app-form-select">
                                        <option value="utilisateur">Utilisateur</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <div class="app-form-text">Niveau d'accès de l'utilisateur</div>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="status" class="app-form-label">
                                        <i class="fas fa-toggle-on me-2"></i>Statut
                                    </label>
                                    <select name="status" id="status" class="app-form-select">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                    </select>
                                    <div class="app-form-text">État du compte utilisateur</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="app-card">
                    <div class="app-card-header">
                        <h2 class="app-card-title">
                            <i class="fas fa-building me-2"></i>Assignation des Bus
                        </h2>
                    </div>
                    
                    <div class="app-card-body">
                        <div class="app-form-group">
                            @foreach($buses as $bus)
                            <div class="app-form-check">
                                <input class="app-form-check-input" type="checkbox" name="buses[]" value="{{ $bus->id }}" id="bus{{ $bus->id }}">
                                <label class="app-form-check-label" for="bus{{ $bus->id }}">
                                    {{ $bus->nom }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="app-mt-4 app-d-flex app-justify-content-end">
            <a href="{{ route('users.index') }}" class="app-btn app-btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
            <button type="submit" class="app-btn app-btn-primary app-ms-2">
                <i class="fas fa-save me-2"></i>Créer l'utilisateur
            </button>
        </div>
    </form>
</div>
@endsection