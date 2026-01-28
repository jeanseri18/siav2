<?php
namespace App\Http\Controllers;

use App\Models\Prestation;
use App\Models\Artisan;
use App\Models\ClientFournisseur;
use App\Models\Contrat;
use App\Models\CorpMetier;
use App\Models\ComptePrestation;
use App\Models\DQE;
use App\Models\DQELigne;
use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use App\Models\Rubrique;

use Illuminate\Http\Request;

class PrestationController extends Controller
{
    public function index() {
        $prestations = Prestation::with(['artisan', 'fournisseur', 'contrat', 'corpMetier'])->get();
        
        // Définir la session contrat_id si elle n'existe pas
        if (!session('contrat_id') && $prestations->isNotEmpty()) {
            $firstContrat = $prestations->first()->contrat;
            if ($firstContrat) {
                session([
                    'contrat_id' => $firstContrat->id,
                    'contrat_nom' => $firstContrat->nom_contrat,
                    'ref_contrat' => $firstContrat->ref_contrat
                ]);
            }
        }
        
        return view('prestations.index', compact('prestations'));
    }

    public function create() {
        $projet_id = session('projet_id');
        $contrats = Contrat::where('id_projet', $projet_id)->get();
        $artisans = Artisan::all();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->get();
        $corpMetiers = CorpMetier::all();
        return view('prestations.create', compact('contrats', 'artisans', 'fournisseurs', 'corpMetiers'));
    }

    public function store(Request $request) {
        $request->validate([
            'id_artisan' => 'nullable|exists:artisan,id',
            'fournisseur_id' => 'nullable|exists:client_fournisseurs,id',
            'id_contrat' => 'required',
            'corps_metier_id' => 'nullable|exists:corp_metiers,id',
            'prestation_titre' => 'required',
            'detail' => 'required',
            'montant' => 'nullable|numeric',
            'taux_avancement' => 'nullable|integer|min:0|max:100',
        ]);
    
        // Ajouter le statut "En cours" par défaut
        Prestation::create([
            'id_artisan' => $request->id_artisan,
            'fournisseur_id' => $request->fournisseur_id,
            'id_contrat' => $request->id_contrat,
            'corps_metier_id' => $request->corps_metier_id,
            'prestation_titre' => $request->prestation_titre,
            'detail' => $request->detail,
            'montant' => $request->montant,
            'taux_avancement' => $request->taux_avancement ?? 0,
            'statut' => 'En cours', // Valeur par défaut
        ]);
    
        return redirect()->route('prestations.index')->with('success', 'Prestation ajoutée avec succès');
    }

    public function edit(Prestation $prestation) {
        $projet_id = session('projet_id');
        $contrats = Contrat::where('id_projet', $projet_id)->get();
        $artisans = Artisan::all();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->get();
        $corpMetiers = CorpMetier::all();
        
        // Charger les données DQE hiérarchiques
        $categories = CategorieRubrique::with(['sousCategories.rubriques.dqeLignes'])->get();
        $dqes = DQE::with(['lignes.rubrique.sousCategorie.categorie'])->get();
        
        // Ajouter des logs pour déboguer
        \Log::info('DQEs loaded:', ['count' => $dqes->count()]);
        \Log::info('First DQE lignes:', ['lignes' => $dqes->first() ? $dqes->first()->lignes : null]);
        
        return view('prestations.edit', compact('prestation', 'contrats', 'artisans', 'fournisseurs', 'corpMetiers', 'categories', 'dqes'));
    }

    public function update(Request $request, Prestation $prestation) {
        $request->validate([
            'id_artisan' => 'nullable|exists:artisan,id',
            'fournisseur_id' => 'nullable|exists:client_fournisseurs,id',
            'id_contrat' => 'required',
            'corps_metier_id' => 'nullable|exists:corp_metiers,id',
            'prestation_titre' => 'required',
            'detail' => 'required',
            'montant' => 'nullable|numeric',
            'taux_avancement' => 'nullable|integer|min:0|max:100',
            'statut' => 'required|string'
        ]);

        // Mettre à jour les informations de base de la prestation
        $prestation->update($request->all());



        return redirect()->route('prestations.index')->with('success', 'Prestation mise à jour');
    }

    public function destroy(Prestation $prestation) {
        $prestation->delete();
        return redirect()->route('prestations.index')->with('success', 'Prestation supprimée');
    }

    /**
     * Récupérer la liste des artisans disponibles
     */
    public function getArtisansDisponibles(Prestation $prestation)
    {
        $artisans = Artisan::orderBy('nom')
                          ->get(['id', 'nom', 'prenoms', 'telephone']);
        
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')
                                        ->orderBy('nom_raison_sociale')
                                        ->get(['id', 'nom_raison_sociale', 'prenoms', 'telephone']);
        
        return response()->json([
            'artisans' => $artisans,
            'fournisseurs' => $fournisseurs
        ]);
    }

    /**
     * Affecter un artisan à une prestation
     */
    public function affecterArtisan(Request $request, Prestation $prestation)
    {
        $request->validate([
            'id_artisan' => 'required',
            'date_affectation' => 'nullable|date'
        ]);

        // Déterminer si c'est un artisan ou un fournisseur
        $selectedOption = $request->id_artisan;
        $typePrestataire = $request->input('type_prestataire_hidden');
        
        // Réinitialiser les deux champs
        $updateData = [
            'id_artisan' => null,
            'fournisseur_id' => null,
            'date_affectation' => $request->date_affectation ?? now()->format('Y-m-d')
        ];
        
        // Selon le type sélectionné, affecter le bon prestataire
        if ($typePrestataire === 'artisan') {
            $updateData['id_artisan'] = $selectedOption;
        } elseif ($typePrestataire === 'fournisseur') {
            $updateData['fournisseur_id'] = $selectedOption;
        }

        $prestation->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Prestataire affecté avec succès'
        ]);
    }

    /**
     * Récupérer les détails d'une prestation
     */
    public function getDetails(Prestation $prestation)
    {
        $prestation->load(['artisan', 'contrat', 'corpMetier', 'comptes']);
        
        return view('prestations.partials.details', compact('prestation'))->render();
    }

    /**
     * Ajouter un compte à une prestation
     */
    public function ajouterCompte(Request $request, Prestation $prestation)
    {
        $request->validate([
            'type_compte' => 'required|in:materiel,main_oeuvre,transport,autres',
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'date_compte' => 'required|date'
        ]);

        $compte = $prestation->comptes()->create([
            'type_compte' => $request->type_compte,
            'montant' => $request->montant,
            'description' => $request->description,
            'date_compte' => $request->date_compte,
            'created_by' => auth()->id()
        ]);

        // Calculer et mettre à jour le taux d'avancement
        $this->updateTauxAvancement($prestation);

        return response()->json([
            'success' => true,
            'message' => 'Compte ajouté avec succès',
            'compte' => $compte,
            'taux_avancement' => $prestation->fresh()->taux_avancement
        ]);
    }



    /**
     * Récupérer les informations de l'artisan actuel et la liste des artisans disponibles
     */
    public function getArtisanInfo(Prestation $prestation)
    {
        $artisanActuel = $prestation->artisan ? $prestation->artisan->nom . ' ' . $prestation->artisan->prenoms : null;
        
        $artisansDisponibles = Artisan::where('id', '!=', $prestation->id_artisan)
                                     ->orderBy('nom')
                                     ->get(['id', 'nom', 'prenoms', 'telephone']);
        
        return response()->json([
            'artisan_actuel' => $artisanActuel,
            'artisans_disponibles' => $artisansDisponibles
        ]);
    }

    /**
     * Remplacer l'artisan d'une prestation
     */
    public function remplacerArtisan(Request $request, Prestation $prestation)
    {
        $request->validate([
            'id_artisan' => 'required|exists:artisan,id',
            'motif_remplacement' => 'nullable|string|max:500'
        ]);

        $ancienArtisan = $prestation->artisan;
        
        $prestation->update([
            'id_artisan' => $request->id_artisan,
            'motif_remplacement' => $request->motif_remplacement,
            'date_remplacement' => now()
        ]);

        // Log du changement d'artisan (optionnel)
        \Log::info('Remplacement d\'artisan', [
            'prestation_id' => $prestation->id,
            'ancien_artisan' => $ancienArtisan ? $ancienArtisan->nom : 'Aucun',
            'nouvel_artisan' => $prestation->fresh()->artisan->nom,
            'motif' => $request->motif_remplacement,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Artisan remplacé avec succès'
        ]);
    }

    /**
     * Mettre à jour le taux d'avancement d'une prestation
     * basé sur le total des comptes par rapport au montant de la prestation
     */
    private function updateTauxAvancement(Prestation $prestation)
    {
        // Calculer le total des comptes
        $totalComptes = $prestation->comptes()->sum('montant');
        
        // Calculer le taux d'avancement (sans limitation à 100%)
        $tauxAvancement = 0;
        if ($prestation->montant > 0) {
            $tauxAvancement = round(($totalComptes / $prestation->montant) * 100, 2);
        }
        
        // Mettre à jour la prestation
        $prestation->update([
            'taux_avancement' => $tauxAvancement
        ]);
        
        return $tauxAvancement;
    }



    /**
     * Générer un document pour la prestation
     */
    public function document(Prestation $prestation)
    {
        $prestation->load(['artisan', 'fournisseur', 'contrat', 'corpMetier', 'comptes']);
        
        return view('prestations.document', compact('prestation'));
    }

    /**
     * Afficher la page de création des lignes de prestation
     */
    public function lignes(Prestation $prestation)
    {
        $prestation->load(['artisan', 'fournisseur', 'contrat', 'corpMetier']);
        
        // Récupérer le premier DQE validé du contrat
        $dqe = DQE::where('contrat_id', $prestation->id_contrat)
                  ->where('statut', 'Validé')
                  ->orderBy('date_validation', 'asc')
                  ->first();
        
        // Récupérer les catégories avec leurs sous-catégories et rubriques
        $categories = [];
        if ($dqe) {
            $categories = CategorieRubrique::where('id_qe', $dqe->id)
                ->with(['sousCategories.rubriques.dqeLignes'])
                ->orderBy('id', 'asc')
                ->get();
        }
        
        // Récupérer les unités de mesure
        $unites = \App\Models\UniteMesure::orderBy('nom', 'asc')->get();
        
        return view('prestations.lignes', compact('prestation', 'dqe', 'categories', 'unites'));
    }

    /**
     * Enregistrer les lignes de prestation
     */
    public function storeLignes(Request $request, Prestation $prestation)
    {
        $request->validate([
            'lignes' => 'required|array',
            'lignes.*.designation' => 'required|string',
            'lignes.*.unite' => 'required|string',
            'lignes.*.quantite' => 'required|numeric|min:0',
            'lignes.*.cout_unitaire' => 'required|numeric|min:0',
            'lignes.*.id_rubrique' => 'required|exists:rubriques,id'
        ]);

        foreach ($request->lignes as $ligneData) {
            $ligne = \App\Models\LignePrestation::create([
                'designation' => $ligneData['designation'],
                'unite' => $ligneData['unite'],
                'quantite' => $ligneData['quantite'],
                'cout_unitaire' => $ligneData['cout_unitaire'],
                'id_rubrique' => $ligneData['id_rubrique'],
                'id_prestation' => $prestation->id,
            ]);
            
            // Calculer les montants automatiquement
            $ligne->calculerMontants();
        }

        return redirect()->route('prestations.lignes', $prestation->id)
            ->with('success', 'Lignes de prestation enregistrées avec succès');
    }

    /**
     * Afficher les lignes de prestation
     */
    public function voirLignes(Prestation $prestation)
    {
        $prestation->load(['artisan', 'fournisseur', 'contrat', 'corpMetier']);
        
        // Récupérer les lignes de prestation avec leurs relations
        $lignes = \App\Models\LignePrestation::where('id_prestation', $prestation->id)
            ->with(['rubrique.sousCategorie.categorie'])
            ->orderBy('id_rubrique')
            ->get();
        
        // Organiser les lignes par hiérarchie
        $lignesParHierarchie = [];
        foreach ($lignes as $ligne) {
            if ($ligne->rubrique) {
                $categorie = $ligne->rubrique->sousCategorie->categorie ?? null;
                $sousCategorie = $ligne->rubrique->sousCategorie ?? null;
                $rubrique = $ligne->rubrique;
                
                if ($categorie && $sousCategorie && $rubrique) {
                    $catId = $categorie->id;
                    $sousCatId = $sousCategorie->id;
                    $rubId = $rubrique->id;
                    
                    if (!isset($lignesParHierarchie[$catId])) {
                        $lignesParHierarchie[$catId] = [
                            'categorie' => $categorie,
                            'sous_categories' => []
                        ];
                    }
                    
                    if (!isset($lignesParHierarchie[$catId]['sous_categories'][$sousCatId])) {
                        $lignesParHierarchie[$catId]['sous_categories'][$sousCatId] = [
                            'sous_categorie' => $sousCategorie,
                            'rubriques' => []
                        ];
                    }
                    
                    if (!isset($lignesParHierarchie[$catId]['sous_categories'][$sousCatId]['rubriques'][$rubId])) {
                        $lignesParHierarchie[$catId]['sous_categories'][$sousCatId]['rubriques'][$rubId] = [
                            'rubrique' => $rubrique,
                            'lignes' => []
                        ];
                    }
                    
                    $lignesParHierarchie[$catId]['sous_categories'][$sousCatId]['rubriques'][$rubId]['lignes'][] = $ligne;
                }
            }
        }
        
        return view('prestations.voir-lignes', compact('prestation', 'lignesParHierarchie'));
    }

    /**
     * Valider les paiements des lignes de prestation
     */
    public function validerPaiements(Request $request, Prestation $prestation)
    {
        $paiements = $request->input('paiements', []);
        
        if (empty($paiements)) {
            return redirect()->back()->with('error', 'Aucun paiement à valider');
        }
        
        $totalMontantDecompte = 0;
        $totalTauxPaiement = 0;
        $nombreLignes = 0;
        
        // Parcourir chaque paiement
        foreach ($paiements as $ligneId => $tauxPaiement) {
            if (empty($tauxPaiement) || $tauxPaiement <= 0) {
                continue;
            }
            
            $ligne = \App\Models\LignePrestation::find($ligneId);
            
            if ($ligne && $ligne->id_prestation == $prestation->id) {
                // Additionner le taux de paiement au taux d'avancement actuel
                $nouveauTaux = $ligne->taux_avancement + $tauxPaiement;
                
                // Limiter à 100% maximum
                if ($nouveauTaux > 100) {
                    $nouveauTaux = 100;
                }
                
                $ligne->taux_avancement = $nouveauTaux;
                
                // Recalculer les montants selon le nouveau taux
                $ligne->montant_paye = ($ligne->montant * $nouveauTaux) / 100;
                $ligne->montant_reste = $ligne->montant - $ligne->montant_paye;
                
                $ligne->save();
                
                // Calculer le montant payé pour ce décompte (basé sur le taux de paiement)
                $montantPaiementLigne = ($ligne->montant * $tauxPaiement) / 100;
                $totalMontantDecompte += $montantPaiementLigne;
                $totalTauxPaiement += $tauxPaiement;
                $nombreLignes++;
            }
        }
        
        if ($nombreLignes > 0) {
            // Calculer le pourcentage moyen du décompte
            $pourcentageMoyen = $totalTauxPaiement / $nombreLignes;
            
            // Créer un décompte
            \App\Models\Decompte::create([
                'titre' => 'Décompte du ' . now()->format('d/m/Y'),
                'montant' => $totalMontantDecompte,
                'pourcentage' => $pourcentageMoyen,
                'id_prestation' => $prestation->id
            ]);
            
            // Mettre à jour le taux d'avancement de la prestation
            $this->updateTauxAvancementPrestation($prestation);
            
            return redirect()->route('prestations.voirLignes', $prestation->id)
                ->with('success', "Paiements validés avec succès. Décompte créé : " . number_format($totalMontantDecompte, 2, ',', ' ') . " FCFA");
        }
        
        return redirect()->back()->with('warning', 'Aucun paiement valide à traiter');
    }
    
    /**
     * Mettre à jour le taux d'avancement de la prestation
     */
    private function updateTauxAvancementPrestation(Prestation $prestation)
    {
        // Récupérer toutes les lignes de la prestation
        $lignes = \App\Models\LignePrestation::where('id_prestation', $prestation->id)->get();
        
        if ($lignes->count() > 0) {
            $totalMontant = $lignes->sum('montant');
            $totalMontantPaye = $lignes->sum('montant_paye');
            
            // Calculer le taux d'avancement global
            if ($totalMontant > 0) {
                $tauxAvancement = ($totalMontantPaye / $totalMontant) * 100;
                $prestation->taux_avancement = round($tauxAvancement, 2);
                $prestation->save();
            }
        }
    }

    /**
     * Afficher le détail d'un décompte
     */
    public function voirDecompte(Prestation $prestation, $decompteId)
    {
        $decompte = \App\Models\Decompte::findOrFail($decompteId);
        
        // Vérifier que le décompte appartient bien à la prestation
        if ($decompte->id_prestation != $prestation->id) {
            abort(403, 'Accès non autorisé');
        }
        
        $prestation->load(['artisan', 'fournisseur', 'contrat', 'corpMetier']);
        
        return view('prestations.decompte-pdf', compact('prestation', 'decompte'));
    }
}
