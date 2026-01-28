<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebourseChantier extends Model
{
    protected $table = 'debourse_chantiers';

    protected $fillable = [
        'parent_id',
        'rubrique_id',
        'designation',
        'unite',
        'quantite',
        'pu_ht',
        'montant_ht',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'pu_ht' => 'decimal:2',
        'montant_ht' => 'decimal:2',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DebourseChantierParent::class, 'parent_id');
    }

    public function rubrique(): BelongsTo
    {
        return $this->belongsTo(Rubrique::class);
    }
}