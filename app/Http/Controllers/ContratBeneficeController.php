<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\LigneBeneficeParent;
use Illuminate\Http\Request;

class ContratBeneficeController extends Controller
{
    public function index($contratId)
    {
        $contrat = Contrat::findOrFail($contratId);
        
        // Récupérer tous les parents de lignes de bénéfice du contrat
        $ligneBeneficeParents = LigneBeneficeParent::with(['dqe'])
            ->where('contrat_id', $contratId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('contrats.benefice.index', compact(
            'contrat',
            'ligneBeneficeParents'
        ));
    }

    public function show($contratId, $parentId)
    {
        $contrat = Contrat::findOrFail($contratId);
        $parent = LigneBeneficeParent::with(['dqe', 'lignes.rubrique'])
            ->where('contrat_id', $contratId)
            ->findOrFail($parentId);
        
        return view('contrats.benefice.show', compact(
            'contrat',
            'parent'
        ));
    }

    public function showParent($contratId, $parentId)
    {
        return $this->show($contratId, $parentId);
    }
}