<?php

namespace App\Imports;

use App\Models\Bpu;
use Maatwebsite\Excel\Concerns\ToModel;

class BpusImport implements ToModel
{
    /**
     * Transforme une ligne du fichier Excel en modèle.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Créer le BPU avec les valeurs de base
        $bpu = new Bpu([
            'designation' => $row[0], // Désignation de l'élément
            'qte' => $row[1], // Quantité
            'materiaux' => $row[2], // Matériaux
            'main_oeuvre' => $row[3], // Main d'oeuvre
            'materiel' => $row[4], // Matériel
            'unite' => $row[5], // Unité
            'id_souscategorie' => $row[6], // ID de la sous-catégorie
        ]);

        // Appliquer les calculs automatiques en utilisant la méthode du modèle
        $bpu->updateDerivedValues();

        return $bpu;
    
}
}