@extends('layouts.app')

@section('content')
<div class="app-container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="app-mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('fournisseurs.index') }}">Fournisseurs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails du fournisseur</li>
        </ol>
    </nav>

    <!-- En-tête -->
    <div class="app-d-flex app-justify-content-between app-align-items-center app-mb-4">
        <div>
            <h1 class="app-h3 app-mb-1">{{ $fournisseur->nom_raison_sociale }} {{ $fournisseur->prenoms }}</h1>
            <p class="app-text-muted app-mb-0">
                <i class="fas fa-truck app-me-2"></i>
                Fournisseur {{ $fournisseur->categorie }}
            </p>
        </div>
        <div class="app-d-flex app-gap-2">
            <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="app-btn app-btn-warning">
                <i class="fas fa-edit app-me-2"></i>Modifier
            </a>
            <a href="{{ route('fournisseurs.index') }}" class="app-btn app-btn-secondary">
                <i class="fas fa-arrow-left app-me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="app-card app-mb-4">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-info-circle app-me-2"></i>
                        Informations principales
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Nom/Raison sociale</label>
                            <p class="app-mb-0">{{ $fournisseur->nom }}</p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Type de fournisseur</label>
                            <p class="app-mb-0">
                                <span class="badge bg-{{ $fournisseur->type === 'Particulier' ? 'info' : 'success' }}">
                                    {{ $fournisseur->categorie }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Téléphone</label>
                            <p class="app-mb-0">{{ $fournisseur->telephone ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Email</label>
                            <p class="app-mb-0">{{ $fournisseur->email ?? 'Non renseigné' }}</p>
                        </div>
                        <div class="col-12 app-mb-3">
                            <label class="app-form-label app-fw-bold">Adresse</label>
                            <p class="app-mb-0">{{ $fournisseur->adresse ?? 'Non renseignée' }}</p>
                        </div>
            
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Régime d'imposition</label>
                            <p class="app-mb-0">{{ $fournisseur->regime_imposition ?? 'Non renseigné' }}</p>
                        </div>
                        
                        @if($fournisseur->n_rccm)
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">N° RCCM</label>
                            <p class="app-mb-0">{{ $fournisseur->n_rccm }}</p>
                        </div>
                        @endif
                        
                        @if($fournisseur->n_cc)
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">N° CC</label>
                            <p class="app-mb-0">{{ $fournisseur->n_cc }}</p>
                        </div>
                        @endif
                        
                        @if($fournisseur->secteur_activite)
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Secteur d'activité</label>
                            <p class="app-mb-0">{{ $fournisseur->secteur_activite }}</p>
                        </div>
                        @endif
                        
                        @if($fournisseur->delai_paiement)
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Délai de paiement</label>
                            <p class="app-mb-0">{{ $fournisseur->delai_paiement }} jours</p>
                        </div>
                        @endif
                        
                        @if($fournisseur->mode_paiement)
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Mode de paiement</label>
                            <p class="app-mb-0">{{ $fournisseur->mode_paiement }}</p>
                        </div>
                        @endif
                        
                        @if($fournisseur->boite_postale)
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Boîte postale</label>
                            <p class="app-mb-0">{{ $fournisseur->boite_postale }}</p>
                        </div>
                        @endif
                        
                        @if($fournisseur->bus)
                        <div class="col-md-6 app-mb-3">
                            <label class="app-form-label app-fw-bold">Business Unit</label>
                            <p class="app-mb-0">{{ $fournisseur->bus->nom ?? 'Non renseigné' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            @if($fournisseur->contactPersons && $fournisseur->contactPersons->count() > 0)
            <div class="app-card">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-users app-me-2"></i>
                        Personnes de contact
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="row">
                        @foreach($fournisseur->contactPersons as $contact)
                        <div class="col-md-6 app-mb-3">
                            <div class="border rounded p-3 {{ $contact->contact_principal ? 'border-success bg-light' : '' }}">
                                @if($contact->contact_principal)
                                <div class="app-mb-2">
                                    <span class="badge bg-success">Contact principal</span>
                                </div>
                                @endif
                                <h6 class="app-mb-2">
                                    {{ $contact->civilite }} {{ $contact->nom }} {{ $contact->prenoms }}
                                </h6>
                                @if($contact->fonction)
                                <p class="app-text-muted app-mb-1"><small>{{ $contact->fonction }}</small></p>
                                @endif
                                @if($contact->telephone_1)
                                <p class="app-mb-1"><i class="fas fa-phone app-me-2"></i>{{ $contact->telephone_1 }}</p>
                                @endif
                                @if($contact->email)
                                <p class="app-mb-1"><i class="fas fa-envelope app-me-2"></i>{{ $contact->email }}</p>
                                @endif
                                <span class="badge bg-{{ $contact->statut === 'Actif' ? 'success' : 'secondary' }}">
                                    {{ $contact->statut }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Demandes de ravitaillement liées -->
            @if($fournisseur->demandesRavitaillement && $fournisseur->demandesRavitaillement->count() > 0)
            <div class="app-card app-mb-4">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-truck-loading app-me-2"></i>
                        Demandes de ravitaillement ({{ $fournisseur->demandesRavitaillement->count() }})
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Date demande</th>
                                    <th>Statut</th>
                                    <th>Montant total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fournisseur->demandesRavitaillement as $demande)
                                <tr>
                                    <td><strong>{{ $demande->reference ?? 'N/A' }}</strong></td>
                                    <td>{{ $demande->date_demande ? \Carbon\Carbon::parse($demande->date_demande)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        @if($demande->statut)
                                            <span class="badge bg-{{ $demande->statut === 'Validée' ? 'success' : ($demande->statut === 'En attente' ? 'warning' : 'secondary') }}">
                                                {{ $demande->statut }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($demande->montant_total)
                                            <span class="badge bg-info">{{ number_format($demande->montant_total, 0, ',', ' ') }} FCFA</span>
                                        @else
                                            <span class="text-muted">Non calculé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('demandes-ravitaillement.show', $demande->id) }}" class="btn btn-sm btn-outline-primary" title="Voir la demande">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="app-card app-mb-4">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-truck-loading app-me-2"></i>
                        Demandes de ravitaillement
                    </h5>
                </div>
                <div class="app-card-body text-center">
                    <i class="fas fa-truck-loading fa-3x text-muted app-mb-3"></i>
                    <p class="text-muted">Aucune demande de ravitaillement associée à ce fournisseur.</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Informations complémentaires -->
        <div class="col-lg-4">
            <div class="app-card app-mb-4">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-cog app-me-2"></i>
                        Informations système
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="app-mb-3">
                        <label class="app-form-label app-fw-bold">Statut</label>
                        <p class="app-mb-0">
                            <span class="badge bg-{{ $fournisseur->statut === 'Actif' ? 'success' : 'secondary' }}">
                                {{ $fournisseur->statut }}
                            </span>
                        </p>
                    </div>
                    <div class="app-mb-3">
                        <label class="app-form-label app-fw-bold">Date de création</label>
                        <p class="app-mb-0">{{ $fournisseur->created_at ? $fournisseur->created_at->format('d/m/Y à H:i') : 'Non disponible' }}</p>
                    </div>
                    <div class="app-mb-0">
                        <label class="app-form-label app-fw-bold">Dernière modification</label>
                        <p class="app-mb-0">{{ $fournisseur->updated_at ? $fournisseur->updated_at->format('d/m/Y à H:i') : 'Non disponible' }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="app-card">
                <div class="app-card-header">
                    <h5 class="app-card-title app-mb-0">
                        <i class="fas fa-chart-bar app-me-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="app-card-body">
                    <div class="app-mb-3">
                        <label class="app-form-label app-fw-bold">Nombre de contacts</label>
                        <p class="app-mb-0">{{ $fournisseur->contactPersons ? $fournisseur->contactPersons->count() : 0 }}</p>
                    </div>
                    <div class="app-mb-0">
                        <label class="app-form-label app-fw-bold">Contact principal défini</label>
                        <p class="app-mb-0">
                            @if($fournisseur->contactPersons && $fournisseur->contactPersons->where('contact_principal', true)->count() > 0)
                                <span class="badge bg-success">Oui</span>
                            @else
                                <span class="badge bg-warning">Non</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection