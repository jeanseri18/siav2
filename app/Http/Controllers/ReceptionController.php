<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\Article;
use App\Models\StockProjet;
use App\Models\Reception;
use App\Models\LigneReception;
use App\Http\Controllers\Concerns\ExportsListPdf;
use App\Support\PdfBranding;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    use ExportsListPdf;

    public function index()
    {
        // BC encore réceptionnables : au moins une ligne avec quantité restante, hors brouillon / annulé
        $bonCommandes = BonCommande::with(['lignes.article', 'fournisseur', 'projet'])
            ->whereNotIn('statut', ['en attente', 'annulée'])
            ->whereHas('lignes', function ($q) {
                $q->whereColumn('quantite_recue', '<', 'quantite');
            })
            ->orderBy('date_commande', 'desc')
            ->paginate(10);

        $receptions = Reception::with(['bonCommande.fournisseur', 'bonCommande.projet', 'user', 'lignes.article.uniteMesure'])
            ->orderBy('date_reception', 'desc')
            ->paginate(15, ['*'], 'rec_page');

        return view('receptions.index', compact('bonCommandes', 'receptions'));
    }

    public function exportListePdf()
    {
        $receptions = Reception::with(['bonCommande.fournisseur', 'lignes'])
            ->orderByDesc('date_reception')
            ->get();

        $rows = [];
        foreach ($receptions as $reception) {
            $quantiteTotale = $reception->quantite_totale_recue ?? $reception->lignes->sum('quantite_recue');
            $rows[] = [
                $reception->numero_reception,
                $reception->bonCommande?->reference ?? '—',
                $reception->bonCommande?->fournisseur?->nom_raison_sociale ?? '—',
                $reception->date_reception?->format('d/m/Y') ?? '—',
                $reception->statut ?? '—',
                (string) $quantiteTotale,
            ];
        }

        return $this->streamListPdf(
            'Liste des réceptions',
            ['N° réception', 'Bon de commande', 'Fournisseur', 'Date', 'Statut', 'Qté reçue'],
            $rows,
            'liste-receptions'
        );
    }

    public function show($id)
    {
        // Si l'ID correspond à une réception, afficher la réception
        $reception = Reception::with(['bonCommande.lignes.article', 'bonCommande.fournisseur', 'bonCommande.projet', 'lignes.article.uniteMesure', 'user'])
            ->find($id);
            
        if ($reception) {
            return view('receptions.show_reception', compact('reception'));
        }
        
        // Sinon, traiter comme un bon de commande
        $bonCommande = BonCommande::with(['lignes.article', 'fournisseur', 'projet'])
            ->findOrFail($id);
        
        return view('receptions.show', compact('bonCommande'));
    }

    public function create(BonCommande $bonCommande = null)
    {
        if (!$bonCommande) {
            return redirect()->route('receptions.index')
                ->with('error', 'Veuillez sélectionner un bon de commande pour effectuer une réception.');
        }
        
        $bonCommande->load(['lignes.article.uniteMesure', 'fournisseur', 'projet']);
        
        // Vérifier si toutes les lignes sont déjà complètement reçues
        $toutesLignesRecues = $bonCommande->lignes->every(function($ligne) {
            return $ligne->quantite_recue >= $ligne->quantite;
        });
        
        if ($toutesLignesRecues) {
            return redirect()->route('receptions.show', $bonCommande->id)
                ->with('info', 'Toutes les lignes de ce bon de commande ont déjà été reçues.');
        }

        $lignesEnAttente = $bonCommande->lignes->filter(function ($ligne) {
            return (float) $ligne->quantite_recue < (float) $ligne->quantite;
        })->values();

        $isNonConformite = false;

        return view('receptions.create', compact('bonCommande', 'lignesEnAttente', 'isNonConformite'));
    }

    /**
     * Formulaire « Signaler une non-conformité » : même principe que la réception, avec répartition conforme / non conforme.
     */
    public function createNonConformite(BonCommande $bonCommande)
    {
        if (! $bonCommande) {
            return redirect()->route('receptions.index')
                ->with('error', 'Bon de commande introuvable.');
        }

        $bonCommande->load(['lignes.article.uniteMesure', 'fournisseur', 'projet']);

        $toutesLignesRecues = $bonCommande->lignes->every(function ($ligne) {
            return (float) $ligne->quantite_recue >= (float) $ligne->quantite;
        });

        if ($toutesLignesRecues) {
            return redirect()->route('receptions.show', $bonCommande->id)
                ->with('info', 'Toutes les lignes de ce bon de commande ont déjà été reçues.');
        }

        $lignesEnAttente = $bonCommande->lignes->filter(function ($ligne) {
            return (float) $ligne->quantite_recue < (float) $ligne->quantite;
        })->values();

        $isNonConformite = true;

        return view('receptions.create', compact('bonCommande', 'lignesEnAttente', 'isNonConformite'));
    }

    public function store(Request $request, BonCommande $bonCommande)
    {
        $request->validate([
            'date_reception' => 'required|date',
            'numero_bon_livraison' => 'nullable|string|max:255',
            'transporteur' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'quantites' => 'nullable|array',
            'quantites.*' => 'nullable|numeric|min:0',
        ]);

        try {
            $quantitesAPrendre = $this->parseQuantitesSelectionnees($request, $bonCommande);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        if ($quantitesAPrendre === []) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cochez au moins une ligne et indiquez une quantité à réceptionner supérieure à zéro.');
        }

        if (in_array($bonCommande->statut, ['en attente', 'annulée'], true)) {
            return redirect()->route('receptions.index')
                ->with('error', 'Ce bon de commande ne peut pas être réceptionné (statut invalide).');
        }

        return $this->commitReception($request, $bonCommande, $quantitesAPrendre, [], 'Réception effectuée avec succès.');
    }

    public function storeNonConformite(Request $request, BonCommande $bonCommande)
    {
        $request->validate([
            'date_reception' => 'required|date',
            'numero_bon_livraison' => 'nullable|string|max:255',
            'transporteur' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'quantites' => 'nullable|array',
            'quantites.*' => 'nullable|numeric|min:0',
            'non_conformes' => 'nullable|array',
            'non_conformes.*' => 'nullable|numeric|min:0',
        ]);

        try {
            $quantitesAPrendre = $this->parseQuantitesSelectionnees($request, $bonCommande);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        if ($quantitesAPrendre === []) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cochez au moins une ligne et indiquez une quantité à enregistrer supérieure à zéro.');
        }

        if (in_array($bonCommande->statut, ['en attente', 'annulée'], true)) {
            return redirect()->route('receptions.index')
                ->with('error', 'Ce bon de commande ne peut pas être traité (statut invalide).');
        }

        $nonConformesParLigne = [];
        $totalNc = 0.0;
        foreach ($quantitesAPrendre as $ligneId => $qty) {
            $nc = (float) ($request->input('non_conformes.'.$ligneId, 0));
            if ($nc < 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Les quantités non conformes ne peuvent pas être négatives.');
            }
            if ($nc > $qty + 0.000001) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'La quantité non conforme ne peut pas dépasser la quantité enregistrée pour une ligne.');
            }
            $nonConformesParLigne[$ligneId] = $nc;
            $totalNc += $nc;
        }

        if ($totalNc <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Indiquez au moins une quantité non conforme sur une des lignes cochées.');
        }

        return $this->commitReception(
            $request,
            $bonCommande,
            $quantitesAPrendre,
            $nonConformesParLigne,
            'Réception enregistrée — non-conformité(s) signalée(s).'
        );
    }

    /**
     * @return array<int, float> ligneId => quantité
     */
    private function parseQuantitesSelectionnees(Request $request, BonCommande $bonCommande): array
    {
        $quantitesBrutes = $request->input('quantites', []);
        if (! is_array($quantitesBrutes)) {
            $quantitesBrutes = [];
        }

        $bonCommande->loadMissing(['lignes.article']);

        $quantitesAPrendre = [];
        foreach ($quantitesBrutes as $ligneId => $qty) {
            $qty = (float) $qty;
            if ($qty <= 0) {
                continue;
            }
            $ligneId = (int) $ligneId;
            $ligne = $bonCommande->lignes->firstWhere('id', $ligneId);
            if (! $ligne) {
                throw new \InvalidArgumentException('Une ligne sélectionnée n\'appartient pas à ce bon de commande.');
            }
            $quantitesAPrendre[$ligneId] = $qty;
        }

        return $quantitesAPrendre;
    }

    /**
     * @param  array<int, float>  $quantitesAPrendre
     * @param  array<int, float>  $nonConformesParLigne  id ligne => qté NC (stock mis à jour sur la partie conforme uniquement si NC &gt; 0)
     */
    private function commitReception(Request $request, BonCommande $bonCommande, array $quantitesAPrendre, array $nonConformesParLigne, string $successMessage): \Illuminate\Http\RedirectResponse
    {
        DB::beginTransaction();

        try {
            $reception = Reception::create([
                'bon_commande_id' => $bonCommande->id,
                'numero_reception' => Reception::generateNumeroReception(),
                'date_reception' => $request->date_reception,
                'numero_bon_livraison' => $request->numero_bon_livraison,
                'transporteur' => $request->transporteur,
                'observations' => $request->observations,
                'user_id' => Auth::id(),
                'statut' => 'en_cours',
            ]);

            $totalQuantiteRecue = 0;
            $montantTotalRecu = 0;
            $articleIdsPourPrix = [];

            foreach ($quantitesAPrendre as $ligneId => $quantiteRecue) {
                $ligne = LigneBonCommande::with('article')
                    ->where('bon_commande_id', $bonCommande->id)
                    ->findOrFail($ligneId);

                $quantiteRestante = (float) $ligne->quantite - (float) $ligne->quantite_recue;
                if ($quantiteRecue > $quantiteRestante + 0.000001) {
                    $nomArticle = $ligne->article ? $ligne->article->nom : 'Article';
                    throw new \Exception(
                        "La quantité pour « {$nomArticle} » dépasse le reste à recevoir ({$quantiteRestante})."
                    );
                }

                $nc = (float) ($nonConformesParLigne[$ligneId] ?? 0);
                if ($nc < 0 || $nc > $quantiteRecue + 0.000001) {
                    throw new \Exception('Quantité non conforme invalide pour une ligne.');
                }

                $quantiteConforme = round($quantiteRecue - $nc, 4);
                if ($quantiteConforme < 0) {
                    $quantiteConforme = 0;
                }

                $stockDelta = $nonConformesParLigne === [] ? $quantiteRecue : $quantiteConforme;

                LigneReception::create([
                    'reception_id' => $reception->id,
                    'ligne_bon_commande_id' => $ligne->id,
                    'article_id' => $ligne->article_id,
                    'quantite_recue' => $quantiteRecue,
                    'quantite_conforme' => $quantiteConforme,
                    'quantite_non_conforme' => $nc,
                    'prix_unitaire_recu' => $ligne->prix_unitaire,
                    'etat_article' => $nc > 0 ? 'defectueux' : 'neuf',
                ]);

                $ligne->quantite_recue = (float) $ligne->quantite_recue + (float) $quantiteRecue;
                $ligne->save();

                $totalQuantiteRecue += $quantiteRecue;
                $montantTotalRecu += $quantiteRecue * (float) $ligne->prix_unitaire;

                $ligne->load('article');
                $articleIdsPourPrix[$ligne->article_id] = true;
                $this->updateStock($ligne->article, $bonCommande->projet_id, $stockDelta, (float) $ligne->prix_unitaire);
            }

            $reception->update([
                'quantite_totale_recue' => $totalQuantiteRecue,
                'montant_total_recu' => $montantTotalRecu,
            ]);

            $bonCommande->refresh();
            $bonCommande->load('lignes');
            $this->updateBonCommandeStatus($bonCommande);

            $this->updateReceptionStatus($reception->fresh());

            foreach (array_keys($articleIdsPourPrix) as $articleId) {
                $a = Article::find($articleId);
                if ($a) {
                    $a->recalculerPrixAchatDepuisReceptions(5);
                }
            }

            DB::commit();

            return redirect()->route('receptions.show', $reception->id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la réception : '.$e->getMessage());
        }
    }

    /**
     * Met à jour le statut de la réception
     */
    private function updateReceptionStatus(Reception $reception)
    {
        $bonCommande = BonCommande::with('lignes')->findOrFail($reception->bon_commande_id);
        $totalQuantiteCommande = $bonCommande->lignes->sum('quantite');
        $totalQuantiteRecue = $bonCommande->lignes->sum('quantite_recue');
        
        if ($totalQuantiteRecue >= $totalQuantiteCommande) {
            $reception->statut = 'complete';
        } elseif ($totalQuantiteRecue > 0) {
            $reception->statut = 'partielle';
        } else {
            $reception->statut = 'en_cours';
        }
        
        $reception->save();
    }
    
    /**
     * Met à jour le statut du bon de commande
     */
    private function updateBonCommandeStatus(BonCommande $bonCommande)
    {
        $totalQuantiteCommande = $bonCommande->lignes->sum('quantite');
        $totalQuantiteRecue = $bonCommande->lignes->sum('quantite_recue');
        
        if ($totalQuantiteRecue >= $totalQuantiteCommande) {
            $bonCommande->statut = 'reçu';
        } elseif ($totalQuantiteRecue > 0) {
            $bonCommande->statut = 'partiellement_reçu';
        }
        
        $bonCommande->save();
    }

    /**
     * Met à jour le stock après réception
     */
    private function updateStock(Article $article, $projetId, $quantite, float $prixUnitaireRecu)
    {
        // Mettre à jour le stock général (catalogue)
        $article->quantite_stock = (float) $article->quantite_stock + (float) $quantite;
        $article->save();

        // Prix d'achat : recalculé après mise à jour du statut de la réception (voir store()).
        
        if ($projetId) {
            $stockProjet = StockProjet::firstOrCreate(
                [
                    'id_projet' => $projetId,
                    'article_id' => $article->id,
                ],
                ['quantite' => 0]
            );

            $stockProjet->increment('quantite', $quantite);
        }
    }

    public function history($bonCommandeId)
    {
        $bonCommande = BonCommande::with(['fournisseur', 'lignes.article'])->findOrFail($bonCommandeId);
        
        return view('receptions.history', compact('bonCommande'));
    }

    /**
     * PDF — Bon de livraison fournisseur (réception)
     */
    public function exportBonLivraisonPdf($id)
    {
        $reception = Reception::with([
            'bonCommande.fournisseur',
            'bonCommande.projet.clientFournisseur',
            'bonCommande.demandeAchat.projet',
            'bonCommande.demandeApprovisionnement.projet',
            'lignes.article.uniteMesure',
            'lignes.ligneBonCommande',
            'user',
        ])->findOrFail($id);

        $bonCommande = $reception->bonCommande;
        $buId = $bonCommande
            ? PdfBranding::resolveBuIdForBonCommande($bonCommande)
            : (session('selected_bu') ? (int) session('selected_bu') : null);
        $pdfBranding = PdfBranding::forBu($buId);

        $bl = $this->buildBonLivraisonData($reception, $pdfBranding);

        $pdf = Pdf::loadView('receptions.bon-livraison-pdf', [
            'reception' => $reception,
            'bonCommande' => $bonCommande,
            'pdfBranding' => $pdfBranding,
            'configGlobal' => $pdfBranding['config'],
            'bl' => $bl,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('Bon_livraison_fournisseur_' . $bl['numero'] . '.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBonLivraisonData(Reception $reception, array $pdfBranding): array
    {
        $bonCommande = $reception->bonCommande;
        $bonCommande?->loadMissing(['projet.clientFournisseur', 'fournisseur']);
        $client = $bonCommande?->projet?->clientFournisseur;

        $numero = filled($reception->numero_bon_livraison)
            ? $reception->numero_bon_livraison
            : str_pad((string) $reception->id, 6, '0', STR_PAD_LEFT);

        $lignes = [];
        $index = 1;
        foreach ($reception->lignes as $ligne) {
            $article = $ligne->article;
            $ligneBc = $ligne->ligneBonCommande;
            $lignes[] = [
                'numero_ligne' => str_pad((string) $index++, 3, '0', STR_PAD_LEFT),
                'ref_produit' => $article?->reference ?? $article?->reference_fournisseur ?? '—',
                'designation' => $article?->nom ?? '—',
                'unite' => $article?->uniteMesure?->ref
                    ?? $article?->uniteMesure?->nom
                    ?? '—',
                'quantite_commandee' => (float) ($ligneBc?->quantite ?? $ligne->quantite_recue),
                'quantite_livree' => (float) $ligne->quantite_recue,
            ];
        }

        $livreA = $client?->nom_raison_sociale
            ?? $bonCommande?->projet?->nom_projet
            ?? '—';

        $adresseRue = filled($client?->adresse_localisation)
            ? $client->adresse_localisation
            : (filled($bonCommande?->lieu_livraison) ? $bonCommande->lieu_livraison : null);

        $adresseBp = filled($client?->boite_postale) ? $client->boite_postale : null;

        return [
            'numero' => $numero,
            'date' => $reception->date_reception?->format('d/m/Y') ?? now()->format('d/m/Y'),
            'ref_bc' => $bonCommande?->reference ?? '',
            'emis_par' => $bonCommande?->fournisseur?->nom_raison_sociale
                ?: ($reception->user?->nom_complet ?? '—'),
            'livre_a' => $livreA,
            'adresse_rue' => $adresseRue,
            'adresse_bp' => $adresseBp,
            'telephone' => $client?->telephone ?? '',
            'email' => $client?->email ?? '',
            'rccm' => $client?->n_rccm ?? '',
            'cc' => $client?->n_cc ?? '',
            'lieu_livraison' => $bonCommande?->lieu_livraison ?? '—',
            'fournisseur_nom' => $bonCommande?->fournisseur?->nom_raison_sociale ?? '',
            'lignes' => $lignes,
        ];
    }
}