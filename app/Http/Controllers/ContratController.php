<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Projet;
use App\Models\Article;
use App\Models\Facture;
use App\Models\Prestation;
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
   public function show($id)
{
    $contrat = Contrat::findOrFail($id);
    
    // Stocker les informations du contrat en session
    session([
        'contrat_id' => $contrat->id,
        'contrat_nom' => $contrat->nom_contrat,
        'ref_contrat' => $contrat->ref_contrat
    ]);
    
    $id_bu = session('selected_bu');
    
    if (!$id_bu) {
        return redirect()->route('select.bu')
            ->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
    }
    
    // Récupérer les clients associés à l'ID du bus
    $clients = ClientFournisseur::where('type', 'client')
        ->where('id_bu', $id_bu)
        ->get();
    
    // Calculer les statistiques du contrat
    $stats = [];
    
    // Montant du contrat
    $stats['Montant du contrat'] = $contrat->montant ?? 0;
    
    // Coût de revient prévisionnel - à partir du dernier DQE validé
    $lastDqe = $contrat->dqes()->where('statut', 'validé')->latest()->first();
    $coutPrevu = 0;
    
    if ($lastDqe) {
        // Calculer le déboursé sec du DQE validé
        $debourseSec = $contrat->debourses()
            ->where('dqe_id', $lastDqe->id)
            ->where('type', 'sec')
            ->first();
            
        // Calculer les frais de chantier du DQE validé
        $fraisChantier = $contrat->debourses()
            ->where('dqe_id', $lastDqe->id)
            ->where('type', 'frais_chantier')
            ->first();
            
        $coutPrevu = ($debourseSec ? $debourseSec->montant_total : 0) + 
                     ($fraisChantier ? $fraisChantier->montant_total : 0);
    }
    $stats['Coût de revient Prév.'] = $coutPrevu;
    
    // Coût de revient réel - à partir des factures liées aux prestations
    // Puisque le montant est dans la facture et non dans la prestation
    $coutReel = Facture::whereHas('prestation', function($query) use ($contrat) {
        $query->where('id_contrat', $contrat->id);
    })->where('statut', 'payée')->sum('montant_total');
    
    // Si aucune prestation n'est facturée, utiliser directement les factures du contrat
    if ($coutReel == 0) {
        $coutReel = Facture::where('id_contrat', $contrat->id)
            ->where('statut', 'payée')
            ->sum('montant_total') * 0.7; // 70% du montant facturé comme coût de revient approximatif
    }
    
    $stats['Coût de revient Réel'] = $coutReel;
    
    // Écart entre prévu et réel
    $stats['Écart'] = $coutPrevu - $coutReel;
    
    // Déboursé sec prévisionnel
    $dsPrevu = $contrat->debourses()
        ->where('type', 'sec')
        ->where('statut', 'validé')
        ->latest()
        ->first();
    $stats['DS Prévisionnel'] = $dsPrevu ? $dsPrevu->montant_total : 0;
    
    // Déboursé sec réalisé - estimation à partir du coût réel
    $dsRealise = $coutReel * 0.7; // Approximation: 70% du coût réel
    $stats['DS Réalisé'] = $dsRealise;
    
    // Frais de chantier réalisés - estimation à partir du coût réel
    $fcRealise = $coutReel * 0.3; // Approximation: 30% du coût réel
    $stats['FC Réalisé'] = $fcRealise;
    
    // Chiffre d'affaires réalisé - Somme des factures payées
    $caRealise = Facture::where('id_contrat', $contrat->id)
        ->where('statut', 'payée')
        ->sum('montant_total');
    $stats['CA Réalisé'] = $caRealise;
    
    return view('contrats.show', compact('contrat', 'clients', 'stats'));
}
}
