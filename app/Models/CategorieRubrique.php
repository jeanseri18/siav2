<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieRubrique extends Model
{
    use HasFactory;
    
    protected $table = 'categorierubriques';
    
    protected $fillable = ['nom', 'type'];
    
    public function sousCategories()
    {
        return $this->hasMany(SousCategorieRubrique::class, 'id_session');
    }
}