<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BU;
use App\Models\BUAssociat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Show the list of users
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    // Show the form to create a new user
    public function create()
    {
        $buses = BU::all();
        $roles = User::roleOptionsForUserManagement();

        return view('users.create', compact('buses', 'roles'));
    }

    // Store a new user
    public function store(Request $request)
    {
        $roleKeys = array_keys(User::roleOptionsForUserManagement());
        $request->validate(
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => ['required', Rule::in($roleKeys)],
                'status' => 'required|in:actif,inactif',
                'buses' => 'required|array|min:1',
                'buses.*' => 'exists:bus,id',
            ],
            [
                'buses.required' => 'Cochez au moins une unité (BU) pour que l’utilisateur puisse se connecter correctement.',
                'buses.min' => 'Cochez au moins une unité (BU) pour que l’utilisateur puisse se connecter correctement.',
            ]
        );

        $user = new User();
        $user->nom = $request->name;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->status = $request->status;
        $user->save();

        // Assign selected buses
        if ($request->has('buses')) {
            foreach ($request->buses as $busId) {
                BUAssociat::create([
                    'bu_id' => $busId,
                    'user_id' => $user->id,
                ]);
            }
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    // Show the form to edit a user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $buses = BU::all();
        $assignedBuses = $user->bus()->pluck('bu_id')->toArray();
        $roles = User::roleOptionsForUserManagement();

        return view('users.edit', compact('user', 'buses', 'assignedBuses', 'roles'));
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $roleKeys = array_keys(User::roleOptionsForUserManagement());
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => ['required', Rule::in($roleKeys)],
            'status' => 'required|in:actif,inactif',
        ]);

        $user = User::findOrFail($id);
        $user->nom = $request->name;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        $user->save();

        // Update the buses assigned to the user
        BUAssociat::where('user_id', $id)->delete(); // Remove old bus associations

        if ($request->has('buses')) {
            foreach ($request->buses as $busId) {
                BUAssociat::create([
                    'bu_id' => $busId,
                    'user_id' => $user->id,
                ]);
            }
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Also delete bus associations
        BUAssociat::where('user_id', $id)->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
