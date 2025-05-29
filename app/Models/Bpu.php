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
        'debourse_sec', 'frais_chantier', 'frais_general', 'marge_nette', 'pu_ht', 'pu_ttc', 'id_rubrique'
    ];
    
    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class, 'id_rubrique');
    }
}