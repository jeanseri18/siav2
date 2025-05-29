<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;
    protected $table = 'contrats';
    protected $fillable = [
        'ref_contrat', 'nom_contrat','id_projet', 'nom_projet', 'date_debut', 'date_fin', 
        'type_travaux', 'taux_garantie', 'client_id', 'montant', 'statut', 'decompte'
    ];

    public function client()
    {
        return $this->belongsTo(ClientFournisseur::class, 'client_id');
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'nom_projet', 'nom_projet');
    }
}
