{{-- Page Create - Ajouter un client --}}
@extends('layouts.app')

@section('title', 'Ajouter un client')
@section('page-title', 'Ajouter un client')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
<li class="breadcrumb-item active">Ajouter</li>
@endsection

@section('content')

<div class="container app-fade-in">
    <div class="row justify-content-left">
        <div class="col-md-8">
            <div class="app-card app-hover-shadow">
                <div class="app-card-header">
                    <h2 class="app-card-title">
                        <i class="fas fa-user-plus me-2"></i>Ajouter un client
                    </h2>
                </div>
                
                <div class="app-card-body">
                    @if ($errors->any())
                        <div class="app-alert app-alert-danger">
                            <div class="app-alert-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="app-alert-content">
                                <div class="app-alert-text">
                                    @foreach ($errors->all() as $error)
                                        <p class="app-mb-1">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" class="app-alert-close" onclick="this.parentElement.style.display='none';">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                
                    <form action="{{ route('clients.store') }}" method="POST" class="app-form">
                        @csrf
                        
                        <div class="app-form-group">
                            <label for="categorie" class="app-form-label">
                                <i class="fas fa-tag me-2"></i>Catégorie:
                            </label>
                            <select name="categorie" id="categorie" class="app-form-select" required>
                                <option value="Particulier">Particulier</option>
                                <option value="Entreprise">Entreprise</option>
                            </select>
                            <div class="app-form-text">Type de client</div>
                        </div>

                        <div id="particulier_fields" class="app-form-group">
                            <label for="nom" class="app-form-label">
                                <i class="fas fa-user me-2"></i>Nom:
                            </label>
                            <input type="text" name="nom_raison_sociale" class="app-form-control" required>
                            
                            <label for="prenoms" class="app-form-label app-mt-3">
                                <i class="fas fa-user-tag me-2"></i>Prénoms:
                            </label>
                            <input type="text" name="prenoms" class="app-form-control" required>
                        </div>

                        <div id="entreprise_fields" class="app-form-group" style="display:none;">
                            <label for="raison_sociale" class="app-form-label">
                                <i class="fas fa-building me-2"></i>Raison Sociale:
                            </label>
                            <input type="text" name="raison_sociale" class="app-form-control">
                            
                            <label for="n_rccm" class="app-form-label app-mt-3">
                                <i class="fas fa-file-alt me-2"></i>N° RCCM:
                            </label>
                            <input type="text" name="n_rccm" class="app-form-control">
                            
                            <label for="n_cc" class="app-form-label app-mt-3">
                                <i class="fas fa-id-card me-2"></i>N° CC:
                            </label>
                            <input type="text" name="n_cc" class="app-form-control">
                            
                            <label for="secteur_activite" class="app-form-label app-mt-3">
                                <i class="fas fa-industry me-2"></i>Secteur d'Activité:
                            </label>
                            <select name="secteur_activite" class="app-form-select">
                                @foreach ($secteurs as $secteur)
                                    <option value="{{ $secteur->nom }}">{{ $secteur->nom }}</option>
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
                                        <option value="30">30 jours</option>
                                        <option value="60">60 jours</option>
                                        <option value="90">90 jours</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="mode_paiement" class="app-form-label">
                                        <i class="fas fa-money-bill-wave me-2"></i>Mode de paiement:
                                    </label>
                                    <select name="mode_paiement" class="app-form-select" required>
                                        <option value="Virement">Virement</option>
                                        <option value="Chèque">Chèque</option>
                                        <option value="Espèces">Espèces</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-group">
                            <label for="regime_imposition" class="app-form-label">
                                <i class="fas fa-balance-scale me-2"></i>Régime d'imposition:
                            </label>
                            <select name="regime_imposition" class="app-form-select" required>
                                <option value="">Sélectionner un régime d'imposition</option>
                                @foreach ($regimes as $regime)
                                    <option value="{{ $regime->nom }}">{{ $regime->nom }} ({{ $regime->ref }}) - TVA: {{ $regime->tva }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="boite_postale" class="app-form-label">
                                        <i class="fas fa-mailbox me-2"></i>Boîte postale:
                                    </label>
                                    <input type="text" name="boite_postale" class="app-form-control" required>
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="adresse_localisation" class="app-form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Adresse:
                                    </label>
                                    <input type="text" name="adresse_localisation" class="app-form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="app-form-row">
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="email" class="app-form-label">
                                        <i class="fas fa-envelope me-2"></i>Email:
                                    </label>
                                    <input type="email" name="email" class="app-form-control">
                                </div>
                            </div>
                            
                            <div class="app-form-col">
                                <div class="app-form-group">
                                    <label for="telephone" class="app-form-label">
                                        <i class="fas fa-phone me-2"></i>Téléphone:
                                    </label>
                                    <input type="text" name="telephone" class="app-form-control">
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
                                    <!-- Les contacts seront ajoutés ici dynamiquement -->
                                </div>
                            </div>
                        </div>

                        <div class="app-card-footer">
                            <a href="{{ route('clients.index') }}" class="app-btn app-btn-secondary">
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
    </div>
</div>

@push('scripts')
<script>
    let contactIndex = 0;
    
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
        
        // Ajouter le premier contact par défaut
        addContactForm();
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