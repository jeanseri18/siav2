<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_contrat',
        'id_souscategorie',
        'nom_tache_planning',
        'date_debut',
        'date_fin',
        'statut'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date'
    ];

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function sousCategorie()
    {
        return $this->belongsTo(\App\Models\SousCategorieRubrique::class, 'id_souscategorie');
    }

    public function getStatutLibelleAttribute()
    {
        $statuts = [
            'non_demarre' => 'Non démarré',
            'en_cours' => 'En cours',
            'retard' => 'En retard',
            'termine' => 'Terminé'
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute()
    {
        $colors = [
            'non_demarre' => '#6c757d',
            'en_cours' => '#0dcaf0',
            'retard' => '#dc3545',
            'termine' => '#198754'
        ];

        return $colors[$this->statut] ?? '#6c757d';
    }

    // Calculer la durée en jours
    public function getDureeJoursAttribute()
    {
        if ($this->date_debut && $this->date_fin) {
            return $this->date_debut->diffInDays($this->date_fin) + 1;
        }
        return 0;
    }
}
