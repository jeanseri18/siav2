<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\BonCommande;
use App\Models\BU;
use App\Models\Categorie;
use App\Models\CategorieRubrique;
use App\Models\ClientFournisseur;
use App\Models\ConfigGlobal;
use App\Models\Contrat;
use App\Models\CorpMetier;
use App\Models\Decompte;
use App\Models\LigneBonCommande;
use App\Models\LignePrestation;
use App\Models\LigneReception;
use App\Models\Prestation;
use App\Models\Projet;
use App\Models\Reception;
use App\Models\Rubrique;
use App\Models\SousCategorie;
use App\Models\SousCategorieRubrique;
use App\Models\UniteMesure;
use App\Models\User;
use App\Models\Vente;
use App\Models\Artisan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PdfDemoSeeder extends Seeder
{
    /**
     * Données de démonstration pour tester les PDF :
     * - Bon de commande, Bon de livraison, Fiche versement artisan,
     *   Attachement travaux, Facture proforma vente.
     *
     * Dépend de UserSeeder et ProjetContratSeeder.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@sia.com')->first();
        $bu = BU::where('nom', 'BU Seed SIA Démo')->first()
            ?? BU::query()->first();
        $projet = Projet::where('ref_projet', 'PROJ-SEED-2026-001')->first()
            ?? Projet::query()->first();
        $contrat = Contrat::where('ref_contrat', 'CTR-SEED-2026-001')->first()
            ?? ($projet ? Contrat::where('id_projet', $projet->id)->first() : null);

        if (! $admin || ! $bu || ! $projet || ! $contrat) {
            $this->command?->warn('PdfDemoSeeder : lancez d’abord ProjetContratSeeder (et UserSeeder pour admin@sia.com).');

            return;
        }

        $client = ClientFournisseur::find($projet->client)
            ?? ClientFournisseur::where('type', 'Client')->first();

        if (! $client) {
            $client = ClientFournisseur::create([
                'categorie' => 'Entreprise',
                'nom_raison_sociale' => 'CIE (seed PDF)',
                'type' => 'Client',
                'statut' => 'Actif',
                'id_bu' => $bu->id,
                'email' => 'contact@cie-seed-pdf.ci',
                'telephone' => '(+225) 27 21 23 33 00',
            ]);
            $projet->update(['client' => (string) $client->id]);
        }

        DB::transaction(function () use ($admin, $bu, $projet, $contrat, $client) {
            $this->seedConfigGlobal($bu);
            $this->enrichClient($client);
            $fournisseur = $this->seedFournisseur($bu);
            [$article1, $article2] = $this->seedArticles();
            $bonCommande = $this->seedBonCommande($admin, $fournisseur, $projet, $article1, $article2);
            $reception = $this->seedReception($admin, $bonCommande, $article1, $article2);
            [$prestation, $decompte1, $decompte2] = $this->seedPrestationDecomptes($contrat);
            $vente = $this->seedVente($client, $article1, $article2);

            $this->printTestLinks(
                $bonCommande,
                $reception,
                $prestation,
                $decompte1,
                $decompte2,
                $vente
            );
        });
    }

    private function seedConfigGlobal(BU $bu): void
    {
        ConfigGlobal::updateOrCreate(
            ['id_bu' => $bu->id],
            [
                'logo' => '',
                'nom_entreprise' => 'SIA-Sarl',
                'localisation' => 'Bingerville, cité colombe 1, Ilot 20, Lot 134',
                'adresse_postale' => '18 BP 682 Abidjan 18',
                'rccm' => 'CI-ABJ-2010-B-123456',
                'cc' => '1509099 H',
                'tel1' => '(+225) 27 22 26 07 79 / (+225) 01 41 60 45 11',
                'tel2' => '(+225) 01 40 18 02 02',
                'email' => 'infos@sia-sarl.com',
                'horaires_ouverture' => '8 :00 – 17 :00, tous les jours du lundi au vendredi',
            ]
        );
    }

    private function enrichClient(ClientFournisseur $client): void
    {
        $client->update([
            'nom_raison_sociale' => 'CIE (seed PDF)',
            'adresse_localisation' => '1, Avenue Christiani Treichville',
            'boite_postale' => '01 BP 6923 Abidjan 01',
            'telephone' => '(+225) 27 21 23 33 00',
            'n_rccm' => 'CI-ABJ-1990-B-149296',
            'n_cc' => '9004996 S',
            'mode_paiement' => 'Chèque',
            'delai_paiement' => '30',
        ]);
    }

    private function seedFournisseur(BU $bu): ClientFournisseur
    {
        $fournisseur = ClientFournisseur::firstOrCreate(
            [
                'email' => 'Service.clients1@universelleindustries.com',
                'type' => 'Fournisseur',
            ],
            [
                'categorie' => 'Entreprise',
                'nom_raison_sociale' => 'UNIVERSELLE INDUSTRIES (seed)',
                'statut' => 'Actif',
                'id_bu' => $bu->id,
                'adresse_localisation' => 'Zone industrielle de Yopougon',
                'boite_postale' => '01 BP 232 ABIDJAN 01',
                'telephone' => '(225) 27 23 52 18 43',
                'mode_paiement' => 'Chèque',
            ]
        );

        $fournisseur->update([
            'nom_raison_sociale' => 'UNIVERSELLE INDUSTRIES (seed)',
            'adresse_localisation' => 'Zone industrielle de Yopougon',
            'boite_postale' => '01 BP 232 ABIDJAN 01',
            'telephone' => '(225) 27 23 52 18 43',
            'mode_paiement' => 'Chèque',
        ]);

        return $fournisseur;
    }

    /**
     * @return array{0: Article, 1: Article}
     */
    private function seedArticles(): array
    {
        $categorie = Categorie::firstOrCreate(['nom' => 'Catégorie seed PDF']);
        $sousCategorie = SousCategorie::firstOrCreate(
            ['nom' => 'Sous-catégorie seed PDF', 'categorie_id' => $categorie->id]
        );
        $unite = UniteMesure::firstOrCreate(
            ['ref' => 'PCE'],
            ['nom' => 'Pièce']
        );
        $uniteBotte = UniteMesure::firstOrCreate(
            ['ref' => 'BOT'],
            ['nom' => 'Botte']
        );

        $article1 = Article::firstOrCreate(
            ['reference' => 'CF850A-SEED'],
            [
                'nom' => 'Coffre fort à Digit',
                'quantite_stock' => 50,
                'prix_unitaire' => 70_000,
                'cout_moyen_pondere' => 70_000,
                'unite_mesure' => $unite->id,
                'categorie_id' => $categorie->id,
                'sous_categorie_id' => $sousCategorie->id,
            ]
        );

        $article2 = Article::firstOrCreate(
            ['reference' => 'HA12-SEED'],
            [
                'nom' => 'Detecteur de faux billets pro',
                'quantite_stock' => 30,
                'prix_unitaire' => 120_000,
                'cout_moyen_pondere' => 120_000,
                'unite_mesure' => $uniteBotte->id,
                'categorie_id' => $categorie->id,
                'sous_categorie_id' => $sousCategorie->id,
            ]
        );

        return [$article1, $article2];
    }

    private function seedBonCommande(
        User $admin,
        ClientFournisseur $fournisseur,
        Projet $projet,
        Article $article1,
        Article $article2
    ): BonCommande {
        $data = [
            'reference' => 'PO-SEED-000223',
            'date_commande' => Carbon::parse('2023-07-31'),
            'fournisseur_id' => $fournisseur->id,
            'user_id' => $admin->id,
            'montant_total' => 1_200_003,
            'date_livraison_prevue' => Carbon::parse('2023-08-15'),
            'statut' => 'confirmée',
            'conditions_paiement' => 'Paiement CASH - Chèque',
            'mode_reglement' => 'Paiement CASH - Chèque',
            'delai_reglement' => 'En référence au contrat',
            'notes' => 'PROFORMA FB/U/1758/14072023',
            'lieu_livraison' => 'CITE FPM / ANGRE CHATEAU',
        ];

        if (Schema::hasColumn('bon_commandes', 'projet_id')) {
            $data['projet_id'] = $projet->id;
        }

        $bonCommande = BonCommande::updateOrCreate(
            ['reference' => 'PO-SEED-000223'],
            $data
        );

        LigneBonCommande::updateOrCreate(
            ['bon_commande_id' => $bonCommande->id, 'article_id' => $article1->id],
            [
                'quantite' => 2,
                'prix_unitaire' => 70_000,
                'remise' => 0,
                'quantite_recue' => 1,
            ]
        );

        LigneBonCommande::updateOrCreate(
            ['bon_commande_id' => $bonCommande->id, 'article_id' => $article2->id],
            [
                'quantite' => 10,
                'prix_unitaire' => 106_000,
                'remise' => 5,
                'quantite_recue' => 0,
            ]
        );

        $bonCommande->update(['montant_total' => 1_200_003]);

        return $bonCommande->fresh(['lignes']);
    }

    private function seedReception(
        User $admin,
        BonCommande $bonCommande,
        Article $article1,
        Article $article2
    ): Reception {
        $ligne1 = $bonCommande->lignes->firstWhere('article_id', $article1->id);

        $reception = Reception::updateOrCreate(
            ['numero_reception' => 'REC-SEED-PDF-001'],
            [
                'bon_commande_id' => $bonCommande->id,
                'date_reception' => Carbon::parse('2025-06-17 10:30:00'),
                'numero_bon_livraison' => '000103',
                'transporteur' => 'Transport seed demo',
                'observations' => 'Réception seed pour test PDF bon de livraison.',
                'statut' => 'partielle',
                'user_id' => $admin->id,
                'quantite_totale_recue' => 1,
                'montant_total_recu' => 70_000,
            ]
        );

        if ($ligne1) {
            LigneReception::updateOrCreate(
                [
                    'reception_id' => $reception->id,
                    'ligne_bon_commande_id' => $ligne1->id,
                ],
                [
                    'article_id' => $article1->id,
                    'quantite_recue' => 1,
                    'quantite_conforme' => 1,
                    'quantite_non_conforme' => 0,
                    'prix_unitaire_recu' => 70_000,
                    'etat_article' => 'neuf',
                ]
            );
        }

        return $reception;
    }

    /**
     * @return array{0: Prestation, 1: Decompte, 2: Decompte}
     */
    private function seedPrestationDecomptes(Contrat $contrat): array
    {
        $corpMetier = CorpMetier::firstOrCreate(
            ['nom' => 'Charpentier'],
            []
        );

        $artisan = Artisan::firstOrCreate(
            ['reference' => 'ART-SEED-PDF-001'],
            [
                'nom' => 'SAKO',
                'prenoms' => 'Yacouba',
                'civilite' => 'Monsieur',
                'type_piece' => 'CNI',
                'numero_piece' => 'CNI-SEED-001',
                'id_corpmetier' => $corpMetier->id,
                'type' => 'artisan',
                'fonction' => 'Charpentier',
                'localisation' => 'Yopougon, Abidjan',
                'boite_postale' => '01 BP 5676 Abidjan 01',
                'tel1' => '(225) 27 23 53 00 60',
                'mail' => 'sako.yacouba@seed.ci',
            ]
        );

        $catDepose = CategorieRubrique::firstOrCreate(
            ['nom' => 'Dépose', 'contrat_id' => $contrat->id],
            ['type' => 'bpu']
        );
        $catCharpente = CategorieRubrique::firstOrCreate(
            ['nom' => 'Charpente - Bois', 'contrat_id' => $contrat->id],
            ['type' => 'bpu']
        );

        $sousDepose = SousCategorieRubrique::firstOrCreate(
            ['nom' => 'Serie 000', 'id_session' => $catDepose->id, 'contrat_id' => $contrat->id],
            ['type' => 'bpu']
        );
        $sousCharpente = SousCategorieRubrique::firstOrCreate(
            ['nom' => 'Serie 100', 'id_session' => $catCharpente->id, 'contrat_id' => $contrat->id],
            ['type' => 'bpu']
        );

        $rubDepose = Rubrique::firstOrCreate(
            ['nom' => 'Dépose charpente existante', 'id_soussession' => $sousDepose->id, 'contrat_id' => $contrat->id],
            ['type' => 'bpu']
        );
        $rubCharpente = Rubrique::firstOrCreate(
            ['nom' => 'Charpente bois blidoouba', 'id_soussession' => $sousCharpente->id, 'contrat_id' => $contrat->id],
            ['type' => 'bpu']
        );

        $prestation = Prestation::updateOrCreate(
            [
                'prestation_titre' => 'PRESTATION DE CHARPENTE BLIDOUBA (seed PDF)',
                'id_contrat' => $contrat->id,
            ],
            [
                'id_artisan' => $artisan->id,
                'detail' => 'Démo décompte artisan / attachement travaux',
                'montant' => 2_500_000,
                'taux_avancement' => 78.15,
                'statut' => 'en cours',
                'corps_metier_id' => $corpMetier->id,
            ]
        );

        $ligne1 = LignePrestation::updateOrCreate(
            [
                'id_prestation' => $prestation->id,
                'designation' => 'Travaux de maçonnerie en Fondation',
            ],
            [
                'unite' => 'Ff',
                'quantite' => 1,
                'cout_unitaire' => 545_800,
                'montant' => 545_800,
                'taux_avancement' => 78.15,
                'montant_paye' => 426_446.70,
                'montant_reste' => 119_353.30,
                'id_rubrique' => $rubDepose->id,
            ]
        );

        $ligne2 = LignePrestation::updateOrCreate(
            [
                'id_prestation' => $prestation->id,
                'designation' => 'Fourniture et pose charpente',
            ],
            [
                'unite' => 'U',
                'quantite' => 1,
                'cout_unitaire' => 1_954_200,
                'montant' => 1_954_200,
                'taux_avancement' => 78.15,
                'montant_paye' => 1_527_207.30,
                'montant_reste' => 426_992.70,
                'id_rubrique' => $rubCharpente->id,
            ]
        );

        Decompte::where('id_prestation', $prestation->id)->delete();

        $decompte1 = Decompte::create([
            'titre' => 'Décompte N°1 du 15/01/2026',
            'montant' => 1_041_500,
            'pourcentage' => 41.66,
            'id_prestation' => $prestation->id,
            'created_at' => Carbon::parse('2026-01-15'),
            'updated_at' => Carbon::parse('2026-01-15'),
        ]);

        $decompte2 = Decompte::create([
            'titre' => 'Décompte N°2 du 13/02/2026',
            'montant' => 545_800,
            'pourcentage' => 36.49,
            'id_prestation' => $prestation->id,
            'created_at' => Carbon::parse('2026-02-13'),
            'updated_at' => Carbon::parse('2026-02-13'),
        ]);

        return [$prestation->fresh(), $decompte1, $decompte2];
    }

    private function seedVente(ClientFournisseur $client, Article $article1, Article $article2): Vente
    {
        $totalHt = 560_000;
        $tva = round($totalHt * 0.18, 2);
        $totalTtc = $totalHt + $tva;

        $vente = Vente::updateOrCreate(
            ['numero_client' => 'CLI-PDF-SEED-001'],
            [
                'client_id' => $client->id,
                'nom_client' => $client->nom_raison_sociale,
                    'commentaire' => 'DRN TORTIYA',
                'total' => $totalTtc,
                'total_ht' => $totalHt,
                'tva' => $tva,
                'total_ttc' => $totalTtc,
                'statut' => 'En attente',
            ]
        );

        $vente->articles()->sync([
            $article1->id => [
                'quantite' => 4,
                'prix_unitaire' => 70_000,
                'sous_total' => 280_000,
            ],
            $article2->id => [
                'quantite' => 2,
                'prix_unitaire' => 140_000,
                'sous_total' => 280_000,
            ],
        ]);

        return $vente->fresh(['client', 'articles']);
    }

    private function printTestLinks(
        BonCommande $bonCommande,
        Reception $reception,
        Prestation $prestation,
        Decompte $decompte1,
        Decompte $decompte2,
        Vente $vente
    ): void {
        $base = rtrim(config('app.url', 'http://localhost'), '/');

        $lines = [
            'PdfDemoSeeder terminé. Connectez-vous (admin@sia.com / password123) puis ouvrez :',
            '',
            "Bon de commande PDF     : {$base}/bon-commandes/{$bonCommande->id}/pdf",
            "Bon de livraison PDF    : {$base}/receptions/{$reception->id}/bon-livraison/pdf",
            "Fiche versement (D1)    : {$base}/prestations/{$prestation->id}/decompte/{$decompte1->id}",
            "Fiche versement (D2)    : {$base}/prestations/{$prestation->id}/decompte/{$decompte2->id}",
            "Attachement travaux (D2): {$base}/prestations/{$prestation->id}/decompte/{$decompte2->id}/attachement",
            "Facture proforma vente  : {$base}/ventes/{$vente->id}/facture",
            '',
            'UI : Réceptions → détail REC-SEED-PDF-001 | Prestations → voir lignes → décomptes | Ventes → détail.',
        ];

        foreach ($lines as $line) {
            $this->command?->info($line);
        }
    }
}
