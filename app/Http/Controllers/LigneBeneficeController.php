<?php

namespace App\Http\Controllers;

use App\Models\LigneBenefice;
use App\Models\LigneBeneficeParent;
use App\Models\DQE;
use App\Models\Rubrique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LigneBeneficeController extends Controller
{
    public function generate(Request $request, $dqeId)
    {
        try {
            DB::beginTransaction();

            $dqe = DQE::findOrFail($dqeId);
            $rubriques = $dqe->lignes()->distinct()->pluck('id_rubrique');
            $totalGenere = 0;

            // Créer un parent pour ce DQE
            $parent = LigneBeneficeParent::create([
                'contrat_id' => $dqe->contrat_id,
                'dqe_id' => $dqeId,
                'type' => LigneBeneficeParent::TYPE_PREVISIONNEL,
                'ref' => 'BEN-' . $dqe->code . '-' . date('YmdHis'),
                'montant_total' => 0,
                'statut' => LigneBeneficeParent::STATUT_BROUILLON
            ]);

            foreach ($rubriques as $rubriqueId) {
                // Récupérer les lignes DQE pour cette rubrique
                $lignesDQE = $dqe->lignes()->where('id_rubrique', $rubriqueId)->get();
                $rubrique = Rubrique::find($rubriqueId);

                if (!$rubrique) continue;

                foreach ($lignesDQE as $ligne) {
                    // Générer le bénéfice à partir des données de la ligne DQE
                    LigneBenefice::create([
                        'parent_id' => $parent->id,
                        'contrat_id' => $dqe->contrat_id,
                        'dqe_id' => $dqeId,
                        'id_rubrique' => $rubriqueId,
                        'designation' => $ligne->designation,
                        'unite' => $ligne->unite,
                        'quantite' => $ligne->quantite,
                        'pu_ht' => $ligne->benefice ?? 0
                    ]);
                }
                $totalGenere++;
            }

            // Mettre à jour le total du parent
            $parent->updateTotal();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bénéfice généré avec succès pour {$totalGenere} rubrique(s)"
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
        $ligneBenefices = LigneBenefice::with(['dqe', 'rubrique'])
            ->where('dqe_id', $dqeId)
            ->get();

        return view('ligne-benefice.index', compact('ligneBenefices', 'dqeId', 'dqe'));
    }
}