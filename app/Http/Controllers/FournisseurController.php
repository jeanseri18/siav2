<?php
namespace App\Http\Controllers;

use App\Models\ClientFournisseur;
use Illuminate\Http\Request;
use App\Models\SecteurActivite;
use App\Models\RegimeImposition;

class FournisseurController extends Controller {
  
    public function index()
{
    // Récupérer l'ID du bus depuis la session
    $id_bu = session('selected_bu');

    // Vérifier si l'ID du bus est présent dans la session
    if (!$id_bu) {
        return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
    }

    // Récupérer les clients associés à l'ID du bus
    $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')
                                ->where('id_bu', $id_bu)  // Filtrage selon l'ID du bus
                                ->get();

    return view('fournisseurs.index', compact('fournisseurs'));
}


    public function create() {
        $secteurs = SecteurActivite::all();
        $regimes = RegimeImposition::all();
        return view('fournisseurs.create', compact('secteurs', 'regimes'));
    }

    public function store(Request $request) {

        // Validation des champs
        $request->validate([
            'categorie' => 'required|in:Particulier,Entreprise',
           
            'secteur_activite' => 'required_if:categorie,Entreprise|string|max:255',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'regime_imposition' => 'required|string|max:255',
            'boite_postale' => 'required|string',
            'adresse_localisation' => 'required|string',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
        ]);
        $id_bu = session('selected_bu'); // Récupération de l'ID du bus depuis la session

        if (!$id_bu) {
            // Si l'ID du bus n'est pas trouvé en session, retourner une erreur
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant de créer un client.']);
        }
     $lastReference = \App\Models\Reference::where('nom', 'Code fournisseur interne')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'Int_0000';  // Si aucune référence, utiliser un modèle
$newReference = 'Int_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête
$request->merge([
'code' => $newReference,
]);
        // Création du client ou fournisseur en fonction des données du formulaire
        $clientData = $request->all();
        // Ajout de la catégorie et du type par défaut
        $clientData['categorie'] = $request->categorie;
        $clientData['type'] = 'Fournisseur'; // Ici on assume que c'est un client par défaut
        $clientData['id_bu'] = $id_bu;
        // On crée le client ou le fournisseur
        ClientFournisseur::create($clientData);
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function edit(ClientFournisseur $fournisseur) {
        $secteurs = SecteurActivite::all();
        $regimes = RegimeImposition::all();
        return view('fournisseurs.edit', compact('fournisseur', 'secteurs', 'regimes'));
    }

    public function update(Request $request, ClientFournisseur $fournisseur) {
        $request->validate([
            'nom_raison_sociale' => 'required|string|max:255',
            'n_rccm' => 'required|string',
            'n_cc' => 'required|string',
            'regime_imposition' => 'required|string|max:255',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'adresse_localisation' => 'required|string',
            'boite_postale' => 'required|string',
            'secteur_activite' => 'required|string',
            'email' => 'required|email',
            'telephone' => 'required|string',
        ]);

        $fournisseur->update($request->all());
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(ClientFournisseur $fournisseur) {
        $fournisseur->delete();
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
    }
}
