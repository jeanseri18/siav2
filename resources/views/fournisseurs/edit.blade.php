@extends('layouts.app')

@section('title', 'Modifier un Client')
@section('page-title', 'Modifier un Client')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('fournisseurs.index') }}">Fournisseurs</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-edit me-2"></i>Modifier le Client: {{ $client->nom_raison_sociale }}
            </h2>
        </div>
        
        <div class="app-card-body">
            <form action="{{ route('fournisseurs.update', $fournisseur->id) }}" method="POST" class="app-form">
                @csrf
                @method('PUT')
                
                <div class="app-form-group">
                    <label for="categorie" class="app-form-label">
                        <i class="fas fa-tag me-2"></i>Catégorie
                    </label>
                    <select name="categorie" id="categorie" class="app-form-select" required>
                        <option value="Particulier" {{ $client->categorie == 'Particulier' ? 'selected' : '' }}>Particulier</option>
                        <option value="Entreprise" {{ $client->categorie == 'Entreprise' ? 'selected' : '' }}>Entreprise</option>
                    </select>
                    <div class="app-form-text">Type du client</div>
                </div>

                <div id="particulier_fields" class="app-form-row" @if($client->categorie == 'Particulier') style="display:flex;" @else style="display:none;" @endif>
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-user me-2"></i>Nom
                            </label>
                            <input type="text" name="nom_raison_sociale" id="nom" class="app-form-control" value="{{ old('nom_raison_sociale', $client->nom_raison_sociale) }}" required>
                            <div class="app-form-text">Nom du client</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="prenoms" class="app-form-label">
                                <i class="fas fa-user-tag me-2"></i>Prénoms
                            </label>
                            <input type="text" name="prenoms" id="prenoms" class="app-form-control" value="{{ old('prenoms', $client->prenoms) }}" required>
                            <div class="app-form-text">Prénoms du client</div>
                        </div>
                    </div>
                </div>

                <div id="entreprise_fields" class="app-form-row" @if($client->categorie == 'Entreprise') style="display:flex;" @else style="display:none;" @endif>
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="raison_sociale" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Raison Sociale
                            </label>
                            <input type="text" name="raison_sociale" id="raison_sociale" class="app-form-control" value="{{ old('raison_sociale', $client->raison_sociale) }}">
                            <div class="app-form-text">Nom de l'entreprise</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="n_rccm" class="app-form-label">
                                <i class="fas fa-file-alt me-2"></i>N° RCCM
                            </label>
                            <input type="text" name="n_rccm" id="n_rccm" class="app-form-control" value="{{ old('n_rccm', $client->n_rccm) }}">
                            <div class="app-form-text">Numéro du registre de commerce</div>
                        </div>
                    </div>
                </div>
                
                <div id="entreprise_fields_suite" class="app-form-row" @if($client->categorie == 'Entreprise') style="display:flex;" @else style="display:none;" @endif>
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="n_cc" class="app-form-label">
                                <i class="fas fa-id-card me-2"></i>N° CC
                            </label>
                            <input type="text" name="n_cc" id="n_cc" class="app-form-control" value="{{ old('n_cc', $client->n_cc) }}">
                            <div class="app-form-text">Numéro de compte contribuable</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="secteur_activite" class="app-form-label">
                                <i class="fas fa-industry me-2"></i>Secteur d'Activité
                            </label>
                            <select name="secteur_activite" id="secteur_activite" class="app-form-select">
                                @foreach ($secteurs as $secteur)
                                    <option value="{{ $secteur->nom }}" {{ $client->secteur_activite == $secteur->nom ? 'selected' : '' }}>{{ $secteur->nom }}</option>
                                @endforeach
                            </select>
                            <div class="app-form-text">Domaine d'activité de l'entreprise</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="delai_paiement" class="app-form-label">
                                <i class="fas fa-calendar-day me-2"></i>Délai de paiement
                            </label>
                            <select name="delai_paiement" id="delai_paiement" class="app-form-select" required>
                                <option value="30" {{ $client->delai_paiement == 30 ? 'selected' : '' }}>30 jours</option>
                                <option value="60" {{ $client->delai_paiement == 60 ? 'selected' : '' }}>60 jours</option>
                                <option value="90" {{ $client->delai_paiement == 90 ? 'selected' : '' }}>90 jours</option>
                            </select>
                            <div class="app-form-text">Délai accordé pour le règlement des factures</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="mode_paiement" class="app-form-label">
                                <i class="fas fa-money-check-alt me-2"></i>Mode de paiement
                            </label>
                            <select name="mode_paiement" id="mode_paiement" class="app-form-select" required>
                                <option value="Virement" {{ $client->mode_paiement == 'Virement' ? 'selected' : '' }}>Virement</option>
                                <option value="Chèque" {{ $client->mode_paiement == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                <option value="Espèces" {{ $client->mode_paiement == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                            </select>
                            <div class="app-form-text">Méthode de règlement préférée</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="regime_imposition" class="app-form-label">
                                <i class="fas fa-balance-scale me-2"></i>Régime d'imposition
                            </label>
                            <select name="regime_imposition" id="regime_imposition" class="app-form-select" required>
                                <option value="Régime A" {{ $client->regime_imposition == 'Régime A' ? 'selected' : '' }}>Régime A</option>
                                <option value="Régime B" {{ $client->regime_imposition == 'Régime B' ? 'selected' : '' }}>Régime B</option>
                            </select>
                            <div class="app-form-text">Régime fiscal applicable</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="boite_postale" class="app-form-label">
                                <i class="fas fa-mailbox me-2"></i>Boîte postale
                            </label>
                            <input type="text" name="boite_postale" id="boite_postale" class="app-form-control" value="{{ old('boite_postale', $client->boite_postale) }}" required>
                            <div class="app-form-text">Boîte postale du client</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-form-group">
                    <label for="adresse_localisation" class="app-form-label">
                        <i class="fas fa-map-marker-alt me-2"></i>Adresse
                    </label>
                    <input type="text" name="adresse_localisation" id="adresse_localisation" class="app-form-control" value="{{ old('adresse_localisation', $client->adresse_localisation) }}" required>
                    <div class="app-form-text">Adresse physique complète</div>
                </div>
                
                <div class="app-form-row">
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="email" class="app-form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" name="email" id="email" class="app-form-control" value="{{ old('email', $client->email) }}">
                            <div class="app-form-text">Adresse email professionnelle</div>
                        </div>
                    </div>
                    
                    <div class="app-form-col">
                        <div class="app-form-group">
                            <label for="telephone" class="app-form-label">
                                <i class="fas fa-phone-alt me-2"></i>Téléphone
                            </label>
                            <input type="text" name="telephone" id="telephone" class="app-form-control" value="{{ old('telephone', $client->telephone) }}">
                            <div class="app-form-text">Numéro de téléphone principal</div>
                        </div>
                    </div>
                </div>
                
                <div class="app-card-footer">
                    <a href="{{ route('fournisseurs.index') }}" class="app-btn app-btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="app-btn app-btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('categorie').addEventListener('change', function () {
        const particulierFields = document.getElementById('particulier_fields');
        const entrepriseFields = document.getElementById('entreprise_fields');
        const entrepriseFieldsSuite = document.getElementById('entreprise_fields_suite');
        
        if (this.value === 'Entreprise') {
            particulierFields.style.display = 'none';
            entrepriseFields.style.display = 'flex';
            entrepriseFieldsSuite.style.display = 'flex';
        } else {
            particulierFields.style.display = 'flex';
            entrepriseFields.style.display = 'none';
            entrepriseFieldsSuite.style.display = 'none';
        }
    });
</script>
@endpush
@endsection