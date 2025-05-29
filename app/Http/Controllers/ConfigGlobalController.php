<?php

namespace App\Http\Controllers;

use App\Models\ConfigGlobal;
use App\Models\BU;
use Illuminate\Http\Request;

class ConfigGlobalController extends Controller
{
    public function index()
    {
        $configs = ConfigGlobal::with('businessUnit')->get();
        return view('config_global.index', compact('configs'));
    }

    public function create()
    {
        $businessUnits = BU::whereDoesntHave('configGlobal')->get(); // Exclure celles ayant déjà une config
        if ($businessUnits->isEmpty()) {
            return redirect()->route('config-global.index')->with('error', 'Toutes les Business Units ont déjà une configuration.');
        }
        return view('config_global.create', compact('businessUnits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entete' => 'required|string|max:255',
            'numdepatfacture' => 'required|string|max:255',
            'pieddepage' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_bu' => 'required|exists:bus,id|unique:config_global,id_bu',
        ]);

        $data = $request->all();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        ConfigGlobal::create($data);

        return redirect()->route('config-global.index')->with('success', 'Configuration ajoutée avec succès.');
    }

    public function edit(ConfigGlobal $configGlobal)
    {
        return view('config_global.edit', compact('configGlobal'));
    }

    public function update(Request $request, ConfigGlobal $configGlobal)
    {
        $request->validate([
            'entete' => 'required|string|max:255',
            'numdepatfacture' => 'required|string|max:255',
            'pieddepage' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $configGlobal->update($data);

        return redirect()->route('config-global.index')->with('success', 'Configuration mise à jour avec succès.');
    }

    public function destroy(ConfigGlobal $configGlobal)
    {
        $configGlobal->delete();
        return redirect()->route('config-global.index')->with('success', 'Configuration supprimée avec succès.');
    }
}
