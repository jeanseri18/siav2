<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebourseChantierDetail extends Model
{
    use HasFactory;

    protected $table = 'debourse_chantier_details';

    protected $fillable = [
        'debourse_chantier_id', 'dqe_ligne_id', 'section', 'designation', 'unite', 'quantite',
        'cout_unitaire_materiaux', 'cout_unitaire_main_oeuvre', 'cout_unitaire_materiel',
        'total_materiaux', 'total_main_oeuvre', 'total_materiel', 'montant_total'
    ];

    /**
     * Relation avec le déboursé chantier
     */
    public function debourseChantier()
    {
        return $this->belongsTo(DebourseChantier::class, 'debourse_chantier_id');
    }

    /**
     * Relation avec la ligne de DQE
     */
    public function dqeLigne()
    {
        return $this->belongsTo(DQELigne::class, 'dqe_ligne_id');
    }

    /**
     * Calculer le montant total
     */
    public function calculerMontantTotal()
    {
        $this->total_materiaux = $this->quantite * ($this->cout_unitaire_materiaux ?? 0);
        $this->total_main_oeuvre = $this->quantite * ($this->cout_unitaire_main_oeuvre ?? 0);
        $this->total_materiel = $this->quantite * ($this->cout_unitaire_materiel ?? 0);
        $this->montant_total = $this->total_materiaux + $this->total_main_oeuvre + $this->total_materiel;
        $this->save();
    }

    /**
     * Calculer le déboursé sec (matériaux + main d'œuvre)
     */
    public function getDebourseSecAttribute()
    {
        return ($this->total_materiaux ?? 0) + ($this->total_main_oeuvre ?? 0);
    }
}