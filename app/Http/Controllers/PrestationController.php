<?php
namespace App\Http\Controllers;

use App\Models\Prestation;
use App\Models\Artisan;
use App\Models\Contrat;
use Illuminate\Http\Request;

class PrestationController extends Controller
{
    public function index() {
        $prestations = Prestation::with(['artisan', 'contrat'])->get();
        return view('prestations.index', compact('prestations'));
    }

    public function create() {
        $projet_id = session('projet_id');
        $contrats = Contrat::where('id_projet', $projet_id)->get();
        $artisans = Artisan::all();
        return view('prestations.create', compact('contrats', 'artisans'));
    }

    public function store(Request $request) {
        $request->validate([
            'id_artisan' => 'nullable|exists:artisan,id',
            'id_contrat' => 'required',
            'prestation_titre' => 'required',
            'detail' => 'required',
            'montant' => 'nullable|numeric',
            'taux_avancement' => 'nullable|integer|min:0|max:100',
        ]);
    
        // Ajouter le statut "En cours" par défaut
        Prestation::create([
            'id_artisan' => $request->id_artisan,
            'id_contrat' => $request->id_contrat,
            'prestation_titre' => $request->prestation_titre,
            'detail' => $request->detail,
            'montant' => $request->montant,
            'taux_avancement' => $request->taux_avancement ?? 0,
            'statut' => 'En cours', // Valeur par défaut
        ]);
    
        return redirect()->route('prestations.index')->with('success', 'Prestation ajoutée avec succès');
    }

    public function edit(Prestation $prestation) {
        $projet_id = session('projet_id');
        $contrats = Contrat::where('id_projet', $projet_id)->get();
        $artisans = Artisan::all();
        return view('prestations.edit', compact('prestation', 'contrats', 'artisans'));
    }

    public function update(Request $request, Prestation $prestation) {
        $request->validate([
            'id_artisan' => 'nullable|exists:artisan,id',
            'id_contrat' => 'required',
            'prestation_titre' => 'required',
            'detail' => 'required',
            'montant' => 'nullable|numeric',
            'taux_avancement' => 'nullable|integer|min:0|max:100',
            'statut' => 'required|string'
        ]);

        $prestation->update($request->all());
        return redirect()->route('prestations.index')->with('success', 'Prestation mise à jour');
    }

    public function destroy(Prestation $prestation) {
        $prestation->delete();
        return redirect()->route('prestations.index')->with('success', 'Prestation supprimée');
    }
}
