<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DemandeApprovisionnement;
use App\Models\User;
use App\Http\Controllers\DemandeApprovisionnementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestApprovalProcess extends Command
{
    protected $signature = 'test:approval';
    protected $description = 'Tester le processus d\'approbation des demandes d\'approvisionnement';

    public function handle()
    {
        $this->info('Début du test d\'approbation...');

        // Trouver une demande en attente
        $demande = DemandeApprovisionnement::where('statut', 'en attente')->first();

        if (!$demande) {
            $this->error('Aucune demande en attente trouvée.');
            return;
        }

        $this->info("Demande trouvée: {$demande->reference} (Statut: {$demande->statut})");
        $this->info("Lignes: {$demande->lignes->count()}");

        // Créer un utilisateur admin pour le test
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('Aucun utilisateur admin trouvé.');
            return;
        }

        // Authentifier l'utilisateur
        Auth::login($admin);
        $this->info("Connecté en tant que: {$admin->nom_complet} (Role: {$admin->role})");

        // Préparer les données de test
        $ligneIds = [];
        $quantitesApprouvees = [];

        foreach ($demande->lignes as $index => $ligne) {
            $ligneIds[$index] = $ligne->id;
            $quantitesApprouvees[$index] = $ligne->quantite_demandee; // Approuver la quantité demandée
            $this->info("Ligne {$ligne->id}: {$ligne->article->nom} - Demandé: {$ligne->quantite_demandee} - Approuvé: {$ligne->quantite_demandee}");
        }

        // Créer une requête simulée
        $request = new Request([
            'ligne_ids' => $ligneIds,
            'quantite_approuvee' => $quantitesApprouvees
        ]);

        $this->info("\nDonnées envoyées:");
        $this->info("ligne_ids: " . json_encode($ligneIds));
        $this->info("quantite_approuvee: " . json_encode($quantitesApprouvees));

        // Appeler la méthode approve
        $controller = new DemandeApprovisionnementController();
        try {
            $response = $controller->approve($request, $demande);
            $this->info("\nRéponse du contrôleur:");
            $this->info("Status: " . $response->getStatusCode());
            $this->info("Redirection: " . $response->getTargetUrl());
            
            // Recharger la demande pour vérifier les changements
            $demande->refresh();
            $this->info("\nStatut après approbation: {$demande->statut}");
            $this->info("Approuvé par: " . ($demande->approved_by ?? 'null'));
            
            // Vérifier les lignes
            foreach ($demande->lignes as $ligne) {
                $this->info("Ligne {$ligne->id} quantité approuvée: " . ($ligne->quantite_approuvee ?? 'null'));
            }
            
            if ($demande->statut === 'approuvée') {
                $this->info("\n✅ Succès: La demande a été approuvée correctement!");
            } else {
                $this->error("\n❌ Échec: Le statut n'a pas été mis à jour.");
            }
            
        } catch (\Exception $e) {
            $this->error("\nErreur: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }
    }
}