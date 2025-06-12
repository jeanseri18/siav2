<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovisionnementCaisse extends Model
{
    use HasFactory;
    
    protected $table = 'approvisionnement_caisses';
    
    protected $fillable = [
        'bus_id',           // ID du BU concerné
        'montant',          // Montant de l'approvisionnement
        'motif',            // Motif de l'approvisionnement
        'mode_paiement',    // Mode de paiement (chèque, espèce)
        'date_appro',       // Date de l'approvisionnement
        'banque_id',        // ID de la banque (si paiement par chèque)
        'reference_cheque',  // Référence du chèque (si paiement par chèque)
        'origine_fonds',    // Origine des fonds (si paiement en espèce)
    ];
    
    // Relation avec le BU
    public function bu()
    {
        return $this->belongsTo(BU::class, 'bus_id');
    }
    
    // Relation avec la banque
    public function banque()
    {
        return $this->belongsTo(Banque::class);
    }
}