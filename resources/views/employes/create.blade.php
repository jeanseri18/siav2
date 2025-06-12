@extends('layouts.app')

@section('title', 'Ajouter un Employé')
@section('page-title', 'Ajouter un Employé')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
<li class="breadcrumb-item"><a href="{{ route('employes.index') }}">Employés</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-user-plus me-2"></i>Ajouter un Employé
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('employes.index') }}" class="app-btn app-btn-secondary app-btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>
        </div>
        
        <div class="app-card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('employes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Informations personnelles -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2"></i>Informations Personnelles
                        </h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                               id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror" 
                               id="telephone" name="telephone" value="{{ old('telephone') }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="date_naissance" class="form-label">Date de Naissance</label>
                        <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" 
                               id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}">
                        @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="sexe" class="form-label">Sexe</label>
                        <select class="form-select @error('sexe') is-invalid @enderror" id="sexe" name="sexe">
                            <option value="">Sélectionner...</option>
                            <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                        @error('sexe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="lieu_naissance" class="form-label">Lieu de Naissance</label>
                        <input type="text" class="form-control @error('lieu_naissance') is-invalid @enderror" 
                               id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance') }}">
                        @error('lieu_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="nationalite" class="form-label">Nationalité</label>
                        <input type="text" class="form-control @error('nationalite') is-invalid @enderror" 
                               id="nationalite" name="nationalite" value="{{ old('nationalite') }}">
                        @error('nationalite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="situation_matrimoniale" class="form-label">Situation Matrimoniale</label>
                        <select class="form-select @error('situation_matrimoniale') is-invalid @enderror" 
                                id="situation_matrimoniale" name="situation_matrimoniale">
                            <option value="">Sélectionner...</option>
                            <option value="celibataire" {{ old('situation_matrimoniale') === 'celibataire' ? 'selected' : '' }}>Célibataire</option>
                            <option value="marie" {{ old('situation_matrimoniale') === 'marie' ? 'selected' : '' }}>Marié(e)</option>
                            <option value="divorce" {{ old('situation_matrimoniale') === 'divorce' ? 'selected' : '' }}>Divorcé(e)</option>
                            <option value="veuf" {{ old('situation_matrimoniale') === 'veuf' ? 'selected' : '' }}>Veuf/Veuve</option>
                        </select>
                        @error('situation_matrimoniale')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                  id="adresse" name="adresse" rows="3">{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="photo" class="form-label">Photo de Profil</label>
                        <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" name="photo" accept="image/*">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</div>
                    </div>
                </div>
                
                <!-- Informations professionnelles -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-briefcase me-2"></i>Informations Professionnelles
                        </h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Rôle/Fonction <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Sélectionner un rôle...</option>
                            <option value="chef_projet" {{ old('role') === 'chef_projet' ? 'selected' : '' }}>Chef de Projet</option>
                            <option value="conducteur_travaux" {{ old('role') === 'conducteur_travaux' ? 'selected' : '' }}>Conducteur de Travaux</option>
                            <option value="chef_chantier" {{ old('role') === 'chef_chantier' ? 'selected' : '' }}>Chef de Chantier</option>
                            <option value="comptable" {{ old('role') === 'comptable' ? 'selected' : '' }}>Comptable</option>
                            <option value="magasinier" {{ old('role') === 'magasinier' ? 'selected' : '' }}>Magasinier</option>
                            <option value="acheteur" {{ old('role') === 'acheteur' ? 'selected' : '' }}>Acheteur</option>
                            <option value="controleur_gestion" {{ old('role') === 'controleur_gestion' ? 'selected' : '' }}>Contrôleur de Gestion</option>
                            <option value="secretaire" {{ old('role') === 'secretaire' ? 'selected' : '' }}>Secrétaire</option>
                            <option value="chauffeur" {{ old('role') === 'chauffeur' ? 'selected' : '' }}>Chauffeur</option>
                            <option value="gardien" {{ old('role') === 'gardien' ? 'selected' : '' }}>Gardien</option>
                            <option value="employe" {{ old('role') === 'employe' ? 'selected' : '' }}>Employé</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="poste" class="form-label">Poste/Titre</label>
                        <input type="text" class="form-control @error('poste') is-invalid @enderror" 
                               id="poste" name="poste" value="{{ old('poste') }}">
                        @error('poste')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="date_embauche" class="form-label">Date d'Embauche</label>
                        <input type="date" class="form-control @error('date_embauche') is-invalid @enderror" 
                               id="date_embauche" name="date_embauche" value="{{ old('date_embauche') }}">
                        @error('date_embauche')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="salaire" class="form-label">Salaire (FCFA)</label>
                        <input type="number" class="form-control @error('salaire') is-invalid @enderror" 
                               id="salaire" name="salaire" value="{{ old('salaire') }}" min="0" step="1000">
                        @error('salaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="numero_cnss" class="form-label">Numéro CNSS</label>
                        <input type="text" class="form-control @error('numero_cnss') is-invalid @enderror" 
                               id="numero_cnss" name="numero_cnss" value="{{ old('numero_cnss') }}">
                        @error('numero_cnss')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="actif" {{ old('status', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('status') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Documents d'identité -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-id-card me-2"></i>Documents d'Identité
                        </h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="numero_cni" class="form-label">Numéro CNI</label>
                        <input type="text" class="form-control @error('numero_cni') is-invalid @enderror" 
                               id="numero_cni" name="numero_cni" value="{{ old('numero_cni') }}">
                        @error('numero_cni')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="numero_passeport" class="form-label">Numéro Passeport</label>
                        <input type="text" class="form-control @error('numero_passeport') is-invalid @enderror" 
                               id="numero_passeport" name="numero_passeport" value="{{ old('numero_passeport') }}">
                        @error('numero_passeport')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Mot de passe -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-lock me-2"></i>Accès au Système
                        </h5>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mot de Passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Minimum 8 caractères</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le Mot de Passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('employes.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-times me-2"></i>Annuler
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