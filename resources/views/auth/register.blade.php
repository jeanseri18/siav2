{{-- Page d'inscription améliorée --}}
@extends('layouts.auth')

@section('title', 'Inscription |SIA')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-8 col-lg-6">
            <div class="auth-card">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/logo.png') }}" alt="Africa Travel Car" class="logo mb-3">
                    <h2 class="auth-title">Inscription</h2>
                    <p class="auth-subtitle">Créez votre compte pour commencer</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nom" class="form-label">Nom</label>
                                <div class="input-group">
                                    <span class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('nom') is-invalid @enderror" 
                                           name="nom" 
                                           id="nom"
                                           value="{{ old('nom') }}"
                                           placeholder="Votre nom"
                                           required>
                                </div>
                                @error('nom')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prenom" class="form-label">Prénom</label>
                                <div class="input-group">
                                    <span class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control @error('prenom') is-invalid @enderror" 
                                           name="prenom" 
                                           id="prenom"
                                           value="{{ old('prenom') }}"
                                           placeholder="Votre prénom"
                                           required>
                                </div>
                                @error('prenom')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Adresse email</label>
                        <div class="input-group">
                            <span class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   id="email"
                                   value="{{ old('email') }}"
                                   placeholder="votre@email.com"
                                   required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">Mot de passe</label>
                                <div class="input-group">
                                    <span class="input-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           name="password" 
                                           id="password"
                                           placeholder="••••••••"
                                           required>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirmation</label>
                                <div class="input-group">
                                    <span class="input-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           id="password_confirmation"
                                           placeholder="••••••••"
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bus" class="form-label">Associer à des BU</label>
                        <div class="select-wrapper">
                            <select name="bus[]" 
                                    id="bus" 
                                    class="form-control" 
                                    multiple 
                                    data-placeholder="Sélectionnez une ou plusieurs BU">
                                @foreach($bus as $bu)
                                    <option value="{{ $bu->id }}" 
                                            {{ in_array($bu->id, old('bus', [])) ? 'selected' : '' }}>
                                        {{ $bu->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                        <small class="form-text text-muted">
                            Maintenez Ctrl/Cmd pour sélectionner plusieurs options
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="fas fa-user-plus me-2"></i>
                        S'inscrire
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Déjà un compte ? <a href="{{ route('login') }}" class="text-link">Se connecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection