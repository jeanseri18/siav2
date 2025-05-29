<?php

namespace App\Imports;

use App\Models\CategorieRubrique;
use Maatwebsite\Excel\Concerns\ToModel;

class CategoriesImport implements ToModel
{
    /**
     * Transforme une ligne du fichier Excel en modèle.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new CategorieRubrique([
            'nom' => $row[0], // Nom de la catégorie
            'type' => 'bpu', // Définir une valeur par défaut
        ]);
    }
}
