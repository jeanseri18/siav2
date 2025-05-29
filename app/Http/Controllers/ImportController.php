<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\RubriquesImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }
    
    public function create(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);
        
        try {
            Excel::import(new RubriquesImport, $request->file('file'));
            
            return redirect()->route('bpu.index')
                ->with('success', 'Importation réussie !');
        } 
        catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
        }
    }
}