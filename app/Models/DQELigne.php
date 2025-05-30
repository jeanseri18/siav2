<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DQELigne extends Model
{
    use HasFactory;

    protected $table = 'dqe_lignes';

    protected $fillable = [
        'dqe_id', 'bpu_id', 'code', 'designation', 'quantite', 
        'unite', 'pu_ht', 'montant_ht'
    ];

    /**
     * Relation avec le DQE
     */
    public function dqe()
    {
        return $this->belongsTo(DQE::class, 'dqe_id');
    }

    /**
     * Relation avec le BPU
     */
    public function bpu()
    {
        return $this->belongsTo(Bpu::class, 'bpu_id');
    }

    /**
     * Relation avec les détails de déboursé
     */
    public function debourseDetails()
    {
        return $this->hasMany(DebourseDetail::class, 'dqe_ligne_id');
    }

    /**
     * Calculer le montant HT en fonction de la quantité et du prix unitaire
     */
    public function calculerMontant()
    {
        $this->montant_ht = $this->quantite * $this->pu_ht;
        $this->save();
        
        // Mettre à jour les totaux du DQE parent
        $this->dqe->updateTotals();
    }
}