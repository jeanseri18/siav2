<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ClientFournisseur;
use App\Models\Contrat;
use App\Models\DebourseSec;
use App\Models\DebourseSecParent;
use App\Models\Facture;
use App\Models\FraisChantierParent;
use App\Models\FraisGenerauxParent;
use App\Models\LigneBeneficeParent;
use App\Models\DQE;
use App\Models\Prestation;
use App\Models\Projet;
use App\Models\TypeTravaux;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Support\PdfBranding;

class ContratController extends Controller
{
    // Afficher tous les contrats de tous les projets
    public function allContracts()
    {
        // Récupérer tous les contrats avec leurs relations
        $contrats = Contrat::with(['client', 'projet'])->get();
        
        // Récupérer tous les projets pour le formulaire de création
        $projets = Projet::all();
        
        return view('contrats.all', compact('contrats', 'projets'));
    }

    // Afficher le formulaire de création de contrat (vue séparée)
    public function allCreate()
    {
        // Récupérer tous les projets pour le formulaire
        $projets = Projet::all();
        
        // Récupérer tous les types de travaux
        $typeTravaux = TypeTravaux::all();
        
        return view('contrats.allcreate', compact('projets', 'typeTravaux'));
    }

    // Afficher la liste des contrats
    public function index()
    {
        // Récupérer l'ID du projet depuis la session
        $projet_id = session('projet_id');
        
        if (!$projet_id) {
            return redirect()->route('projets.index')->with('error', 'Aucun projet sélectionné');
        }
        
        // Récupérer les contrats du projet sélectionné
        $contrats = Contrat::where('id_projet', $projet_id)
                          ->with(['client', 'projet', 'chefChantier'])
                          ->get();
        
        // Récupérer tous les projets et articles pour le sublayout projetdetail
        $projets = Projet::all();
        $articles = Article::all();
        
        return view('contrats.index', compact('contrats', 'projets', 'articles'));
    }

    public function exportListePdf()
    {
        $projet_id = session('projet_id');
        if (! $projet_id) {
            return redirect()->route('projets.index')->with('error', 'Aucun projet sélectionné');
        }

        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $contrats = $this->contratsForListing((int) $projet_id);
        $pdfBranding = PdfBranding::forBu($buId);

        $pdf = Pdf::loadView('contrats.liste-export', [
            'contrats' => $contrats,
            'pdfBranding' => $pdfBranding,
            'documentTitle' => 'Liste des contrats',
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('liste-contrats-'.now()->format('Y-m-d').'.pdf', ['Attachment' => false]);
    }

    private function contratsForListing(int $projetId)
    {
        return Contrat::query()
            ->where('id_projet', $projetId)
            ->with(['client', 'projet.bu', 'projet.secteurActivite', 'chefChantier'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    // Afficher le formulaire de création d'un contrat
    public function create()
    {

                // Récupérer l'ID du bus depuis la session
                $id_bu = session('selected_bu');
    
                // Vérifier si l'ID du bus est présent dans la session
                if (!$id_bu) {
                    return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
                }
            
                // Récupérer les clients associés à l'ID du bus
                $clients = ClientFournisseur::where('type', 'client')
                                            ->where('id_bu', $id_bu)  // Filtrage selon l'ID du bus
                                            ->get();
        $projet_id = session('projet_id');
        $projets = Projet::all();
        $articles = Article::all();
        $typeTravaux=TypeTravaux::all();
        $chefsChantier = User::where('role', 'chef_chantier')->get();
        return view('contrats.create', compact('projet_id','clients','projets','articles','typeTravaux','chefsChantier'));
    }

    // Enregistrer un nouveau contrat
    public function store(Request $request)
    {
        $request->validate([
            'nom_contrat' => 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'type_travaux' => 'required',
            'taux_garantie' => 'required|numeric',
            'client_id' => 'required|exists:client_fournisseurs,id',
            'chef_chantier_id' => 'nullable|exists:users,id',
            'montant' => 'nullable|numeric',
            'statut' => 'nullable|in:non débuté,en cours,terminé,annulé',
            'projet_id' => 'nullable|exists:projets,id',
            'tva_18' => 'nullable|boolean',
            'retenue_decennale' => 'nullable|numeric|min:0|max:100',
            'avance_demarrage' => 'nullable|numeric|min:0',
        ]);

        $lastReference = \App\Models\Reference::where('nom', 'Code contrat')
            ->latest('created_at')
            ->first();

        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'Ctr_000';
        $newReference = 'Ctr_' . now()->format('YmdHis');

        // Déterminer l'ID du projet (depuis le formulaire ou la session)
        $projetId = $request->projet_id ?? session('projet_id');
        $projetNom = null;
        
        if ($projetId) {
            $projet = Projet::find($projetId);
            $projetNom = $projet ? $projet->nom_projet : session('projet_nom');
        }

        Contrat::create([
            'ref_contrat' => $newReference,
            'nom_contrat' => $request->nom_contrat,
            'id_projet' => $projetId,
            'nom_projet' => $projetNom,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'type_travaux' => $request->type_travaux,
            'taux_garantie' => $request->taux_garantie,
            'client_id' => $request->client_id,
            'chef_chantier_id' => $request->chef_chantier_id,
            'montant' => $request->montant,
            'statut' => $request->input('statut', 'non débuté'),
            'decompte' => $request->decompte ?? false,
            'tva_18' => $request->tva_18 ?? true,
            'retenue_decennale' => $request->retenue_decennale ?? 0,
            'avance_demarrage' => $request->avance_demarrage ?? 0,
        ]);

        if ($projetId) {
            Projet::find($projetId)?->syncDatesFromContrats();
        }

        // Rediriger selon la source de la création
        if ($request->projet_id) {
            // Création depuis la vue "tous les contrats"
            return redirect()->route('contrats.all')->with('success', 'Contrat créé avec succès!');
        } else {
            // Création depuis la vue projet spécifique
            return redirect()->route('contrats.index')->with('success', 'Contrat créé avec succès!');
        }
    }



    // Afficher le formulaire d'édition d'un contrat
    public function edit($id)
    {
        $id_bu = session('selected_bu');

        if (!$id_bu) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }
    
        // Récupérer les clients associés à l'ID du bus
        $clients = ClientFournisseur::where('type', 'client')
                                    ->where('id_bu', $id_bu)  // Filtrage selon l'ID du bus
                                    ->get();
        $contrat = Contrat::findOrFail($id);
        $projets = Projet::all();
        $articles = Article::all();
        $typeTravaux=TypeTravaux::all();
        $chefsChantier = User::where('role', 'chef_chantier')->get();
        return view('contrats.edit', compact('contrat','clients','projets','articles','typeTravaux','chefsChantier'));
    }

    // Mettre à jour un contrat
    public function update(Request $request, $id)
    {              

        $request->validate([
            'nom_contrat' => 'required',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'type_travaux' => 'required',
            'taux_garantie' => 'required|numeric',
            'client_id' => 'required',
            'chef_chantier_id' => 'nullable|exists:users,id',
            'montant' => 'nullable|numeric',
            'statut' => 'required|in:non débuté,en cours,terminé,annulé',
            'tva_18' => 'nullable|boolean',
            'retenue_decennale' => 'nullable|numeric|min:0|max:100',
            'avance_demarrage' => 'nullable|numeric|min:0',
        ]);
        $contrat = Contrat::findOrFail($id);
        $contrat->update($request->except(['projet_id_hidden']));
        $contrat->projet?->syncDatesFromContrats();

        return redirect()->route('contrats.index')->with('success', 'Contrat mis à jour avec succès!');
    }

    public function updateStatut(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:non débuté,en cours,terminé,annulé',
        ]);

        $contrat = Contrat::findOrFail($id);
        $projetId = $contrat->id_projet;
        $contrat->update(['statut' => $request->statut]);
        if ($projetId) {
            Projet::find($projetId)?->syncDatesFromContrats();
        }

        return redirect()->back()->with('success', 'Statut du contrat mis à jour.');
    }

    // Supprimer un contrat
    public function destroy($id)
    {
        $contrat = Contrat::findOrFail($id);
        $projetId = $contrat->id_projet;
        $contrat->delete();
        if ($projetId) {
            Projet::find($projetId)?->syncDatesFromContrats();
        }

        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé avec succès!');
    }
    public function show($id)
    {
        $contrat = Contrat::with([
            'client.contactPersons',
            'chefChantier',
            'projet.bu',
        ])->findOrFail($id);

        session([
            'contrat_id' => $contrat->id,
            'contrat_nom' => $contrat->nom_contrat,
            'ref_contrat' => $contrat->ref_contrat,
        ]);

        $id_bu = session('selected_bu');

        if (! $id_bu) {
            return redirect()->route('select.bu')
                ->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $clients = ClientFournisseur::where('type', 'client')
            ->where('id_bu', $id_bu)
            ->get();

        $stats = $this->computeContratFinancialStats($contrat);

        return view('contrats.show', compact('contrat', 'clients', 'stats'));
    }

    /**
     * DQE de référence : validé → approuvé → dernier mis à jour.
     */
    private function resolveReferenceDqe(Contrat $contrat): ?DQE
    {
        $d = $contrat->getLastValidatedDQE();
        if ($d) {
            return $d;
        }

        $d = $contrat->dqes()->where('statut', 'approuvé')->orderByDesc('updated_at')->first();
        if ($d) {
            return $d;
        }

        return $contrat->dqes()->orderByDesc('updated_at')->first();
    }

    /**
     * Somme des montants "parent" (DS / FC / FG) pour un DQE, en priorisant les bordereaux validés.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     */
    private function sumParentMontantForDqe(string $model, int $contratId, ?int $dqeId): float
    {
        if (! $dqeId) {
            return 0.0;
        }

        $base = $model::query()->where('contrat_id', $contratId)->where('dqe_id', $dqeId);
        $valide = (clone $base)->where('statut', 'valide')->sum('montant_total');

        if ((float) $valide > 0) {
            return (float) $valide;
        }

        return (float) $base->sum('montant_total');
    }

    private function factureMontantSum(Contrat $contrat, ?string $statut = null): float
    {
        $q = Facture::query()
            ->where('id_contrat', $contrat->id)
            ->where(function ($qq) {
                $qq->whereNull('statut')
                    ->orWhereNotIn('statut', ['annulée', 'annulé']);
            });

        if ($statut !== null) {
            $q->where('statut', $statut);
        }

        return (float) $q->sum('montant_total');
    }

    /**
     * Somme des montants des bordereaux FC de type « réalisé » (saisie Contrat → Frais de chantier).
     */
    private function sumFraisChantierRealiseMontant(Contrat $contrat): float
    {
        return (float) FraisChantierParent::query()
            ->where('contrat_id', $contrat->id)
            ->where(function ($q) {
                $q->where('type', FraisChantierParent::TYPE_REALISE)
                    ->orWhere('type', 'realise');
            })
            ->sum('montant_total');
    }

    /**
     * @return array<string, float|int>
     */
    private function computeContratFinancialStats(Contrat $contrat): array
    {
        $referenceDqe = $this->resolveReferenceDqe($contrat);
        $dqeId = $referenceDqe?->id;

        $montantContrat = (float) ($contrat->montant ?? 0);
        if ($montantContrat <= 0 && $referenceDqe) {
            $montantContrat = (float) ($referenceDqe->montant_total_ttc ?? 0);
            if ($montantContrat <= 0) {
                $ht = (float) ($referenceDqe->montant_total_ht ?? 0);
                if ($ht > 0) {
                    $montantContrat = round($ht * 1.18, 2);
                }
            }
        }

        $dsPrevu = $this->sumParentMontantForDqe(DebourseSecParent::class, $contrat->id, $dqeId);
        if ($dsPrevu <= 0 && $dqeId) {
            $dsPrevu = (float) DebourseSec::where('contrat_id', $contrat->id)
                ->where('dqe_id', $dqeId)
                ->sum('montant_ht');
        }
        if ($dsPrevu <= 0 && $referenceDqe) {
            $dsPrevu = (float) ($referenceDqe->montant_total_ht ?? 0);
        }

        /** FC = frais de chantier (écran Frais de chantier), pas déboursé chantier */
        $fcPrevu = $this->sumParentMontantForDqe(FraisChantierParent::class, $contrat->id, $dqeId);

        $fgPrevu = $this->sumParentMontantForDqe(FraisGenerauxParent::class, $contrat->id, $dqeId);
        if ($fgPrevu <= 0 && ($dsPrevu + $fcPrevu) > 0) {
            $fgPrevu = round(($dsPrevu + $fcPrevu) * 0.15, 2);
        }

        $coutPrevu = $dsPrevu + $fcPrevu + $fgPrevu;

        $caPaye = $this->factureMontantSum($contrat, 'payée');
        $caFacture = $this->factureMontantSum($contrat, null);

        $coutReel = (float) Facture::whereHas('prestation', function ($query) use ($contrat) {
            $query->where('id_contrat', $contrat->id);
        })->where('statut', 'payée')->sum('montant_total');

        if ($coutReel <= 0) {
            $coutReel = $caPaye > 0 ? round($caPaye * 0.7, 2) : 0.0;
        }
        if ($coutReel <= 0 && $caFacture > 0) {
            $coutReel = round($caFacture * 0.65, 2);
        }

        $caRealise = $caPaye > 0 ? $caPaye : $caFacture;

        $dsRealise = $coutReel > 0 ? round($coutReel * 0.7, 2) : 0.0;
        /** FC réalisé : priorité à la saisie « Frais de chantier » (bordereaux type réalisé), sinon reste de l’heuristique 30 % du coût réel */
        $fcRealiseSaisie = $this->sumFraisChantierRealiseMontant($contrat);
        $fcRealiseHeuristique = $coutReel > 0 ? round($coutReel * 0.3, 2) : 0.0;
        $fcRealise = $fcRealiseSaisie > 0 ? $fcRealiseSaisie : $fcRealiseHeuristique;
        $fgRealise = $coutReel > 0 ? round($coutReel * 0.15, 2) : 0.0;

        /** Bénéfice prévisionnel = total saisi dans l’écran Bénéfice (LigneBenefice) */
        $beneficePrevu = $this->sumParentMontantForDqe(LigneBeneficeParent::class, $contrat->id, $dqeId);
        $beneficeRealise = $caRealise - $coutReel;

        return [
            'Montant du contrat' => $montantContrat,
            'Coût de revient Prév.' => $coutPrevu,
            'Coût de revient Réel' => $coutReel,
            'Écart' => $coutPrevu - $coutReel,
            'DS Prévisionnel' => $dsPrevu,
            'FC Prévisionnel' => $fcPrevu,
            'FG Prévisionnel' => $fgPrevu,
            'DS Réalisé' => $dsRealise,
            'FC Réalisé' => $fcRealise,
            'FG Réalisé' => $fgRealise,
            'CA Réalisé' => $caRealise,
            'Bénéfice Prévisionnel' => $beneficePrevu,
            'Bénéfice Réalisé' => $beneficeRealise,
        ];
    }

    // Dupliquer un contrat
    public function duplicate($id)
    {
        $contrat = Contrat::findOrFail($id);
        
        // Générer une nouvelle référence
        $newReference = 'Ctr_' . now()->format('YmdHis');
        
        // Créer une copie du contrat
        $newContrat = $contrat->replicate();
        $newContrat->ref_contrat = $newReference;
        $newContrat->nom_contrat = $contrat->nom_contrat . ' (Copie)';
        $newContrat->statut = 'non débuté'; // Réinitialiser le statut
        $newContrat->save();
        if ($newContrat->id_projet) {
            Projet::find($newContrat->id_projet)?->syncDatesFromContrats();
        }
        
        return redirect()->route('contrats.show', $newContrat->id)
            ->with('success', 'Contrat dupliqué avec succès!');
    }

    // API pour récupérer les clients selon le projet
    public function getClientsByProject($projetId)
    {
        // Récupérer le projet avec son client
        $projet = Projet::with('clientFournisseur')->find($projetId);
        
        if (!$projet) {
            return response()->json(['error' => 'Projet non trouvé'], 404);
        }
        
        // Si le projet a un client associé, le retourner directement
        if ($projet->clientFournisseur) {
            $clients = collect([$projet->clientFournisseur->only(['id', 'nom_raison_sociale', 'prenoms'])]);
        } else {
            // Sinon, récupérer tous les clients du BU comme fallback
            $id_bu = session('selected_bu');
            
            if (!$id_bu) {
                return response()->json(['error' => 'Aucun BU sélectionné'], 400);
            }
            
            $clients = ClientFournisseur::where('type', 'client')
                                        ->where('id_bu', $id_bu)
                                        ->select('id', 'nom_raison_sociale', 'prenoms')
                                        ->get();
        }
        
        return response()->json($clients);
    }
}
