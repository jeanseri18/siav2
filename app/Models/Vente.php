<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model {
    use HasFactory;

    protected $fillable = ['client_id', 'total', 'statut'];

    public function client() {
        return $this->belongsTo(ClientFournisseur::class, 'client_id');
    }

    public function articles() {
        return $this->belongsToMany(Article::class, 'vente_articles')
            ->withPivot('quantite', 'prix_unitaire', 'sous_total')
            ->withTimestamps();
    }
}
