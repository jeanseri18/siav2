<?php
namespace App\Http\Controllers;

use App\Models\ClientFournisseur;
use App\Models\ContactPerson;
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
        // Validation personnalisée pour nom_raison_sociale
        $validationRules = [
            'categorie' => 'required|in:Particulier,Entreprise',
            'nom_raison_sociale' => 'required|string|max:255',
            'prenoms' => 'nullable|string|max:255',
            'n_rccm' => 'nullable|string|max:255',
            'n_cc' => 'nullable|string|max:255',
            'secteur_activite' => 'nullable|string|max:255',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'regime_imposition' => 'nullable|string|max:255',
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
            'contacts.*.telephone_1' => 'nullable|string|max:255',
            'contacts.*.telephone_2' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.adresse' => 'nullable|string',
            'contacts.*.contact_principal' => 'nullable|boolean',
            'contacts.*.statut' => 'nullable|in:Actif,Inactif',
        ];
        
        // Validation conditionnelle pour les prénoms
        if ($request->categorie === 'Particulier') {
            $validationRules['prenoms'] = 'required|string|max:255';
        }
        
        $request->validate($validationRules);
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
        $fournisseur = ClientFournisseur::create($clientData);
        
        // Gestion des contacts
        if ($request->has('contacts') && is_array($request->contacts)) {
            foreach ($request->contacts as $contactData) {
                if (!empty($contactData['nom']) && !empty($contactData['prenoms'])) {
                    $fournisseur->contactPersons()->create([
                        'civilite' => $contactData['civilite'],
                        'nom' => $contactData['nom'],
                        'prenoms' => $contactData['prenoms'],
                        'fonction' => $contactData['fonction'] ?? null,
                        'telephone_1' => $contactData['telephone_1'] ?? null,
                        'telephone_2' => $contactData['telephone_2'] ?? null,
                        'email' => $contactData['email'] ?? null,
                        'adresse' => $contactData['adresse'] ?? null,
                        'contact_principal' => isset($contactData['contact_principal']) ? true : false,
                        'statut' => $contactData['statut'] ?? 'Actif',
                    ]);
                }
            }
        }
        
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur ajouté avec succès.');
    }

    public function edit(ClientFournisseur $fournisseur) {
        $secteurs = SecteurActivite::all();
        $regimes = RegimeImposition::all();
        $contacts = $fournisseur->contactPersons;
        return view('fournisseurs.edit', compact('fournisseur', 'secteurs', 'regimes', 'contacts'));
    }

    public function update(Request $request, ClientFournisseur $fournisseur) {
        $validationRules = [
            'categorie' => 'required|in:Particulier,Entreprise',
            'nom_raison_sociale' => 'required|string|max:255',
            'prenoms' => 'nullable|string|max:255',
            'n_rccm' => 'nullable|string|max:255',
            'n_cc' => 'nullable|string|max:255',
            'regime_imposition' => 'nullable|string|max:255',
            'delai_paiement' => 'required|integer',
            'mode_paiement' => 'required|in:Virement,Chèque,Espèces',
            'secteur_activite' => 'nullable|string|max:255',
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
            'contacts.*.telephone_1' => 'nullable|string|max:255',
            'contacts.*.telephone_2' => 'nullable|string|max:255',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.adresse' => 'nullable|string',
            'contacts.*.contact_principal' => 'nullable|boolean',
            'contacts.*.statut' => 'nullable|in:Actif,Inactif',
        ];
        
        // Validation conditionnelle pour les prénoms
        if ($request->categorie === 'Particulier') {
            $validationRules['prenoms'] = 'required|string|max:255';
        }
        
        $request->validate($validationRules);

        $fournisseur->update($request->except('contacts'));
        
        // Supprimer les anciens contacts
        $fournisseur->contactPersons()->delete();
        
        // Gestion des nouveaux contacts
        if ($request->has('contacts') && is_array($request->contacts)) {
            foreach ($request->contacts as $contactData) {
                if (!empty($contactData['nom']) && !empty($contactData['prenoms'])) {
                    $fournisseur->contactPersons()->create([
                        'civilite' => $contactData['civilite'],
                        'nom' => $contactData['nom'],
                        'prenoms' => $contactData['prenoms'],
                        'fonction' => $contactData['fonction'] ?? null,
                        'telephone_1' => $contactData['telephone_1'] ?? null,
                        'telephone_2' => $contactData['telephone_2'] ?? null,
                        'email' => $contactData['email'] ?? null,
                        'adresse' => $contactData['adresse'] ?? null,
                        'contact_principal' => isset($contactData['contact_principal']) ? true : false,
                        'statut' => $contactData['statut'] ?? 'Actif',
                    ]);
                }
            }
        }
        
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(ClientFournisseur $fournisseur) {
        $fournisseur->delete();
        return redirect()->route('fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
    }
}
