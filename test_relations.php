<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;

echo "=== Test des relations ===\n";

// Vérifier les catégories
$categories = CategorieRubrique::where('type', 'bpu')->get();
echo "Nombre de catégories 'bpu': " . $categories->count() . "\n";

// Vérifier les sous-catégories
$sousCategories = SousCategorieRubrique::all();
echo "Nombre total de sous-catégories: " . $sousCategories->count() . "\n";

// Vérifier les relations
foreach ($categories as $cat) {
    $sousCount = $cat->sousCategories()->count();
    echo "Catégorie '{$cat->nom}' (ID: {$cat->id}) - Sous-catégories: {$sousCount}\n";
    
    // Vérifier manuellement avec la clé étrangère
    $manualCount = SousCategorieRubrique::where('id_session', $cat->id)->count();
    echo "  -> Vérification manuelle: {$manualCount}\n";
}

// Afficher quelques sous-catégories avec leur id_session
echo "\n=== Échantillon de sous-catégories ===\n";
$sample = SousCategorieRubrique::take(5)->get();
foreach ($sample as $sc) {
    echo "Sous-catégorie '{$sc->nom}' - id_session: {$sc->id_session}\n";
}