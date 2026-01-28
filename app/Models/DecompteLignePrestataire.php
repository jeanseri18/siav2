<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecompteLignePrestataire extends Model
{
    protected $table = 'decompte_ligne_prestataires';
    
    protected $fillable = [
        'montant',
        'idprestation',
        'date',
        'pourcentage_globalpaye'
    ];
    
    protected $casts = [
        'montant' => 'decimal:2',
        'idprestation' => 'integer',
        'date' => 'date',
        'pourcentage_globalpaye' => 'decimal:2'
    ];
    
    public function prestation(): BelongsTo
    {
        return $this->belongsTo(Prestation::class, 'idprestation');
    }
}