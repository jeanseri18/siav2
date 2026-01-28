<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\DevisController;

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
echo "Statut avant: " . $devis->statut . "\n";
echo "Utilisé pour vente: " . ($devis->utilise_pour_vente ? 'Oui' : 'Non') . "\n\n";

// Tester la méthode approve avec le nouvel ID
echo "Test de la méthode approve()...\n";
try {
    // Appeler la méthode approve avec l'ID
    $response = $controller->approve($devis->id);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    
    // Recharger le devis
    $devis->refresh();
    echo "Statut après approve: " . $devis->statut . "\n";
    
    if ($devis->statut === 'Approuvé') {
        echo "✓ Succès: Le statut est maintenant 'Approuvé'\n";
    } else {
        echo "✗ Erreur: Le statut est '{$devis->statut}' au lieu de 'Approuvé'\n";
    }
    
} catch (\Exception $e) {
    echo "Erreur lors de approve: " . $e->getMessage() . "\n";
}