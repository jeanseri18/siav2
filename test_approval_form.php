<?php

// Script de test pour vérifier le formulaire d'approbation

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DemandeApprovisionnement;
use App\Models\User;

// Trouver une demande en attente
$demande = DemandeApprovisionnement::where('statut', 'en attente')->first();

if (!$demande) {
    echo "Aucune demande d'approvisionnement en attente trouvée.\n";
    exit;
}

echo "Demande trouvée: ID {$demande->id}, Statut: {$demande->statut}\n";
echo "Nombre de lignes: " . $demande->lignes()->count() . "\n";

// Trouver un utilisateur admin
$user = User::where('role', 'admin')->first();

if (!$user) {
    echo "Aucun utilisateur admin trouvé.\n";
    exit;
}

echo "Utilisateur admin trouvé: {$user->name} (ID: {$user->id})\n";

// Se connecter en tant qu'admin
Auth::login($user);

// Préparer les données du formulaire
$ligneIds = [];
$quantitesApprouvees = [];

foreach ($demande->lignes as $index => $ligne) {
    $ligneIds[$index] = $ligne->id;
    $quantitesApprouvees[$index] = $ligne->quantite_demandee;
}

echo "\nDonnées préparées:\n";
echo "ligne_ids: " . json_encode($ligneIds) . "\n";
echo "quantite_approuvee: " . json_encode($quantitesApprouvees) . "\n";

// Créer une requête de test
$request = Illuminate\Http\Request::create(
    "/demande-approvisionnements/{$demande->id}/approve",
    'POST',
    [
        'ligne_ids' => $ligneIds,
        'quantite_approuvee' => $quantitesApprouvees
    ]
);

// Ajouter le token CSRF
$token = csrf_token();
$request->headers->set('X-CSRF-TOKEN', $token);

echo "\nToken CSRF: {$token}\n";

// Créer une session pour la requête
$session = new Illuminate\Session\Store('test_session', new Illuminate\Session\FileSessionHandler(storage_path('framework/sessions')));
$session->put('_token', $token);
$request->setSession($session);

// Appeler le contrôleur
try {
    $controller = new App\Http\Controllers\DemandeApprovisionnementController();
    $response = $controller->approve($request, $demande);
    
    echo "\n✅ Succès! Réponse du contrôleur:\n";
    echo "Status: " . $response->getStatusCode() . "\n";
    
    if (method_exists($response, 'getTargetUrl')) {
        echo "URL de redirection: " . $response->getTargetUrl() . "\n";
    }
    
    // Recharger la demande
    $demande->refresh();
    echo "\nStatut après approbation: {$demande->statut}\n";
    echo "Approuvé par: " . ($demande->approved_by ?? 'null') . "\n";
    
    foreach ($demande->lignes as $ligne) {
        echo "Ligne {$ligne->id} - Quantité approuvée: " . ($ligne->quantite_approuvee ?? 'null') . "\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}