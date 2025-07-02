<?php
namespace App\Http\Controllers;

use App\Models\ClientFournisseur;
use App\Models\ContactPerson;
use App\Models\SecteurActivite;
use App\Models\RegimeImposition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // On récupère les régimes d'imposition
        $regimes = RegimeImposition::all();
        return view('clients.create', compact('secteurs', 'regimes'));
    }

    public function store(Request $request) {
     

        // Validation des champs
        $validationRules = [
            'categorie' => 'required|in:Particulier,Entreprise',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'regime_imposition' => 'required|string|max:255',
            'boite_postale' => 'required|string',
            'adresse_localisation' => 'required|string',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            // Validation des contacts
            'contacts' => 'nullable|array',
            'contacts.*.civilite' => 'required_with:contacts|in:M.,Mme,Mlle',
            'contacts.*.nom' => 'required_with:contacts|string|max:255',
            'contacts.*.prenoms' => 'required_with:contacts|string|max:255',
            'contacts.*.fonction' => 'nullable|string|max:255',
            'contacts.*.telephone_1' => 'nullable|string|max:20',
            'contacts.*.telephone_2' => 'nullable|string|max:20',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.adresse' => 'nullable|string',
            'contacts.*.statut' => 'required_with:contacts|in:Actif,Inactif',
            'contacts.*.contact_principal' => 'nullable|boolean',
        ];

        // Validation conditionnelle selon la catégorie
        if ($request->categorie === 'Particulier') {
            $validationRules['nom_raison_sociale'] = 'nullable|string|max:255'; // Nom optionnel pour particulier
            $validationRules['prenoms'] = 'required|string|max:255';
        } else {
            $validationRules['nom_raison_sociale'] = 'required|string|max:255'; // Raison sociale pour entreprise
            $validationRules['n_rccm'] = 'nullable|string|max:255';
            $validationRules['n_cc'] = 'nullable|string|max:255';
            $validationRules['secteur_activite'] = 'required|string|max:255';
        }

        $request->validate($validationRules);
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
        // Transaction pour créer le client et ses contacts
        DB::transaction(function () use ($clientData, $request) {
            // On crée le client ou le fournisseur
            $client = ClientFournisseur::create($clientData);
            
            // Créer les personnes contacts si elles sont fournies
            $contacts = $request->input('contacts', []);
            if (!empty($contacts)) {
                $hasMainContact = false;
                
                foreach ($contacts as $contactData) {
                    // Vérifier que les données essentielles sont présentes
                    if (empty($contactData['nom']) || empty($contactData['prenoms'])) {
                        continue;
                    }
                    
                    $contactData['client_fournisseur_id'] = $client->id;
                    $contactData['contact_principal'] = isset($contactData['contact_principal']) ? true : false;
                    
                    // S'assurer qu'il n'y a qu'un seul contact principal
                    if ($contactData['contact_principal']) {
                        if ($hasMainContact) {
                            $contactData['contact_principal'] = false;
                        } else {
                            $hasMainContact = true;
                        }
                    }
                    
                    ContactPerson::create($contactData);
                }
                
                // Si aucun contact principal n'a été défini, définir le premier comme principal
                if (!$hasMainContact) {
                    $firstContact = ContactPerson::where('client_fournisseur_id', $client->id)->first();
                    if ($firstContact) {
                        $firstContact->update(['contact_principal' => true]);
                    }
                }
            }
        });

        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function edit(ClientFournisseur $client) {
        // On récupère les secteurs pour les entreprises
        $secteurs = SecteurActivite::all();
        // On récupère les régimes d'imposition
        $regimes = RegimeImposition::all();
        return view('clients.edit', compact('client', 'secteurs', 'regimes'));
    }

    public function update(Request $request, ClientFournisseur $client) {
        // Validation des champs
        $validationRules = [
            'categorie' => 'required|in:Particulier,Entreprise',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'regime_imposition' => 'required|string|max:255',
            'boite_postale' => 'required|string',
            'adresse_localisation' => 'required|string',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            // Validation des contacts
            'contacts' => 'required|array|min:1',
            'contacts.*.civilite' => 'required|in:M.,Mme,Mlle',
            'contacts.*.nom' => 'required|string|max:255',
            'contacts.*.prenoms' => 'required|string|max:255',
            'contacts.*.fonction' => 'nullable|string|max:255',
            'contacts.*.telephone_1' => 'nullable|string|max:20',
            'contacts.*.telephone_2' => 'nullable|string|max:20',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.adresse' => 'nullable|string',
            'contacts.*.statut' => 'required|in:Actif,Inactif',
            'contacts.*.contact_principal' => 'nullable|boolean',
        ];

        // Validation conditionnelle selon la catégorie
        if ($request->categorie === 'Particulier') {
            $validationRules['nom_raison_sociale'] = 'nullable|string|max:255'; // Nom optionnel pour particulier
            $validationRules['prenoms'] = 'required|string|max:255';
        } else {
            $validationRules['nom_raison_sociale'] = 'required|string|max:255'; // Raison sociale pour entreprise
            $validationRules['n_rccm'] = 'nullable|string|max:255';
            $validationRules['n_cc'] = 'nullable|string|max:255';
            $validationRules['secteur_activite'] = 'required|string|max:255';
        }

        $request->validate($validationRules);

        // Transaction pour mettre à jour le client et ses contacts
        DB::transaction(function () use ($request, $client) {
            // Mise à jour du client ou fournisseur
            $clientData = $request->except('contacts');
            $client->update($clientData);
            
            // Gérer les contacts
            $contacts = $request->input('contacts', []);
            $contactIds = [];
            $hasMainContact = false;
            
            foreach ($contacts as $contactData) {
                $contactData['client_fournisseur_id'] = $client->id;
                $contactData['contact_principal'] = isset($contactData['contact_principal']) ? true : false;
                
                // S'assurer qu'il n'y a qu'un seul contact principal
                if ($contactData['contact_principal']) {
                    if ($hasMainContact) {
                        $contactData['contact_principal'] = false;
                    } else {
                        $hasMainContact = true;
                    }
                }
                
                if (isset($contactData['id']) && $contactData['id']) {
                    // Mettre à jour le contact existant
                    $contact = ContactPerson::find($contactData['id']);
                    if ($contact) {
                        $contact->update($contactData);
                        $contactIds[] = $contact->id;
                    }
                } else {
                    // Créer un nouveau contact
                    unset($contactData['id']);
                    $contact = ContactPerson::create($contactData);
                    $contactIds[] = $contact->id;
                }
            }
            
            // Supprimer les contacts qui ne sont plus dans la liste
            ContactPerson::where('client_fournisseur_id', $client->id)
                        ->whereNotIn('id', $contactIds)
                        ->delete();
            
            // Si aucun contact principal n'a été défini, définir le premier comme principal
            if (!$hasMainContact && !empty($contacts)) {
                $firstContact = ContactPerson::where('client_fournisseur_id', $client->id)->first();
                if ($firstContact) {
                    $firstContact->update(['contact_principal' => true]);
                }
            }
        });

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(ClientFournisseur $client) {
        // Suppression du client
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
}
