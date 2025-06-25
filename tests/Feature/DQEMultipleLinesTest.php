<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contrat;
use App\Models\DQE;
use App\Models\Bpu;
use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use App\Models\Rubrique;

class DQEMultipleLinesTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $contrat;
    private $dqe;
    private $bpus;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur avec les bonnes permissions
        $this->user = User::factory()->create([
            'role' => 'chef_projet'
        ]);
        
        // Créer un contrat
        $this->contrat = Contrat::factory()->create();
        
        // Créer un DQE en brouillon
        $this->dqe = DQE::factory()->create([
            'contrat_id' => $this->contrat->id,
            'statut' => 'brouillon'
        ]);
        
        // Créer la hiérarchie pour les BPUs
        $categorie = CategorieRubrique::factory()->create(['nom' => 'Test Catégorie']);
        $sousCategorie = SousCategorieRubrique::factory()->create([
            'nom' => 'Test Sous-catégorie',
            'id_session' => $categorie->id
        ]);
        $rubrique = Rubrique::factory()->create([
            'nom' => 'Test Rubrique',
            'id_soussession' => $sousCategorie->id
        ]);
        
        // Créer plusieurs BPUs
        $this->bpus = collect();
        for ($i = 1; $i <= 3; $i++) {
            $this->bpus->push(Bpu::factory()->create([
                'designation' => "BPU Test {$i}",
                'unite' => 'm²',
                'pu_ht' => 1000 * $i,
                'id_rubrique' => $rubrique->id
            ]));
        }
    }

    /** @test */
    public function it_can_add_multiple_lines_to_dqe()
    {
        $this->actingAs($this->user);
        
        // Préparer les données pour l'ajout multiple
        $bpusData = [
            [
                'bpu_id' => $this->bpus[0]->id,
                'quantite' => 2.5
            ],
            [
                'bpu_id' => $this->bpus[1]->id,
                'quantite' => 1.0
            ],
            [
                'bpu_id' => $this->bpus[2]->id,
                'quantite' => 3.0
            ]
        ];
        
        // Effectuer la requête d'ajout multiple
        $response = $this->post(route('dqe.lines.addMultiple', $this->dqe->id), [
            'bpus' => $bpusData
        ]);
        
        // Vérifier la redirection
        $response->assertRedirect(route('dqe.edit', $this->dqe->id));
        $response->assertSessionHas('success', '3 ligne(s) ajoutée(s) avec succès.');
        
        // Vérifier que les lignes ont été créées
        $this->assertDatabaseCount('dqe_lignes', 3);
        
        // Vérifier les détails de chaque ligne
        foreach ($bpusData as $index => $bpuData) {
            $bpu = $this->bpus[$index];
            $this->assertDatabaseHas('dqe_lignes', [
                'dqe_id' => $this->dqe->id,
                'bpu_id' => $bpu->id,
                'designation' => $bpu->designation,
                'quantite' => $bpuData['quantite'],
                'unite' => $bpu->unite,
                'pu_ht' => $bpu->pu_ht,
                'montant_ht' => $bpu->pu_ht * $bpuData['quantite']
            ]);
        }
        
        // Vérifier que les totaux du DQE ont été mis à jour
        $this->dqe->refresh();
        $expectedTotal = (1000 * 2.5) + (2000 * 1.0) + (3000 * 3.0);
        $this->assertEquals($expectedTotal, $this->dqe->montant_total_ht);
    }

    /** @test */
    public function it_validates_required_fields_for_multiple_lines()
    {
        $this->actingAs($this->user);
        
        // Test sans données BPU
        $response = $this->post(route('dqe.lines.addMultiple', $this->dqe->id), [
            'bpus' => []
        ]);
        
        $response->assertSessionHasErrors(['bpus']);
        
        // Test avec BPU invalide
        $response = $this->post(route('dqe.lines.addMultiple', $this->dqe->id), [
            'bpus' => [
                [
                    'bpu_id' => 99999, // ID inexistant
                    'quantite' => 1.0
                ]
            ]
        ]);
        
        $response->assertSessionHasErrors(['bpus.0.bpu_id']);
        
        // Test avec quantité invalide
        $response = $this->post(route('dqe.lines.addMultiple', $this->dqe->id), [
            'bpus' => [
                [
                    'bpu_id' => $this->bpus[0]->id,
                    'quantite' => 0 // Quantité invalide
                ]
            ]
        ]);
        
        $response->assertSessionHasErrors(['bpus.0.quantite']);
    }

    /** @test */
    public function it_prevents_adding_lines_to_non_draft_dqe()
    {
        $this->actingAs($this->user);
        
        // Changer le statut du DQE
        $this->dqe->update(['statut' => 'validé']);
        
        $response = $this->post(route('dqe.lines.addMultiple', $this->dqe->id), [
            'bpus' => [
                [
                    'bpu_id' => $this->bpus[0]->id,
                    'quantite' => 1.0
                ]
            ]
        ]);
        
        // Vérifier qu'aucune ligne n'a été ajoutée
        $this->assertDatabaseCount('dqe_lignes', 0);
    }

    /** @test */
    public function it_handles_large_number_of_lines()
    {
        $this->actingAs($this->user);
        
        // Créer plus de BPUs
        $largeBpuSet = collect();
        for ($i = 4; $i <= 20; $i++) {
            $largeBpuSet->push(Bpu::factory()->create([
                'designation' => "BPU Large Test {$i}",
                'unite' => 'm²',
                'pu_ht' => 500 * $i,
                'id_rubrique' => $this->bpus[0]->rubrique->id
            ]));
        }
        
        // Préparer les données pour un grand nombre de lignes
        $bpusData = [];
        foreach ($largeBpuSet as $bpu) {
            $bpusData[] = [
                'bpu_id' => $bpu->id,
                'quantite' => 1.0
            ];
        }
        
        // Effectuer la requête
        $response = $this->post(route('dqe.lines.addMultiple', $this->dqe->id), [
            'bpus' => $bpusData
        ]);
        
        $response->assertRedirect(route('dqe.edit', $this->dqe->id));
        $response->assertSessionHas('success', '17 ligne(s) ajoutée(s) avec succès.');
        
        // Vérifier que toutes les lignes ont été créées
        $this->assertDatabaseCount('dqe_lignes', 17);
    }
}