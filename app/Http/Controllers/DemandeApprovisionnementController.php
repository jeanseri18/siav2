<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Article;
use App\Models\Reference;
use App\Models\DemandeApprovisionnement;
use App\Models\LigneDemandeApprovisionnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\PdfBranding;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DemandeApprovisionnementController extends Controller
{
    public function index()
    {
        $demandes = DemandeApprovisionnement::with(['user', 'projet', 'lignes.article'])
            ->withCount('demandeAchats')
            ->get();
        return view('demande_approvisionnements.index', compact('demandes'));
    }

    public function exportListePdf()
    {
        $buId = session('selected_bu') ? (int) session('selected_bu') : null;
        $demandes = $this->demandesApprovisionnementForListing($buId);
        $pdfBranding = PdfBranding::forBu($buId);

        $pdf = PDF::loadView('demande_approvisionnements.liste-export', [
            'demandes' => $demandes,
            'pdfBranding' => $pdfBranding,
            'documentTitle' => 'Liste des demandes d\'approvisionnement',
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        return $pdf->stream('liste-demandes-approvisionnement-'.now()->format('Y-m-d').'.pdf', ['Attachment' => false]);
    }

    private function demandesApprovisionnementForListing(?int $buId = null)
    {
        $query = DemandeApprovisionnement::with(['user', 'projet', 'approbateur', 'lignes'])
            ->withCount('lignes')
            ->orderByDesc('date_demande')
            ->orderByDesc('id');

        if ($buId) {
            $query->whereHas('projet', fn ($q) => $q->where('bu_id', $buId));
        }

        return $query->get();
    }

    public function create()
    {
        $projets = Projet::all();
        $articles = Article::with('categorie')->get();
        return view('demande_approvisionnements.create', compact('projets', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_demande' => 'required|date',
            'date_reception' => 'nullable|date|after_or_equal:date_demande',
            'projet_id' => 'nullable|exists:projets,id',
            'initiateur' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite_demandee' => 'required|array',
            'quantite_demandee.*' => 'integer|min:1',
            'designation' => 'nullable|array',
            'unite' => 'nullable|array'
        ]);

        // Générer la référence
        $lastReference = Reference::where('nom', 'Code Demande Approvisionnement')
            ->latest('created_at')
            ->first();
        
        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'DA_0000';
        $newReference = 'DA_' . now()->format('YmdHis');

        // Créer la demande
        $demande = DemandeApprovisionnement::create([
            'reference' => $newReference,
            'date_demande' => $request->date_demande,
            'date_reception' => $request->date_reception,
            'projet_id' => $request->projet_id,
            'initiateur' => $request->initiateur ?? Auth::user()->nom_complet,
            'user_id' => Auth::id(),
            'statut' => 'en attente'
        ]);

        // Ajouter les lignes de la demande
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite_demandee[$i] > 0) {
                LigneDemandeApprovisionnement::create([
                    'demande_approvisionnement_id' => $demande->id,
                    'article_id' => $request->article_id[$i],
                    'quantite_demandee' => $request->quantite_demandee[$i],
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        // Enregistrer la référence
        Reference::create([
            'nom' => 'Code Demande Approvisionnement',
            'ref' => $newReference
        ]);

        return redirect()->route('demande-approvisionnements.index')
            ->with('success', 'Demande d\'approvisionnement créée avec succès');
    }

    public function show(DemandeApprovisionnement $demandeApprovisionnement)
    {
        $demandeApprovisionnement->load(['user', 'projet', 'approbateur', 'lignes.article.uniteMesure']);
        $demandeApprovisionnement->loadCount('demandeAchats');
        return view('demande_approvisionnements.show', compact('demandeApprovisionnement'));
    }

    public function edit(DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en attente');
        }

        $projets = Projet::all();
        $articles = Article::with('categorie')->get();
        $demandeApprovisionnement->load(['lignes.article']);
        
        return view('demande_approvisionnements.edit', compact('demandeApprovisionnement', 'projets', 'articles'));
    }

    public function update(Request $request, DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en attente');
        }

        $request->validate([
            'date_demande' => 'required|date',
            'projet_id' => 'nullable|exists:projets,id',
            'description' => 'nullable|string',
            'article_id' => 'required|array',
            'article_id.*' => 'exists:articles,id',
            'quantite_demandee' => 'required|array',
            'quantite_demandee.*' => 'integer|min:1'
        ]);

        // Mettre à jour la demande
        $demandeApprovisionnement->update([
            'date_demande' => $request->date_demande,
            'projet_id' => $request->projet_id,
            'description' => $request->description
        ]);

        // Supprimer les anciennes lignes
        $demandeApprovisionnement->lignes()->delete();

        // Ajouter les nouvelles lignes
        for ($i = 0; $i < count($request->article_id); $i++) {
            if ($request->article_id[$i] && $request->quantite_demandee[$i] > 0) {
                LigneDemandeApprovisionnement::create([
                    'demande_approvisionnement_id' => $demandeApprovisionnement->id,
                    'article_id' => $request->article_id[$i],
                    'quantite_demandee' => $request->quantite_demandee[$i],
                    'commentaire' => $request->commentaire[$i] ?? null
                ]);
            }
        }

        return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
            ->with('success', 'Demande d\'approvisionnement mise à jour avec succès');
    }

    public function destroy(DemandeApprovisionnement $demandeApprovisionnement)
    {
        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.index')
                ->with('error', 'Impossible de supprimer une demande qui n\'est pas en attente');
        }

        // Supprimer les lignes de la demande
        $demandeApprovisionnement->lignes()->delete();
        
        // Supprimer la demande
        $demandeApprovisionnement->delete();

        return redirect()->route('demande-approvisionnements.index')
            ->with('success', 'Demande d\'approvisionnement supprimée avec succès');
    }

    public function approve(Request $request, DemandeApprovisionnement $demandeApprovisionnement)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'chef_chantier', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour approuver cette demande.');
        }

        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Cette demande ne peut pas être approuvée');
        }

        $lignesASupprimer = array_filter((array) $request->input('lignes_a_supprimer', []));
        foreach ($lignesASupprimer as $ligneId) {
            $demandeApprovisionnement->lignes()->whereKey($ligneId)->delete();
        }

        $demandeApprovisionnement->load('lignes');
        if ($demandeApprovisionnement->lignes->isEmpty()) {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Impossible d\'approuver : la demande ne contient plus aucune ligne d\'article.');
        }

        $request->validate([
            'quantite_approuvee' => 'required|array',
            'quantite_approuvee.*' => 'integer|min:0',
            'lignes_a_supprimer' => 'nullable|array',
            'lignes_a_supprimer.*' => 'integer|exists:lignes_demande_approvisionnement,id',
        ]);

        $clesQuantites = collect($request->quantite_approuvee ?? [])->keys()->map(fn ($k) => (int) $k);
        foreach ($demandeApprovisionnement->lignes as $ligne) {
            if (! $clesQuantites->contains((int) $ligne->id)) {
                return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                    ->with('error', 'Chaque ligne restante doit avoir une quantité approuvée.');
            }
        }

        foreach ($request->quantite_approuvee as $ligneId => $quantite) {
            $ligne = $demandeApprovisionnement->lignes()->whereKey($ligneId)->first();
            if (! $ligne) {
                return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                    ->with('error', 'Ligne d\'article invalide.');
            }
            $ligne->update([
                'quantite_approuvee' => $quantite,
            ]);
        }

        // Mettre à jour le statut de la demande
        $demandeApprovisionnement->update([
            'statut' => 'approuvée',
            'approved_by' => Auth::id()
        ]);

        return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
            ->with('success', 'Demande d\'approvisionnement approuvée avec succès');
    }

    public function reject(Request $request, DemandeApprovisionnement $demandeApprovisionnement)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'chef_chantier', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour rejeter cette demande.');
        }

        if ($demandeApprovisionnement->statut !== 'en attente') {
            return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
                ->with('error', 'Cette demande ne peut pas être rejetée');
        }

        $request->validate([
            'motif_rejet' => 'required|string'
        ]);

        // Mettre à jour le statut de la demande
        $demandeApprovisionnement->update([
            'statut' => 'rejetée',
            'motif_rejet' => $request->motif_rejet,
            'approved_by' => Auth::id()
        ]);

       return redirect()->route('demande-approvisionnements.show', $demandeApprovisionnement)
            ->with('success', 'Demande d\'approvisionnement rejetée');
    }

    /**
     * Exporter une demande d'approvisionnement en PDF
     */
    public function exportPDF($id)
    {
        $demandeApprovisionnement = DemandeApprovisionnement::with([
            'user.bus', 
            'projet', 
            'approbateur', 
            'lignes.article.uniteMesure'
        ])->findOrFail($id);

        $buId = $demandeApprovisionnement->projet?->bu_id ?? (session('selected_bu') ? (int) session('selected_bu') : null);
        $pdfBranding = PdfBranding::forBu($buId);
        
        $pdf = PDF::loadView('demande_approvisionnements.pdf', compact('demandeApprovisionnement', 'pdfBranding'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->download('Demande_Approvisionnement_' . $demandeApprovisionnement->reference . '.pdf');
    }
}