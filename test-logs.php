<?php

// Route de test pour voir les logs récents
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Log;

echo "<h1>Test des logs</h1>";

// Créer un log de test
Log::info('Test de fonctionnement des logs');

// Afficher le chemin du fichier de log
$logPath = storage_path('logs/laravel.log');
echo "<p>Chemin du fichier de log: " . $logPath . "</p>";

// Lire les 10 dernières lignes du fichier de log
if (file_exists($logPath)) {
    $lines = file($logPath);
    $lastLines = array_slice($lines, -10);
    
    echo "<h2>10 dernières lignes du log:</h2>";
    echo "<pre>";
    foreach ($lastLines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
} else {
    echo "<p>Le fichier de log n'existe pas encore.</p>";
}

echo "<p><a href='".$_SERVER['PHP_SELF']."'>Rafraîchir</a></p>";

?>