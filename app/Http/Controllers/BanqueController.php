<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BanqueController extends Controller
{
    public function index()
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $banques = Banque::where('bu_id', $buId)->get();

        return view('banques.index', compact('banques'));
    }

    public function create()
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        return view('banques.create');
    }

    public function store(Request $request)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banques', 'nom')->where(fn ($query) => $query->where('bu_id', $buId)),
            ],
            'solde_initial' => 'nullable|numeric',
            'code_banque' => 'nullable|string|max:255',
            'code_guichet' => 'nullable|string|max:255',
            'numero_compte' => 'nullable|string|max:255',
            'cle_rib' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'code_swift' => 'nullable|string|max:255',
            'domiciliation' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255'
        ]);

        Banque::create(array_merge($request->all(), ['bu_id' => $buId]));

        return redirect()->route('banques.index')->with('success', 'Banque ajoutée avec succès.');
    }

    public function edit(Banque $banque)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $banque->bu_id !== (int) $buId) {
            abort(404);
        }

        return view('banques.edit', compact('banque'));
    }

    public function update(Request $request, Banque $banque)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $banque->bu_id !== (int) $buId) {
            abort(404);
        }

        $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('banques', 'nom')
                    ->where(fn ($query) => $query->where('bu_id', $buId))
                    ->ignore($banque->id),
            ],
            'solde_initial' => 'nullable|numeric',
            'code_banque' => 'nullable|string|max:255',
            'code_guichet' => 'nullable|string|max:255',
            'numero_compte' => 'nullable|string|max:255',
            'cle_rib' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'code_swift' => 'nullable|string|max:255',
            'domiciliation' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255'
        ]);

        $banque->update($request->except('bu_id'));

        return redirect()->route('banques.index')->with('success', 'Banque mise à jour.');
    }

    public function destroy(Banque $banque)
    {
        $buId = session('selected_bu');
        if (! $buId) {
            return redirect()->route('select.bu')->withErrors(['error' => 'Veuillez sélectionner un bus avant d\'accéder à cette page.']);
        }

        if ((int) $banque->bu_id !== (int) $buId) {
            abort(404);
        }

        $banque->delete();
        return redirect()->route('banques.index')->with('success', 'Banque supprimée.');
    }
}
