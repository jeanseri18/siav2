<?php

namespace App\Http\Controllers;

use App\Models\DemandeRavitaillement;
use App\Models\LigneDemandeRavitaillement;
use App\Models\Contrat;
use App\Models\User;
use App\Models\ClientFournisseur;
use App\Models\Article;
use App\Models\UniteMesure;use App\Models\StockProjet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemandeRavitaillementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $demandes = DemandeRavitaillement::with(['contrat', 'demandeur', 'approbateur'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('demandes_ravitaillement.index', compact('demandes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contratSessionId = session('contrat_id');
        
        if (!$contratSessionId) {
            return redirect()->route('demandes-ravitaillement.index')
                ->with('error', 'Aucun contrat sélectionné en session. Veuillez sélectionner un contrat.');
        }
        
        $contrats = Contrat::with('client')->get();
        $articles = Article::with('uniteMesure')->get();
        $unitesMesure = UniteMesure::all();
        
        return view('demandes_ravitaillement.create', compact('contrats', 'articles', 'unitesMesure', 'contratSessionId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return back()->withInput()->withErrors(['error' => 'Aucun contrat sélectionné en session.']);
        }
        
        $request->validate([
            'reference' => 'required|string|max:255|unique:demandes_ravitaillement',
            'objet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'date_demande' => 'required|date',
            'date_livraison_souhaitee' => 'nullable|date|after:date_demande',
            'commentaires' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.article_id' => 'required|exists:articles,id',
            'lignes.*.quantite_demandee' => 'required|numeric|min:0.001',
            'lignes.*.unite_mesure_id' => 'nullable|exists:unite_mesures,id'
        ]);
        
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return back()->withInput()->withErrors(['error' => 'Aucun contrat sélectionné en session.']);
        }
        
        DB::beginTransaction();
        
        try {
            $demande = DemandeRavitaillement::create([
                'reference' => $request->reference,
                'objet' => $request->objet,
                'description' => $request->description,
                'statut' => 'en_attente',
                'priorite' => $request->priorite,
                'date_demande' => $request->date_demande,
                'date_livraison_souhaitee' => $request->date_livraison_souhaitee,
                'contrat_id' => $contratId,
                'demandeur_id' => Auth::id(),
                'commentaires' => $request->commentaires
            ]);
            
            foreach ($request->lignes as $ligne) {
                LigneDemandeRavitaillement::create([
                    'demande_ravitaillement_id' => $demande->id,
                    'article_id' => $ligne['article_id'],
                    'quantite_demandee' => $ligne['quantite_demandee'],
                    'unite_mesure_id' => $ligne['unite_mesure_id'] ?? null
                ]);
            }
            
            // Pas de calcul de montant pour les demandes internes
            $demande->update(['montant_estime' => 0]);
            
            DB::commit();
            
            return redirect()->route('demandes-ravitaillement.index')
                ->with('success', 'Demande de ravitaillement créée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création de la demande.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $demandeRavitaillement = DemandeRavitaillement::with(['contrat.client', 'demandeur', 'approbateur', 'lignes.article.uniteMesure', 'lignes.uniteMesure'])
                ->findOrFail($id);
            
            return view('demandes_ravitaillement.show', compact('demandeRavitaillement'));
        } catch (\Exception $e) {
            return redirect()->route('demandes-ravitaillement.index')
                ->with('error', 'Demande de ravitaillement non trouvée.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $demandeRavitaillement = DemandeRavitaillement::with(['lignes.article', 'lignes.uniteMesure'])
                ->findOrFail($id);
                
            if ($demandeRavitaillement->statut !== 'en_attente') {
                return redirect()->route('demandes-ravitaillement.show', $demandeRavitaillement->id)
                    ->with('error', 'Seules les demandes en attente peuvent être modifiées.');
            }
            $contrats = Contrat::with('client')->get();
            $articles = Article::with('uniteMesure')->get();
            $unitesMesure = UniteMesure::all();
            $contratSessionId = session('contrat_id');
            
            return view('demandes_ravitaillement.edit', compact('demandeRavitaillement', 'contrats', 'articles', 'unitesMesure', 'contratSessionId'));
        } catch (\Exception $e) {
            return redirect()->route('demandes-ravitaillement.index')
                ->with('error', 'Erreur lors du chargement de la demande: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $demandeRavitaillement = DemandeRavitaillement::findOrFail($id);
        
        if ($demandeRavitaillement->statut !== 'en_attente') {
            return redirect()->route('demandes-ravitaillement.show', $demandeRavitaillement->id)
                ->with('error', 'Seules les demandes en attente peuvent être modifiées.');
        }
        
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return back()->withInput()->withErrors(['error' => 'Aucun contrat sélectionné en session.']);
        }
        
        $request->validate([
            'reference' => 'required|string|max:255|unique:demandes_ravitaillement,reference,' . $demandeRavitaillement->id,
            'objet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'date_demande' => 'required|date',
            'date_livraison_souhaitee' => 'nullable|date|after:date_demande',
            'commentaires' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.article_id' => 'required|exists:articles,id',
            'lignes.*.quantite_demandee' => 'required|numeric|min:0.001',
            'lignes.*.unite_mesure_id' => 'nullable|exists:unite_mesures,id'
        ]);
        
        DB::beginTransaction();
        
        try {
            $demandeRavitaillement->update([
                'reference' => $request->reference,
                'objet' => $request->objet,
                'description' => $request->description,
                'priorite' => $request->priorite,
                'date_demande' => $request->date_demande,
                'date_livraison_souhaitee' => $request->date_livraison_souhaitee,
                'contrat_id' => $contratId,
                'commentaires' => $request->commentaires
            ]);
            
            // Supprimer les anciennes lignes
            $demandeRavitaillement->lignes()->delete();
            
            // Créer les nouvelles lignes
            foreach ($request->lignes as $ligne) {
                LigneDemandeRavitaillement::create([
                    'demande_ravitaillement_id' => $demandeRavitaillement->id,
                    'article_id' => $ligne['article_id'],
                    'quantite_demandee' => $ligne['quantite_demandee'],
                    'unite_mesure_id' => $ligne['unite_mesure_id'] ?? null
                ]);
            }
            
            // Pas de calcul de montant pour les demandes internes
            $demandeRavitaillement->update(['montant_estime' => 0]);
            
            DB::commit();
            
            return redirect()->route('demandes-ravitaillement.show', $demandeRavitaillement->id)
                ->with('success', 'Demande de ravitaillement mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour de la demande.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DemandeRavitaillement $demandeRavitaillement)
    {
        if ($demandeRavitaillement->statut !== 'en_attente') {
            return redirect()->route('demandes-ravitaillement.index')
                ->with('error', 'Seules les demandes en attente peuvent être supprimées.');
        }
        
        try {
            $demandeRavitaillement->delete();
            
            return redirect()->route('demandes-ravitaillement.index')
                ->with('success', 'Demande de ravitaillement supprimée avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('demandes-ravitaillement.index')
                ->with('error', 'Erreur lors de la suppression de la demande.');
        }
    }
    
    /**
     * Approuver une demande de ravitaillement
     */
    public function approuver(Request $request, DemandeRavitaillement $demandeRavitaillement)
    {
        if ($demandeRavitaillement->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande ne peut plus être approuvée.');
        }
        
        $request->validate([
            'commentaires' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Mettre à jour le statut de la demande
            $demandeRavitaillement->update([
                'statut' => 'approuvee',
                'approbateur_id' => Auth::id(),
                'commentaires' => $request->commentaires
            ]);
            
            // Initialiser les quantités approuvées (par défaut = demandées)
            foreach ($demandeRavitaillement->lignes as $ligne) {
                $ligne->update([
                    'quantite_approuvee' => $ligne->quantite_demandee,
                    'quantite_livree' => 0
                ]);
            }
            
            DB::commit();
            
            return back()->with('success', 'Demande approuvée avec succès. En attente de livraison.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    /**
     * Livrer une demande de ravitaillement (Gestionnaire de stock)
     */
    public function livrer(Request $request, DemandeRavitaillement $demandeRavitaillement)
    {
        if ($demandeRavitaillement->statut !== 'approuvee' && $demandeRavitaillement->statut !== 'en_cours') {
            return back()->with('error', 'Seules les demandes approuvées ou en cours peuvent être livrées.');
        }

        $request->validate([
            'lignes' => 'required|array',
            'lignes.*.id' => 'required|exists:lignes_demande_ravitaillement,id',
            'lignes.*.quantite_a_livrer' => 'required|numeric|min:0',
            'date_livraison' => 'required|date',
            'commentaires' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $toutLivre = true;

            foreach ($request->lignes as $data) {
                $ligne = LigneDemandeRavitaillement::findOrFail($data['id']);
                $quantiteALivrer = $data['quantite_a_livrer'];

                if ($quantiteALivrer > 0) {
                    // Vérifier le stock disponible dans le projet
                    $stock = StockProjet::where('id_projet', $demandeRavitaillement->contrat->id_projet)
                        ->where('article_id', $ligne->article_id)
                        ->first();

                    if (!$stock || $stock->quantite < $quantiteALivrer) {
                        throw new \Exception("Stock insuffisant pour l'article " . $ligne->article->nom);
                    }

                    // Décrémenter le stock
                    $quantiteAvant = $stock->quantite;
                    $stock->decrement('quantite', $quantiteALivrer);
                    
                    // Enregistrer le mouvement de stock (Sortie)
                    \App\Models\MouvementStock::create([
                        'stock_projet_id' => $stock->id,
                        'type_mouvement' => 'sortie', // Ravitaillement = Sortie vers chantier
                        'quantite' => -$quantiteALivrer,
                        'quantite_avant' => $quantiteAvant,
                        'quantite_apres' => $stock->quantite,
                        'date_mouvement' => $request->date_livraison,
                        'user_id' => Auth::id(),
                        'reference_mouvement' => 'RAV-' . $demandeRavitaillement->reference,
                        'commentaires' => 'Livraison pour demande ravitaillement ' . $demandeRavitaillement->reference
                    ]);

                    // Mettre à jour la ligne
                    $ligne->quantite_livree += $quantiteALivrer;
                    $ligne->save();
                }

                if ($ligne->quantite_livree < $ligne->quantite_approuvee) {
                    $toutLivre = false;
                }
            }

            $demandeRavitaillement->update([
                'statut' => $toutLivre ? 'livree' : 'en_cours',
                'date_livraison_effective' => $request->date_livraison,
                'commentaires' => $demandeRavitaillement->commentaires . "\n" . $request->commentaires
            ]);

            DB::commit();

            return back()->with('success', 'Livraison enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la livraison: ' . $e->getMessage());
        }
    }

    /**
     * Réceptionner une livraison (Chef de chantier)
     */
    public function receptionner(Request $request, DemandeRavitaillement $demandeRavitaillement)
    {
        $request->validate([
            'lignes' => 'required|array',
            'lignes.*.id' => 'required|exists:lignes_demande_ravitaillement,id',
            'lignes.*.quantite_recue' => 'required|numeric|min:0',
            'lignes.*.motif_retour' => 'nullable|string',
            'date_reception' => 'required|date'
        ]);

        DB::beginTransaction();

        try {
            $receptionDetails = [
                'date' => $request->date_reception,
                'items' => []
            ];
            
            $retourNecessaire = false;

            foreach ($request->lignes as $data) {
                $ligne = LigneDemandeRavitaillement::findOrFail($data['id']);
                $quantiteRecue = $data['quantite_recue'];
                $quantiteRetournee = max(0, $ligne->quantite_livree - $quantiteRecue);

                $receptionDetails['items'][$ligne->id] = [
                    'recu' => $quantiteRecue,
                    'retour' => $quantiteRetournee,
                    'motif' => $data['motif_retour'] ?? null
                ];

                if ($quantiteRetournee > 0) {
                    $retourNecessaire = true;
                }
            }
            
            // Stocker les détails de réception dans le commentaire (JSON encode)
            // On ajoute au commentaire existant ou on utilise un format spécifique
            $newComment = "RECEPTION_LOG: " . json_encode($receptionDetails);
            
            $demandeRavitaillement->update([
                'commentaires' => $demandeRavitaillement->commentaires . "\n" . $newComment
            ]);

            DB::commit();

            return back()->with('success', 'Réception enregistrée.' . ($retourNecessaire ? ' Des articles ont été refusés et doivent être retournés au stock.' : ''));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la réception: ' . $e->getMessage());
        }
    }

    /**
     * Valider le retour en stock (Gestionnaire de stock)
     */
    public function validerRetour(Request $request, DemandeRavitaillement $demandeRavitaillement)
    {
        // Cette fonction analyse les commentaires pour trouver les retours non traités
        // C'est un peu "hacky" sans table dédiée, mais ça évite la migration.
        
        DB::beginTransaction();

        try {
            // Logique simplifiée: On suppose que le gestionnaire valide manuellement les quantités à réintégrer
            // via un formulaire qui liste les articles.
            
            $request->validate([
                'lignes' => 'required|array',
                'lignes.*.article_id' => 'required|exists:articles,id',
                'lignes.*.quantite_retour' => 'required|numeric|min:0'
            ]);
            
            foreach ($request->lignes as $data) {
                if ($data['quantite_retour'] > 0) {
                    $stock = StockProjet::where('id_projet', $demandeRavitaillement->contrat->id_projet)
                        ->where('article_id', $data['article_id'])
                        ->first();

                    if ($stock) {
                        $quantiteAvant = $stock->quantite;
                        $stock->increment('quantite', $data['quantite_retour']);

                        // Enregistrer le mouvement de stock (Entrée/Retour)
                        \App\Models\MouvementStock::create([
                            'stock_projet_id' => $stock->id,
                            'type_mouvement' => 'retour_chantier',
                            'quantite' => $data['quantite_retour'],
                            'quantite_avant' => $quantiteAvant,
                            'quantite_apres' => $stock->quantite,
                            'date_mouvement' => now(),
                            'user_id' => Auth::id(),
                            'reference_mouvement' => 'RET-RAV-' . $demandeRavitaillement->reference,
                            'commentaires' => 'Retour suite à refus sur demande ' . $demandeRavitaillement->reference
                        ]);
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'Retours validés et réintégrés au stock.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la validation du retour: ' . $e->getMessage());
        }
    }
    
    /**
     * Rejeter une demande de ravitaillement
     */
    public function rejeter(Request $request, DemandeRavitaillement $demandeRavitaillement)
    {
        if ($demandeRavitaillement->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande ne peut plus être rejetée.');
        }
        
        $request->validate([
            'motif_rejet' => 'required|string'
        ]);
        
        $demandeRavitaillement->update([
            'statut' => 'rejetee',
            'approbateur_id' => Auth::id(),
            'motif_rejet' => $request->motif_rejet
        ]);
        
        return back()->with('success', 'Demande rejetée.');
    }
    
    /**
     * Marquer une demande comme livrée
     */
    public function marquerLivree(Request $request, DemandeRavitaillement $demandeRavitaillement)
    {
        if ($demandeRavitaillement->statut !== 'approuvee') {
            return back()->with('error', 'Seules les demandes approuvées peuvent être marquées comme livrées.');
        }
        
        $request->validate([
            'date_livraison_effective' => 'required|date',
            'montant_reel' => 'nullable|numeric|min:0',
            'commentaires' => 'nullable|string'
        ]);
        
        $demandeRavitaillement->update([
            'statut' => 'livree',
            'date_livraison_effective' => $request->date_livraison_effective,
            'montant_reel' => $request->montant_reel,
            'commentaires' => $request->commentaires
        ]);
        
        return back()->with('success', 'Demande marquée comme livrée avec succès.');
    }
}
