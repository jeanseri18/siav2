<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LignePrestation extends Model
{
    use HasFactory;
    
    protected $table = 'ligne_prestations';
    
    protected $fillable = [
        'designation',
        'unite',
        'quantite',
        'cout_unitaire',
        'taux_avancement',
        'montant',
        'montant_paye',
        'montant_reste',
        'id_rubrique',
        'id_prestation'
    ];
    
    /**
     * Relation avec la prestation
     */
    public function prestation()
    {
        return $this->belongsTo(Prestation::class, 'id_prestation');
    }
    
    /**
     * Relation avec la rubrique
     */
    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class, 'id_rubrique');
    }
    
    /**
     * Calculer et mettre à jour les montants
     */
    public function calculerMontants()
    {
        // Montant total = Quantité x Coût unitaire
        $this->montant = $this->quantite * $this->cout_unitaire;
        
        // Montant payé = Montant total x Taux d'avancement / 100
        $this->montant_paye = ($this->montant * $this->taux_avancement) / 100;
        
        // Montant restant = Montant total - Montant payé
        $this->montant_reste = $this->montant - $this->montant_paye;
        
        $this->save();
    }
}
