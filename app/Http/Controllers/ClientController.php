<?php
namespace App\Http\Controllers;

use App\Models\ClientFournisseur;
use App\Models\SecteurActivite;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
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
    
        return view('clients.index', compact('clients'));
    }
    

    public function create() {
        // On récupère les secteurs d'activité pour les entreprises
        $secteurs = SecteurActivite::all();
        return view('clients.create', compact('secteurs'));
    }

    public function store(Request $request) {
     

        // Validation des champs
        $request->validate([
            'categorie' => 'required|in:Particulier,Entreprise',

            
            'secteur_activite' => 'required_if:categorie,Entreprise|string|max:255',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'regime_imposition' => 'required|in:Régime A,Régime B',
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

        // Création du client ou fournisseur en fonction des données du formulaire
        $clientData = $request->all();
        // Ajout de la catégorie et du type par défaut
        $clientData['categorie'] = $request->categorie;
        $clientData['type'] = 'Client'; // Ici on assume que c'est un client par défaut
        $clientData['id_bu'] = $id_bu;
 $lastReference = \App\Models\Reference::where('nom', 'Code client')
        ->latest('created_at')
        ->first();

// Générer la nouvelle référence en prenant la dernière partie de la référence + la date actuelle
$newReference = $lastReference ? $lastReference->ref : 'Cli_0000';  // Si aucune référence, utiliser un modèle
$newReference = 'Cli_' . now()->format('YmdHis'); // Utiliser un underscore et ajouter la date/heure

// Ajouter la référence générée à la requête
$request->merge([
'code' => $newReference,
]);
        // On crée le client ou le fournisseur
        ClientFournisseur::create($clientData);

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function edit(ClientFournisseur $client) {
        // On récupère les secteurs pour les entreprises
        $secteurs = SecteurActivite::all();
        return view('clients.edit', compact('client', 'secteurs'));
    }

    public function update(Request $request, ClientFournisseur $client) {
        // Validation des champs
        $request->validate([
            'categorie' => 'required|in:Particulier,Entreprise',
           
            'secteur_activite' => 'required_if:categorie,Entreprise|string|max:255',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'regime_imposition' => 'required|in:Régime A,Régime B',
            'boite_postale' => 'required|string',
            'adresse_localisation' => 'required|string',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
        ]);

        // Mise à jour du client ou fournisseur
        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(ClientFournisseur $client) {
        // Suppression du client
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}
