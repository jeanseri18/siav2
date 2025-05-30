<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\FraisGeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FraisGeneralController extends Controller
{
    /**
     * Afficher les frais généraux du contrat
     */
    public function index()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $contrat = Contrat::findOrFail($contratId);
        $fraisGeneraux = FraisGeneral::where('contrat_id', $contratId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calcul du montant total du contrat (pour référence)
        $montantContrat = $contrat->montant ?? 0;
        
        return view('frais_generaux.index', compact('contrat', 'fraisGeneraux', 'montantContrat'));
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $contrat = Contrat::findOrFail($contratId);
        
        // Suggérer un montant de base (montant du contrat)
        $montantSuggere = $contrat->montant ?? 0;
        
        return view('frais_generaux.create', compact('contrat', 'montantSuggere'));
    }
    
    /**
     * Enregistrer les nouveaux frais généraux
     */
    public function store(Request $request)
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $request->validate([
            'montant_base' => 'required|numeric|min:0',
            'pourcentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);
        
        // Calcul du montant total
        $montantTotal = $request->montant_base * ($request->pourcentage / 100);
        
        // Création des frais généraux
        $fraisGeneral = FraisGeneral::create([
            'contrat_id' => $contratId,
            'montant_base' => $request->montant_base,
            'pourcentage' => $request->pourcentage,
            'montant_total' => $montantTotal,
            'description' => $request->description,
            'date_calcul' => now(),
            'statut' => 'brouillon',
        ]);
        
        return redirect()->route('frais_generaux.index')
            ->with('success', 'Frais généraux créés avec succès.');
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $fraisGeneral = FraisGeneral::findOrFail($id);
        $contrat = $fraisGeneral->contrat;
        
        return view('frais_generaux.edit', compact('fraisGeneral', 'contrat'));
    }
    
    /**
     * Mettre à jour les frais généraux
     */
    public function update(Request $request, $id)
    {
        $fraisGeneral = FraisGeneral::findOrFail($id);
        
        $request->validate([
            'montant_base' => 'required|numeric|min:0',
            'pourcentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'statut' => 'required|in:brouillon,validé,archivé',
        ]);
        
        // Calcul du montant total
        $montantTotal = $request->montant_base * ($request->pourcentage / 100);
        
        // Mise à jour des frais généraux
        $fraisGeneral->update([
            'montant_base' => $request->montant_base,
            'pourcentage' => $request->pourcentage,
            'montant_total' => $montantTotal,
            'description' => $request->description,
            'statut' => $request->statut,
        ]);
        
        return redirect()->route('frais_generaux.index')
            ->with('success', 'Frais généraux mis à jour avec succès.');
    }
    
    /**
     * Supprimer les frais généraux
     */
    public function destroy($id)
    {
        $fraisGeneral = FraisGeneral::findOrFail($id);
        $fraisGeneral->delete();
        
        return redirect()->route('frais_generaux.index')
            ->with('success', 'Frais généraux supprimés avec succès.');
    }
    
    /**
     * Générer automatiquement les frais généraux
     * basés sur le montant du contrat
     */
    public function generate()
    {
        $contratId = session('contrat_id');
        
        if (!$contratId) {
            return redirect()->route('contrats.index')
                ->withErrors(['error' => 'Aucun contrat sélectionné. Veuillez d\'abord choisir un contrat.']);
        }
        
        $contrat = Contrat::findOrFail($contratId);
        
        if (!$contrat->montant) {
            return redirect()->route('frais_generaux.index')
                ->withErrors(['error' => 'Le montant du contrat n\'est pas défini. Impossible de calculer les frais généraux automatiquement.']);
        }
        
        // Création des frais généraux avec le montant du contrat
        $fraisGeneral = FraisGeneral::create([
            'contrat_id' => $contratId,
            'montant_base' => $contrat->montant,
            'pourcentage' => 10.00, // 10% par défaut
            'montant_total' => $contrat->montant * 0.10,
            'description' => 'Frais généraux calculés automatiquement (10% du montant du contrat)',
            'date_calcul' => now(),
            'statut' => 'brouillon',
        ]);
        
        return redirect()->route('frais_generaux.index')
            ->with('success', 'Frais généraux générés automatiquement avec succès.');
    }
    
    /**
     * Exporter les frais généraux en PDF
     */
    public function export($id)
    {
        $fraisGeneral = FraisGeneral::with('contrat')->findOrFail($id);
        
        $pdf = \PDF::loadView('frais_generaux.export', compact('fraisGeneral'));
        
        return $pdf->download('Frais_Generaux_' . $fraisGeneral->contrat->ref_contrat . '.pdf');
    }
}