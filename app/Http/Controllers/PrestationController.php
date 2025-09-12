<?php
namespace App\Http\Controllers;

use App\Models\Prestation;
use App\Models\Artisan;
use App\Models\Contrat;
use App\Models\CorpMetier;
use App\Models\ComptePrestation;
use Illuminate\Http\Request;

class PrestationController extends Controller
{
    public function index() {
        $prestations = Prestation::with(['artisan', 'contrat', 'corpMetier'])->get();
        
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

    public function create() {
        $projet_id = session('projet_id');
        $contrats = Contrat::where('id_projet', $projet_id)->get();
        $artisans = Artisan::all();
        $corpMetiers = CorpMetier::all();
        return view('prestations.create', compact('contrats', 'artisans', 'corpMetiers'));
    }

    public function store(Request $request) {
        $request->validate([
            'id_artisan' => 'nullable|exists:artisan,id',
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
        $corpMetiers = CorpMetier::all();
        return view('prestations.edit', compact('prestation', 'contrats', 'artisans', 'corpMetiers'));
    }

    public function update(Request $request, Prestation $prestation) {
        $request->validate([
            'id_artisan' => 'nullable|exists:artisan,id',
            'id_contrat' => 'required',
            'corps_metier_id' => 'nullable|exists:corp_metiers,id',
            'prestation_titre' => 'required',
            'detail' => 'required',
            'montant' => 'nullable|numeric',
            'taux_avancement' => 'nullable|integer|min:0|max:100',
            'statut' => 'required|string'
        ]);

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
                          ->get(['id', 'nom', 'prenoms', 'tel1']);
        
        return response()->json([
            'artisans' => $artisans
        ]);
    }

    /**
     * Affecter un artisan à une prestation
     */
    public function affecterArtisan(Request $request, Prestation $prestation)
    {
        $request->validate([
            'id_artisan' => 'required|exists:artisan,id',
            'date_affectation' => 'nullable|date'
        ]);

        $prestation->update([
            'id_artisan' => $request->id_artisan,
            'date_affectation' => $request->date_affectation ?? now()->format('Y-m-d')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Artisan affecté avec succès'
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
     * Récupérer les décomptes d'une prestation
     */
    public function getDecomptes(Prestation $prestation)
    {
        $comptes = $prestation->comptes()
                             ->with('createdBy')
                             ->orderBy('date_compte', 'desc')
                             ->get();
        
        $totauxParType = [
            'materiel' => $comptes->where('type_compte', 'materiel')->sum('montant'),
            'main_oeuvre' => $comptes->where('type_compte', 'main_oeuvre')->sum('montant'),
            'transport' => $comptes->where('type_compte', 'transport')->sum('montant'),
            'autres' => $comptes->where('type_compte', 'autres')->sum('montant')
        ];
        
        $totalGeneral = $comptes->sum('montant');
        
        return view('prestations.partials.decomptes', compact('prestation', 'comptes', 'totauxParType', 'totalGeneral'))->render();
    }

    /**
     * Récupérer les informations de l'artisan actuel et la liste des artisans disponibles
     */
    public function getArtisanInfo(Prestation $prestation)
    {
        $artisanActuel = $prestation->artisan ? $prestation->artisan->nom . ' ' . $prestation->artisan->prenom : null;
        
        $artisansDisponibles = Artisan::where('id', '!=', $prestation->id_artisan)
                                     ->orderBy('nom')
                                     ->get(['id', 'nom', 'prenom', 'telephone']);
        
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
}
