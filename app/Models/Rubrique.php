<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubrique extends Model
{
    use HasFactory;
    
    protected $table = 'rubriques';
    
    protected $fillable = ['nom', 'id_soussession', 'type'];
    
    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorieRubrique::class, 'id_soussession');
    }
    
    public function bpus()
    {
        return $this->hasMany(Bpu::class, 'id_rubrique');
    }
}
