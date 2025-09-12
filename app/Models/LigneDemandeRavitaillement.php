<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneDemandeRavitaillement extends Model
{
    protected $table = 'lignes_demande_ravitaillement';
    
    protected $fillable = [
        'quantite_demandee',
        'quantite_approuvee',
        'quantite_livree',
        'prix_unitaire_estime',
        'prix_unitaire_reel',
        'montant_estime',
        'montant_reel',
        'description',
        'commentaires',
        'demande_ravitaillement_id',
        'article_id',
        'unite_mesure_id'
    ];
    
    protected $casts = [
        'quantite_demandee' => 'decimal:3',
        'quantite_approuvee' => 'decimal:3',
        'quantite_livree' => 'decimal:3',
        'prix_unitaire_estime' => 'decimal:2',
        'prix_unitaire_reel' => 'decimal:2',
        'montant_estime' => 'decimal:2',
        'montant_reel' => 'decimal:2'
    ];
    
    // Relations
    public function demandeRavitaillement(): BelongsTo
    {
        return $this->belongsTo(DemandeRavitaillement::class);
    }
    
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
    
    public function uniteMesure(): BelongsTo
    {
        return $this->belongsTo(UniteMesure::class, 'unite_mesure_id');
    }
    
    // Accessors
    public function getMontantEstimeCalculeAttribute()
    {
        return $this->quantite_demandee * $this->prix_unitaire_estime;
    }
    
    public function getMontantReelCalculeAttribute()
    {
        return $this->quantite_livree * $this->prix_unitaire_reel;
    }
    
    public function getQuantiteRestanteAttribute()
    {
        return $this->quantite_approuvee - $this->quantite_livree;
    }
}
