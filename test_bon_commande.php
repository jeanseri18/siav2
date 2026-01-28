<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BonCommande;
use App\Models\ClientFournisseur;
use App\Models\Projet;
use Illuminate\Support\Facades\DB;

// Test de création d'un bon de commande avec projet_id et lieu_livraison
try {
    // Récupérer un fournisseur existant
    $fournisseur = ClientFournisseur::where('type', 'Fournisseur')->first();
    
    // Récupérer un projet existant
    $projet = Projet::first();
    
    if (!$fournisseur) {
        echo "Aucun fournisseur trouvé\n";
        exit;
    }
    
    if (!$projet) {
        echo "Aucun projet trouvé\n";
        exit;
    }
    
    echo "Fournisseur trouvé: {$fournisseur->nom}\n";
    echo "Projet trouvé: {$projet->nom_projet}\n";
    
    // Créer un bon de commande de test
    $bonCommande = BonCommande::create([
        'reference' => 'TEST_' . now()->format('YmdHis'),
        'date_commande' => now(),
        'fournisseur_id' => $fournisseur->id,
        'projet_id' => $projet->id,
        'lieu_livraison' => 'Test Lieu de Livraison',
        'user_id' => 1,
        'montant_total' => 1000.00,
        'statut' => 'en attente'
    ]);
    
    echo "Bon de commande créé avec ID: {$bonCommande->id}\n";
    echo "Projet ID sauvegardé: {$bonCommande->projet_id}\n";
    echo "Lieu de livraison sauvegardé: {$bonCommande->lieu_livraison}\n";
    
    // Vérifier en base de données
    $bonCommandeFromDB = DB::table('bon_commandes')->where('id', $bonCommande->id)->first();
    echo "\nVérification en base de données:\n";
    echo "Projet ID en DB: {$bonCommandeFromDB->projet_id}\n";
    echo "Lieu de livraison en DB: {$bonCommandeFromDB->lieu_livraison}\n";
    
    // Nettoyer - supprimer le bon de commande de test
    $bonCommande->delete();
    echo "\nBon de commande de test supprimé.\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}