<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="@yield('title', 'Tableau de bord') | {{ config('app.name') }}">
    <meta name="author" content="SIA">
    <meta name="description" content="Système de gestion complet pour le suivi des projets, stocks et finances.">
    <meta name="keywords" content="gestion, projets, finance, dashboard, ERP">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') | {{ config('app.name') }}</title>

    <!-- Favicon XBTP -->
    <link rel="icon" type="image/png" href="{{ asset('Logo_XBTP_Png/Icone_Noir.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('Logo_XBTP_Png/Icone_Noir.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('Logo_XBTP_Png/Icone_Noir.png') }}">

    <!-- Global styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
    
    <!-- Toastr Notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" crossorigin="anonymous">
    
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" crossorigin="anonymous">
    
    <!-- Chart.js (JavaScript library only, no CSS needed) -->

    @stack('styles') {{-- Inclure les styles spécifiques à une page --}}
    

<style>

    /* app-styles.css - Styles généraux pour l'application */

:root {
    /* Palette de couleurs principale */
    --primary: #033d71;
    --primary-light: #033d71;
    --primary-dark: #033d71;
    --bs-primary: #033d71;
    --bs-primary-rgb: 3, 61, 113;
    --bs-link-color: #033d71;
    --bs-link-hover-color: #033d71;
    --secondary: #6c757d;
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;
    --light: #f8f9fa;
    --dark: #343a40;
    
    /* Couleurs neutres */
    --white: #ffffff;
    --gray-100: #f8f9fa;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #6c757d;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;

    /* Taille unique des boutons */
    --btn-padding-y: 0.5rem;
    --btn-padding-x: 1rem;
    --btn-font-size: 0.875rem;
    --btn-line-height: 1.5;
    --btn-min-height: 2.375rem;
    --btn-gap: 0.375rem;

    /* Taille unique des champs de formulaire */
    --field-padding-y: 0.5rem;
    --field-padding-x: 0.875rem;
    --field-font-size: 0.875rem;
    --field-line-height: 1.5;
    --field-min-height: 2.375rem;
    --field-border-width: 1px;
    --field-border-color: var(--gray-300);
    --field-border-radius: var(--border-radius-md);
    
    /* Variables d'espacement */
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 3rem;
    
    /* Ombres */
    --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    
    /* Arrondis */
    --border-radius-sm: 0.25rem;
    --border-radius-md: 0.5rem;
    --border-radius-lg: 1rem;
    
    /* Transitions */
    --transition-base: all 0.2s ease-in-out;
    --transition-slow: all 0.3s ease-in-out;
}

/* ===== Structure de base ===== */
body {
    font-family: 'Source Sans 3', 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    color: var(--gray-800);
    background-color: var(--gray-100);
    line-height: 1.6;
    padding-top: 80px; /* Espace pour la navbar fixe */
}

.container {
    padding: var(--spacing-lg);
}

.page-title {
    color: var(--primary);
    font-weight: 700;
    margin-bottom: var(--spacing-lg);
    position: relative;
    padding-bottom: var(--spacing-sm);
}

.page-title:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(to right, var(--primary), var(--primary-light));
    border-radius: var(--border-radius-sm);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: var(--spacing-lg);
    color: var(--gray-800);
}

/* ===== Cartes et Conteneurs ===== */
.app-card {
    background: var(--white);
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    transition: var(--transition-base);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
    /* visible : sinon les tableaux larges (ex. bon de commande) sont coupés par overflow:hidden */
    overflow: visible;
}

.app-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.app-card-header {
    border-bottom: 1px solid var(--gray-200);
    padding-bottom: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.app-card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary);
    margin: 0;
}

.app-card-actions {
    display: flex;
    gap: var(--spacing-sm);
}

.app-card-body {
    padding: var(--spacing-md) 0;
}

.app-card-footer {
    border-top: 1px solid var(--gray-200);
    padding-top: var(--spacing-md);
    margin-top: var(--spacing-lg);
    display: flex;
    justify-content: flex-end;
    gap: var(--spacing-md);
}

/* ===== Tableaux ===== */
.app-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: var(--spacing-lg);
}

.app-table th {
    background-color: var(--primary);
    color: var(--white);
    font-weight: 600;
    text-align: left;
    padding: var(--spacing-md);
    position: relative;
}

.app-table th:first-child {
    border-top-left-radius: var(--border-radius-sm);
}

.app-table th:last-child {
    border-top-right-radius: var(--border-radius-sm);
}

.app-table td {
    padding: var(--spacing-md);
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.app-table tbody tr {
    transition: var(--transition-base);
}

.app-table tbody tr:hover {
    background-color: rgba(3, 55, 101, 0.05);
}

.app-table tbody tr:last-child td:first-child {
    border-bottom-left-radius: var(--border-radius-sm);
}

.app-table tbody tr:last-child td:last-child {
    border-bottom-right-radius: var(--border-radius-sm);
}

/* Pagination */
.app-pagination {
    display: flex;
    justify-content: center;
    margin-top: var(--spacing-lg);
}

.app-pagination .page-item .page-link {
    color: var(--primary);
    border: 1px solid var(--gray-300);
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border-radius: var(--border-radius-sm);
    transition: var(--transition-base);
}

.app-pagination .page-item.active .page-link {
    background-color: var(--primary);
    border-color: var(--primary);
    color: var(--white);
}

.app-pagination .page-item .page-link:hover {
    background-color: var(--gray-200);
}

/* ===== Formulaires ===== */
.app-form {
    margin-bottom: var(--spacing-lg);
}

.app-form-group {
    margin-bottom: var(--spacing-md);
}

.app-form-label,
.form-label {
    display: block;
    margin-bottom: var(--spacing-sm);
    font-weight: 600;
    font-size: var(--field-font-size);
    color: var(--gray-700);
}

.app-form-control,
.app-form-select,
.form-control,
.form-select,
.form-control-sm,
.form-control-lg,
.form-select-sm,
.form-select-lg,
select.form-control,
input.form-control,
textarea.form-control {
    display: block;
    width: 100%;
    min-height: var(--field-min-height);
    height: var(--field-min-height);
    padding: var(--field-padding-y) var(--field-padding-x) !important;
    font-size: var(--field-font-size) !important;
    line-height: var(--field-line-height) !important;
    font-weight: 400;
    color: var(--gray-800);
    background-color: var(--white);
    background-clip: padding-box;
    border: var(--field-border-width) solid var(--field-border-color) !important;
    border-radius: var(--field-border-radius) !important;
    box-sizing: border-box;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

textarea.app-form-control,
textarea.form-control,
textarea.form-control-sm,
textarea.form-control-lg {
    height: auto !important;
    min-height: 6rem;
    resize: vertical;
    line-height: 1.5 !important;
}

input[type="file"].app-form-control,
input[type="file"].form-control {
    height: auto !important;
    min-height: var(--field-min-height);
    padding-top: 0.4rem !important;
    padding-bottom: 0.4rem !important;
}

div.app-form-control {
    display: flex;
    align-items: center;
}

.app-form-control:focus,
.app-form-select:focus,
.form-control:focus,
.form-select:focus,
.form-control-sm:focus,
.form-control-lg:focus {
    border-color: var(--primary) !important;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(3, 61, 113, 0.15);
}

.app-form-control:disabled,
.app-form-select:disabled,
.form-control:disabled,
.form-select:disabled,
.app-form-control[readonly],
.form-control[readonly] {
    background-color: var(--gray-100);
    opacity: 1;
}

.app-form-select,
.form-select,
.form-select-sm,
.form-select-lg,
select.app-form-control,
select.form-control {
    padding-right: 2.25rem !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    appearance: none;
}

.app-form-check {
    display: flex;
    align-items: center;
    margin-bottom: var(--spacing-sm);
}

.app-form-check-input,
.form-check-input {
    width: 1.125rem;
    height: 1.125rem;
    margin-right: var(--spacing-sm);
    border: var(--field-border-width) solid var(--gray-400);
    border-radius: var(--border-radius-sm);
}

.app-form-check-label,
.form-check-label {
    margin-bottom: 0;
    font-size: var(--field-font-size);
}

.app-form-text,
.form-text {
    margin-top: var(--spacing-xs);
    font-size: 0.8125rem;
    color: var(--gray-600);
}

.input-group > .app-form-control,
.input-group > .form-control,
.input-group > .form-select,
.input-group > .app-form-select {
    min-height: var(--field-min-height);
    height: var(--field-min-height);
}

.input-group > .app-btn,
.input-group > .btn {
    min-height: var(--field-min-height);
    height: var(--field-min-height);
}

/* Select2 aligné sur les champs */
.select2-container .select2-selection--single {
    min-height: var(--field-min-height) !important;
    height: var(--field-min-height) !important;
    border: var(--field-border-width) solid var(--field-border-color) !important;
    border-radius: var(--field-border-radius) !important;
    padding: 0 var(--field-padding-x);
    display: flex !important;
    align-items: center;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    font-size: var(--field-font-size) !important;
    line-height: var(--field-line-height) !important;
    padding-left: 0 !important;
    color: var(--gray-800);
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100% !important;
    top: 0 !important;
    right: 0.5rem !important;
}

.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 0.2rem rgba(3, 61, 113, 0.15);
}

.app-form-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -0.5rem;
    margin-left: -0.5rem;
}

.app-form-col {
    flex: 1 0 0%;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: var(--spacing-md);
}

.app-form-col-3 {
    flex: 0 0 25%;
    max-width: 25%;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: var(--spacing-md);
}

.app-form-col-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: var(--spacing-md);
}

.app-form-col-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: var(--spacing-md);
}

.app-form-col-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: var(--spacing-md);
}

.app-form-col-9 {
    flex: 0 0 75%;
    max-width: 75%;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: var(--spacing-md);
}

.app-form-col-12 {
    flex: 0 0 100%;
    max-width: 100%;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
    margin-bottom: var(--spacing-md);
}

/* ===== Groupes d'input ===== */
.input-group {
    position: relative;
    display: flex;
    align-items: stretch;
    width: 100%;
}

.input-group .app-form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .app-btn {
    position: relative;
    z-index: 2;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-left: 0;
}

.input-group .app-btn:hover,
.input-group .app-btn:focus {
    z-index: 3;
}

/* ===== Indicateur de force du mot de passe ===== */
.password-strength {
    margin-top: 0.5rem;
}

.password-strength-bar {
    height: 4px;
    background-color: var(--gray-200);
    border-radius: var(--border-radius-sm);
    overflow: hidden;
    margin-bottom: 0.25rem;
}

.password-strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: var(--border-radius-sm);
}

.password-strength-text {
    font-size: 0.75rem;
    color: var(--gray-600);
    font-weight: 500;
}

/* Classes de force du mot de passe */
.strength-weak .password-strength-fill {
    background-color: var(--danger);
    width: 25%;
}

.strength-fair .password-strength-fill {
    background-color: var(--warning);
    width: 50%;
}

.strength-good .password-strength-fill {
    background-color: var(--info);
    width: 75%;
}

.strength-strong .password-strength-fill {
    background-color: var(--success);
    width: 100%;
}

/* ===== Boutons ===== */
.app-btn,
a.app-btn,
a.app-btn:link,
a.app-btn:visited,
a.app-btn:hover,
a.app-btn:focus,
a.app-btn:active {
    text-decoration: none !important;
}

.app-btn,
.app-btn-sm,
.app-btn-lg,
.btn,
.btn-sm,
.btn-lg {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--btn-gap);
    font-weight: 500;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: var(--btn-padding-y) var(--btn-padding-x) !important;
    font-size: var(--btn-font-size) !important;
    line-height: var(--btn-line-height) !important;
    min-height: var(--btn-min-height);
    height: var(--btn-min-height);
    box-sizing: border-box;
    border-radius: var(--border-radius-md) !important;
    transition: var(--transition-base);
    cursor: pointer;
}

.app-btn:focus, .app-btn:hover,
.btn:focus, .btn:hover {
    text-decoration: none !important;
    box-shadow: 0 0 0 0.2rem rgba(3, 55, 101, 0.25);
    transform: translateY(-2px);
}

.app-btn-primary {
    color: var(--white);
    background-color: var(--primary);
    border-color: var(--primary);
}

.app-btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
}

.app-btn-secondary {
    color: var(--white);
    background-color: var(--secondary);
    border-color: var(--secondary);
}

.app-btn-success {
    color: var(--white);
    background-color: var(--success);
    border-color: var(--success);
}

.app-btn-danger {
    color: var(--white);
    background-color: var(--danger);
    border-color: var(--danger);
}

.app-btn-warning {
    color: var(--dark);
    background-color: var(--warning);
    border-color: var(--warning);
}

.app-btn-info {
    color: var(--white);
    background-color: var(--info);
    border-color: var(--info);
}

.app-btn-light {
    color: var(--gray-800);
    background-color: var(--light);
    border-color: var(--light);
}

.app-btn-dark {
    color: var(--white);
    background-color: var(--dark);
    border-color: var(--dark);
}

.app-btn-outline-primary {
    color: var(--primary);
    background-color: transparent;
    border-color: var(--primary);
}

.app-btn-outline-primary:hover {
    color: var(--white);
    background-color: var(--primary);
}

.app-btn-outline-secondary {
    color: var(--secondary);
    background-color: transparent;
    border-color: var(--secondary);
}

.app-btn-outline-secondary:hover {
    color: var(--white);
    background-color: var(--secondary);
}

.app-btn-link {
    font-weight: 400;
    color: var(--primary);
    text-decoration: none;
    background-color: transparent;
    border: none;
    padding: 0 !important;
    min-height: auto;
    height: auto;
}

.app-btn-link:hover {
    color: var(--primary-dark);
    text-decoration: underline;
    box-shadow: none;
    transform: none;
}

.app-btn-block {
    display: flex;
    width: 100%;
}

.app-btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--btn-gap);
}

.app-btn-icon i,
.app-btn-icon svg,
.app-btn i,
.app-btn svg,
.btn i,
.btn svg {
    font-size: 1em;
    line-height: 1;
}

/* ===== Alertes ===== */
.app-alert {
    position: relative;
    padding: 1rem 1.5rem;
    margin-bottom: var(--spacing-lg);
    border: 1px solid transparent;
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
}

.app-alert-icon {
    margin-right: var(--spacing-md);
    font-size: 1.5rem;
}

.app-alert-content {
    flex: 1;
}

.app-alert-heading {
    margin-top: 0;
    margin-bottom: var(--spacing-xs);
    font-weight: 600;
}

.app-alert-text {
    margin-bottom: 0;
}

.app-alert-primary {
    color: var(--primary);
    background-color: rgba(3, 55, 101, 0.1);
    border-left: 4px solid var(--primary);
}

.app-alert-secondary {
    color: var(--secondary);
    background-color: rgba(108, 117, 125, 0.1);
    border-left: 4px solid var(--secondary);
}

.app-alert-success {
    color: var(--success);
    background-color: rgba(40, 167, 69, 0.1);
    border-left: 4px solid var(--success);
}

.app-alert-danger {
    color: var(--danger);
    background-color: rgba(220, 53, 69, 0.1);
    border-left: 4px solid var(--danger);
}

.app-alert-warning {
    color: var(--warning);
    background-color: rgba(255, 193, 7, 0.1);
    border-left: 4px solid var(--warning);
}

.app-alert-info {
    color: var(--info);
    background-color: rgba(23, 162, 184, 0.1);
    border-left: 4px solid var(--info);
}

.app-alert-close {
    position: absolute;
    top: 0;
    right: 0;
    padding: 1rem;
    color: inherit;
    background-color: transparent;
    border: 0;
    cursor: pointer;
}

/* ===== Badges ===== */
.app-badge {
    display: inline-block;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: var(--border-radius-sm);
    color: var(--white);
}

.app-badge-primary {
    background-color: var(--primary);
}

.app-badge-secondary {
    background-color: var(--secondary);
}

.app-badge-success {
    background-color: var(--success);
}

.app-badge-danger {
    background-color: var(--danger);
}

.app-badge-warning {
    background-color: var(--warning);
    color: var(--dark);
}

.app-badge-info {
    background-color: var(--info);
}

.app-badge-light {
    background-color: var(--light);
    color: var(--dark);
}

.app-badge-dark {
    background-color: var(--dark);
}

.app-badge-pill {
    border-radius: 50rem;
}

/* ===== États interactifs ===== */
.app-hover-scale {
    transition: var(--transition-base);
}

.app-hover-scale:hover {
    transform: scale(1.05);
}

.app-hover-translate {
    transition: var(--transition-base);
}

.app-hover-translate:hover {
    transform: translateY(-5px);
}

.app-hover-shadow {
    transition: var(--transition-base);
}

.app-hover-shadow:hover {
    box-shadow: var(--shadow-md);
}

/* ===== Utilitaires ===== */
.app-d-flex {
    display: flex !important;
}

.app-align-items-center {
    align-items: center !important;
}

.app-justify-content-between {
    justify-content: space-between !important;
}

.app-justify-content-center {
    justify-content: center !important;
}

.app-justify-content-end {
    justify-content: flex-end !important;
}

.app-flex-column {
    flex-direction: column !important;
}

.app-d-grid {
    display: grid !important;
}

.app-d-grid > form {
    display: grid;
}

.app-gap-1 {
    gap: var(--spacing-xs) !important;
}

.app-gap-2 {
    gap: var(--spacing-sm) !important;
}

.app-gap-3 {
    gap: var(--spacing-md) !important;
}

.app-gap-4 {
    gap: var(--spacing-lg) !important;
}

.app-text-center {
    text-align: center !important;
}

.app-text-end {
    text-align: right !important;
}

.app-fw-bold {
    font-weight: 700 !important;
}

.app-fw-medium {
    font-weight: 500 !important;
}

.app-fw-normal {
    font-weight: 400 !important;
}

.app-w-100 {
    width: 100% !important;
}

.app-h-100 {
    height: 100% !important;
}

.app-m-0 {
    margin: 0 !important;
}

.app-mt-0 {
    margin-top: 0 !important;
}

.app-mb-0 {
    margin-bottom: 0 !important;
}

.app-mt-1 {
    margin-top: var(--spacing-xs) !important;
}

.app-mb-1 {
    margin-bottom: var(--spacing-xs) !important;
}

.app-mt-2 {
    margin-top: var(--spacing-sm) !important;
}

.app-mb-2 {
    margin-bottom: var(--spacing-sm) !important;
}

.app-mt-3 {
    margin-top: var(--spacing-md) !important;
}

.app-mb-3 {
    margin-bottom: var(--spacing-md) !important;
}

.app-mt-4 {
    margin-top: var(--spacing-lg) !important;
}

.app-mb-4 {
    margin-bottom: var(--spacing-lg) !important;
}

.app-mt-5 {
    margin-top: var(--spacing-xl) !important;
}

.app-mb-5 {
    margin-bottom: var(--spacing-xl) !important;
}

/* Défilement horizontal des tableaux sur tous les écrans (classe aussi redondante dans la media query mobile) */
.app-table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    max-width: 100%;
}

.app-p-0 {
    padding: 0 !important;
}

.app-p-1 {
    padding: var(--spacing-xs) !important;
}

.app-p-2 {
    padding: var(--spacing-sm) !important;
}

.app-p-3 {
    padding: var(--spacing-md) !important;
}

.app-p-4 {
    padding: var(--spacing-lg) !important;
}

.app-p-5 {
    padding: var(--spacing-xl) !important;
}

/* ===== Médias responsifs ===== */
@media (max-width: 768px) {
    .container {
        padding: var(--spacing-md);
    }
    
    .app-form-row {
        flex-direction: column;
    }
    
    .app-form-col-3,
    .app-form-col-4,
    .app-form-col-6,
    .app-form-col-8,
    .app-form-col-9,
    .app-form-col-12 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .app-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }
    
    .app-card-actions {
        width: 100%;
        justify-content: space-between;
    }
}

/* ===== DataTables personnalisé ===== */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    margin-bottom: var(--spacing-md);
    color: var(--gray-700);
}

.dataTables_wrapper .dataTables_length select {
    min-height: var(--field-min-height);
    height: var(--field-min-height);
    padding: var(--field-padding-y) 2rem var(--field-padding-y) var(--field-padding-x);
    font-size: var(--field-font-size);
    border: var(--field-border-width) solid var(--field-border-color);
    border-radius: var(--field-border-radius);
    color: var(--gray-700);
    background-color: var(--white);
    box-sizing: border-box;
}

.dataTables_wrapper .dataTables_filter input {
    min-height: var(--field-min-height);
    height: var(--field-min-height);
    padding: var(--field-padding-y) var(--field-padding-x);
    font-size: var(--field-font-size);
    border: var(--field-border-width) solid var(--field-border-color);
    border-radius: var(--field-border-radius);
    color: var(--gray-700);
    background-color: var(--white);
    margin-left: 0.5rem;
    box-sizing: border-box;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    margin: 0 0.25rem;
    padding: 0.375rem 0.75rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius-sm);
    background-color: var(--white);
    color: var(--gray-700) !important;
    transition: var(--transition-base);
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: var(--white) !important;
}

.dataTables_wrapper .dt-buttons {
    margin-bottom: var(--spacing-md);
}

.dataTables_wrapper .dt-buttons .dt-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary);
    color: var(--white);
    border: none;
    border-radius: var(--border-radius-md);
    padding: var(--btn-padding-y) var(--btn-padding-x);
    font-size: var(--btn-font-size);
    line-height: var(--btn-line-height);
    min-height: var(--btn-min-height);
    height: var(--btn-min-height);
    margin-right: 0.25rem;
    transition: var(--transition-base);
    box-sizing: border-box;
}

.dataTables_wrapper .dt-buttons .dt-button:hover {
    background-color: var(--primary-dark);
}

/* ===== Modals ===== */
.app-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.app-modal {
    background-color: var(--white);
    border-radius: var(--border-radius-md);
    box-shadow: var(--shadow-lg);
    max-width: 600px;
    width: 100%;
    margin: 1.75rem;
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.app-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.app-modal-title {
    font-weight: 600;
    font-size: 1.25rem;
    margin: 0;
    color: var(--primary);
}

.app-modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    color: var(--gray-700);
    cursor: pointer;
}

.app-modal-body {
    padding: 1.5rem;
}

.app-modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--gray-200);
    gap: var(--spacing-sm);
}

/* ===== Animations ===== */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.app-fade-in {
    animation: fadeIn 0.3s ease-out;
}

.app-slide-in-up {
    animation: slideInUp 0.3s ease-out;
}

.app-pulse {
    animation: pulse 2s infinite;
}

/* ===== Thème sombre (optionnel) ===== */
.dark-mode {
    --white: #1a1a1a;
    --gray-100: #2d2d2d;
    --gray-200: #444444;
    --gray-300: #555555;
    --gray-400: #666666;
    --gray-500: #777777;
    --gray-600: #999999;
    --gray-700: #aaaaaa;
    --gray-800: #dddddd;
    --gray-900: #f0f0f0;
    --primary: #033d71;
    --primary-light: #033d71;
    --light: #2d2d2d;
    --dark: #f0f0f0;
    
    color-scheme: dark;
    background-color: var(--gray-100);
    color: var(--gray-800);
}

.dark-mode .app-card,
.dark-mode .app-form-control,
.dark-mode .app-form-select {
    background-color: var(--white);
    border-color: var(--gray-200);
}

.dark-mode .app-table td {
    border-bottom-color: var(--gray-200);
}

.dark-mode .app-btn-light {
    background-color: var(--gray-200);
    border-color: var(--gray-200);
    color: var(--gray-800);
}

.dark-mode .app-table tbody tr:hover {
    background-color: rgba(26, 118, 204, 0.1);
}

/* ===== NAVBAR STYLES ===== */
.app-navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: #033d71;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1030;
    padding: 0;
    transition: var(--transition-base);
}

.navbar-brand {
    display: flex;
    align-items: center;
    color: white !important;
    font-weight: 700;
    font-size: 1.5rem;
    text-decoration: none;
    padding: 1rem 1.5rem;
    transition: var(--transition-base);
}

.navbar-brand:hover {
    color: var(--warning) !important;
    transform: scale(1.05);
}

.navbar-nav {
    display: flex;
    flex-direction: row;
    align-items: center;
    margin: 0;
    padding: 0;
}

.navbar-nav .nav-item {
    position: relative;
}

.navbar-nav .nav-link {
    color: white !important;
    font-weight: 500;
    padding: 0.6rem 1rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition-base);
    border-radius: 0.5rem;
    margin: 0.45rem 0.2rem;
    white-space: nowrap;
}

.navbar-nav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--warning) !important;
    transform: translateY(-2px);
}

.navbar-nav .nav-link.active {
    background-color: var(--warning);
    color: #033d71 !important;
    font-weight: 600;
    border-radius: 0.5rem;
}

.navbar-nav .nav-link i {
    font-size: 1.1rem;
    transition: var(--transition-base);
}

.navbar-nav .nav-link:hover i {
    transform: scale(1.2);
}

.navbar-toggler {
    border: none;
    padding: 0.5rem;
    color: white;
    background: transparent;
    font-size: 1.5rem;
}

.navbar-toggler:focus {
    box-shadow: none;
}

.navbar-collapse {
    flex-grow: 1;
    justify-content: space-between;
}

.user-profile-navbar {
    display: flex;
    align-items: center;
    color: white;
    padding: 0.5rem 1rem;
    gap: 0.75rem;
}

.avatar-initials {
    background-color: #fff;
    color: #033d71;
    font-weight: bold;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: var(--transition-base);
}

.avatar-initials:hover {
    transform: scale(1.1);
}

.user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.user-info .name {
    margin: 0;
    font-weight: 600;
    font-size: 0.9rem;
}

.user-info .role {
    margin: 0;
    font-size: 0.75rem;
    opacity: 0.8;
}

.notification-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background-color: var(--danger);
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: pulse 2s infinite;
}

/* Menu mobile */
@media (max-width: 991.98px) {
    .navbar-nav {
        flex-direction: column;
        width: 100%;
        background-color: #033d71;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 1rem;
        padding-top: 1rem;
    }
    
    .navbar-nav .nav-link {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .user-profile-navbar {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 1rem;
        padding-top: 1rem;
        justify-content: center;
    }
    
    body {
        padding-top: 60px; /* Moins d'espace pour mobile */
    }
}

/* Dropdown pour les sous-menus si nécessaire */
.nav-item.dropdown .dropdown-menu {
    background-color: #033d71;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.nav-item.dropdown .dropdown-item {
    color: white;
    padding: 0.75rem 1.5rem;
    transition: var(--transition-base);
}

.nav-item.dropdown .dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: var(--warning);
}

/* Responsive adjustments */
@media (min-width: 992px) {
    .navbar-nav .nav-link {
        padding: 1rem 1rem;
    }
}

@media (max-width: 1200px) {
    .navbar-nav .nav-link {
        padding: 1rem 0.75rem;
        font-size: 0.9rem;
    }
}
</style>

    <style>
    :root {
        --primary-color: #033d71;
        --secondary-color: #033d71;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
        --light-bg: #f8f9fa;
        --white: #ffffff;
        --text-dark: #2d3436;
        --text-muted: #636e72;
        --border-radius: 0.5rem;
    }
    
    .page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease-out;
    }
    
    .loader-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid var(--light-bg);
        border-top: 5px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .app-content {
        padding: 1.5rem;
        background-color: var(--light-bg);
    }
    
    .card {
        border-radius: var(--border-radius);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }
    
    .btn {
        border-radius: var(--border-radius);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    /* Ajout d'animations */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Indicateurs de chargement pour les cartes et graphiques */
    .loading {
        position: relative;
        pointer-events: none;
        opacity: 0.7;
    }
    
    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 30px;
        height: 30px;
        margin: -15px 0 0 -15px;
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-top-color: var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 10;
    }
    
    /* Thème clair/sombre */
    .theme-switch {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 999;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        border: none;
    }
    
    .theme-switch:hover {
        transform: scale(1.1);
    }
    
    /* Mode maintenance */
    .maintenance-badge {
        position: fixed;
        top: 80px; /* Ajusté pour la navbar */
        right: 10px;
        background-color: var(--warning-color);
        color: var(--text-dark);
        padding: 0.25rem 0.5rem;
        border-radius: var(--border-radius);
        font-size: 0.8rem;
        z-index: 999;
    }



    /* Styles pour le menu déroulant du profil utilisateur */
.user-profile-navbar.dropdown {
    position: relative;
}

.user-profile-navbar .dropdown-menu {
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-md);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    padding: 0.5rem 0;
    margin-top: 0.5rem;
    min-width: 12rem;
}

.user-profile-navbar .dropdown-menu.show {
    animation: fadeIn 0.3s ease-out;
}

.user-profile-navbar .dropdown-item {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    color: var(--gray-800);
    transition: var(--transition-base);
}

.user-profile-navbar .dropdown-item:hover {
    background-color: rgba(3, 55, 101, 0.05);
    color: var(--primary);
}

.user-profile-navbar .dropdown-item i {
    width: 1.25rem;
    text-align: center;
    color: var(--primary);
}

.user-profile-navbar .dropdown-divider {
    margin: 0.5rem 0;
    border-top: 1px solid var(--gray-200);
}

/* Style pour le mode sombre */
.dark-mode .user-profile-navbar .dropdown-menu {
    background-color: var(--white);
    border-color: var(--gray-200);
}

.dark-mode .user-profile-navbar .dropdown-item {
    color: var(--gray-800);
}

.dark-mode .user-profile-navbar .dropdown-item:hover {
    background-color: rgba(26, 118, 204, 0.1);
}

.dark-mode .user-profile-navbar .dropdown-divider {
    border-top-color: var(--gray-200);
}

/* Style pour le logo dans la navbar */
.navbar-logo {
    height: 40px;
    width: auto;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

/* ===== Cards dashboard sublayouts : blanc / bleu + hover inversé ===== */
.dashboard-card,
a.dashboard-card,
button.dashboard-card {
    background: #ffffff !important;
    background-image: none !important;
    color: #033d71 !important;
    border: none !important;
    border-radius: 1.5rem !important;
    box-shadow: 0 0.5rem 1.25rem rgba(3, 61, 113, 0.1) !important;
    text-decoration: none !important;
    text-align: center !important;
    transition: background-color 0.25s ease, color 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease !important;
}

.dashboard-card::before,
.dashboard-card::after,
a.dashboard-card::before,
a.dashboard-card::after,
button.dashboard-card::before,
button.dashboard-card::after {
    display: none !important;
    content: none !important;
    background: none !important;
}

.dashboard-card .card-icon,
.dashboard-card .card-icon i,
.dashboard-card.primary .card-icon,
.dashboard-card.primary .card-icon i,
.dashboard-card.success .card-icon,
.dashboard-card.success .card-icon i,
.dashboard-card.info .card-icon,
.dashboard-card.info .card-icon i,
.dashboard-card.warning .card-icon,
.dashboard-card.warning .card-icon i,
.dashboard-card.secondary .card-icon,
.dashboard-card.secondary .card-icon i,
.dashboard-card.contracts .card-icon,
.dashboard-card.contracts .card-icon i,
.dashboard-card.import .card-icon,
.dashboard-card.import .card-icon i,
.dashboard-card.config .card-icon,
.dashboard-card.config .card-icon i {
    color: #033d71 !important;
    -webkit-text-fill-color: #033d71 !important;
    background: none !important;
}

.dashboard-card .card-content,
.dashboard-card .card-content h3,
.dashboard-card .card-content p,
.dashboard-card .card-action,
.dashboard-card .card-badge,
.dashboard-card .card-stats,
.dashboard-card .stat-number,
.dashboard-card .stat-label,
.dashboard-card .feature-item,
.dashboard-card .feature-item i {
    color: #033d71 !important;
    -webkit-text-fill-color: #033d71 !important;
}

.dashboard-card .card-content h3 {
    font-weight: 700 !important;
}

.dashboard-card .card-content p {
    opacity: 0.85;
}

.dashboard-card:hover,
a.dashboard-card:hover,
button.dashboard-card:hover,
.dashboard-card:focus,
a.dashboard-card:focus,
button.dashboard-card:focus {
    background: #033d71 !important;
    background-image: none !important;
    color: #ffffff !important;
    transform: translateY(-4px);
    box-shadow: 0 1rem 2rem rgba(3, 61, 113, 0.28) !important;
    text-decoration: none !important;
}

.dashboard-card:hover .card-icon,
.dashboard-card:hover .card-icon i,
.dashboard-card:hover .card-content,
.dashboard-card:hover .card-content h3,
.dashboard-card:hover .card-content p,
.dashboard-card:hover .card-action,
.dashboard-card:hover .card-action i,
.dashboard-card:hover .card-badge,
.dashboard-card:hover .card-stats,
.dashboard-card:hover .stat-number,
.dashboard-card:hover .stat-label,
.dashboard-card:hover .feature-item,
.dashboard-card:hover .feature-item i,
.dashboard-card:hover *,
a.dashboard-card:hover *,
button.dashboard-card:hover * {
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
}

.dashboard-card:hover .card-content p {
    opacity: 0.95;
}
    </style>
</head>

<body class="bg-body-tertiary">
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="loader-spinner"></div>
    </div>
    
    <!-- Maintenance Mode Badge (à afficher uniquement en environnement de développement) -->
    @if(config('app.env') !== 'production')
    <div class="maintenance-badge">
        Mode développement
    </div>
    @endif

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg app-navbar">
        <div class="container-fluid">
            <!-- Brand/Logo -->
            <a class="navbar-brand" href="{{ route('menu') }}" title="Menu principal">
                <img src="{{ asset('Logo_XBTP_Png/Logo_Blanc.png') }}" alt="XBTP" class="navbar-logo">
            </a>

            <!-- Mobile toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Navbar content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main navigation -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard', 'statistiques.*') ? 'active' : '' }}" href="{{ route('statistiques.index') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sublayouts_bu', 'bu.*') ? 'active' : '' }}" href="{{ route('sublayouts_bu') }}">
                            <i class="fas fa-building"></i>
                            <span>Business Units</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sublayouts_projet', 'sublayouts_projetdetail', 'sublayouts_contrat', 'projets.*', 'contrats.*') ? 'active' : '' }}" href="{{ route('sublayouts_projet') }}">
                            <i class="fas fa-project-diagram"></i>
                            <span>Projets</span>
                            @if(isset($projetsEnAttente) && $projetsEnAttente > 0)
                            <span class="notification-badge">{{ $projetsEnAttente }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sublayouts_article', 'articles.*', 'demande-approvisionnements.*', 'demande-achats.*', 'demande-cotations.*', 'bon-commandes.*', 'receptions.*', 'fournisseurs.*') ? 'active' : '' }}" href="{{ route('sublayouts_article') }}">
                            <i class="fas fa-boxes"></i>
                            <span>Stock</span>
                            @if(isset($articlesAlerte) && $articlesAlerte > 0)
                            <span class="notification-badge">{{ $articlesAlerte }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sublayouts_tresorerie', 'sublayouts_caisse', 'sublayouts_banque', 'banque.*', 'banques.*', 'caisse.*', 'bu-budget.*') ? 'active' : '' }}" href="{{ route('sublayouts_tresorerie') }}">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Comptabilité</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sublayouts_vente', 'ventes.*', 'clients.*') ? 'active' : '' }}" href="{{ route('sublayouts_vente') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Vente</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sublayouts_until', 'until', 'config-global.*') ? 'active' : '' }}" href="{{ route('sublayouts_until') }}">
                            <i class="fas fa-tools"></i>
                            <span>Utilitaire</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sublayouts_user', 'users.*') ? 'active' : '' }}" href="{{ route('sublayouts_user') }}">
                            <i class="fas fa-users-cog"></i>
                            <span>Accès</span>
                        </a>
                    </li>
                </ul>

                <!-- User profile section -->
              <div class="user-profile-navbar dropdown">
    @php
        $name = auth()->user()->name ?? 'Utilisateur';
        $initials = collect(explode(' ', $name))->map(fn($word) => strtoupper(mb_substr($word, 0, 1)))->join('');
    @endphp

    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="avatar-initials">
            {{ $initials }}
        </div>

        <div class="user-info">
            <p class="name text-white">{{ $name }}</p>
            <p class="role text-white">{{ auth()->user()->role ?? 'Rôle non défini' }}</p>
        </div>
    </a>
    
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i> Mon profil</a></li>
        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-edit me-2"></i> Modifier profil</a></li>
        <li><a class="dropdown-item" href="{{ route('profile.edit-password') }}"><i class="fas fa-key me-2"></i> Changer mot de passe</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a></li>
    </ul>
</div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="app-main">
        <!-- <div class="app-content-header container">
            <div class="container-fluid"> 
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">@yield('page-title', 'Dashboard')</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                            @yield('breadcrumb')
                            <li class="breadcrumb-item active" aria-current="page">
                                @yield('page-title', 'Dashboard')
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
         -->
        <div class="app-content">
            <div class="container-fluid fade-in">
                <!-- Affichage des messages flash -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                <!-- Contenu principal -->
                @yield('content')
            </div>
        </div>
    </main>
    
    <!-- Switch thème clair/sombre -->
    <button class="theme-switch" id="themeSwitch">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Global scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('dist/js/adminlte.js') }}"></script>
    
    <!-- Toastr Notifications -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" crossorigin="anonymous"></script>
    
    <!-- Sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js" crossorigin="anonymous"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" crossorigin="anonymous"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    // Configuration Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    };
    
    // Afficher/cacher le loader de page
    $(window).on('load', function() {
        $('.page-loader').fadeOut(500, function() {
            $(this).remove();
        });
    });

    // Confirmation avant suppression
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Cette action est irréversible!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
    
    // Gestion du thème clair/sombre
    const themeSwitch = document.getElementById('themeSwitch');
    const icon = themeSwitch.querySelector('i');
    
    // Vérifier si un thème est déjà stocké
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Appliquer le thème actuel
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
    }
    
    // Changer de thème au clic
    themeSwitch.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        } else {
            localStorage.setItem('theme', 'light');
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        }
    });
    
    // Animation des cartes au défilement
    const animateOnScroll = () => {
        const cards = document.querySelectorAll('.card');
        
        cards.forEach(card => {
            const cardTop = card.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (cardTop < windowHeight - 50) {
                card.classList.add('fade-in');
            }
        });
    };
    
    window.addEventListener('scroll', animateOnScroll);
    window.addEventListener('load', animateOnScroll);
    
    // Fermeture automatique du menu mobile après clic
    $('.navbar-nav .nav-link').on('click', function() {
        if (window.innerWidth < 992) {
            $('.navbar-collapse').collapse('hide');
        }
    });
    
    // Vérification des mises à jour en temps réel (toutes les 5 minutes)
    const checkForUpdates = () => {
        $.ajax({
            url: '/check-updates',
            method: 'GET',
            success: function(response) {
                if (response.hasUpdates) {
                    toastr.info('Des mises à jour sont disponibles. <a href="/refresh-data">Actualiser</a>', 'Information');
                }
            }
        });
    };
    
    // Vérifier les mises à jour toutes les 5 minutes
    setInterval(checkForUpdates, 300000);




    // Ajouter ceci à la section scripts
$(document).ready(function() {
    // Activation des dropdowns de Bootstrap
    var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });
});
    </script>

    @if(session('success'))
        <script>toastr.success("{{ session('success') }}");</script>
    @endif

    @if(session('error'))
        <script>toastr.error("{{ session('error') }}");</script>
    @endif

    @if(session('warning'))
        <script>toastr.warning("{{ session('warning') }}");</script>
    @endif

    @if(session('info'))
        <script>toastr.info("{{ session('info') }}");</script>
    @endif

    @stack('scripts') {{-- Inclure les scripts spécifiques à une page --}}

</body>

</html>
