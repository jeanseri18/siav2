<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $table = 'niveaux';

    protected $fillable = [
        'id_lot',
        'titre_niveau',
        'id_contrat'
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'id_lot');
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function localisations()
    {
        return $this->hasMany(Localisation::class, 'id_niveau');
    }
}
