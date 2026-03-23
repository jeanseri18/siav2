<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use App\Models\Lot;
use App\Models\Niveau;
use App\Models\Localisation;
use App\Models\CorpsDeMetier;
use App\Models\CorpMetier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TacheController extends Controller
{
    public function index()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')->with('error', 'Veuillez sélectionner un contrat');
        }
        
        $lots = Lot::with([
            'niveaux.localisations.corpsDeMetiers.corpMetier',
            'niveaux.localisations.corpsDeMetiers.taches'
        ])
        ->where('id_contrat', $contratId)
        ->get();

        // Charger tous les corps de métier existants depuis la DB
        $corpsMetiers = CorpMetier::orderBy('nom')->get();

        return view('taches.index', compact('lots', 'corpsMetiers'));
    }

    // Gestion des lots
    public function storeLot(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné'
            ], 400);
        }
        
        $request->validate([
            'titre' => 'required|string|max:255'
        ]);

        $lot = Lot::create([
            'titre' => $request->titre,
            'id_contrat' => $contratId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lot créé avec succès',
            'lot' => $lot
        ]);
    }

    // Gestion des niveaux
    public function storeNiveau(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné'
            ], 400);
        }
        
        $request->validate([
            'id_lot' => 'required|exists:lots,id',
            'titre_niveau' => 'required|string|max:255'
        ]);

        $niveau = Niveau::create([
            'id_lot' => $request->id_lot,
            'titre_niveau' => $request->titre_niveau,
            'id_contrat' => $contratId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Niveau créé avec succès',
            'niveau' => $niveau
        ]);
    }

    // Gestion des localisations
    public function storeLocalisation(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné'
            ], 400);
        }
        
        $request->validate([
            'id_niveau' => 'required|exists:niveaux,id',
            'titre_localisation' => 'required|string|max:255'
        ]);

        $localisation = Localisation::create([
            'id_niveau' => $request->id_niveau,
            'titre_localisation' => $request->titre_localisation,
            'id_contrat' => $contratId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Localisation créée avec succès',
            'localisation' => $localisation
        ]);
    }

    // Gestion des corps de métier
    public function storeCorpsDeMetier(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné'
            ], 400);
        }
        
        $request->validate([
            'id_localisation' => 'required|exists:localisations,id',
            'id_corpmetier' => 'required|exists:corp_metiers,id'
        ]);

        $corpsDeMetier = CorpsDeMetier::create([
            'id_localisation' => $request->id_localisation,
            'id_corpmetier' => $request->id_corpmetier,
            'id_contrat' => $contratId
        ]);

        // Charger la relation pour retourner le nom
        $corpsDeMetier->load('corpMetier');

        return response()->json([
            'success' => true,
            'message' => 'Corps de métier associé avec succès',
            'corpsDeMetier' => $corpsDeMetier
        ]);
    }

    // Gestion des tâches
    public function storeTache(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun contrat sélectionné'
            ], 400);
        }
        
        $request->validate([
            'id_corps_de_metier' => 'required|exists:corps_de_metiers,id',
            'description' => 'required|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'nbre_jr_previsionnelle' => 'required|integer|min:0',
            'progression' => 'nullable|numeric|min:0|max:100',
            'statut' => 'required|in:non_debute,en_cours,suspendu,receptionne,termine',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');
        $data['id_contrat'] = $contratId;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('taches', 'public');
            $data['image'] = $imagePath;
        }

        $tache = Tache::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tâche créée avec succès',
            'tache' => $tache
        ]);
    }

    public function updateTache(Request $request, Tache $tache)
    {
        $request->validate([
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'nbre_jr_previsionnelle' => 'nullable|integer|min:0',
            'progression' => 'nullable|numeric|min:0|max:100',
            'statut' => 'nullable|in:non_debute,en_cours,suspendu,receptionne,termine',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($tache->image) {
                Storage::disk('public')->delete($tache->image);
            }
            $imagePath = $request->file('image')->store('taches', 'public');
            $data['image'] = $imagePath;
        }

        $tache->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Tâche mise à jour avec succès',
            'tache' => $tache->fresh()
        ]);
    }

    public function deleteTache(Tache $tache)
    {
        try {
            if ($tache->image) {
                Storage::disk('public')->delete($tache->image);
            }

            $tache->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tâche supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    // Méthodes pour obtenir les données hiérarchiques
    public function getNiveauxByLot($lotId)
    {
        $niveaux = Niveau::where('id_lot', $lotId)->get();
        return response()->json($niveaux);
    }

    public function getLocalisationsByNiveau($niveauId)
    {
        $localisations = Localisation::where('id_niveau', $niveauId)->get();
        return response()->json($localisations);
    }

    public function getCorpsDeMetiersByLocalisation($localisationId)
    {
        $corpsDeMetiers = CorpsDeMetier::with('corpMetier')
            ->where('id_localisation', $localisationId)
            ->get();
        return response()->json($corpsDeMetiers);
    }

    // Mise à jour rapide d'un champ (progression ou statut)
    public function updateField(Request $request, Tache $tache)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        // Valider selon le champ
        if ($field === 'progression') {
            $request->validate([
                'value' => 'required|numeric|min:0|max:100'
            ]);
            $tache->progression = $value;
        } elseif ($field === 'statut') {
            $request->validate([
                'value' => 'required|in:non_debute,en_cours,suspendu,receptionne,termine'
            ]);
            $tache->statut = $value;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Champ invalide'
            ], 400);
        }

        $tache->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($field) . ' mis à jour avec succès',
            'tache' => $tache->fresh()
        ]);
    }
}
