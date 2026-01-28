<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SousCategorieRubrique extends Model
{
    use HasFactory;
    
    protected $table = 'souscategorierubriques';
    
    protected $fillable = ['nom', 'type', 'id_session', 'contrat_id', 'id_qe'];
    
    public function categorie()
    {
        return $this->belongsTo(CategorieRubrique::class, 'id_session');
    }
    
    public function rubriques()
    {
        return $this->hasMany(Rubrique::class, 'id_soussession');
    }
    
    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }
    
    public function dqe()
    {
        return $this->belongsTo(DQE::class, 'id_qe');
    }
}