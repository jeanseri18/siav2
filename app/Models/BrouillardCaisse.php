<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrouillardCaisse extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',            // Type: entrée ou sortie
        'montant',         // Montant de l'entrée ou de la sortie
        'motif',           // Motif de l'entrée/sortie
        'solde_cumule',    // Solde cumulé après cette transaction
        'bus_id',          // Référence à la caisse
    ];

    // Définir la relation avec le modèle Bus
    public function bus()
    {
        return $this->belongsTo(Bu::class);
    }

    // Calculer le solde cumulé basé sur les entrées et sorties
    public static function updateSoldeCumule($busId)
    {
        $entries = self::where('bus_id', $busId)->get();
        $solde = 0;

        foreach ($entries as $entry) {
            if ($entry->type == 'entrée') {
                $solde += $entry->montant;
            } else {
                $solde -= $entry->montant;
            }
            $entry->update(['solde_cumule' => $solde]);
        }
    }
}
