<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FournisseurDemandeCotation extends Model
{
    use HasFactory;

    protected $table = 'fournisseurs_demande_cotation';

    protected $fillable = [
        'demande_cotation_id',
        'fournisseur_id',
        'repondu',
        'date_reponse',
        'montant_total',
        'retenu',
        'commentaire'
    ];

    protected $casts = [
        'repondu' => 'boolean',
        'date_reponse' => 'date',
        'montant_total' => 'decimal:2',
        'retenu' => 'boolean'
    ];

    public function demandeCotation()
    {
        return $this->belongsTo(DemandeCotation::class, 'demande_cotation_id');
    }

    public function fournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'fournisseur_id');
    }
}