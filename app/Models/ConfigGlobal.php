<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigGlobal extends Model {
    use HasFactory;

    protected $table = 'config_global';
    protected $fillable = ['logo', 'id_bu', 'nom_entreprise', 'localisation', 'adresse_postale', 'rccm', 'cc', 'tel1', 'tel2', 'email'];

    public function businessUnit() {
        return $this->belongsTo(BU::class, 'id_bu');
    }
}
