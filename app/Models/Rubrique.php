<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubrique extends Model
{
    use HasFactory;
    
    protected $table = 'rubriques';
    
    protected $fillable = ['nom', 'id_soussession', 'type', 'contrat_id', 'id_qe'];
    
    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorieRubrique::class, 'id_soussession');
    }
    
    public function bpus()
    {
        return $this->hasMany(Bpu::class, 'id_rubrique');
    }
    
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }
    
    public function dqe()
    {
        return $this->belongsTo(DQE::class, 'id_qe');
    }
    
    /**
     * Relation avec les lignes DQE
     */
    public function dqeLignes()
    {
        return $this->hasMany(DQELigne::class, 'id_rubrique');
    }
    

}
