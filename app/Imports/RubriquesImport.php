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
                // Récupérer et nettoyer les valeurs numériques
                $materiaux = $this->parseNumeric($row[3] ?? 0);
                $mainOeuvre = $this->parseNumeric($row[4] ?? 0);
                $materiel = $this->parseNumeric($row[5] ?? 0);
                
                // Si toutes les valeurs sont 0, essayer de prendre les valeurs calculées dans le fichier
                if ($materiaux == 0 && $mainOeuvre == 0 && $materiel == 0) {
                    $debourse_sec = $this->parseNumeric($row[6] ?? 0);
                    $frais_chantier = $this->parseNumeric($row[7] ?? 0);
                    $frais_general = $this->parseNumeric($row[8] ?? 0);
                    $marge_nette = $this->parseNumeric($row[9] ?? 0);
                    $pu_ht = $this->parseNumeric($row[10] ?? 0);
                    
                    // Si on a un déboursé sec, essayer de déduire les composants
                    if ($debourse_sec > 0) {
                        // Estimation approximative basée sur les proportions courantes
                        $materiaux = $debourse_sec * 0.60;
                        $mainOeuvre = $debourse_sec * 0.30;
                        $materiel = $debourse_sec * 0.10;
                    }
                }
                
                // Calculs automatiques
                $ds = $materiaux + $mainOeuvre + $materiel;
                $fc = $ds * 0.30; // 30%
                $fg = ($ds + $fc) * 0.15; // 15%
                $mn = ($ds + $fc + $fg) * 0.15; // 15%
                $pu_ht = $ds + $fc + $fg + $mn;
                $pu_ttc = $pu_ht * 1.18; // TVA 18%
                
                // Création de la ligne BPU
                Bpu::create([
                    'designation' => $designation,
                    'qte' => 1,
                    'materiaux' => $materiaux,
                    'main_oeuvre' => $mainOeuvre,
                    'materiel' => $materiel,
                    'unite' => $unite,
                    'debourse_sec' => $ds,
                    'frais_chantier' => $fc,
                    'frais_general' => $fg,
                    'marge_nette' => $mn,
                    'pu_ht' => $pu_ht,
                    'pu_ttc' => $pu_ttc,
                    'id_rubrique' => $currentRubriqueId,
                ]);
                
                Log::info("BPU créé: $designation");
                
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