<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestation extends Model {
    use HasFactory;

    protected $table = 'prestation';
    protected $fillable = ['id_artisan', 'id_contrat', 'prestation_titre', 'detail', 'statut'];

    public function artisan() {
        return $this->belongsTo(Artisan::class, 'id_artisan');
    }

    public function contrat() {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }
}
