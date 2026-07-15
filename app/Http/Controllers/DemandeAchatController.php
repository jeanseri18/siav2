<?php

namespace App\Http\Controllers;

use App\Models\DemandeAchat;
use App\Models\LigneDemandeAchat;
use App\Models\Projet;
use App\Models\Article;
use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\PdfBranding;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DemandeAchatController extends Controller
{
    public function index()
    {
        $demandes = DemandeAchat::with(['user', 'projet', 'lignes.article'])
            ->withCount('demandeCotations')
            ->get();
        return view('demande_achats.index', compact('demandes'));
    }

    public function exportListePdf()
    {
        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $demandes = $this->demandesAchatForListing($buId);
        $pdfBranding = PdfBranding::forBu($buId);

        $pdf = PDF::loadView('demande_achats.liste-export', [
            'demandes' => $demandes,
            'pdfBranding' => $pdfBranding,
            'documentTitle' => 'Liste des demandes d\'achat',
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('liste-demandes-achat-'.now()->format('Y-m-d').'.pdf', ['Attachment' => false]);
    }

    private function demandesAchatForListing(?int $buId = null)
    {
        $query = DemandeAchat::with(['user', 'projet', 'approbateur', 'lignes'])
            ->withCount('lignes')
            ->orderByDesc('date_demande')
            ->orderByDesc('id');

        if ($buId) {
            $query->whereHas('projet', fn ($q) => $q->where('bu_id', $buId));
        }

        return $query->get();
    }

    public function create(Request $request)
    {
        $projets = Projet::all();
        $articles = Article::with(['categorie', 'uniteMesure'])->get();
        $demandesApprovisionnement = \App\Models\DemandeApprovisionnement::with(['projet', 'lignes.article.uniteMesure'])
            ->where('statut', 'approuvée')
            ->get();
        $demandeApprovisionnementPreselectId = $request->filled('demande_approvisionnement_id')
            ? (int) $request->query('demande_approvisionnement_id')
            : null;

        return view('demande_achats.create', compact('projets', 'articles', 'demandesApprovisionnement', 'demandeApprovisionnementPreselectId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_demande' => 'required|date',
            'demande_approvisionnement_id' => 'nullable|exists:demande_approvisionnements,id',
            'projet_id' => 'nullable|exists:projets,id',
            'description' => 'nullable|string',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'date_besoin' => 'nullable|date|after_or_equal:date_demande',
            'designation' => 'required|array',
            'designation.*' => 'required|string',
            'article_id' => 'nullable|array',
            'article_id.*' => 'nullable|exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'unite_mesure' => 'required|array',
            'unite_mesure.*' => 'string',
            'prix_estime' => 'nullable|array',
            'prix_estime.*' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

        // Générer la référence
        $lastReference = Reference::where('nom', 'Code Demande Achat')
            ->latest('created_at')
            ->first();
        
        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'DAC_0000';
        $newReference = 'DAC_' . now()->format('YmdHis');

        // Créer la demande
        $demande = DemandeAchat::create([
            'reference' => $newReference,
            'date_demande' => $request->date_demande,
            'demande_approvisionnement_id' => $request->demande_approvisionnement_id,
            'projet_id' => $request->projet_id,
            'description' => $request->description,
            'priorite' => $request->priorite,
            'date_besoin' => $request->date_besoin,
            'user_id' => Auth::id(),
            'statut' => 'en attente'
        ]);

        // Ajouter les lignes de la demande
        for ($i = 0; $i < count($request->designation); $i++) {
            if ($request->designation[$i] && $request->quantite[$i] > 0) {
                LigneDemandeAchat::create([
                    'demande_achat_id' => $demande->id,
                    'article_id' => $request->article_id[$i] ?? null,
                    'designation' => $request->designation[$i],
                    'quantite' => $request->quantite[$i],
                    'unite_mesure' => $request->unite_mesure[$i],
                    'prix_estime' => isset($request->prix_estime[$i]) ? $request->prix_estime[$i] : null,
                    'specifications' => $request->specifications[$i] ?? null,
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        // Enregistrer la référence
        Reference::create([
            'nom' => 'Code Demande Achat',
            'ref' => $newReference
        ]);

        return redirect()->route('demande-achats.index')
            ->with('success', 'Demande d\'achat créée avec succès');
    }

    public function show(DemandeAchat $demandeAchat)
    {
        $demandeAchat->load(['user', 'projet', 'approbateur', 'lignes.article']);
        $demandeAchat->loadCount('demandeCotations');
        return view('demande_achats.show', compact('demandeAchat'));
    }

    public function edit(DemandeAchat $demandeAchat)
    {
        if ($demandeAchat->statut !== 'en attente') {
            return redirect()->route('demande-achats.show', $demandeAchat)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en attente');
        }

        $projets = Projet::all();
        $articles = Article::with(['categorie', 'uniteMesure'])->get();
        $demandeAchat->load(['lignes.article']);
        
        return view('demande_achats.edit', compact('demandeAchat', 'projets', 'articles'));
    }

    public function update(Request $request, DemandeAchat $demandeAchat)
    {
        if ($demandeAchat->statut !== 'en attente') {
            return redirect()->route('demande-achats.show', $demandeAchat)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en attente');
        }

        $request->validate([
            'date_demande' => 'required|date',
            'projet_id' => 'nullable|exists:projets,id',
            'description' => 'nullable|string',
            'priorite' => 'required|in:basse,normale,haute,urgente',
            'date_besoin' => 'nullable|date|after_or_equal:date_demande',
            'designation' => 'required|array',
            'designation.*' => 'required|string',
            'article_id' => 'nullable|array',
            'article_id.*' => 'nullable|exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'unite_mesure' => 'required|array',
            'unite_mesure.*' => 'string',
            'prix_estime' => 'nullable|array',
            'prix_estime.*' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

        // Mettre à jour la demande
        $demandeAchat->update([
            'date_demande' => $request->date_demande,
            'projet_id' => $request->projet_id,
            'description' => $request->description,
            'priorite' => $request->priorite,
            'date_besoin' => $request->date_besoin
        ]);

        // Supprimer les anciennes lignes
        $demandeAchat->lignes()->delete();

        // Ajouter les nouvelles lignes
        for ($i = 0; $i < count($request->designation); $i++) {
            if ($request->designation[$i] && $request->quantite[$i] > 0) {
                LigneDemandeAchat::create([
                    'demande_achat_id' => $demandeAchat->id,
                    'article_id' => $request->article_id[$i] ?? null,
                    'designation' => $request->designation[$i],
                    'quantite' => $request->quantite[$i],
                    'unite_mesure' => $request->unite_mesure[$i],
                    'prix_estime' => isset($request->prix_estime[$i]) ? $request->prix_estime[$i] : null,
                    'specifications' => $request->specifications[$i] ?? null,
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        return redirect()->route('demande-achats.index')
            ->with('success', 'Demande d\'achat mise à jour avec succès');
    }

    /**
     * API pour récupérer les articles d'une demande d'achat
     */
    public function getArticles($id)
    {
        $demandeAchat = DemandeAchat::with('lignes.article')->findOrFail($id);
        
        $articles = $demandeAchat->lignes->map(function($ligne) {
            return [
                'article_id' => $ligne->article_id,
                'designation' => $ligne->designation,
                'quantite' => $ligne->quantite,
                'unite_mesure' => $ligne->unite_mesure,
                'specifications' => $ligne->specifications,
                'reference' => $ligne->article ? $ligne->article->reference : null
            ];
        });
        
        return response()->json(['articles' => $articles]);
    }

    public function destroy(DemandeAchat $demandeAchat)
    {
        if ($demandeAchat->statut !== 'en attente') {
            return redirect()->route('demande-achats.index')
                ->with('error', 'Impossible de supprimer une demande qui n\'est pas en attente');
        }

        // Supprimer les lignes de la demande
        $demandeAchat->lignes()->delete();
        
        // Supprimer la demande
        $demandeAchat->delete();

        return redirect()->route('demande-achats.index')
            ->with('success', 'Demande d\'achat supprimée avec succès');
    }

    public function approve(Request $request, DemandeAchat $demandeAchat)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('demande-achats.show', $demandeAchat)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour approuver cette demande.');
        }

        if ($demandeAchat->statut !== 'en attente') {
            return redirect()->route('demande-achats.show', $demandeAchat)
                ->with('error', 'Cette demande ne peut pas être approuvée');
        }

        // Mettre à jour le statut de la demande
        $demandeAchat->update([
            'statut' => 'approuvée',
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('demande-achats.show', $demandeAchat)
            ->with('success', 'Demande d\'achat approuvée avec succès');
    }

    public function reject(Request $request, DemandeAchat $demandeAchat)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('demande-achats.show', $demandeAchat)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour rejeter cette demande.');
        }

        if ($demandeAchat->statut !== 'en attente') {
            return redirect()->route('demande-achats.show', $demandeAchat)
                ->with('error', 'Cette demande ne peut pas être rejetée');
        }

        $request->validate([
            'motif_rejet' => 'required|string'
        ]);

        // Mettre à jour le statut de la demande
        $demandeAchat->update([
            'statut' => 'rejetée',
            'motif_rejet' => $request->motif_rejet,
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('demande-achats.show', $demandeAchat)
            ->with('success', 'Demande d\'achat rejetée');
    }

    /**
     * Exporter une demande d'achat en PDF
     */
    public function exportPDF($id)
    {
        $demandeAchat = DemandeAchat::with([
            'user.bus', 
            'projet', 
            'approbateur', 
            'lignes.article'
        ])->findOrFail($id);

        $buId = $demandeAchat->projet?->bu_id ?? (session('selected_bu') ? (int) session('selected_bu') : null);
        $pdfBranding = PdfBranding::forBu($buId);
        
        $pdf = PDF::loadView('demande_achats.pdf', compact('demandeAchat', 'pdfBranding'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->download('Demande_Achat_' . $demandeAchat->reference . '.pdf');
    }
}