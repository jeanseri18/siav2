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
}