<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Projet;
use App\Models\Article;
use App\Models\ClientFournisseur;
use App\Models\TypeTravaux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ContratController extends Controller
{
    // Afficher le formulaire de création d'un contrat
    public function create()
    {

                // Récupérer l'ID du bus depuis la session
                $id_bu = session('selected_bu');
    
                // Vérifier si l'ID du bus est présent dans la session
                if (!$id_bu) {
                    return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
                }
            
                // Récupérer les clients associés à l'ID du bus
                $clients = ClientFournisseur::where('type', 'client')
                                            ->where('id_bu', $id_bu)  // Filtrage selon l'ID du bus
                                            ->get();
        $projet_id = session('projet_id');
        $projets = Projet::all();
        $articles = Article::all();
        $typeTravaux=TypeTravaux::all();
        return view('contrats.create', compact('projet_id','clients','projets','articles','typeTravaux'));
    }

    // Enregistrer un nouveau contrat
    public function store(Request $request)
    {
        
        $request->validate([
            // 'ref_contrat' => 'required|unique:contrats',
            'nom_contrat' => 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'type_travaux' => 'required',
            'taux_garantie' => 'required|numeric',
            'client_id' => 'required|exists:client_fournisseurs,id',
            'montant' => 'required|numeric',
            'statut' => 'required|in:en cours,terminé,annulé',
        ]);

                $lastReference = \App\Models\Reference::where('nom', 'Code contrat')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'Ctr_000';  // Si aucune référence, utiliser un modèle
$newReference = 'Ctr_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête

        Contrat::create([
            'ref_contrat' => $newReference,
            'nom_contrat' => $request->nom_contrat,
            'id_projet' => session('projet_id'),
          
            'nom_projet' => session('projet_nom'),
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'type_travaux' => $request->type_travaux,
            'taux_garantie' => $request->taux_garantie,
            'client_id' => $request->client_id,
            'montant' => $request->montant,
            'statut' => $request->statut,
            'decompte' => $request->decompte ?? false,
        ]);

        return redirect()->route('contrats.index')->with('success', 'Contrat créé avec succès!');
    }

    // Afficher les contrats
    public function index()
    {
        $projet_id = session('projet_id');
        $contrats = Contrat::where('id_projet', $projet_id)->get();
        $projets = Projet::all();
        $articles = Article::all();
        return view('contrats.index', compact('contrats','projets','articles'));
    }

    // Afficher le formulaire d'édition d'un contrat
    public function edit($id)
    {
        $id_bu = session('selected_bu');

        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
    
        // Récupérer les clients associés à l'ID du bus
        $clients = ClientFournisseur::where('type', 'client')
                                    ->where('id_bu', $id_bu)  // Filtrage selon l'ID du bus
                                    ->get();
        $contrat = Contrat::findOrFail($id);
        $projets = Projet::all();
        $articles = Article::all();
        $typeTravaux=TypeTravaux::all();
        return view('contrats.edit', compact('contrat','clients','projets','articles','typeTravaux'));
    }

    // Mettre à jour un contrat
    public function update(Request $request, $id)
    {              

        $request->validate([
            'nom_contrat' => 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'type_travaux' => 'required',
            'taux_garantie' => 'required|numeric',
            'client_id' => 'required',
            'montant' => 'required|numeric',
            'statut' => 'required|in:en cours,terminé,annulé',
        ]);
        $lastReference = \App\Models\Reference::where('nom', 'Code contrat')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'Ctr_000';  // Si aucune référence, utiliser un modèle
$newReference = 'Ctr_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête
$request->merge([
'ref_contrat' => $newReference,
]);  
        $contrat = Contrat::findOrFail($id);
        $contrat->update($request->all());

        return redirect()->route('contrats.index')->with('success', 'Contrat mis à jour avec succès!');
    }

    // Supprimer un contrat
    public function destroy($id)
    {
        $contrat = Contrat::findOrFail($id);
        $contrat->delete();

        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé avec succès!');
    }
    // Afficher les détails d'un contrat
public function show($id)

    {  
        $contrat = Contrat::findOrFail($id);
        session(['contrat_id' => $contrat->id,'contrat_nom'=>$contrat->nom_contrat,'ref_contrat'=>$contrat->ref_contrat]);
        $id_bu = session('selected_bu');

        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
    
        // Récupérer les clients associés à l'ID du bus
        $clients = ClientFournisseur::where('type', 'client')
                                    ->where('id_bu', $id_bu)  // Filtrage selon l'ID du bus
                                    ->get();
    return view('contrats.show', compact('contrat','clients'));
}

}
