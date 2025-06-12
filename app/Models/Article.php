<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'reference_fournisseur',
        'nom',
        'type',
        'quantite_stock',
        'prix_unitaire',
        'unite_mesure',
        'cout_moyen_pondere',
        'categorie_id',
        'sous_categorie_id',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorie::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'reference_fournisseur');
    }

    public function unite()
    {
        return $this->belongsTo(UniteMesure::class, 'unite_mesure');
    }
}