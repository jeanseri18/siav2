<?php 

namespace App\Imports;

use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use App\Models\Rubrique;
use App\Models\Bpu;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class RubriquesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Variables pour suivre les ID actuels
        $currentCategorieId = null;
        $currentSousCategorieId = null;
        $currentRubriqueId = null;
        
        // Créer une catégorie par défaut si nécessaire
        $defaultCategorie = CategorieRubrique::firstOrCreate(
            ['nom' => 'Catégorie par défaut'],
            ['type' => 'bpu']
        );
        $currentCategorieId = $defaultCategorie->id;

        // Créer une sous-catégorie par défaut si nécessaire
        $defaultSousCategorie = SousCategorieRubrique::firstOrCreate(
            ['nom' => 'Sous-catégorie par défaut', 'id_session' => $currentCategorieId],
            ['type' => 'bpu']
        );
        $currentSousCategorieId = $defaultSousCategorie->id;

        // Créer une rubrique par défaut si nécessaire
        $defaultRubrique = Rubrique::firstOrCreate(
            ['nom' => 'Rubrique par défaut', 'id_soussession' => $currentSousCategorieId],
            ['type' => 'bpu']
        );
        $currentRubriqueId = $defaultRubrique->id;
        
        // Ignorer la ligne d'en-tête si présente
        if (count($rows) > 0 && $this->isHeaderRow($rows[0])) {
            $rows->shift();
        }
        
        foreach ($rows as $index => $row) {
            try {
                // Vérifier que la ligne a assez de colonnes
                if (count($row) < 2) {
                    continue; // Ignorer les lignes trop courtes
                }
                
                // Nettoyer les données
                $code = $this->cleanString($row[0] ?? '');
                $designation = $this->cleanString($row[1] ?? '');
                $unite = $this->cleanString($row[2] ?? '');
                
                // Ignorer les lignes vides
                if (empty($code) || empty($designation)) {
                    continue;
                }
                
                // Essayer de déterminer le type de ligne
                $codeLower = strtolower($code);
                
                // Vérification pour une catégorie
                if ($codeLower === 'categorie' || $codeLower === 'catégorie') {
                    $categorie = CategorieRubrique::create([
                        'nom' => $designation,
                        'type' => 'bpu',
                    ]);
                    $currentCategorieId = $categorie->id;
                    $currentSousCategorieId = null;
                    $currentRubriqueId = null;
                    Log::info("Catégorie créée: $designation (ID: $currentCategorieId)");
                    continue;
                }
                
                // Vérification pour une sous-catégorie
                if ($codeLower === 'souscategorie' || $codeLower === 'sous-categorie' || 
                    $codeLower === 'sous-catégorie' || $codeLower === 'souscatégorie') {
                    $sousCategorie = SousCategorieRubrique::create([
                        'nom' => $designation,
                        'type' => 'bpu',
                        'id_session' => $currentCategorieId,
                    ]);
                    $currentSousCategorieId = $sousCategorie->id;
                    $currentRubriqueId = null;
                    Log::info("Sous-catégorie créée: $designation (ID: $currentSousCategorieId)");
                    continue;
                }
                
                // Vérification pour une rubrique
                if ($codeLower === 'rubrique') {
                    $rubrique = Rubrique::create([
                        'nom' => $designation,
                        'type' => 'bpu',
                        'id_soussession' => $currentSousCategorieId,
                    ]);
                    $currentRubriqueId = $rubrique->id;
                    Log::info("Rubrique créée: $designation (ID: $currentRubriqueId)");
                    continue;
                }
                
                // Si on arrive ici, c'est probablement une ligne BPU
                // Récupération des champs dans l'ordre spécifié :
                // code, Désignation, Unité, Matériaux, T.MO (%), T.MAT (%), T.FC (%), T.FG (%), T.BEN (%)
                $materiaux = $this->parseNumeric($row[3] ?? 0);
                $tauxMO = $this->parseNumeric($row[4] ?? 0);
                $tauxMAT = $this->parseNumeric($row[5] ?? 0);
                $tauxFC = $this->parseNumeric($row[6] ?? 0);
                $tauxFG = $this->parseNumeric($row[7] ?? 0);
                $tauxBEN = $this->parseNumeric($row[8] ?? 0);
                
                // Création de la ligne BPU avec les valeurs de base
                $bpu = Bpu::create([
                    // 'code' => $code,
                    'designation' => $designation,
                    'unite' => $unite,
                    'qte' => 1,
                    'materiaux' => $materiaux,
                    'taux_mo' => $tauxMO,
                    'taux_mat' => $tauxMAT,
                    'taux_fc' => $tauxFC,
                    'taux_fg' => $tauxFG,
                    'taux_benefice' => $tauxBEN,
                    'id_rubrique' => $currentRubriqueId,
                ]);
                
                // Calculer automatiquement toutes les valeurs dérivées
                $bpu->updateDerivedValues();
                
                Log::info("BPU créé: $code - $designation");
                
            } catch (\Exception $e) {
                Log::error("Erreur lors du traitement de la ligne $index: " . $e->getMessage());
                continue; // Passer à la ligne suivante en cas d'erreur
            }
        }
    }
    
    /**
     * Vérifie si une ligne est un en-tête
     */
    private function isHeaderRow($row)
    {
        if (empty($row[0])) return false;
        
        // Convertir en minuscules pour la comparaison
        $firstCol = strtolower($this->cleanString($row[0]));
        
        // Mots-clés qui indiquent une ligne d'en-tête
        $headerKeywords = ['n°', 'numero', 'numéro', 'code', 'reference', 'référence', 'designation'];
        
        foreach ($headerKeywords as $keyword) {
            if (strpos($firstCol, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Nettoie une chaîne de caractères
     */
    private function cleanString($value)
    {
        if (!is_string($value)) {
            return '';
        }
        
        // Supprimer les espaces en début et fin de chaîne
        $value = trim($value);
        
        // Supprimer les caractères de contrôle invisibles
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
        
        return $value;
    }
    
    /**
     * Convertit une valeur en nombre
     */
    private function parseNumeric($value)
    {
        // Si la valeur est déjà un nombre, la retourner
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Si la valeur est une chaîne
        if (is_string($value)) {
            // Nettoyer la chaîne
            $value = trim($value);
            
            // Remplacer les séparateurs de milliers et la virgule décimale
            $value = str_replace([' ', ','], ['', '.'], $value);
            
            // Supprimer tout caractère non numérique sauf le point
            $value = preg_replace('/[^0-9.]/', '', $value);
            
            // Vérifier si c'est un nombre valide
            if (is_numeric($value)) {
                return (float) $value;
            }
        }
        
        // Par défaut, retourner 0
        return 0;
    }
}