<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeAchat extends Model
{
    use HasFactory;

    protected $table = 'demande_achats';

    protected $fillable = [
        'reference',
        'date_demande',
        'description',
        'projet_id',
        'user_id',
        'priorite',
        'statut',
        'motif_rejet',
        'approved_by',
        'date_besoin'
    ];

    protected $casts = [
        'date_demande' => 'date',
        'date_besoin' => 'date'
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approbateur()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function lignes()
    {
        return $this->hasMany(LigneDemandeAchat::class, 'demande_achat_id');
    }

    public function demandeCotations()
    {
        return $this->hasMany(DemandeCotation::class, 'demande_achat_id');
    }

    public function bonCommandes()
    {
        return $this->hasMany(BonCommande::class, 'demande_achat_id');
    }
}