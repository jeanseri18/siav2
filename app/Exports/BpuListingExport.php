<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;

class BpuListingExport implements FromArray
{
    public function __construct(
        private Collection $categories
    ) {}

    public function array(): array
    {
        $rows = [];
        $rows[] = [
            'Catégorie',
            'Sous-catégorie',
            'Rubrique',
            'Code',
            'Désignation',
            'Unité',
            'Matériaux',
            'Taux MO (%)',
            'Main d\'œuvre',
            'Taux MAT (%)',
            'Matériel',
            'DS',
            'Taux FC (%)',
            'FC',
            'Taux FG (%)',
            'Frais généraux',
            'Taux Bénéfice (%)',
            'Bénéfice',
            'PU HT',
        ];

        foreach ($this->categories as $categorie) {
            foreach ($categorie->sousCategories as $sousCategorie) {
                foreach ($sousCategorie->rubriques as $rubrique) {
                    foreach ($rubrique->bpus as $bpu) {
                        $rows[] = [
                            $categorie->nom,
                            $sousCategorie->nom,
                            $rubrique->nom,
                            $categorie->id.'.'.$sousCategorie->id.'.'.$rubrique->id.'.'.$bpu->id,
                            $bpu->designation,
                            $bpu->unite,
                            $bpu->materiaux,
                            $bpu->taux_mo,
                            $bpu->main_oeuvre,
                            $bpu->taux_mat,
                            $bpu->materiel,
                            $bpu->debourse_sec,
                            $bpu->taux_fc,
                            $bpu->frais_chantier,
                            $bpu->taux_fg,
                            $bpu->frais_general,
                            $bpu->taux_benefice,
                            $bpu->marge_nette,
                            $bpu->pu_ht,
                        ];
                    }
                }
            }
        }

        return $rows;
    }
}
