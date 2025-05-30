{{-- Page Edit Password - Modifier le Mot de Passe --}}
@extends('layouts.app')

@section('title', 'Changer mon Mot de Passe')
@section('page-title', 'Changer mon Mot de Passe')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Mon Profil</a></li>
<li class="breadcrumb-item active">Changer le mot de passe</li>
@endsection

@section('content')
<div class="container app-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-key me-2"></i>Changer mon Mot de Passe
                    </h2>
                    <div class="app-card-actions">
                        <a href="{{ route('profile.show') }}" class="app-btn app-btn-secondary app-btn-sm app-btn-icon">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('profile.update-password') }}" method="POST" class="app-form" id="password-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-alert app-alert-info">
                            <div class="app-alert-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="app-alert-content">
                                <div class="app-alert-heading">Sécurité du mot de passe</div>
                                <div class="app-alert-text">
                                    Votre nouveau mot de passe doit contenir au moins 8 caractères, incluant des majuscules, 
                                    des minuscules, des chiffres et des symboles.
                                </div>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="current_password" class="app-form-label">
                                <i class="fas fa-lock me-2"></i>Mot de passe actuel <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="app-form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required
                                       placeholder="Saisissez votre mot de passe actuel">
                                <button class="app-btn app-btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="app-form-text">Votre mot de passe actuel pour confirmation</div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="password" class="app-form-label">
                                <i class="fas fa-key me-2"></i>Nouveau mot de passe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="app-form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required
                                       placeholder="Saisissez votre nouveau mot de passe">
                                <button class="app-btn app-btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password_icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="app-form-text">Minimum 8 caractères avec majuscules, minuscules, chiffres et symboles</div>
                            
                            <!-- Indicateur de force du mot de passe -->
                            <div class="password-strength mt-2">
                                <div class="password-strength-bar">
                                    <div class="password-strength-fill" id="password-strength-fill"></div>
                                </div>
                                <small class="password-strength-text" id="password-strength-text">Saisissez un mot de passe</small>
                            </div>
                        </div>
                        
                        <div class="app-form-group">
                            <label for="password_confirmation" class="app-form-label">
                                <i class="fas fa-check-double me-2"></i>Confirmer le nouveau mot de passe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" class="app-form-control" 
                                       id="password_confirmation" name="password_confirmation" required
                                       placeholder="Confirmez votre nouveau mot de passe">
                                <button class="app-btn app-btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                            <div class="app-form-text">Ressaisissez le nouveau mot de passe</div>
                        </div>
                        
                        <!-- Critères de sécurité -->
                        <div class="app-card mt-4">
                            <div class="app-card-header">
                                <h3 class="app-card-title">
                                    <i class="fas fa-list-check me-2"></i>Critères de sécurité
                                </h3>
                            </div>
                            <div class="app-card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2" id="length-check">
                                        <i class="fas fa-times text-danger me-2"></i>Au moins 8 caractères
                                    </li>
                                    <li class="mb-2" id="uppercase-check">
                                        <i class="fas fa-times text-danger me-2"></i>Au moins une majuscule
                                    </li>
                                    <li class="mb-2" id="lowercase-check">
                                        <i class="fas fa-times text-danger me-2"></i>Au moins une minuscule
                                    </li>
                                    <li class="mb-2" id="number-check">
                                        <i class="fas fa-times text-danger me-2"></i>Au moins un chiffre
                                    </li>
                                    <li class="mb-0" id="symbol-check">
                                        <i class="fas fa-times text-danger me-2"></i>Au moins un symbole (!@#$%^&*)
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="app-card-footer mt-4">
                            <a href="{{ route('profile.show') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="app-btn app-btn-primary" id="submit-btn" disabled>
                                <i class="fas fa-save me-2"></i>Changer le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Panel latéral avec conseils -->
        <div class="col-md-4">
            <div class="app-card">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-shield-alt me-2"></i>Sécurité
                    </h3>
                </div>
                <div class="app-card-body">
                    <div class="app-alert app-alert-warning">
                        <div class="app-alert-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="app-alert-content">
                            <div class="app-alert-text">
                                <strong>Important :</strong> Après avoir changé votre mot de passe, 
                                vous devrez vous reconnecter avec le nouveau mot de passe.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-card mt-4">
                <div class="app-card-header">
                    <h3 class="app-card-title">
                        <i class="fas fa-lightbulb me-2"></i>Conseils
                    </h3>
                </div>
                <div class="app-card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Utilisez des phrases</strong><br>
                            <small class="text-muted">Ex: "J'aime2Voyager!" est plus sûr que "aB3$fG9k"</small>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Évitez les informations personnelles</strong><br>
                            <small class="text-muted">Pas de nom, date de naissance, etc.</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Changez régulièrement</strong><br>
                            <small class="text-muted">Tous les 6 mois idéalement</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.password-strength {
    width: 100%;
}

.password-strength-bar {
    width: 100%;
    height: 8px;
    background-color: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
}

.password-strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 4px;
}

.password-strength-text {
    display: block;
    margin-top: 4px;
    font-weight: 500;
}

.strength-weak .password-strength-fill {
    width: 25%;
    background-color: var(--danger);
}

.strength-fair .password-strength-fill {
    width: 50%;
    background-color: var(--warning);
}

.strength-good .password-strength-fill {
    width: 75%;
    background-color: var(--info);
}

.strength-strong .password-strength-fill {
    width: 100%;
    background-color: var(--success);
}

.input-group .app-btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>
@endpush

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkPasswordStrength(password) {
    let score = 0;
    let feedback = 'Très faible';
    
    // Critères de validation
    const criteria = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        symbol: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Mise à jour des indicateurs visuels
    updateCriteriaDisplay(criteria);
    
    // Calcul du score
    Object.values(criteria).forEach(met => {
        if (met) score++;
    });
    
    // Détermination de la force
    if (score === 5) {
        feedback = 'Très fort';
        document.querySelector('.password-strength').className = 'password-strength strength-strong';
        document.getElementById('password-strength-text').textContent = feedback;
        document.getElementById('password-strength-text').className = 'password-strength-text text-success';
    } else if (score >= 4) {
        feedback = 'Fort';
        document.querySelector('.password-strength').className = 'password-strength strength-good';
        document.getElementById('password-strength-text').textContent = feedback;
        document.getElementById('password-strength-text').className = 'password-strength-text text-info';
    } else if (score >= 3) {
        feedback = 'Moyen';
        document.querySelector('.password-strength').className = 'password-strength strength-fair';
        document.getElementById('password-strength-text').textContent = feedback;
        document.getElementById('password-strength-text').className = 'password-strength-text text-warning';
    } else if (score >= 1) {
        feedback = 'Faible';
        document.querySelector('.password-strength').className = 'password-strength strength-weak';
        document.getElementById('password-strength-text').textContent = feedback;
        document.getElementById('password-strength-text').className = 'password-strength-text text-danger';
    } else {
        document.querySelector('.password-strength').className = 'password-strength';
        document.getElementById('password-strength-text').textContent = 'Saisissez un mot de passe';
        document.getElementById('password-strength-text').className = 'password-strength-text text-muted';
    }
    
    // Activation/désactivation du bouton submit
    const submitBtn = document.getElementById('submit-btn');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    if (score === 5 && password === passwordConfirm.value && password.length > 0) {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
    
    return score;
}

function updateCriteriaDisplay(criteria) {
    const checks = ['length', 'uppercase', 'lowercase', 'number', 'symbol'];
    
    checks.forEach(check => {
        const element = document.getElementById(check + '-check');
        const icon = element.querySelector('i');
        
        if (criteria[check]) {
            icon.className = 'fas fa-check text-success me-2';
        } else {
            icon.className = 'fas fa-times text-danger me-2';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submit-btn');
    
    passwordField.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        validatePasswordMatch();
    });
    
    confirmField.addEventListener('input', function() {
        validatePasswordMatch();
    });
    
    function validatePasswordMatch() {
        const password = passwordField.value;
        const confirm = confirmField.value;
        
        if (confirm.length > 0) {
            if (password === confirm) {
                confirmField.classList.remove('is-invalid');
                confirmField.classList.add('is-valid');
            } else {
                confirmField.classList.remove('is-valid');
                confirmField.classList.add('is-invalid');
            }
        } else {
            confirmField.classList.remove('is-valid', 'is-invalid');
        }
        
        // Vérifier si le bouton peut être activé
        const score = checkPasswordStrength(password);
        if (score === 5 && password === confirm && password.length > 0) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }
});
</script>
@endpush
@endsection