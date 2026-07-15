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
use App\Models\Decompte;
use App\Models\LignePrestation;
use App\Http\Controllers\Concerns\ExportsListPdf;
use App\Support\PdfBranding;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrestationController extends Controller
{
    use ExportsListPdf;

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

    public function exportListePdf()
    {
        $prestations = Prestation::with(['artisan', 'fournisseur', 'contrat', 'corpMetier'])
            ->orderByDesc('created_at')
            ->get();

        $rows = [];
        foreach ($prestations as $prestation) {
            $prestataire = $prestation->artisan?->nom
                ?? $prestation->fournisseur?->nom_raison_sociale
                ?? '—';
            $rows[] = [
                $prestataire,
                $prestation->contrat?->ref_contrat ?? '—',
                $prestation->corpMetier?->nom ?? '—',
                $prestation->prestation_titre ?? '—',
                number_format((float) ($prestation->montant ?? 0), 0, ',', ' ').' FCFA',
                ($prestation->taux_avancement ?? 0).'%',
                $prestation->statut ?? '—',
            ];
        }

        return $this->streamListPdf(
            'Liste des prestations',
            ['Prestataire', 'Contrat', 'Corps de métier', 'Prestation', 'Montant', 'Avancement', 'Statut'],
            $rows,
            'liste-prestations'
        );
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
     * PDF — Fiche de versement artisan
     */
    public function voirDecompte(Prestation $prestation, $decompteId)
    {
        $decompte = Decompte::findOrFail($decompteId);

        if ($decompte->id_prestation != $prestation->id) {
            abort(403, 'Accès non autorisé');
        }

        $prestation->load([
            'artisan',
            'fournisseur',
            'contrat.projet.commune',
            'contrat.projet.quartier',
            'contrat.projet.secteurLocalisation',
            'corpMetier',
        ]);

        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $pdfBranding = PdfBranding::forBu($buId);
        $fiche = $this->buildFicheVersementData($prestation, $decompte);

        $pdf = Pdf::loadView('prestations.decompte-pdf', [
            'prestation' => $prestation,
            'decompte' => $decompte,
            'pdfBranding' => $pdfBranding,
            'configGlobal' => $pdfBranding['config'],
            'fiche' => $fiche,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = sprintf(
            'Fiche_versement_artisan_%03d.pdf',
            $fiche['numero_decompte']
        );

        return $pdf->stream($filename);
    }

    /**
     * PDF — Attachement des travaux (exécution contrat / décompte artisan)
     */
    public function voirAttachementTravaux(Prestation $prestation, $decompteId)
    {
        $decompte = Decompte::findOrFail($decompteId);

        if ($decompte->id_prestation != $prestation->id) {
            abort(403, 'Accès non autorisé');
        }

        $prestation->load(['artisan', 'fournisseur', 'contrat', 'corpMetier']);

        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $pdfBranding = PdfBranding::forBu($buId);
        $attachement = $this->buildAttachementTravauxData($prestation, $decompte, $pdfBranding);

        $pdf = Pdf::loadView('prestations.attachement-travaux-pdf', [
            'prestation' => $prestation,
            'decompte' => $decompte,
            'pdfBranding' => $pdfBranding,
            'configGlobal' => $pdfBranding['config'],
            'attachement' => $attachement,
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = sprintf(
            'Attachement_travaux_%02d.pdf',
            $attachement['numero_attachement']
        );

        return $pdf->stream($filename);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAttachementTravauxData(Prestation $prestation, Decompte $decompte, array $pdfBranding): array
    {
        $contrat = $prestation->contrat;
        $tauxIncrement = (float) $decompte->pourcentage;
        $tauxTva = ($contrat && ($contrat->tva_18 ?? true)) ? 18 : 0;

        $decomptes = Decompte::where('id_prestation', $prestation->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $indexDecompte = $decomptes->search(fn (Decompte $d) => $d->id === $decompte->id);
        $numeroAttachement = ($indexDecompte === false) ? 1 : (int) $indexDecompte + 1;

        $codesPrix = [];
        if ($contrat) {
            $dqe = DQE::where('contrat_id', $contrat->id)
                ->where('statut', 'validé')
                ->latest('updated_at')
                ->first()
                ?? DQE::where('contrat_id', $contrat->id)->latest('updated_at')->first();

            if ($dqe) {
                $codesPrix = DQELigne::where('dqe_id', $dqe->id)
                    ->whereNotNull('code')
                    ->pluck('code', 'id_rubrique')
                    ->toArray();
            }
        }

        $lignes = LignePrestation::where('id_prestation', $prestation->id)
            ->with(['rubrique.sousCategorie.categorie'])
            ->orderBy('id_rubrique')
            ->orderBy('id')
            ->get();

        $groupes = [];
        $compteurPrix = 1;

        foreach ($lignes as $ligne) {
            $categorie = $ligne->rubrique?->sousCategorie?->categorie;
            $catId = $categorie?->id ?? 0;
            $catNom = $categorie?->nom ?? 'AUTRES TRAVAUX';

            if (! isset($groupes[$catId])) {
                $groupes[$catId] = [
                    'code' => str_pad((string) (count($groupes) * 100), 3, '0', STR_PAD_LEFT),
                    'label' => $catNom,
                    'lignes' => [],
                    'totaux' => $this->emptyAttachementTotaux(),
                ];
            }

            $montantHt = (float) $ligne->montant;
            $tauxCumul = (float) $ligne->taux_avancement;
            $tauxM = min($tauxIncrement, max(0, $tauxCumul));
            $tauxM1 = max(0, $tauxCumul - $tauxM);
            $montantM1 = $montantHt * $tauxM1 / 100;
            $montantM = $montantHt * $tauxM / 100;
            $montantCumul = (float) ($ligne->montant_paye ?: ($montantHt * $tauxCumul / 100));

            $numeroPrix = $codesPrix[$ligne->id_rubrique]
                ?? str_pad((string) $compteurPrix, 3, '0', STR_PAD_LEFT);
            $compteurPrix++;

            $ligneData = [
                'numero_prix' => $numeroPrix,
                'designation' => $ligne->designation,
                'unite' => $ligne->unite ?: 'U',
                'quantite' => (float) $ligne->quantite,
                'prix_unitaire' => (float) $ligne->cout_unitaire,
                'montant_total_ht' => $montantHt,
                'taux_m1' => $tauxM1,
                'taux_m' => $tauxM,
                'taux_cumul' => $tauxCumul,
                'montant_m1' => $montantM1,
                'montant_m' => $montantM,
                'montant_cumul' => $montantCumul,
            ];

            $groupes[$catId]['lignes'][] = $ligneData;
            $groupes[$catId]['totaux'] = $this->addAttachementTotaux(
                $groupes[$catId]['totaux'],
                $ligneData
            );
        }

        if (empty($groupes)) {
            $montantHt = (float) $decompte->montant;
            $groupes[0] = [
                'code' => '000',
                'label' => 'TRAVAUX EXECUTES',
                'lignes' => [[
                    'numero_prix' => '001',
                    'designation' => $decompte->titre ?: 'Travaux exécutés',
                    'unite' => 'Ff',
                    'quantite' => 1,
                    'prix_unitaire' => $montantHt,
                    'montant_total_ht' => $montantHt,
                    'taux_m1' => 0,
                    'taux_m' => $tauxIncrement,
                    'taux_cumul' => $tauxIncrement,
                    'montant_m1' => 0,
                    'montant_m' => $montantHt,
                    'montant_cumul' => $montantHt,
                ]],
                'totaux' => [
                    'montant_total_ht' => $montantHt,
                    'montant_m1' => 0,
                    'montant_m' => $montantHt,
                    'montant_cumul' => $montantHt,
                ],
            ];
        }

        $series = array_values($groupes);
        foreach ($series as $i => &$serie) {
            $serie['code'] = str_pad((string) ($i * 100), 3, '0', STR_PAD_LEFT);
        }
        unset($serie);

        $sousTotalGeneral = $this->emptyAttachementTotaux();
        foreach ($series as $serie) {
            $sousTotalGeneral = $this->addAttachementTotaux($sousTotalGeneral, [
                'montant_total_ht' => $serie['totaux']['montant_total_ht'],
                'montant_m1' => $serie['totaux']['montant_m1'],
                'montant_m' => $serie['totaux']['montant_m'],
                'montant_cumul' => $serie['totaux']['montant_cumul'],
            ]);
        }

        $totalHtPeriode = (float) ($sousTotalGeneral['montant_m'] ?: $decompte->montant);
        $montantTva = $totalHtPeriode * $tauxTva / 100;
        $totalTtc = $totalHtPeriode + $montantTva;

        $prestataireNom = '—';
        $corpsMetier = $prestation->corpMetier?->nom ?? '—';
        if ($prestation->artisan) {
            $prestataireNom = trim(($prestation->artisan->nom ?? '') . ' ' . ($prestation->artisan->prenoms ?? ''));
            $corpsMetier = $prestation->corpMetier?->nom ?? $prestation->artisan->fonction ?? $corpsMetier;
        } elseif ($prestation->fournisseur) {
            $prestataireNom = $prestation->fournisseur->nom_raison_sociale ?? '—';
        }

        return [
            'numero_attachement' => $numeroAttachement,
            'numero_label' => str_pad((string) $numeroAttachement, 2, '0', STR_PAD_LEFT),
            'date' => $decompte->created_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'titulaire' => $pdfBranding['nom_entreprise'],
            'contrat_ref' => $contrat?->ref_contrat ?? '—',
            'contrat_libelle' => $contrat?->nom_contrat ?? '—',
            'attachement_titre' => $prestation->prestation_titre,
            'objet' => sprintf(
                'DECOMPTE N°%d - %s',
                $numeroAttachement,
                strtoupper($prestation->prestation_titre ?? 'PRESTATION')
            ),
            'prestataire' => $prestataireNom,
            'corps_metier' => strtoupper($corpsMetier),
            'series' => $series,
            'sous_total_general' => $sousTotalGeneral,
            'total_ht' => $totalHtPeriode,
            'taux_tva' => $tauxTva,
            'montant_tva' => $montantTva,
            'total_ttc' => $totalTtc,
        ];
    }

    /**
     * @return array<string, float>
     */
    private function emptyAttachementTotaux(): array
    {
        return [
            'montant_total_ht' => 0.0,
            'montant_m1' => 0.0,
            'montant_m' => 0.0,
            'montant_cumul' => 0.0,
        ];
    }

    /**
     * @param  array<string, float>  $totaux
     * @param  array<string, mixed>  $ligne
     * @return array<string, float>
     */
    private function addAttachementTotaux(array $totaux, array $ligne): array
    {
        $totaux['montant_total_ht'] += (float) ($ligne['montant_total_ht'] ?? 0);
        $totaux['montant_m1'] += (float) ($ligne['montant_m1'] ?? 0);
        $totaux['montant_m'] += (float) ($ligne['montant_m'] ?? 0);
        $totaux['montant_cumul'] += (float) ($ligne['montant_cumul'] ?? 0);

        return $totaux;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFicheVersementData(Prestation $prestation, Decompte $decompte): array
    {
        $contrat = $prestation->contrat;
        $projet = $contrat?->projet;

        $lignes = LignePrestation::where('id_prestation', $prestation->id)->get();
        $montantContratHt = (float) ($lignes->sum('montant') ?: ($prestation->montant ?? $contrat?->montant ?? 0));
        $tauxGarantie = (float) ($contrat?->taux_garantie ?? 10);
        $tauxTva = ($contrat && ($contrat->tva_18 ?? true)) ? 18 : 0;

        $decomptes = Decompte::where('id_prestation', $prestation->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $indexDecompte = $decomptes->search(fn (Decompte $d) => $d->id === $decompte->id);
        $numeroDecompte = ($indexDecompte === false) ? 1 : (int) $indexDecompte + 1;

        $lignesTravaux = [];
        $totalTravauxHt = 0.0;
        $tauxPrecedentMoyen = 0.0;
        $tauxActuelMoyen = 0.0;
        $lignesAvecMontant = 0;

        foreach ($lignes as $ligne) {
            $tauxActuel = (float) $ligne->taux_avancement;
            $tauxPrecedent = max(0, $tauxActuel - (float) $decompte->pourcentage);
            $montantLigne = (float) $ligne->montant * (float) $decompte->pourcentage / 100;

            if ($montantLigne <= 0 && $tauxActuel <= 0) {
                continue;
            }

            $lignesTravaux[] = [
                'libelle' => $ligne->designation,
                'taux_precedent' => $tauxPrecedent,
                'taux_actuel' => $tauxActuel,
                'unite' => $ligne->unite ?: 'Ff',
                'quantite' => (float) $ligne->quantite ?: 1,
                'prix_unitaire' => (float) $ligne->cout_unitaire,
                'montant' => $montantLigne,
            ];

            $totalTravauxHt += $montantLigne;
            $tauxPrecedentMoyen += $tauxPrecedent;
            $tauxActuelMoyen += $tauxActuel;
            $lignesAvecMontant++;
        }

        if ($totalTravauxHt <= 0) {
            $totalTravauxHt = (float) $decompte->montant;
        }

        if ($lignesAvecMontant > 0) {
            $tauxPrecedentMoyen /= $lignesAvecMontant;
            $tauxActuelMoyen /= $lignesAvecMontant;
        } else {
            $tauxActuelMoyen = (float) ($decompte->pourcentage ?? 0);
            $tauxPrecedentMoyen = max(0, (float) ($prestation->taux_avancement ?? 0) - $tauxActuelMoyen);
        }

        $montantRetenue = $totalTravauxHt * $tauxGarantie / 100;
        $montantPenalites = 0.0;
        $montantPpsi = 0.0;
        $montantRecuperationAvances = 0.0;

        if ($contrat && (float) $contrat->avance_demarrage > 0 && $numeroDecompte === 1) {
            $montantRecuperationAvances = min((float) $contrat->avance_demarrage, $totalTravauxHt);
        }

        $totalHtRegler = $totalTravauxHt - $montantRetenue - $montantPenalites - $montantPpsi - $montantRecuperationAvances;
        $montantTva = $totalHtRegler * $tauxTva / 100;
        $totalNetTtc = $totalHtRegler + $montantTva;

        $totalDecomptesPercus = 0.0;
        $totalRetenueGarantie = 0.0;

        foreach ($decomptes as $index => $item) {
            if ($index + 1 > $numeroDecompte) {
                break;
            }

            $travaux = (float) $item->montant;
            $retenue = $travaux * $tauxGarantie / 100;
            $totalDecomptesPercus += ($travaux - $retenue);
            $totalRetenueGarantie += $retenue;
        }

        $resteAPercevoir = max(0, $montantContratHt - $totalDecomptesPercus - $totalRetenueGarantie);

        $localisationParts = array_filter([
            $projet?->commune?->nom,
            $projet?->quartier?->nom,
            $projet?->secteurLocalisation?->nom,
        ]);
        $localisation = $localisationParts
            ? implode(' - ', $localisationParts)
            : ($contrat?->nom_projet ?? '—');

        $delaiExecution = '—';
        if ($contrat?->date_debut && $contrat?->date_fin) {
            $debut = Carbon::parse($contrat->date_debut);
            $fin = Carbon::parse($contrat->date_fin);
            $mois = max(1, (int) round($debut->diffInMonths($fin, false)));
            $delaiExecution = $mois . ' MOIS';
        }

        $user = auth()->user();
        $saisiPar = $user?->nom_complet ?: ($user?->email ?? '—');

        $prestation->loadMissing(['fournisseur', 'artisan', 'contrat.client']);
        $modePaiement = $prestation->fournisseur?->mode_paiement
            ?? $contrat?->client?->mode_paiement
            ?? '—';

        return [
            'numero_decompte' => $numeroDecompte,
            'numero_decompte_label' => str_pad((string) $numeroDecompte, 3, '0', STR_PAD_LEFT),
            'montant_contrat_ht' => $montantContratHt,
            'montant_avenant_ht' => null,
            'montant_total_contrat_ht' => $montantContratHt,
            'date_emission' => $decompte->created_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'saisi_par' => $saisiPar,
            'projet' => $projet?->nom_projet ?? ($contrat?->nom_projet ?? '—'),
            'contrat' => $contrat?->nom_contrat ?? '—',
            'localisation' => $localisation,
            'date_debut' => $contrat?->date_debut
                ? Carbon::parse($contrat->date_debut)->format('d/m/Y')
                : '—',
            'delai_execution' => $delaiExecution,
            'taux_garantie' => $tauxGarantie,
            'lignes_travaux' => $lignesTravaux,
            'travaux_supplementaires' => 0.0,
            'retenue' => [
                'taux_precedent' => $tauxPrecedentMoyen,
                'taux_actuel' => $tauxActuelMoyen,
                'montant' => $montantRetenue,
            ],
            'montant_penalites' => $montantPenalites,
            'montant_ppsi' => $montantPpsi,
            'montant_recuperation_avances' => $montantRecuperationAvances,
            'total_ht_regler' => $totalHtRegler,
            'taux_tva' => $tauxTva,
            'montant_tva' => $montantTva,
            'total_net_ttc' => $totalNetTtc,
            'mode_paiement' => $modePaiement,
            'total_decomptes_percus' => $totalDecomptesPercus,
            'total_retenue_garantie' => $totalRetenueGarantie,
            'reste_a_percevoir' => $resteAPercevoir,
        ];
    }
}
