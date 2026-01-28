<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisChantier extends Model
{
    use HasFactory;

    protected $table = 'frais_chantiers';

    protected $fillable = [
        'parent_id',
        'contrat_id',
        'dqe_id',
        'id_rubrique',
        'designation',
        'unite',
        'quantite',
        'pu_ht',
        'montant_ht'
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'pu_ht' => 'decimal:2',
        'montant_ht' => 'decimal:2'
    ];

    public function dqe()
    {
        return $this->belongsTo(DQE::class);
    }

    public function rubrique()
    {
        return $this->belongsTo(Rubrique::class, 'id_rubrique');
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function parent()
    {
        return $this->belongsTo(FraisChantierParent::class, 'parent_id');
    }
}