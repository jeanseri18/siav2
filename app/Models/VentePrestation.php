<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentePrestation extends Model
{
    protected $table = 'vente_prestations';
    
    protected $fillable = [
        'vente_id',
        'nom_prestation',
        'quantite',
        'prix_unitaire',
        'montant_total'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'montant_total' => 'decimal:2'
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }
}
