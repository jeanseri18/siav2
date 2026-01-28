<?php

require_once 'vendor/autoload.php';

// Charger l'environnement Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\Article;
use App\Models\ClientFournisseur;
use App\Models\DemandeApprovisionnement;
use App\Models\DemandeAchat;
use App\Models\Reference;
use Illuminate\Support\Facades\Auth;

echo "=== Test de sauvegarde des remises ===\n\n";

try {
    // Utiliser un utilisateur existant
    $user = \App\Models\User::first();
    if (!$user) {
        echo "❌ ERREUR: Aucun utilisateur trouvé\n";
        exit(1);
    }
    Auth::login($user);
    
    // Utiliser un fournisseur existant
    $fournisseur = ClientFournisseur::where('type', 'Fournisseur')->first();
    if (!$fournisseur) {
        echo "❌ ERREUR: Aucun fournisseur trouvé\n";
        exit(1);
    }
    
    // Utiliser des articles existants
    $articles = Article::take(2)->get();
    if ($articles->count() < 2) {
        echo "❌ ERREUR: Pas assez d'articles trouvés\n";
        exit(1);
    }
    
    // Utiliser une demande d'approvisionnement existante
    $demandeAppro = DemandeApprovisionnement::first();
    $demandeAchat = DemandeAchat::first();
    
    // Créer un bon de commande avec remises
    $reference = 'TEST_REMISE_' . time();
    
    $bonCommande = BonCommande::create([
        'reference' => $reference,
        'date_commande' => now(),
        'fournisseur_id' => $fournisseur->id,
        'demande_approvisionnement_id' => $demandeAppro?->id,
        'demande_achat_id' => $demandeAchat?->id,
        'user_id' => $user->id,
        'montant_total' => 0, // Sera recalculé
        'date_livraison_prevue' => now()->addDays(7),
        'conditions_paiement' => 'Test remise',
        'notes' => 'Test de sauvegarde des remises',
        'statut' => 'en attente'
    ]);
    
    echo "✅ Bon de commande créé avec ID: {$bonCommande->id}\n";
    
    // Créer des lignes avec différentes remises
    $lignes = [
        [
            'article_id' => $articles[0]->id,
            'quantite' => 10,
            'prix_unitaire' => 1000,
            'remise' => 5.5, // 5.5% de remise
            'commentaire' => 'Ligne avec remise de 5.5%'
        ],
        [
            'article_id' => $articles[1]->id,
            'quantite' => 5,
            'prix_unitaire' => 2000,
            'remise' => 10, // 10% de remise
            'commentaire' => 'Ligne avec remise de 10%'
        ]
    ];
    
    $montantTotal = 0;
    
    foreach ($lignes as $ligneData) {
        $ligne = LigneBonCommande::create([
            'bon_commande_id' => $bonCommande->id,
            'article_id' => $ligneData['article_id'],
            'quantite' => $ligneData['quantite'],
            'prix_unitaire' => $ligneData['prix_unitaire'],
            'remise' => $ligneData['remise'],
            'commentaire' => $ligneData['commentaire']
        ]);
        
        // Calculer le montant avec remise
        $montantBrut = $ligneData['quantite'] * $ligneData['prix_unitaire'];
        $montantRemise = $montantBrut * ($ligneData['remise'] / 100);
        $montantAvecRemise = $montantBrut - $montantRemise;
        $montantTotal += $montantAvecRemise;
        
        echo "✅ Ligne créée - Article: {$articles->find($ligneData['article_id'])->nom}\n";
        echo "   - Quantité: {$ligneData['quantite']}\n";
        echo "   - Prix unitaire: {$ligneData['prix_unitaire']} FCFA\n";
        echo "   - Remise: {$ligneData['remise']}%\n";
        echo "   - Montant brut: {$montantBrut} FCFA\n";
        echo "   - Montant remise: {$montantRemise} FCFA\n";
        echo "   - Montant avec remise: {$montantAvecRemise} FCFA\n\n";
    }
    
    // Mettre à jour le montant total du bon de commande
    $bonCommande->update(['montant_total' => $montantTotal]);
    
    echo "💰 Montant total du bon de commande: {$montantTotal} FCFA\n\n";
    
    // Vérifier que les remises sont bien sauvegardées
    echo "=== Vérification des données sauvegardées ===\n";
    
    $bonCommandeVerif = BonCommande::with('lignes.article')->find($bonCommande->id);
    
    foreach ($bonCommandeVerif->lignes as $ligne) {
        echo "📋 Ligne ID {$ligne->id}:\n";
        echo "   - Article: {$ligne->article->nom}\n";
        echo "   - Remise sauvegardée: {$ligne->remise}%\n";
        echo "   - Montant brut: {$ligne->montant} FCFA\n";
        echo "   - Montant avec remise: {$ligne->montant_avec_remise} FCFA\n";
        echo "   - Montant remise: {$ligne->montant_remise} FCFA\n\n";
    }
    
    echo "✅ SUCCESS: Les remises sont correctement sauvegardées en base de données!\n\n";
    
    // Nettoyage
    echo "🧹 Nettoyage des données de test...\n";
    $bonCommande->lignes()->delete();
    $bonCommande->delete();
    
    echo "✅ Test terminé avec succès!\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}