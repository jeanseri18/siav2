<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeController extends Controller
{
    public function index()
    {
        $employes = User::where('role', '!=', 'admin')
                       ->orderBy('nom')
                       ->orderBy('prenom')
                       ->paginate(15);
        
        return view('employes.index', compact('employes'));
    }

    public function create()
    {
        $roles = [
            'chef_projet' => 'Chef de Projet',
            'conducteur_travaux' => 'Conducteur de Travaux',
            'chef_chantier' => 'Chef de Chantier',
            'comptable' => 'Comptable',
            'magasinier' => 'Magasinier',
            'acheteur' => 'Acheteur',
            'controleur_gestion' => 'Contrôleur de Gestion',
            'secretaire' => 'Secrétaire',
            'chauffeur' => 'Chauffeur',
            'gardien' => 'Gardien',
            'employe' => 'Employé'
        ];
        
        return view('employes.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:chef_projet,conducteur_travaux,chef_chantier,comptable,magasinier,acheteur,controleur_gestion,secretaire,chauffeur,gardien,employe',
            'poste' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'date_embauche' => 'nullable|date',
            'salaire' => 'nullable|numeric|min:0',
            'numero_cnss' => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date|before:today',
            'sexe' => 'nullable|in:M,F',
            'lieu_naissance' => 'nullable|string|max:255',
            'nationalite' => 'nullable|string|max:100',
            'situation_matrimoniale' => 'nullable|in:celibataire,marie,divorce,veuf',
            'numero_cni' => 'nullable|string|max:50',
            'numero_passeport' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:actif,inactif'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Gestion de l'upload de photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
            $validated['photo'] = $photoPath;
        }

        User::create($validated);

        return redirect()->route('employes.index')
                        ->with('success', 'Employé créé avec succès.');
    }

    public function show(User $employe)
    {
        return view('employes.show', compact('employe'));
    }

    public function edit(User $employe)
    {
        $roles = [
            'chef_projet' => 'Chef de Projet',
            'conducteur_travaux' => 'Conducteur de Travaux',
            'chef_chantier' => 'Chef de Chantier',
            'comptable' => 'Comptable',
            'magasinier' => 'Magasinier',
            'acheteur' => 'Acheteur',
            'controleur_gestion' => 'Contrôleur de Gestion',
            'secretaire' => 'Secrétaire',
            'chauffeur' => 'Chauffeur',
            'gardien' => 'Gardien',
            'employe' => 'Employé'
        ];
        
        return view('employes.edit', compact('employe', 'roles'));
    }

    public function update(Request $request, User $employe)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employe->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:chef_projet,conducteur_travaux,chef_chantier,comptable,magasinier,acheteur,controleur_gestion,secretaire,chauffeur,gardien,employe',
            'poste' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'date_embauche' => 'nullable|date',
            'salaire' => 'nullable|numeric|min:0',
            'numero_cnss' => 'nullable|string|max:50',
            'date_naissance' => 'nullable|date|before:today',
            'sexe' => 'nullable|in:M,F',
            'lieu_naissance' => 'nullable|string|max:255',
            'nationalite' => 'nullable|string|max:100',
            'situation_matrimoniale' => 'nullable|in:celibataire,marie,divorce,veuf',
            'numero_cni' => 'nullable|string|max:50',
            'numero_passeport' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:actif,inactif'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Gestion de l'upload de photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($employe->photo && Storage::disk('public')->exists($employe->photo)) {
                Storage::disk('public')->delete($employe->photo);
            }

            // Stocker la nouvelle photo
            $photoPath = $request->file('photo')->store('profiles', 'public');
            $validated['photo'] = $photoPath;
        }

        $employe->update($validated);

        return redirect()->route('employes.index')
                        ->with('success', 'Employé mis à jour avec succès.');
    }

    public function destroy(User $employe)
    {
        // Vérifier si l'employé est assigné à des projets
        if ($employe->projetsCommeChefProjet()->count() > 0 || $employe->projetsCommeConducteurTravaux()->count() > 0) {
            return redirect()->route('employes.index')
                            ->with('error', 'Impossible de supprimer cet employé car il est assigné à des projets.');
        }

        $employe->delete();

        return redirect()->route('employes.index')
                        ->with('success', 'Employé supprimé avec succès.');
    }
    
    // Méthodes API pour les sélections dans les formulaires
    public function getConducteursTravaux()
    {
        $conducteurs = User::conducteursTravaux()->actifs()->get(['id', 'nom', 'prenom']);
        return response()->json($conducteurs);
    }
    
    public function getChefsProjets()
    {
        $chefs = User::chefsProjets()->actifs()->get(['id', 'nom', 'prenom']);
        return response()->json($chefs);
    }
}