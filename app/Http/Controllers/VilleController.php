<?php
namespace App\Http\Controllers;

use App\Models\Ville;
use App\Models\Pays;
use Illuminate\Http\Request;

class VilleController extends Controller
{
    public function index()
    {
        $villes = Ville::with('pays')->get();
        return view('villes.index', compact('villes'));
    }

    public function create()
    {
        $pays = Pays::all();
        return view('villes.create', compact('pays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'pays_id' => 'required|exists:pays,id',
        ]);
    
        Ville::create($request->all());
    
        return redirect()->route('villes.index')->with('success', 'Ville ajoutée avec succès');
    }
    public function edit($id)
    {
        $ville = Ville::findOrFail($id);
        $pays = Pays::all();
        return view('villes.edit', compact('ville', 'pays'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'id_pays' => 'required|exists:pays,id'
        ]);

        $ville = Ville::findOrFail($id);
        $ville->update($request->all());
        return redirect()->route('villes.index')->with('success', 'Ville mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $ville = Ville::findOrFail($id);
        $ville->delete();
        return redirect()->route('villes.index')->with('success', 'Ville supprimée avec succès.');
    }
}
