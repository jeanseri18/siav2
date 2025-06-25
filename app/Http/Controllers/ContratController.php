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
    // Afficher tous les contrats de tous les projets
    public function allContracts()
    {
        // Récupérer tous les contrats avec leurs relations
        $contrats = Contrat::with(['client', 'projet'])->get();
        
        // Récupérer tous les projets pour le formulaire de création
        $projets = Projet::all();
        
        return view('contrats.all', compact('contrats', 'projets'));
    }

    // Afficher la liste des contrats
    public function index()
    {
        // Récupérer l'ID du projet depuis la session
        $projet_id = session('projet_id');
        
        if (!$projet_id) {
            return redirect()->route('projets.index')->with('error', 'Aucun projet sélectionné');
        }
        
        // Récupérer les contrats du projet sélectionné
        $contrats = Contrat::where('id_projet', $projet_id)
                          ->with(['client', 'projet'])
                          ->get();
        
        // Récupérer tous les projets et articles pour le sublayout projetdetail
        $projets = Projet::all();
        $articles = Article::all();
        
        return view('contrats.index', compact('contrats', 'projets', 'articles'));
    }

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
            'client_id' => 'nullable|exists:client_fournisseurs,id',
            'montant' => 'nullable|numeric',
            'statut' => 'required|in:en cours,terminé,annulé',
            'projet_id' => 'nullable|exists:projets,id',
        ]);

        $lastReference = \App\Models\Reference::where('nom', 'Code contrat')
            ->latest('created_at')
            ->first();

        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'Ctr_000';
        $newReference = 'Ctr_' . now()->format('YmdHis');

        // Déterminer l'ID du projet (depuis le formulaire ou la session)
        $projetId = $request->projet_id ?? session('projet_id');
        $projetNom = null;
        
        if ($projetId) {
            $projet = Projet::find($projetId);
            $projetNom = $projet ? $projet->nom_projet : session('projet_nom');
        }

        Contrat::create([
            'ref_contrat' => $newReference,
            'nom_contrat' => $request->nom_contrat,
            'id_projet' => $projetId,
            'nom_projet' => $projetNom,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'type_travaux' => $request->type_travaux,
            'taux_garantie' => $request->taux_garantie,
            'client_id' => $request->client_id,
            'montant' => $request->montant,
            'statut' => $request->statut,
            'decompte' => $request->decompte ?? false,
        ]);

        // Rediriger selon la source de la création
        if ($request->projet_id) {
            // Création depuis la vue "tous les contrats"
            return redirect()->route('contrats.all')->with('success', 'Contrat créé avec succès!');
        } else {
            // Création depuis la vue projet spécifique
            return redirect()->route('contrats.index')->with('success', 'Contrat créé avec succès!');
        }
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
            'montant' => 'nullable|numeric',
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
    $dsPrevu = 0;
    $fcPrevu = 0;
    $fgPrevu = 0;
    
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
            
        $dsPrevu = $debourseSec ? $debourseSec->montant_total : 0;
        $fcPrevu = $fraisChantier ? $fraisChantier->montant_total : 0;
        
        // Calculer les frais généraux prévisionnels (estimation: 15% du DS + FC)
        $fgPrevu = ($dsPrevu + $fcPrevu) * 0.15;
        
        $coutPrevu = $dsPrevu + $fcPrevu + $fgPrevu;
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
    
    // Statistiques prévisionnelles
    $stats['DS Prévisionnel'] = $dsPrevu;
    $stats['FC Prévisionnel'] = $fcPrevu;
    $stats['FG Prévisionnel'] = $fgPrevu;
    
    // Déboursé sec réalisé - estimation à partir du coût réel
    $dsRealise = $coutReel * 0.7; // Approximation: 70% du coût réel
    $stats['DS Réalisé'] = $dsRealise;
    
    // Frais de chantier réalisés - estimation à partir du coût réel
    $fcRealise = $coutReel * 0.3; // Approximation: 30% du coût réel
    $stats['FC Réalisé'] = $fcRealise;
    
    // Frais généraux réalisés - estimation à partir du coût réel
    $fgRealise = $coutReel * 0.15; // Approximation: 15% du coût réel
    $stats['FG Réalisé'] = $fgRealise;
    
    // Chiffre d'affaires réalisé - Somme des factures payées
    $caRealise = Facture::where('id_contrat', $contrat->id)
        ->where('statut', 'payée')
        ->sum('montant_total');
    $stats['CA Réalisé'] = $caRealise;
    
    // Bénéfices
    $beneficePrevu = $stats['Montant du contrat'] - $coutPrevu;
    $beneficeRealise = $caRealise - $coutReel;
    $stats['Bénéfice Prévisionnel'] = $beneficePrevu;
    $stats['Bénéfice Réalisé'] = $beneficeRealise;
    
    return view('contrats.show', compact('contrat', 'clients', 'stats'));
}

    // Dupliquer un contrat
    public function duplicate($id)
    {
        $contrat = Contrat::findOrFail($id);
        
        // Générer une nouvelle référence
        $newReference = 'Ctr_' . now()->format('YmdHis');
        
        // Créer une copie du contrat
        $newContrat = $contrat->replicate();
        $newContrat->ref_contrat = $newReference;
        $newContrat->nom_contrat = $contrat->nom_contrat . ' (Copie)';
        $newContrat->statut = 'en cours'; // Réinitialiser le statut
        $newContrat->save();
        
        return redirect()->route('contrats.show', $newContrat->id)
            ->with('success', 'Contrat dupliqué avec succès!');
    }
}
