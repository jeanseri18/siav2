@extends('layouts.app')

@section('title', 'Modifier une Facture')
@section('page-title', 'Modifier une Facture')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('factures.index') }}">Factures</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-file-invoice-dollar me-2"></i>Modifier la Facture: {{ $facture->num }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <!-- Affichage des erreurs de validation -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation :</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <form action="{{ route('factures.update', $facture->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="num" class="app-form-label">
                                <i class="fas fa-hashtag me-2"></i>Numéro de Facture
                            </label>
                            <input type="text" name="num" id="num" class="app-form-control" value="{{ $facture->num }}" required>
                            <div class="app-form-text">Identifiant unique de la facture</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_prestation" class="app-form-label">
                                <i class="fas fa-toolbox me-2"></i>Prestation (optionnel)
                            </label>
                            <select name="id_prestation" id="id_prestation" class="app-form-select">
                                <option value="">Sélectionner</option>
                                @foreach($prestations as $prestation)
                                    <option value="{{ $prestation->id }}" {{ $facture->id_prestation == $prestation->id ? 'selected' : '' }}>
                                        {{ $prestation->artisan->nom }} - {{ $prestation->montant }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Prestation associée à cette facture</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_contrat" class="app-form-label">
                                <i class="fas fa-file-signature me-2"></i>Contrat (optionnel)
                            </label>
                            <select name="id_contrat" id="id_contrat" class="app-form-select">
                                <option value="">Sélectionner</option>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}" {{ $facture->id_contrat == $contrat->id ? 'selected' : '' }}>
                                        {{ $contrat->nom_contrat }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Contrat associé à cette facture</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="id_artisan" class="app-form-label">
                                <i class="fas fa-hard-hat me-2"></i>Artisan
                            </label>
                            <select name="id_artisan" id="id_artisan" class="app-form-select" required>
                                @foreach($artisans as $artisan)
                                    <option value="{{ $artisan->id }}" {{ $facture->id_artisan == $artisan->id ? 'selected' : '' }}>
                                        {{ $artisan->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Artisan concerné par cette facture</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="montant" class="app-form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>Montant
                            </label>
                            <div class="input-group">
                                <input type="number" name="montant" id="montant" class="app-form-control" value="{{ $facture->montant }}" required>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="app-form-text">Montant total de la facture</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="decompte" class="app-form-label">
                                <i class="fas fa-calculator me-2"></i>Décompte (optionnel)
                            </label>
                            <div class="input-group">
                                <input type="number" name="decompte" id="decompte" class="app-form-control" value="{{ $facture->decompte }}">
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="app-form-text">Montant déjà payé sur cette facture</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="retenue" class="app-form-label">
                                <i class="fas fa-percentage me-2"></i>Retenue (optionnel)
                            </label>
                            <div class="input-group">
                                <input type="number" name="retenue" id="retenue" class="app-form-control" value="{{ $facture->retenue }}">
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <div class="app-form-text">Montant retenu pour garantie</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="date_emission" class="app-form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Date d'émission
                            </label>
                            <input type="date" name="date_emission" id="date_emission" class="app-form-control" value="{{ $facture->date_emission }}" required>
                            <div class="app-form-text">Date à laquelle la facture a été émise</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="statut" class="app-form-label">
                                <i class="fas fa-flag me-2"></i>Statut
                            </label>
                            <select name="statut" id="statut" class="app-form-select" required>
                                <option value="En attente" {{ $facture->statut == 'En attente' ? 'selected' : '' }}>En attente</option>
                                <option value="Payée" {{ $facture->statut == 'Payée' ? 'selected' : '' }}>Payée</option>
                                <option value="Annulée" {{ $facture->statut == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                                <option value="Partiellement payée" {{ $facture->statut == 'Partiellement payée' ? 'selected' : '' }}>Partiellement payée</option>
                            </select>
                            <div class="app-form-text">État actuel de la facture</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('factures.index') }}" class="app-btn app-btn-secondary">
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