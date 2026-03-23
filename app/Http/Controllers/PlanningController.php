<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\DQE;
use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlanningController extends Controller
{
    public function index()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')->with('error', 'Veuillez sélectionner un contrat');
        }

        // Récupérer les DQE validés du contrat
        $dqes = DQE::where('contrat_id', $contratId)
            ->whereIn('statut', ['validé', 'approuvé'])
            ->get();

        // Récupérer les catégories et sous-catégories des DQE validés
        $categories = [];
        foreach ($dqes as $dqe) {
            $cats = CategorieRubrique::where('id_qe', $dqe->id)
                ->with(['sousCategories'])
                ->get();
            
            foreach ($cats as $cat) {
                $categories[] = $cat;
            }
        }

        // Récupérer tous les plannings du contrat
        $plannings = Planning::where('id_contrat', $contratId)
            ->orderBy('date_debut')
            ->get();

        // Calculer la période d'affichage (toute l'année en cours)
        $dateDebut = Carbon::now()->startOfYear();
        $dateFin = Carbon::now()->endOfYear();
        
        // Générer tous les jours de l'année
        $jours = $this->genererJours($dateDebut, $dateFin);

        return view('planning.index', compact('categories', 'plannings', 'jours', 'dateDebut', 'dateFin'));
    }

    private function genererJours($dateDebut, $dateFin)
    {
        $jours = [];
        $current = $dateDebut->copy();
        $moisActuel = '';
        $semaineNum = 1;

        while ($current <= $dateFin) {
            $mois = $current->locale('fr')->translatedFormat('F');
            
            // Déterminer le numéro de semaine (afficher S1, S2... uniquement le lundi)
            $numSemaine = '';
            if ($current->dayOfWeek == 1) { // 1 = Lundi
                $numSemaine = 'S' . $semaineNum;
                $semaineNum++;
            }

            $jours[] = [
                'date' => $current->copy(),
                'jour' => $current->format('d'),
                'jourSemaine' => $current->dayOfWeek, // 0=dimanche, 1=lundi, ..., 6=samedi
                'mois' => $mois,
                'moisChange' => ($mois !== $moisActuel),
                'isWeekend' => $current->isWeekend(),
                'numSemaine' => $numSemaine
            ];

            $moisActuel = $mois;
            $current->addDay();
        }

        return $jours;
    }

    public function store(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné'
            ], 400);
        }

        $request->validate([
            'id_souscategorie' => 'nullable|integer',
            'nom_tache_planning' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:non_demarre,en_cours,retard,termine'
        ]);

        $planning = Planning::create([
            'id_contrat' => $contratId,
            'id_souscategorie' => $request->id_souscategorie,
            'nom_tache_planning' => $request->nom_tache_planning,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->statut
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tâche de planning créée avec succès',
            'planning' => $planning
        ]);
    }

    public function update(Request $request, Planning $planning)
    {
        $request->validate([
            'nom_tache_planning' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'nullable|in:non_demarre,en_cours,retard,termine'
        ]);

        $planning->update($request->only([
            'nom_tache_planning',
            'date_debut',
            'date_fin',
            'statut'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Planning mis à jour avec succès',
            'planning' => $planning->fresh()
        ]);
    }

    public function destroy(Planning $planning)
    {
        $planning->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tâche de planning supprimée avec succès'
        ]);
    }

    public function updateField(Request $request, Planning $planning)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'statut') {
            $request->validate([
                'value' => 'required|in:non_demarre,en_cours,retard,termine'
            ]);
            $planning->statut = $value;
        } elseif ($field === 'date_debut' || $field === 'date_fin') {
            $request->validate([
                'value' => 'required|date'
            ]);
            $planning->{$field} = $value;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Champ invalide'
            ], 400);
        }

        $planning->save();

        return response()->json([
            'success' => true,
            'message' => 'Planning mis à jour avec succès',
            'planning' => $planning->fresh()
        ]);
    }
}
