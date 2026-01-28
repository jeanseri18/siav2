{{-- Page Edit - Modifier le Profil --}}
@extends('layouts.app')

@section('title', 'Modifier mon Profil')
@section('page-title', 'Modifier mon Profil')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Mon Profil</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="container app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-edit me-2"></i>Modifier mon Profil
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('profile.show') }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Photo de profil -->
                        <div class="app-card mb-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-camera me-2"></i>Photo de Profil
                                </h3>
                            </div>
                            <div class="app-card-body">
                                <div class="text-center mb-3">
                                    @if($user->photo)
                                        <img src="{{ Storage::url($user->photo) }}" alt="Photo actuelle" id="photo-preview"
                                             class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                        
                                        <div class="mt-2">
                                            <form action="{{ route('profile.delete-photo') }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="app-btn app-btn-danger app-btn-sm" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')">
                                                    <i class="fas fa-trash me-1"></i>Supprimer la photo
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        @php
                                            $initials = collect(explode(' ', $user->nom))->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))->join('');
                                        @endphp
                                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                             style="width: 120px; height: 120px; font-size: 2.5rem; font-weight: bold;" id="photo-preview">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="app-form-group">
                                    <label for="photo" class="app-form-label">
                                        <i class="fas fa-upload me-2"></i>Nouvelle photo
                                    </label>
                                    <input type="file" class="app-form-control @error('photo') is-invalid @enderror" 
                                           id="photo" name="photo" accept="image/*" onchange="previewPhoto(this)">
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="app-form-text">
                                        Formats acceptés: JPEG, PNG, JPG, GIF. Taille maximale: 2MB.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations personnelles -->
                        <div class="app-card mb-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-user me-2"></i>Informations Personnelles
                                </h3>
                            </div>
                            <div class="app-card-body">
                                <div class="app-form-row">
                                    <div class="app-form-col-6">
                                        <div class="app-form-group">
                                            <label for="name" class="app-form-label">
                                                <i class="fas fa-user me-2"></i>Nom complet <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="app-form-control @error('name') is-invalid @enderror" 
                                                   id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required
                                                   placeholder="Votre nom complet">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Votre nom et prénom</div>
                                        </div>
                                    </div>
                                    <div class="app-form-col-6">
                                        <div class="app-form-group">
                                            <label for="email" class="app-form-label">
                                                <i class="fas fa-envelope me-2"></i>Adresse email <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="app-form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email', $user->email) }}" required
                                                   placeholder="votre@email.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="app-form-text">Cette adresse sera utilisée pour la connexion</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations en lecture seule -->
                        <div class="app-card mb-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-info-circle me-2"></i>Informations Système
                                </h3>
                            </div>
                            <div class="app-card-body">
                                <div class="app-form-row">
                                    <div class="app-form-col-6">
                                        <div class="app-form-group">
                                            <label class="app-form-label">
                                                <i class="fas fa-user-tag me-2"></i>Rôle
                                            </label>
                                            <div class="app-form-control bg-light">
                                                @php
                                                    $roleClass = '';
                                                    $roleIcon = '';
                                                    switch($user->role) {
                                                        case 'admin':
                                                            $roleClass = 'danger';
                                                            $roleIcon = 'user-shield';
                                                            break;
                                                        case 'dg':
                                                            $roleClass = 'primary';
                                                            $roleIcon = 'crown';
                                                            break;
                                                        case 'chefprojet':
                                                            $roleClass = 'info';
                                                            $roleIcon = 'user-tie';
                                                            break;
                                                        case 'utilisateur':
                                                            $roleClass = 'secondary';
                                                            $roleIcon = 'user';
                                                            break;
                                                        default:
                                                            $roleClass = 'light';
                                                            $roleIcon = 'question-circle';
                                                    }
                                                @endphp
                                                <span class="app-badge app-badge-{{ $roleClass }} app-badge-pill">
                                                    <i class="fas fa-{{ $roleIcon }} me-1"></i> 
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </div>
                                            <div class="app-form-text">Votre rôle ne peut pas être modifié</div>
                                        </div>
                                    </div>
                                    <div class="app-form-col-6">
                                        <div class="app-form-group">
                                            <label class="app-form-label">
                                                <i class="fas fa-toggle-on me-2"></i>Statut
                                            </label>
                                            <div class="app-form-control bg-light">
                                                @if($user->status === 'actif')
                                                    <span class="app-badge app-badge-success app-badge-pill">
                                                        <i class="fas fa-check-circle me-1"></i> Actif
                                                    </span>
                                                @else
                                                    <span class="app-badge app-badge-danger app-badge-pill">
                                                        <i class="fas fa-times-circle me-1"></i> Inactif
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="app-form-text">Géré par l'administrateur</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-card-footer">
                            <a href="{{ route('profile.show') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Panel latéral avec liens rapides -->
        <div class="col-md-4">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h3>
                </div>
                <div class="app-card-body app-d-grid app-gap-2">
                    <a href="{{ route('profile.show') }}" class="app-btn app-btn-outline-secondary w-100">
                        <i class="fas fa-eye me-2"></i>Voir mon profil
                    </a>
                    <a href="{{ route('profile.edit-password') }}" class="app-btn app-btn-primary w-100">
                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="app-btn app-btn-danger w-100" 
                                onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                            <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Conseils -->
            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-lightbulb me-2"></i>Conseils
                    </h3>
                </div>
                <div class="app-card-body">
                    <div class="app-alert app-alert-info">
                        <div class="app-alert-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="app-alert-content">
                            <div class="app-alert-text">
                                <strong>Photo de profil :</strong> Utilisez une photo claire de votre visage pour faciliter l'identification.
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-alert app-alert-warning">
                        <div class="app-alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="app-alert-content">
                            <div class="app-alert-text">
                                <strong>Email :</strong> Si vous changez votre email, vous devrez l'utiliser pour vous connecter.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            
            // Si c'est une image, remplacer par la nouvelle
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Si c'est un div avec initiales, créer une nouvelle image
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.alt = 'Aperçu de la photo';
                newImg.id = 'photo-preview';
                newImg.className = 'img-fluid rounded-circle mb-3';
                newImg.style.width = '120px';
                newImg.style.height = '120px';
                newImg.style.objectFit = 'cover';
                
                preview.parentNode.replaceChild(newImg, preview);
            }
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Validation côté client
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validation du nom
        if (nameInput.value.trim().length < 2) {
            nameInput.classList.add('is-invalid');
            isValid = false;
        } else {
            nameInput.classList.remove('is-invalid');
        }
        
        // Validation de l'email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            emailInput.classList.add('is-invalid');
            isValid = false;
        } else {
            emailInput.classList.remove('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
@endsection