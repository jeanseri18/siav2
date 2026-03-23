<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banque extends Model
{
    use HasFactory;

    protected $fillable = ['bu_id', 'solde_initial', 'nom', 'code_banque', 'code_guichet', 'numero_compte', 'cle_rib', 'iban', 'code_swift', 'domiciliation', 'telephone'];

    protected $casts = [
        'solde_initial' => 'decimal:2',
    ];

    public function bu()
    {
        return $this->belongsTo(BU::class, 'bu_id');
    }
}
