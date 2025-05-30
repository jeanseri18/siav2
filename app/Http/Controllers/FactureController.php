<?php
namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Prestation;
use App\Models\Contrat;
use App\Models\Artisan;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function index() {
        $factures = Facture::with(['prestation', 'contrat', 'artisan'])->get();
        return view('factures.index', compact('factures'));
    }

    public function create() {
        $prestations = Prestation::all();
        $contrats = Contrat::all();
        $artisans = Artisan::all();
        return view('factures.create', compact('prestations', 'contrats', 'artisans'));
    }
    public function store(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'num' => 'required|string|unique:factures,num',
            'id_prestation' => 'nullable',
            'id_contrat' => 'nullable',
            'id_artisan' => 'nullable',
            'montant_ht' => 'required|numeric',
            'montant_total' => 'required|numeric',
            'ca_realise' => 'nullable|numeric',
            'montant_reglement' => 'nullable|numeric',
            'date_emission' => 'required|date',
            'statut' => 'required|in:en attente,payée,annulée',
        ]);
    
        // Créer la facture
        Facture::create([
            'num' => $request->num,
            'id_prestation' => $request->id_prestation,
            'id_contrat' => $request->id_contrat,
            'id_artisan' => $request->id_artisan,
            'montant_ht' => $request->montant_ht,
            'montant_total' => $request->montant_total,
            'ca_realise' => $request->ca_realise ?? 0,
            'montant_reglement' => $request->montant_reglement ?? 0,
            'statut' => $request->statut,
            'date_emission' => $request->date_emission,
            'num_decompte' => $request->decompte ?? null,
            'taux_avancement' => $request->taux_avancement ?? 0
        ]);
    
        return redirect()->route('factures.index')->with('success', 'Facture ajoutée avec succès');
    }


    public function show($id)
    {
        $facture = Facture::with(['prestation', 'contrat', 'artisan'])->findOrFail($id);
        return view('factures.show', compact('facture'));
    }
    
    /**
     * Afficher le formulaire d'édition d'une facture
     */
    public function edit($id)
    {
        $facture = Facture::findOrFail($id);
        $prestations = Prestation::all();
        $contrats = Contrat::all();
        $artisans = Artisan::all();
        
        return view('factures.edit', compact('facture', 'prestations', 'contrats', 'artisans'));
    }
    
    /**
     * Mettre à jour une facture
     */
    public function update(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);
        
        // Validation des champs du formulaire
        $request->validate([
            'num' => 'required|string|unique:factures,num,'.$id,
            'id_prestation' => 'nullable',
            'id_contrat' => 'nullable',
            'id_artisan' => 'nullable',
            'montant_ht' => 'required|numeric',
            'montant_total' => 'required|numeric',
            'ca_realise' => 'nullable|numeric',
            'montant_reglement' => 'nullable|numeric',
            'date_emission' => 'required|date',
            'statut' => 'required|in:en attente,payée,annulée',
        ]);
        
        // Mise à jour de la facture
        $facture->update([
            'num' => $request->num,
            'id_prestation' => $request->id_prestation,
            'id_contrat' => $request->id_contrat,
            'id_artisan' => $request->id_artisan,
            'montant_ht' => $request->montant_ht,
            'montant_total' => $request->montant_total,
            'ca_realise' => $request->ca_realise ?? 0,
            'montant_reglement' => $request->montant_reglement ?? 0,
            'statut' => $request->statut,
            'date_emission' => $request->date_emission,
            'num_decompte' => $request->decompte ?? null,
            'taux_avancement' => $request->taux_avancement ?? 0
        ]);
        
        // Mettre à jour le CA réalisé pour le contrat si la facture est payée
        if ($request->statut == 'payée' && $facture->id_contrat) {
            $this->updateContratCA($facture->id_contrat);
        }
        
        return redirect()->route('factures.index')->with('success', 'Facture mise à jour avec succès');
    }
    
    /**
     * Supprimer une facture
     */
    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);
        $contratId = $facture->id_contrat; // Sauvegarde l'ID du contrat avant la suppression
        
        $facture->delete();
        
        // Mettre à jour le CA réalisé pour le contrat après suppression
        if ($contratId) {
            $this->updateContratCA($contratId);
        }
        
        return redirect()->route('factures.index')->with('success', 'Facture supprimée avec succès');
    }
    
    /**
     * Générer un PDF de la facture
     */
    public function generatePDF($id)
    {
        $facture = Facture::with(['prestation', 'contrat', 'artisan'])->findOrFail($id);
        
        // Génération du PDF (nécessite l'installation d'un package comme dompdf)
        $pdf = \PDF::loadView('factures.pdf', compact('facture'));
        
        return $pdf->download('Facture_'.$facture->num.'.pdf');
    }
    
    /**
     * Changer le statut d'une facture
     */
    public function changeStatus(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);
        
        $request->validate([
            'statut' => 'required|in:en attente,payée,annulée',
            'montant_reglement' => 'required_if:statut,payée|nullable|numeric',
        ]);
        
        $oldStatus = $facture->statut;
        
        $facture->update([
            'statut' => $request->statut,
            'montant_reglement' => $request->statut == 'payée' ? $request->montant_reglement : $facture->montant_reglement,
            'date_reglement' => $request->statut == 'payée' ? now() : null,
        ]);
        
        // Si la facture est maintenant payée ou était payée et ne l'est plus, mettre à jour le CA réalisé
        if (($oldStatus != 'payée' && $request->statut == 'payée') || 
            ($oldStatus == 'payée' && $request->statut != 'payée')) {
            if ($facture->id_contrat) {
                $this->updateContratCA($facture->id_contrat);
            }
        }
        
        return redirect()->route('factures.show', $id)->with('success', 'Statut de la facture mis à jour avec succès');
    }
    
    /**
     * Mettre à jour le CA réalisé pour un contrat en fonction des factures payées
     */
    private function updateContratCA($contratId)
    {
        $caRealise = Facture::where('id_contrat', $contratId)
                           ->where('statut', 'payée')
                           ->sum('montant_total');
        
        $contrat = Contrat::findOrFail($contratId);
        $contrat->update(['ca_realise' => $caRealise]);
        
        return $caRealise;
    }
    
    /**
     * Afficher les statistiques des factures
     */
    public function statistics()
    {
        // Montant total des factures par statut
        $statsByStatus = Facture::select('statut', DB::raw('SUM(montant_total) as total'))
                              ->groupBy('statut')
                              ->get();
        
        // Montant total des factures par mois pour l'année en cours
        $statsByMonth = Facture::select(DB::raw('MONTH(date_emission) as month'), DB::raw('SUM(montant_total) as total'))
                             ->whereYear('date_emission', date('Y'))
                             ->groupBy(DB::raw('MONTH(date_emission)'))
                             ->get();
        
        // Montant total des factures par contrat
        $statsByContrat = Facture::select('id_contrat', DB::raw('SUM(montant_total) as total'))
                              ->whereNotNull('id_contrat')
                              ->groupBy('id_contrat')
                              ->with('contrat')
                              ->get();
        
        return view('factures.statistics', compact('statsByStatus', 'statsByMonth', 'statsByContrat'));
    }

}
    