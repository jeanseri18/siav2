<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre_localisation',
        'id_niveau',
        'id_contrat'
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'id_niveau');
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function corpsDeMetiers()
    {
        return $this->hasMany(CorpsDeMetier::class, 'id_localisation');
    }
}
