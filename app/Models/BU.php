<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BU extends Model
{
    use HasFactory;
    protected $table = 'bus';

    protected $fillable = [
        'nom', 'secteur_activite_id', 'nombre_utilisateurs', 'adresse', 'logo', 
        'numero_rccm', 'numero_cc','soldecaisse', 'statut'
    ];

    public function secteur()
    {
        return $this->belongsTo(SecteurActivite::class, 'secteur_activite_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'bu_associats');
    }

    public function projets()
    {
        return $this->hasMany(Projet::class);
    }
    public function configGlobal()
{
    return $this->hasOne(ConfigGlobal::class, 'id_bu');
}
  // Relation avec les entrées et sorties de la caisse
  public function brouillardCaisse()
  {
      return $this->hasMany(BrouillardCaisse::class);
  }

  // Relation avec les demandes de dépenses
  public function demandesDepenses()
  {
      return $this->hasMany(DemandeDepense::class);
  }
}

