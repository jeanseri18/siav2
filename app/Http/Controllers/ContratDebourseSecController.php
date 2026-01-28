<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\DebourseSecParent;
use Illuminate\Http\Request;

class ContratDebourseSecController extends Controller
{
    public function index($contratId)
    {
        $contrat = Contrat::findOrFail($contratId);
        
        // Récupérer tous les parents de déboursés secs du contrat
        $debourseSecParents = DebourseSecParent::with(['dqe'])
            ->where('contrat_id', $contratId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('contrats.debourse-sec.index', compact(
            'contrat',
            'debourseSecParents'
        ));
    }

    public function show($contratId, $parentId)
    {
        $contrat = Contrat::findOrFail($contratId);
        $parent = DebourseSecParent::with(['dqe', 'lignes.rubrique'])
            ->where('contrat_id', $contratId)
            ->findOrFail($parentId);
        
        return view('contrats.debourse-sec.show', compact(
            'contrat',
            'parent'
        ));
    }

    public function showParent($contratId, $parentId)
    {
        return $this->show($contratId, $parentId);
    }
}