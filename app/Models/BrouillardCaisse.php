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
        'date_operation', // Date comptable (optionnelle ; sinon DATE(created_at))
        'solde_cumule',    // Solde cumulé après cette transaction
        'bus_id',          // Référence à la caisse
    ];

    protected $casts = [
        'date_operation' => 'date',
    ];

    /** Expression SQL : date d'opération effective pour tri / filtres (MySQL). */
    public static function sqlDateEffective(): string
    {
        return 'COALESCE(date_operation, DATE(created_at))';
    }

    // Définir la relation avec le modèle Bus
    public function bus()
    {
        return $this->belongsTo(Bu::class);
    }

    /** Libellé date pour les tableaux (date d'opération ou saisie). */
    public function getDateAffichageAttribute(): string
    {
        if ($this->date_operation) {
            return $this->date_operation->format('d/m/Y');
        }

        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Entrée : libellés possibles « Entrée », « entrée », etc.
     */
    public static function estTypeEntree(?string $type): bool
    {
        if ($type === null || $type === '') {
            return false;
        }

        return (bool) preg_match('/^entr/i', trim($type));
    }

    /**
     * Recalcule tous les soldes cumulés dans l'ordre chronologique et aligne soldecaisse sur la BU.
     */
    public static function synchroniserSoldesPourBus(int $busId): float
    {
        $entries = self::where('bus_id', $busId)
            ->orderByRaw(self::sqlDateEffective().' asc')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $solde = 0.0;
        foreach ($entries as $entry) {
            if (self::estTypeEntree($entry->type)) {
                $solde += (float) $entry->montant;
            } else {
                $solde -= (float) $entry->montant;
            }
            $entry->forceFill(['solde_cumule' => $solde])->save();
        }

        BU::where('id', $busId)->update(['soldecaisse' => $solde]);

        return $solde;
    }

    // Calculer le solde cumulé basé sur les entrées et sorties
    public static function updateSoldeCumule($busId)
    {
        self::synchroniserSoldesPourBus((int) $busId);
    }
}
