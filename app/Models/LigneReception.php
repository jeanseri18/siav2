<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LigneReception extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reception_id',
        'ligne_bon_commande_id',
        'article_id',
        'quantite_recue',
        'quantite_conforme',
        'quantite_non_conforme',
        'prix_unitaire_recu',
        'observations',
        'numero_lot',
        'date_peremption',
        'etat_article'
    ];

    protected $casts = [
        'quantite_recue' => 'decimal:2',
        'quantite_conforme' => 'decimal:2',
        'quantite_non_conforme' => 'decimal:2',
        'prix_unitaire_recu' => 'decimal:2',
        'date_peremption' => 'date'
    ];

    protected $dates = [
        'date_peremption',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relation avec la réception
     */
    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    /**
     * Relation avec la ligne de bon de commande
     */
    public function ligneBonCommande()
    {
        return $this->belongsTo(LigneBonCommande::class);
    }

    /**
     * Relation avec l'article
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Calcule le montant total de la ligne
     */
    public function getMontantTotalAttribute()
    {
        return $this->quantite_recue * $this->prix_unitaire_recu;
    }

    /**
     * Calcule le pourcentage de conformité
     */
    public function getPourcentageConformiteAttribute()
    {
        if ($this->quantite_recue == 0) return 0;
        
        return round(($this->quantite_conforme / $this->quantite_recue) * 100, 2);
    }

    /**
     * Vérifie si la ligne est entièrement conforme
     */
    public function getIsConformeAttribute()
    {
        return $this->quantite_non_conforme == 0 && $this->quantite_recue > 0;
    }

    /**
     * Retourne l'état formaté de l'article
     */
    public function getEtatArticleFormateAttribute()
    {
        $etats = [
            'neuf' => 'Neuf',
            'bon' => 'Bon état',
            'acceptable' => 'Acceptable',
            'defectueux' => 'Défectueux',
            'endommage' => 'Endommagé'
        ];
        
        return $etats[$this->etat_article] ?? $this->etat_article;
    }

    /**
     * Retourne la classe CSS pour le badge d'état
     */
    public function getEtatBadgeClassAttribute()
    {
        $classes = [
            'neuf' => 'bg-success',
            'bon' => 'bg-primary',
            'acceptable' => 'bg-warning',
            'defectueux' => 'bg-danger',
            'endommage' => 'bg-dark'
        ];
        
        return $classes[$this->etat_article] ?? 'bg-secondary';
    }

    /**
     * Scope pour les lignes conformes
     */
    public function scopeConformes($query)
    {
        return $query->where('quantite_non_conforme', 0)
                    ->where('quantite_recue', '>', 0);
    }

    /**
     * Scope pour les lignes non conformes
     */
    public function scopeNonConformes($query)
    {
        return $query->where('quantite_non_conforme', '>', 0);
    }

    /**
     * Scope pour les lignes par état d'article
     */
    public function scopeByEtat($query, $etat)
    {
        return $query->where('etat_article', $etat);
    }
}