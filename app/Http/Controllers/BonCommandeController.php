<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\ClientFournisseur;
use App\Models\DemandeApprovisionnement;
use App\Models\DemandeAchat;
use App\Models\DemandeCotation;
use App\Models\FournisseurDemandeCotation;
use App\Models\Projet;
use App\Models\Article;
use App\Models\Reference;
use App\Models\ModePaiement;
use App\Models\Monnaie;
use App\Http\Controllers\Concerns\ExportsListPdf;
use App\Support\PdfBranding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class BonCommandeController extends Controller
{
    use ExportsListPdf;

    public function index()
    {
        $bonCommandes = BonCommande::with(['fournisseur', 'user', 'demandeApprovisionnement', 'demandeAchat'])->get();
        return view('bon_commandes.index', compact('bonCommandes'));
    }

    public function exportListePdf()
    {
        $bonCommandes = BonCommande::with('fournisseur')
            ->orderByDesc('date_commande')
            ->get();

        $rows = [];
        foreach ($bonCommandes as $bonCommande) {
            $rows[] = [
                $bonCommande->reference,
                $bonCommande->date_commande?->format('d/m/Y') ?? '—',
                $bonCommande->fournisseur?->nom_raison_sociale ?? '—',
                number_format((float) ($bonCommande->montant_total ?? 0), 0, ',', ' ').' FCFA',
                $bonCommande->statut ?? '—',
            ];
        }

        return $this->streamListPdf(
            'Liste des bons de commande',
            ['Référence', 'Date', 'Fournisseur', 'Montant', 'Statut'],
            $rows,
            'liste-bons-commande'
        );
    }

    public function create(Request $request)
    {
        if ($request->filled('demande_approvisionnement_id')) {
            if (DemandeAchat::where('demande_approvisionnement_id', $request->demande_approvisionnement_id)->exists()) {
                return redirect()->route('demande-approvisionnements.show', $request->demande_approvisionnement_id)
                    ->with('error', 'Une demande d\'achat existe déjà pour cette demande d\'approvisionnement. Le bon de commande se fait après la cotation, à partir de la demande d\'achat ou de la demande de cotation.');
            }
        }

        if ($request->filled('demande_achat_id')) {
            $da = DemandeAchat::find($request->demande_achat_id);
            if ($da && !$da->peutAssocierNouveauBonCommande()) {
                return redirect()->route('demande-achats.show', $da)
                    ->with('error', 'Un bon de commande actif existe déjà pour cette demande d\'achat (créé ou validé). Création d\'un second bon impossible tant qu\'il n\'est pas annulé.');
            }
        }

        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $fournisseursPaiementMap = $fournisseurs->mapWithKeys(function (ClientFournisseur $f) {
            return [
                (string) $f->id => [
                    'mode_paiement' => $f->mode_paiement,
                    'delai_reglement' => $this->normalizeDelaiPourSelect($f->delai_paiement),
                ],
            ];
        });
        // Inclure les DAP sans DA, ou avec au moins une DA approuvée (sinon la DAP disparaît du <select>
        // alors que le flux DC → DA la référence encore, et le préremplissage JS ne peut pas s’afficher).
        $demandesAppro = DemandeApprovisionnement::where('statut', 'approuvée')
            ->where(function ($q) {
                $q->whereDoesntHave('demandeAchats')
                    ->orWhereHas('demandeAchats', function ($q2) {
                        $q2->where('statut', 'approuvée');
                    });
            })
            ->get();
        $demandesAchat = DemandeAchat::where('statut', 'approuvée')
            ->whereDoesntHave('bonCommandes', function ($q) {
                $q->where('statut', '!=', 'annulée');
            })
            ->get();
        $demandesCotation = DemandeCotation::with('demandeAchat')
            ->eligiblePourBonCommande()
            ->whereDoesntHave('bonCommandes')
            ->get();
        $projets = Projet::all();
        $modesPaiement = ModePaiement::all();
        $articles = Article::with('uniteMesure')->get();
        
        return view('bon_commandes.create', compact('fournisseurs', 'fournisseursPaiementMap', 'demandesAppro', 'demandesAchat', 'demandesCotation', 'projets', 'modesPaiement', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id' => 'required|exists:client_fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
            'mode_reglement' => 'nullable|string',
            'delai_reglement' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'prix_unitaire' => 'required|array',
            'prix_unitaire.*' => 'numeric|min:0'
        ]);

        if ($request->filled('demande_cotation_id')) {
            if (BonCommande::where('demande_cotation_id', $request->demande_cotation_id)->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Un bon de commande existe déjà pour cette demande de cotation.');
            }
        }

        if ($request->filled('demande_achat_id')) {
            $da = DemandeAchat::find($request->demande_achat_id);
            if ($da && !$da->peutAssocierNouveauBonCommande()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Un bon de commande actif existe déjà pour cette demande d\'achat. Impossible d\'en créer un second tant que le précédent n\'est pas annulé.');
            }
        }

        if ($request->filled('demande_approvisionnement_id')) {
            if (DemandeAchat::where('demande_approvisionnement_id', $request->demande_approvisionnement_id)->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Une demande d\'achat existe déjà pour cette demande d\'approvisionnement. Le bon de commande ne peut pas être créé directement depuis la demande d\'approvisionnement.');
            }
        }

        // Générer la référence
        $lastReference = Reference::where('nom', 'Code bon de commande')
            ->latest('created_at')
            ->first();
        
        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'PO_0000';
        $newReference = 'PO_' . now()->format('YmdHis');

        // Calculer le montant total avec remises
        $montantTotal = 0;
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                $montantBrut = $request->quantite[$i] * $request->prix_unitaire[$i];
                $remise = $request->remise[$i] ?? 0;
                $montantRemise = $montantBrut * ($remise / 100);
                $montantTotal += $montantBrut - $montantRemise;
            }
        }

        // Créer le bon de commande
        $bonCommande = BonCommande::create([
            'reference' => $newReference,
            'date_commande' => $request->date_commande,
            'fournisseur_id' => $request->fournisseur_id,
            'demande_approvisionnement_id' => $request->demande_approvisionnement_id,
            'demande_achat_id' => $request->demande_achat_id,
            'projet_id' => $request->projet_id,
            'demande_cotation_id' => $request->demande_cotation_id,
            'user_id' => Auth::id(),
            'montant_total' => $montantTotal,
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'conditions_paiement' => $request->conditions_paiement,
            'notes' => $request->notes,
            'mode_reglement' => $request->mode_reglement,
            'delai_reglement' => $request->delai_reglement,
            'lieu_livraison' => $request->lieu_livraison,
            'statut' => 'en attente'
        ]);

        // Ajouter les lignes du bon de commande
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                LigneBonCommande::create([
                    'bon_commande_id' => $bonCommande->id,
                    'article_id' => $request->article_id[$i],
                    'quantite' => $request->quantite[$i],
                    'prix_unitaire' => $request->prix_unitaire[$i],
                    'remise' => $request->remise[$i] ?? 0,
                    'quantite_livree' => 0,
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        // Enregistrer la référence
        Reference::create([
            'nom' => 'Code Bon Commande',
            'ref' => $newReference
        ]);

        return redirect()->route('bon-commandes.index')
            ->with('success', 'Bon de commande créé avec succès');
    }

    public function show(BonCommande $bonCommande)
    {
        $bonCommande->load(['fournisseur', 'user', 'demandeApprovisionnement', 'demandeAchat', 'lignes.article.uniteMesure']);
        return view('bon_commandes.show', compact('bonCommande'));
    }

    public function edit(BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Impossible de modifier un bon de commande qui n\'est pas en attente');
        }

        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $demandesAppro = DemandeApprovisionnement::where('statut', 'approuvée')
            ->where(function ($q) use ($bonCommande) {
                $q->whereDoesntHave('demandeAchats');
                if ($bonCommande->demande_approvisionnement_id) {
                    $q->orWhere('id', $bonCommande->demande_approvisionnement_id);
                }
            })
            ->get();
        $demandesAchat = DemandeAchat::where('statut', 'approuvée')
            ->whereDoesntHave('bonCommandes', function ($q2) use ($bonCommande) {
                $q2->where('statut', '!=', 'annulée')
                    ->where('bon_commandes.id', '!=', $bonCommande->id);
            })
            ->get();
        $demandesCotation = DemandeCotation::with('demandeAchat')
            ->eligiblePourBonCommande()
            ->where(function ($q) use ($bonCommande) {
                $q->whereDoesntHave('bonCommandes');
                if ($bonCommande->demande_cotation_id) {
                    $q->orWhere('id', $bonCommande->demande_cotation_id);
                }
            })
            ->get();
        $projets = Projet::all();
        $modesPaiement = ModePaiement::all();
        $articles = Article::with('uniteMesure')->get();
        
        $bonCommande->load(['lignes.article']);
        
        return view('bon_commandes.edit', compact('bonCommande', 'fournisseurs', 'demandesAppro', 'demandesAchat', 'demandesCotation', 'projets', 'modesPaiement', 'articles'));
    }

    public function update(Request $request, BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Impossible de modifier un bon de commande qui n\'est pas en attente');
        }

        $request->validate([
            'fournisseur_id' => 'required|exists:client_fournisseurs,id',
            'date_commande' => 'required|date',
            'date_livraison_prevue' => 'nullable|date|after_or_equal:date_commande',
            'conditions_paiement' => 'nullable|string',
            'notes' => 'nullable|string',
            'mode_reglement' => 'nullable|string',
            'delai_reglement' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'prix_unitaire' => 'required|array',
            'prix_unitaire.*' => 'numeric|min:0'
        ]);

        if ($request->filled('demande_approvisionnement_id')) {
            $dapId = (int) $request->demande_approvisionnement_id;
            if (DemandeAchat::where('demande_approvisionnement_id', $dapId)->exists()) {
                $currentId = $bonCommande->demande_approvisionnement_id ? (int) $bonCommande->demande_approvisionnement_id : null;
                if ($currentId !== $dapId) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Cette demande d\'approvisionnement a déjà une demande d\'achat : le bon de commande doit suivre le circuit achat / cotation.');
                }
            }
        }

        if ($request->filled('demande_achat_id')) {
            $daId = (int) $request->demande_achat_id;
            $conflit = BonCommande::where('demande_achat_id', $daId)
                ->where('statut', '!=', 'annulée')
                ->where('id', '!=', $bonCommande->id)
                ->exists();
            if ($conflit) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Cette demande d\'achat a déjà un autre bon de commande actif.');
            }
        }

        // Calculer le montant total avec remises
        $montantTotal = 0;
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                $montantBrut = $request->quantite[$i] * $request->prix_unitaire[$i];
                $remise = $request->remise[$i] ?? 0;
                $montantRemise = $montantBrut * ($remise / 100);
                $montantTotal += $montantBrut - $montantRemise;
            }
        }

        // Mettre à jour le bon de commande
        $bonCommande->update([
            'date_commande' => $request->date_commande,
            'fournisseur_id' => $request->fournisseur_id,
            'demande_approvisionnement_id' => $request->demande_approvisionnement_id,
            'demande_achat_id' => $request->demande_achat_id,
            'projet_id' => $request->projet_id,
            'demande_cotation_id' => $request->demande_cotation_id,
            'montant_total' => $montantTotal,
            'date_livraison_prevue' => $request->date_livraison_prevue,
            'conditions_paiement' => $request->conditions_paiement,
            'notes' => $request->notes,
            'mode_reglement' => $request->mode_reglement,
            'delai_reglement' => $request->delai_reglement,
            'lieu_livraison' => $request->lieu_livraison
        ]);

        // Supprimer les anciennes lignes
        $bonCommande->lignes()->delete();

        // Ajouter les nouvelles lignes
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite[$i] > 0 && $request->prix_unitaire[$i] > 0) {
                LigneBonCommande::create([
                    'bon_commande_id' => $bonCommande->id,
                    'article_id' => $request->article_id[$i],
                    'quantite' => $request->quantite[$i],
                    'prix_unitaire' => $request->prix_unitaire[$i],
                    'remise' => $request->remise[$i] ?? 0,
                    'quantite_livree' => 0,
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande mis à jour avec succès');
    }

    public function destroy(BonCommande $bonCommande)
    {
        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.index')
                ->with('error', 'Impossible de supprimer un bon de commande qui n\'est pas en attente');
        }

        // Supprimer les lignes du bon de commande
        $bonCommande->lignes()->delete();
        
        // Supprimer le bon de commande
        $bonCommande->delete();

        return redirect()->route('bon-commandes.index')
            ->with('success', 'Bon de commande supprimé avec succès');
    }

    public function confirm(Request $request, BonCommande $bonCommande)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour confirmer ce bon de commande.');
        }

        if ($bonCommande->statut !== 'en attente') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Ce bon de commande ne peut pas être confirmé');
        }

        // Mettre à jour le statut du bon de commande
        $bonCommande->update([
            'statut' => 'confirmée'
        ]);

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande confirmé avec succès');
    }

    public function cancel(Request $request, BonCommande $bonCommande)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour annuler ce bon de commande.');
        }

        if ($bonCommande->statut === 'livrée') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Impossible d\'annuler un bon de commande déjà livré');
        }

        // Mettre à jour le statut du bon de commande
        $bonCommande->update([
            'statut' => 'annulée'
        ]);

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande annulé avec succès');
    }

    public function livrer(Request $request, BonCommande $bonCommande)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['magasinier', 'chef_chantier', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour marquer ce bon de commande comme livré.');
        }

        if ($bonCommande->statut !== 'confirmée') {
            return redirect()->route('bon-commandes.show', $bonCommande)
                ->with('error', 'Ce bon de commande ne peut pas être marqué comme livré');
        }

        $request->validate([
            'quantite_livree' => 'required|array',
            'quantite_livree.*' => 'integer|min:0'
        ]);

        // Mettre à jour les quantités livrées
        foreach ($bonCommande->lignes as $index => $ligne) {
            $ligne->update([
                'quantite_livree' => $request->quantite_livree[$index]
            ]);

            // Mettre à jour le stock
            $article = $ligne->article;
            $article->update([
                'quantite_stock' => $article->quantite_stock + $request->quantite_livree[$index]
            ]);
        }

        // Mettre à jour le statut du bon de commande
        $bonCommande->update([
            'statut' => 'livrée'
        ]);

        return redirect()->route('bon-commandes.show', $bonCommande)
            ->with('success', 'Bon de commande marqué comme livré avec succès');
    }

    /**
     * Récupérer la demande d'achat liée à une demande de cotation
     */
    public function getDemandeAchatFromCotation($demandeCotationId)
    {
        $demandeCotation = DemandeCotation::with([
            'demandeAchat.demandeApprovisionnement.projet',
            'demandeAchat.projet',
            'lignes.article.uniteMesure',
            'fournisseurs.fournisseur',
        ])->eligiblePourBonCommande()->find($demandeCotationId);

        $response = ['success' => false];

        if (! $demandeCotation) {
            $response['message'] = 'Demande de cotation introuvable ou non éligible pour un bon de commande (statut validée / terminée, ou fournisseur retenu avec réponse enregistrée).';

            return response()->json($response);
        }

        $response['success'] = true;
        $response['demande_cotation_reference'] = $demandeCotation->reference;

        if ($demandeCotation->demandeAchat) {
            $da = $demandeCotation->demandeAchat;
            $response['demande_achat_id'] = $da->id;
            $response['demande_achat_reference'] = $da->reference;

            if ($da->demandeApprovisionnement) {
                $response['demande_approvisionnement_id'] = $da->demandeApprovisionnement->id;
                $response['demande_approvisionnement_reference'] = $da->demandeApprovisionnement->reference;
            }

            $projet = $da->projet ?? $da->demandeApprovisionnement?->projet;
            if ($projet) {
                $response['projet_id'] = $projet->id;
                $response['projet_nom'] = $projet->nom_projet;
                $response['lieu_livraison'] = $this->formatLieuLivraisonDepuisProjet($projet);
            }
        } else {
            $response['message'] = 'Aucune demande d\'achat liée à cette cotation';
        }

        $fournisseurRetenu = $this->resolveFournisseurPourBonCommande($demandeCotation);

        if ($fournisseurRetenu && $fournisseurRetenu->fournisseur) {
            $frs = $fournisseurRetenu->fournisseur;
            $response['fournisseur_id'] = $fournisseurRetenu->fournisseur_id;
            $response['fournisseur'] = [
                'id' => $frs->id,
                'nom' => $frs->nom_raison_sociale,
                'prenoms' => $frs->prenoms,
                'mode_paiement' => $frs->mode_paiement,
                'delai_paiement' => $frs->delai_paiement,
                'delai_reglement' => $this->normalizeDelaiPourSelect($frs->delai_paiement),
            ];
        }

        $response['lignes_articles'] = $demandeCotation->lignes->map(function ($ligne) {
            $article = $ligne->article;
            $pu = ($article && $article->prix_unitaire !== null) ? (float) $article->prix_unitaire : 0.0;

            return [
                'article_id' => $ligne->article_id,
                'quantite' => (float) $ligne->quantite,
                'prix_unitaire' => $pu,
                'remise' => 0,
                'commentaire' => trim((string) ($ligne->specifications ?: ($ligne->designation ?? ''))),
            ];
        })->values()->all();

        return response()->json($response);
    }

    /**
     * Localisation textuelle du projet (secteur, quartier, commune, ville, pays).
     */
    protected function formatLieuLivraisonDepuisProjet(Projet $projet): ?string
    {
        $projet->loadMissing([
            'pays',
            'ville',
            'commune',
            'quartier',
            'secteurLocalisation',
        ]);

        $parts = array_filter([
            $projet->secteurLocalisation?->nom,
            $projet->quartier?->nom,
            $projet->commune?->nom,
            $projet->ville?->nom,
            $projet->pays?->nom,
        ]);

        return $parts !== [] ? implode(', ', $parts) : null;
    }

    /**
     * Aligne la valeur fiche fournisseur sur les options du select (0, 15, 30…).
     */
    protected function normalizeDelaiPourSelect(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $v = trim((string) $value);
        if (preg_match('/^(\d+)/', $v, $m)) {
            $days = (int) $m[1];
            if (in_array($days, [0, 15, 30, 45, 60, 90], true)) {
                return (string) $days;
            }
        }
        if (stripos($v, 'comptant') !== false) {
            return '0';
        }

        return null;
    }

    /**
     * Fournisseur marqué « retenu », sinon meilleure offre parmi les fournisseurs ayant répondu (cas « terminer » sans clic explicite).
     */
    protected function resolveFournisseurPourBonCommande(DemandeCotation $demandeCotation): ?FournisseurDemandeCotation
    {
        $lignes = $demandeCotation->fournisseurs;

        $explicit = $lignes->first(fn (FournisseurDemandeCotation $f) => (bool) $f->retenu);
        if ($explicit) {
            return $explicit;
        }

        $repondu = $lignes->where('repondu', true);
        if ($repondu->isEmpty()) {
            return null;
        }

        return $repondu->sortBy(function (FournisseurDemandeCotation $f) {
            $m = $f->montant_total;

            return $m === null ? PHP_FLOAT_MAX : (float) $m;
        })->first();
    }

    /**
     * Exporter un bon de commande en PDF
     */
    public function exportPDF($id)
    {
        $bonCommande = BonCommande::with([
            'fournisseur',
            'user',
            'lignes.article.uniteMesure',
            'demandeApprovisionnement.projet',
            'demandeAchat.projet',
            'demandeCotation',
            'projet.chefProjet',
            'projet.contrats',
        ])->findOrFail($id);

        $buId = PdfBranding::resolveBuIdForBonCommande($bonCommande);
        $pdfBranding = PdfBranding::forBu($buId);
        $configGlobal = $pdfBranding['config'];
        $bc = $this->buildBonCommandePdfData($bonCommande, $pdfBranding);

        $pdf = PDF::loadView('bon_commandes.bon_commande_pdf', compact(
            'bonCommande',
            'configGlobal',
            'pdfBranding',
            'bc'
        ))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('Bon_Commande_' . $bc['numero_po'] . '.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBonCommandePdfData(BonCommande $bonCommande, array $pdfBranding): array
    {
        $lignes = [];
        $totalHtBrut = 0.0;
        $totalRemise = 0.0;
        $totalHtNet = 0.0;
        $index = 1;

        foreach ($bonCommande->lignes as $ligne) {
            $montantBrut = (float) $ligne->quantite * (float) $ligne->prix_unitaire;
            $montantRemise = $montantBrut * (float) ($ligne->remise ?? 0) / 100;
            $montantHt = $montantBrut - $montantRemise;

            $lignes[] = [
                'numero_ligne' => str_pad((string) $index++, 3, '0', STR_PAD_LEFT),
                'ref_article' => $ligne->article?->reference ?? '—',
                'designation' => $ligne->article?->nom ?? ('Article #' . $ligne->article_id),
                'unite' => $ligne->article?->uniteMesure?->ref
                    ?? $ligne->article?->uniteMesure?->nom
                    ?? '—',
                'quantite' => (float) $ligne->quantite,
                'prix_unitaire' => (float) $ligne->prix_unitaire,
                'remise' => (float) ($ligne->remise ?? 0),
                'montant_ht' => $montantHt,
            ];

            $totalHtBrut += $montantBrut;
            $totalRemise += $montantRemise;
            $totalHtNet += $montantHt;
        }

        if ($totalHtNet <= 0) {
            $totalHtNet = (float) ($bonCommande->montant_total ?? 0);
            $totalHtBrut = $totalHtNet;
        }

        $projet = $bonCommande->projet;
        $tauxTva = ($projet && $projet->tva_achat) ? 18 : 0;
        if ($tauxTva === 0 && $projet?->contrats?->isNotEmpty()) {
            $contrat = $projet->contrats->first();
            $tauxTva = ($contrat->tva_18 ?? false) ? 18 : 0;
        }

        $tva = $totalHtNet * $tauxTva / 100;
        $totalTtc = $totalHtNet + $tva;

        $ref = strtoupper((string) ($bonCommande->reference ?? ''));
        $numeroPo = str_starts_with($ref, 'PO')
            ? $bonCommande->reference
            : 'PO' . str_pad((string) $bonCommande->id, 7, '0', STR_PAD_LEFT);

        $fournisseur = $bonCommande->fournisseur;
        $company = $pdfBranding['company'] ?? [];
        $adresseSia = trim(collect([
            $company['localisation'] ?? null,
            $company['adresse_postale'] ?? null,
        ])->filter()->implode(' - '));

        $docExterne = $bonCommande->demandeCotation?->reference
            ?? $bonCommande->notes
            ?? '';

        $conditionsPaiement = $bonCommande->mode_reglement
            ?: ($bonCommande->conditions_paiement
            ?: ($fournisseur?->mode_paiement ?? '—'));

        if (filled($bonCommande->delai_reglement)) {
            $conditionsPaiement = trim($conditionsPaiement . ' — ' . $bonCommande->delai_reglement);
        }

        $monnaie = Monnaie::query()->whereIn('sigle', ['XOF', 'FCFA'])->first()
            ?? Monnaie::query()->first();
        $devise = $monnaie?->sigle ?? '—';
        $deviseLibelle = $monnaie?->nom ?? '';

        $contratRef = $projet?->contrats?->first()?->ref_contrat
            ?? $projet?->ref_projet
            ?? '';

        $responsableContrat = $bonCommande->user?->nom_complet
            ?: trim(($projet?->chefProjet?->prenom ?? '') . ' ' . ($projet?->chefProjet?->nom ?? ''))
            ?: '—';

        $horairesOuverture = filled($pdfBranding['config']?->horaires_ouverture)
            ? $pdfBranding['config']->horaires_ouverture
            : (filled($company['horaires_ouverture'] ?? null)
                ? $company['horaires_ouverture']
                : '8 :00 – 17 :00, tous les jours du lundi au vendredi');

        return [
            'numero_po' => $numeroPo,
            'date_document' => $bonCommande->date_commande?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'nom_projet' => $projet?->nom_projet ?? '—',
            'doc_externe' => $docExterne,
            'conditions_paiement' => $conditionsPaiement,
            'devise' => $devise,
            'devise_libelle' => $deviseLibelle,
            'contrat_ref' => $contratRef,
            'responsable_contrat' => $responsableContrat,
            'lieu_livraison' => $bonCommande->lieu_livraison ?? '—',
            'adresse_sia' => $adresseSia ?: '—',
            'horaires_ouverture' => $horairesOuverture,
            'nom_entreprise' => $pdfBranding['nom_entreprise'],
            'email_entreprise' => $company['email'] ?? '',
            'company' => $company,
            'fournisseur_nom' => $fournisseur?->nom_raison_sociale ?? '—',
            'fournisseur_adresse' => trim(($fournisseur?->adresse_localisation ?? '')
                . ($fournisseur?->boite_postale ? "\n" . $fournisseur->boite_postale : '')),
            'fournisseur_tel' => $fournisseur?->telephone ?? '',
            'fournisseur_email' => $fournisseur?->email ?? '',
            'lignes' => $lignes,
            'total_ht_brut' => $totalHtBrut,
            'total_remise' => $totalRemise,
            'total_ht_net' => $totalHtNet,
            'taux_tva' => $tauxTva,
            'tva' => $tva,
            'total_ttc' => $totalTtc,
            'montant_lettres' => trim($this->montantEnLettres($totalHtNet)
                . ($deviseLibelle !== '' ? ' ' . $deviseLibelle : '')),
            'date_visa' => $bonCommande->date_commande?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'visa_financier' => $bonCommande->user?->nom_complet ?? '',
            'visa_dg' => $projet?->chefProjet
                ? trim(($projet->chefProjet->prenom ?? '') . ' ' . ($projet->chefProjet->nom ?? ''))
                : '',
        ];
    }

    private function montantEnLettres(float $montant): string
    {
        $n = (int) round($montant);
        if ($n === 0) {
            return 'Zéro';
        }

        $ones = ['', 'Un', 'Deux', 'Trois', 'Quatre', 'Cinq', 'Six', 'Sept', 'Huit', 'Neuf'];
        $tens = ['', 'Dix', 'Vingt', 'Trente', 'Quarante', 'Cinquante', 'Soixante', 'Soixante-dix', 'Quatre-vingt', 'Quatre-vingt-dix'];

        $convert = function (int $number) use (&$convert, $ones, $tens): string {
            if ($number === 0) {
                return '';
            }
            if ($number >= 1000000) {
                $m = intdiv($number, 1000000);
                $rest = $number % 1000000;
                $word = ($m === 1 ? 'Un Million' : $convert($m) . ' Millions');

                return trim($word . ($rest ? ' ' . $convert($rest) : ''));
            }
            if ($number >= 1000) {
                $t = intdiv($number, 1000);
                $rest = $number % 1000;
                $word = ($t === 1 ? 'Mille' : $convert($t) . ' Mille');

                return trim($word . ($rest ? ' ' . $convert($rest) : ''));
            }
            if ($number >= 100) {
                $h = intdiv($number, 100);
                $rest = $number % 100;
                $word = ($h === 1 ? 'Cent' : $ones[$h] . ' Cent');

                return trim($word . ($rest ? ' ' . $convert($rest) : ''));
            }
            if ($number >= 20) {
                $ten = intdiv($number, 10);
                $one = $number % 10;

                return $one > 0 ? $tens[$ten] . '-' . $ones[$one] : $tens[$ten];
            }
            if ($number >= 10) {
                return match ($number) {
                    10 => 'Dix', 11 => 'Onze', 12 => 'Douze', 13 => 'Treize',
                    14 => 'Quatorze', 15 => 'Quinze', 16 => 'Seize',
                    default => 'Dix-' . $ones[$number - 10],
                };
            }

            return $ones[$number];
        };

        return $convert($n);
    }
}