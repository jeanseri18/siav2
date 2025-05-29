<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ville extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'pays_id','coef_eloignement'];

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    public function secteurs()
    {
        return $this->hasMany(Secteur::class);
    }
}
