{{-- Page Show - Détail du contrat --}}
@extends('layouts.app')

@section('title', 'Détail du contrat')
@section('page-title', 'Détail du contrat')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('projets.index') }}">Projets</a></li>
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Détail</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid app-fade-in">
    <div class="row">
        <div class="col-md-3">
            <div class="app-card" style="background-color: var(--primary); color: var(--white); height: 200px">
                <div class="app-card-body">
                    <h3 class="app-fw-bold">{{ $contrat->nom_contrat }}</h3>
                    <div class="app-mt-3">
                        <p><i class="fas fa-calendar-alt me-2"></i> Début: {{ $contrat->date_debut }}</p>
                        <p><i class="fas fa-calendar-check me-2"></i> Fin: {{ $contrat->date_fin ?: 'Non défini' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="app-mt-4">
                <div class="app-card" style="background-color: var(--primary); color: var(--white); height: 100px">
                    <div class="app-card-body">
                        <h4 class="app-fw-bold">{{ $contrat->statut }}</h4>
                        <p><i class="fas fa-user me-2"></i> {{ $contrat->client->nom_raison_sociale ?? 'Client' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="row app-mb-4">
                @php
                    $cards = [
                        'Montant du contrat' => 123000,
                        'Coût de revient Prév.' => 123000,
                        'Coût de revient Réel' => 123000,
                        'Écart' => 123000,
                        'DS Prévisionnel' => 123000,
                        'DS Réalisé' => 123000,
                        'FC Réalisé' => 123000,
                        'CA Réalisé' => 123000
                    ];
                @endphp

                @foreach ($cards as $title => $amount)
                    <div class="col-md-3 app-mt-3">
                        <div class="app-card" style="background-color: #5EB3F6; border: none; padding: 10px;">
                            <div class="app-card-body app-p-2">
                                <p class="app-fw-bold app-mb-1" style="color: var(--primary-dark);">{{ $title }}</p>
                                <h3 class="app-fw-bold app-mb-0">{{ number_format($amount, 0, ',', ' ') }} CFA</h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="app-card app-mt-4">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-cogs me-2"></i>Informations du contrat
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('contrats.update', $contrat->id) }}" method="POST" class="app-form">
                        @csrf
                        @method('PUT')

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="ref_contrat" class="app-form-label">
                                        <i class="fas fa-hashtag me-2"></i>Référence du contrat
                                    </label>
                                    <input type="text" class="app-form-control" id="ref_contrat" name="ref_contrat" value="{{ $contrat->ref_contrat }}" required>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="nom_contrat" class="app-form-label">
                                        <i class="fas fa-file-signature me-2"></i>Nom du contrat
                                    </label>
                                    <input type="text" class="app-form-control" id="nom_contrat" name="nom_contrat" value="{{ $contrat->nom_contrat }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_debut" class="app-form-label">
                                        <i class="fas fa-calendar-alt me-2"></i>Date de début
                                    </label>
                                    <input type="date" class="app-form-control" id="date_debut" name="date_debut" value="{{ $contrat->date_debut }}" required>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="date_fin" class="app-form-label">
                                        <i class="fas fa-calendar-check me-2"></i>Date de fin
                                    </label>
                                    <input type="date" class="app-form-control" id="date_fin" name="date_fin" value="{{ $contrat->date_fin }}">
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="type_travaux" class="app-form-label">
                                        <i class="fas fa-hard-hat me-2"></i>Type de travaux
                                    </label>
                                    <input type="text" class="app-form-control" id="type_travaux" name="type_travaux" value="{{ $contrat->type_travaux }}" required>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="taux_garantie" class="app-form-label">
                                        <i class="fas fa-shield-alt me-2"></i>Taux de garantie
                                    </label>
                                    <input type="number" step="0.01" class="app-form-control" id="taux_garantie" name="taux_garantie" value="{{ $contrat->taux_garantie }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="client_id" class="app-form-label">
                                        <i class="fas fa-user me-2"></i>Client
                                    </label>
                                    <select class="app-form-select" id="client_id" name="client_id" required>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" @if($contrat->client_id == $client->id) selected @endif>{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="montant" class="app-form-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>Montant
                                    </label>
                                    <input type="number" step="0.01" class="app-form-control" id="montant" name="montant" value="{{ $contrat->montant }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-group">
                            <label for="statut" class="app-form-label">
                                <i class="fas fa-info-circle me-2"></i>Statut
                            </label>
                            <select class="app-form-select" id="statut" name="statut" required>
                                <option value="en cours" @if($contrat->statut == 'en cours') selected @endif>En cours</option>
                                <option value="terminé" @if($contrat->statut == 'terminé') selected @endif>Terminé</option>
                                <option value="annulé" @if($contrat->statut == 'annulé') selected @endif>Annulé</option>
                            </select>
                        </div>

                        <div class="app-card-footer">
                            <a href="{{ route('contrats.index') }}" class="app-btn app-btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="app-btn app-btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection