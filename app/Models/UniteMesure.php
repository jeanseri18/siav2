<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniteMesure extends Model
{
    use HasFactory;

    protected $fillable = ['nom','ref'];
    
    /**
     * Relation avec les articles
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'unite_mesure');
    }
    
    /**
     * Relation avec les lignes de demande de ravitaillement
     */
    public function lignesDemandeRavitaillement()
    {
        return $this->hasMany(LigneDemandeRavitaillement::class);
    }
}

