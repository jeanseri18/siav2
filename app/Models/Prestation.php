<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestation extends Model {
    use HasFactory;

    protected $table = 'prestation';
    protected $fillable = [
        'id_artisan', 
        'id_contrat', 
        'prestation_titre', 
        'detail', 
        'montant', 
        'taux_avancement', 
        'statut', 
        'corps_metier_id',
        'date_affectation',
        'motif_remplacement',
        'date_remplacement'
    ];

    public function artisan() {
        return $this->belongsTo(Artisan::class, 'id_artisan');
    }

    public function contrat() {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    /**
     * Relation avec le corps de métier
     */
    public function corpMetier() {
        return $this->belongsTo(CorpMetier::class, 'corps_metier_id');
    }

    /**
     * Relation avec les comptes de prestation
     */
    public function comptes()
    {
        return $this->hasMany(ComptePrestation::class);
    }
}
