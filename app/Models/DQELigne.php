<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DQELigne extends Model
{
    use HasFactory;

    protected $table = 'dqe_lignes';

    protected $fillable = [
        'dqe_id', 'id_rubrique', 'code', 'section', 'designation', 'quantite', 
        'unite', 'pu_ht', 'montant_ht', 'materiaux', 'mo', 'materiel', 
        'frais_chantier', 'frais_generaux', 'benefice'
    ];

    /**
     * Relation avec le DQE
     */
    public function dqe()
    {
        return $this->belongsTo(DQE::class, 'dqe_id');
    }

    /**
     * Relation avec la rubrique
     */
    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class, 'id_rubrique');
    }

    /**
     * REMOVED: Relation avec les détails de déboursé supprimée
     * Les déboursés ont été supprimés du système
     */

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