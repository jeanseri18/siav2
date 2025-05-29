<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecteurActivite extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    public function bus()
    {
        return $this->hasMany(BU::class);
    }

    public function projets()
    {
        return $this->hasMany(Projet::class);
    }
}
