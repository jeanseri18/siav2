<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_projet', 'date_creation', 'nom_projet', 'description', 'date_debut', 
        'date_fin', 'client', 'secteur_activite_id', 'conducteur_travaux_id', 
        'chef_projet_id', 'hastva', 'tva_achat', 'montant_global', 'chiffre_affaire_global', 
        'total_depenses', 'statut', 'bu_id', 'pays_id', 'ville_id', 
        'commune_id', 'quartier_id', 'secteur_id', 'created_by', 'updated_by'
    ];

    public function clientFournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'client', 'id');
    }

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
    
    public function conducteurTravaux()
    {
        return $this->belongsTo(User::class, 'conducteur_travaux_id');
    }
    
    public function chefProjet()
    {
        return $this->belongsTo(User::class, 'chef_projet_id');
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function ville()
    {
        return $this->belongsTo(Ville::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }

    public function secteurLocalisation()
    {
        return $this->belongsTo(Secteur::class, 'secteur_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
