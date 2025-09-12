<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'stock_projet_id',
        'type_mouvement',
        'quantite',
        'quantite_avant',
        'quantite_apres',
        'reference_mouvement',
        'commentaires',
        'date_mouvement',
        'user_id',
        'donnees_supplementaires'
    ];

    protected $casts = [
        'date_mouvement' => 'date',
        'donnees_supplementaires' => 'array',
        'quantite' => 'decimal:2',
        'quantite_avant' => 'decimal:2',
        'quantite_apres' => 'decimal:2'
    ];

    /**
     * Relation avec le stock projet
     */
    public function stockProjet(): BelongsTo
    {
        return $this->belongsTo(StockProjet::class, 'stock_projet_id');
    }

    /**
     * Relation avec l'utilisateur qui a effectué le mouvement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour filtrer par type de mouvement
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type_mouvement', $type);
    }

    /**
     * Scope pour filtrer par stock projet
     */
    public function scopeByStockProjet($query, $stockProjetId)
    {
        return $query->where('stock_projet_id', $stockProjetId);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeByPeriod($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_mouvement', [$dateDebut, $dateFin]);
    }

    /**
     * Obtenir le libellé du type de mouvement
     */
    public function getTypeMouvementLibelleAttribute()
    {
        $libelles = [
            'entree' => 'Entrée en stock',
            'sortie' => 'Sortie de stock',
            'transfert' => 'Transfert',
            'livraison_chantier' => 'Livraison Chantier',
            'retour_chantier' => 'Retour Chantier',
            'retour_projet' => 'Retour Projet'
        ];

        return $libelles[$this->type_mouvement] ?? $this->type_mouvement;
    }

    /**
     * Obtenir l'icône du type de mouvement
     */
    public function getTypeMouvementIconeAttribute()
    {
        $icones = [
            'entree' => 'fas fa-plus-circle text-success',
            'sortie' => 'fas fa-minus-circle text-danger',
            'transfert' => 'fas fa-exchange-alt text-info',
            'livraison_chantier' => 'fas fa-truck text-warning',
            'retour_chantier' => 'fas fa-undo text-info',
            'retour_projet' => 'fas fa-reply text-primary'
        ];

        return $icones[$this->type_mouvement] ?? 'fas fa-circle';
    }

    /**
     * Créer un mouvement de stock
     */
    public static function creerMouvement(
        $stockProjetId,
        $typeMouvement,
        $quantite,
        $quantiteAvant,
        $quantiteApres,
        $dateMouvement = null,
        $commentaires = null,
        $referenceMouvement = null,
        $donneesSupplementaires = null
    ) {
        return self::create([
            'stock_projet_id' => $stockProjetId,
            'type_mouvement' => $typeMouvement,
            'quantite' => $quantite,
            'quantite_avant' => $quantiteAvant,
            'quantite_apres' => $quantiteApres,
            'date_mouvement' => $dateMouvement ?? now()->toDateString(),
            'commentaires' => $commentaires,
            'reference_mouvement' => $referenceMouvement,
            'user_id' => auth()->id(),
            'donnees_supplementaires' => $donneesSupplementaires
        ]);
    }
}