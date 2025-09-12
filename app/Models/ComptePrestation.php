<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComptePrestation extends Model
{
    use HasFactory;

    protected $table = 'comptes_prestations';

    protected $fillable = [
        'prestation_id',
        'type_compte',
        'montant',
        'description',
        'date_compte',
        'created_by'
    ];

    protected $casts = [
        'date_compte' => 'date',
        'montant' => 'decimal:2'
    ];

    /**
     * Relation avec la prestation
     */
    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }

    /**
     * Relation avec l'utilisateur qui a créé le compte
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour filtrer par type de compte
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type_compte', $type);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeByPeriod($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_compte', [$dateDebut, $dateFin]);
    }

    /**
     * Calculer le total des comptes par type
     */
    public static function getTotalByType($prestationId, $type)
    {
        return self::where('prestation_id', $prestationId)
                   ->where('type_compte', $type)
                   ->sum('montant');
    }

    /**
     * Calculer le total général des comptes pour une prestation
     */
    public static function getTotalGeneral($prestationId)
    {
        return self::where('prestation_id', $prestationId)->sum('montant');
    }
}