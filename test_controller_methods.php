<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DevisController;
use Illuminate\Http\Request;

// Créer une instance du contrôleur
$controller = new DevisController();

// Récupérer un devis "En attente"
$devis = \App\Models\Devis::where('statut', 'En attente')->first();

if (!$devis) {
    echo "Aucun devis 'En attente' trouvé. Création d'un test...\n";
    $devis = \App\Models\Devis::create([
        'client_id' => 1,
        'numero_client' => 'TEST001',
        'nom_client' => 'Client Test',
        'commentaire' => 'Devis de test',
        'total_ht' => 1000,
        'tva' => 180,
        'total_ttc' => 1180,
        'statut' => 'En attente',
        'utilise_pour_vente' => false,
        'user_id' => 1,
        'ref_devis' => 'TEST-' . time()
    ]);
}

echo "Devis de test:\n";
echo "ID: " . $devis->id . "\n";
echo "Statut: " . $devis->statut . "\n";
echo "Utilisé pour vente: " . ($devis->utilise_pour_vente ? 'Oui' : 'Non') . "\n\n";

// Tester la méthode approve
echo "Test de la méthode approve()...\n";
try {
    // Simuler une requête
    $request = Request::create('/devis/' . $devis->id . '/approve', 'POST');
    app()->instance('request', $request);
    
    // Appeler la méthode approve
    $response = $controller->approve($devis);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
    // Recharger le devis
    $devis->refresh();
    echo "Statut après approve: " . $devis->statut . "\n";
    
} catch (\Exception $e) {
    echo "Erreur lors de approve: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

// Réinitialiser le statut pour tester reject
echo "\nRéinitialisation du statut...\n";
$devis->update(['statut' => 'En attente']);

// Tester la méthode reject
echo "\nTest de la méthode reject()...\n";
try {
    // Simuler une requête
    $request = Request::create('/devis/' . $devis->id . '/reject', 'POST');
    app()->instance('request', $request);
    
    // Appeler la méthode reject
    $response = $controller->reject($devis);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . substr($response->getContent(), 0, 200) . "...\n";
    
    // Recharger le devis
    $devis->refresh();
    echo "Statut après reject: " . $devis->statut . "\n";
    
} catch (\Exception $e) {
    echo "Erreur lors de reject: " . $e->getMessage() . "\n";
}