<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BU;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Afficher le formulaire d'inscription
    public function showRegisterForm()
    {
        $bus = BU::all();
        return view('auth.register', compact('bus'));
    }

    // Inscription d'un utilisateur avec association à une ou plusieurs BU
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'bus' => 'required|array', // Les BU sélectionnées
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'utilisateur',
            'status' => 'actif'
        ]);

        // Associer l'utilisateur aux BU sélectionnées
        $user->bus()->attach($request->bus);

        return redirect()->route('login')->with('success', 'Compte créé avec succès.');
    }

    // Afficher le formulaire de connexion
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Connexion de l'utilisateur
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->bus->count() > 1) {
                return redirect()->route('select.bu');
            } elseif ($user->bus->count() == 1) {
                session(['selected_bu' => $user->bus->first()->id]);
                return redirect()->route('menu');
            }
        }

        return back()->withErrors(['email' => 'Les identifiants ne sont pas valides.']);
    }

    // Afficher la page de sélection de BU après connexion
    public function showSelectBU()
    {
        $bus = Auth::user()->bus;
        return view('auth.select_bu', compact('bus'));
    }

    // Enregistrer la BU sélectionnée dans la session
    public function selectBU(Request $request)
    {
        $request->validate([
            'bu_id' => 'required|exists:bus,id'
        ]);

        session(['selected_bu' => $request->bu_id]);
        return redirect()->route('menu');
    }

    // Déconnexion
    public function logout()
    {
        Auth::logout();
        session()->forget('selected_bu');
        return redirect()->route('login');
    }
}
