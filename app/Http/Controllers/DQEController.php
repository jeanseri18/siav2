<?php

namespace App\Http\Controllers;

use App\Models\Bpu;
use App\Models\Contrat;
use App\Models\DQE;
use App\Models\DQELigne;
use App\Models\CategorieRubrique;
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
    
    // Ajouter les catégories pour le modal de génération de DQE
    $categories = CategorieRubrique::with([
        'sousCategories.rubriques.bpus'
    ])->get();
    
    return view('dqe.index', compact('contrat', 'dqes', 'categories'));
}

    /**
     * Afficher les détails d'un DQE
     */
    public function show($id)
    {
        $dqe = DQE::with([
            'lignes.bpu.rubrique.sousCategorie.categorie',
            'contrat'
        ])->findOrFail($id);
        $contrat = $dqe->contrat;
        
        // Organiser les lignes par hiérarchie
        $lignesOrganisees = $this->organiserLignesParHierarchie($dqe->lignes);

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
    $categories = CategorieRubrique::with([
        'sousCategories.rubriques.bpus'
    ])->get();
    
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
            'lignes.bpu.rubrique.sousCategorie.categorie'
        ])->findOrFail($id);
        $contrat = $dqe->contrat;
        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus'
        ])->get();
        
        // Organiser les lignes par hiérarchie
        $lignesOrganisees = $this->organiserLignesParHierarchie($dqe->lignes);
        
        return view('dqe.edit', compact('dqe', 'contrat', 'categories', 'lignesOrganisees'));
    }

    /**
     * Organiser les lignes DQE par hiérarchie (Catégorie > Sous-catégorie > Rubrique)
     */
    private function organiserLignesParHierarchie($lignes)
    {
        $organisation = [];
        
        foreach ($lignes as $ligne) {
            if ($ligne->bpu && $ligne->bpu->rubrique) {
                $rubrique = $ligne->bpu->rubrique;
                $sousCategorie = $rubrique->sousCategorie;
                $categorie = $sousCategorie ? $sousCategorie->categorie : null;
                
                $categorieNom = $categorie ? $categorie->nom : 'Sans catégorie';
                $sousCategorieNom = $sousCategorie ? $sousCategorie->nom : 'Sans sous-catégorie';
                $rubriqueNom = $rubrique->nom;
                
                if (!isset($organisation[$categorieNom])) {
                    $organisation[$categorieNom] = [
                        'categorie' => $categorie,
                        'sousCategories' => []
                    ];
                }
                
                if (!isset($organisation[$categorieNom]['sousCategories'][$sousCategorieNom])) {
                    $organisation[$categorieNom]['sousCategories'][$sousCategorieNom] = [
                        'sousCategorie' => $sousCategorie,
                        'rubriques' => []
                    ];
                }
                
                if (!isset($organisation[$categorieNom]['sousCategories'][$sousCategorieNom]['rubriques'][$rubriqueNom])) {
                    $organisation[$categorieNom]['sousCategories'][$sousCategorieNom]['rubriques'][$rubriqueNom] = [
                        'rubrique' => $rubrique,
                        'lignes' => []
                    ];
                }
                
                $organisation[$categorieNom]['sousCategories'][$sousCategorieNom]['rubriques'][$rubriqueNom]['lignes'][] = $ligne;
            } else {
                // Lignes sans BPU ou rubrique
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
   public function generateFromBPU(Request $request)
{
    $contratId = session('contrat_id');
    
    if (!$contratId) {
        return redirect()->route('contrats.index')
            ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
    }
    
    $contrat = Contrat::findOrFail($contratId);
    
    // Créer le DQE
    $dqe = DQE::create([
        'contrat_id' => $contratId,
        'reference' => 'DQE-' . date('YmdHis'),
        'statut' => 'brouillon',
    ]);

    // Récupérer les BPU sélectionnés
    $bpuIds = $request->bpu_ids ?? [];
    $bpus = Bpu::whereIn('id', $bpuIds)->get();

    // Créer les lignes de DQE
    foreach ($bpus as $bpu) {
        DQELigne::create([
            'dqe_id' => $dqe->id,
            'bpu_id' => $bpu->id,
            'designation' => $bpu->designation,
            'quantite' => 1, // Quantité par défaut
            'unite' => $bpu->unite,
            'pu_ht' => $bpu->pu_ht,
            'montant_ht' => $bpu->pu_ht, // Montant initial = prix unitaire × 1
        ]);
    }

    // Mettre à jour les totaux
    $dqe->updateTotals();

    return redirect()->route('dqe.edit', $dqe->id)
        ->with('success', 'DQE généré avec succès à partir du BPU. Veuillez ajuster les quantités.');
}
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
            'bpu_id' => $bpu->id,
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
                'bpu_id' => $bpu->id,
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
}