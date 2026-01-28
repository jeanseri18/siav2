<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureDecompteLigne extends Model
{
    protected $fillable = [
        'facture_decompte_id',
        'dqe_ligne_id',
        'quantite_realisee',
        'pourcentage_realise',
        'montant_ht',
        'observations'
    ];

    protected $casts = [
        'quantite_realisee' => 'decimal:2',
        'pourcentage_realise' => 'decimal:2',
        'montant_ht' => 'decimal:2',
    ];

    public function factureDecompte()
    {
        return $this->belongsTo(FactureDecompte::class);
    }

    public function dqeLigne()
    {
        return $this->belongsTo(DQELigne::class);
    }
}