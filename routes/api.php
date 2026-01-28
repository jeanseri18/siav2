<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route pour récupérer les articles avec leurs unités de mesure (pour l'autocomplete)
Route::get('/articles/search', function (Request $request) {
    $search = $request->get('q', '');
    
    $articles = \App\Models\Article::with('uniteMesure')
        ->where('nom', 'like', "%{$search}%")
        ->orWhere('reference', 'like', "%{$search}%")
        ->limit(10)
        ->get()
        ->map(function ($article) {
            return [
                'id' => $article->id,
                'nom' => $article->nom,
                'reference' => $article->reference,
                'unite_mesure' => $article->uniteMesure ? $article->uniteMesure->nom : 'Unité',
                'unite_mesure_id' => $article->uniteMesure ? $article->uniteMesure->id : null,
                'prix_unitaire' => $article->prix_unitaire
            ];
        });
    
    return response()->json($articles);
});

// Routes pour les selects en cascade de localisation

// Route pour récupérer les données d'un client avec ses contacts
Route::get('/clients/{id}', function ($id) {
    $client = \App\Models\ClientFournisseur::with('contactPersons')->find($id);
    
    if (!$client) {
        return response()->json(['error' => 'Client non trouvé'], 404);
    }
    
    return response()->json([
        'id' => $client->id,
        'nom_raison_sociale' => $client->nom_raison_sociale,
        'prenoms' => $client->prenoms,
        'type_client' => $client->type_client,
        'delai_paiement' => $client->delai_paiement,
        'secteur_activite' => $client->secteur_activite,
        'representants' => $client->contactPersons->map(function ($contact) {
            return [
                'nom' => $contact->nom,
                'prenoms' => $contact->prenoms,
                'fonction' => $contact->fonction,
                'telephone_1' => $contact->telephone_1,
                'telephone_2' => $contact->telephone_2
            ];
        })
    ]);
});