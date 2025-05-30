<?php

namespace App\Http\Controllers;

use App\Models\Bpu;
use App\Models\Contrat;
use App\Models\DQE;
use App\Models\DQELigne;
use App\Models\CategorieRubrique;
use Illuminate\Http\Request;

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
    $dqes = DQE::where('contrat_id', $contratId)->get();
    
    // Ajouter les catégories pour le modal de génération de DQE
    $categories = CategorieRubrique::with([
        'sousCategories.rubriques.bpus'
    ])->get();
    
    return view('dqe.index', compact('contrat', 'dqes', 'categories'));
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
        $dqe = DQE::with('lignes.bpu')->findOrFail($id);
        $contrat = $dqe->contrat;
        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus'
        ])->get();
        
        return view('dqe.edit', compact('dqe', 'contrat', 'categories'));
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

        $dqe->update([
            'reference' => $request->reference,
            'notes' => $request->notes,
            'statut' => $request->statut,
        ]);

        return redirect()->route('dqe.edit', $dqe->id)
            ->with('success', 'DQE mis à jour avec succès.');
    }

    /**
     * Supprimer un DQE
     */
    public function destroy($id)
    {
        $dqe = DQE::findOrFail($id);
        $contratId = $dqe->contrat_id;
        
        $dqe->delete();

        return redirect()->route('dqe.index', $contratId)
            ->with('success', 'DQE supprimé avec succès.');
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
     * Mettre à jour une ligne de DQE
     */
    public function updateLine(Request $request, $id, $ligneId)
    {
        $ligne = DQELigne::findOrFail($ligneId);
        
        $request->validate([
            'quantite' => 'required|numeric|min:0.01',
        ]);

        $ligne->quantite = $request->quantite;
        $ligne->calculerMontant();

        return redirect()->route('dqe.edit', $id)
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
}