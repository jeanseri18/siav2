<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reception extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bon_commande_id',
        'numero_reception',
        'date_reception',
        'numero_bon_livraison',
        'transporteur',
        'observations',
        'statut',
        'user_id',
        'quantite_totale_recue',
        'montant_total_recu'
    ];

    protected $casts = [
        'date_reception' => 'datetime',
        'quantite_totale_recue' => 'decimal:2',
        'montant_total_recu' => 'decimal:2'
    ];

    protected $dates = [
        'date_reception',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relation avec le bon de commande
     */
    public function bonCommande()
    {
        return $this->belongsTo(BonCommande::class);
    }

    /**
     * Relation avec l'utilisateur qui a effectué la réception
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les lignes de réception
     */
    public function lignes()
    {
        return $this->hasMany(LigneReception::class);
    }

    /**
     * Scope pour les réceptions par statut
     */
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les réceptions d'une période
     */
    public function scopeByPeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_reception', [$dateDebut, $dateFin]);
    }

    /**
     * Génère automatiquement un numéro de réception
     */
    public static function generateNumeroReception()
    {
        $lastReception = self::orderBy('id', 'desc')->first();
        $lastNumber = $lastReception ? intval(substr($lastReception->numero_reception, -6)) : 0;
        $newNumber = $lastNumber + 1;
        
        return 'REC-' . date('Y') . '-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcule le pourcentage de réception du bon de commande
     */
    public function getPourcentageReceptionAttribute()
    {
        $bonCommande = $this->bonCommande;
        if (!$bonCommande) return 0;
        
        $totalCommande = $bonCommande->lignes->sum('quantite');
        $totalRecu = $bonCommande->lignes->sum('quantite_recue');
        
        return $totalCommande > 0 ? round(($totalRecu / $totalCommande) * 100, 2) : 0;
    }

    /**
     * Vérifie si la réception est complète
     */
    public function getIsCompleteAttribute()
    {
        return $this->pourcentage_reception >= 100;
    }

    /**
     * Retourne le statut formaté
     */
    public function getStatutFormateAttribute()
    {
        $statuts = [
            'en_cours' => 'En cours',
            'complete' => 'Complète',
            'partielle' => 'Partielle',
            'annulee' => 'Annulée'
        ];
        
        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Retourne la classe CSS pour le badge de statut
     */
    public function getStatutBadgeClassAttribute()
    {
        $classes = [
            'en_cours' => 'bg-warning',
            'complete' => 'bg-success',
            'partielle' => 'bg-info',
            'annulee' => 'bg-danger'
        ];
        
        return $classes[$this->statut] ?? 'bg-secondary';
    }
}