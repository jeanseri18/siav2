<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debourse extends Model
{
    use HasFactory;

    protected $table = 'debourses';

    protected $fillable = [
        'reference', 'projet_id', 'contrat_id', 'dqe_id', 'type', 'montant_total', 'statut', 'notes'
    ];

    /**
     * Relation avec le contrat
     */
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }
    
    /**
     * Relation avec le projet
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Relation avec le DQE
     */
    public function dqe()
    {
        return $this->belongsTo(DQE::class);
    }

    /**
     * Relation avec les détails de déboursé
     */
    public function details()
    {
        return $this->hasMany(DebourseDetail::class, 'debourse_id');
    }

    /**
     * Mettre à jour le montant total
     */
    public function updateTotal()
    {
        $this->montant_total = $this->details->sum('montant');
        $this->save();
    }
}