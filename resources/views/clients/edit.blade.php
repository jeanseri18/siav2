{{-- Page Edit - Modifier un client --}}
@extends('layouts.app')

@section('title', 'Modifier un client')
@section('page-title', 'Modifier un client')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
<li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-user-edit me-2"></i>Modifier le client: {{ $client->nom_raison_sociale }}
                    </h2>
                </div>
                
                <div class="app-card-body">
                    <form action="{{ route('clients.update', $client->id) }}" method="POST" class="app-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="app-form-group">
                            <label for="categorie" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Catégorie:
                            </label>
                            <select name="categorie" id="categorie" class="app-form-select" required>
                                <option value="Particulier" {{ $client->categorie == 'Particulier' ? 'selected' : '' }}>Particulier</option>
                                <option value="Entreprise" {{ $client->categorie == 'Entreprise' ? 'selected' : '' }}>Entreprise</option>
                            </select>
                            <div class="app-form-text">Type de client</div>
                        </div>

                        <div id="particulier_fields" class="app-form-group" @if($client->categorie == 'Particulier') style="display:block;" @else style="display:none;" @endif>
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-user me-2"></i>Nom:
                            </label>
                            <input type="text" name="nom_raison_sociale" value="{{ old('nom_raison_sociale', $client->nom_raison_sociale) }}" class="app-form-control" required>
                            
                            <label for="prenoms" class="app-form-label app-mt-3">
                                <i class="fas fa-user-tag me-2"></i>Prénoms:
                            </label>
                            <input type="text" name="prenoms" value="{{ old('prenoms', $client->prenoms) }}" class="app-form-control" required>
                        </div>

                        <div id="entreprise_fields" class="app-form-group" @if($client->categorie == 'Entreprise') style="display:block;" @else style="display:none;" @endif>
                            <label for="raison_sociale" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Raison Sociale:
                            </label>
                            <input type="text" name="raison_sociale" value="{{ old('raison_sociale', $client->raison_sociale) }}" class="app-form-control">
                            
                            <label for="n_rccm" class="app-form-label app-mt-3">
                                <i class="fas fa-file-alt me-2"></i>N° RCCM:
                            </label>
                            <input type="text" name="n_rccm" value="{{ old('n_rccm', $client->n_rccm) }}" class="app-form-control">
                            
                            <label for="n_cc" class="app-form-label app-mt-3">
                                <i class="fas fa-id-card me-2"></i>N° CC:
                            </label>
                            <input type="text" name="n_cc" value="{{ old('n_cc', $client->n_cc) }}" class="app-form-control">
                            
                            <label for="secteur_activite" class="app-form-label app-mt-3">
                                <i class="fas fa-industry me-2"></i>Secteur d'Activité:
                            </label>
                            <select name="secteur_activite" class="app-form-select">
                                @foreach ($secteurs as $secteur)
                                    <option value="{{ $secteur->nom }}" {{ $client->secteur_activite == $secteur->nom ? 'selected' : '' }}>{{ $secteur->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="delai_paiement" class="app-form-label">
                                        <i class="fas fa-calendar me-2"></i>Délai de paiement:
                                    </label>
                                    <select name="delai_paiement" class="app-form-select" required>
                                        <option value="30" {{ $client->delai_paiement == 30 ? 'selected' : '' }}>30 jours</option>
                                        <option value="60" {{ $client->delai_paiement == 60 ? 'selected' : '' }}>60 jours</option>
                                        <option value="90" {{ $client->delai_paiement == 90 ? 'selected' : '' }}>90 jours</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="mode_paiement" class="app-form-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>Mode de paiement:
                                    </label>
                                    <select name="mode_paiement" class="app-form-select" required>
                                        <option value="Virement" {{ $client->mode_paiement == 'Virement' ? 'selected' : '' }}>Virement</option>
                                        <option value="Chèque" {{ $client->mode_paiement == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                        <option value="Espèces" {{ $client->mode_paiement == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-group">
                            <label for="regime_imposition" class="app-form-label">
                                <i class="fas fa-balance-scale me-2"></i>Régime d'imposition:
                            </label>
                            <select name="regime_imposition" class="app-form-select" required>
                                @foreach ($regimes as $regime)
                                    <option value="{{ $regime->nom }}" {{ $client->regime_imposition == $regime->nom ? 'selected' : '' }}>{{ $regime->nom }} ({{ $regime->ref }}) - TVA: {{ $regime->tva }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="boite_postale" class="app-form-label">
                                        <i class="fas fa-mailbox me-2"></i>Boîte postale:
                                    </label>
                                    <input type="text" name="boite_postale" value="{{ old('boite_postale', $client->boite_postale) }}" class="app-form-control" required>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="adresse_localisation" class="app-form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Adresse:
                                    </label>
                                    <input type="text" name="adresse_localisation" value="{{ old('adresse_localisation', $client->adresse_localisation) }}" class="app-form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="email" class="app-form-label">
                                        <i class="fas fa-envelope me-2"></i>Email:
                                    </label>
                                    <input type="email" name="email" value="{{ old('email', $client->email) }}" class="app-form-control">
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="telephone" class="app-form-label">
                                        <i class="fas fa-phone me-2"></i>Téléphone:
                                    </label>
                                    <input type="text" name="telephone" value="{{ old('telephone', $client->telephone) }}" class="app-form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Section Personnes Contacts -->
                        <div class="app-card app-mt-4">
                            <div class="app-card-header">
                                <h4 class="app-card-title">
                                    <i class="fas fa-users me-2"></i>Personnes Contacts
                                </h4>
                                <button type="button" class="app-btn app-btn-sm app-btn-primary" id="add-contact">
                                    <i class="fas fa-plus me-2"></i>Ajouter un contact
                                </button>
                            </div>
                            <div class="app-card-body">
                                <div id="contacts-container">
                                    @foreach($client->contactPersons as $index => $contact)
                                        <div class="contact-form app-card app-mb-3" data-index="{{ $index }}">
                                            <div class="app-card-header">
                                                <h5 class="app-card-title">Contact {{ $index + 1 }}</h5>
                                                <button type="button" class="app-btn app-btn-sm app-btn-danger remove-contact">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="app-card-body">
                                                <input type="hidden" name="contacts[{{ $index }}][id]" value="{{ $contact->id }}">
                                                <div class="app-form-row">
                                                    <div class="app-form-col-3">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Civilité:</label>
                                                            <select name="contacts[{{ $index }}][civilite]" class="app-form-select" required>
                                                                <option value="M." {{ $contact->civilite == 'M.' ? 'selected' : '' }}>M.</option>
                                                                <option value="Mme" {{ $contact->civilite == 'Mme' ? 'selected' : '' }}>Mme</option>
                                                                <option value="Mlle" {{ $contact->civilite == 'Mlle' ? 'selected' : '' }}>Mlle</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="app-form-col-3">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Nom:</label>
                                                            <input type="text" name="contacts[{{ $index }}][nom]" value="{{ $contact->nom }}" class="app-form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="app-form-col-3">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Prénoms:</label>
                                                            <input type="text" name="contacts[{{ $index }}][prenoms]" value="{{ $contact->prenoms }}" class="app-form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="app-form-col-3">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Fonction:</label>
                                                            <input type="text" name="contacts[{{ $index }}][fonction]" value="{{ $contact->fonction }}" class="app-form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="app-form-row">
                                                    <div class="app-form-col">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Téléphone 1:</label>
                                                            <input type="text" name="contacts[{{ $index }}][telephone_1]" value="{{ $contact->telephone_1 }}" class="app-form-control">
                                                        </div>
                                                    </div>
                                                    <div class="app-form-col">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Téléphone 2:</label>
                                                            <input type="text" name="contacts[{{ $index }}][telephone_2]" value="{{ $contact->telephone_2 }}" class="app-form-control">
                                                        </div>
                                                    </div>
                                                    <div class="app-form-col">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Email:</label>
                                                            <input type="email" name="contacts[{{ $index }}][email]" value="{{ $contact->email }}" class="app-form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="app-form-group">
                                                    <label class="app-form-label">Adresse:</label>
                                                    <textarea name="contacts[{{ $index }}][adresse]" class="app-form-control" rows="2">{{ $contact->adresse }}</textarea>
                                                </div>
                                                
                                                <div class="app-form-row">
                                                    <div class="app-form-col">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">
                                                                <input type="checkbox" name="contacts[{{ $index }}][contact_principal]" value="1" class="contact-principal-checkbox" {{ $contact->contact_principal ? 'checked' : '' }}>
                                                                Contact principal
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="app-form-col">
                                                        <div class="app-form-group">
                                                            <label class="app-form-label">Statut:</label>
                                                            <select name="contacts[{{ $index }}][statut]" class="app-form-select">
                                                                <option value="Actif" {{ $contact->statut == 'Actif' ? 'selected' : '' }}>Actif</option>
                                                                <option value="Inactif" {{ $contact->statut == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="app-card-footer">
                            <a href="{{ route('clients.index') }}" class="app-btn app-btn-secondary">
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
    </div>
</div>

@push('scripts')
<script>
    let contactIndex = {{ $client->contactPersons->count() }};
    
    $(document).ready(function() {
        // Afficher/masquer les champs en fonction de la catégorie sélectionnée
        $('#categorie').change(function() {
            const particulierFields = document.getElementById('particulier_fields');
            const entrepriseFields = document.getElementById('entreprise_fields');
            
            if (this.value === 'Entreprise') {
                particulierFields.style.display = 'none';
                entrepriseFields.style.display = 'block';
            } else {
                particulierFields.style.display = 'block';
                entrepriseFields.style.display = 'none';
            }
        });
        
        // Ajouter un contact
        $('#add-contact').click(function() {
            addContactForm();
        });
        
        // Initialiser les événements pour les contacts existants
        initializeContactEvents();
    });
    
    function addContactForm() {
        const contactHtml = `
            <div class="contact-form app-card app-mb-3" data-index="${contactIndex}">
                <div class="app-card-header">
                    <h5 class="app-card-title">Contact ${contactIndex + 1}</h5>
                    <button type="button" class="app-btn app-btn-sm app-btn-danger remove-contact">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="app-card-body">
                    <div class="app-form-row">
                        <div class="app-form-col-3">
                            <div class="app-form-group">
                                <label class="app-form-label">Civilité:</label>
                                <select name="contacts[${contactIndex}][civilite]" class="app-form-select" required>
                                    <option value="M.">M.</option>
                                    <option value="Mme">Mme</option>
                                    <option value="Mlle">Mlle</option>
                                </select>
                            </div>
                        </div>
                        <div class="app-form-col-3">
                            <div class="app-form-group">
                                <label class="app-form-label">Nom:</label>
                                <input type="text" name="contacts[${contactIndex}][nom]" class="app-form-control" required>
                            </div>
                        </div>
                        <div class="app-form-col-3">
                            <div class="app-form-group">
                                <label class="app-form-label">Prénoms:</label>
                                <input type="text" name="contacts[${contactIndex}][prenoms]" class="app-form-control" required>
                            </div>
                        </div>
                        <div class="app-form-col-3">
                            <div class="app-form-group">
                                <label class="app-form-label">Fonction:</label>
                                <input type="text" name="contacts[${contactIndex}][fonction]" class="app-form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label">Téléphone 1:</label>
                                <input type="text" name="contacts[${contactIndex}][telephone_1]" class="app-form-control">
                            </div>
                        </div>
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label">Téléphone 2:</label>
                                <input type="text" name="contacts[${contactIndex}][telephone_2]" class="app-form-control">
                            </div>
                        </div>
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label">Email:</label>
                                <input type="email" name="contacts[${contactIndex}][email]" class="app-form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="app-form-group">
                        <label class="app-form-label">Adresse:</label>
                        <textarea name="contacts[${contactIndex}][adresse]" class="app-form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="app-form-row">
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label">
                                    <input type="checkbox" name="contacts[${contactIndex}][contact_principal]" value="1" class="contact-principal-checkbox">
                                    Contact principal
                                </label>
                            </div>
                        </div>
                        <div class="app-form-col">
                            <div class="app-form-group">
                                <label class="app-form-label">Statut:</label>
                                <select name="contacts[${contactIndex}][statut]" class="app-form-select">
                                    <option value="Actif" selected>Actif</option>
                                    <option value="Inactif">Inactif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#contacts-container').append(contactHtml);
        contactIndex++;
        
        // Réinitialiser les événements
        initializeContactEvents();
    }
    
    function initializeContactEvents() {
        // Gérer la suppression des contacts
        $('.remove-contact').off('click').on('click', function() {
            if ($('.contact-form').length > 1) {
                $(this).closest('.contact-form').remove();
            } else {
                alert('Au moins un contact est requis.');
            }
        });
        
        // Gérer les contacts principaux (un seul à la fois)
        $('.contact-principal-checkbox').off('change').on('change', function() {
            if ($(this).is(':checked')) {
                $('.contact-principal-checkbox').not(this).prop('checked', false);
            }
        });
    }
</script>
@endpush
@endsection