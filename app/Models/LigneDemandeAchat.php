<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneDemandeAchat extends Model
{
    use HasFactory;

    protected $table = 'lignes_demande_achat';

    protected $fillable = [
        'demande_achat_id',
        'article_id',
        'designation',
        'quantite',
        'unite_mesure',
        'prix_estime',
        'specifications',
        'commentaire'
    ];

    protected $casts = [
        'prix_estime' => 'decimal:2'
    ];

    public function demandeAchat()
    {
        return $this->belongsTo(DemandeAchat::class, 'demande_achat_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}