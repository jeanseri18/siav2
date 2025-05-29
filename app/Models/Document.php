<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {
    use HasFactory;

    protected $table = 'document';
    protected $fillable = ['nom', 'chemin', 'id_projet', 'id_contrat', 'id_facture'];

    public function projet() {
        return $this->belongsTo(Projet::class, 'id_projet');
    }

    public function contrat() {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function facture() {
        return $this->belongsTo(Facture::class, 'id_facture');
    }
}
