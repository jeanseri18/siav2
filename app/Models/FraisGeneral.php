<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisGeneral extends Model
{
    use HasFactory;

    protected $table = 'frais_generals';

    protected $fillable = [
        'contrat_id',
        'montant_base',
        'pourcentage',
        'montant_total',
        'description',
        'date_calcul',
        'statut'
    ];

    /**
     * Relation avec le contrat
     */
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    /**
     * Calcule le montant des frais généraux
     */
    public function calculerMontant()
    {
        $this->montant_total = $this->montant_base * ($this->pourcentage / 100);
        $this->save();
    }
}