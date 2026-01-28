<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FactureContrat;

class RecalculerMontantsVerses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'factures:recalculer-montants-verses {--contrat-id= : ID spécifique du contrat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculer les montants versés de toutes les factures contrat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Début du recalcul des montants versés...');
        
        $contratId = $this->option('contrat-id');
        
        if ($contratId) {
            $facturesContrat = FactureContrat::where('id', $contratId)->get();
            $this->info("Recalcul pour le contrat ID: $contratId");
        } else {
            $facturesContrat = FactureContrat::all();
            $this->info('Recalcul pour tous les contrats...');
        }
        
        $count = 0;
        
        foreach ($facturesContrat as $factureContrat) {
            $ancienMontant = $factureContrat->montant_verse;
            $nouveauMontant = $factureContrat->calculerMontantVerse();
            
            if ($ancienMontant != $nouveauMontant) {
                $factureContrat->montant_verse = $nouveauMontant;
                $factureContrat->save();
                
                $this->info("Contrat {$factureContrat->id}: Montant versé mis à jour de {$ancienMontant} à {$nouveauMontant}");
                $count++;
            }
        }
        
        $this->info("Recalcul terminé. {$count} factures contrat mises à jour.");
        
        return Command::SUCCESS;
    }
}