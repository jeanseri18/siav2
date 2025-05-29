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
        // Calculs automatiques
        $ds = $row[3] + $row[4] + $row[5];
        $fc = $ds * 0.30;
        $fg = ($ds + $fc) * 0.15;
        $mn = ($ds + $fc + $fg) * 0.15;
        $pu_ht = $ds + $fc + $fg + $mn;
        $pu_ttc = $pu_ht * 1.18;

        return new Bpu([
            'designation' => $row[0], // Désignation de l'élément
            'qte' => $row[1], // Quantité
            'materiaux' => $row[2], // Matériaux
            'main_oeuvre' => $row[3], // Main d'oeuvre
            'materiel' => $row[4], // Matériel
            'unite' => $row[5], // Unité
            'debourse_sec' => $ds, // Déboursé S
            'frais_chantier' => $fc, // Frais de chantier
            'frais_general' => $fg, // Frais généraux
            'marge_nette' => $mn, // Marge nette
            'pu_ht' => $pu_ht, // Prix HT
            'pu_ttc' => $pu_ttc, // Prix TTC
            'id_souscategorie' => $row[6], // ID de la sous-catégorie
        ]);
    }
}
