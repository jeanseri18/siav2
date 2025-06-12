@extends('layouts.app')

@section('title', 'Ajouter un Artisan')
@section('page-title', 'Ajouter un Artisan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('artisans.index') }}">Artisans</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-hard-hat me-2"></i>Ajouter un Artisan
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('artisans.store') }}" method="POST" class="app-form">
                @csrf
                
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
                                        <input type="radio" name="civilite" id="monsieur" value="Monsieur" required>
                                        <label for="monsieur">Monsieur</label>
                                    </div>
                                    <div class="app-form-radio">
                                        <input type="radio" name="civilite" id="madame" value="Madame" required>
                                        <label for="madame">Madame</label>
                                    </div>
                                    <div class="app-form-radio">
                                        <input type="radio" name="civilite" id="mademoiselle" value="Mademoiselle" required>
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
                        <input type="text" name="nom" id="nom" class="app-form-control" required>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="prenoms" class="app-form-label">
                            <i class="fas fa-user me-2"></i>Prénoms
                        </label>
                        <input type="text" name="prenoms" id="prenoms" class="app-form-control">
                    </div>
                    
                    <div class="app-form-group">
                        <label for="type_piece" class="app-form-label">
                            <i class="fas fa-id-card me-2"></i>Type de pièce <span class="text-danger">*</span>
                        </label>
                        <select name="type_piece" id="type_piece" class="app-form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="CNI">CNI</option>
                            <option value="Passeport">Passeport</option>
                            <option value="Permis">Permis de conduire</option>
                        </select>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="numero_piece" class="app-form-label">
                            <i class="fas fa-id-card me-2"></i>Numéro de pièce <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="numero_piece" id="numero_piece" class="app-form-control" required>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="date_naissance" class="app-form-label">
                            <i class="fas fa-calendar me-2"></i>Date de Naissance
                        </label>
                        <input type="date" name="date_naissance" id="date_naissance" class="app-form-control">
                    </div>
                    
                    <div class="app-form-group">
                        <label for="nationalite" class="app-form-label">
                            <i class="fas fa-flag me-2"></i>Nationalité
                        </label>
                        <input type="text" name="nationalite" id="nationalite" class="app-form-control">
                    </div>
                    
                    <div class="app-form-group">
                        <label for="fonction" class="app-form-label">
                            <i class="fas fa-briefcase me-2"></i>Fonction <span class="text-danger">*</span>
                        </label>
                        <select name="fonction" id="fonction" class="app-form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="Artisan">Artisan</option>
                                <option value="Ouvrier">Ouvrier</option>
                                <option value="Chef equipe">Chef equipe</option>
                        </select>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="localisation" class="app-form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Localisation <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="localisation" id="localisation" class="app-form-control" required>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="rcc" class="app-form-label">
                                    <i class="fas fa-id-badge me-2"></i>RCC
                                </label>
                                <input type="text" name="rcc" id="rcc" class="app-form-control">
                            </div>
                        </div>
                        
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="rccm" class="app-form-label">
                                    <i class="fas fa-id-badge me-2"></i>RCCM
                                </label>
                                <input type="text" name="rccm" id="rccm" class="app-form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="boite_postale" class="app-form-label">
                            <i class="fas fa-mailbox me-2"></i>Boîte postale
                        </label>
                        <input type="text" name="boite_postale" id="boite_postale" class="app-form-control">
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="tel1" class="app-form-label">
                                    <i class="fas fa-phone me-2"></i>Tél 1 <span class="text-danger">*</span>
                                </label>
                                <input type="tel" name="tel1" id="tel1" class="app-form-control" required>
                            </div>
                        </div>
                        
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label for="tel2" class="app-form-label">
                                    <i class="fas fa-phone me-2"></i>Tél 2
                                </label>
                                <input type="tel" name="tel2" id="tel2" class="app-form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label for="mail" class="app-form-label">
                            <i class="fas fa-envelope me-2"></i>Mail
                        </label>
                        <input type="email" name="mail" id="mail" class="app-form-control">
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('artisans.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection