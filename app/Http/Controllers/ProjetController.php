<?php
namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Article;
use App\Models\BU;
use App\Models\SecteurActivite;
use App\Models\ClientFournisseur;
use App\Models\User;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\Commune;
use App\Models\Quartier;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProjetController extends Controller
{
    public function index()
    {
        $projets = Projet::with(['clientFournisseur', 'chefProjet', 'conducteurTravaux'])->get();
        return view('projets.index', compact('projets'));
    }


    public function create()
    {
        $id_bu = session('selected_bu');
        
        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        // Récupérer les clients associés à l'ID du bus
        $clients = ClientFournisseur::where('type', 'client')
                                   ->where('id_bu', $id_bu)  // Filtrage selon l'ID du bus
                                   ->get();
        $secteurs = SecteurActivite::all();
        $bus = BU::all();
        
        // Récupérer les employés pour les sélecteurs
        $chefs = User::chefsProjets()->actifs()->get();
        $conducteurs = User::conducteursTravaux()->actifs()->get();
        
        // Récupérer les données de localisation
        $pays = Pays::all();
        $villes = Ville::all();
        $communes = Commune::all();
        $quartiers = Quartier::all();
        $secteursLocalisation = Secteur::all();

        return view('projets.create', compact('clients', 'secteurs', 'bus', 'chefs', 'conducteurs', 'pays', 'villes', 'communes', 'quartiers', 'secteursLocalisation'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'date_creation' => 'required|date',
            'nom_projet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'client' => 'required|string',
            'secteur_activite_id' => 'required|exists:secteur_activites,id',
            'chef_projet_id' => 'nullable|exists:users,id',
            'conducteur_travaux_id' => 'nullable|exists:users,id',
            'hastva' => 'boolean',
            'tva_achat' => 'boolean',
            'montant_global' => 'nullable|numeric|min:0',
            'chiffre_affaire_global' => 'nullable|numeric|min:0',
            'total_depenses' => 'nullable|numeric|min:0',
            'statut' => 'required|in:en cours,terminé,annulé',
            'bu_id' => 'required|exists:bus,id',
            'pays_id' => 'required|exists:pays,id',
            'ville_id' => 'required|exists:villes,id',
            'commune_id' => 'required|exists:communes,id',
            'quartier_id' => 'required|exists:quartiers,id',
            'secteur_id' => 'required|exists:secteurs,id'
        ]);
        
        $lastReference = \App\Models\Reference::where('nom', 'Code Projet')
            ->latest('created_at')
            ->first();

        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'Prj_0000';
        $newReference = 'Prj_' . now()->format('YmdHis');
        
        // Préparer les données
        $data = $request->all();
        $data['ref_projet'] = $newReference;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        
        Projet::create($data);
        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }

    public function show(Projet $projet)
    {
        session([
            'projet_id' => $projet->id,
            'projet_nom' => $projet->nom_projet
        ]);
        
        // Charger les relations nécessaires pour les 4 zones
        $projet->load([
            'clientFournisseur.contactPersons',
            'chefProjet',
            'conducteurTravaux',
            'secteurActivite',
            'bu',
            'createdBy',
            'updatedBy',
            'contrats'
        ]);
        
        $projets = Projet::all();
        $articles = Article::all();
        
        return view('projets.show', compact('projet','projets','articles'));
    }

    public function edit(Projet $projet)
    {
        $id_bu = session('selected_bu');
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un BU avant d\'accéder à cette page.']);
        }
    
        $clients = ClientFournisseur::where('type', 'client')->where('id_bu', $id_bu)->get();
        $secteurs = SecteurActivite::all();
        $bus = BU::all();
        
        // Récupérer les employés pour les sélecteurs
        $chefsProjet = User::chefsProjets()->actifs()->get();
        $conducteursTravaux = User::conducteursTravaux()->actifs()->get();
        
        // Récupérer les données de localisation
        $pays = Pays::all();
        $villes = Ville::all();
        $communes = Commune::all();
        $quartiers = Quartier::all();
        $secteursLocalisation = Secteur::all();
    
        return view('projets.edit', compact('projet', 'clients', 'secteurs', 'bus', 'chefsProjet', 'conducteursTravaux', 'pays', 'villes', 'communes', 'quartiers', 'secteursLocalisation'));
    }
    public function update(Request $request, Projet $projet)
    {
        $request->validate([
            'date_creation' => 'required|date',
            'nom_projet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'client' => 'required|string',
            'secteur_activite_id' => 'required|exists:secteur_activites,id',
            'chef_projet_id' => 'nullable|exists:users,id',
            'conducteur_travaux_id' => 'nullable|exists:users,id',
            'hastva' => 'boolean',
            'tva_achat' => 'boolean',
            'montant_global' => 'nullable|numeric|min:0',
            'chiffre_affaire_global' => 'nullable|numeric|min:0',
            'total_depenses' => 'nullable|numeric|min:0',
            'statut' => 'required|in:en cours,terminé,annulé',
            'bu_id' => 'required|exists:bus,id',
            'pays_id' => 'required|exists:pays,id',
            'ville_id' => 'required|exists:villes,id',
            'commune_id' => 'required|exists:communes,id',
            'quartier_id' => 'required|exists:quartiers,id',
            'secteur_id' => 'required|exists:secteurs,id'
        ]);

        // Préparer les données avec updated_by
        $data = $request->all();
        $data['updated_by'] = auth()->id();
        
        $projet->update($data);
        return redirect()->route('projets.index')->with('success', 'Projet mis à jour.');
    }

    public function destroy(Projet $projet)
    {
        $projet->delete();
        return redirect()->route('projets.index')->with('success', 'Projet supprimé.');
    }

    public function changeProject(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id'
        ]);

        $projet = Projet::findOrFail($request->projet_id);
        
        session([
            'projet_id' => $projet->id,
            'projet_nom' => $projet->nom_projet
        ]);

        return redirect()->back()->with('success', 'Projet changé avec succès vers: ' . $projet->nom_projet);
    }

    public function selectForContract(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id'
        ]);

        $projet = Projet::findOrFail($request->projet_id);
        
        session([
            'projet_id' => $projet->id,
            'projet_nom' => $projet->nom_projet
        ]);

        return redirect()->route('contrats.create')->with('success', 'Projet sélectionné: ' . $projet->nom_projet);
    }
}
