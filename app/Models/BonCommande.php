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

    public function receptions()
    {
        return $this->hasMany(Reception::class, 'bon_commande_id');
    }

    public function getPourcentageReceptionAttribute()
    {
        $totalQuantiteCommandee = $this->lignes->sum('quantite');
        if ($totalQuantiteCommandee == 0) {
            return 0;
        }
        
        $totalQuantiteRecue = $this->lignes->sum('quantite_recue');
        return round(($totalQuantiteRecue / $totalQuantiteCommandee) * 100, 2);
    }

    public function getStatutReceptionAttribute()
    {
        $pourcentage = $this->pourcentage_reception;
        
        if ($pourcentage == 0) {
            return 'Non reçu';
        } elseif ($pourcentage < 100) {
            return 'Partiellement reçu';
        } else {
            return 'Complètement reçu';
        }
    }

    public function isCompletelyReceived()
    {
        return $this->pourcentage_reception >= 100;
    }

    public function isPartiallyReceived()
    {
        $pourcentage = $this->pourcentage_reception;
        return $pourcentage > 0 && $pourcentage < 100;
    }

    public function isNotReceived()
    {
        return $this->pourcentage_reception == 0;
    }
}