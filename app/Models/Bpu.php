<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bpu extends Model
{
    use HasFactory;
    
    protected $table = 'bpus';
    
    protected $fillable = [
        'designation', 'qte', 'materiaux', 'unite', 'main_oeuvre', 'materiel',
        'debourse_sec', 'frais_chantier', 'frais_general', 'marge_nette', 'pu_ht', 'pu_ttc', 'id_rubrique', 'contrat_id'
    ];
    
    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class, 'id_rubrique');
    }
    
    /**
     * Relation avec les lignes de DQE
     */
    public function dqeLignes()
    {
        return $this->hasMany(DQELigne::class, 'bpu_id');
    }
    
    /**
     * Relation avec le contrat
     */
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }
    
    /**
     * Scope pour les BPU utilitaires (globaux)
     */
    public function scopeUtilitaires($query)
    {
        return $query->whereNull('contrat_id');
    }
    
    /**
     * Scope pour les BPU d'un contrat spécifique
     */
    public function scopeContrat($query, $contratId)
    {
        return $query->where('contrat_id', $contratId);
    }
    
    /**
     * Calculer et mettre à jour les valeurs dérivées
     */
    public function updateDerivedValues()
    {
        // Déboursé sec = Matériaux + Main d'œuvre + Matériel
        $this->debourse_sec = $this->materiaux + $this->main_oeuvre + $this->materiel;
        
        // Frais de chantier = 30% du déboursé sec
        $this->frais_chantier = $this->debourse_sec * 0.3;
        
        // Frais généraux = 15% du (déboursé sec + frais de chantier)
        $this->frais_general = ($this->debourse_sec + $this->frais_chantier) * 0.15;
        
        // Marge nette = 15% du (déboursé sec + frais de chantier + frais généraux)
        $this->marge_nette = ($this->debourse_sec + $this->frais_chantier + $this->frais_general) * 0.15;
        
        // Prix unitaire HT = Déboursé sec + Frais de chantier + Frais généraux + Marge nette
        $this->pu_ht = $this->debourse_sec + $this->frais_chantier + $this->frais_general + $this->marge_nette;
        
        // Prix unitaire TTC = Prix unitaire HT * 1.18 (TVA 18%)
        $this->pu_ttc = $this->pu_ht * 1.18;
        
        $this->save();
    }
}