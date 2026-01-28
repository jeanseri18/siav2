<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieRubrique extends Model
{
    use HasFactory;
    
    protected $table = 'categorierubriques';
    
    protected $fillable = ['nom', 'type', 'contrat_id', 'id_qe'];
    
    // Forcer le nom de la relation en camelCase pour la sérialisation JSON
    protected $with = [];
    

    
    public function sousCategories()
    {
        return $this->hasMany(SousCategorieRubrique::class, 'id_session');
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