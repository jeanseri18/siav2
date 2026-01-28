<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 
        'numero_client', 
        'nom_client', 
        'commentaire',
        'total_ht', 
        'tva', 
        'total_ttc', 
        'statut',
        'utilise_pour_vente',
        'user_id',
        'ref_devis'
    ];

    protected $casts = [
        'utilise_pour_vente' => 'boolean',
        'total_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2'
    ];

    public function client()
    {
        return $this->belongsTo(ClientFournisseur::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'devis_articles')
            ->withPivot('quantite', 'prix_unitaire_ht', 'montant_total', 'remise')
            ->withTimestamps();
    }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    /**
     * Récupérer la première vente associée (s'il y en a)
     */
    public function vente()
    {
        return $this->hasOne(Vente::class);
    }

    /**
     * Vérifier si le devis est utilisé pour une vente
     */
    public function getUtiliseAttribute()
    {
        return $this->utilise_pour_vente;
    }

    /**
     * Calculer le total HT
     */
    public function calculerTotalHT()
    {
        return $this->articles->sum('pivot.montant_total');
    }

    /**
     * Calculer la TVA (18%)
     */
    public function calculerTVA()
    {
        return $this->total_ht * 0.18;
    }

    /**
     * Calculer le total TTC
     */
    public function calculerTotalTTC()
    {
        return $this->total_ht + $this->calculerTVA();
    }

    /**
     * Scope pour les devis non utilisés
     */
    public function scopeNonUtilises($query)
    {
        return $query->where('utilise_pour_vente', false);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Générer une référence unique pour le devis
     */
    public static function generateRefDevis()
    {
        $now = now();
        $timestamp = $now->format('YmdHis');
        return 'DEV_' . $timestamp;
    }
}