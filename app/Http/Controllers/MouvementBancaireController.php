<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use App\Models\MouvementBancaire;
use App\Http\Controllers\Concerns\ExportsListPdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MouvementBancaireController extends Controller
{
    use ExportsListPdf;

    public function index(Request $request)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $banques = Banque::where('bu_id', $buId)->orderBy('nom')->get();

        $banqueId = $request->query('banque_id');
        $query = MouvementBancaire::with('banque')
            ->where('bu_id', $buId)
            ->orderByDesc('date_operation')
            ->orderByDesc('id');

        if ($banqueId) {
            $query->where('banque_id', $banqueId);
        }

        $mouvements = $query->paginate(15)->withQueryString();

        return view('banque.mouvements.index', compact('banques', 'banqueId', 'mouvements'));
    }

    public function exportListePdf(Request $request)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $banqueId = $request->query('banque_id');
        $query = MouvementBancaire::with('banque')
            ->where('bu_id', $buId)
            ->orderByDesc('date_operation')
            ->orderByDesc('id');

        if ($banqueId) {
            $query->where('banque_id', $banqueId);
        }

        $mouvements = $query->get();
        $rows = [];
        foreach ($mouvements as $mouvement) {
            $rows[] = [
                $mouvement->date_operation?->format('d/m/Y') ?? '—',
                $mouvement->banque?->nom ?? '—',
                $mouvement->libelle ?? '—',
                $mouvement->type ?? '—',
                number_format((float) ($mouvement->montant ?? 0), 0, ',', ' ').' FCFA',
                $mouvement->est_passe ? 'Passé' : 'En attente',
            ];
        }

        return $this->streamListPdf(
            'Liste des mouvements bancaires',
            ['Date', 'Banque', 'Libellé', 'Type', 'Montant', 'Statut'],
            $rows,
            'liste-mouvements-bancaires'
        );
    }

    public function soldes(Request $request)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $banques = Banque::where('bu_id', $buId)->orderBy('nom')->get();
        $banqueId = $request->query('banque_id');

        $banquesScope = $banqueId ? $banques->where('id', (int) $banqueId) : $banques;

        $rows = $banquesScope->map(function (Banque $banque) use ($buId) {
            $agg = $this->aggregateForBanque($buId, $banque->id, (float) $banque->solde_initial);

            return array_merge(['banque' => $banque], $agg);
        })->values();

        $totals = [
            'solde_initial' => (float) $rows->sum('solde_initial'),
            'entrees_prev' => (float) $rows->sum('entrees_prev'),
            'sorties_prev' => (float) $rows->sum('sorties_prev'),
            'solde_prev' => (float) $rows->sum('solde_prev'),
            'entrees_reel' => (float) $rows->sum('entrees_reel'),
            'sorties_reel' => (float) $rows->sum('sorties_reel'),
            'solde_reel' => (float) $rows->sum('solde_reel'),
        ];

        return view('banque.soldes.index', compact('banques', 'banqueId', 'rows', 'totals'));
    }

    public function rapprochement(Request $request)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $banques = Banque::where('bu_id', $buId)->orderBy('nom')->get();
        $banqueId = $request->query('banque_id');

        $query = MouvementBancaire::with('banque')
            ->where('bu_id', $buId)
            ->where('est_passe', false)
            ->orderByDesc('date_operation')
            ->orderByDesc('id');

        if ($banqueId) {
            $query->where('banque_id', $banqueId);
        }

        $mouvements = $query->get();

        return view('banque.rapprochement.index', compact('banques', 'banqueId', 'mouvements'));
    }

    public function create()
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $banques = Banque::where('bu_id', $buId)->orderBy('nom')->get();

        return view('banque.mouvements.create', compact('banques'));
    }

    public function store(Request $request)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        MouvementBancaire::create($this->validateAndAttributesForMouvement($request, (int) $buId, null));

        return redirect()->route('banque.mouvements.index')->with('success', 'Mouvement bancaire ajouté avec succès.');
    }

    public function edit(MouvementBancaire $mouvementBancaire)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $mouvementBancaire->bu_id !== (int) $buId) {
            abort(404);
        }

        $banques = Banque::where('bu_id', $buId)->orderBy('nom')->get();

        return view('banque.mouvements.edit', compact('banques', 'mouvementBancaire'));
    }

    public function update(Request $request, MouvementBancaire $mouvementBancaire)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $mouvementBancaire->bu_id !== (int) $buId) {
            abort(404);
        }

        $mouvementBancaire->update($this->validateAndAttributesForMouvement($request, (int) $buId, $mouvementBancaire));

        return redirect()->route('banque.mouvements.index')->with('success', 'Mouvement bancaire modifié avec succès.');
    }

    public function destroy(MouvementBancaire $mouvementBancaire)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $mouvementBancaire->bu_id !== (int) $buId) {
            abort(404);
        }

        $mouvementBancaire->delete();

        return redirect()->route('banque.mouvements.index')->with('success', 'Mouvement bancaire supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateAndAttributesForMouvement(Request $request, int $buId, ?MouvementBancaire $existingMouvement = null): array
    {
        $request->validate([
            'banque_id' => [
                'required',
                Rule::exists('banques', 'id')->where(fn ($query) => $query->where('bu_id', $buId)),
            ],
            'type' => ['required', Rule::in(['entree', 'sortie'])],
            'mode' => ['required', Rule::in(['virement', 'cheque', 'espece'])],
            'montant' => ['required', 'numeric', 'min:0.01'],
            'date_operation' => ['required', 'date'],
            'numero_piece' => ['nullable', 'string', 'max:255'],
            'cheque_barre' => ['nullable', 'boolean'],
            'beneficiaire' => ['nullable', 'string', 'max:255'],
            'libelle' => ['nullable', 'string'],
            'est_passe' => ['nullable', 'boolean'],
            'date_passage' => ['nullable', 'date'],
        ]);

        if ($request->input('type') === 'sortie') {
            $banqueId = (int) $request->input('banque_id');
            $montant = (float) $request->input('montant');
            $solde = $this->getSoldePrevisionnel($buId, $banqueId, $existingMouvement);

            if ($montant > $solde) {
                throw ValidationException::withMessages([
                    'montant' => sprintf(
                        'Solde insuffisant pour effectuer cette opération. Solde disponible : %s.',
                        number_format($solde, 0, ',', ' ')
                    ),
                ]);
            }
        }

        $estPasse = (bool) $request->boolean('est_passe');
        $datePassage = $request->input('date_passage');
        if ($estPasse && ! $datePassage) {
            $datePassage = $request->input('date_operation');
        }
        if (! $estPasse) {
            $datePassage = null;
        }

        $mode = (string) $request->input('mode');
        $numeroPiece = $mode === 'espece' ? null : $request->input('numero_piece');

        return [
            'bu_id' => $buId,
            'banque_id' => $request->input('banque_id'),
            'type' => $request->input('type'),
            'mode' => $mode,
            'montant' => $request->input('montant'),
            'date_operation' => $request->input('date_operation'),
            'numero_piece' => $numeroPiece,
            'cheque_barre' => $mode === 'cheque' ? (bool) $request->boolean('cheque_barre') : false,
            'beneficiaire' => $request->input('beneficiaire'),
            'libelle' => $request->input('libelle'),
            'est_passe' => $estPasse,
            'date_passage' => $datePassage,
        ];
    }

    public function toggleRapprochement(Request $request, MouvementBancaire $mouvementBancaire)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $mouvementBancaire->bu_id !== (int) $buId) {
            abort(404);
        }

        $request->validate([
            'est_passe' => ['required', 'boolean'],
        ]);

        $estPasse = (bool) $request->boolean('est_passe');

        $mouvementBancaire->update([
            'est_passe' => $estPasse,
            'date_passage' => $estPasse ? now()->toDateString() : null,
        ]);

        return redirect()->back()->with('success', 'Rapprochement mis à jour.');
    }

    private function getSoldePrevisionnel(int $buId, int $banqueId, ?MouvementBancaire $excludeMouvement = null): float
    {
        $banque = Banque::where('bu_id', $buId)->findOrFail($banqueId);
        $soldeInitial = (float) $banque->solde_initial;

        $baseAggregate = MouvementBancaire::where('bu_id', $buId)->where('banque_id', $banqueId);

        if ($excludeMouvement) {
            $baseAggregate->where('id', '!=', $excludeMouvement->id);
        }

        $entreesPrev = (float) (clone $baseAggregate)->where('type', 'entree')->sum('montant');
        $sortiesPrev = (float) (clone $baseAggregate)->where('type', 'sortie')->sum('montant');

        return $soldeInitial + $entreesPrev - $sortiesPrev;
    }

    private function aggregateForBanque(int $buId, int $banqueId, float $soldeInitial): array
    {
        $baseAggregate = MouvementBancaire::where('bu_id', $buId)->where('banque_id', $banqueId);

        $entreesPrev = (float) (clone $baseAggregate)->where('type', 'entree')->sum('montant');
        $sortiesPrev = (float) (clone $baseAggregate)->where('type', 'sortie')->sum('montant');
        $soldePrev = $soldeInitial + $entreesPrev - $sortiesPrev;

        $entreesReel = (float) (clone $baseAggregate)->where('type', 'entree')->where('est_passe', true)->sum('montant');
        $sortiesReel = (float) (clone $baseAggregate)->where('type', 'sortie')->where('est_passe', true)->sum('montant');
        $soldeReel = $soldeInitial + $entreesReel - $sortiesReel;

        return [
            'solde_initial' => $soldeInitial,
            'entrees_prev' => $entreesPrev,
            'sorties_prev' => $sortiesPrev,
            'solde_prev' => $soldePrev,
            'entrees_reel' => $entreesReel,
            'sorties_reel' => $sortiesReel,
            'solde_reel' => $soldeReel,
        ];
    }
}
