<?php
// CaisseController.php
namespace App\Http\Controllers;

use App\Models\BrouillardCaisse;
use App\Models\DemandeDepense;
use App\Models\ApprovisionnementCaisse;
use App\Models\BU;
use App\Models\Banque;
use App\Models\ModePaiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
        // Valider les données du formulaire
        $request->validate([
            'bu_id' => 'required|exists:bus,id',
            'montant' => 'required|numeric|min:0',
            'motif' => 'required|string',
            'mode_paiement' => 'required|in:cheque,espece',
            'date_appro' => 'required|date',
        ]);
        
        // Validation spécifique selon le mode de paiement
        if ($request->mode_paiement == 'cheque') {
            $request->validate([
                'banque_id' => 'required|exists:banques,id',
                'reference_cheque' => 'required|string',
            ]);
        } else { // espece
            $request->validate([
                'origine_fonds' => 'required|string',
            ]);
        }
        
        $bus = BU::find($request->bu_id);
        $soldeCaisse = (float)$bus->soldecaisse + (float)$request->montant;
        
        // Mettre à jour le solde de caisse
        $bus->update(['soldecaisse' => $soldeCaisse]);

        // Préparer les données pour l'enregistrement
        $motifComplet = $request->motif;
        
        // Ajouter les détails du mode de paiement au motif
        if ($request->mode_paiement == 'cheque') {
            $banque = Banque::find($request->banque_id);
            $motifComplet .= ' (Chèque n°' . $request->reference_cheque . ' - ' . $banque->nom . ')';
        } else {
            $motifComplet .= ' (Espèce - Origine: ' . $request->origine_fonds . ')';
        }

        // Enregistrer l'approvisionnement dans le brouillard de caisse
        BrouillardCaisse::create([
            'bus_id' => $request->bu_id,
            'type' => 'Entrée',
            'montant' => $request->montant,
            'motif' => $motifComplet,
            'solde_cumule' => $soldeCaisse
        ]);
        
        // Enregistrer les détails de l'approvisionnement
        ApprovisionnementCaisse::create([
            'bus_id' => $request->bu_id,
            'montant' => $request->montant,
            'motif' => $request->motif,
            'mode_paiement' => $request->mode_paiement,
            'date_appro' => $request->date_appro,
            'banque_id' => $request->mode_paiement == 'cheque' ? $request->banque_id : null,
            'reference_cheque' => $request->mode_paiement == 'cheque' ? $request->reference_cheque : null,
            'origine_fonds' => $request->mode_paiement == 'espece' ? $request->origine_fonds : null,
        ]);

        return redirect()->back()->with('success', 'Approvisionnement effectué avec succès.');
    }

    public function demandeDepense(Request $request)
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
        
        // Valider les données du formulaire
        $request->validate([
            'mois' => 'required|string',
            'objet' => 'required|string',
            'beneficiaires' => 'required|string',
            'date_emission' => 'required|date',
            'montant_total' => 'required|numeric|min:0',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string',
            'lignes.*.quantite' => 'required|numeric|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);
        
        $bus = BU::find($id_bu);
        
        // Construire le motif détaillé
        $motifDetail = "Objet: {$request->objet} - Mois: {$request->mois} - Bénéficiaires: {$request->beneficiaires}";
        
        // Créer la demande de dépense principale
        $demande = DemandeDepense::create([
            'bus_id' => $id_bu,
            'montant' => $request->montant_total,
            'motif' => $motifDetail,
            'statut' => 'en attente'
        ]);
        
        // Stocker les détails des lignes dans le motif (puisque nous n'avons pas de table pour les lignes)
        // Dans une évolution future, on pourrait créer une table LigneDemandeDepense
        $detailsLignes = [];
        foreach ($request->lignes as $index => $ligne) {
            $detailsLignes[] = [
                'designation' => $ligne['designation'],
                'quantite' => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
                'total' => $ligne['quantite'] * $ligne['prix_unitaire']
            ];
        }
        
        // Stocker les détails des lignes en JSON dans un champ supplémentaire
        // Note: Ceci nécessiterait d'ajouter un champ 'details_lignes' à la table demandes_de_depenses
        // Pour l'instant, nous allons simplement les ajouter au motif
        $motifAvecLignes = $motifDetail . "\n" . json_encode($detailsLignes);
        $demande->update(['motif' => $motifAvecLignes]);

        return redirect()->back()->with('success', 'Demande de dépense créée avec succès.');
    }

    public function validerDemandeDepense($demandeId)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['caissier', 'chef_projet', 'conducteur_travaux', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions nécessaires pour valider cette demande.');
        }

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
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['caissier', 'chef_projet', 'conducteur_travaux', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions nécessaires pour annuler cette demande.');
        }

        $demande = DemandeDepense::find($demandeId);
        $demande->update(['statut' => 'annulée']);
               return redirect()->back()->with('success', 'Demande annulée avec  effectué avec succès.');
    }
    public function listerDemandesDepenses()
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        // Récupérer les demandes de dépenses pour ce bus
        $bus = BU::find($id_bu);
        $demandes = DemandeDepense::where('bus_id', $id_bu)->orderBy('created_at', 'desc')->get();

        return view('caisse.demandedepenseliste', compact('bus', 'demandes'));
    }
    
    public function voirDemandeDepensePDF($demandeId)
    {
        $demande = DemandeDepense::find($demandeId);
        $bus = $demande->bus;
        
        // Vérifier si la demande existe
        if (!$demande) {
            return redirect()->back()->with('error', 'Demande de dépense non trouvée.');
        }
        
        // Générer le PDF avec la vue
        $pdf = \PDF::loadView('caisse.demandedepensepdf', compact('demande', 'bus'));
        
        // Définir le nom du fichier PDF
        $filename = 'demande_depense_' . $demandeId . '.pdf';
        
        // Retourner le PDF pour téléchargement ou affichage
        return $pdf->stream($filename);
    }
    
    public function showApprovisionnementForm()
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
        
        $bus = BU::find($id_bu);
        $banques = Banque::all();
        
        return view('caisse.approvisionnement', compact('bus', 'banques'));
    }
}
