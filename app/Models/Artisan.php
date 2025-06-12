<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artisan extends Model {
    use HasFactory;

    protected $table = 'artisan';
    protected $fillable = [
        'reference',
        'nom',
        'civilite',
        'prenoms',
        'type_piece',
        'numero_piece',
        'date_naissance',
        'nationalite',
        'fonction',
        'localisation',
        'rcc',
        'rccm',
        'boite_postale',
        'tel1',
        'tel2',
        'mail'
    ];

    public function contrats() {
        return $this->hasMany(Contrat::class, 'id_artisan');
    }

    public function prestations() {
        return $this->hasMany(Prestation::class, 'id_artisan');
    }

    public function factures() {
        return $this->hasMany(Facture::class, 'id_artisan');
    }
}
