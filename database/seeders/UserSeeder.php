<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BU;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur administrateur
        $admin = User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'actif',
            'poste' => 'Administrateur Système',
            'telephone' => '+225 01 02 03 04 05',
            'adresse' => 'Abidjan, Côte d\'Ivoire',
            'date_embauche' => Carbon::parse('2024-01-01'),
            'salaire' => 1500000,
            'numero_cnss' => 'CNSS001',
            'date_naissance' => Carbon::parse('1985-05-15'),
            'sexe' => 'M',
            'lieu_naissance' => 'Abidjan',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'marié(e)',
            'numero_cni' => 'CNI001234567',
            'permissions' => ['all']
        ]);

        // Créer un chef de projet
        $chefProjet = User::create([
            'nom' => 'Kouassi',
            'prenom' => 'Jean',
            'email' => 'jean.kouassi@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'chef_projet',
            'status' => 'actif',
            'poste' => 'Chef de Projet',
            'telephone' => '+225 07 08 09 10 11',
            'adresse' => 'Cocody, Abidjan',
            'date_embauche' => Carbon::parse('2024-02-01'),
            'salaire' => 1200000,
            'numero_cnss' => 'CNSS002',
            'date_naissance' => Carbon::parse('1980-03-20'),
            'sexe' => 'M',
            'lieu_naissance' => 'Bouaké',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'marié(e)',
            'numero_cni' => 'CNI002345678',
            'permissions' => ['projets', 'contrats', 'equipes']
        ]);

        // Créer un conducteur de travaux
        $conducteurTravaux = User::create([
            'nom' => 'Traoré',
            'prenom' => 'Marie',
            'email' => 'marie.traore@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'conducteur_travaux',
            'status' => 'actif',
            'poste' => 'Conducteur de Travaux',
            'telephone' => '+225 05 06 07 08 09',
            'adresse' => 'Yopougon, Abidjan',
            'date_embauche' => Carbon::parse('2024-03-01'),
            'salaire' => 1000000,
            'numero_cnss' => 'CNSS003',
            'date_naissance' => Carbon::parse('1988-07-10'),
            'sexe' => 'F',
            'lieu_naissance' => 'Yamoussoukro',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'célibataire',
            'numero_cni' => 'CNI003456789',
            'permissions' => ['chantiers', 'equipes', 'materiaux']
        ]);

        // Créer un chef de chantier
        $chefChantier = User::create([
            'nom' => 'Diabaté',
            'prenom' => 'Ibrahim',
            'email' => 'ibrahim.diabate@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'chef_chantier',
            'status' => 'actif',
            'poste' => 'Chef de Chantier',
            'telephone' => '+225 01 23 45 67 89',
            'adresse' => 'Adjamé, Abidjan',
            'date_embauche' => Carbon::parse('2024-04-01'),
            'salaire' => 800000,
            'numero_cnss' => 'CNSS004',
            'date_naissance' => Carbon::parse('1990-11-25'),
            'sexe' => 'M',
            'lieu_naissance' => 'Korhogo',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'marié(e)',
            'numero_cni' => 'CNI004567890',
            'permissions' => ['chantiers', 'ouvriers', 'materiaux']
        ]);

        // Créer un comptable
        $comptable = User::create([
            'nom' => 'Bamba',
            'prenom' => 'Fatou',
            'email' => 'fatou.bamba@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'comptable',
            'status' => 'actif',
            'poste' => 'Comptable',
            'telephone' => '+225 09 87 65 43 21',
            'adresse' => 'Plateau, Abidjan',
            'date_embauche' => Carbon::parse('2024-05-01'),
            'salaire' => 900000,
            'numero_cnss' => 'CNSS005',
            'date_naissance' => Carbon::parse('1987-09-12'),
            'sexe' => 'F',
            'lieu_naissance' => 'Abidjan',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'célibataire',
            'numero_cni' => 'CNI005678901',
            'permissions' => ['finances', 'factures', 'paiements']
        ]);

        // Créer un acheteur
        $acheteur = User::create([
            'nom' => 'Ouattara',
            'prenom' => 'Seydou',
            'email' => 'seydou.ouattara@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'acheteur',
            'status' => 'actif',
            'poste' => 'Acheteur',
            'telephone' => '+225 02 34 56 78 90',
            'adresse' => 'Marcory, Abidjan',
            'date_embauche' => Carbon::parse('2024-06-01'),
            'salaire' => 700000,
            'numero_cnss' => 'CNSS006',
            'date_naissance' => Carbon::parse('1992-01-30'),
            'sexe' => 'M',
            'lieu_naissance' => 'Daloa',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'célibataire',
            'numero_cni' => 'CNI006789012',
            'permissions' => ['achats', 'fournisseurs', 'commandes']
        ]);

        // Créer un contrôleur de gestion
        $controleurGestion = User::create([
            'nom' => 'Koné',
            'prenom' => 'Aminata',
            'email' => 'aminata.kone@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'controleur_gestion',
            'status' => 'actif',
            'poste' => 'Contrôleur de Gestion',
            'telephone' => '+225 03 45 67 89 01',
            'adresse' => 'Treichville, Abidjan',
            'date_embauche' => Carbon::parse('2024-07-01'),
            'salaire' => 1100000,
            'numero_cnss' => 'CNSS007',
            'date_naissance' => Carbon::parse('1986-04-18'),
            'sexe' => 'F',
            'lieu_naissance' => 'Gagnoa',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'marié(e)',
            'numero_cni' => 'CNI007890123',
            'permissions' => ['controle', 'budgets', 'rapports']
        ]);

        // Créer une secrétaire
        $secretaire = User::create([
            'nom' => 'Yao',
            'prenom' => 'Akissi',
            'email' => 'akissi.yao@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'secretaire',
            'status' => 'actif',
            'poste' => 'Secrétaire',
            'telephone' => '+225 04 56 78 90 12',
            'adresse' => 'Port-Bouët, Abidjan',
            'date_embauche' => Carbon::parse('2024-08-01'),
            'salaire' => 500000,
            'numero_cnss' => 'CNSS008',
            'date_naissance' => Carbon::parse('1995-12-05'),
            'sexe' => 'F',
            'lieu_naissance' => 'Abengourou',
            'nationalite' => 'Ivoirienne',
            'situation_matrimoniale' => 'célibataire',
            'numero_cni' => 'CNI008901234',
            'permissions' => ['documents', 'courrier', 'agenda']
        ]);

        // Créer un utilisateur simple
        $utilisateur = User::create([
            'nom' => 'Doumbia',
            'prenom' => 'Moussa',
            'email' => 'moussa.doumbia@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'employe',
            'status' => 'actif',
            'poste' => 'Assistant',
            'telephone' => '+225 06 78 90 12 34',
            'adresse' => 'Abobo, Abidjan',
            'date_embauche' => Carbon::parse('2024-09-01'),
            'salaire' => 400000,
            'numero_cnss' => 'CNSS009',
            'date_naissance' => Carbon::parse('1998-08-22'),
            'sexe' => 'M',
            'lieu_naissance' => 'Sikasso',
            'nationalite' => 'Malienne',
            'situation_matrimoniale' => 'célibataire',
            'numero_cni' => 'CNI009012345',
            'permissions' => ['consultation']
        ]);

        // Créer un utilisateur inactif pour test
        $utilisateurInactif = User::create([
            'nom' => 'Sanogo',
            'prenom' => 'Adama',
            'email' => 'adama.sanogo@sia.com',
            'password' => Hash::make('password123'),
            'role' => 'employe',
            'status' => 'inactif',
            'poste' => 'Ex-employé',
            'telephone' => '+225 08 90 12 34 56',
            'adresse' => 'Anyama, Abidjan',
            'date_embauche' => Carbon::parse('2023-01-01'),
            'salaire' => 0,
            'numero_cnss' => 'CNSS010',
            'date_naissance' => Carbon::parse('1985-06-14'),
            'sexe' => 'M',
            'lieu_naissance' => 'Ségou',
            'nationalite' => 'Malienne',
            'situation_matrimoniale' => 'marié(e)',
            'numero_cni' => 'CNI010123456',
            'permissions' => []
        ]);

        echo "UserSeeder: " . User::count() . " utilisateurs créés avec succès.\n";
    }
}