<?php
namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Article;
use App\Models\BU;
use App\Models\SecteurActivite;
use App\Models\ClientFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProjetController extends Controller
{
    public function index()
    {
        $projets = Projet::all();
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

    return view('projets.create', compact('clients', 'secteurs', 'bus'));
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
            'conducteur_travaux' => 'required|string|max:255',
            'hastva' => 'boolean',
            'statut' => 'required|in:en cours,terminé,annulé',
            'bu_id' => 'required|exists:bus,id'
        ]);
        $lastReference = \App\Models\Reference::where('nom', 'Code Projet')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'Prj_0000';  // Si aucune référence, utiliser un modèle
$newReference = 'Prj_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête
$request->merge([
'ref_projet' => $newReference,
]);
        Projet::create($request->all());
        return redirect()->route('projets.index')->with('success', 'Projet créé avec succès.');
    }

    public function show(Projet $projet)
    {
        session([
            'projet_id' => $projet->id,
            'projet_nom' => $projet->nom_projet
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
    
        return view('projets.edit', compact('projet', 'clients', 'secteurs', 'bus'));
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
            'conducteur_travaux' => 'required|string|max:255',
            'hastva' => 'boolean',
            'statut' => 'required|in:en cours,terminé,annulé',
            'bu_id' => 'required|exists:bus,id'
        ]);

        $projet->update($request->all());
        return redirect()->route('projets.index')->with('success', 'Projet mis à jour.');
    }

    public function destroy(Projet $projet)
    {
        $projet->delete();
        return redirect()->route('projets.index')->with('success', 'Projet supprimé.');
    }
}
