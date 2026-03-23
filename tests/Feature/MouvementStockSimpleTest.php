<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Projet;
use App\Models\Article;
use App\Models\Contrat;
use App\Models\StockProjet;
use App\Models\DemandeRavitaillement;
use App\Models\LigneDemandeRavitaillement;
use App\Models\TransfertStock;
use App\Models\MouvementStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class MouvementStockSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $projet;
    protected $article;
    protected $contrat;
    protected $stock;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un utilisateur pour l'authentification
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'photo' => 'default.jpg'
        ]);
        
        // Créer les données de base avec factories
        $this->projet = Projet::factory()->create(['nom_projet' => 'Projet Test Stock']);
        
        $this->article = Article::factory()->create([
            'nom' => 'Ciment Test', 
            'reference' => 'ART-TEST-001',
            'type' => 'Materiau'
        ]);
        
        $this->contrat = Contrat::factory()->create([
            'id_projet' => $this->projet->id,
            'ref_contrat' => 'CTR-TEST-001'
        ]);

        // Initialiser un stock de départ (200 unités)
        $this->stock = StockProjet::create([
            'id_projet' => $this->projet->id,
            'article_id' => $this->article->id,
            'quantite' => 200,
            'id_contrat' => $this->contrat->id
        ]);
    }

    /**
     * Test simple du cycle de ravitaillement
     */
    public function test_cycle_ravitaillement_simple()
    {
        $this->actingAs($this->user);

        // 1. Création de la demande
        $response = $this->withSession(['contrat_id' => $this->contrat->id])
            ->post(route('demandes-ravitaillement.store'), [
                'reference' => 'DR-TEST-001',
                'objet' => 'Besoin Ciment',
                'priorite' => 'normale',
                'date_demande' => now()->format('Y-m-d'),
                'lignes' => [
                    [
                        'article_id' => $this->article->id,
                        'quantite_demandee' => 20
                    ]
                ]
            ]);

        $response->assertRedirect();
        $demande = DemandeRavitaillement::where('reference', 'DR-TEST-001')->first();
        $this->assertNotNull($demande);
        $this->assertEquals('en_attente', $demande->statut);

        // 2. Approbation
        $response = $this->post(route('demandes-ravitaillement.approuver', $demande), [
            'commentaires' => 'OK pour moi'
        ]);
        
        $demande->refresh();
        $this->assertEquals('approuvee', $demande->statut);
        
        // Vérifier que le stock N'A PAS bougé à l'approbation
        $this->assertEquals(200, $this->stock->fresh()->quantite);
        $this->assertEquals(20, $demande->lignes->first()->quantite_approuvee);

        // 3. Livraison partielle (15 unités sur 20)
        $response = $this->post(route('demandes-ravitaillement.livrer', $demande), [
            'date_livraison' => now()->format('Y-m-d'),
            'lignes' => [
                [
                    'id' => $demande->lignes->first()->id,
                    'quantite_a_livrer' => 15
                ]
            ]
        ]);

        $demande->refresh();
        $ligne = $demande->lignes->first();

        // Le stock doit avoir diminué de 15 (200 - 15 = 185)
        $this->assertEquals(185, $this->stock->fresh()->quantite);
        // La quantité livrée doit être à 15
        $this->assertEquals(15, $ligne->quantite_livree);
        // Statut doit être 'en_cours' car partiel (15/20)
        $this->assertEquals('en_cours', $demande->statut);

        // Vérifier création Mouvement Stock (Sortie)
        $this->assertDatabaseHas('mouvements_stock', [
            'stock_projet_id' => $this->stock->id,
            'type_mouvement' => 'sortie',
            'quantite' => -15,
            'reference_mouvement' => 'RAV-' . $demande->reference
        ]);

        // 4. Réception avec refus partiel (Reçu 10, Refusé 5 sur les 15 livrés)
        $response = $this->post(route('demandes-ravitaillement.receptionner', $demande), [
            'date_reception' => now()->format('Y-m-d'),
            'lignes' => [
                [
                    'id' => $ligne->id,
                    'quantite_recue' => 10,
                    'motif_retour' => 'Sacs abimés'
                ]
            ]
        ]);

        $demande->refresh();
        // Vérifier que les logs sont dans les commentaires (solution sans migration)
        $this->assertStringContainsString('RECEPTION_LOG', $demande->commentaires);

        // 5. Validation du Retour (Réintégration des 5 refusés)
        // Le stock est à 185. On réintègre 5. Il doit passer à 190.
        $response = $this->post(route('demandes-ravitaillement.valider-retour', $demande), [
            'lignes' => [
                [
                    'article_id' => $this->article->id,
                    'quantite_retour' => 5
                ]
            ]
        ]);

        $this->assertEquals(190, $this->stock->fresh()->quantite);

        // Vérifier création Mouvement Stock (Retour)
        $this->assertDatabaseHas('mouvements_stock', [
            'stock_projet_id' => $this->stock->id,
            'type_mouvement' => 'retour_chantier',
            'quantite' => 5,
            'reference_mouvement' => 'RET-RAV-' . $demande->reference
        ]);
    }

    /**
     * Test simple du cycle de transfert
     */
    public function test_cycle_transfert_simple()
    {
        $this->actingAs($this->user);

        $projetDestination = Projet::factory()->create(['nom_projet' => 'Projet Dest Test']);
        
        // Stock initial Source = 200 (défini dans setUp)

        // 1. Initier le transfert (80 unités)
        $response = $this->post(route('transferts.store'), [
            'projet_source' => $this->projet->id,
            'projet_destination' => $projetDestination->id,
            'date_transfert' => now()->format('Y-m-d'),
            'items' => [
                [
                    'article_id' => $this->article->id,
                    'quantite' => 80
                ]
            ]
        ]);

        // Vérifier décrémentation immédiate source (200 - 80 = 120)
        $this->assertEquals(120, $this->stock->fresh()->quantite);

        // Vérifier création enregistrement TransfertStock
        $transfert = TransfertStock::where('id_projet_source', $this->projet->id)
            ->where('id_projet_destination', $projetDestination->id)
            ->first();
        $this->assertNotNull($transfert);
        $this->assertEquals(80, $transfert->quantite);

        // Vérifier Mouvement Sortie Source
        $this->assertDatabaseHas('mouvements_stock', [
            'stock_projet_id' => $this->stock->id,
            'type_mouvement' => 'transfert',
            'quantite' => -80,
            'reference_mouvement' => 'TR-OUT-' . $transfert->id
        ]);

        // Vérifier que la destination n'a PAS encore reçu le stock
        $stockDest = StockProjet::where('id_projet', $projetDestination->id)
            ->where('article_id', $this->article->id)
            ->first();
        // Le stock dest peut ne pas exister ou être à 0, mais ne doit pas avoir augmenté
        if ($stockDest) {
            $this->assertEquals(0, $stockDest->quantite);
        }

        // 2. Réceptionner le transfert
        $response = $this->post(route('transferts.receptionner', $transfert));

        // Vérifier incrémentation destination
        $stockDest = StockProjet::where('id_projet', $projetDestination->id)
            ->where('article_id', $this->article->id)
            ->first();
        $this->assertNotNull($stockDest);
        $this->assertEquals(80, $stockDest->quantite);

        // Vérifier Mouvement Entrée Destination
        $this->assertDatabaseHas('mouvements_stock', [
            'stock_projet_id' => $stockDest->id,
            'type_mouvement' => 'transfert',
            'quantite' => 80,
            'reference_mouvement' => 'TR-IN-' . $transfert->id
        ]);
    }
}
