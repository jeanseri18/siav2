<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artisan extends Model {
    use HasFactory;

    protected $table = 'artisan';
    protected $fillable = ['nom', 'id_corpmetier', 'type'];

    public function contrats() {
        return $this->hasMany(Contrat::class, 'id_artisan');
    }


    // Relation avec Corps de Métier
    public function corpMetier() {
        return $this->belongsTo(CorpMetier::class, 'id_corpmetier');
    }


    public function prestations() {
        return $this->hasMany(Prestation::class, 'id_artisan');
    }

    public function factures() {
        return $this->hasMany(Facture::class, 'id_artisan');
    }
}
