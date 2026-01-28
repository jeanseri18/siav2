<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bpu extends Model
{
    use HasFactory;
    
    protected $table = 'bpus';
    
    protected $fillable = [
        'code', 'designation', 'qte', 'materiaux', 'taux_mo', 'unite', 'main_oeuvre', 'taux_mat', 'materiel',
        'debourse_sec', 'taux_fc', 'frais_chantier', 'taux_fg', 'frais_general', 'taux_benefice', 'marge_nette', 'pu_ht', 'pu_ttc', 'id_rubrique', 'contrat_id'
    ];
    
    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class, 'id_rubrique');
    }
    
    /**
     * Relation avec les lignes de DQE via la rubrique
     */
    // public function dqeLignes()
    // {
    //     return $this->hasManyThrough(DQELigne::class, Rubrique::class, 'id', 'id_rubrique', 'id_rubrique', 'id');
    // }
    
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
     * Calculer et mettre à jour les valeurs dérivées selon les formules BPU
     */
    public function updateDerivedValues()
    {
        // MAIN D'ŒUVRE (MO) = % MO x MATERIAUX
        $this->main_oeuvre = ($this->taux_mo / 100) * $this->materiaux;
        
        // MATERIEL (MAT) = % MAT x MATERIAUX
        $this->materiel = ($this->taux_mat / 100) * $this->materiaux;
        
        // DEBOURSE SEC (DS) = MATERIAUX + MAIN D'ŒUVRE + MATERIEL
        $this->debourse_sec = $this->materiaux + $this->main_oeuvre + $this->materiel;
        
        // FRAIS CHANTIER (FC) = % FC x DS
        $this->frais_chantier = ($this->taux_fc / 100) * $this->debourse_sec;
        
        // FRAIS GENERAUX (FG) = % FG x DS
        $this->frais_general = ($this->taux_fg / 100) * $this->debourse_sec;
        
        // BENEFICE (B) = % B x DS
        $this->marge_nette = ($this->taux_benefice / 100) * $this->debourse_sec;
        
        // P.U HT = DS + FC + FG + B
        $this->pu_ht = $this->debourse_sec + $this->frais_chantier + $this->frais_general + $this->marge_nette;
        
        // Prix unitaire TTC = Prix unitaire HT * 1.18 (TVA 18%)
        $this->pu_ttc = $this->pu_ht * 1.18;
        
        $this->save();
    }
}