<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BUController;
use App\Http\Controllers\SecteurActiviteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SousCategorieController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\ClientFournisseurController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\CorpsMetierController;
use App\Http\Controllers\ConfigGlobalController;

use App\Http\Controllers\ContratController;
use App\Http\Controllers\StockProjetController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TransfertsStockController;
// Routes web.php
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\PaysController;
use App\Http\Controllers\VilleController;
use App\Http\Controllers\SecteurController;
use App\Http\Controllers\RegimeImpositionController;
use App\Http\Controllers\UniteMesureController;
use App\Http\Controllers\TypeTravauxController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\BanqueController;
use App\Http\Controllers\MonnaieController;
use App\Http\Controllers\ModeDePaiementController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\BpuController;
use App\Http\Controllers\CategorieBpuController;
use App\Http\Controllers\SousCategorieBpuController;
use App\Http\Controllers\RubriqueController;
use App\Http\Controllers\DemandeApprovisionnementController;
use App\Http\Controllers\BonCommandeController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\DemandeAchatController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DemandeCotationController;
use App\Http\Controllers\DemandeRavitaillementController;
use App\Http\Controllers\DQEController;
use App\Http\Controllers\FraisGeneralController;
use App\Http\Controllers\DebourseController;
use App\Http\Controllers\DebourseChantierController;
use App\Http\Controllers\EmployeController;
use App\Models\DemandeApprovisionnement;
use App\Models\DemandeAchat;

use App\Http\Controllers\CommuneController;
use App\Http\Controllers\QuartierController;

// Routes pour les Communes
Route::get('/communes', [CommuneController::class, 'index'])->name('communes.index');
Route::get('/communes/create', [CommuneController::class, 'create'])->name('communes.create');
Route::post('/communes', [CommuneController::class, 'store'])->name('communes.store');
Route::get('/communes/{commune}', [CommuneController::class, 'show'])->name('communes.show');
Route::get('/communes/{commune}/edit', [CommuneController::class, 'edit'])->name('communes.edit');
Route::put('/communes/{commune}', [CommuneController::class, 'update'])->name('communes.update');
Route::delete('/communes/{commune}', [CommuneController::class, 'destroy'])->name('communes.destroy');
Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::get('users/create', [UserController::class, 'create'])->name('users.create');
Route::post('users', [UserController::class, 'store'])->name('users.store');
Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// Routes pour les Quartiers
Route::get('/quartiers', [QuartierController::class, 'index'])->name('quartiers.index');
Route::get('/quartiers/create', [QuartierController::class, 'create'])->name('quartiers.create');
Route::post('/quartiers', [QuartierController::class, 'store'])->name('quartiers.store');
Route::get('/quartiers/{quartier}', [QuartierController::class, 'show'])->name('quartiers.show');
Route::get('/quartiers/{quartier}/edit', [QuartierController::class, 'edit'])->name('quartiers.edit');
Route::put('/quartiers/{quartier}', [QuartierController::class, 'update'])->name('quartiers.update');
Route::delete('/quartiers/{quartier}', [QuartierController::class, 'destroy'])->name('quartiers.destroy');

// Routes pour les Employés
Route::get('/employes', [EmployeController::class, 'index'])->name('employes.index');
Route::get('/employes/create', [EmployeController::class, 'create'])->name('employes.create');
Route::post('/employes', [EmployeController::class, 'store'])->name('employes.store');
Route::get('/employes/{employe}', [EmployeController::class, 'show'])->name('employes.show');
Route::get('/employes/{employe}/edit', [EmployeController::class, 'edit'])->name('employes.edit');
Route::put('/employes/{employe}', [EmployeController::class, 'update'])->name('employes.update');
Route::delete('/employes/{employe}', [EmployeController::class, 'destroy'])->name('employes.destroy');
// Dans routes/web.php
// Routes pour les frais généraux
Route::get('/contrats_frais_generaux', [FraisGeneralController::class, 'index'])->name('frais_generaux.index');
Route::get('/contrats_frais_generaux_create/create', [FraisGeneralController::class, 'create'])->name('frais_generaux.create');
Route::post('/contrats/frais-generaux', [FraisGeneralController::class, 'store'])->name('frais_generaux.store');
Route::get('/frais-generaux_edit/{fraisGeneral}/edit', [FraisGeneralController::class, 'edit'])->name('frais_generaux.edit');
Route::put('/frais-generaux/{fraisGeneral}', [FraisGeneralController::class, 'update'])->name('frais_generaux.update');
Route::delete('/frais-generaux/{fraisGeneral}', [FraisGeneralController::class, 'destroy'])->name('frais_generaux.destroy');
Route::post('/contrats_frais_generaux_generate', [FraisGeneralController::class, 'generate'])->name('frais_generaux.generate');
Route::get('/frais-generaux_export/{fraisGeneral}/export', [FraisGeneralController::class, 'export'])->name('frais_generaux.export');
// Routes pour les DQE
Route::get('/contrats_dqe/dqe', [DQEController::class, 'index'])->name('dqe.index');
Route::get('/contrats_dqe_create/{contrat}/dqe/create', [DQEController::class, 'create'])->name('dqe.create');
Route::post('/contrats_dqe/{contrat}/dqe', [DQEController::class, 'store'])->name('dqe.store');
Route::get('/dqe/{dqe}', [DQEController::class, 'show'])->name('dqe.show');
Route::get('/dqe_edit/{dqe}/edit', [DQEController::class, 'edit'])->name('dqe.edit');
Route::put('/dqe/{dqe}', [DQEController::class, 'update'])->name('dqe.update');
Route::delete('/dqe/{dqe}', [DQEController::class, 'destroy'])->name('dqe.destroy');
Route::post('/contrats_dqe_generate/{contrat}/dqe/generate', [DQEController::class, 'generateFromBPU'])->name('dqe.generate');

// Routes pour les lignes de DQE
Route::post('/dqe/{dqe}/lines', [DQEController::class, 'addLine'])->name('dqe.lines.add');
Route::post('/dqe/{dqe}/lines/multiple', [DQEController::class, 'addMultipleLines'])->name('dqe.lines.addMultiple');
Route::put('/dqe/{dqe}/lines/{line}', [DQEController::class, 'updateLine'])->name('dqe.lines.update');
Route::delete('/dqe/{dqe}/lines/{line}', [DQEController::class, 'deleteLine'])->name('dqe.lines.delete');
Route::post('/dqe/{dqe}/sections', [DQEController::class, 'createSection'])->name('dqe.sections.create');

// Routes pour les déboursés
Route::get('/contrats_debourses/debourses', [DebourseController::class, 'index'])->name('debourses.index');
// Routes pour les vues séparées de déboursés
Route::get('/contrats_debourses/debourse-sec', [DebourseController::class, 'debourse_sec'])->name('debourses.sec');
// DÉSACTIVÉ - Route pour déboursé main d'œuvre temporairement commentée
// Route::get('/contrats_debourses/debourse-main-oeuvre', [DebourseController::class, 'debourseMainOeuvre'])->name('debourses.main_oeuvre');
Route::get('/contrats_debourses/frais-chantier', [DebourseController::class, 'frais_chantier'])->name('debourses.frais_chantier');
Route::get('/contrats_debourses/debourse-chantier', [DebourseChantierController::class, 'index'])->name('debourses.chantier');
Route::post('/dqe_debourses/{dqe}/debourses/generate', [DebourseController::class, 'generate'])->name('debourses.generate');

// Nouvelles routes pour la génération spécifique de chaque type de déboursé
Route::post('/dqe_debourses/{dqe}/debourses/generate-sec', [DebourseController::class, 'generateDebourseSec'])->name('debourses.generate_sec');
Route::post('/dqe_debourses/{dqe}/debourses/generate-frais-chantier', [DebourseController::class, 'generateFraisChantier'])->name('debourses.generate_frais_chantier');
Route::post('/dqe_debourses/{dqe}/debourses/generate-chantier', [DebourseChantierController::class, 'generate'])->name('debourses.generate_chantier');

Route::get('/debourses/{debourse}', [DebourseController::class, 'details'])->name('debourses.details');
Route::get('/debourses/{debourse}/show', [DebourseController::class, 'details'])->name('debourses.show');
Route::get('/debourses_export/{debourse}/export', [DebourseController::class, 'export'])->name('debourses.export');
Route::put('/debourses/{detail}/update-detail', [DebourseController::class, 'updateDetail'])->name('debourses.update_detail');

// Routes pour les déboursés chantier
Route::get('/contrats_debourses_chantier/{contrat}/debourses_chantier', [App\Http\Controllers\DebourseChantierController::class, 'index'])->name('debourses_chantier.index');
Route::post('/dqe_debourses_chantier/{dqe}/debourses_chantier/generate', [App\Http\Controllers\DebourseChantierController::class, 'generate'])->name('debourses_chantier.generate');
Route::get('/debourses_chantier/{debourseChantier}', [App\Http\Controllers\DebourseChantierController::class, 'details'])->name('debourses_chantier.details');
Route::get('/debourses_chantier_export/{debourseChantier}/export', [App\Http\Controllers\DebourseChantierController::class, 'export'])->name('debourses_chantier.export');
Route::put('/debourses_chantier/{detail}/update-detail', [App\Http\Controllers\DebourseChantierController::class, 'updateDetail'])->name('debourses_chantier.update_detail');
Route::post('/debourses_chantier/{detail}/duplicate', [App\Http\Controllers\DebourseChantierController::class, 'duplicateDetail'])->name('debourses_chantier.duplicate_detail');
Route::delete('/debourses_chantier/{detail}/delete', [App\Http\Controllers\DebourseChantierController::class, 'deleteDetail'])->name('debourses_chantier.delete_detail');

// Ajoutez ces routes à votre fichier de routes
Route::get('/dashboard/realtime-stats', 'StatistiqueController@getRealtimeStats')->name('dashboard.realtime-stats');
Route::get('/dashboard/evolution-data', 'StatistiqueController@getEvolutionData')->name('dashboard.evolution-data');
Route::post('/dashboard/export-pdf', 'StatistiqueController@exportPDF')->name('dashboard.export-pdf');

Route::get('demandes-approvisionnement/{demandeApprovisionnement}/articles', function(DemandeApprovisionnement $demandeApprovisionnement) {
    $lignes = $demandeApprovisionnement->lignes()->with('article')->get();
    return response()->json($lignes);
});

// Route pour récupérer les articles d'une demande d'achat
Route::get('demandes-achat/{demandeAchat}/articles', function(DemandeAchat $demandeAchat) {
    $lignes = $demandeAchat->lignes()->with('article')->get();
    return response()->json($lignes);
});


// Routes pour les demandes d'approvisionnement
// Routes pour les demandes d'approvisionnement
Route::resource('demande-approvisionnements', DemandeApprovisionnementController::class);
Route::post('demande-approvisionnements/{demandeApprovisionnement}/approve', 
    [DemandeApprovisionnementController::class, 'approve'])
    ->middleware('role:chef_projet,conducteur_travaux,chef_chantier,admin,dg')
    ->name('demande-approvisionnements.approve');
Route::post('demande-approvisionnements/{demandeApprovisionnement}/reject', 
    [DemandeApprovisionnementController::class, 'reject'])
    ->middleware('role:chef_projet,conducteur_travaux,chef_chantier,admin,dg')
    ->name('demande-approvisionnements.reject');
Route::get('demande-approvisionnements/{demandeApprovisionnement}/pdf', 
    [DemandeApprovisionnementController::class, 'exportPDF'])
    ->name('demande-approvisionnements.pdf');

// Routes pour les bons de commande
Route::resource('bon-commandes', BonCommandeController::class);
Route::post('bon-commandes/{bonCommande}/confirm', 
    [BonCommandeController::class, 'confirm'])
    ->middleware('role:chef_projet,conducteur_travaux,acheteur,admin,dg')
    ->name('bon-commandes.confirm');
Route::post('bon-commandes/{bonCommande}/cancel', 
    [BonCommandeController::class, 'cancel'])
    ->middleware('role:chef_projet,conducteur_travaux,acheteur,admin,dg')
    ->name('bon-commandes.cancel');
Route::post('bon-commandes/{bonCommande}/livrer', 
    [BonCommandeController::class, 'livrer'])
    ->middleware('role:magasinier,chef_chantier,admin,dg')
    ->name('bon-commandes.livrer');
Route::get('bon-commandes/{bonCommande}/pdf', 
    [BonCommandeController::class, 'exportPDF'])
    ->name('bon-commandes.pdf');

// Routes pour les réceptions
Route::resource('receptions', ReceptionController::class)->only(['index', 'show', 'create', 'store']);
Route::get('receptions/create/{bonCommande}', 
    [ReceptionController::class, 'create'])
    ->middleware('role:magasinier,chef_chantier,admin,dg')
    ->name('receptions.create');
Route::post('receptions/store/{bonCommande}', 
    [ReceptionController::class, 'store'])
    ->middleware('role:magasinier,chef_chantier,admin,dg')
    ->name('receptions.store');

// Routes pour les demandes d'achat
Route::resource('demande-achats', DemandeAchatController::class);
Route::post('demande-achats/{demandeAchat}/approve', 
    [DemandeAchatController::class, 'approve'])
    ->middleware('role:chef_projet,conducteur_travaux,acheteur,admin,dg')
    ->name('demande-achats.approve');
Route::post('demande-achats/{demandeAchat}/reject', 
    [DemandeAchatController::class, 'reject'])
    ->middleware('role:chef_projet,conducteur_travaux,acheteur,admin,dg')
    ->name('demande-achats.reject');
Route::get('demande-achats/{demandeAchat}/pdf', 
    [DemandeAchatController::class, 'exportPDF'])
    ->name('demande-achats.pdf');

// Routes pour les demandes de cotation
Route::resource('demande-cotations', DemandeCotationController::class);
Route::post('demande-cotations/{demandeCotation}/terminate', 
    [DemandeCotationController::class, 'terminate'])
    ->middleware('role:chef_projet,conducteur_travaux,acheteur,admin,dg')
    ->name('demande-cotations.terminate');
Route::post('demande-cotations/{demandeCotation}/cancel', 
    [DemandeCotationController::class, 'cancel'])
    ->middleware('role:chef_projet,conducteur_travaux,acheteur,admin,dg')
    ->name('demande-cotations.cancel');
Route::post('demande-cotations/{demandeCotation}/fournisseurs/{fournisseurDemandeCotation}/save-response', 
    [DemandeCotationController::class, 'saveFournisseurResponse'])
    ->name('demande-cotations.save-fournisseur-response');
Route::post('demande-cotations/{demandeCotation}/fournisseurs/{fournisseurDemandeCotation}/select', 
    [DemandeCotationController::class, 'selectFournisseur'])
    ->name('demande-cotations.select-fournisseur');
Route::get('demande-cotations/{demandeCotation}/pdf', 
    [DemandeCotationController::class, 'exportPDF'])
    ->name('demande-cotations.pdf');
// Route::get('/import_index', [ImportController::class, 'index'])->name('import.index');
// Route::post('/import', [ImportController::class, 'import'])->name('import.create');




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Routes pour les BPU
Route::get('/bpu', [BpuController::class, 'index'])->name('bpu.index');
Route::get('/bpu/until', [BpuController::class, 'indexuntil'])->name('bpu.indexuntil');
Route::get('/bpu/print', [BpuController::class, 'print'])->name('bpu.print');
Route::get('/bpus/create', [BpuController::class, 'create'])->name('bpus.create');
Route::post('/bpus', [BpuController::class, 'store'])->name('bpus.store');
Route::get('/bpus/{bpu}/edit', [BpuController::class, 'edit'])->name('bpus.edit');
Route::put('/bpus/{bpu}', [BpuController::class, 'update'])->name('bpus.update');
Route::delete('/bpus/{bpu}', [BpuController::class, 'destroy'])->name('bpus.destroy');
Route::post('/bpus/duplicate', [BpuController::class, 'duplicate'])->name('bpus.duplicate');
Route::post('/bpus/copy-to-contract', [BpuController::class, 'copyToContract'])->name('bpus.copyToContract');

// Routes pour les catégories BPU
Route::post('/categoriesbpu', [CategorieBpuController::class, 'store'])->name('categoriesbpu.store');
Route::put('/categoriesbpu/{categorie}', [CategorieBpuController::class, 'update'])->name('categoriesbpu.update');
Route::delete('/categoriesbpu/{categorie}', [CategorieBpuController::class, 'destroy'])->name('categoriesbpu.destroy');

// Routes pour les sous-catégories
Route::post('/souscategoriesbpu', [SousCategorieBpuController::class, 'store'])->name('souscategoriesbpu.store');
Route::put('/souscategoriesbpu/{souscategorie}', [SousCategorieBpuController::class, 'update'])->name('souscategoriesbpu.update');
Route::delete('/souscategoriesbpu/{souscategorie}', [SousCategorieBpuController::class, 'destroy'])->name('souscategoriesbpu.destroy');

// Routes pour les rubriques
Route::post('/rubriques', [RubriqueController::class, 'store'])->name('rubriques.store');
Route::put('/rubriques/{rubrique}', [RubriqueController::class, 'update'])->name('rubriques.update');
Route::delete('/rubriques/{rubrique}', [RubriqueController::class, 'destroy'])->name('rubriques.destroy');

// Routes pour l'importation
Route::get('/import', [ImportController::class, 'index'])->name('import.index');
Route::post('/import', [ImportController::class, 'create'])->name('import.create');
Route::resource('bpus', BpuController::class);

// Route::get('/bup-contrat', [BpuController::class, 'index_contrat'])->name('bpu.contrat');
// Route::get('/bup-print', [BpuController::class, 'print'])->name('bpu.print');
// Route::get('/bupn-general', [BpuController::class, 'index'])->name('bpu.index');

Route::resource('prestations', PrestationController::class);

// Routes supplémentaires pour les prestations
Route::get('/prestations/{prestation}/artisans-disponibles', [PrestationController::class, 'getArtisansDisponibles'])->name('prestations.artisans-disponibles');
Route::put('/prestations/{prestation}/affecter-artisan', [PrestationController::class, 'affecterArtisan'])->name('prestations.affecter-artisan');
Route::get('/prestations/{prestation}/details', [PrestationController::class, 'getDetails'])->name('prestations.details');
Route::post('/prestations/{prestation}/comptes', [PrestationController::class, 'ajouterCompte'])->name('prestations.ajouter-compte');
Route::get('/prestations/{prestation}/decomptes', [PrestationController::class, 'getDecomptes'])->name('prestations.decomptes');
Route::get('/prestations/{prestation}/artisan-info', [PrestationController::class, 'getArtisanInfo'])->name('prestations.artisan-info');
Route::put('/prestations/{prestation}/remplacer-artisan', [PrestationController::class, 'remplacerArtisan'])->name('prestations.remplacer-artisan');
Route::resource('factures', FactureController::class);
// Routes supplémentaires pour les factures
Route::get('/factures_statistics', [FactureController::class, 'statistics'])->name('factures.statistics');
Route::get('/factures_pdf/{facture}/pdf', [FactureController::class, 'generatePDF'])->name('factures.generatePDF');
Route::put('/factures_change/{facture}/change-status', [FactureController::class, 'changeStatus'])->name('factures.changeStatus');
Route::get('/factures/artisan/{artisan}/decomptes', [FactureController::class, 'getDecomptesArtisan'])->name('factures.decomptes.artisan');

Route::resource('documents', DocumentController::class)->except(['edit', 'update']);
Route::get('/documents_contrat', [DocumentController::class, 'index_contrat'])->name('document_contrat.index');

Route::resource('references', ReferenceController::class);
Route::resource('banques', BanqueController::class);

// Routes pour les Monnaies
Route::resource('monnaies', MonnaieController::class);

// Routes pour les Modes de Paiement
Route::resource('modes_de_paiement', ModeDePaiementController::class);

Route::get('/pays', [PaysController::class, 'index'])->name('pays.index');
Route::get('/pays/create', [PaysController::class, 'create'])->name('pays.create');
Route::post('/pays', [PaysController::class, 'store'])->name('pays.store');
Route::get('/pays/{id}/edit', [PaysController::class, 'edit'])->name('pays.edit');
Route::put('/pays/{id}', [PaysController::class, 'update'])->name('pays.update');
Route::delete('/pays/{id}', [PaysController::class, 'destroy'])->name('pays.destroy');

Route::get('/villes', [VilleController::class, 'index'])->name('villes.index');
Route::get('/villes/create', [VilleController::class, 'create'])->name('villes.create');
Route::post('/villes', [VilleController::class, 'store'])->name('villes.store');
Route::get('/villes/{id}/edit', [VilleController::class, 'edit'])->name('villes.edit');
Route::put('/villes/{id}', [VilleController::class, 'update'])->name('villes.update');
Route::delete('/villes/{id}', [VilleController::class, 'destroy'])->name('villes.destroy');

Route::get('/secteurs', [SecteurController::class, 'index'])->name('secteurs.index');
Route::get('/secteurs/create', [SecteurController::class, 'create'])->name('secteurs.create');
Route::post('/secteurs', [SecteurController::class, 'store'])->name('secteurs.store');
Route::get('/secteurs/{id}/edit', [SecteurController::class, 'edit'])->name('secteurs.edit');
Route::put('/secteurs/{id}', [SecteurController::class, 'update'])->name('secteurs.update');
Route::delete('/secteurs/{id}', [SecteurController::class, 'destroy'])->name('secteurs.destroy');

Route::get('/regime-impositions', [RegimeImpositionController::class, 'index'])->name('regime-impositions.index');
Route::get('/regime-impositions/create', [RegimeImpositionController::class, 'create'])->name('regime-impositions.create');
Route::post('/regime-impositions', [RegimeImpositionController::class, 'store'])->name('regime-impositions.store');
Route::get('/regime-impositions/{id}/edit', [RegimeImpositionController::class, 'edit'])->name('regime-impositions.edit');
Route::put('/regime-impositions/{id}', [RegimeImpositionController::class, 'update'])->name('regime-impositions.update');
Route::delete('/regime-impositions/{id}', [RegimeImpositionController::class, 'destroy'])->name('regime-impositions.destroy');

Route::get('/unite-mesures', [UniteMesureController::class, 'index'])->name('unite-mesures.index');
Route::get('/unite-mesures/create', [UniteMesureController::class, 'create'])->name('unite-mesures.create');
Route::post('/unite-mesures', [UniteMesureController::class, 'store'])->name('unite-mesures.store');
Route::get('/unite-mesures/{id}/edit', [UniteMesureController::class, 'edit'])->name('unite-mesures.edit');
Route::put('/unite-mesures/{id}', [UniteMesureController::class, 'update'])->name('unite-mesures.update');
Route::delete('/unite-mesures/{id}', [UniteMesureController::class, 'destroy'])->name('unite-mesures.destroy');

Route::get('/type-travaux', [TypeTravauxController::class, 'index'])->name('type-travaux.index');
Route::get('/type-travaux/create', [TypeTravauxController::class, 'create'])->name('type-travaux.create');
Route::post('/type-travaux', [TypeTravauxController::class, 'store'])->name('type-travaux.store');
Route::get('/type-travaux/{id}/edit', [TypeTravauxController::class, 'edit'])->name('type-travaux.edit');
Route::put('/type-travaux/{id}', [TypeTravauxController::class, 'update'])->name('type-travaux.update');
Route::delete('/type-travaux/{id}', [TypeTravauxController::class, 'destroy'])->name('type-travaux.destroy');

Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

Route::patch('/ventes/{vente}/status', [VenteController::class, 'updateStatus'])->name('ventes.updateStatus');
Route::get('/ventes/report', [VenteController::class, 'showReportForm'])->name('ventes.report.form');
Route::post('/ventes/report', [VenteController::class, 'generateReport'])->name('ventes.report.generate');
// Dans routes/web.php
Route::get('/ventes/report/pdf', [VenteController::class, 'generatePDF'])->name('ventes.report.pdf');

Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index'); // Liste des ventes
Route::get('/ventes/create', [VenteController::class, 'create'])->name('ventes.create'); // Formulaire de création
Route::post('/ventes', [VenteController::class, 'store'])->name('ventes.store'); // Enregistrer une vente
Route::get('/ventes/{vente}', [VenteController::class, 'show'])->name('ventes.show'); // Voir une vente
Route::delete('/ventes/{vente}', [VenteController::class, 'destroy'])->name('ventes.destroy'); // Supprimer une vente
Route::get('/api/devis/client/{clientId}', [VenteController::class, 'getDevisForClient'])->name('api.devis.client');

// Routes pour les devis
Route::resource('devis', DevisController::class);
Route::get('/api/devis/client/{clientId}', [DevisController::class, 'getDevisForClient'])->name('api.devis.client.controller');

Route::prefix('caisse/')->group(function () {
    Route::get('brouillard', [CaisseController::class, 'showBrouillardCaisse'])->name('caisse.brouillard');
    Route::get('approvisionnement', [CaisseController::class, 'showApprovisionnementForm'])->name('caisse.approvisionnement');
    Route::post('saisir-depense', [CaisseController::class, 'saisirDepense'])->name('caisse.saisirDepense');
    Route::post('approvisionner', [CaisseController::class, 'approvisionnerCaisse'])->name('caisse.approvisionnerCaisse');
    Route::post('demander-depense', [CaisseController::class, 'demandeDepense'])->name('caisse.demandeDepense');
    Route::post('valider-demande/{demandeId}', [CaisseController::class, 'validerDemandeDepense'])
    ->middleware('role:caissier,chef_projet,conducteur_travaux,admin,dg')
    ->name('caisse.validerDemandeDepense');
Route::post('annuler-demande/{demandeId}', [CaisseController::class, 'annulerDemandeDepense'])
    ->middleware('role:caissier,chef_projet,conducteur_travaux,admin,dg')
    ->name('caisse.annulerDemandeDepense');
    Route::get('voir-demande-depense-pdf/{demandeId}', [CaisseController::class, 'voirDemandeDepensePDF'])->name('caisse.voirDemandeDepensePDF');
    Route::post('approuver-responsable/{demandeId}', [CaisseController::class, 'approuverParResponsable'])
        ->middleware('role:chef_projet,conducteur_travaux,admin,dg')
        ->name('caisse.approuverParResponsable');
    Route::post('approuver-raf/{demandeId}', [CaisseController::class, 'approuverParRAF'])
        ->middleware('role:admin,dg')
        ->name('caisse.approuverParRAF');
    Route::get('demandes-en-attente', [CaisseController::class, 'demandesEnAttente'])->name('caisse.demandesEnAttente');
});
Route::get('/demande-depense', [CaisseController::class, 'listerDemandesDepenses'])->name('caisse.demande-liste');

Route::get('transferts', [TransfertsStockController::class, 'index'])->name('transferts.index');
Route::post('transferts', [TransfertsStockController::class, 'store'])->name('transferts.store');

Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
Route::get('articles_show/{article}/', [ArticleController::class, 'show'])->name('articles.show');
Route::get('articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
Route::put('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
Route::get('articles/{article}/stock-details', [ArticleController::class, 'stockDetails'])->name('articles.stock-details');

Route::prefix('stock')->group
(function() {
    Route::get('/', [StockProjetController::class, 'index'])->name('stock.index');
    Route::get('/create', [StockProjetController::class, 'create'])->name('stock.create');
    Route::post('/', [StockProjetController::class, 'store'])->name('stock.store');
    Route::get('/{id}', [StockProjetController::class, 'show'])->name('stock.show');
    Route::get('/{id}/edit', [StockProjetController::class, 'edit'])->name('stock.edit');
    Route::put('/{id}', [StockProjetController::class, 'update'])->name('stock.update');
    Route::delete('/{id}', [StockProjetController::class, 'destroy'])->name('stock.destroy');
});
Route::prefix('stock_contrat')->group(function() {
    Route::get('/', [StockProjetController::class, 'index_contrat'])->name('stock_contrat.index');
    Route::get('/create', [StockProjetController::class, 'create_contrat'])->name('stock_contrat.create');
    Route::get('/historique', [StockProjetController::class, 'historique_contrat'])->name('stock_contrat.historique');
     Route::get('/historique-complet', [StockProjetController::class, 'historiqueComplet'])->name('stock_contrat.historique_complet');
    Route::post('/', [StockProjetController::class, 'store_contrat'])->name('stock_contrat.store');
    Route::post('/livraison', [StockProjetController::class, 'livraison'])->name('stock_contrat.livraison');
    Route::post('/retour-chantier', [StockProjetController::class, 'retourChantier'])->name('stock_contrat.retour_chantier');
    Route::post('/retour-projet', [StockProjetController::class, 'retourProjet'])->name('stock_contrat.retour_projet');
    Route::get('/{id}', [StockProjetController::class, 'show_contrat'])->name('stock_contrat.show');
    Route::get('/{id}/edit', [StockProjetController::class, 'edit_contrat'])->name('stock_contrat.edit');
    Route::put('/{id}', [StockProjetController::class, 'update_contrat'])->name('stock_contrat.update');
    Route::delete('/{id}', [StockProjetController::class, 'destroy_contrat'])->name('stock_contrat.destroy');
});

Route::prefix('contrats')->group(function() {
    Route::get('/', [ContratController::class, 'index'])->name('contrats.index');
    Route::get('/all', [ContratController::class, 'allContracts'])->name('contrats.all');
    Route::get('/allcreate', [ContratController::class, 'allCreate'])->name('contrats.allcreate');
    Route::get('create', [ContratController::class, 'create'])->name('contrats.create');
    Route::post('store', [ContratController::class, 'store'])->name('contrats.store');
    Route::get('edit/{id}', [ContratController::class, 'edit'])->name('contrats.edit');
    Route::put('update/{id}', [ContratController::class, 'update'])->name('contrats.update');
    Route::delete('destroy/{id}', [ContratController::class, 'destroy'])->name('contrats.destroy');
    Route::post('duplicate/{id}', [ContratController::class, 'duplicate'])->name('contrats.duplicate');
    Route::get('projet/{projetId}/clients', [ContratController::class, 'getClientsByProject'])->name('contrats.clients-by-project');
    Route::get('{id}', [ContratController::class, 'show'])->name('contrats.show');
});
// Route::get('/contrats/{id}', [ContratController::class, 'show'])->name('contrats.show');

Route::get('/config-global', [ConfigGlobalController::class, 'index'])->name('config-global.index');
Route::get('/config-global/create', [ConfigGlobalController::class, 'create'])->name('config-global.create');
Route::post('/config-global', [ConfigGlobalController::class, 'store'])->name('config-global.store');
Route::get('/config-global/{configGlobal}/edit', [ConfigGlobalController::class, 'edit'])->name('config-global.edit');
Route::put('/config-global/{configGlobal}', [ConfigGlobalController::class, 'update'])->name('config-global.update');
Route::delete('/config-global/{configGlobal}', [ConfigGlobalController::class, 'destroy'])->name('config-global.destroy');

Route::get('/corpsmetiers', [CorpsMetierController::class, 'index'])->name('corpsmetiers.index');
Route::get('/corpsmetiers/create', [CorpsMetierController::class, 'create'])->name('corpsmetiers.create');
Route::post('/corpsmetiers', [CorpsMetierController::class, 'store'])->name('corpsmetiers.store');
Route::get('/corpsmetiers/{id}/edit', [CorpsMetierController::class, 'edit'])->name('corpsmetiers.edit');
Route::put('/corpsmetiers/{id}', [CorpsMetierController::class, 'update'])->name('corpsmetiers.update');
Route::delete('/corpsmetiers/{id}', [CorpsMetierController::class, 'destroy'])->name('corpsmetiers.destroy');

Route::get('/artisans', [ArtisanController::class, 'index'])->name('artisans.index'); // 🏠 Voir tous les artisans
Route::get('/artisans/create', [ArtisanController::class, 'create'])->name('artisans.create'); // ➕ Formulaire d'ajout
Route::post('/artisans', [ArtisanController::class, 'store'])->name('artisans.store'); // ✅ Ajouter un artisan
Route::get('/artisans/{id}/edit', [ArtisanController::class, 'edit'])->name('artisans.edit'); // ✏️ Modifier un artisan
Route::put('/artisans/{id}', [ArtisanController::class, 'update'])->name('artisans.update'); // 🔄 Sauvegarder modification
Route::delete('/artisans/{id}', [ArtisanController::class, 'destroy'])->name('artisans.destroy'); // ❌ Supprimer

Route::get('fournisseurs', [FournisseurController::class, 'index'])->name('fournisseurs.index');
Route::get('fournisseurs/create', [FournisseurController::class, 'create'])->name('fournisseurs.create');
Route::post('fournisseurs', [FournisseurController::class, 'store'])->name('fournisseurs.store');
Route::get('fournisseurs/{id}', [FournisseurController::class, 'show'])->name('fournisseurs.show');
Route::get('fournisseurs/{fournisseur}/edit', [FournisseurController::class, 'edit'])->name('fournisseurs.edit');
Route::put('fournisseurs/{fournisseur}', [FournisseurController::class, 'update'])->name('fournisseurs.update');
Route::delete('fournisseurs/{fournisseur}', [FournisseurController::class, 'destroy'])->name('fournisseurs.destroy');

Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
Route::get('clients/{id}', [ClientController::class, 'show'])->name('clients.show');
Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

// Afficher la liste des projets
Route::get('/projets', [ProjetController::class, 'index'])->name('projets.index');
// Afficher le formulaire de création
Route::get('/projets/create', [ProjetController::class, 'create'])->name('projets.create');
// Enregistrer un nouveau projet
Route::post('/projets', [ProjetController::class, 'store'])->name('projets.store');
// Afficher un projet spécifique
Route::get('/projets/{projet}', [ProjetController::class, 'show'])->name('projets.show');
// Afficher le formulaire de modification
Route::get('/projets/{projet}/edit', [ProjetController::class, 'edit'])->name('projets.edit');
// Mettre à jour un projet
Route::put('/projets/{projet}', [ProjetController::class, 'update'])->name('projets.update');
// Supprimer un projet
Route::delete('/projets/{projet}', [ProjetController::class, 'destroy'])->name('projets.destroy');
// Changer le projet en session
Route::post('/projets/change-project', [ProjetController::class, 'changeProject'])->name('projets.change');
// Sélectionner un projet pour créer un contrat
Route::post('/projets/select-for-contract', [ProjetController::class, 'selectForContract'])->name('projets.select-for-contract');

Route::get('sous_categories', [SousCategorieController::class, 'index'])->name('sous_categories.index'); // Liste des sous-catégories
Route::get('sous_categories/create', [SousCategorieController::class, 'create'])->name('sous_categories.create'); // Formulaire de création
Route::post('sous_categories', [SousCategorieController::class, 'store'])->name('sous_categories.store'); // Sauvegarder une sous-catégorie
Route::delete('sous_categories/{sousCategorie}', [SousCategorieController::class, 'destroy'])->name('sous_categories.destroy'); // Supprimer une sous-catégorie

Route::get('categories', [CategoryController::class, 'index'])->name('categories.index'); // Liste des catégories
Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create'); // Formulaire de création
Route::post('categories', [CategoryController::class, 'store'])->name('categories.store'); // Sauvegarder une catégorie
Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // Supprimer une catégorie


Route::get('secteur_activites', [SecteurActiviteController::class, 'index'])->name('secteur_activites.index'); // Lister les secteurs
Route::get('secteur_activites/create', [SecteurActiviteController::class, 'create'])->name('secteur_activites.create'); // Afficher le formulaire de création
Route::post('secteur_activites', [SecteurActiviteController::class, 'store'])->name('secteur_activites.store'); // Enregistrer un secteur
Route::get('secteur_activites/{secteur}/edit', [SecteurActiviteController::class, 'edit'])->name('secteur_activites.edit'); // Afficher le formulaire d'édition
Route::put('secteur_activites/{secteur}', [SecteurActiviteController::class, 'update'])->name('secteur_activites.update'); // Mettre à jour un secteur
Route::delete('secteur_activites/{secteur}', [SecteurActiviteController::class, 'destroy'])->name('secteur_activites.destroy'); // Supprimer un secteur

Route::get('bu', [BUController::class, 'index'])->name('bu.index'); // Lister tous les BUs
Route::get('bu/create', [BUController::class, 'create'])->name('bu.create'); // Formulaire de création
Route::post('bu', [BUController::class, 'store'])->name('bu.store'); // Enregistrer un BU
Route::get('bu/{bu}/edit', [BUController::class, 'edit'])->name('bu.edit'); // Formulaire d'édition
Route::put('bu/{bu}', [BUController::class, 'update'])->name('bu.update'); // Mettre à jour un BU
Route::delete('bu/{bu}', [BUController::class, 'destroy'])->name('bu.destroy'); // Supprimer un BU

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/select-bu', [AuthController::class, 'showSelectBU'])->name('select.bu')->middleware('auth');
Route::post('/select-bu', [AuthController::class, 'selectBU'])->name('select.bu.post')->middleware('auth');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');



 Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    
    // Modifier le profil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Modifier le mot de passe
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.edit-password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    
    // Supprimer la photo de profil
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard')->middleware('auth');
Route::get('/utilaire', function () {
    return view('until.index');
})->name('until')->middleware('auth');

Route::get('/utilaire', function () {
    return view('until.index');
})->name('until')->middleware('auth');
Route::get('/sublayouts_until', function () {
    return view('sublayouts.until');
})->name('sublayouts_until')->middleware('auth');
Route::get('/sublayouts_article', function () {
    return view('sublayouts.article');
})->name('sublayouts_article')->middleware('auth');
Route::get('/sublayouts_bu', function () {
    return view('sublayouts.bu');
})->name('sublayouts_bu')->middleware('auth');
use App\Models\BU;
use App\Models\BrouillardCaisse;

Route::get('/sublayouts_caisse', function () {
    $id_bu = session('selected_bu');
    
    if (!$id_bu) {
        return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
    }
    
    $bus = BU::find($id_bu);
    $brouillardCaisse = BrouillardCaisse::where('bus_id', $id_bu)->orderBy('created_at', 'desc')->get();
    
    return view('sublayouts.caisse', compact('bus', 'brouillardCaisse'));
})->name('sublayouts_caisse')->middleware('auth');

Route::get('/sublayouts_clientfournisseur', function () {
    return view('sublayouts.clientfournisseur');
})->name('sublayouts_clientfournisseur')->middleware('auth');
Route::get('/sublayouts_contrat', function () {
    return view('sublayouts.contrat');
})->name('sublayouts_contrat')->middleware('auth');

Route::get('/sublayouts_projet', function () {
    return view('sublayouts.projet');
})->name('sublayouts_projet')->middleware('auth');

Route::get('/sublayouts_projetdetail', function () {
    $projets = \App\Models\Projet::all();
    $articles = \App\Models\Article::all();
    return view('sublayouts.projetdetail', compact('projets', 'articles'));
})->name('sublayouts_projetdetail')->middleware('auth');

Route::get('/sublayouts_user', function () {
    return view('sublayouts.user');
})->name('sublayouts_user')->middleware('auth');

Route::get('/sublayouts_vente', function () {
    return view('sublayouts.vente');
})->name('sublayouts_vente')->middleware('auth');

// Routes pour les demandes de ravitaillement
Route::middleware('auth')->group(function () {
    Route::resource('demandes-ravitaillement', DemandeRavitaillementController::class);
    Route::post('demandes-ravitaillement/{demandeRavitaillement}/approuver', [DemandeRavitaillementController::class, 'approuver'])->name('demandes-ravitaillement.approuver');
    Route::post('demandes-ravitaillement/{demandeRavitaillement}/rejeter', [DemandeRavitaillementController::class, 'rejeter'])->name('demandes-ravitaillement.rejeter');
    Route::post('demandes-ravitaillement/{demandeRavitaillement}/marquer-livree', [DemandeRavitaillementController::class, 'marquerLivree'])->name('demandes-ravitaillement.marquer-livree');
});

Route::get('/', function () {
    return view('auth.login');
});




