<?php

namespace App\Http\Controllers;

use App\Models\FraisGeneraux;
use App\Models\FraisGenerauxParent;
use App\Models\DQE;
use App\Models\Rubrique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FraisGenerauxController extends Controller
{
    public function generate(Request $request, $dqeId)
    {
        try {
            DB::beginTransaction();

            $dqe = DQE::findOrFail($dqeId);
            $rubriques = $dqe->lignes()->distinct()->pluck('id_rubrique');
            $totalGenere = 0;

            // Créer un parent pour ce DQE
            $parent = FraisGenerauxParent::create([
                'contrat_id' => $dqe->contrat_id,
                'dqe_id' => $dqeId,
                'type' => FraisGenerauxParent::TYPE_PREVISIONNEL,
                'ref' => 'FG-' . $dqe->code . '-' . date('YmdHis'),
                'montant_total' => 0,
                'statut' => FraisGenerauxParent::STATUT_BROUILLON
            ]);

            foreach ($rubriques as $rubriqueId) {
                // Récupérer les lignes DQE pour cette rubrique
                $lignesDQE = $dqe->lignes()->where('id_rubrique', $rubriqueId)->get();
                $rubrique = Rubrique::find($rubriqueId);

                if (!$rubrique) continue;

                foreach ($lignesDQE as $ligne) {
                    // Générer les frais généraux à partir des données de la ligne DQE
                    FraisGeneraux::create([
                        'parent_id' => $parent->id,
                        'contrat_id' => $dqe->contrat_id,
                        'dqe_id' => $dqeId,
                        'id_rubrique' => $rubriqueId,
                        'designation' => $ligne->designation,
                        'unite' => $ligne->unite,
                        'quantite' => $ligne->quantite,
                        'pu_ht' => $ligne->frais_generaux ?? 0
                    ]);
                }
                $totalGenere++;
            }

            // Mettre à jour le total du parent
            $parent->updateTotal();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Frais généraux générés avec succès pour {$totalGenere} rubrique(s)"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index($dqeId)
    {
        $dqe = DQE::findOrFail($dqeId);
        $fraisGeneraux = FraisGeneraux::with(['dqe', 'rubrique'])
            ->where('dqe_id', $dqeId)
            ->get();

        return view('frais-generaux.index', compact('fraisGeneraux', 'dqeId', 'dqe'));
    }
}