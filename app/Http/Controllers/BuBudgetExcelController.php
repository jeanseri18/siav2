<?php

namespace App\Http\Controllers;

use App\Models\BuBudgetExcel;
use App\Models\BuBudgetExcelRow;
use App\Models\BuBudgetExcelValue;
use Illuminate\Http\Request;

class BuBudgetExcelController extends Controller
{
    public function index()
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $budgets = BuBudgetExcel::where('bu_id', $buId)->orderByDesc('annee')->get();

        return view('bu-budget.index', compact('budgets'));
    }

    public function store(Request $request)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $request->validate([
            'annee' => 'required|integer|min:2000|max:2100',
        ]);

        $budget = BuBudgetExcel::firstOrCreate([
            'bu_id' => $buId,
            'annee' => (int) $request->annee,
        ]);

        return redirect()->route('bu-budget.show', $budget);
    }

    public function show(Request $request, BuBudgetExcel $budget)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $budget->bu_id !== (int) $buId) {
            abort(404);
        }

        $tab = (string) $request->query('tab', 'hypotheses');

        $rowsBySheet = BuBudgetExcelRow::where('bu_budget_excel_id', $budget->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('sheet')
            ->map(fn ($rows) => $rows->values())
            ->all();

        $calc = $this->calculs($rowsBySheet);
        $seuilCommentaire = (string) BuBudgetExcelValue::where('bu_budget_excel_id', $budget->id)
            ->where('sheet', 'seuil_rentabilite')
            ->where('key', 'commentaire')
            ->value('value_text');
        $calc['seuil_rentabilite']['commentaire'] = $seuilCommentaire;

        return view('bu-budget.show', compact(
            'budget',
            'tab',
            'rowsBySheet',
            'calc',
            'seuilCommentaire'
        ));
    }

    public function saveSeuilCommentaire(Request $request, BuBudgetExcel $budget)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $budget->bu_id !== (int) $buId) {
            abort(404);
        }

        $request->validate([
            'commentaire' => 'nullable|string|max:5000',
        ]);

        BuBudgetExcelValue::updateOrCreate(
            [
                'bu_budget_excel_id' => $budget->id,
                'sheet' => 'seuil_rentabilite',
                'key' => 'commentaire',
            ],
            [
                'value_text' => $request->input('commentaire'),
                'value_decimal' => null,
            ]
        );

        return redirect()->route('bu-budget.show', ['budget' => $budget->id, 'tab' => 'seuil_rentabilite'])->with('success', 'Commentaire enregistré.');
    }

    public function addRow(Request $request, BuBudgetExcel $budget)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $budget->bu_id !== (int) $buId) {
            abort(404);
        }

        $sheet = (string) $request->sheet;
        $rules = [
            'sheet' => 'required|string|max:50',
            'amount_decimal' => 'required|numeric',
            'tab' => 'nullable|string|max:50',
        ];

        if ($sheet === 'hypotheses') {
            $rules['reference'] = 'required|string|max:255';
            $rules['parametre'] = 'required|numeric';
        } else {
            $rules['label'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $maxSort = (int) BuBudgetExcelRow::where('bu_budget_excel_id', $budget->id)->where('sheet', $sheet)->max('sort_order');

        $label = $sheet === 'hypotheses'
            ? (string) $request->parametre
            : (string) $request->label;

        BuBudgetExcelRow::create([
            'bu_budget_excel_id' => $budget->id,
            'sheet' => $sheet,
            'reference' => $sheet === 'hypotheses' ? (string) $request->reference : null,
            'parametre' => $sheet === 'hypotheses' ? (string) $request->parametre : $label,
            'label' => $label,
            'amount_decimal' => $request->amount_decimal,
            'sort_order' => $maxSort + 1,
        ]);

        $tab = (string) $request->input('tab', $sheet);

        return redirect()->route('bu-budget.show', ['budget' => $budget->id, 'tab' => $tab])->with('success', 'Ligne ajoutée.');
    }

    public function updateRow(Request $request, BuBudgetExcel $budget, BuBudgetExcelRow $row)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $budget->bu_id !== (int) $buId) {
            abort(404);
        }

        if ($row->bu_budget_excel_id !== $budget->id) {
            abort(404);
        }

        $rules = [
            'amount_decimal' => 'required|numeric',
            'tab' => 'nullable|string|max:50',
        ];

        if ($row->sheet === 'hypotheses') {
            $rules['reference'] = 'required|string|max:255';
            $rules['parametre'] = 'required|numeric';
        } else {
            $rules['label'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $row->update([
            'reference' => $row->sheet === 'hypotheses' ? (string) $request->reference : $row->reference,
            'parametre' => $row->sheet === 'hypotheses' ? (string) $request->parametre : (string) $request->label,
            'label' => $row->sheet === 'hypotheses' ? (string) $request->parametre : (string) $request->label,
            'amount_decimal' => $request->amount_decimal,
        ]);

        $tab = (string) $request->input('tab', $row->sheet);

        return redirect()->route('bu-budget.show', ['budget' => $budget->id, 'tab' => $tab])->with('success', 'Ligne mise à jour.');
    }

    public function deleteRow(Request $request, BuBudgetExcel $budget, BuBudgetExcelRow $row)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $budget->bu_id !== (int) $buId) {
            abort(404);
        }

        if ($row->bu_budget_excel_id !== $budget->id) {
            abort(404);
        }

        $tab = (string) $request->input('tab', $row->sheet);

        $row->delete();

        return redirect()->route('bu-budget.show', ['budget' => $budget->id, 'tab' => $tab])->with('success', 'Ligne supprimée.');
    }

    private function sumRows(array $rowsBySheet, string $sheet): float
    {
        if (! array_key_exists($sheet, $rowsBySheet)) {
            return 0.0;
        }

        return (float) collect($rowsBySheet[$sheet])->sum('amount_decimal');
    }

    private function calculs(array $rowsBySheet): array
    {
        $caRows = [];
        $hypotheses = $rowsBySheet['hypotheses'] ?? [];
        foreach ($hypotheses as $row) {
            $ref = trim((string) $row->reference);
            if ($ref === '') {
                continue;
            }

            $nombre = (float) $row->parametre;
            $mu = (float) $row->amount_decimal;

            $caRows[] = [
                'type_travaux' => $ref,
                'nombre' => $nombre,
                'montant_unitaire' => $mu,
                'montant_annuel' => $nombre * $mu,
            ];
        }
        usort($caRows, fn ($a, $b) => strcmp((string) $a['type_travaux'], (string) $b['type_travaux']));

        $totalCa = array_reduce($caRows, fn ($carry, $r) => $carry + (float) $r['montant_annuel'], 0.0);

        $totalCoutChantiers = $this->sumRows($rowsBySheet, 'cout_chantiers');
        $totalCoutVentes = $this->sumRows($rowsBySheet, 'cout_ventes');
        $totalChargesFixes = $this->sumRows($rowsBySheet, 'charges_fixes');
        $totalInvestDepart = $this->sumRows($rowsBySheet, 'investissements_depart');
        $totalFinancement = $this->sumRows($rowsBySheet, 'plan_financement_initial');

        $resultatNet = $totalCa - $totalCoutChantiers - $totalChargesFixes;

        $tauxMarge = 0.0;
        if ($totalCa > 0) {
            $tauxMarge = ($totalCa - $totalCoutChantiers) / $totalCa;
        }

        $seuilRentabilite = 0.0;
        if ($tauxMarge > 0) {
            $seuilRentabilite = $totalChargesFixes / $tauxMarge;
        }

        return [
            'ca' => [
                'rows' => $caRows,
                'total' => $totalCa,
            ],
            'totaux' => [
                'cout_chantiers' => $totalCoutChantiers,
                'cout_ventes' => $totalCoutVentes,
                'charges_fixes' => $totalChargesFixes,
                'investissements_depart' => $totalInvestDepart,
                'financement_initial' => $totalFinancement,
            ],
            'resultat_previsionnel' => [
                'ca_total' => $totalCa,
                'cout_chantiers' => $totalCoutChantiers,
                'charges_fixes' => $totalChargesFixes,
                'cout_ventes' => $totalCoutVentes,
                'resultat_net' => $resultatNet,
            ],
            'seuil_rentabilite' => [
                'charges_fixes' => $totalChargesFixes,
                'taux_marge' => $tauxMarge,
                'seuil' => $seuilRentabilite,
                'commentaire' => '',
            ],
            'plan_financement_initial' => [
                'total' => $totalFinancement,
                'ecart_vs_invest' => $totalFinancement - $totalInvestDepart,
            ],
        ];
    }
}
