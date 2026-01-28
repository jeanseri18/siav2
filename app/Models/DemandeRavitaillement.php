<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemandeRavitaillement extends Model
{
    protected $table = 'demandes_ravitaillement';
    
    protected $fillable = [
        'reference',
        'objet',
        'statut',
        'priorite',
        'date_demande',
        'date_livraison_souhaitee',
        'date_livraison_effective',
        'commentaires',
        'motif_rejet',
        'contrat_id',
        'demandeur_id',
        'approbateur_id'
    ];
    
    protected $casts = [
        'date_demande' => 'date',
        'date_livraison_souhaitee' => 'date',
        'date_livraison_effective' => 'date'
    ];
    
    // Relations
    public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }
    
    public function demandeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }
    
    public function approbateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approbateur_id');
    }
    

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneDemandeRavitaillement::class);
    }
    
    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
    
    public function scopeApprouvee($query)
    {
        return $query->where('statut', 'approuvee');
    }
    
    // Accessors
    public function getStatutLabelAttribute()
    {
        $statuts = [
            'en_attente' => 'En attente',
            'approuvee' => 'Approuvée',
            'rejetee' => 'Rejetée',
            'en_cours' => 'En cours',
            'livree' => 'Livrée'
        ];
        
        return $statuts[$this->statut] ?? $this->statut;
    }
    
    public function getPrioriteLabelAttribute()
    {
        $priorites = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];
        
        return $priorites[$this->priorite] ?? $this->priorite;
    }
}
