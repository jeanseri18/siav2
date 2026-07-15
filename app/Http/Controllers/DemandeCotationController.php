<?php

namespace App\Http\Controllers;

use App\Models\DemandeCotation;
use App\Models\LigneDemandeCotation;
use App\Models\FournisseurDemandeCotation;
use App\Models\DemandeAchat;
use App\Models\ClientFournisseur;
use App\Models\Article;
use App\Models\Reference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Concerns\ExportsListPdf;
use App\Support\PdfBranding;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DemandeCotationController extends Controller
{
    use ExportsListPdf;

    public function index()
    {
        $demandes = DemandeCotation::with(['user', 'demandeAchat', 'fournisseurs.fournisseur'])
            ->withCount('bonCommandes')
            ->get();
        return view('demande_cotations.index', compact('demandes'));
    }

    public function exportListePdf()
    {
        $demandes = DemandeCotation::with(['demandeAchat', 'fournisseurs'])
            ->orderByDesc('date_demande')
            ->get();

        $rows = [];
        foreach ($demandes as $demande) {
            $rows[] = [
                $demande->reference,
                $demande->date_demande?->format('d/m/Y') ?? '—',
                $demande->date_expiration?->format('d/m/Y') ?? '—',
                (string) $demande->fournisseurs->count(),
                $demande->demandeAchat?->reference ?? '—',
                $demande->statut ?? '—',
            ];
        }

        return $this->streamListPdf(
            'Liste des demandes de cotation',
            ['Référence', 'Date', 'Expiration', 'Nb fournisseurs', 'Demande achat', 'Statut'],
            $rows,
            'liste-demandes-cotation'
        );
    }

    public function create(Request $request)
    {
        if ($request->filled('demande_achat_id')) {
            if (DemandeCotation::where('demande_achat_id', $request->demande_achat_id)->exists()) {
                return redirect()->route('demande-achats.show', $request->demande_achat_id)
                    ->with('error', 'Une demande de cotation existe déjà pour cette demande d\'achat.');
            }
        }

        $demandesAchat = DemandeAchat::where('statut', 'approuvée')
            ->whereDoesntHave('demandeCotations')
            ->get();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $articles = Article::all();

        return view('demande_cotations.create', compact('demandesAchat', 'fournisseurs', 'articles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_demande' => 'required|date',
            'date_expiration' => 'required|date|after:date_demande',
            'demande_achat_id' => 'nullable|exists:demande_achats,id',
            'description' => 'nullable|string',
            'conditions_generales' => 'nullable|string',
            'fournisseur_id' => 'required|array',
            'fournisseur_id.*' => 'exists:client_fournisseurs,id',
            'designation' => 'required|array',
            'designation.*' => 'required|string',
            'article_id' => 'nullable|array',
            'article_id.*' => 'nullable|exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'unite_mesure' => 'required|array',
            'unite_mesure.*' => 'string',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

        if ($request->filled('demande_achat_id')) {
            if (DemandeCotation::where('demande_achat_id', $request->demande_achat_id)->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Une demande de cotation existe déjà pour cette demande d\'achat.');
            }
        }

        // Générer la référence
        $lastReference = Reference::where('nom', 'Code Demande Cotation')
            ->latest('created_at')
            ->first();
        
        // Générer la nouvelle référence
        $newReference = $lastReference ? $lastReference->ref : 'DC_0000';
        $newReference = 'DC_' . now()->format('YmdHis');

        // Créer la demande
        $demande = DemandeCotation::create([
            'reference' => $newReference,
            'date_demande' => $request->date_demande,
            'date_expiration' => $request->date_expiration,
            'demande_achat_id' => $request->demande_achat_id,
            'description' => $request->description,
            'conditions_generales' => $request->conditions_generales,
            'user_id' => Auth::id(),
            'statut' => 'en cours'
        ]);

        // Ajouter les fournisseurs
        foreach ($request->fournisseur_id as $fournisseurId) {
            FournisseurDemandeCotation::create([
                'demande_cotation_id' => $demande->id,
                'fournisseur_id' => $fournisseurId
            ]);
        }

        // Ajouter les lignes de la demande
        for ($i = 0; $i < count($request->designation); $i++) {
            if ($request->designation[$i] && $request->quantite[$i] > 0) {
                LigneDemandeCotation::create([
                    'demande_cotation_id' => $demande->id,
                    'article_id' => $request->article_id[$i] ?? null,
                    'designation' => $request->designation[$i],
                    'quantite' => $request->quantite[$i],
                    'unite_mesure' => $request->unite_mesure[$i],
                    'specifications' => $request->specifications[$i] ?? null
                ]);
            }
        }

        // Enregistrer la référence
        Reference::create([
            'nom' => 'Code Demande Cotation',
            'ref' => $newReference
        ]);

        return redirect()->route('demande-cotations.index')
            ->with('success', 'Demande de cotation créée avec succès');
    }

    public function show(DemandeCotation $demandeCotation)
    {
        $demandeCotation->load(['user', 'demandeAchat', 'fournisseurs.fournisseur', 'lignes.article']);
        $demandeCotation->loadCount('bonCommandes');
        return view('demande_cotations.show', compact('demandeCotation'));
    }

    public function edit(DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en cours');
        }

        $demandesAchat = DemandeAchat::where('statut', 'approuvée')
            ->where(function ($q) use ($demandeCotation) {
                $q->whereDoesntHave('demandeCotations');
                if ($demandeCotation->demande_achat_id) {
                    $q->orWhere('id', $demandeCotation->demande_achat_id);
                }
            })
            ->get();
        $fournisseurs = ClientFournisseur::where('type', 'Fournisseur')->where('statut', 'Actif')->get();
        $articles = Article::all();

        $demandeCotation->load(['fournisseurs.fournisseur', 'lignes.article']);

        return view('demande_cotations.edit', compact('demandeCotation', 'demandesAchat', 'fournisseurs', 'articles'));
    }

    public function update(Request $request, DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de modifier une demande qui n\'est pas en cours');
        }

        $request->validate([
            'date_demande' => 'required|date',
            'date_expiration' => 'required|date|after:date_demande',
            'demande_achat_id' => 'nullable|exists:demande_achats,id',
            'description' => 'nullable|string',
            'conditions_generales' => 'nullable|string',
            'fournisseur_id' => 'required|array',
            'fournisseur_id.*' => 'exists:client_fournisseurs,id',
            'designation' => 'required|array',
            'designation.*' => 'required|string',
            'article_id' => 'nullable|array',
            'article_id.*' => 'nullable|exists:articles,id',
            'quantite' => 'required|array',
            'quantite.*' => 'integer|min:1',
            'unite_mesure' => 'required|array',
            'unite_mesure.*' => 'string',
            'specifications' => 'nullable|array',
            'specifications.*' => 'nullable|string'
        ]);

        if ($request->filled('demande_achat_id')) {
            $conflit = DemandeCotation::where('demande_achat_id', $request->demande_achat_id)
                ->where('id', '!=', $demandeCotation->id)
                ->exists();
            if ($conflit) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Une autre demande de cotation est déjà liée à cette demande d\'achat.');
            }
        }

       // Mettre à jour la demande
        $demandeCotation->update([
            'date_demande' => $request->date_demande,
            'date_expiration' => $request->date_expiration,
            'demande_achat_id' => $request->demande_achat_id,
            'description' => $request->description,
            'conditions_generales' => $request->conditions_generales
        ]);

        // Supprimer les anciennes lignes
        $demandeCotation->lignes()->delete();

        // Supprimer les anciens fournisseurs
        $demandeCotation->fournisseurs()->delete();

        // Ajouter les nouveaux fournisseurs
        foreach ($request->fournisseur_id as $fournisseurId) {
            FournisseurDemandeCotation::create([
                'demande_cotation_id' => $demandeCotation->id,
                'fournisseur_id' => $fournisseurId
            ]);
        }

        // Ajouter les nouvelles lignes
        for ($i = 0; $i < count($request->designation); $i++) {
            if ($request->designation[$i] && $request->quantite[$i] > 0) {
                LigneDemandeCotation::create([
                    'demande_cotation_id' => $demandeCotation->id,
                    'article_id' => $request->article_id[$i] ?? null,
                    'designation' => $request->designation[$i],
                    'quantite' => $request->quantite[$i],
                    'unite_mesure' => $request->unite_mesure[$i],
                    'specifications' => $request->specifications[$i] ?? null
                ]);
            }
        }

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Demande de cotation mise à jour avec succès');
    }

    public function destroy(DemandeCotation $demandeCotation)
    {
        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.index')
                ->with('error', 'Impossible de supprimer une demande qui n\'est pas en cours');
        }

        // Supprimer les lignes de la demande
        $demandeCotation->lignes()->delete();
        
        // Supprimer les fournisseurs de la demande
        $demandeCotation->fournisseurs()->delete();
        
        // Supprimer la demande
        $demandeCotation->delete();

        return redirect()->route('demande-cotations.index')
            ->with('success', 'Demande de cotation supprimée avec succès');
    }

    public function terminate(DemandeCotation $demandeCotation)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour terminer cette demande.');
        }

        if (! in_array($demandeCotation->statut, ['en cours', 'validée'], true)) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Cette demande ne peut pas être terminée');
        }

        // Mettre à jour le statut de la demande
        $demandeCotation->update([
            'statut' => 'terminée'
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Demande de cotation terminée avec succès');
    }

    public function cancel(DemandeCotation $demandeCotation)
    {
        // Vérifier les permissions basées sur le rôle
        $rolesAutorises = ['chef_projet', 'conducteur_travaux', 'acheteur', 'admin', 'dg'];
        if (!in_array(Auth::user()->role, $rolesAutorises)) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour annuler cette demande.');
        }

        if (! in_array($demandeCotation->statut, ['en cours', 'validée'], true)) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Cette demande ne peut pas être annulée');
        }

        // Mettre à jour le statut de la demande
        $demandeCotation->update([
            'statut' => 'annulée'
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Demande de cotation annulée avec succès');
    }

    public function saveFournisseurResponse(Request $request, DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation)
    {
        $this->assertFournisseurLigneBelongs($demandeCotation, $fournisseurDemandeCotation);

        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible d\'enregistrer la réponse d\'un fournisseur pour une demande qui n\'est pas en cours');
        }

        $request->validate([
            'date_reponse' => 'required|date',
            'montant_total' => 'required|numeric|min:0',
            'commentaire' => 'nullable|string',
            'devis_fichier' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240',
        ]);

        $data = [
            'repondu' => true,
            'date_reponse' => $request->date_reponse,
            'montant_total' => $request->montant_total,
            'commentaire' => $request->commentaire,
        ];

        if ($request->hasFile('devis_fichier')) {
            if ($fournisseurDemandeCotation->devis_fichier) {
                Storage::disk('public')->delete($fournisseurDemandeCotation->devis_fichier);
            }
            $file = $request->file('devis_fichier');
            $data['devis_fichier'] = $file->store('cotations_devis', 'public');
            $data['devis_fichier_nom'] = $file->getClientOriginalName();
        }

        $fournisseurDemandeCotation->update($data);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Réponse du fournisseur enregistrée avec succès');
    }

    /**
     * Afficher ou télécharger le devis / pièce jointe fournisseur (PDF / image).
     * Utiliser ?download=1 pour forcer le téléchargement (en-tête Content-Disposition: attachment).
     */
    public function showFournisseurDevis(Request $request, DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation)
    {
        $this->assertFournisseurLigneBelongs($demandeCotation, $fournisseurDemandeCotation);

        if (! $fournisseurDemandeCotation->devis_fichier) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($fournisseurDemandeCotation->devis_fichier)) {
            abort(404);
        }

        $nom = $fournisseurDemandeCotation->devis_fichier_nom ?: basename($fournisseurDemandeCotation->devis_fichier);

        if ($request->boolean('download')) {
            return Storage::disk('public')->download($fournisseurDemandeCotation->devis_fichier, $nom);
        }

        return Storage::disk('public')->response($fournisseurDemandeCotation->devis_fichier, $nom, [], 'inline');
    }

    /**
     * Ajouter ou remplacer le devis d’une réponse déjà enregistrée.
     */
    public function uploadFournisseurDevis(Request $request, DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation)
    {
        $this->assertFournisseurLigneBelongs($demandeCotation, $fournisseurDemandeCotation);

        if (! $this->statutPermetMiseAJourPieceJointeFournisseur($demandeCotation)) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de modifier la pièce jointe : la demande est annulée ou n\'est plus modifiable.');
        }

        if (! $fournisseurDemandeCotation->repondu) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Enregistrez d\'abord la réponse du fournisseur (montant, date).');
        }

        $request->validate([
            'devis_fichier' => 'required|file|mimes:pdf,jpeg,jpg,png|max:10240',
        ]);

        if ($fournisseurDemandeCotation->devis_fichier) {
            Storage::disk('public')->delete($fournisseurDemandeCotation->devis_fichier);
        }

        $file = $request->file('devis_fichier');
        $fournisseurDemandeCotation->update([
            'devis_fichier' => $file->store('cotations_devis', 'public'),
            'devis_fichier_nom' => $file->getClientOriginalName(),
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Pièce jointe du fournisseur enregistrée avec succès.');
    }

    /**
     * Supprimer la pièce jointe (fichier sur disque + champs en base).
     */
    public function destroyFournisseurDevis(DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation)
    {
        $this->assertFournisseurLigneBelongs($demandeCotation, $fournisseurDemandeCotation);

        if (! $this->statutPermetMiseAJourPieceJointeFournisseur($demandeCotation)) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de supprimer la pièce jointe : la demande est annulée ou n\'est plus modifiable.');
        }

        if (! $fournisseurDemandeCotation->repondu) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Aucune réponse fournisseur enregistrée pour cette ligne.');
        }

        if ($fournisseurDemandeCotation->devis_fichier) {
            Storage::disk('public')->delete($fournisseurDemandeCotation->devis_fichier);
        }

        $fournisseurDemandeCotation->update([
            'devis_fichier' => null,
            'devis_fichier_nom' => null,
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Pièce jointe supprimée.');
    }

    public function selectFournisseur(Request $request, DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation)
    {
        $this->assertFournisseurLigneBelongs($demandeCotation, $fournisseurDemandeCotation);

        if ($demandeCotation->statut !== 'en cours') {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de sélectionner un fournisseur pour une demande qui n\'est pas en cours');
        }

        if (!$fournisseurDemandeCotation->repondu) {
            return redirect()->route('demande-cotations.show', $demandeCotation)
                ->with('error', 'Impossible de sélectionner un fournisseur qui n\'a pas répondu');
        }

        // Mettre à jour tous les fournisseurs (désélectionner)
        $demandeCotation->fournisseurs()->update([
            'retenu' => false
        ]);

        // Sélectionner le fournisseur
        $fournisseurDemandeCotation->update([
            'retenu' => true
        ]);

        return redirect()->route('demande-cotations.show', $demandeCotation)
            ->with('success', 'Fournisseur sélectionné avec succès');
    }

    /**
     * Exporter une demande de cotation en PDF
     */
    public function exportPDF($id)
    {
        $demandeCotation = DemandeCotation::with([
            'user.bus', 
            'demandeAchat.projet', 
            'lignes.article', 
            'fournisseurs.fournisseur'
        ])->findOrFail($id);

        $buId = $demandeCotation->demandeAchat?->projet?->bu_id ?? (session('selected_bu') ? (int) session('selected_bu') : null);
        $pdfBranding = PdfBranding::forBu($buId);
        
        $pdf = PDF::loadView('demande_cotations.pdf', compact('demandeCotation', 'pdfBranding'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->download('Demande_Cotation_' . $demandeCotation->reference . '.pdf');
    }

    protected function assertFournisseurLigneBelongs(DemandeCotation $demandeCotation, FournisseurDemandeCotation $fournisseurDemandeCotation): void
    {
        abort_unless(
            (int) $fournisseurDemandeCotation->demande_cotation_id === (int) $demandeCotation->id,
            404
        );
    }

    /**
     * Statuts où l’on peut ajouter ou remplacer le fichier devis d’un fournisseur (réponse déjà enregistrée).
     */
    protected function statutPermetMiseAJourPieceJointeFournisseur(DemandeCotation $demandeCotation): bool
    {
        return in_array($demandeCotation->statut, ['en cours', 'validée', 'terminée'], true);
    }
}