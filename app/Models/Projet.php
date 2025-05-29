<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_projet', 'date_creation', 'nom_projet', 'description', 'date_debut', 
        'date_fin', 'client', 'secteur_activite_id', 'conducteur_travaux', 
        'hastva', 'statut', 'bu_id'
    ];

    public function secteurActivite()
    {
        return $this->belongsTo(SecteurActivite::class);
    }

    public function bu()
    {
        return $this->belongsTo(BU::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'nom_projet', 'nom_projet');
    }
}
