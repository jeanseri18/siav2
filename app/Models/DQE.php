<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DQE extends Model
{
    use HasFactory;

    protected $table = 'dqes';

    protected $fillable = [
        'contrat_id', 'reference', 'montant_total_ht', 'montant_total_ttc',
        'statut', 'notes'
    ];

    /**
     * Relation avec le contrat
     */
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    /**
     * Relation avec les lignes de DQE
     */
    public function lignes()
    {
        return $this->hasMany(DQELigne::class, 'dqe_id');
    }

    /**
     * Relation avec les déboursés
     */
    public function debourses()
    {
        return $this->hasMany(Debourse::class, 'dqe_id');
    }

    /**
     * Mettre à jour les montants totaux
     */
    public function updateTotals()
    {
        $this->montant_total_ht = $this->lignes->sum('montant_ht');
        $this->montant_total_ttc = $this->montant_total_ht * 1.18; // TVA 18%
        $this->save();
    }
}