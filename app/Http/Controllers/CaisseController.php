<?php
// CaisseController.php
namespace App\Http\Controllers;

use App\Models\BrouillardCaisse;
use App\Models\DemandeDepense;
use App\Models\ApprovisionnementCaisse;
use App\Models\BU;
use App\Models\Banque;
use App\Models\ModePaiement;
use App\Models\User;
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
        
        // Calculs pour le tableau de bord
        $now = now();
        $debutMois = $now->copy()->startOfMonth();
        
        // Solde début de mois (dernier solde_cumule avant le début du mois)
        $soldeDebutMois = BrouillardCaisse::where('bus_id', $id_bu)
            ->where('created_at', '<', $debutMois)
            ->orderBy('created_at', 'desc')
            ->value('solde_cumule') ?? 0;
            
        // Montant total des sorties du mois en cours
        $totalSortiesMois = BrouillardCaisse::where('bus_id', $id_bu)
            ->where('type', 'Sortie')
            ->where('created_at', '>=', $debutMois)
            ->sum('montant');
            
        // Montant total des approvisionnements du mois en cours
        $totalApproMois = BrouillardCaisse::where('bus_id', $id_bu)
            ->where('type', 'Entrée')
            ->where('created_at', '>=', $debutMois)
            ->sum('montant');
            
        // Solde actuel
        $soldeActuel = (float)$bus->soldecaisse;
        
        // Récupérer la liste des responsables hiérarchiques (chef_projet, conducteur_travaux, admin, dg)
        $responsables = User::whereIn('role', ['chef_projet', 'conducteur_travaux', 'admin', 'dg'])
            ->where('status', 'actif')
            ->orderBy('nom')
            ->get();
        
        return view('caisse.brouillard', compact('bus', 'brouillardCaisse', 'soldeDebutMois', 'totalSortiesMois', 'totalApproMois', 'soldeActuel', 'responsables'));
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
            'motif' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'responsable_hierarchique_id' => 'required|exists:users,id',
            'justification' => 'required|string'
        ]);
        
        // Créer la demande de dépense avec le nouveau workflow
        $demande = DemandeDepense::create([
            'bus_id' => $id_bu,
            'user_id' => Auth::id(),
            'montant' => $request->montant,
            'motif' => $request->motif . "\n\nJustification: " . $request->justification,
            'responsable_hierarchique_id' => $request->responsable_hierarchique_id,
            'statut' => 'en_attente_responsable',
            'statut_responsable' => 'en_attente',
            'statut_raf' => 'en_attente'
        ]);

        return redirect()->back()->with('success', 'Demande de dépense créée avec succès et envoyée à votre responsable hiérarchique.');
    }

    // Approbation par le responsable hiérarchique
    public function approuverParResponsable(Request $request, $demandeId)
    {
        $demande = DemandeDepense::findOrFail($demandeId);
        
        // Vérifier que l'utilisateur est bien le responsable hiérarchique assigné
        if ($demande->responsable_hierarchique_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à approuver cette demande.');
        }
        
        $request->validate([
            'action' => 'required|in:approuver,rejeter',
            'commentaire' => 'nullable|string'
        ]);
        
        if ($request->action === 'approuver') {
            // Trouver un RAF disponible (admin ou dg)
            $raf = User::whereIn('role', ['admin', 'dg'])->where('status', 'actif')->first();
            
            $demande->update([
                'statut_responsable' => 'approuve',
                'date_approbation_responsable' => now(),
                'commentaire_responsable' => $request->commentaire,
                'raf_id' => $raf ? $raf->id : null,
                'statut' => 'approuve_responsable'
            ]);
            
            return redirect()->back()->with('success', 'Demande approuvée et transmise au RAF.');
        } else {
            $demande->update([
                'statut_responsable' => 'rejete',
                'date_approbation_responsable' => now(),
                'commentaire_responsable' => $request->commentaire,
                'statut' => 'rejete'
            ]);
            
            return redirect()->back()->with('success', 'Demande rejetée.');
        }
    }
    
    // Approbation par le RAF
    public function approuverParRAF(Request $request, $demandeId)
    {
        $demande = DemandeDepense::findOrFail($demandeId);
        
        // Vérifier que l'utilisateur est bien le RAF assigné ou a le rôle approprié
        if ($demande->raf_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'dg'])) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à approuver cette demande.');
        }
        
        $request->validate([
            'action' => 'required|in:approuver,rejeter',
            'commentaire' => 'nullable|string'
        ]);
        
        if ($request->action === 'approuver') {
            $demande->update([
                'statut_raf' => 'approuve',
                'date_approbation_raf' => now(),
                'commentaire_raf' => $request->commentaire,
                'statut' => 'approuve_raf'
            ]);
            
            return redirect()->back()->with('success', 'Demande approuvée par le RAF. Elle peut maintenant être validée pour paiement.');
        } else {
            $demande->update([
                'statut_raf' => 'rejete',
                'date_approbation_raf' => now(),
                'commentaire_raf' => $request->commentaire,
                'statut' => 'rejete'
            ]);
            
            return redirect()->back()->with('success', 'Demande rejetée par le RAF.');
        }
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
        $user = Auth::user();
        $demandesDepenses = collect();
        
        // Si l'utilisateur est admin, dg, caissier ou contrôleur caisse, il voit toutes les demandes
        if (in_array($user->role, ['admin', 'dg', 'caissier', 'controleur_caisse'])) {
            $demandesDepenses = DemandeDepense::with(['bu', 'user', 'responsableHierarchique', 'raf'])->get();
        }
        // Si l'utilisateur est responsable hiérarchique, il voit ses demandes et celles à approuver
        elseif (in_array($user->role, ['chef_projet', 'conducteur_travaux'])) {
            $demandesDepenses = DemandeDepense::with(['bu', 'user', 'responsableHierarchique', 'raf'])
                ->where(function($query) use ($user) {
                    $query->where('user_id', $user->id) // Ses propres demandes
                          ->orWhere('responsable_hierarchique_id', $user->id); // Demandes à approuver
                })->get();
        }
        // Utilisateur normal : seulement ses propres demandes
        else {
            $demandesDepenses = DemandeDepense::with(['bu', 'user', 'responsableHierarchique', 'raf'])
                ->where('user_id', $user->id)->get();
        }
        
        return view('caisse.demandedepenseliste', compact('demandesDepenses'));
    }
    
    // Nouvelle méthode pour les demandes en attente d'approbation
    public function demandesEnAttente()
    {
        $user = Auth::user();
        $demandesEnAttente = collect();
        
        // Demandes en attente pour le responsable hiérarchique
        if (in_array($user->role, ['chef_projet', 'conducteur_travaux'])) {
            $demandesEnAttente = DemandeDepense::with(['bu', 'user', 'responsableHierarchique', 'raf'])
                ->where('responsable_hierarchique_id', $user->id)
                ->where('statut', 'en_attente_responsable')
                ->get();
        }
        // Demandes en attente pour le RAF
        elseif (in_array($user->role, ['admin', 'dg'])) {
            $demandesEnAttente = DemandeDepense::with(['bu', 'user', 'responsableHierarchique', 'raf'])
                ->where('raf_id', $user->id)
                ->where('statut', 'approuve_responsable')
                ->get();
        }
        
        return view('caisse.demandes-en-attente', compact('demandesEnAttente'));
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
