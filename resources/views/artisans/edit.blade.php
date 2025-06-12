@extends('layouts.app')

@section('title', 'Modifier un Artisan')
@section('page-title', 'Modifier un Artisan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('artisans.index') }}">Artisans</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier l'Artisan: {{ $artisan->nom }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('artisans.update', $artisan->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <!-- Informations de base -->
                <div class="app-form-section">
                    <h3 class="app-form-section-title">
                        <i class="fas fa-user me-2"></i>Informations de base
                    </h3>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="civilite" class="app-form-label">
                                    <i class="fas fa-user me-2"></i>Civilité <span class="text-danger">*</span>
                                </label>
                                <div class="app-form-radio-group">
                                    <div class="app-form-radio">
                                        <input type="radio" name="civilite" id="monsieur" value="Monsieur" {{ old('civilite', $artisan->civilite) == 'Monsieur' ? 'checked' : '' }} required>
                                        <label for="monsieur">Monsieur</label>
                                    </div>
                                    <div class="app-form-radio">
                                        <input type="radio" name="civilite" id="madame" value="Madame" {{ old('civilite', $artisan->civilite) == 'Madame' ? 'checked' : '' }} required>
                                        <label for="madame">Madame</label>
                                    </div>
                                    <div class="app-form-radio">
                                        <input type="radio" name="civilite" id="mademoiselle" value="Mademoiselle" {{ old('civilite', $artisan->civilite) == 'Mademoiselle' ? 'checked' : '' }} required>
                                        <label for="mademoiselle">Mademoiselle</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="nom" class="app-form-label">
                            <i class="fas fa-user me-2"></i>Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" class="app-form-control" value="{{ old('nom', $artisan->nom) }}" required>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="prenoms" class="app-form-label">
                            <i class="fas fa-user me-2"></i>Prénoms
                        </label>
                        <input type="text" name="prenoms" id="prenoms" class="app-form-control" value="{{ old('prenoms', $artisan->prenoms) }}">
                    </div>
                    
                    <div class="app-form-group">
                        <label for="type_piece" class="app-form-label">
                            <i class="fas fa-id-card me-2"></i>Type de pièce <span class="text-danger">*</span>
                        </label>
                        <select name="type_piece" id="type_piece" class="app-form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="CNI" {{ old('type_piece', $artisan->type_piece) == 'CNI' ? 'selected' : '' }}>CNI</option>
                            <option value="Passeport" {{ old('type_piece', $artisan->type_piece) == 'Passeport' ? 'selected' : '' }}>Passeport</option>
                            <option value="Permis" {{ old('type_piece', $artisan->type_piece) == 'Permis' ? 'selected' : '' }}>Permis de conduire</option>
                        </select>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="numero_piece" class="app-form-label">
                            <i class="fas fa-id-card me-2"></i>Numéro de pièce <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="numero_piece" id="numero_piece" class="app-form-control" value="{{ old('numero_piece', $artisan->numero_piece) }}" required>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="date_naissance" class="app-form-label">
                            <i class="fas fa-calendar me-2"></i>Date de Naissance
                        </label>
                        <input type="date" name="date_naissance" id="date_naissance" class="app-form-control" value="{{ old('date_naissance', $artisan->date_naissance) }}">
                    </div>
                    
                    <div class="app-form-group">
                        <label for="nationalite" class="app-form-label">
                            <i class="fas fa-flag me-2"></i>Nationalité
                        </label>
                        <input type="text" name="nationalite" id="nationalite" class="app-form-control" value="{{ old('nationalite', $artisan->nationalite) }}">
                    </div>
                    
                    <div class="app-form-group">
                        <label for="fonction" class="app-form-label">
                            <i class="fas fa-briefcase me-2"></i>Fonction <span class="text-danger">*</span>
                        </label>
                        <select name="fonction" id="fonction" class="app-form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="Artisan" {{ old('fonction', $artisan->fonction) == 'Artisan' ? 'selected' : '' }}>Artisan</option>
                            <option value="Ouvrier" {{ old('fonction', $artisan->fonction) == 'Ouvrier' ? 'selected' : '' }}>Ouvrier</option>
                            <option value="Chef equipe" {{ old('fonction', $artisan->fonction) == 'Chef equipe' ? 'selected' : '' }}>Chef equipe</option>
                        </select>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="localisation" class="app-form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Localisation <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="localisation" id="localisation" class="app-form-control" value="{{ old('localisation', $artisan->localisation) }}" required>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="rcc" class="app-form-label">
                                    <i class="fas fa-id-badge me-2"></i>RCC
                                </label>
                                <input type="text" name="rcc" id="rcc" class="app-form-control" value="{{ old('rcc', $artisan->rcc) }}">
                            </div>
                        </div>
                        
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="rccm" class="app-form-label">
                                    <i class="fas fa-id-badge me-2"></i>RCCM
                                </label>
                                <input type="text" name="rccm" id="rccm" class="app-form-control" value="{{ old('rccm', $artisan->rccm) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="boite_postale" class="app-form-label">
                            <i class="fas fa-mailbox me-2"></i>Boîte postale
                        </label>
                        <input type="text" name="boite_postale" id="boite_postale" class="app-form-control" value="{{ old('boite_postale', $artisan->boite_postale) }}">
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="tel1" class="app-form-label">
                                    <i class="fas fa-phone me-2"></i>Tél 1 <span class="text-danger">*</span>
                                </label>
                                <input type="tel" name="tel1" id="tel1" class="app-form-control" value="{{ old('tel1', $artisan->tel1) }}" required>
                            </div>
                        </div>
                        
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="tel2" class="app-form-label">
                                    <i class="fas fa-phone me-2"></i>Tél 2
                                </label>
                                <input type="tel" name="tel2" id="tel2" class="app-form-control" value="{{ old('tel2', $artisan->tel2) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="mail" class="app-form-label">
                            <i class="fas fa-envelope me-2"></i>Mail
                        </label>
                        <input type="email" name="mail" id="mail" class="app-form-control" value="{{ old('mail', $artisan->mail) }}">
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('artisans.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-warning">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection