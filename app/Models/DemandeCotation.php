<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCotation extends Model
{
    use HasFactory;

    protected $table = 'demande_cotations';

    protected $fillable = [
        'reference',
        'date_demande',
        'demande_achat_id',
        'date_expiration',
        'description',
        'user_id',
        'statut',
        'conditions_generales'
    ];

    protected $casts = [
        'date_demande' => 'date',
        'date_expiration' => 'date'
    ];

    public function demandeAchat()
    {
        return $this->belongsTo(DemandeAchat::class, 'demande_achat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fournisseurs()
    {
        return $this->hasMany(FournisseurDemandeCotation::class, 'demande_cotation_id');
    }

    public function lignes()
    {
        return $this->hasMany(LigneDemandeCotation::class, 'demande_cotation_id');
    }
}