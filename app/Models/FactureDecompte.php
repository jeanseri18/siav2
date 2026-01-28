<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureDecompte extends Model
{
    protected $fillable = [
        'facture_contrat_id',
        'numero',
        'date_facture',
        'pourcentage_avancement',
        'montant_ht',
        'montant_ttc',
        'statut',
        'observations'
    ];

    protected $casts = [
        'date_facture' => 'date',
        'pourcentage_avancement' => 'decimal:2',
        'montant_ht' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
    ];

    public function factureContrat()
    {
        return $this->belongsTo(FactureContrat::class);
    }

    public function lignes()
    {
        return $this->hasMany(FactureDecompteLigne::class);
    }
}