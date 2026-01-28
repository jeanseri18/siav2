<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureContrat extends Model
{
    protected $fillable = [
        'dqe_id',
        'montant_a_payer',
        'montant_verse'
    ];

    protected $casts = [
        'montant_a_payer' => 'decimal:2',
        'montant_verse' => 'decimal:2',
    ];

    public function dqe()
    {
        return $this->belongsTo(DQE::class);
    }

    public function facturesDecompte()
    {
        return $this->hasMany(FactureDecompte::class);
    }

    public function calculerMontantVerse()
    {
        \Log::info('Calcul du montant versé', [
            'facture_contrat_id' => $this->id,
            'nombre_decomptes' => $this->facturesDecompte()->count(),
            'nombre_decomptes_valides' => $this->facturesDecompte()->where('statut', 'valide')->count(),
            'somme_montant_ht_valides' => $this->facturesDecompte()->where('statut', 'valide')->sum('montant_ht')
        ]);
        return $this->facturesDecompte()->where('statut', 'valide')->sum('montant_ht');
    }

    public function mettreAJourMontantVerse()
    {
        $ancienMontant = $this->montant_verse;
        $this->montant_verse = $this->calculerMontantVerse();
        $this->save();
        
        \Log::info('Mise à jour du montant versé', [
            'facture_contrat_id' => $this->id,
            'ancien_montant' => $ancienMontant,
            'nouveau_montant' => $this->montant_verse,
            'difference' => $this->montant_verse - $ancienMontant
        ]);
    }
}
