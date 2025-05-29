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
}
    