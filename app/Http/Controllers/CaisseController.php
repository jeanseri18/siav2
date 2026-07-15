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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


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
        $brouillardCaisse = BrouillardCaisse::where('bus_id', $id_bu)
            ->orderByRaw(BrouillardCaisse::sqlDateEffective().' desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Calculs pour le tableau de bord
        $now = now();
        $debutMois = $now->copy()->startOfMonth();
        
        // Solde début de mois (dernier solde_cumule avant le début du mois, selon date d'opération)
        $dateDebutMois = $debutMois->format('Y-m-d');
        $ligneAvantMois = BrouillardCaisse::where('bus_id', $id_bu)
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' < ?', [$dateDebutMois])
            ->orderByRaw(BrouillardCaisse::sqlDateEffective().' asc')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->last();
        $soldeDebutMois = $ligneAvantMois ? (float) $ligneAvantMois->solde_cumule : 0;

        $ligneReferenceDebutMois = $ligneAvantMois;
            
        $finMois = $now->copy()->endOfMonth()->format('Y-m-d');

        // Montant total des sorties du mois en cours (date d'opération)
        $totalSortiesMois = BrouillardCaisse::where('bus_id', $id_bu)
            ->where('type', 'Sortie')
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' >= ?', [$dateDebutMois])
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' <= ?', [$finMois])
            ->sum('montant');
            
        // Montant total des approvisionnements du mois en cours
        $totalApproMois = BrouillardCaisse::where('bus_id', $id_bu)
            ->where('type', 'Entrée')
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' >= ?', [$dateDebutMois])
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' <= ?', [$finMois])
            ->sum('montant');
            
        // Solde actuel
        $soldeActuel = (float)$bus->soldecaisse;

        $brouillardChrono = BrouillardCaisse::where('bus_id', $id_bu)
            ->orderByRaw(BrouillardCaisse::sqlDateEffective().' asc')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $sortiesMoisListe = BrouillardCaisse::where('bus_id', $id_bu)
            ->where('type', 'Sortie')
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' >= ?', [$dateDebutMois])
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' <= ?', [$finMois])
            ->orderByRaw(BrouillardCaisse::sqlDateEffective().' asc')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $entreesMoisListe = BrouillardCaisse::where('bus_id', $id_bu)
            ->where('type', 'Entrée')
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' >= ?', [$dateDebutMois])
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' <= ?', [$finMois])
            ->orderByRaw(BrouillardCaisse::sqlDateEffective().' asc')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $mouvementsMoisChrono = BrouillardCaisse::where('bus_id', $id_bu)
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' >= ?', [$dateDebutMois])
            ->whereRaw(BrouillardCaisse::sqlDateEffective().' <= ?', [$finMois])
            ->orderByRaw(BrouillardCaisse::sqlDateEffective().' asc')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        
        // Récupérer la liste des responsables hiérarchiques (chef_projet, conducteur_travaux, admin, dg)
        $responsables = User::whereIn('role', ['chef_projet', 'conducteur_travaux', 'admin', 'dg'])
            ->where('status', 'actif')
            ->orderBy('nom')
            ->get();
        
        return view('caisse.brouillard', compact(
            'bus',
            'brouillardCaisse',
            'soldeDebutMois',
            'totalSortiesMois',
            'totalApproMois',
            'soldeActuel',
            'responsables',
            'debutMois',
            'ligneReferenceDebutMois',
            'brouillardChrono',
            'sortiesMoisListe',
            'entreesMoisListe',
            'mouvementsMoisChrono'
        ));
    }

    /**
     * Supprime une ligne du brouillard (saisie erronée) et recalcule soldes cumulés + solde BU.
     */
    public function destroyBrouillard(BrouillardCaisse $brouillard)
    {
        $rolesAutorises = ['admin', 'dg', 'caissier', 'chef_projet', 'conducteur_travaux', 'controleur_caisse'];
        if (! in_array(Auth::user()->role, $rolesAutorises, true)) {
            abort(403);
        }

        $id_bu = session('selected_bu');
        if (! $id_bu || (int) $brouillard->bus_id !== (int) $id_bu) {
            return redirect()->route('caisse.brouillard')
                ->with('error', 'Cette transaction ne correspond pas à la BU sélectionnée.');
        }

        DB::transaction(function () use ($brouillard) {
            $busId = (int) $brouillard->bus_id;
            $brouillard->delete();
            BrouillardCaisse::synchroniserSoldesPourBus($busId);
        });

        return redirect()->route('caisse.brouillard')
            ->with('success', 'Transaction supprimée. Les soldes du brouillard et de la caisse ont été mis à jour.');
    }

    /**
     * Met à jour une ligne du brouillard et recalcule les soldes cumulés + solde BU.
     */
    public function updateBrouillard(Request $request, BrouillardCaisse $brouillard)
    {
        $rolesAutorises = ['admin', 'dg', 'caissier', 'chef_projet', 'conducteur_travaux', 'controleur_caisse'];
        if (! in_array(Auth::user()->role, $rolesAutorises, true)) {
            abort(403);
        }

        $id_bu = session('selected_bu');
        if (! $id_bu || (int) $brouillard->bus_id !== (int) $id_bu) {
            return redirect()->route('caisse.brouillard')
                ->with('error', 'Cette transaction ne correspond pas à la BU sélectionnée.');
        }

        $request->validate([
            'type' => 'required|in:Entrée,Sortie',
            'montant' => 'required|numeric|min:0.01',
            'motif' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $brouillard) {
            $brouillard->update([
                'type' => $request->type,
                'montant' => $request->montant,
                'motif' => $request->motif,
            ]);
            BrouillardCaisse::synchroniserSoldesPourBus((int) $brouillard->bus_id);
        });

        return redirect()->route('caisse.brouillard')
            ->with('success', 'Transaction modifiée. Les soldes du brouillard et de la caisse ont été recalculés.');
    }

    /**
     * Ramène le solde caisse à zéro via une écriture d'ajustement (historique conservé).
     */
    public function remiseAZeroSoldeCaisse()
    {
        $rolesAutorises = ['admin', 'dg', 'caissier', 'chef_projet', 'conducteur_travaux', 'controleur_caisse'];
        if (! in_array(Auth::user()->role, $rolesAutorises, true)) {
            abort(403);
        }

        $id_bu = session('selected_bu');
        if (! $id_bu) {
            return redirect()->route('select.bu')
                ->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $bus = BU::find($id_bu);
        if (! $bus) {
            return redirect()->route('caisse.brouillard')->with('error', 'BU introuvable.');
        }

        BrouillardCaisse::synchroniserSoldesPourBus((int) $id_bu);
        $bus->refresh();
        $solde = (float) $bus->soldecaisse;

        if (abs($solde) < 0.00001) {
            return redirect()->route('caisse.brouillard')
                ->with('success', 'Le solde caisse est déjà à zéro.');
        }

        DB::transaction(function () use ($id_bu, $solde) {
            if ($solde > 0) {
                BrouillardCaisse::create([
                    'bus_id' => $id_bu,
                    'type' => 'Sortie',
                    'montant' => $solde,
                    'motif' => 'Ajustement — remise à zéro manuelle du solde caisse',
                    'solde_cumule' => 0,
                ]);
            } else {
                BrouillardCaisse::create([
                    'bus_id' => $id_bu,
                    'type' => 'Entrée',
                    'montant' => abs($solde),
                    'motif' => 'Ajustement — remise à zéro manuelle du solde caisse',
                    'solde_cumule' => 0,
                ]);
            }
            BrouillardCaisse::synchroniserSoldesPourBus((int) $id_bu);
        });

        return redirect()->route('caisse.brouillard')
            ->with('success', 'Une écriture d\'ajustement a été enregistrée. Le solde caisse est maintenant à zéro.');
    }

    public function saisirDepense(Request $request)
    {
        $id_bu = session('selected_bu');

        // Vérifier si l'ID du bus est présent dans la session
        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $request->validate([
            'motif' => 'required|string|max:500',
            'montant' => 'required|numeric|min:0.01',
            'date_operation' => 'required|date',
            'description' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($request, $id_bu) {
            $motif = $request->motif;
            if ($request->filled('description')) {
                $motif .= ' — '.$request->description;
            }

            BrouillardCaisse::create([
                'bus_id' => $id_bu,
                'type' => 'Sortie',
                'montant' => $request->montant,
                'motif' => $motif,
                'date_operation' => $request->date_operation,
                'solde_cumule' => 0,
            ]);
            BrouillardCaisse::synchroniserSoldesPourBus((int) $id_bu);
        });

        return redirect()->back()->with('success', 'Depense effectué avec succès.');
    }

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
                'banque_id' => [
                    'required',
                    Rule::exists('banques', 'id')->where(fn ($query) => $query->where('bu_id', $request->bu_id)),
                ],
                'reference_cheque' => 'required|string',
            ]);
        } else { // espece
            $request->validate([
                'origine_fonds' => 'required|string',
            ]);
        }

        // Préparer les données pour l'enregistrement
        $motifComplet = $request->motif;

        // Ajouter les détails du mode de paiement au motif
        if ($request->mode_paiement == 'cheque') {
            $banque = Banque::find($request->banque_id);
            $motifComplet .= ' (Chèque n°' . $request->reference_cheque . ' - ' . $banque->nom . ')';
        } else {
            $motifComplet .= ' (Espèce - Origine: ' . $request->origine_fonds . ')';
        }

        $dateOperation = Carbon::parse($request->date_appro)->format('Y-m-d');

        DB::transaction(function () use ($request, $motifComplet, $dateOperation) {
            BrouillardCaisse::create([
                'bus_id' => $request->bu_id,
                'type' => 'Entrée',
                'montant' => $request->montant,
                'motif' => $motifComplet,
                'date_operation' => $dateOperation,
                'solde_cumule' => 0,
            ]);

            ApprovisionnementCaisse::create([
                'bus_id' => $request->bu_id,
                'montant' => $request->montant,
                'motif' => $request->motif,
                'mode_paiement' => $request->mode_paiement,
                'date_appro' => $dateOperation,
                'banque_id' => $request->mode_paiement == 'cheque' ? $request->banque_id : null,
                'reference_cheque' => $request->mode_paiement == 'cheque' ? $request->reference_cheque : null,
                'origine_fonds' => $request->mode_paiement == 'espece' ? $request->origine_fonds : null,
            ]);

            BrouillardCaisse::synchroniserSoldesPourBus((int) $request->bu_id);
        });

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

        $estResponsable = (int) $demande->responsable_hierarchique_id === (int) Auth::id();
        $estAdminOuDg = in_array(Auth::user()->role, ['admin', 'dg'], true);
        if (! $estResponsable && ! $estAdminOuDg) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à approuver cette demande (responsable hiérarchique ou administrateur).');
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

    /**
     * Enregistrement caisse : sorties au brouillard + solde des BU concernées,
     * pour les demandes déjà « approuvé RAF ». Réservé à l’admin, au caissier et au contrôleur de caisse.
     * (Les approbations responsable / RAF ne touchent pas au solde.)
     */
    public function enregistrerDepensesEnCaisse(Request $request)
    {
        if (! in_array(Auth::user()->role, ['admin', 'caissier', 'controleur_caisse'], true)) {
            return redirect()->back()->with('error', 'Seuls l’administrateur, le caissier et le contrôleur de caisse peuvent enregistrer des dépenses au brouillard.');
        }

        $request->validate([
            'demande_ids' => 'required|array|min:1',
            'demande_ids.*' => 'integer|exists:demandes_de_depenses,id',
        ]);

        $ids = array_values(array_unique(array_map('intval', $request->demande_ids)));

        try {
            DB::transaction(function () use ($ids) {
                $busIds = [];

                foreach ($ids as $id) {
                    $demande = DemandeDepense::lockForUpdate()->find($id);

                    if (! $demande) {
                        throw new \RuntimeException('Demande #'.$id.' introuvable.');
                    }
                    if ($demande->statut !== 'approuve_raf') {
                        throw new \RuntimeException(
                            'La demande #'.$id.' n’est pas au statut « approuvé RAF » ou a déjà été traitée.'
                        );
                    }

                    BrouillardCaisse::create([
                        'bus_id' => $demande->bus_id,
                        'type' => 'Sortie',
                        'montant' => $demande->montant,
                        'motif' => '[Demande dépense #'.$demande->id.'] '.$demande->motif,
                        'date_operation' => now()->toDateString(),
                        'solde_cumule' => 0,
                    ]);

                    $demande->update(['statut' => 'validée']);
                    $busIds[(int) $demande->bus_id] = true;
                }

                foreach (array_keys($busIds) as $busId) {
                    BrouillardCaisse::synchroniserSoldesPourBus((int) $busId);
                }
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $n = count($ids);

        return redirect()->back()->with(
            'success',
            $n.' demande(s) enregistrée(s) en caisse : écritures au brouillard sur la BU de chaque demande et soldes mis à jour.'
        );
    }

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
        
        $peutEnregistrerDepensesEnCaisse = in_array($user->role, ['admin', 'caissier', 'controleur_caisse'], true);

        return view('caisse.demandedepenseliste', compact('demandesDepenses', 'peutEnregistrerDepensesEnCaisse'));
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
        // Admin / DG : toutes les demandes en attente responsable + en attente RAF (tout administrateur peut traiter l’étape RAF)
        elseif (in_array($user->role, ['admin', 'dg'])) {
            $demandesEnAttente = DemandeDepense::with(['bu', 'user', 'responsableHierarchique', 'raf'])
                ->whereIn('statut', ['en_attente_responsable', 'approuve_responsable'])
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
        $pdf = Pdf::loadView('caisse.demandedepensepdf', compact('demande', 'bus'));
        
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
        $banques = Banque::where('bu_id', $id_bu)->get();
        
        return view('caisse.approvisionnement', compact('bus', 'banques'));
    }
}
