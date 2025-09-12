<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DemandeRavitaillement;
use App\Models\LigneDemandeRavitaillement;
use App\Models\User;
use App\Models\Contrat;
use App\Models\ClientFournisseur;
use App\Models\Article;
use App\Models\UniteMesure;
use Carbon\Carbon;

class DemandeRavitaillementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer des données existantes
        $users = User::all();
        $contrats = Contrat::all();
        $fournisseurs = ClientFournisseur::all();
        $articles = Article::all();
        $unites = UniteMesure::all();

        if ($users->isEmpty() || $contrats->isEmpty()) {
            $this->command->info('Aucun utilisateur ou contrat trouvé. Veuillez d\'abord créer des utilisateurs et des contrats.');
            return;
        }

        // Créer quelques demandes de ravitaillement
        for ($i = 1; $i <= 5; $i++) {
            $demande = DemandeRavitaillement::create([
                'reference' => 'DR-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'objet' => 'Demande de ravitaillement #' . $i,
                'description' => 'Description de la demande de ravitaillement numéro ' . $i,
                'statut' => 'en_attente',
                'priorite' => collect(['basse', 'normale', 'haute', 'urgente'])->random(),
                'date_demande' => Carbon::now()->subDays(rand(1, 30)),
                'date_livraison_souhaitee' => Carbon::now()->addDays(rand(1, 60)),
                'contrat_id' => $contrats->random()->id,
                'demandeur_id' => $users->random()->id,
                'fournisseur_id' => $fournisseurs->isNotEmpty() ? $fournisseurs->random()->id : null,
                'commentaires' => 'Commentaires pour la demande #' . $i,
                'montant_estime' => rand(10000, 500000)
            ]);

            // Ajouter des lignes de demande si des articles existent
            if ($articles->isNotEmpty()) {
                for ($j = 1; $j <= rand(1, 3); $j++) {
                    LigneDemandeRavitaillement::create([
                        'demande_ravitaillement_id' => $demande->id,
                        'article_id' => $articles->random()->id,
                        'quantite_demandee' => rand(1, 100),
                        'prix_unitaire_estime' => rand(100, 10000),
                        'unite_mesure_id' => $unites->isNotEmpty() ? $unites->random()->id : null,
                        'description' => 'Ligne de demande #' . $j . ' pour la demande #' . $i
                    ]);
                }
            }
        }

        $this->command->info('5 demandes de ravitaillement créées avec succès.');
    }
}