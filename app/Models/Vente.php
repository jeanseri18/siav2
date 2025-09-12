<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model {
    use HasFactory;

    protected $fillable = [
        'client_id', 
        'devis_id',
        'numero_client', 
        'nom_client', 
        'commentaire',
        'total_ht', 
        'tva', 
        'total_ttc', 
        'statut'
    ];

    protected $casts = [
        'total_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2'
    ];

    public function client() {
        return $this->belongsTo(ClientFournisseur::class, 'client_id');
    }

    public function devis() {
        return $this->belongsTo(Devis::class, 'devis_id');
    }

    public function articles() {
        return $this->belongsToMany(Article::class, 'vente_articles')
            ->withPivot('quantite', 'prix_unitaire_ht', 'montant_total')
            ->withTimestamps();
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

    // Maintenir la compatibilité avec l'ancien champ 'total'
    public function getTotalAttribute()
    {
        return $this->total_ttc;
    }
}
