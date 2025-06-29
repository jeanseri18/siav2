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


    // Dans app/Models/Contrat.php, ajoutez cette méthode

/**
 * Relation avec les frais généraux
 */
public function fraisGeneraux()
{
    return $this->hasMany(FraisGeneral::class, 'contrat_id');
}
    /**
     * Relation avec les DQE
     */
    public function dqes()
    {
        return $this->hasMany(DQE::class, 'contrat_id');
    }
    
    /**
     * Relation avec les déboursés
     */
    public function debourses()
    {
        return $this->hasMany(Debourse::class, 'contrat_id');
    }
    
    /**
     * Relation avec les prestations
     */
    public function prestations()
    {
        return $this->hasMany(Prestation::class, 'id_contrat');
    }
    
    /**
     * Relation avec les factures
     */
    public function factures()
    {
        return $this->hasMany(Facture::class, 'id_contrat');
    }
    
    /**
     * Obtenir le dernier DQE validé
     */
    public function getLastValidatedDQE()
    {
        return $this->dqes()
            ->where('statut', 'validé')
            ->orderBy('updated_at', 'desc')
            ->first();
    }
    
    /**
     * Mettre à jour le montant du contrat basé sur le dernier DQE validé
     */
    public function updateMontantFromDQE()
    {
        $lastDqe = $this->getLastValidatedDQE();
        
        if ($lastDqe && $lastDqe->montant_total_ttc > 0) {
            $this->update([
                'montant' => $lastDqe->montant_total_ttc
            ]);
            
            return true;
        }
        
        return false;
    }
}