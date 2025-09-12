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
        'user_id',         // Utilisateur qui a créé la demande
        'responsable_hierarchique_id', // Responsable hiérarchique choisi
        'raf_id',          // RAF assigné
        'statut_responsable', // Statut d'approbation du responsable
        'statut_raf',      // Statut d'approbation du RAF
        'date_approbation_responsable',
        'date_approbation_raf',
        'commentaire_responsable',
        'commentaire_raf'
    ];

    // Définir la relation avec le modèle Bus
    public function bus()
    {
        return $this->belongsTo(BU::class, 'bus_id');
    }

    // Alias pour la relation BU (pour compatibilité avec les vues)
    public function bu()
    {
        return $this->belongsTo(BU::class, 'bus_id');
    }

    // Relation avec l'utilisateur qui a créé la demande
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le responsable hiérarchique
    public function responsableHierarchique()
    {
        return $this->belongsTo(User::class, 'responsable_hierarchique_id');
    }

    // Relation avec le RAF
    public function raf()
    {
        return $this->belongsTo(User::class, 'raf_id');
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
