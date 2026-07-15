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
use App\Models\Secteur;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Support\PdfBranding;

class ProjetController extends Controller
{
    public function index()
    {
        $projets = $this->projetsForListing();
        return view('projets.index', compact('projets'));
    }

    public function exportPdf()
    {
        $buId = session('selected_bu');
        $projets = $this->projetsForListing();
        $pdfBranding = PdfBranding::forBu($buId ? (int) $buId : null);

        $pdf = Pdf::loadView('projets.liste-export', [
            'projets' => $projets,
            'pdfBranding' => $pdfBranding,
            'printMode' => false,
            'documentTitle' => 'Liste des projets',
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('liste-projets-'.now()->format('Y-m-d').'.pdf');
    }

    private function projetsForListing()
    {
        return Projet::with(['clientFournisseur', 'chefProjet', 'conducteurTravaux', 'secteurActivite', 'bu'])
            ->orderByDesc('date_creation')
            ->orderByDesc('id')
            ->get();
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
        $buCourante = BU::find($id_bu);

        // Récupérer les employés pour les sélecteurs
        $chefs = User::chefsProjets()->actifs()->get();
        $conducteurs = User::conducteursTravaux()->actifs()->get();
        
        // Récupérer les données de localisation (listes communes / secteurs chargées en AJAX dans la vue)
        $pays = Pays::all();
        $villes = Ville::all();

        return view('projets.create', compact('clients', 'secteurs', 'buCourante', 'chefs', 'conducteurs', 'pays', 'villes'));
    }



    public function store(Request $request)
    {
        $id_bu = session('selected_bu');
        if (! $id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $request->validate([
            'nom_projet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client' => 'required|string',
            'secteur_activite_id' => 'required|exists:secteur_activites,id',
            'chef_projet_id' => 'nullable|exists:users,id',
            'conducteur_travaux_id' => 'nullable|exists:users,id',
            'montant_global' => 'nullable|numeric|min:0',
            'chiffre_affaire_global' => 'nullable|numeric|min:0',
            'total_depenses' => 'nullable|numeric|min:0',
            'statut' => 'nullable|in:non débuté,en cours,terminé,annulé',
            'pays_id' => 'nullable|exists:pays,id',
            'ville_id' => 'nullable|exists:villes,id',
            'commune_id' => 'nullable|exists:communes,id',
            'quartier_id' => 'nullable|exists:quartiers,id',
            'secteur_id' => 'nullable|exists:secteurs,id'
        ]);
        
        $lastReference = \App\Models\Reference::where('nom', 'Code Projet')
            ->latest('created_at')
            ->first();

        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'Prj_0000';
        $newReference = 'Prj_' . now()->format('YmdHis');
        
        // Préparer les données (dates projet = agrégat des contrats, pas de saisie ici)
        $data = $request->except(['hastva', 'tva_achat', 'date_debut', 'date_fin']);
        if (! empty($data['secteur_id']) && empty($data['quartier_id'])) {
            $data['quartier_id'] = Secteur::query()->whereKey($data['secteur_id'])->value('quartier_id');
        }
        $data['bu_id'] = $id_bu;
        $data['date_debut'] = null;
        $data['date_fin'] = null;
        $data['hastva'] = $request->has('hastva');
        $data['tva_achat'] = $request->has('tva_achat');
        $data['date_creation'] = now(); // Ajout automatique de la date de création
        $data['ref_projet'] = $newReference;
        $data['montant_global'] = null;
        $data['chiffre_affaire_global'] = null;
        $data['total_depenses'] = null;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        $data['statut'] = $request->input('statut', 'non débuté');
        
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
            'contrats.dqes',
        ]);

        $projet->syncFinancialAggregates();
        
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

        if ((int) $projet->bu_id !== (int) $id_bu) {
            return redirect()->route('projets.index')
                ->with('error', 'Ce projet n\'appartient pas à la BU actuellement sélectionnée. Changez d\'unité ou choisissez un autre projet.');
        }

        $clients = ClientFournisseur::where('type', 'client')->where('id_bu', $id_bu)->get();
        $secteurs = SecteurActivite::all();
        $buCourante = BU::find($id_bu);

        // Récupérer les employés pour les sélecteurs
        $chefsProjet = User::chefsProjets()->actifs()->get();
        $conducteursTravaux = User::conducteursTravaux()->actifs()->get();
        
        // Récupérer les données de localisation
        $pays = Pays::all();
        $villes = Ville::all();
    
        return view('projets.edit', compact('projet', 'clients', 'secteurs', 'buCourante', 'chefsProjet', 'conducteursTravaux', 'pays', 'villes'));
    }
    public function update(Request $request, Projet $projet)
    {
        $id_bu = session('selected_bu');
        if (! $id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un BU avant d\'accéder à cette page.']);
        }
        if ((int) $projet->bu_id !== (int) $id_bu) {
            return redirect()->route('projets.index')
                ->with('error', 'Ce projet n\'appartient pas à la BU actuellement sélectionnée.');
        }

        $request->validate([
            'nom_projet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client' => 'required|string',
            'secteur_activite_id' => 'required|exists:secteur_activites,id',
            'chef_projet_id' => 'nullable|exists:users,id',
            'conducteur_travaux_id' => 'nullable|exists:users,id',
            'montant_global' => 'nullable|numeric|min:0',
            'chiffre_affaire_global' => 'nullable|numeric|min:0',
            'total_depenses' => 'nullable|numeric|min:0',
            'statut' => 'required|in:non débuté,en cours,terminé,annulé',
            'pays_id' => 'nullable|exists:pays,id',
            'ville_id' => 'nullable|exists:villes,id',
            'commune_id' => 'nullable|exists:communes,id',
            'quartier_id' => 'nullable|exists:quartiers,id',
            'secteur_id' => 'nullable|exists:secteurs,id'
        ]);

        // Préparer les données avec updated_by (dates projet gérées par les contrats)
        $data = $request->except(['hastva', 'tva_achat', 'date_creation', 'date_debut', 'date_fin']);
        if (! empty($data['secteur_id']) && empty($data['quartier_id'])) {
            $data['quartier_id'] = Secteur::query()->whereKey($data['secteur_id'])->value('quartier_id');
        }
        $data['bu_id'] = $id_bu;
        $data['hastva'] = $request->has('hastva');
        $data['tva_achat'] = $request->has('tva_achat');
        $data['updated_by'] = auth()->id();
        
        $projet->update($data);
        return redirect()->route('projets.index')->with('success', 'Projet mis à jour.');
    }

    public function updateStatut(Request $request, Projet $projet)
    {
        $id_bu = session('selected_bu');
        if (! $id_bu || (int) $projet->bu_id !== (int) $id_bu) {
            return redirect()->route('projets.index')
                ->with('error', 'Action non autorisée pour ce projet.');
        }

        $request->validate([
            'statut' => 'required|in:non débuté,en cours,terminé,annulé',
        ]);

        $projet->update([
            'statut' => $request->statut,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('projets.index')->with('success', 'Statut du projet mis à jour.');
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
