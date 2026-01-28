<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Projet;
use App\Models\Article;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Affiche la liste des documents.
     */
    public function index()
    {
        $projet_id = session('projet_id');
        $projets = Projet::all();
        $articles = Article::all();
        // Récupérer uniquement les documents du projet en session
        $documents = Document::where('id_projet', $projet_id)->get();
    
        return view('documents.index', compact('documents','projets','articles'));
    }
    public function index_contrat()
    {
        $projet_id = session('projet_id');
        $projets = Projet::all();
        $articles = Article::all();
        
        // Récupérer le contrat du projet en session
        $projet = Projet::find($projet_id);
        $contrats_ids = $projet ? $projet->contrats()->pluck('id') : collect();
        
        // Récupérer uniquement les documents liés aux contrats du projet
        $documents = Document::whereIn('id_contrat', $contrats_ids)->get();
    
        return view('documents.index_contrat', compact('documents','projets','articles'));
    }

    /**
     * Affiche le formulaire d'ajout d'un document.
     */
    public function create()
    {    $projets = Projet::all();
        $articles = Article::all();
        return view('documents.create',compact('projets','articles'));
    }

    /**
     * Enregistre un document en base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
       
            'fichier' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'id_projet' => 'nullable|exists:projets,id',
            'id_contrat' => 'nullable|exists:contrats,id',
            'id_facture' => 'nullable|exists:factures,id',
        ]);

        // Stockage du fichier
        $chemin = $request->file('fichier')->store('documents', 'public');
        $projet_id = session('projet_id');
        // Création du document
        Document::create([
            'nom' => $request->nom,
         
            'chemin' => $chemin,
            'id_projet' => $projet_id,
            'id_contrat' => $request->id_contrat,
            'id_facture' => $request->id_facture,
        ]);

        return redirect()->route('documents.index')->with('success', 'Document ajouté avec succès.');
    }
    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }
    /**
     * Supprime un document.
     */
    public function destroy(Document $document)
    {
        // Supprimer le fichier
        \Storage::disk('public')->delete($document->chemin);

        // Supprimer l'entrée en base
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document supprimé avec succès.');
    }
}
