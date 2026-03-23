<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_corps_de_metier',
        'description',
        'date_debut',
        'date_fin',
        'nbre_jr_previsionnelle',
        'nbre_de_jr_realise',
        'progression',
        'statut',
        'id_contrat',
        'image'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'progression' => 'decimal:2'
    ];

    // Événement pour mettre à jour automatiquement nbre_de_jr_realise quand progression = 100%
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tache) {
            if ($tache->progression >= 100 && $tache->date_debut && $tache->date_fin) {
                $dateDebut = \Carbon\Carbon::parse($tache->date_debut);
                $dateFin = \Carbon\Carbon::parse($tache->date_fin);
                $tache->nbre_de_jr_realise = $dateDebut->diffInDays($dateFin);
            }
        });
    }

    public function corpsDeMetier()
    {
        return $this->belongsTo(CorpsDeMetier::class, 'id_corps_de_metier');
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function getStatutLibelleAttribute()
    {
        $statuts = [
            'non_debute' => 'Non débuté',
            'en_cours' => 'En cours',
            'suspendu' => 'Suspendu',
            'receptionne' => 'Réceptionné',
            'termine' => 'Terminé'
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    public function getStatutColorAttribute()
    {
        $colors = [
            'non_debute' => '#6c757d',
            'en_cours' => '#ffc107',
            'suspendu' => '#dc3545',
            'receptionne' => '#17a2b8',
            'termine' => '#28a745'
        ];

        return $colors[$this->statut] ?? '#6c757d';
    }
}
