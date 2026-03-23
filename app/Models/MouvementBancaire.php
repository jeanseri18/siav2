<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementBancaire extends Model
{
    use HasFactory;

    protected $table = 'mouvements_bancaires';

    protected $fillable = [
        'bu_id',
        'banque_id',
        'type',
        'mode',
        'montant',
        'date_operation',
        'numero_piece',
        'cheque_barre',
        'beneficiaire',
        'libelle',
        'est_passe',
        'date_passage',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_operation' => 'date',
        'cheque_barre' => 'boolean',
        'est_passe' => 'boolean',
        'date_passage' => 'date',
    ];

    public function bu()
    {
        return $this->belongsTo(BU::class, 'bu_id');
    }

    public function banque()
    {
        return $this->belongsTo(Banque::class, 'banque_id');
    }
}
