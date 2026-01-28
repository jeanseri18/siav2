<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FactureContrat;
use App\Models\FactureDecompte;

// Trouver une facture contrat
$facture = FactureContrat::first();

if ($facture) {
    echo "=== Test de création et validation de décompte ===\n";
    echo "Facture Contrat ID: " . $facture->id . "\n";
    echo "Montant à payer: " . $facture->montant_a_payer . "\n";
    echo "Montant versé (avant): " . $facture->montant_verse . "\n";
    
    // Créer un décompte de test
    $decompte = FactureDecompte::create([
        'facture_contrat_id' => $facture->id,
        'numero' => 'TEST-001',
        'date_facture' => now(),
        'pourcentage_avancement' => 50.00,
        'montant_ht' => 1000.00,
        'montant_ttc' => 1180.00,
        'statut' => 'brouillon',
        'observations' => 'Test de décompte'
    ]);
    
    echo "\nDécompte créé:\n";
    echo "ID: " . $decompte->id . "\n";
    echo "Statut: " . $decompte->statut . "\n";
    echo "Montant HT: " . $decompte->montant_ht . "\n";
    
    // Valider le décompte
    $decompte->update(['statut' => 'valide']);
    
    // Mettre à jour le montant versé
    $facture->mettreAJourMontantVerse();
    $facture->refresh();
    
    echo "\nAprès validation:\n";
    echo "Statut du décompte: " . $decompte->fresh()->statut . "\n";
    echo "Montant versé (après): " . $facture->montant_verse . "\n";
    
    // Nettoyer
    $decompte->delete();
    
} else {
    echo "Aucune facture contrat trouvée.\n";
}