# UserSeeder - Documentation

## Description
Le `UserSeeder` crée des utilisateurs de test pour l'application SIA avec différents rôles et profils.

## Utilisateurs créés

### 1. Administrateur Système
- **Email:** admin@sia.com
- **Mot de passe:** password123
- **Rôle:** admin
- **Nom:** Admin Système
- **Permissions:** Toutes

### 2. Chef de Projet
- **Email:** jean.kouassi@sia.com
- **Mot de passe:** password123
- **Rôle:** chef_projet
- **Nom:** Jean Kouassi
- **Permissions:** projets, contrats, équipes

### 3. Conducteur de Travaux
- **Email:** marie.traore@sia.com
- **Mot de passe:** password123
- **Rôle:** conducteur_travaux
- **Nom:** Marie Traoré
- **Permissions:** chantiers, équipes, matériaux

### 4. Chef de Chantier
- **Email:** ibrahim.diabate@sia.com
- **Mot de passe:** password123
- **Rôle:** chef_chantier
- **Nom:** Ibrahim Diabaté
- **Permissions:** chantiers, ouvriers, matériaux

### 5. Comptable
- **Email:** fatou.bamba@sia.com
- **Mot de passe:** password123
- **Rôle:** comptable
- **Nom:** Fatou Bamba
- **Permissions:** finances, factures, paiements

### 6. Acheteur
- **Email:** seydou.ouattara@sia.com
- **Mot de passe:** password123
- **Rôle:** acheteur
- **Nom:** Seydou Ouattara
- **Permissions:** achats, fournisseurs, commandes

### 7. Contrôleur de Gestion
- **Email:** aminata.kone@sia.com
- **Mot de passe:** password123
- **Rôle:** controleur_gestion
- **Nom:** Aminata Koné
- **Permissions:** contrôle, budgets, rapports

### 8. Secrétaire
- **Email:** akissi.yao@sia.com
- **Mot de passe:** password123
- **Rôle:** secretaire
- **Nom:** Akissi Yao
- **Permissions:** documents, courrier, agenda

### 9. Employé
- **Email:** moussa.doumbia@sia.com
- **Mot de passe:** password123
- **Rôle:** employe
- **Nom:** Moussa Doumbia
- **Permissions:** consultation

### 10. Utilisateur Inactif (pour tests)
- **Email:** adama.sanogo@sia.com
- **Mot de passe:** password123
- **Rôle:** employe
- **Statut:** inactif
- **Nom:** Adama Sanogo
- **Permissions:** aucune

## Utilisation

### Exécuter le seeder individuellement
```bash
php artisan db:seed --class=UserSeeder
```

### Exécuter tous les seeders (inclut UserSeeder)
```bash
php artisan db:seed
```

### Réinitialiser et exécuter les seeders
```bash
php artisan migrate:fresh --seed
```

## Informations importantes

- **Mot de passe par défaut:** `password123` pour tous les utilisateurs
- **Statut par défaut:** `actif` (sauf pour l'utilisateur de test inactif)
- **Données personnelles:** Informations fictives mais réalistes pour la Côte d'Ivoire
- **Permissions:** Chaque rôle a des permissions spécifiques selon ses responsabilités

## Rôles disponibles

Selon la migration `add_employee_fields_to_users_table`, les rôles disponibles sont :
- admin
- dg
- chef_projet
- conducteur_travaux
- chef_chantier
- comptable
- magasinier
- acheteur
- controleur_gestion
- secretaire
- chauffeur
- gardien
- employe

## Sécurité

⚠️ **ATTENTION:** Ces utilisateurs sont destinés uniquement au développement et aux tests. 
Ne jamais utiliser ces comptes en production avec les mots de passe par défaut.

## Personnalisation

Pour modifier les utilisateurs créés, éditez le fichier `database/seeders/UserSeeder.php` et relancez le seeder.