<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebourseDetail extends Model
{
    use HasFactory;

    protected $table = 'debourse_details';

    protected $fillable = [
        'debourse_id', 'dqe_ligne_id', 'montant',
        'cout_unitaire_materiaux', 'cout_unitaire_main_oeuvre', 'cout_unitaire_materiel',
        'total_materiaux', 'total_main_oeuvre', 'total_materiel',
        'is_editable'
    ];

    /**
     * Relation avec le déboursé
     */
    public function debourse()
    {
        return $this->belongsTo(Debourse::class, 'debourse_id');
    }

    /**
     * Relation avec la ligne de DQE
     */
    public function dqeLigne()
    {
        return $this->belongsTo(DQELigne::class, 'dqe_ligne_id');
    }
}