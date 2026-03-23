<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorpsDeMetier extends Model
{
    use HasFactory;

    protected $table = 'corps_de_metiers';

    protected $fillable = [
        'id_localisation',
        'id_corpmetier',
        'nom_corpsdemetier',
        'id_contrat'
    ];

    public function localisation()
    {
        return $this->belongsTo(Localisation::class, 'id_localisation');
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function corpMetier()
    {
        return $this->belongsTo(CorpMetier::class, 'id_corpmetier');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'id_corps_de_metier');
    }

    // Attribut pour obtenir le nom du corps de métier
    public function getNomAttribute()
    {
        return $this->corpMetier ? $this->corpMetier->nom : $this->nom_corpsdemetier;
    }
}
