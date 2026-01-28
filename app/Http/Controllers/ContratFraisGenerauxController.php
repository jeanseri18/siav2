<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\FraisGenerauxParent;
use App\Models\DQE;
use App\Models\FraisGeneraux;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContratFraisGenerauxController extends Controller
{
    public function index($contratId)
    {
        $contrat = Contrat::findOrFail($contratId);
        
        // Récupérer tous les parents de frais généraux du contrat
        $fraisGenerauxParents = FraisGenerauxParent::with(['dqe'])
            ->where('contrat_id', $contratId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('contrats.frais-generaux.index', compact(
            'contrat',
            'fraisGenerauxParents'
        ));
    }

    public function show($contratId, $parentId)
    {
        $contrat = Contrat::findOrFail($contratId);
        $parent = FraisGenerauxParent::with(['dqe', 'lignes.rubrique'])
            ->where('contrat_id', $contratId)
            ->findOrFail($parentId);
        
        return view('contrats.frais-generaux.show', compact(
            'contrat',
            'parent'
        ));
    }

    public function showParent($contratId, $parentId)
    {
        return $this->show($contratId, $parentId);
    }

    public function store(Request $request, $contratId)
    {
        $contrat = Contrat::findOrFail($contratId);
        
        // Créer un nouveau parent de frais généraux
        $parent = new FraisGenerauxParent();
        $parent->contrat_id = $contratId;
        $parent->ref = 'FG-' . $contrat->code . '-' . date('YmdHis');
        $parent->montant_total = 0;
        $parent->type = 'réalisé';
        $parent->statut = FraisGenerauxParent::STATUT_BROUILLON;
        $parent->save();
        
        return redirect()->route('contrats.frais-generaux.show', [$contratId, $parent->id])
            ->with('success', 'Document créé avec succès');
    }

    public function storeLigne(Request $request, Contrat $contrat, FraisGenerauxParent $parent)
    {
        try {
            $validated = $request->validate([
                'designation' => 'required|string|max:255',
                'unite' => 'required|string|max:50',
                'quantite' => 'required|numeric|min:0',
                'pu_ht' => 'required|numeric|min:0',
            ]);

            // Créer la nouvelle ligne de frais généraux
            $ligne = FraisGeneraux::create([
                'parent_id' => $parent->id,
                'contrat_id' => $contrat->id,
                'dqe_id' => $parent->dqe_id,
                'designation' => $validated['designation'],
                'unite' => $validated['unite'],
                'quantite' => $validated['quantite'],
                'pu_ht' => $validated['pu_ht'],
            ]);

            // Recalculer le montant total du parent
            $parent->updateTotal();

            return response()->json([
                'success' => true,
                'message' => 'Ligne ajoutée avec succès!',
                'ligne' => $ligne
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de la ligne : ' . $e->getMessage()
            ], 422);
        }
    }
}