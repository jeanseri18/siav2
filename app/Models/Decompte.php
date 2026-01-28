<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decompte extends Model
{
    use HasFactory;
    
    protected $table = 'decomptes';
    
    protected $fillable = [
        'titre',
        'montant',
        'pourcentage',
        'id_prestation'
    ];
    
    /**
     * Relation avec la prestation
     */
    public function prestation()
    {
        return $this->belongsTo(Prestation::class, 'id_prestation');
    }
}
