


{{-- Page de connexion améliorée --}}
@extends('layouts.auth')

@section('title', 'Connexion | SIA')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="auth-card">
                <div class="text-center mb-4">
                    <img src="{{ asset('Logo_XBTP_Png/Logo_Noir.png') }}" alt="XBTP" class="logo mb-3">
                    <h2 class="auth-title">Connexion</h2>
                    <p class="auth-subtitle">Accédez à votre espace personnel</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf
                    
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
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Se connecter
                    </button>
                </form>

                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="auth-footer">
                    <p><a href="#" class="text-link">Mot de passe oublié ?</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection