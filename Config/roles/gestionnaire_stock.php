<?php
return [
                //user
                    //'users.index',
                    //'users.show',
                    // 'users.create',
                    // 'users.store',
                    // 'users.edit',
                    // 'users.update',
                    // 'users.destroy',

                //contrats 
                    'contrats.index',
                    'contrats.export.pdf',
                    'contrats.all',
                    'contrats.allcreate',
                   // 'contrats.create',
                    'contrats.store',
                   //'contrats.update-statut',
                  //  'contrats.edit',
                    'contrats.update',
                   // 'contrats.destroy',
                    'contrats.duplicate',
                    'contrats.clients-by-project',
                    'contrats.show',


                //clients
                    'clients.view',
                    //'clients.create',
                    //'clients.update',
                    //'clients.export.pdf',
                    //'clients.show',
                    //'clients.store',
                    //'clients.edit',
                    //'clients.index',
                   // 'clients.destroy',
                    

                //fournisseur
                    'fournisseurs.index', 
                    'fournisseurs.export.pdf',
                  //  'fournisseurs.create',
                  //  'fournisseurs.store',
                    'fournisseurs.show',
                  //  'fournisseurs.edit',
                  //  'fournisseurs.update',
                    //'fournisseurs.destroy',

                //-- Artisan
                    'artisans.index',
                    'artisans.export.pdf',
                   // 'artisans.create',
                   // 'artisans.store',
                    'artisans.show',
                   // 'artisans.edit',
                   // 'artisans.update',
                   // 'artisans.destroy',
                    'prestations.artisans-disponibles',
                    'prestations.affecter-artisan',
                    'prestations.artisan-info',
                    'prestations.remplacer-artisan',
                    'factures.decomptes.artisan',

                    //projet -------
                    'projets.index',
                    'projets.export.pdf',
                    //'projets.create',
                    'projets.store',
                    'projets.show',
                    //'projets.edit',
                    //'projets.update',
                    //'projets.update-statut',
                   // 'projets.destroy',
                    'projets.change',
                    'projets.select-for-contract',



                //Document
                    'prestations.document',
                    'documents.export.pdf',
                    'document_contrat.index',
                    'documents.create',
                    //'documents.destroy',
                    'documents.edit',

                //CORPS DE METIER
                    'corpsmetiers.index',
                    //'corpsmetiers.create',
                    //'corpsmetiers.store',
                    //'corpsmetiers.edit',
                    //'corpsmetiers.update',
                    //'corpsmetiers.destroy',
                
                //-- Banque
                    'banques.index',
                    //'banques.create',
                    //'banques.store',
                    //'banques.show',
                    //'banques.edit',
                    //'banques.update',
                    //'banques.destroy',

                //UNITE DE MESURE
                    'unite-mesures.index',
                    //'unite-mesures.create',
                    //'unite-mesures.store',
                    //'unite-mesures.edit',
                    //'unite-mesures.update',
                    //'unite-mesures.destroy',
            
                //BPU
                    'bpu.index',
                    'bpu.indexuntil',
                    'bpu.print',
                    'bpu.export.excel',
                    'bpu.export.pdf',
                    //'bpus.create',
                    //'bpus.store',
                    //'bpus.edit',
                    //'bpus.update',
                    //'bpus.destroy',
                    //'bpus.duplicate',
                    //'bpus.copyToContract',

                //CATEGORIE
                    'categories.index',
                    //'categories.create',
                    //'categories.store',
                    //'categories.destroy',

                    //SOUS-CATEGORIE
                    'sous_categories.index',
                   // 'sous_categories.create',
                   // 'sous_categories.edit',
                   // 'sous_categories.store',
                    //'sous_categories.destroy',

                //BU
                    'bu.index',
                    'bu.show',
                    //'bu.create',
                    //'bu.store',
                    //'bu.edit',
                    //'bu.update',
                    //'bu.destroy',

                //-- article
                    'articles.index',
                    'articles.export.pdf',
                    //'articles.create',
                   // 'articles.store',
                    'articles.show',
                   // 'articles.edit',
                   // 'articles.update',
                    //'articles.destroy',
                    'articles.stock-details',
                    'articles.search',
                    'demandes-achat.articles',

                //REGIME D'IMPOSITION
                    'regime-impositions.index',
                    //'regime-impositions.create',
                    //'regime-impositions.store',
                    //'regime-impositions.edit',
                    //'regime-impositions.update',
                    //'regime-impositions.destroy',

                //types de Travaux
                    'type-travaux.index',
                    //'type-travaux.create',
                    //'type-travaux.store',
                    //'type-travaux.edit',
                    //'type-travaux.update',
                    //'type-travaux.destroy',

                //secteur d'activité
                    'secteur_activites.index',
                    //'secteur_activites.create',
                    //'secteur_activites.store',
                    //'secteur_activites.edit',
                    //'secteur_activites.update',
                    //'secteur_activites.destroy',

                    
                //-- stock
                    'transferts.stock_projet',
                    'transferts.edit',
                    'transferts.create',
                    'stock.export.pdf',
                    'stock.index',
                    'stock.create',
                    'stock.store',
                    'stock.show',
                    'stock.edit',
                    'stock.update',
                    //'stock.destroy',
                //------ achat
                    'demande-achats.pdf',
                    //'demande-achats.show',
                    //'demande-achats.create',
                    //'demande-achats.edit',
                    //'demande-achats.approuver',
                    //'demande-achats.destroy',
                    'demande-achats.reject',
                    'demande-cotations.export.pdf',
                    'demande-achats.export.pdf',
                    'bon-commandes.demande-achat',

                //------ bon de commande
                    //'bon-commandes.demande-achat',
                    //'bon-commandes.confirm',
                    //'bon-commandes.edit',
                    //'bon-commandes.create',
                    //'bon-commandes.destroy',
                    //'bon-commandes.cancel',
                    'bon-commandes.livrer',
                    'bon-commandes.pdf',
                    'bon-commandes.show',

                //------ facture d'achat
                    'factures.export.pdf',
                    'facture-decompte.show',
                    'facture-decompte.valider',
                    'facture-decompte.print',
                    'factures.statistics',
                    'factures.generatePDF',
                    'factures.changeStatus',
                    'factures.decomptes.artisan',
                    'ventes.facture',

                //------ reception de commande
                    //'receptions.export.pdf',
                    //'receptions.bon-livraison.pdf',
                    //'receptions.history',
                    //'receptions.create',
                    //'receptions.non-conformite.create',
                    //'receptions.store',
                    //'receptions.non-conformite.store',
                    'receptions.show',
                    
                //TRESORERIE -------
                //-- caisse
                    'caisse.brouillard',
                    //'caisse.brouillard.show',
                    'caisse.depense.create',
                    //'caisse.depense.approuver',
                    //'caisse.depense.destroy',
                    'caisse.brouillard.destroy',
                    'caisse.brouillard.update',
                    'caisse.brouillard.remiseAZero',
                    'caisse.approvisionnement',
                    'caisse.saisirDepense',
                    'caisse.approvisionnerCaisse',
                    'caisse.demandeDepense',
                    'caisse.enregistrerDepensesEnCaisse',
                    'caisse.annulerDemandeDepense',
                    'caisse.voirDemandeDepensePDF',
                    'caisse.approuverParResponsable',
                    'caisse.approuverParRAF',
                    'caisse.demandesEnAttente',
                    'caisse.demande-liste',
                                    //-- Buget
                    'bu-budget.index',
                    'bu-budget.store',
                    'bu-budget.show',
                    'bu-budget.rows.store',
                    'bu-budget.rows.update',
                    'bu-budget.rows.delete',
                    'bu-budget.rows.seuil.commentaire',

                    'banque.mouvements.export.pdf',
                    'banque.mouvements.index',
                    'banque.mouvements.create',
                    'banque.mouvements.store',
                    'banque.mouvements.edit',
                    'banque.mouvements.update',
                    'banque.mouvements.destroy',
                    'banque.soldes.index',
                    'banque.rapprochement.index',
                    'banque.rapprochement.toggle',

                //TRESORERIE -------
                //-- DQE
                    'debourse-sec.generate',
                    'debourse-sec.index',
                    'frais-chantier.generate',
                    'frais-chantier.index',
                    'frais-generaux.generate',
                    'frais-generaux.index',
                    'ligne-benefice.index',

                    'dqe.index',
                    'dqe.create',
                    'dqe.store',
                    'dqe.show',
                    'dqe.edit',
                    'dqe.update',
                    'dqe.destroy',
                    'dqe.valider',
                    'dqe.rejeter',
                    'dqe.soumettre',
                    'dqe.approuver',
                    'dqe.generate',
                    'debourse-chantier.generate',
                    'dqe.lines.add',
                    'dqe.lines.addMultiple',
                    'dqe.lines.update',
                    'dqe.lines.delete',
                    'dqe.sections.create',

                    'dqe.categories.store',
                    'dqe.sous-categories.store',
                    'dqe.rubriques.store',
                    'dqe.categories.get',
                    'dqe.sous-categories.get',
                    'dqe.rubriques.get',
                    'dqe.categories.update',
                    'dqe.categories.delete',
                    'dqe.sous-categories.update',
                    'dqe.sous-categories.delete',
                    'dqe.rubriques.update',
                    'dqe.rubriques.delete',
                    'dqe.lignes.get',
                    'dqe.lignes.show',
                    'dqe.lignes.edit',
                    'dqe.lignes.store',
                    'dqe.lignes.update',
                    'dqe.lignes.delete',
                    'dqe.lignes.depuis-bpu',

                //stock contrat
                    'stock.contrat.create',
                    'stock.historique.show',
                    'stock.allhistorique.show',
                    'stock.details.show',
                    'stock.transfert',
                    'stock.livraison.show',

                //planning 
                    'planning.create',
                    'planning.store',
                    'planning.categorie.create',

                //ravitaillement
                //------ demande de ravitaillement
                    'demandes-ravitaillement.create',
                    'demandes-ravitaillement.edit',
                    'demandes-ravitaillement.approuver',
                    'demandes-ravitaillement.rejeter',
                    'demandes-ravitaillement.marquer-livree',
                    'demandes-ravitaillement.livrer',
                    'demandes-ravitaillement.receptionner',
                    'demandes-ravitaillement.valider-retour',
                    'demandes-ravitaillement.destroy',

                //facture
                    'facture.index',
                    'facture.create',
                    'facture.show',
                    'facture.destroy',

                // facture contrat
                    'facture-contrat.generate',
                    'facture-contrat.export.pdf',
                    'facture-contrat.index',
                    'facture-contrat.create',
                    'facture-contrat.show',
                    'facture-contrat.destroy',
                    'facture-contrat.decompte.create',
                    'facture-contrat.decompte.store',

                //presation
                    'prestations.create',
                    'prestations.affecte',
                    'prestations.edit',
                    'prestations.generate',
                    'prestations.remplacer',
                    'prestations.destroy',

                //vente
                    //'ventes.index', //liste des ventes 
                    //'ventes.create', //creation
                    //'ventes.store', //enregistrer une vente
                    //'ventes.show', //voir une vente
                    //'ventes.devis.show',
                    //'ventes.valider',
                    //'ventes.facture', //facture d'une vente
                    //'ventes.bon-livraison-client.pdf',
                    //'ventes.destroy', //supprimer une vente
                    //'ventes.updateStatus', //mise a jour status d'une vente
                    //'ventes.report.form',
                    //'ventes.report.generate',
                    //'ventes.report.pdf',
                    //'ventes.export.pdf',


                //------ approvisionement
                    'demandes-approvisionnement.articles',
                    'demande-approvisionnements.export.pdf',
                    //'demande-approvisionnements.approve',
                    'demande-approvisionnements.reject',
                    'demande-approvisionnements.pdf',
                    'demande-approvisionnements.create',
                    'demande-approvisionnements.store',
                    'demande-approvisionnements.show',
                    'demande-approvisionnements.edit',
                    //'demande-approvisionnements.approuver',
                    //'demande-approvisionnements.destroy',
                    'demande-commande.store', // creer un bon au niveau demande d'approvisionnements
                    'commande-bon.create',

                //------ cotations
                    'demande-cotasion.create',
                    'demande-cotasion.edit',
                    'demande-cotations.terminate',
                    'demande-cotations.destroy',
                    //'demande-cotations.show',
                    'demande-cotations.download',
                    'demande-cotations.cancel',
                    'demande-cotations.save-fournisseur-response',
                    'demande-cotations.fournisseur-devis',
                    'demande-cotations.upload-fournisseur-devis',
                    'demande-cotations.delete-fournisseur-devis',
                    'demande-cotations.select-fournisseur',
                    'demande-cotations.pdf',
                    'demandes-cotation.articles',




                //-- Frais de chantier
                    'frais-chantier.generate',
                    'frais-chantier.index',
                    'frais-chantier.create',
                    'contrats.frais-chantier.index',
                    'contrats.frais-chantier.store',
                    'contrats.frais-chantier.show',
                    'contrats.frais-chantier.parent.show',
                    'contrats.frais-chantier.lignes.store',
                    'contrats.debourse-chantier.index',
                    'contrats.debourse-chantier.show',
                    'contrats.debourse-chantier.parent.show',
                    'contrats.debourse-chantier.lignes.store',
                    'contrats.debourse-chantier.lignes.destroy',
                    'frais-chantier.generate',
                    'frais-chantier.index',

                    //Localisation
                    'taches.storeLocalisation',
                    'taches.getLocalisationsByNiveau',
                    'taches.getCorpsDeMetiersByLocalisation',

                //villes
                    //'villes.create',
                    //'villes.destroy',
                    //'villes.edit',

                //quartiers
                    //'quartiers.create',
                    //'quartiers.destroy',
                    //'quartiers.edit',

                //module budget
                    //'module.budget.show',
                ];