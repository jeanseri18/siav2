<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeDepense extends Model
{
    use HasFactory;
    protected $table = 'demandes_de_depenses';

    protected $fillable = [
        'montant',         // Montant de la demande
        'motif',           // Motif de la demande
        'statut',          // Statut de la demande (ex: en attente, validée)
        'bus_id',          // Référence à la caisse
    ];

    // Définir la relation avec le modèle Bus
    public function bus()
    {
        return $this->belongsTo(BU::class);
    }

    // Méthode pour valider la demande de dépense
    public function valider()
    {
        $this->statut = 'validée';
        $this->save();

        // Lorsque la demande est validée, une sortie est créée dans le Brouillard de Caisse
        BrouillardCaisse::create([
            'type' => 'sortie',
            'montant' => $this->montant,
            'motif' => $this->motif,
            'solde_cumule' => 0, // À ajuster selon ton modèle de calcul
            'bus_id' => $this->bus_id
        ]);

        // Mettre à jour le solde cumulé
        BrouillardCaisse::updateSoldeCumule($this->bus_id);
    }
}
