<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetourApprovisionnement extends Model
{
    protected $table = 'retour_approvisionnement';
    
    protected $fillable = [
        'bon_commande_id',
        'article_id',
        'projet_id',
        'quantite_retournee',
        'date_retour',
        'motif',
        'statut'
    ];

    protected $casts = [
        'date_retour' => 'date',
    ];

    public function bonCommande(): BelongsTo
    {
        return $this->belongsTo(BonCommande::class, 'bon_commande_id');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }
}