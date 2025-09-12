<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    // Définir le nom de la table si ce n'est pas le pluriel de la classe
    protected $table = 'factures';

    // Les champs qui peuvent être remplis via une requête (mass-assignment)
    protected $fillable = [
        'num',
        'id_prestation',
        'id_contrat',
        'id_artisan',
        'date_emission',
        'num_decompte',
        'decompte',
        'taux_avancement',
        'montant_ht',
        'tva',
        'montant_total',
        'montant_ttc',
        'ca_realise',
        'montant_reglement',
        'statut'
    ];

    // Les relations
    public function prestation()
    {
        return $this->belongsTo(Prestation::class, 'id_prestation');
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class, 'id_contrat');
    }

    public function artisan()
    {
        return $this->belongsTo(Artisan::class, 'id_artisan');
    }

    // Accesseur pour `reste_a_regler` (calculé dynamiquement dans le modèle)
    public function getResteAReglerAttribute()
    {
        return $this->montant_total - $this->montant_reglement;
    }
}
