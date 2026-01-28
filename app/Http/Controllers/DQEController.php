<?php

namespace App\Http\Controllers;

use App\Models\Bpu;
use App\Models\Contrat;
use App\Models\DQE;
use App\Models\DQELigne;
use App\Models\CategorieRubrique;
use App\Models\Rubrique;
use App\Models\Categorie;
use App\Models\SousCategorie;
use App\Models\SousCategorieRubrique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DQEController extends Controller
{
    /**
     * Afficher la liste des DQE pour un contrat
     */
  /**
 * Afficher la liste des DQE pour un contrat
 */
public function index()
{
    $contratId = session('contrat_id');
    
    if (!$contratId) {
        return redirect()->route('contrats.index')
            ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
    }
    
    $contrat = Contrat::findOrFail($contratId);
    $dqes = DQE::where('contrat_id', $contratId)
               ->orderBy('created_at', 'desc')
               ->get();
    
    // Récupérer le DQE ID depuis la session s'il existe
    $dqeId = session('dqe_id');
    
    // Ajouter seulement les catégories qui ont des BPU liés au contrat
    $categories = CategorieRubrique::with([
        'sousCategories.rubriques.bpus' => function($query) use ($contratId) {
            $query->where('contrat_id', $contratId);
        }
    ])->whereHas('sousCategories.rubriques.bpus', function($query) use ($contratId) {
        $query->where('contrat_id', $contratId);
    });
    
    // Filtrer par DQE si un DQE est sélectionné
    if ($dqeId) {
        $categories->where('id_qe', $dqeId);
    }
    
    $categories = $categories->get();
    
    return view('dqe.index', compact('contrat', 'dqes', 'categories'));
}

    /**
     * Afficher les détails d'un DQE
     */
    public function show($id)
    {
        $dqe = DQE::with([
            'lignes.rubrique.sousCategorie.categorie',
            'contrat'
        ])->findOrFail($id);
        $contrat = $dqe->contrat;
        
        // Organiser les lignes par hiérarchie
        $lignesOrganisees = $this->organiserLignesParHierarchie($dqe->lignes, $id);

        return view('dqe.show', compact('dqe', 'contrat', 'lignesOrganisees'));
    }

    /**
     * Afficher le formulaire de création d'un DQE
     */
  public function create()
{
    $contratId = session('contrat_id');
    
    if (!$contratId) {
        return redirect()->route('contrats.index')
            ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
    }
    
    $contrat = Contrat::findOrFail($contratId);
    
    // Récupérer le DQE ID depuis la session s'il existe
    $dqeId = session('dqe_id');
    
    // Récupérer seulement les catégories qui ont des BPU liés au contrat
    $categories = CategorieRubrique::with([
        'sousCategories.rubriques.bpus' => function($query) use ($contratId) {
            $query->where('contrat_id', $contratId);
        }
    ])->whereHas('sousCategories.rubriques.bpus', function($query) use ($contratId) {
        $query->where('contrat_id', $contratId);
    });
    
    // Filtrer par DQE si un DQE est sélectionné
    if ($dqeId) {
        $categories->where('id_qe', $dqeId);
    }
    
    $categories = $categories->get();
    
    return view('dqe.create', compact('contrat', 'categories'));
}
    /**
     * Enregistrer un nouveau DQE
     */
    public function store(Request $request)
{
    $contratId = session('contrat_id');
    
    if (!$contratId) {
        return redirect()->route('contrats.index')
            ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
    }
    
    $request->validate([
        'reference' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);


          $lastReference = \App\Models\Reference::where('nom', 'Code devis émis')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'DQE_0000';  // Si aucune référence, utiliser un modèle
$newReference = 'DQE_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête
$request->merge([
'reference' => $newReference,
]);
    // Créer le DQE
    $dqe = DQE::create([
        'contrat_id' => $contratId,
        'reference' => $request->reference,
        'notes' => $request->notes,
        'statut' => 'brouillon',
    ]);

    return redirect()->route('dqe.edit', $dqe->id)
        ->with('success', 'DQE créé avec succès. Vous pouvez maintenant ajouter des lignes.');
}
    /**
     * Afficher le formulaire d'édition d'un DQE
     */
    public function edit($id)
    {
        $dqe = DQE::with([
            'lignes.rubrique.sousCategorie.categorie'
        ])->findOrFail($id);
        $contrat = $dqe->contrat;
        $contratId = $contrat->id;
        
        // Stocker le DQE ID dans la session pour le filtrage
        session(['dqe_id' => $id]);
        
        // Récupérer toutes les catégories DQE manuelles pour ce DQE
        $categories = CategorieRubrique::where('id_qe', $id)
            ->where('type', 'dqe_manuel')
            ->with(['sousCategories' => function($query) use ($id) {
                $query->where('id_qe', $id)
                      ->where('type', 'dqe_manuel')
                      ->orderBy('nom');
            }])
            ->orderBy('nom')
            ->get();
        
        // Organiser les lignes par hiérarchie
        $lignesOrganisees = $this->organiserLignesParHierarchie($dqe->lignes, $id);
        
        return view('dqe.edit', compact('dqe', 'contrat', 'categories', 'lignesOrganisees'));
    }

    /**
     * Organiser les lignes DQE par hiérarchie (Catégorie > Sous-catégorie > Rubrique)
     * Nouvelle approche : afficher toutes les catégories et sous-catégories même sans lignes
     */
    private function organiserLignesParHierarchie($lignes, $dqeId = null)
    {
        $organisation = [];
        
        // Utiliser l'ID du DQE fourni
        if (!$dqeId) {
            return $organisation;
        }
        
        // Récupérer toutes les catégories DQE (CategorieRubrique) pour ce DQE
        $categoriesDqe = CategorieRubrique::where('id_qe', $dqeId)
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
            $sousCategoriesDqe = SousCategorieRubrique::where('id_session', $categorie->id)
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
        $rubriquesAvecLignes = Rubrique::with([
            'sousCategorie.categorie',
            'dqeLignes' => function($query) use ($dqeId) {
                $query->where('dqe_id', $dqeId);
            }
        ])->whereHas('dqeLignes', function($query) use ($dqeId) {
            $query->where('dqe_id', $dqeId);
        })->get();
        
        // Récupérer aussi toutes les rubriques manuelles (même sans lignes DQE) pour ce DQE
        $rubriquesManuelles = Rubrique::with([
            'sousCategorie.categorie'
        ])->where('type', 'dqe_manuel')
          ->where('id_qe', $dqeId)
          ->get();
        
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
        
        // Ajouter aussi les rubriques manuelles (même sans lignes DQE)
        foreach ($rubriquesManuelles as $rubrique) {
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
     * Mettre à jour un DQE
     */
    public function update(Request $request, $id)
    {
        $dqe = DQE::findOrFail($id);
        
        $request->validate([
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'statut' => 'required|in:brouillon,validé,archivé',
        ]);

        // Vérifier les permissions pour la validation du DQE
        if ($request->statut === 'validé' && $dqe->statut !== 'validé') {
            $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'admin', 'dg'];
            if (!in_array(Auth::user()->role, $rolesAutorises)) {
                return redirect()->back()->with('error', 'Vous n\'avez pas les permissions nécessaires pour valider ce DQE.');
            }
        }

        // Sauvegarder l'ancien statut avant la mise à jour
        $ancienStatut = $dqe->statut;
        
        $dqe->update([
            'reference' => $request->reference,
            'notes' => $request->notes,
            'statut' => $request->statut,
        ]);

        // Si le DQE vient d'être validé, mettre à jour le montant du contrat
        $contratUpdated = false;
        if ($request->statut === 'validé' && $ancienStatut !== 'validé') {
            $contratUpdated = $this->updateContratMontant($dqe);
        }

        $message = 'DQE mis à jour avec succès.';
        if ($contratUpdated) {
            $message .= ' Le montant du contrat a été automatiquement mis à jour avec le montant TTC du DQE (' . number_format($dqe->montant_total_ttc, 0, ',', ' ') . ' FCFA).';
        }

        return redirect()->route('dqe.edit', $dqe->id)
            ->with('success', $message);
    }

    /**
     * Supprimer un DQE
     */
    public function destroy($id)
    {
        $dqe = DQE::findOrFail($id);
        $contratId = $dqe->contrat_id;
        
        $dqe->delete();

        return redirect()->route('dqe.index')
            ->with('success', 'DQE supprimé avec succès.');
    }

    /**
     * Mettre à jour le montant du contrat basé sur le DQE validé
     */
    private function updateContratMontant(DQE $dqe)
    {
        $contrat = $dqe->contrat;
        
        if ($contrat) {
            $updated = $contrat->updateMontantFromDQE();
            
            if ($updated) {
                \Log::info("Montant du contrat {$contrat->ref_contrat} mis à jour automatiquement: {$dqe->montant_total_ttc} FCFA");
            }
            
            return $updated;
        }
        
        return false;
    }

    /**
     * Générer un DQE à partir du BPU
     */
//    public function generateFromBPU(Request $request)
// {
//     $contratId = session('contrat_id');
    
//     if (!$contratId) {
//         return redirect()->route('contrats.index')
//             ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
//     }
    
//     $contrat = Contrat::findOrFail($contratId);
    
//     // Créer le DQE
//     $dqe = DQE::create([
//         'contrat_id' => $contratId,
//         'reference' => 'DQE-' . date('YmdHis'),
//         'statut' => 'brouillon',
//     ]);

//     // Récupérer les BPU sélectionnés
//     $bpuIds = $request->bpu_ids ?? [];
//     $bpusUtilitaires = Bpu::whereIn('id', $bpuIds)->utilitaires()->get();

//     // Créer les lignes de DQE avec copie des BPU utilitaires vers BPU contrat
//         foreach ($bpusUtilitaires as $bpuUtilitaire) {
//             // Créer une copie du BPU utilitaire pour ce contrat
//             $bpuContrat = $bpuUtilitaire->replicate();
//             $bpuContrat->contrat_id = $contratId;
//             $bpuContrat->save();
            
//             // Créer la ligne DQE avec le nouveau BPU contrat
//             DQELigne::create([
//                 'dqe_id' => $dqe->id,
//                 'id_rubrique' => $bpuContrat->rubrique_id,
//                 'designation' => $bpuContrat->designation,
//                 'quantite' => 1, // Quantité par défaut
//                 'unite' => $bpuContrat->unite,
//                 'pu_ht' => $bpuContrat->pu_ht,
//                 'montant_ht' => $bpuContrat->pu_ht, // Montant initial = prix unitaire × 1
//             ]);
//         }
    
//     // Gérer aussi les BPU déjà spécifiques au contrat (s'ils existent)
//     $bpusContrat = Bpu::whereIn('id', $bpuIds)->contrat($contratId)->get();
//     foreach ($bpusContrat as $bpu) {
//         DQELigne::create([
//             'dqe_id' => $dqe->id,
//             'id_rubrique' => $bpu->rubrique_id,
//             'designation' => $bpu->designation,
//             'quantite' => 1,
//             'unite' => $bpu->unite,
//             'pu_ht' => $bpu->pu_ht,
//             'montant_ht' => $bpu->pu_ht,
//         ]);
//     }

//     // Mettre à jour les totaux
//     $dqe->updateTotals();

//     return redirect()->route('dqe.edit', $dqe->id)
//         ->with('success', 'DQE généré avec succès à partir du BPU. Veuillez ajuster les quantités.');
// }
    /**
     * Ajouter une ligne au DQE
     */
    public function addLine(Request $request, $id)
    {
        $dqe = DQE::findOrFail($id);
        
        $request->validate([
            'bpu_id' => 'required|exists:bpus,id',
            'quantite' => 'required|numeric|min:0.01',
        ]);

        $bpu = Bpu::findOrFail($request->bpu_id);
        
        // Créer la ligne
        $ligne = DQELigne::create([
            'dqe_id' => $dqe->id,
            'id_rubrique' => $bpu->rubrique_id,
            'designation' => $bpu->designation,
            'quantite' => $request->quantite,
            'unite' => $bpu->unite,
            'pu_ht' => $bpu->pu_ht,
            'montant_ht' => $bpu->pu_ht * $request->quantite,
        ]);

        // Mettre à jour les totaux
        $dqe->updateTotals();

        return redirect()->route('dqe.edit', $dqe->id)
            ->with('success', 'Ligne ajoutée avec succès.');
    }

    /**
     * Ajouter plusieurs lignes au DQE
     */
    public function addMultipleLines(Request $request, $id)
    {
        $dqe = DQE::findOrFail($id);
        
        $request->validate([
            'bpus' => 'required|array|min:1',
            'bpus.*.bpu_id' => 'required|exists:bpus,id',
            'bpus.*.quantite' => 'required|numeric|min:0.01',
        ]);

        $lignesAjoutees = 0;
        
        foreach ($request->bpus as $bpuData) {
            $bpu = Bpu::findOrFail($bpuData['bpu_id']);
            
            // Créer la ligne
            DQELigne::create([
                'dqe_id' => $dqe->id,
                'id_rubrique' => $bpu->rubrique_id,
                'designation' => $bpu->designation,
                'quantite' => $bpuData['quantite'],
                'unite' => $bpu->unite,
                'pu_ht' => $bpu->pu_ht,
                'montant_ht' => $bpu->pu_ht * $bpuData['quantite'],
            ]);
            
            $lignesAjoutees++;
        }

        // Mettre à jour les totaux
        $dqe->updateTotals();

        return redirect()->route('dqe.edit', $dqe->id)
            ->with('success', $lignesAjoutees . ' ligne(s) ajoutée(s) avec succès.');
    }

    /**
     * Mettre à jour une ligne de DQE
     */
    public function updateLine(Request $request, $id, $ligneId)
    {
        $ligne = DQELigne::findOrFail($ligneId);
        
        $request->validate([
            'quantite' => 'nullable|numeric|min:0.01',
            'designation' => 'nullable|string|max:500',
        ]);

        // Mettre à jour la quantité si fournie
        if ($request->has('quantite')) {
            $ligne->quantite = $request->quantite;
        }
        
        // Mettre à jour la désignation si fournie
        if ($request->has('designation')) {
            $ligne->designation = $request->designation;
        }
        
        $ligne->calculerMontant();

        return redirect()->back()
            ->with('success', 'Ligne mise à jour avec succès.');
    }

    /**
     * Supprimer une ligne de DQE
     */
    public function deleteLine($id, $ligneId)
    {
        $ligne = DQELigne::findOrFail($ligneId);
        $dqe = $ligne->dqe;
        
        $ligne->delete();
        $dqe->updateTotals();

        return redirect()->route('dqe.edit', $id)
            ->with('success', 'Ligne supprimée avec succès.');
    }

    /**
     * Créer une nouvelle section pour le DQE
     */
    public function createSection(Request $request, $id)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
        ]);

        $dqe = DQE::findOrFail($id);
        
        // Vérifier si la section existe déjà
        $existingSection = DQELigne::where('dqe_id', $dqe->id)
            ->where('section', $request->section_name)
            ->first();
            
        if ($existingSection) {
            return redirect()->route('dqe.edit', $id)
                ->withErrors(['section_name' => 'Cette section existe déjà.']);
        }

        return redirect()->route('dqe.edit', $id)
            ->with('success', 'Section créée avec succès. Vous pouvez maintenant ajouter des lignes à cette section.');
    }

    /**
     * Récupérer les lignes BPU d'un contrat
     */
    public function getBpuLignes($contratId)
    {
        try {
            $contrat = Contrat::findOrFail($contratId);
            
            // Récupérer toutes les lignes BPU du contrat avec leurs rubriques et catégories
            $lignesBPU = \App\Models\Bpu::with(['rubrique.sousCategorie.categorie'])
                ->where('contrat_id', $contratId)
                ->orderBy('designation')
                ->get();

            return response()->json([
                'success' => true,
                'lignes' => $lignesBPU->map(function ($ligne) {
                    $categorie = null;
                    $sousCategorie = null;
                    
                    if ($ligne->rubrique && $ligne->rubrique->sousCategorie) {
                        $sousCategorie = [
                            'id' => $ligne->rubrique->sousCategorie->id,
                            'nom' => $ligne->rubrique->sousCategorie->nom
                        ];
                        
                        if ($ligne->rubrique->sousCategorie->categorie) {
                            $categorie = [
                                'id' => $ligne->rubrique->sousCategorie->categorie->id,
                                'nom' => $ligne->rubrique->sousCategorie->categorie->nom
                            ];
                        }
                    }
                    
                    return [
                        'id' => $ligne->id,
                        'designation' => $ligne->designation,
                        'unite' => $ligne->unite,
                        'prix_unitaire' => $ligne->pu_ht,
                        'quantite' => $ligne->qte,
                        'categorie' => $categorie,
                        'sous_categorie' => $sousCategorie,
                        'rubrique' => $ligne->rubrique ? [
                            'id' => $ligne->rubrique->id,
                            'nom' => $ligne->rubrique->nom,
                            'sous_categorie' => $sousCategorie
                        ] : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des lignes BPU: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer un DQE à partir du BPU
     */
    public function generateFromBPU(Request $request, $contratId)
    {
        try {
            $contrat = Contrat::findOrFail($contratId);
            
            // Récupérer les BPU sélectionnés
            $bpuIds = $request->input('bpu_ids', []);
            
            if (empty($bpuIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun BPU sélectionné.'
                ], 400);
            }
            
            // Créer un nouveau DQE
            $dqe = DQE::create([
                'contrat_id' => $contratId,
                'reference' => 'DQE_' . now()->format('YmdHis'),
                'statut' => 'brouillon',
            ]);
            
            $lignesCreees = 0;
            
            // Récupérer les BPU sélectionnés
            $bpus = \App\Models\Bpu::whereIn('id', $bpuIds)->get();
            
            foreach ($bpus as $bpu) {
                // Créer la ligne DQE
                DQELigne::create([
                    'dqe_id' => $dqe->id,
                    'id_rubrique' => $bpu->rubrique_id,
                    'designation' => $bpu->designation,
                    'quantite' => $bpu->qte ?? 1,
                    'unite' => $bpu->unite,
                    'pu_ht' => $bpu->pu_ht ?? 0,
                    'montant_ht' => ($bpu->pu_ht ?? 0) * ($bpu->qte ?? 1),
                    // Champs de coût détaillés depuis le BPU
                    'materiaux' => $bpu->materiaux ?? 0,
                    'mo' => $bpu->main_oeuvre ?? 0,
                    'materiel' => $bpu->materiel ?? 0,
                    'frais_chantier' => $bpu->frais_chantier ?? 0,
                    'frais_generaux' => $bpu->frais_general ?? 0,
                    'benefice' => $bpu->marge_nette ?? 0
                ]);
                
                $lignesCreees++;
            }
            
            // Mettre à jour les totaux du DQE
            $dqe->updateTotals();
            
            return response()->json([
                'success' => true,
                'message' => "DQE généré avec succès avec {$lignesCreees} ligne(s)"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du DQE: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer des lignes DQE à partir de lignes BPU sélectionnées
     */
    public function creerLignesDepuisBPU(Request $request, $dqeId)
    {
        try {
            $request->validate([
                'lignes_bpu_ids' => 'required|array|min:1',
                'lignes_bpu_ids.*' => 'exists:bpus,id',
                'rubrique_id' => 'required|exists:rubriques,id'
            ]);

            $dqe = DQE::findOrFail($dqeId);
            $lignesBPUIds = $request->input('lignes_bpu_ids');
            $rubriqueId = $request->input('rubrique_id');
            
            $lignesCreees = 0;
            $lignesExistantes = 0;

            foreach ($lignesBPUIds as $bpuId) {
                $bpu = \App\Models\Bpu::find($bpuId);
                
                if (!$bpu) {
                    continue;
                }

                // Vérifier si une ligne DQE existe déjà pour ce BPU et cette rubrique
                $ligneExistante = DQELigne::where('dqe_id', $dqeId)
                    ->where('id_rubrique', $rubriqueId)
                    ->where('designation', $bpu->designation)
                    ->first();

                if ($ligneExistante) {
                    $lignesExistantes++;
                    continue;
                }

                // Note: La catégorie est disponible via la relation rubrique, pas stockée directement dans dqe_lignes

                // Créer la nouvelle ligne DQE avec les détails de coût
                $ligneDQE = DQELigne::create([
                    'dqe_id' => $dqeId,
                    'id_rubrique' => $rubriqueId,
                    'designation' => $bpu->designation,
                    'quantite' => $bpu->qte ?? 1,
                    'unite' => $bpu->unite,
                    'pu_ht' => $bpu->pu_ht ?? 0,
                    'montant_ht' => ($bpu->pu_ht ?? 0) * ($bpu->qte ?? 1),
                    // Champs de coût détaillés depuis le BPU
                    'materiaux' => $bpu->materiaux ?? 0,
                    'mo' => $bpu->main_oeuvre ?? 0,
                    'materiel' => $bpu->materiel ?? 0,
                    'frais_chantier' => $bpu->frais_chantier ?? 0,
                    'frais_generaux' => $bpu->frais_general ?? 0,
                    'benefice' => $bpu->marge_nette ?? 0
                ]);

                $lignesCreees++;
            }

            // Mettre à jour les totaux du DQE
            $dqe->updateTotals();

            return response()->json([
                'success' => true,
                'lignes_creees' => $lignesCreees,
                'lignes_existantes' => $lignesExistantes,
                'message' => $lignesCreees > 0 
                    ? "$lignesCreees ligne(s) créée(s) avec succès" 
                    : "Aucune nouvelle ligne créée (lignes déjà existantes)"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création des lignes DQE: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valider un DQE
     */
    public function valider(DQE $dqe)
    {
        try {
            // Vérifier si le DQE peut être validé
            if ($dqe->statut !== 'approuvé') {
                return redirect()->route('dqe.index')->with('error', 'Ce DQE ne peut pas être validé car son statut est : ' . $dqe->statut . '. Il doit être approuvé avant validation.');
            }

            // Mettre à jour le statut
            $dqe->update([
                'statut' => 'validé',
                'date_validation' => now()
            ]);

            return redirect()->route('dqe.index')->with('success', 'DQE validé avec succès');

        } catch (\Exception $e) {
            return redirect()->route('dqe.index')->with('error', 'Erreur lors de la validation du DQE: ' . $e->getMessage());
        }
    }

    /**
     * Soumettre un DQE pour approbation
     */
    public function soumettre(DQE $dqe)
    {
        try {
            // Vérifier si le DQE peut être soumis
            if ($dqe->statut !== 'brouillon') {
                return redirect()->route('dqe.index')->with('error', 'Ce DQE ne peut pas être soumis car son statut est : ' . $dqe->statut);
            }

            // Vérifier que le DQE a au moins une ligne
            if ($dqe->lignes->count() === 0) {
                return redirect()->route('dqe.index')->with('error', 'Ce DQE ne peut pas être soumis car il ne contient aucune ligne.');
            }

            // Mettre à jour le statut
            $dqe->update([
                'statut' => 'soumis'
            ]);

            return redirect()->route('dqe.index')->with('success', 'DQE soumis pour approbation avec succès');

        } catch (\Exception $e) {
            return redirect()->route('dqe.index')->with('error', 'Erreur lors de la soumission du DQE: ' . $e->getMessage());
        }
    }

    /**
     * Approuver un DQE
     */
    public function approuver(DQE $dqe)
    {
        try {
            // Vérifier si le DQE peut être approuvé
            if ($dqe->statut !== 'soumis') {
                return redirect()->route('dqe.index')->with('error', 'Ce DQE ne peut pas être approuvé car son statut est : ' . $dqe->statut);
            }

            // Mettre à jour le statut
            $dqe->update([
                'statut' => 'approuvé'
            ]);

            return redirect()->route('dqe.index')->with('success', 'DQE approuvé avec succès');

        } catch (\Exception $e) {
            return redirect()->route('dqe.index')->with('error', 'Erreur lors de l\'approbation du DQE: ' . $e->getMessage());
        }
    }

    /**
     * Rejeter un DQE
     */
    public function rejeter(DQE $dqe)
    {
        try {
            // Vérifier si le DQE peut être rejeté
            if (!in_array($dqe->statut, ['brouillon', 'soumis'])) {
                return redirect()->route('dqe.index')->with('error', 'Ce DQE ne peut pas être rejeté car son statut est : ' . $dqe->statut);
            }

            // Mettre à jour le statut
            $dqe->update([
                'statut' => 'rejeté',
                'date_rejet' => now()
            ]);

            return redirect()->route('dqe.index')->with('success', 'DQE rejeté avec succès');

        } catch (\Exception $e) {
            return redirect()->route('dqe.index')->with('error', 'Erreur lors du rejet du DQE: ' . $e->getMessage());
        }
    }
}