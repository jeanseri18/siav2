<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'quartier_id'];

    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }
}
