<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FactureContrat;

$facture = FactureContrat::with('facturesDecompte')->first();

if ($facture) {
    echo "=== Test du calcul du montant versé ===\n";
    echo "Facture Contrat ID: " . $facture->id . "\n";
    echo "Montant à payer: " . $facture->montant_a_payer . "\n";
    echo "Montant versé (actuel): " . $facture->montant_verse . "\n";
    echo "Nombre de décomptes: " . $facture->facturesDecompte->count() . "\n";
    echo "Nombre de décomptes validés: " . $facture->facturesDecompte()->where('statut', 'valide')->count() . "\n";
    echo "Somme des montants HT des décomptes validés: " . $facture->facturesDecompte()->where('statut', 'valide')->sum('montant_ht') . "\n";
    echo "Calcul du montant versé: " . $facture->calculerMontantVerse() . "\n";
    
    echo "\n=== Détail des décomptes ===\n";
    foreach ($facture->facturesDecompte as $decompte) {
        echo "Décompte ID: " . $decompte->id . " - Statut: " . $decompte->statut . " - Montant HT: " . $decompte->montant_ht . "\n";
    }
} else {
    echo "Aucune facture contrat trouvée.\n";
}