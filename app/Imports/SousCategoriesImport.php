<?php

namespace App\Imports;

use App\Models\SousCategorieRubrique;
use Maatwebsite\Excel\Concerns\ToModel;

class SousCategoriesImport implements ToModel
{
    /**
     * Transforme une ligne du fichier Excel en modèle.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new SousCategorieRubrique([
            'nom' => $row[0], // Nom de la sous-catégorie
            'id_session' => $row[1], // ID de la session
            'type' => 'bpu', // Définir une valeur par défaut
        ]);
    }
}
