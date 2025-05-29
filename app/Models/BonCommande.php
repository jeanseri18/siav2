<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonCommande extends Model
{
    use HasFactory;

    protected $table = 'bon_commandes';

    protected $fillable = [
        'reference',
        'date_commande',
        'fournisseur_id',
        'demande_approvisionnement_id',
        'demande_achat_id',
        'user_id',
        'montant_total',
        'date_livraison_prevue',
        'statut',
        'conditions_paiement',
        'notes'
    ];

    protected $casts = [
        'date_commande' => 'date',
        'date_livraison_prevue' => 'date',
        'montant_total' => 'decimal:2'
    ];

    public function fournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'fournisseur_id');
    }

    public function demandeApprovisionnement()
    {
        return $this->belongsTo(DemandeApprovisionnement::class, 'demande_approvisionnement_id');
    }

    public function demandeAchat()
    {
        return $this->belongsTo(DemandeAchat::class, 'demande_achat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lignes()
    {
        return $this->hasMany(LigneBonCommande::class, 'bon_commande_id');
    }
}