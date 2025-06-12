<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    use HasFactory;

    protected $table = 'contact_persons';

    protected $fillable = [
        'client_fournisseur_id',
        'civilite',
        'nom',
        'prenoms',
        'fonction',
        'telephone_1',
        'telephone_2',
        'email',
        'adresse',
        'statut',
        'contact_principal'
    ];

    protected $casts = [
        'contact_principal' => 'boolean',
    ];

    /**
     * Relation avec ClientFournisseur
     */
    public function clientFournisseur()
    {
        return $this->belongsTo(ClientFournisseur::class, 'client_fournisseur_id');
    }

    /**
     * Scope pour les contacts actifs
     */
    public function scopeActif($query)
    {
        return $query->where('statut', 'Actif');
    }

    /**
     * Scope pour les contacts principaux
     */
    public function scopePrincipal($query)
    {
        return $query->where('contact_principal', true);
    }

    /**
     * Obtenir le nom complet
     */
    public function getNomCompletAttribute()
    {
        return $this->civilite . ' ' . $this->prenoms . ' ' . $this->nom;
    }
}