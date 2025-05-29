<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorpMetier extends Model
{
    use HasFactory;
    protected $table = 'corp_metiers';

    protected $fillable = ['nom'];

    // Relation avec Artisan
    public function artisans() {
        return $this->hasMany(Artisan::class, 'id_corpmetier');
    }
}
