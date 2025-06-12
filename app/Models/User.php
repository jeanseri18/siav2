<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;


    protected $fillable = [
        'nom', 'prenom', 'email', 'role', 'permissions', 'password', 'status',
        'poste', 'telephone', 'adresse', 'date_embauche', 'salaire', 'numero_cnss',
        'date_naissance', 'sexe', 'lieu_naissance', 'nationalite', 'situation_matrimoniale',
        'numero_cni', 'numero_passeport', 'photo'
    ];

    protected $casts = [
        'permissions' => 'array',
        'date_embauche' => 'date',
        'date_naissance' => 'date',
    ];

 


        public function bus()
        {
            return $this->belongsToMany(BU::class, 'bu_associats', 'user_id', 'bu_id');
        }
  
    
    public function contrats()
    {
        return $this->hasMany(Contrat::class, 'client_id');
    }
    
    // Relations pour les projets
    public function projetsCommeChefProjet()
    {
        return $this->hasMany(Projet::class, 'chef_projet_id');
    }
    
    public function projetsCommeConducteurTravaux()
    {
        return $this->hasMany(Projet::class, 'conducteur_travaux_id');
    }
    
    // Méthodes utilitaires
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }
    
    public function getAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }
    
    public function getAncienneteAttribute()
    {
        return $this->date_embauche ? $this->date_embauche->diffInYears(now()) : null;
    }
    
    // Scopes pour filtrer par rôle
    public function scopeConducteursTravaux($query)
    {
        return $query->where('role', 'conducteur_travaux');
    }
    
    public function scopeChefsProjets($query)
    {
        return $query->where('role', 'chef_projet');
    }
    
    public function scopeChefsChantier($query)
    {
        return $query->where('role', 'chef_chantier');
    }
    
    public function scopeComptables($query)
    {
        return $query->where('role', 'comptable');
    }
    
    public function scopeActifs($query)
    {
        return $query->where('status', 'actif');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
