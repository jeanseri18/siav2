<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraisChantierParent extends Model
{
    use HasFactory;

    protected $table = 'frais_chantier_parents';

    protected $fillable = [
        'contrat_id',
        'dqe_id',
        'type',
        'ref',
        'montant_total',
        'statut'
    ];

    protected $casts = [
        'montant_total' => 'decimal:2'
    ];

    const STATUT_BROUILLON = 'brouillon';
    const STATUT_VALIDE = 'valide';
    const STATUT_REFUSE = 'refuse';

    const TYPE_PREVISIONNEL = 'previsionnelle';

    protected $attributes = [
        'type' => self::TYPE_PREVISIONNEL,
        'statut' => self::STATUT_BROUILLON
    ];

    public function fraisChantiers()
    {
        return $this->hasMany(FraisChantier::class, 'parent_id');
    }

    public function dqe()
    {
        return $this->belongsTo(DQE::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function updateTotal()
    {
        $this->montant_total = $this->fraisChantiers()->sum('montant_ht');
        $this->save();
    }
}