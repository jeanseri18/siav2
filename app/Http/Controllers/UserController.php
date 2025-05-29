<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BU;
use App\Models\BUAssociat;
use Illuminate\Http\Request;

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
        $buses = BU::all(); // Get all bus data to assign
        return view('users.create', compact('buses'));
    }

    // Store a new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:utilisateur,admin',
            'status' => 'required|in:actif,inactif',
        ]);

        $user = new User();
        $user->name = $request->name;
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
        $buses = Bu::all(); // Get all bus data to assign
        $assignedBuses = $user->bus()->pluck('bu_id')->toArray(); // Get the buses assigned to the user
        return view('users.edit', compact('user', 'buses', 'assignedBuses'));
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:utilisateur,admin',
            'status' => 'required|in:actif,inactif',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
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
