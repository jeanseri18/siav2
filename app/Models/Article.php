<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'reference_fournisseur',
        'nom',
        'type',
        'quantite_stock',
        'prix_unitaire',
        'unite_mesure',
        'cout_moyen_pondere',
        'categorie_id',
        'sous_categorie_id',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorie::class);
    }

    public function fournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'reference_fournisseur');
    }

    public function uniteMesure()
    {
        return $this->belongsTo(UniteMesure::class, 'unite_mesure');
    }
    
    // Relation pour les lignes de demande de ravitaillement
    public function lignesDemandeRavitaillement()
    {
        return $this->hasMany(LigneDemandeRavitaillement::class);
    }

    // Relation pour les lignes de réception
    public function lignesReception()
    {
        return $this->hasMany(LigneReception::class);
    }

    // Récupère la date du dernier approvisionnement
    public function getDateDernierApprovisionnementAttribute()
    {
        return $this->lignesReception()
            ->join('receptions', 'ligne_receptions.reception_id', '=', 'receptions.id')
            ->where('receptions.statut', 'complete')
            ->orderBy('receptions.date_reception', 'desc')
            ->value('receptions.date_reception');
    }

    // Récupère le prix du dernier achat
    public function getPrixDernierAchatAttribute()
    {
        return $this->lignesReception()
            ->join('receptions', 'ligne_receptions.reception_id', '=', 'receptions.id')
            ->where('receptions.statut', 'complete')
            ->where('ligne_receptions.quantite_recue', '>', 0)
            ->orderBy('receptions.date_reception', 'desc')
            ->value('ligne_receptions.prix_unitaire_recu');
    }
}