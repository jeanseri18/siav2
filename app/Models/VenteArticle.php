<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenteArticle extends Model {
    use HasFactory;

    protected $table = 'vente_articles';
    protected $fillable = ['vente_id', 'article_id', 'quantite', 'prix_unitaire', 'sous_total'];
}
