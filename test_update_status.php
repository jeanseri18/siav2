<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialiser Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Récupérer un devis par ID
$devisId = 1; // Vous pouvez changer cet ID
$devis = \App\Models\Devis::find($devisId);

if ($devis) {
    echo "Devis trouvé :\n";
    echo "ID: " . $devis->id . "\n";
    echo "Statut actuel: " . $devis->statut . "\n";
    echo "Utilisé pour vente: " . ($devis->utilise_pour_vente ? 'Oui' : 'Non') . "\n\n";
    
    // Tester la mise à jour du statut
    echo "Mise à jour du statut...\n";
    $devis->statut = 'Validé';
    $result = $devis->save();
    
    if ($result) {
        echo "✓ Mise à jour réussie !\n";
        echo "Nouveau statut: " . $devis->statut . "\n";
    } else {
        echo "✗ Échec de la mise à jour\n";
    }
    
    // Recharger le devis pour vérifier
    $devis->refresh();
    echo "\nStatut après rechargement: " . $devis->statut . "\n";
    
} else {
    echo "Aucun devis trouvé avec l'ID: " . $devisId . "\n";
    
    // Lister les devis disponibles
    echo "\nDevis disponibles:\n";
    $allDevis = \App\Models\Devis::limit(5)->get();
    foreach ($allDevis as $d) {
        echo "ID: " . $d->id . " - Statut: " . $d->statut . " - Client: " . $d->nom_client . "\n";
    }
}