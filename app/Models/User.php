<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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

    /**
     * URL publique de la photo de profil (disque public).
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        $path = trim($this->photo);
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return asset('storage/'.$path);
    }
    
    public function getAncienneteAttribute()
    {
        return $this->date_embauche ? $this->date_embauche->diffInYears(now()) : null;
    }

    /**
     * Rôles du menu « Utilisateurs » (création / édition).
     * Doit rester aligné sur l'enum `users.role` en base.
     */
    public static function roleOptionsForUserManagement(): array
    {
        return [
            'admin' => 'Administrateur',
            'dg' => 'DG',
            //'chef_projet' => 'Chef de projet',
            //'conducteur_travaux' => 'Conducteur de travaux',
            //'chef_chantier' => 'Chef de chantier',
            'comptable' => 'Comptable',
            'magasinier' => 'Magasinier',
            'acheteur' => 'Acheteur',
            'controleur_gestion' => 'Contrôleur de gestion',
            'caissier' => 'Caissier',
            'controleur_caisse' => 'Contrôleur de caisse',
            'secretaire' => 'Secrétaire',
            'chauffeur' => 'Chauffeur',
            'gardien' => 'Gardien',
            'employe' => 'Employé',

            'raf'=> "RAF",
            'rt'=> "RT",
            'caisse'=> "Caisse",
            'charge_des_achats'=> "Charge des Achats",
            'gestionnaire_stock'=> "Gestionnaire Stock",
            'qse'=> "QSE",
            'gestion_projets'=> "Gestion Projets",
            'conducteur_des_travaux'=> "Conducteur des Travaux",
            'chef_chantier'=> "Chef Chantier",
        ];
    }


public function hasPermission($permission)
{
    $rolePermissions = config("permissions.{$this->role}", []);


    return in_array($permission, $rolePermissions);
}


    /**
     * Rôles pour les fiches « Employés » (sans administrateur).
     */
    public static function roleOptionsForEmployeForms(): array
    {
        $roles = self::roleOptionsForUserManagement();
        unset($roles['admin']);

        return $roles;
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
    
    // Relations pour les demandes de ravitaillement
    public function demandesRavitaillementDemandees()
    {
        return $this->hasMany(DemandeRavitaillement::class, 'demandeur_id');
    }
    
    public function demandesRavitaillementApprouvees()
    {
        return $this->hasMany(DemandeRavitaillement::class, 'approbateur_id');
    }
    
    // Accessor pour le nom complet
    public function getNameAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
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
 