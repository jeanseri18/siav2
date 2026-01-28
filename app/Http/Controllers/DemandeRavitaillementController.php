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
            
            // Ajouter les articles au stock du contrat
            foreach ($demandeRavitaillement->lignes as $ligne) {
                // Vérifier si l'article existe déjà dans le stock du contrat
                $stockExistant = StockProjet::where('id_contrat', $demandeRavitaillement->contrat_id)
                    ->where('article_id', $ligne->article_id)
                    ->first();
                
                if ($stockExistant) {
                    // Augmenter la quantité existante
                    $stockExistant->increment('quantite', $ligne->quantite_demandee);
                } else {
                    // Créer un nouveau stock
                    StockProjet::create([
                        'id_projet' => $demandeRavitaillement->contrat->id_projet,
                        'id_contrat' => $demandeRavitaillement->contrat_id,
                        'article_id' => $ligne->article_id,
                        'quantite' => $ligne->quantite_demandee,
                        'unite_mesure_id' => $ligne->unite_mesure_id ?? $ligne->article->unite_mesure_id,
                    ]);
                }
            }
            
            DB::commit();
            
            return back()->with('success', 'Demande approuvée avec succès. Les articles ont été ajoutés au stock du contrat.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de l\'approbation: ' . $e->getMessage());
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
