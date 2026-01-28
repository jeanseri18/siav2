<?php

namespace App\Http\Controllers;

use App\Models\FraisChantier;
use App\Models\FraisChantierParent;
use App\Models\DQE;
use App\Models\Rubrique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FraisChantierController extends Controller
{
    public function generate(Request $request, $dqeId)
    {
        try {
            DB::beginTransaction();

            $dqe = DQE::findOrFail($dqeId);
            $rubriques = $dqe->lignes()->distinct()->pluck('id_rubrique');
            $totalGenere = 0;

            // Créer un parent pour ce DQE
            $parent = FraisChantierParent::create([
                'contrat_id' => $dqe->contrat_id,
                'dqe_id' => $dqeId,
                'type' => FraisChantierParent::TYPE_PREVISIONNEL,
                'ref' => 'FC-' . $dqe->code . '-' . date('YmdHis'),
                'montant_total' => 0,
                'statut' => FraisChantierParent::STATUT_BROUILLON
            ]);

            foreach ($rubriques as $rubriqueId) {
                // Récupérer les lignes DQE pour cette rubrique
                $lignesDQE = $dqe->lignes()->where('id_rubrique', $rubriqueId)->get();
                $rubrique = Rubrique::find($rubriqueId);

                if (!$rubrique) continue;

                foreach ($lignesDQE as $ligne) {
                    // Générer les frais de chantier à partir des données de la ligne DQE
                    FraisChantier::create([
                        'parent_id' => $parent->id,
                        'contrat_id' => $dqe->contrat_id,
                        'dqe_id' => $dqeId,
                        'id_rubrique' => $rubriqueId,
                        'designation' => $ligne->designation,
                        'unite' => $ligne->unite,
                        'quantite' => $ligne->quantite,
                        'pu_ht' => $ligne->frais_chantier ?? 0
                    ]);
                }
                $totalGenere++;
            }

            // Mettre à jour le total du parent
            $parent->updateTotal();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Frais de chantier générés avec succès pour {$totalGenere} rubrique(s)"
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
        $fraisChantiers = FraisChantier::with(['dqe', 'rubrique'])
            ->where('dqe_id', $dqeId)
            ->get();

        return view('frais-chantier.index', compact('fraisChantiers', 'dqeId', 'dqe'));
    }
}