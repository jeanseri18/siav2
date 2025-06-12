<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebourseChantier extends Model
{
    use HasFactory;

    protected $table = 'debourse_chantier';

    protected $fillable = [
        'reference', 'projet_id', 'contrat_id', 'dqe_id', 'montant_total', 'statut', 'notes'
    ];

    /**
     * Relation avec le projet
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Relation avec le contrat
     */
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    /**
     * Relation avec le DQE
     */
    public function dqe()
    {
        return $this->belongsTo(DQE::class);
    }

    /**
     * Relation avec les détails du déboursé chantier
     */
    public function details()
    {
        return $this->hasMany(DebourseChantierDetail::class, 'debourse_chantier_id');
    }

    /**
     * Mettre à jour le montant total
     */
    public function updateTotal()
    {
        $this->montant_total = $this->details->sum('montant_total');
        $this->save();
    }
}