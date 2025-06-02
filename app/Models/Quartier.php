<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quartier extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'commune_id', 'code'];

    /**
     * Obtenir la commune à laquelle appartient ce quartier
     */
    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    /**
     * Obtenir la ville à laquelle appartient ce quartier via la commune
     */
    public function ville()
    {
        return $this->commune->ville();
    }
}