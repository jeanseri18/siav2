<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Codescandy" />

    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" />

    <!-- Darkmode JS -->
    <script src="{{ asset('assets/js/vendors/darkMode.js') }}"></script>

    <!-- Libs CSS -->
    <link href="{{ asset('assets/fonts/feather/feather.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/libs/tiny-slider/dist/tiny-slider.css') }}" />
    <title>@yield('title', 'Homepage | Geeks - Bootstrap 5 Template')</title>
</head>
<style>
/* Variables CSS */
:root {
    --primary-color: #033765;
    --primary-hover: #022954;
    --secondary-color: #0A91EA;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --light-gray: #f8f9fa;
    --border-color: #dee2e6;
    --text-muted: #6c757d;
    --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    --border-radius: 12px;
    --transition: all 0.3s ease;
}

/* Layout général */
body {
    background: linear-gradient(135deg, #667eea 0%, #01162FFF 100%);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    min-height: 100vh;
}

.container {
    padding: 20px;
}

/* Carte d'authentification */
.auth-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 2.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: var(--transition);
}

.auth-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Logo */
.logo {
    max-width: 120px;
    height: auto;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

/* Titres */
.auth-title {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
}

.auth-subtitle {
    color: var(--text-muted);
    font-size: 0.95rem;
    margin-bottom: 0;
}

/* Formulaire */
.auth-form {
    margin-top: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

/* Groupes d'input */
.input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 15px;
    color: var(--text-muted);
    z-index: 3;
    font-size: 0.9rem;
}

.form-control {
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 0.95rem;
    transition: var(--transition);
    background-color: #fff;
}

.form-control:focus {
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 0.2rem rgba(10, 145, 234, 0.25);
    transform: translateY(-1px);
}

.form-control.is-invalid {
    border-color: var(--danger-color);
}

/* Toggle mot de passe */
.password-toggle {
    position: absolute;
    right: 15px;
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    z-index: 3;
    padding: 0;
    transition: var(--transition);
}

.password-toggle:hover {
    color: var(--primary-color);
}

/* Select personnalisé */
.select-wrapper {
    position: relative;
}

.select-wrapper select {
    appearance: none;
    background-color: white;
    padding-right: 2.5rem;
}

.select-arrow {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
    font-size: 0.8rem;
}

/* Boutons */
.btn-auth {
    width: 100%;
    padding: 0.875rem 1.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    border-radius: 8px;
    transition: var(--transition);
    text-transform: none;
    letter-spacing: 0.025em;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border: none;
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-hover) 0%, #0779c4 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(3, 55, 101, 0.3);
}

.btn-primary:active {
    transform: translateY(0);
}

/* Cases à cocher */
.form-check {
    margin: 1rem 0;
}

.form-check-input {
    margin-right: 0.5rem;
}

.form-check-label {
    font-size: 0.9rem;
    color: var(--text-muted);
}

/* Messages d'erreur */
.invalid-feedback {
    display: block;
    font-size: 0.85rem;
    color: var(--danger-color);
    margin-top: 0.25rem;
}

.alert {
    border-radius: 8px;
    border: none;
    font-size: 0.9rem;
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: var(--danger-color);
    border-left: 4px solid var(--danger-color);
}

.alert ul {
    padding-left: 1.2rem;
}

/* Pied de page */
.auth-footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.auth-footer p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.text-link {
    color: var(--secondary-color);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.text-link:hover {
    color: var(--primary-color);
    text-decoration: underline;
}

/* Texte d'aide */
.form-text {
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

/* Responsive */
@media (max-width: 768px) {
    .auth-card {
        padding: 1.5rem;
        margin: 1rem;
    }
    
    .auth-title {
        font-size: 1.5rem;
    }
    
    .container {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .row > div {
        padding: 0;
    }
    
    .auth-card {
        border-radius: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Focus visible pour l'accessibilité */
.form-control:focus-visible,
.btn:focus-visible {
    outline: 2px solid var(--secondary-color);
    outline-offset: 2px;
}
</style>

<script>
// Fonction pour basculer la visibilité du mot de passe
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Animation des champs au focus
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    });
});
</script>
<body style="background-color:#033765">
   


    <main>
        @yield('content')
    </main>


    <!-- Scroll top -->
    <div class="btn-scroll-top">
        <svg class="progress-square svg-content" width="100%" height="100%" viewBox="0 0 40 40">
            <path d="M8 1H32C35.866 1 39 4.13401 39 8V32C39 35.866 35.866 39 32 39H8C4.13401 39 1 35.866 1 32V8C1 4.13401 4.13401 1 8 1Z" />
        </svg>
    </div>

    <!-- Scripts -->
    <!-- Libs JS -->
    <script src="{{ asset('assets/libs/%40popperjs/core/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>

    <!-- Theme JS -->
    <script src="{{ asset('assets/js/theme.min.js') }}"></script>
    <script src="{{ asset('assets/libs/tiny-slider/dist/min/tiny-slider.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/tnsSlider.js') }}"></script>
</body>
</html>
