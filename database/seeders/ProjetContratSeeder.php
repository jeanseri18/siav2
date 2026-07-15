<?php

namespace Database\Seeders;

use App\Models\BU;
use App\Models\ClientFournisseur;
use App\Models\Contrat;
use App\Models\Projet;
use App\Models\SecteurActivite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjetContratSeeder extends Seeder
{
    /**
     * Données de démonstration : secteur d’activité, BU, client, projets et contrats.
     * Dépend de UserSeeder (emails connus pour les rôles métier).
     */
    public function run(): void
    {
        $secteur = SecteurActivite::firstOrCreate(
            ['nom' => 'BTP et infrastructures'],
            []
        );

        $bu = BU::firstOrCreate(
            ['nom' => 'BU Seed SIA Démo'],
            [
                'secteur_activite_id' => $secteur->id,
                'nombre_utilisateurs' => 0,
                'adresse' => 'Plateau, Abidjan, Côte d\'Ivoire',
                'numero_rccm' => 'RCCM-CI-ABJ-SEED-001',
                'numero_cc' => 'CC-ABJ-SEED-001',
                'statut' => 'actif',
            ]
        );

        $client = ClientFournisseur::firstOrCreate(
            ['code' => 'CLI-SEED-001'],
            [
                'categorie' => 'Entreprise',
                'nom_raison_sociale' => 'Client démo SIA (seed)',
                'type' => 'Client',
                'statut' => 'Actif',
                'id_bu' => $bu->id,
                'email' => 'contact@client-seed-sia.ci',
                'telephone' => '+225 01 00 00 00 01',
            ]
        );

        $chefProjet = User::where('email', 'jean.kouassi@sia.com')->first();
        $conducteur = User::where('email', 'marie.traore@sia.com')->first();
        $chefChantier = User::where('email', 'ibrahim.diabate@sia.com')->first();
        $admin = User::where('email', 'admin@sia.com')->first();

        if (! $chefProjet || ! $conducteur) {
            $this->command?->warn('ProjetContratSeeder : utilisateurs chef_projet / conducteur_travaux introuvables. Lancez d’abord UserSeeder.');
        }

        DB::transaction(function () use ($bu, $secteur, $client, $chefProjet, $conducteur, $chefChantier, $admin) {
            $projetA = Projet::firstOrCreate(
                ['ref_projet' => 'PROJ-SEED-2026-001'],
                [
                    'date_creation' => Carbon::parse('2026-01-10'),
                    'nom_projet' => 'Immeuble résidentiel Cocody (démo seed)',
                    'description' => 'Projet de démonstration (ProjetContratSeeder).',
                    'date_debut' => Carbon::parse('2026-02-01'),
                    'date_fin' => Carbon::parse('2026-12-31'),
                    'client' => (string) $client->id,
                    'secteur_activite_id' => $secteur->id,
                    'conducteur_travaux_id' => $conducteur?->id,
                    'chef_projet_id' => $chefProjet?->id,
                    'hastva' => true,
                    'tva_achat' => true,
                    'montant_global' => 500_000_000,
                    'chiffre_affaire_global' => 0,
                    'total_depenses' => 0,
                    'statut' => 'en cours',
                    'bu_id' => $bu->id,
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ]
            );

            Contrat::firstOrCreate(
                ['ref_contrat' => 'CTR-SEED-2026-001'],
                [
                    'nom_contrat' => 'Lot gros œuvre — Cocody (seed)',
                    'id_projet' => $projetA->id,
                    'nom_projet' => $projetA->nom_projet,
                    'date_debut' => Carbon::parse('2026-02-01'),
                    'date_fin' => Carbon::parse('2026-08-30'),
                    'type_travaux' => 'Gros œuvre',
                    'taux_garantie' => 10,
                    'client_id' => $client->id,
                    'chef_chantier_id' => $chefChantier?->id,
                    'montant' => 180_000_000,
                    'statut' => 'en cours',
                    'decompte' => false,
                    'tva_18' => true,
                    'retenue_decennale' => 5,
                    'avance_demarrage' => 10_000_000,
                ]
            );

            Contrat::firstOrCreate(
                ['ref_contrat' => 'CTR-SEED-2026-002'],
                [
                    'nom_contrat' => 'Lot second œuvre — Cocody (seed)',
                    'id_projet' => $projetA->id,
                    'nom_projet' => $projetA->nom_projet,
                    'date_debut' => Carbon::parse('2026-06-01'),
                    'date_fin' => Carbon::parse('2026-11-15'),
                    'type_travaux' => 'Second œuvre / finitions',
                    'taux_garantie' => 10,
                    'client_id' => $client->id,
                    'chef_chantier_id' => $chefChantier?->id,
                    'montant' => 95_000_000,
                    'statut' => 'en cours',
                    'decompte' => false,
                    'tva_18' => true,
                    'retenue_decennale' => 5,
                    'avance_demarrage' => 0,
                ]
            );

            $projetB = Projet::firstOrCreate(
                ['ref_projet' => 'PROJ-SEED-2026-002'],
                [
                    'date_creation' => Carbon::parse('2026-03-05'),
                    'nom_projet' => 'Extension route secondaire (démo seed)',
                    'description' => 'Second projet de démonstration.',
                    'date_debut' => Carbon::parse('2026-04-01'),
                    'date_fin' => Carbon::parse('2027-03-31'),
                    'client' => (string) $client->id,
                    'secteur_activite_id' => $secteur->id,
                    'conducteur_travaux_id' => $conducteur?->id,
                    'chef_projet_id' => $chefProjet?->id,
                    'hastva' => true,
                    'tva_achat' => false,
                    'montant_global' => 120_000_000,
                    'chiffre_affaire_global' => 0,
                    'total_depenses' => 0,
                    'statut' => 'en cours',
                    'bu_id' => $bu->id,
                    'created_by' => $admin?->id,
                    'updated_by' => $admin?->id,
                ]
            );

            Contrat::firstOrCreate(
                ['ref_contrat' => 'CTR-SEED-2026-003'],
                [
                    'nom_contrat' => 'Marché route — zone industrielle (seed)',
                    'id_projet' => $projetB->id,
                    'nom_projet' => $projetB->nom_projet,
                    'date_debut' => Carbon::parse('2026-04-15'),
                    'date_fin' => Carbon::parse('2027-01-20'),
                    'type_travaux' => 'Voirie et réseaux',
                    'taux_garantie' => 10,
                    'client_id' => $client->id,
                    'chef_chantier_id' => $chefChantier?->id,
                    'montant' => 120_000_000,
                    'statut' => 'en cours',
                    'decompte' => false,
                    'tva_18' => true,
                    'retenue_decennale' => 5,
                    'avance_demarrage' => 0,
                ]
            );
        });

        $this->command?->info('ProjetContratSeeder : '.Projet::where('ref_projet', 'like', 'PROJ-SEED-%')->count().' projet(s), '.Contrat::where('ref_contrat', 'like', 'CTR-SEED-%')->count().' contrat(s) (références seed).');
    }
}
