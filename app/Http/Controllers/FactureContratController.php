<?php

namespace App\Http\Controllers;

use App\Models\DQE;
use App\Models\FactureContrat;
use App\Models\FactureDecompte;
use App\Models\FactureDecompteLigne;
use Illuminate\Http\Request;

class FactureContratController extends Controller
{
    /**
     * Afficher la liste des factures contrat
     */
    public function index()
    {
        $facturesContrat = FactureContrat::with(['dqe.contrat'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('facture-contrat.index', compact('facturesContrat'));
    }

    /**
     * Afficher les détails d'une facture contrat
     */
    public function show($id)
    {
        $factureContrat = FactureContrat::with(['dqe.contrat', 'facturesDecompte'])->findOrFail($id);
        
        // Organiser les lignes DQE par hiérarchie si le DQE existe
        $lignesOrganisees = [];
        if ($factureContrat->dqe) {
            $lignesOrganisees = $this->organiserLignesParHierarchie($factureContrat->dqe->lignes, $factureContrat->dqe->id);
        }
        
        return view('facture-contrat.show', compact('factureContrat', 'lignesOrganisees'));
    }

    /**
     * Supprimer une facture contrat
     */
    public function destroy($id)
    {
        try {
            $factureContrat = FactureContrat::findOrFail($id);
            $factureContrat->delete();
            
            return redirect()->route('facture-contrat.index')
                ->with('success', 'Facture contrat supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('facture-contrat.index')
                ->with('error', 'Erreur lors de la suppression de la facture contrat: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de création de facture de décompte
     */
    public function createDecompte($id)
    {
        $factureContrat = FactureContrat::with(['dqe.lignes'])->findOrFail($id);
        
        // Organiser les lignes DQE par hiérarchie
        $lignesOrganisees = [];
        if ($factureContrat->dqe) {
            $lignesOrganisees = $this->organiserLignesParHierarchie($factureContrat->dqe->lignes, $factureContrat->dqe->id);
        }
        
        return view('facture-contrat.create-decompte', compact('factureContrat', 'lignesOrganisees'));
    }

    /**
     * Enregistrer une nouvelle facture de décompte
     */
    public function storeDecompte(Request $request, $id)
    {
        $request->validate([
            'date_facture' => 'required|date',
            'pourcentage_avancement' => 'required|numeric|min:0|max:100',
            'observations' => 'nullable|string',
            'lignes' => 'required|array',
            'lignes.*.pourcentage_realise' => 'required|numeric|min:0|max:100',
            'lignes.*.quantite_realisee' => 'required|numeric|min:0',
        ]);

        // Validation supplémentaire: vérifier que le cumul des pourcentages ne dépasse pas 100%
        $factureContrat = FactureContrat::with(['facturesDecompte.lignes'])->findOrFail($id);
        
        foreach ($request->lignes as $ligneId => $ligneData) {
            if ($ligneData['pourcentage_realise'] > 0) {
                // Calculer le cumul existant pour cette ligne DQE
                $cumulExistant = 0;
                foreach ($factureContrat->facturesDecompte as $decompte) {
                    if ($decompte->statut === 'valide') {
                        foreach ($decompte->lignes as $ligneDecompte) {
                            if ($ligneDecompte->dqe_ligne_id == $ligneId) {
                                $cumulExistant += $ligneDecompte->pourcentage_realise;
                            }
                        }
                    }
                }
                
                // Vérifier que le nouveau pourcentage ne fait pas dépasser 100%
                if (($cumulExistant + $ligneData['pourcentage_realise']) > 100) {
                    $dqeLigne = \App\Models\DQELigne::find($ligneId);
                    return redirect()->back()
                        ->with('error', "Le pourcentage de réalisation pour la ligne '{$dqeLigne->code} - {$dqeLigne->designation}' ne peut pas dépasser " . number_format(100 - $cumulExistant, 2) . "% (cumul existant: " . number_format($cumulExistant, 2) . "%)");
                }
            }
        }

        $factureContrat = FactureContrat::findOrFail($id);
        
        // Journaliser les données reçues
        \Log::info('Tentative de création de facture de décompte', [
            'facture_contrat_id' => $id,
            'donnees_reçues' => $request->all(),
            'utilisateur' => auth()->user()->id
        ]);
        
        try {
            // Générer le numéro de facture
            $derniereFacture = FactureDecompte::orderBy('id', 'desc')->first();
            $numero = 'FD-' . date('Y') . '-' . str_pad(($derniereFacture ? $derniereFacture->id + 1 : 1), 4, '0', STR_PAD_LEFT);
            
            // Calculer le montant total HT
            $montantTotalHT = 0;
            $lignesData = [];
            
            foreach ($request->lignes as $ligneId => $ligneData) {
                $dqeLigne = \App\Models\DQELigne::find($ligneId);
                if ($dqeLigne && $ligneData['pourcentage_realise'] > 0) {
                    $quantiteRealisee = $dqeLigne->quantite * ($ligneData['pourcentage_realise'] / 100);
                    $montantHT = $dqeLigne->pu_ht * $quantiteRealisee;
                    $montantTotalHT += $montantHT;
                    
                    $lignesData[] = [
                        'dqe_ligne_id' => $ligneId,
                        'quantite_realisee' => $quantiteRealisee,
                        'pourcentage_realise' => $ligneData['pourcentage_realise'],
                        'montant_ht' => $montantHT,
                        'observations' => $ligneData['observations'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            // Créer la facture de décompte
            $pourcentageAvancement = $request->pourcentage_avancement ?? 0;
            \Log::info('Création facture de décompte - Pourcentage avancement', [
                'pourcentage_avancement_recu' => $request->pourcentage_avancement,
                'pourcentage_avancement_final' => $pourcentageAvancement
            ]);
            
            \Log::info('Création de la facture de décompte', [
                'montant_total_ht' => $montantTotalHT,
                'nombre_lignes' => count($lignesData),
                'pourcentage_avancement' => $pourcentageAvancement
            ]);
            
            $factureDecompte = FactureDecompte::create([
                'facture_contrat_id' => $factureContrat->id,
                'numero' => $numero,
                'date_facture' => $request->date_facture,
                'pourcentage_avancement' => $pourcentageAvancement,
                'montant_ht' => $montantTotalHT,
                'montant_ttc' => $montantTotalHT * 1.18, // TVA 18%
                'statut' => 'brouillon',
                'observations' => $request->observations,
            ]);
            
            // Créer les lignes de la facture
            foreach ($lignesData as $ligneData) {
                $ligneData['facture_decompte_id'] = $factureDecompte->id;
                FactureDecompteLigne::create($ligneData);
            }
            
            \Log::info('Facture de décompte créée avec succès', [
                'facture_decompte_id' => $factureDecompte->id,
                'numero' => $numero,
                'nombre_lignes_creees' => count($lignesData)
            ]);
            
            // Mettre à jour le montant versé du contrat
            $factureContrat->mettreAJourMontantVerse();
            $factureContrat->refresh(); // Rafraîchir le modèle pour obtenir les dernières valeurs
            \Log::info('Montant versé mis à jour après création de facture de décompte', [
                'facture_contrat_id' => $factureContrat->id,
                'nouveau_montant_verse' => $factureContrat->montant_verse,
                'montant_total_decompte' => $montantTotalHT,
                'montant_total_contrat' => $factureContrat->montant_a_payer
            ]);
            
            return redirect()->route('facture-contrat.show', $factureContrat->id)
                ->with('success', 'Facture de décompte créée avec succès. Numéro: ' . $numero);
                
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la facture de décompte', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'facture_contrat_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la facture de décompte: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher une facture de décompte
     */
    public function showDecompte($id)
    {
        $factureDecompte = FactureDecompte::with(['factureContrat.dqe.contrat', 'lignes.dqeLigne'])->findOrFail($id);
        
        return view('facture-contrat.show-decompte', compact('factureDecompte'));
    }

    /**
     * Valider une facture de décompte
     */
    public function validerDecompte($id)
    {
        try {
            $factureDecompte = FactureDecompte::findOrFail($id);
            $factureDecompte->update(['statut' => 'valide']);
            
            // Mettre à jour le montant versé dans le facture contrat
            $factureContrat = $factureDecompte->factureContrat;
            $factureContrat->mettreAJourMontantVerse();
            $factureContrat->refresh(); // Rafraîchir le modèle pour obtenir les dernières valeurs
            
            \Log::info('Facture de décompte validée et montant versé mis à jour', [
                'facture_decompte_id' => $id,
                'facture_contrat_id' => $factureContrat->id,
                'nouveau_montant_verse' => $factureContrat->montant_verse,
                'montant_decompte' => $factureDecompte->montant_ht
            ]);
            
            return redirect()->route('facture-decompte.show', $factureDecompte->id)
                ->with('success', 'Facture de décompte validée avec succès. Le montant versé a été mis à jour.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la validation de la facture de décompte', [
                'message' => $e->getMessage(),
                'facture_decompte_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de la validation de la facture: ' . $e->getMessage());
        }
    }

    public function generate(DQE $dqe)
    {
        try {
            // Calculer le montant total à payer à partir des lignes du DQE
            $montantAPayer = $dqe->lignes()->sum('montant_ht');
            
            // Créer la facture contrat
            $factureContrat = FactureContrat::create([
                'dqe_id' => $dqe->id,
                'montant_a_payer' => $montantAPayer,
                'montant_verse' => 0
            ]);
            
            // Si c'est une requête AJAX, retourner JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Facture contrat générée avec succès.',
                    'montant' => number_format($montantAPayer, 2) . ' FCFA',
                    'facture_id' => $factureContrat->id
                ]);
            }
            
            // Sinon, rediriger normalement
            return redirect()->back()->with('success', 'Facture contrat générée avec succès. Montant à payer: ' . number_format($montantAPayer, 2) . ' FCFA');
            
        } catch (\Exception $e) {
            // Si c'est une requête AJAX, retourner l'erreur en JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la génération de la facture contrat: ' . $e->getMessage()
                ], 500);
            }
            
            // Sinon, rediriger avec l'erreur
            return redirect()->back()->with('error', 'Erreur lors de la génération de la facture contrat: ' . $e->getMessage());
        }
    }

    /**
     * Organiser les lignes DQE par hiérarchie (Catégorie > Sous-catégorie > Rubrique)
     */
    private function organiserLignesParHierarchie($lignes, $dqeId = null)
    {
        $organisation = [];
        
        if (!$dqeId) {
            return $organisation;
        }
        
        // Récupérer toutes les catégories DQE (CategorieRubrique) pour ce DQE
        $categoriesDqe = \App\Models\CategorieRubrique::where('id_qe', $dqeId)
            ->where('type', 'dqe_manuel')
            ->orderBy('nom')
            ->get();
        
        // Créer la structure hiérarchique complète avec les catégories DQE
        foreach ($categoriesDqe as $categorie) {
            $categorieNom = $categorie->nom;
            
            if (!isset($organisation[$categorieNom])) {
                $organisation[$categorieNom] = [
                    'categorie' => $categorie,
                    'sousCategories' => []
                ];
            }
            
            // Récupérer et ajouter toutes les sous-catégories de cette catégorie DQE
            $sousCategoriesDqe = \App\Models\SousCategorieRubrique::where('id_session', $categorie->id)
                ->where('type', 'dqe_manuel')
                ->orderBy('nom')
                ->get();
            
            foreach ($sousCategoriesDqe as $sousCategorie) {
                $sousCategorieNom = $sousCategorie->nom;
                
                if (!isset($organisation[$categorieNom]['sousCategories'][$sousCategorieNom])) {
                    $organisation[$categorieNom]['sousCategories'][$sousCategorieNom] = [
                        'sousCategorie' => $sousCategorie,
                        'rubriques' => []
                    ];
                }
            }
        }
        
        // Récupérer toutes les rubriques qui ont des lignes DQE
        $rubriquesAvecLignes = \App\Models\Rubrique::with([
            'sousCategorie.categorie',
            'dqeLignes' => function($query) use ($dqeId) {
                $query->where('dqe_id', $dqeId);
            }
        ])->whereHas('dqeLignes', function($query) use ($dqeId) {
            $query->where('dqe_id', $dqeId);
        })->get();
        
        // Organiser les rubriques et lignes existantes
        foreach ($rubriquesAvecLignes as $rubrique) {
            $sousCategorie = $rubrique->sousCategorie;
            $categorie = $sousCategorie ? $sousCategorie->categorie : null;
            
            if ($categorie && $sousCategorie) {
                $categorieNom = $categorie->nom;
                $sousCategorieNom = $sousCategorie->nom;
                $rubriqueNom = $rubrique->nom;
                
                // Si la catégorie et sous-catégorie existent dans notre structure, ajouter la rubrique
                if (isset($organisation[$categorieNom]['sousCategories'][$sousCategorieNom])) {
                    if (!isset($organisation[$categorieNom]['sousCategories'][$sousCategorieNom]['rubriques'][$rubriqueNom])) {
                        $organisation[$categorieNom]['sousCategories'][$sousCategorieNom]['rubriques'][$rubriqueNom] = [
                            'rubrique' => $rubrique,
                            'lignes' => []
                        ];
                    }
                    
                    // Ajouter les lignes DQE pour cette rubrique
                    foreach ($rubrique->dqeLignes as $ligne) {
                        $organisation[$categorieNom]['sousCategories'][$sousCategorieNom]['rubriques'][$rubriqueNom]['lignes'][] = $ligne;
                    }
                }
            }
        }
        
        // Gérer les lignes sans rubrique
        $lignesSansRubrique = $lignes->whereNull('id_rubrique');
        if ($lignesSansRubrique->isNotEmpty()) {
            if (!isset($organisation['Sans catégorie'])) {
                $organisation['Sans catégorie'] = [
                    'categorie' => null,
                    'sousCategories' => []
                ];
            }
            
            if (!isset($organisation['Sans catégorie']['sousCategories']['Sans sous-catégorie'])) {
                $organisation['Sans catégorie']['sousCategories']['Sans sous-catégorie'] = [
                    'sousCategorie' => null,
                    'rubriques' => []
                ];
            }
            
            if (!isset($organisation['Sans catégorie']['sousCategories']['Sans sous-catégorie']['rubriques']['Sans rubrique'])) {
                $organisation['Sans catégorie']['sousCategories']['Sans sous-catégorie']['rubriques']['Sans rubrique'] = [
                    'rubrique' => null,
                    'lignes' => []
                ];
            }
            
            foreach ($lignesSansRubrique as $ligne) {
                $organisation['Sans catégorie']['sousCategories']['Sans sous-catégorie']['rubriques']['Sans rubrique']['lignes'][] = $ligne;
            }
        }
        
        return $organisation;
    }

    /**
     * Afficher la facture de décompte au format d'impression
     */
    public function printDecompte($id)
    {
        $factureDecompte = FactureDecompte::with(['factureContrat.dqe.contrat.client'])->findOrFail($id);
        
        return view('facture-contrat.print-decompte', compact('factureDecompte'));
    }
}
