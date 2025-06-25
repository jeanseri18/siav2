<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contrat;
use App\Models\DQE;
use App\Models\ClientFournisseur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class DQEContratMontantUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $contrat;
    protected $dqe;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur avec les permissions appropriées
        $this->user = User::factory()->create([
            'role' => 'chef_projet'
        ]);
        
        // Créer un client
        $client = ClientFournisseur::factory()->create([
            'type' => 'client'
        ]);
        
        // Créer un contrat sans montant initial
        $this->contrat = Contrat::create([
            'ref_contrat' => 'TEST_001',
            'nom_contrat' => 'Contrat de test',
            'date_debut' => now(),
            'type_travaux' => 'Construction',
            'taux_garantie' => 10,
            'client_id' => $client->id,
            'montant' => null, // Pas de montant initial
            'statut' => 'en cours'
        ]);
        
        // Créer un DQE en brouillon
        $this->dqe = DQE::create([
            'contrat_id' => $this->contrat->id,
            'reference' => 'DQE_TEST_001',
            'montant_total_ht' => 1000000,
            'montant_total_ttc' => 1180000, // Avec TVA 18%
            'statut' => 'brouillon'
        ]);
    }

    /** @test */
    public function test_contrat_montant_is_updated_when_dqe_is_validated()
    {
        // Vérifier que le contrat n'a pas de montant initial
        $this->assertNull($this->contrat->fresh()->montant);
        
        // Simuler la validation du DQE
        $this->actingAs($this->user)
            ->put(route('dqe.update', $this->dqe->id), [
                'reference' => $this->dqe->reference,
                'notes' => 'DQE validé pour test',
                'statut' => 'validé'
            ]);
        
        // Vérifier que le montant du contrat a été mis à jour
        $contratUpdated = $this->contrat->fresh();
        $this->assertEquals(1180000, $contratUpdated->montant);
    }

    /** @test */
    public function test_contrat_montant_is_not_updated_when_dqe_status_unchanged()
    {
        // Mettre le DQE déjà en statut validé
        $this->dqe->update(['statut' => 'validé']);
        $this->contrat->update(['montant' => 1180000]);
        
        // Modifier autre chose que le statut
        $this->actingAs($this->user)
            ->put(route('dqe.update', $this->dqe->id), [
                'reference' => 'DQE_MODIFIED',
                'notes' => 'Notes modifiées',
                'statut' => 'validé' // Statut inchangé
            ]);
        
        // Le montant ne devrait pas changer
        $contratUpdated = $this->contrat->fresh();
        $this->assertEquals(1180000, $contratUpdated->montant);
    }

    /** @test */
    public function test_unauthorized_user_cannot_validate_dqe()
    {
        // Créer un utilisateur sans permissions
        $unauthorizedUser = User::factory()->create([
            'role' => 'employe'
        ]);
        
        // Tenter de valider le DQE
        $response = $this->actingAs($unauthorizedUser)
            ->put(route('dqe.update', $this->dqe->id), [
                'reference' => $this->dqe->reference,
                'notes' => 'Tentative de validation',
                'statut' => 'validé'
            ]);
        
        // Vérifier que la validation est refusée
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Vérifier que le DQE n'est pas validé
        $this->assertEquals('brouillon', $this->dqe->fresh()->statut);
        
        // Vérifier que le montant du contrat n'a pas été mis à jour
        $this->assertNull($this->contrat->fresh()->montant);
    }
}