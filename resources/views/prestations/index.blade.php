@extends('layouts.app')

@section('title', 'Liste des Prestations')
@section('page-title', 'Liste des Prestations')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('contrats.index') }}">Contrats</a></li>
<li class="breadcrumb-item active">Prestations</li>
@endsection

@section('content')
@include('sublayouts.contrat')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-tools me-2"></i>Liste des Prestations
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('prestations.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i> Ajouter une prestation
                </a>
            </div>
        </div>

        <div class="app-card-body app-table-responsive">
            <table id="Table" class="app-table display">
                <thead>
                    <tr>
                        <th>Prestataire</th>
                        <th>Contrat</th>
                        <th>Corps de Métier</th>
                        <th>Prestation</th>
                        <th>Montant</th>
                        <th>Avancement</th>
                        <th>Statut</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestations as $prestation)
                    <tr>
                        <td width="25%;">
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    @if($prestation->artisan)
                                        <i class="fas fa-hard-hat text-primary"></i>
                                    @elseif($prestation->fournisseur)
                                        <i class="fas fa-building text-success"></i>
                                    @else
                                        <i class="fas fa-user-slash text-secondary"></i>
                                    @endif
                                </div>
                                <span>
                                    @if($prestation->artisan)
                                        {{ $prestation->artisan->nom }}  {{ $prestation->artisan->prenoms }}
                                    @elseif($prestation->fournisseur)
                                        {{ $prestation->fournisseur->raison_social }}                                         {{ $prestation->fournisseur->prenoms }}
                                    @else
                                        Non assigné
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td>{{ $prestation->contrat->nom_contrat }}</td>
                        <td>
                            <div class="app-d-flex app-align-items-center app-gap-2">
                                <div class="item-icon">
                                    <i class="fas fa-tools text-info"></i>
                                </div>
                                <span>{{ $prestation->corpMetier ? $prestation->corpMetier->nom : 'Non défini' }}</span>
                            </div>
                        </td>
                        <td>{{ $prestation->prestation_titre }}</td>
                        <td>{{ number_format($prestation->montant, 2, ',', ' ') }} FCFA</td>
                        <td>
                            @if($prestation->taux_avancement)
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                    style="width: {{ $prestation->taux_avancement }}%" 
                                    aria-valuenow="{{ $prestation->taux_avancement }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $prestation->taux_avancement }}%
                                </div>
                            </div>
                            @else
                            0%
                            @endif
                        </td>
                        <td>
                            <span class="app-badge app-badge-{{ $prestation->statut == 'En cours' ? 'warning' : ($prestation->statut == 'Terminée' ? 'success' : 'danger') }} app-badge-pill">
                                {{ $prestation->statut }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="app-btn app-btn-primary app-btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $prestation->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cog me-1"></i>Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $prestation->id }}">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="affecterArtisan({{ $prestation->id }})">
                                            <i class="fas fa-user-plus me-2"></i>Affecter un prestataire
                                        </a>
                                    </li>

                                    
                                    <li>
                                        <a class="dropdown-item" href="{{ route('prestations.edit', $prestation->id) }}">
                                            <i class="fas fa-edit me-2"></i>modifier  les détails
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('prestations.lignes', $prestation->id) }}">
                                            <i class="fas fa-list me-2"></i>Créer les lignes prestation
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('prestations.voirLignes', $prestation->id) }}">
                                            <i class="fas fa-eye me-2"></i>Voir les lignes prestation
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('prestations.document', $prestation->id) }}" target="_blank">
                                            <i class="fas fa-file-pdf me-2"></i>Générer un document
                                        </a>
                                    </li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="remplacerArtisan({{ $prestation->id }})">
                                            <i class="fas fa-user-edit me-2"></i>Remplacer le prestataire
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="supprimerPrestation({{ $prestation->id }})">
                                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

 <!-- Modal pour affecter un prestataire -->
<div class="modal fade" id="affecterArtisanModal" tabindex="-1" aria-labelledby="affecterArtisanModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="affecterArtisanModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Affecter un prestataire
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="affecterArtisanForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type_prestataire_hidden" id="type_prestataire_hidden">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type_prestataire" class="form-label">Type de prestataire</label>
                        <select name="type_prestataire" id="type_prestataire" class="form-select" onchange="changerTypePrestataire()" required>
                            <option value="">-- Choisir le type --</option>
                            <option value="artisan">Artisan</option>
                            <option value="fournisseur">Fournisseur</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="artisan_select" class="form-label">Sélectionner un prestataire</label>
                        <select name="id_artisan" id="artisan_select" class="form-select" required>
                            <option value="">-- Choisir d'abord le type --</option>
                            <!-- Les prestataires seront chargés dynamiquement via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_affectation" class="form-label">Date d'affectation</label>
                        <input type="date" name="date_affectation" id="date_affectation" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Affecter le prestataire</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour remplacer un prestataire -->
<div class="modal fade" id="remplacerArtisanModal" tabindex="-1" aria-labelledby="remplacerArtisanModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remplacerArtisanModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Remplacer le prestataire
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="remplacerArtisanForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Prestataire actuel</label>
                        <input type="text" id="artisan_actuel" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nouvel_artisan" class="form-label">Nouveau prestataire</label>
                        <select name="id_artisan" id="nouvel_artisan" class="form-select" required>
                            <option value="">-- Choisir un nouveau prestataire --</option>
                            <!-- Les artisans seront chargés dynamiquement via JavaScript -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="motif_remplacement" class="form-label">Motif du remplacement</label>
                        <textarea name="motif_remplacement" id="motif_remplacement" class="form-control" rows="3" placeholder="Raison du changement de prestataire..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Remplacer le prestataire</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails -->
<div class="modal fade" id="voirDetailsModal" tabindex="-1" aria-labelledby="voirDetailsModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voirDetailsModalLabel">
                    <i class="fas fa-eye me-2"></i>Détails de la prestation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter des comptes -->
<div class="modal fade" id="ajouterComptesModal" tabindex="-1" aria-labelledby="ajouterComptesModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ajouterComptesModalLabel">
                    <i class="fas fa-calculator me-2"></i>Ajouter des comptes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ajouterComptesForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type_compte" class="form-label">Type de compte</label>
                                <select name="type_compte" id="type_compte" class="form-select" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="materiel">Matériel</option>
                                    <option value="main_oeuvre">Main d'œuvre</option>
                                    <option value="transport">Transport</option>
                                    <option value="autres">Autres</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="montant_compte" class="form-label">Montant</label>
                                <input type="number" step="0.01" name="montant" id="montant_compte" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description_compte" class="form-label">Description</label>
                        <textarea name="description" id="description_compte" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="date_compte" class="form-label">Date</label>
                        <input type="date" name="date_compte" id="date_compte" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter le compte</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour afficher les décomptes -->


@push('styles')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        // Configuration DataTable
        $('#Table').DataTable({
            responsive: true,
            dom: '<"dt-header"Bf>rt<"dt-footer"ip>',
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="fas fa-file-export"></i> Exporter',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                },
                {
                    extend: 'colvis',
                    text: '<i class="fas fa-columns"></i> Colonnes'
                }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            }
        });
        
        // Amélioration visuelle des boutons DataTables
        $('.dt-buttons .dt-button').addClass('app-btn app-btn-outline-primary app-btn-sm me-2');
        
        // Variable globale pour stocker l'ID de prestation
        let currentPrestationId = null;
        
        // Fonctions pour les actions du menu déroulant
        window.affecterArtisan = function(prestationId) {
            // Stocker l'ID de prestation
            currentPrestationId = prestationId;
            
            // Configurer le formulaire pour l'affectation d'artisan
            document.getElementById('affecterArtisanForm').action = `/prestations/${prestationId}/affecter-artisan`;
            
            // Réinitialiser le formulaire
            document.getElementById('type_prestataire').value = '';
            document.getElementById('artisan_select').innerHTML = '<option value="">-- Choisir d\'abord le type --</option>';
            
            // Afficher la modale
            new bootstrap.Modal(document.getElementById('affecterArtisanModal')).show();
        };
        
        window.changerTypePrestataire = function() {
            const typePrestataire = document.getElementById('type_prestataire').value;
            const select = document.getElementById('artisan_select');
            
            // Mettre à jour le champ caché
            document.getElementById('type_prestataire_hidden').value = typePrestataire;
            
            if (!typePrestataire) {
                select.innerHTML = '<option value="">-- Choisir d\'abord le type --</option>';
                return;
            }
            
            // Charger la liste des prestataires disponibles
            fetch(`/prestations/${currentPrestationId}/artisans-disponibles`)
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">-- Choisir un prestataire --</option>';
                    
                    if (typePrestataire === 'artisan' && data.artisans) {
                        data.artisans.forEach(artisan => {
                            select.innerHTML += `<option value="${artisan.id}" data-type="artisan">${artisan.nom} ${artisan.prenoms || ''}</option>`;
                        });
                    } else if (typePrestataire === 'fournisseur' && data.fournisseurs) {
                        data.fournisseurs.forEach(fournisseur => {
                            select.innerHTML += `<option value="${fournisseur.id}" data-type="fournisseur">${fournisseur.nom_raison_sociale} ${fournisseur.prenoms || ''}</option>`;
                        });
                    }
                })
                .catch(error => console.error('Erreur:', error));
        };
        
        window.voirDetails = function(prestationId) {
            // Charger les détails de la prestation
            fetch(`/prestations/${prestationId}/details`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('detailsContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('voirDetailsModal')).show();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('detailsContent').innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des détails.</div>';
                    new bootstrap.Modal(document.getElementById('voirDetailsModal')).show();
                });
        };
        
        window.ajouterComptes = function(prestationId) {
            // Configurer le formulaire pour l'ajout de comptes
            document.getElementById('ajouterComptesForm').action = `/prestations/${prestationId}/comptes`;
            
            // Afficher la modale
            new bootstrap.Modal(document.getElementById('ajouterComptesModal')).show();
        };
        

        
        window.remplacerArtisan = function(prestationId) {
            // Configurer le formulaire pour le remplacement d'artisan
            document.getElementById('remplacerArtisanForm').action = `/prestations/${prestationId}/remplacer-artisan`;
            
            // Charger les informations de l'artisan actuel et la liste des artisans disponibles
            fetch(`/prestations/${prestationId}/artisan-info`)
                .then(response => response.json())
                .then(data => {
                    // Afficher l'artisan actuel
                    document.getElementById('artisan_actuel').value = data.artisan_actuel || 'Aucun artisan assigné';
                    
                    // Charger la liste des artisans disponibles
                    const select = document.getElementById('nouvel_artisan');
                    select.innerHTML = '<option value="">-- Choisir un nouvel artisan --</option>';
                    data.artisans_disponibles.forEach(artisan => {
                        select.innerHTML += `<option value="${artisan.id}">${artisan.nom}</option>`;
                    });
                })
                .catch(error => console.error('Erreur:', error));
            
            // Afficher la modale
            new bootstrap.Modal(document.getElementById('remplacerArtisanModal')).show();
        };
        
        window.imprimerDecomptes = function() {
             // Fonction pour imprimer les décomptes
             const printContent = document.getElementById('decomptesContent').innerHTML;
             const originalContent = document.body.innerHTML;
             
             document.body.innerHTML = printContent;
             window.print();
             document.body.innerHTML = originalContent;
             location.reload();
         };
         
         // Gestion des soumissions de formulaires AJAX
         document.getElementById('affecterArtisanForm').addEventListener('submit', function(e) {
             e.preventDefault();
             const formData = new FormData(this);
             
             fetch(this.action, {
                 method: 'POST',
                 body: formData,
                 headers: {
                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                 }
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     bootstrap.Modal.getInstance(document.getElementById('affecterArtisanModal')).hide();
                     location.reload();
                 } else {
                     alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                 }
             })
             .catch(error => {
                 console.error('Erreur:', error);
                 alert('Une erreur est survenue lors de l\'affectation de l\'artisan');
             });
         });
         
         document.getElementById('remplacerArtisanForm').addEventListener('submit', function(e) {
             e.preventDefault();
             const formData = new FormData(this);
             
             fetch(this.action, {
                 method: 'POST',
                 body: formData,
                 headers: {
                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                 }
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     bootstrap.Modal.getInstance(document.getElementById('remplacerArtisanModal')).hide();
                     location.reload();
                 } else {
                     alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                 }
             })
             .catch(error => {
                 console.error('Erreur:', error);
                 alert('Une erreur est survenue lors du remplacement de l\'artisan');
             });
         });
         
         document.getElementById('ajouterComptesForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('ajouterComptesModal')).hide();
                    alert('Compte ajouté avec succès!');
                    

                    
                    // Recharger la page pour afficher les changements
                    location.reload();
                } else {
                    alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'ajout du compte');
            });
        });
        

        
        window.supprimerPrestation = function(prestationId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette prestation ?')) {
                // Créer un formulaire de suppression dynamique
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/prestations/' + prestationId;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        };
        
        // Ancienne fonction de suppression (conservée pour compatibilité)
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir supprimer cette prestation ?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endpush
@endsection