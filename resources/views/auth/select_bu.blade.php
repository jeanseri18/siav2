
{{-- Page de sélection BU améliorée --}}
@extends('layouts.auth')

@section('title', 'Sélection BU | SIA')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="auth-card">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/logo.png') }}" alt="SIA" class="logo mb-3">
                    <h2 class="auth-title">Sélection BU</h2>
                    <p class="auth-subtitle">Choisissez votre unité de gestion</p>
                </div>

                <form method="POST" action="{{ route('select.bu.post') }}" class="auth-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="bu_id" class="form-label">Unité de gestion (BU)</label>
                        <div class="select-wrapper">
                            <select name="bu_id" 
                                    id="bu_id" 
                                    class="form-control @error('bu_id') is-invalid @enderror" 
                                    required>
                                <option value="">Sélectionnez une BU</option>
                                @foreach($bus as $bu)
                                    <option value="{{ $bu->id }}" 
                                            {{ old('bu_id') == $bu->id ? 'selected' : '' }}>
                                        {{ $bu->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down select-arrow"></i>
                        </div>
                        @error('bu_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="fas fa-check me-2"></i>
                        Valider la sélection
                    </button>
                </form>

                <div class="auth-footer">
                    <p><a href="{{ route('logout') }}" class="text-link">Se déconnecter</a></p>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection