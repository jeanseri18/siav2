<?php
// CaisseController.php
namespace App\Http\Controllers;

use App\Models\BrouillardCaisse;
use App\Models\DemandeDepense;
use App\Models\ApprovisionnementCaisse;
use App\Models\BU;
use Illuminate\Http\Request;

class CaisseController extends Controller
{
    public function showBrouillardCaisse()
    {
   // Récupérer l'ID du bus depuis la session
   $id_bu = session('selected_bu');

   // Vérifier si l'ID du bus est présent dans la session
   if (!$id_bu) {
       return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
   }
    $bus = BU::find($id_bu);
        $brouillardCaisse = BrouillardCaisse::where('bus_id', $id_bu)->orderBy('created_at', 'desc')->get();
        return view('caisse.brouillard', compact('bus', 'brouillardCaisse'));
    }
    

    public function saisirDepense(Request $request)
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
        $bus = BU::find($id_bu);
        $soldeCaisse = (float)$bus->soldecaisse - (float)$request->montant;
        
        // Mettre à jour le solde de caisse
        $bus->update(['soldecaisse' => $soldeCaisse]);

        // Enregistrer la dépense dans le brouillard de caisse
        BrouillardCaisse::create([
            'bus_id' => $id_bu,
            'type' => 'Sortie',
            'montant' => $request->montant,
            'motif' => $request->motif,
            'solde_cumule' => $soldeCaisse
        ]);

        return redirect()->back()->with('success', 'Depense effectué avec succès.');    }

    public function approvisionnerCaisse(Request $request)
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
        $bus = BU::find($id_bu);
        $soldeCaisse = (float)$bus->soldecaisse + (float)$request->montant;
        
        // Mettre à jour le solde de caisse
        $bus->update(['soldecaisse' => $soldeCaisse]);

        // Enregistrer l'approvisionnement dans le brouillard de caisse
        BrouillardCaisse::create([
            'bus_id' => $id_bu,
            'type' => 'Entrée',
            'montant' => $request->montant,
            'motif' => $request->motif,
            'solde_cumule' => $soldeCaisse
        ]);

        return redirect()->back()->with('success', 'Approvisionnement effectué avec succès.');    }

    public function demandeDepense(Request $request)
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
        $bus = BU::find($id_bu);
        
        $demande = DemandeDepense::create([
            'bus_id' => $id_bu,
            'montant' => $request->montant,
            'motif' => $request->motif,
            'statut' => 'en attente'
        ]);

        return redirect()->back()->with('success', 'Demande crée avec succès.');    }

    public function validerDemandeDepense($demandeId)
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
        $demande = DemandeDepense::find($demandeId);
        $bus = $demande->bus;
        
        // Valider la demande, mettre à jour le solde et ajouter la sortie
        $soldeCaisse = (float)$bus->soldecaisse - (float)$demande->montant;
        $bus->update(['soldecaisse' => $soldeCaisse]);

        // Enregistrer la dépense dans le brouillard de caisse
        BrouillardCaisse::create([
            'bus_id' => $id_bu,
            'type' => 'Sortie',
            'montant' => $demande->montant,
            'motif' => $demande->motif,
            'solde_cumule' => $soldeCaisse
        ]);

        // Mettre à jour le statut de la demande
        $demande->update(['statut' => 'validée']);

        return redirect()->back()->with('success', 'Demande validée effectué avec succès.');    }

    public function annulerDemandeDepense($demandeId)
    {
        $demande = DemandeDepense::find($demandeId);
        $demande->update(['statut' => 'annulée']);
               return redirect()->back()->with('success', 'Demande annulée avec  effectué avec succès.');
    }
    public function listerDemandesDepenses()
{        $id_bu = session('selected_bu');

    // Vérifier si l'ID du bus est présent dans la session
    if (!$id_bu) {
        return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
    }

    // Récupérer les demandes de dépenses pour ce bus
    $bus = BU::find($id_bu);
    $demandes = DemandeDepense::where('bus_id', $id_bu)->orderBy('created_at', 'desc')->get();

    return view('caisse.demandedepenseliste', compact('bus', 'demandes'));
}

}
