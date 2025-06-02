<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'ville_id', 'code'];

    /**
     * Obtenir la ville à laquelle appartient cette commune
     */
    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    /**
     * Obtenir les quartiers de cette commune
     */
    public function quartiers(): HasMany
    {
        return $this->hasMany(Quartier::class);
    }
}