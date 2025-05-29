<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigGlobal extends Model {
    use HasFactory;

    protected $table = 'config_global';
    protected $fillable = ['entete', 'numdepatfacture', 'pieddepage', 'logo', 'id_bu'];

    public function businessUnit() {
        return $this->belongsTo(BU::class, 'id_bu');
    }
}
