<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeApprovisionnement extends Model
{
    use HasFactory;

    protected $table = 'demande_approvisionnements';

    protected $fillable = [
        'reference',
        'date_demande',
        'date_reception',
        'initiateur',
        'projet_id',
        'user_id',
        'statut',
        'motif_rejet',
        'approved_by'
    ];

    protected $casts = [
        'date_demande' => 'date',
        'date_reception' => 'date'
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
        return $this->hasMany(LigneDemandeApprovisionnement::class, 'demande_approvisionnement_id');
    }

    public function bonCommandes()
    {
        return $this->hasMany(BonCommande::class, 'demande_approvisionnement_id');
    }
}