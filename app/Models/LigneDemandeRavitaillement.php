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
        'commentaires',
        'demande_ravitaillement_id',
        'article_id',
        'unite_mesure_id'
    ];
    
    protected $casts = [
        'quantite_demandee' => 'decimal:3',
        'quantite_approuvee' => 'decimal:3',
        'quantite_livree' => 'decimal:3'
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
    

    public function getQuantiteRestanteAttribute()
    {
        return $this->quantite_approuvee - $this->quantite_livree;
    }
}
