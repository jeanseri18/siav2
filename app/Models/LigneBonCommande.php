<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneBonCommande extends Model
{
    use HasFactory;

    protected $table = 'lignes_bon_commande';

    protected $fillable = [
        'bon_commande_id',
        'article_id',
        'quantite',
        'prix_unitaire',
        'quantite_livree',
        'quantite_recue',
        'commentaire'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2'
    ];

    public function bonCommande()
    {
        return $this->belongsTo(BonCommande::class, 'bon_commande_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function getMontantAttribute()
    {
        return $this->quantite * $this->prix_unitaire;
    }

    /**
     * Relation avec les lignes de réception
     */
    public function lignesReception()
    {
        return $this->hasMany(LigneReception::class);
    }

    /**
     * Calcule la quantité restante à recevoir
     */
    public function getQuantiteRestanteAttribute()
    {
        return $this->quantite - $this->quantite_recue;
    }

    /**
     * Calcule le pourcentage de réception
     */
    public function getPourcentageReceptionAttribute()
    {
        if ($this->quantite == 0) return 0;
        return round(($this->quantite_recue / $this->quantite) * 100, 2);
    }

    /**
     * Vérifie si la ligne est entièrement reçue
     */
    public function getIsCompleteAttribute()
    {
        return $this->quantite_recue >= $this->quantite;
    }

    /**
     * Vérifie si la ligne est partiellement reçue
     */
    public function getIsPartielleAttribute()
    {
        return $this->quantite_recue > 0 && $this->quantite_recue < $this->quantite;
    }

    /**
     * Scope pour les lignes non reçues
     */
    public function scopeNonRecues($query)
    {
        return $query->where('quantite_recue', 0);
    }

    /**
     * Scope pour les lignes partiellement reçues
     */
    public function scopePartiellesRecues($query)
    {
        return $query->whereColumn('quantite_recue', '<', 'quantite')
                    ->where('quantite_recue', '>', 0);
    }

    /**
     * Scope pour les lignes complètement reçues
     */
    public function scopeCompleteRecues($query)
    {
        return $query->whereColumn('quantite_recue', '>=', 'quantite');
    }
}