<?php

namespace App\Http\Controllers;

use App\Exports\BpuListingExport;
use App\Models\CategorieRubrique;
use App\Models\SousCategorieRubrique;
use App\Models\Rubrique;
use App\Models\Bpu;
use App\Models\UniteMesure;
use App\Models\CategoriesBpu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class BpuController extends Controller
{
    public function index()
    {
        $contratId = session('contrat_id');
        
        $rubriques = Rubrique::all();
        $uniteMesures = UniteMesure::all();
        
        // BPU utilitaires : même ordre d’affichage que /bpu/until (nom, puis id).
        $categories = CategorieRubrique::query()
            ->whereNull('contrat_id')
            ->with([
                'sousCategories' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('nom')->orderBy('id');
                },
                'sousCategories.rubriques' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('nom')->orderBy('id');
                },
                'sousCategories.rubriques.bpus' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('id');
                },
            ])
            ->whereHas('sousCategories.rubriques.bpus', function ($query) {
                $query->whereNull('contrat_id');
            })
            ->orderBy('nom')
            ->orderBy('id')
            ->get();
        $categories = $this->applyBpuCatalogDisplayOrder($categories);

        if ($contratId) {
            // BPU contrat : tri identique au catalogue (nom / id), pas l’ordre d’insertion après copie.
            $categoriesContrat = CategorieRubrique::query()
                ->where('contrat_id', $contratId)
                ->with([
                    'sousCategories' => function ($query) use ($contratId) {
                        $query->where('contrat_id', $contratId)->orderBy('nom')->orderBy('id');
                    },
                    'sousCategories.rubriques' => function ($query) use ($contratId) {
                        $query->where('contrat_id', $contratId)->orderBy('nom')->orderBy('id');
                    },
                    'sousCategories.rubriques.bpus' => function ($query) use ($contratId) {
                        $query->where('contrat_id', $contratId)->orderBy('id');
                    },
                ])
                ->whereHas('sousCategories.rubriques.bpus', function ($query) use ($contratId) {
                    $query->where('contrat_id', $contratId);
                })
                ->orderBy('nom')
                ->orderBy('id')
                ->get();
            $categoriesContrat = $this->applyBpuCatalogDisplayOrder($categoriesContrat);
            
            return view('bpu.index', compact('categories', 'categoriesContrat', 'rubriques', 'uniteMesures', 'contratId'));
        } else {
            // Mode utilitaires : afficher uniquement les BPU utilitaires
            return view('bpu.index', compact('categories', 'rubriques', 'uniteMesures', 'contratId'));
        }
    }

    public function indexuntil()
    {
        $uniteMesures = UniteMesure::all();

        // Catalogue utilitaire : catégories / sous-catégories / rubriques sans contrat_id.
        // Pas de whereHas sur les BPU : une catégorie vide doit quand même s'afficher après création.
        $categories = CategorieRubrique::query()
            ->whereNull('contrat_id')
            ->with([
                'sousCategories' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('nom')->orderBy('id');
                },
                'sousCategories.rubriques' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('nom')->orderBy('id');
                },
                'sousCategories.rubriques.bpus' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('id');
                },
            ])
            ->orderBy('nom')
            ->orderBy('id')
            ->get();
        $categories = $this->applyBpuCatalogDisplayOrder($categories);

        return view('bpu.until', compact('categories', 'uniteMesures'));
    }
    
    
    public function print()
    {
        $uniteMesures = UniteMesure::all();

        // Récupérer seulement les catégories qui ont des BPU
        $categories = CategorieRubrique::with([
            'sousCategories.rubriques.bpus'
        ])->whereHas('sousCategories.rubriques.bpus')->get();
        $categories = $this->applyBpuCatalogDisplayOrder($categories);

        return view('bpu.print', compact('categories', 'uniteMesures'));
    }

    public function exportExcel(Request $request)
    {
        $scope = $request->validate([
            'scope' => 'required|in:contrat,utilitaires,complet',
        ])['scope'];

        $categories = $this->categoriesForBpuScope($scope);
        $contratId = session('contrat_id');
        if ($scope === 'complet') {
            $base = 'bpu-complet';
        } elseif ($scope === 'contrat' && $contratId) {
            $base = 'bpu-contrat-'.$contratId;
        } else {
            $base = 'bpu-utilitaires';
        }
        $filename = $base.'-'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new BpuListingExport($categories), $filename);
    }

    public function exportPdf(Request $request)
    {
        $scope = $request->validate([
            'scope' => 'required|in:contrat,utilitaires,complet',
        ])['scope'];

        $categories = $this->categoriesForBpuScope($scope);
        $contratId = session('contrat_id');

        if ($scope === 'complet') {
            $documentTitle = 'Bordereau des prix unitaires — Liste complète';
            $filename = 'bpu-complet.pdf';
        } elseif ($scope === 'contrat') {
            $documentTitle = 'Bordereau des prix unitaires — BPU contrat';
            $filename = $contratId ? 'bpu-contrat-'.$contratId.'.pdf' : 'bpu-contrat.pdf';
        } else {
            $documentTitle = 'Bordereau des prix unitaires — BPU utilitaires';
            $filename = 'bpu-utilitaires.pdf';
        }

        $pdf = Pdf::loadView('bpu.pdf', compact('categories', 'documentTitle'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    protected function categoriesForBpuScope(string $scope): Collection
    {
        if ($scope === 'complet') {
            return $this->applyBpuCatalogDisplayOrder(CategorieRubrique::with([
                'sousCategories.rubriques.bpus',
            ])->whereHas('sousCategories.rubriques.bpus')->get());
        }

        if ($scope === 'contrat') {
            $contratId = session('contrat_id');
            if (! $contratId) {
                abort(403, 'Aucun contrat sélectionné en session.');
            }

            return $this->applyBpuCatalogDisplayOrder(CategorieRubrique::query()
                ->where('contrat_id', $contratId)
                ->with([
                    'sousCategories' => function ($query) use ($contratId) {
                        $query->where('contrat_id', $contratId)->orderBy('nom')->orderBy('id');
                    },
                    'sousCategories.rubriques' => function ($query) use ($contratId) {
                        $query->where('contrat_id', $contratId)->orderBy('nom')->orderBy('id');
                    },
                    'sousCategories.rubriques.bpus' => function ($query) use ($contratId) {
                        $query->where('contrat_id', $contratId)->orderBy('id');
                    },
                ])
                ->whereHas('sousCategories.rubriques.bpus', function ($query) use ($contratId) {
                    $query->where('contrat_id', $contratId);
                })
                ->orderBy('nom')
                ->orderBy('id')
                ->get());
        }

        return $this->applyBpuCatalogDisplayOrder(CategorieRubrique::query()
            ->whereNull('contrat_id')
            ->with([
                'sousCategories' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('nom')->orderBy('id');
                },
                'sousCategories.rubriques' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('nom')->orderBy('id');
                },
                'sousCategories.rubriques.bpus' => function ($query) {
                    $query->whereNull('contrat_id')->orderBy('id');
                },
            ])
            ->orderBy('nom')
            ->orderBy('id')
            ->get());
    }

    public function create()
    {
        $rubriques = Rubrique::all();
        $uniteMesures = UniteMesure::all();
        
        return view('bpu.create', compact('rubriques', 'uniteMesures'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'designation' => 'required',
    //         'qte' => 'required|numeric',
    //         'materiaux' => 'required|numeric',
    //         'unite' => 'required',
    //         'main_oeuvre' => 'required|numeric',
    //         'materiel' => 'required|numeric',
    //         'id_rubrique' => 'required|exists:rubriques,id'
    //     ]);

    //     // 🔢 Calculs automatiques
    //     $ds = $request->materiaux + $request->main_oeuvre + $request->materiel;
    //     $fc = $ds * 0.30; // 30%
    //     $fg = ($ds + $fc) * 0.15; // 15%
    //     $mn = ($ds + $fc + $fg) * 0.15; // 15%
    //     $pu_ht = $ds + $fc + $fg + $mn;
    //     $pu_ttc = $pu_ht * 1.18; // TVA 18%

    //     // 💾 Création du BPU
    //  $bpu =     Bpu::create([
    //         'designation' => $request->designation,
    //         'qte' => $request->qte,
    //         'materiaux' => $request->materiaux,
    //         'main_oeuvre' => $request->main_oeuvre,
    //         'materiel' => $request->materiel,
    //         'unite' => $request->unite,
    //         'debourse_sec' => $ds,
    //         'frais_chantier' => $fc,
    //         'frais_general' => $fg,
    //         'marge_nette' => $mn,
    //         'pu_ht' => $pu_ht,
    //         'pu_ttc' => $pu_ttc,
    //         'id_rubrique' => $request->id_rubrique,
    //     ]);
    // $bpu->updateDerivedValues();

    //     // Redirection intelligente selon la page d'origine
   // $redirectRoute = $request->input('redirect_to', 'bpu.index');
 //   return redirect()->route($redirectRoute)->with('success', 'BPU ajouté avec succès.');
    // }

    public function edit(Bpu $bpu)
    {
        $uniteMesures = UniteMesure::all();
        $rubriques = Rubrique::all();
        
        return view('bpu.edit', compact('bpu', 'uniteMesures', 'rubriques'));
    }

    // public function update(Request $request, $id)
    // {
    //     $bpu = Bpu::findOrFail($id);
    
    //     // 🔍 Validation des données
    //     $request->validate([
    //         'designation' => 'required',
    //         'qte' => 'required|numeric',
    //         'materiaux' => 'required|numeric',
    //         'unite' => 'required',
    //         'main_oeuvre' => 'required|numeric',
    //         'materiel' => 'required|numeric',
    //     ]);
    
    //     // 🔢 Calculs auto
    //     $ds = $request->materiaux + $request->main_oeuvre + $request->materiel;
    //     $fc = $ds * 0.30;
    //     $fg = ($ds + $fc) * 0.15;
    //     $mn = ($ds + $fc + $fg) * 0.15;
    //     $pu_ht = $ds + $fc + $fg + $mn;
    //     $pu_ttc = $pu_ht * 1.18;
    
    //     // 🔁 Update de la ligne BPU
    //     $bpu->update([
    //         'designation' => $request->designation,
    //         'qte' => $request->qte,
    //         'materiaux' => $request->materiaux,
    //         'main_oeuvre' => $request->main_oeuvre,
    //         'materiel' => $request->materiel,
    //         'unite' => $request->unite,
    //         'debourse_sec' => $ds,
    //         'frais_chantier' => $fc,
    //         'frais_general' => $fg,
    //         'marge_nette' => $mn,
    //         'pu_ht' => $pu_ht,
    //         'pu_ttc' => $pu_ttc,
    //     ]);

    //     $bpu->updateDerivedValues();

    //     // Redirection intelligente selon la page d'origine
  //  $redirectRoute = $request->input('redirect_to', 'bpu.index');
   // return redirect()->route($redirectRoute)->with('success', 'BPU mis à jour avec succès.');
    // }
   
    


    public function store(Request $request)
{
        if ($request->input('qte') === null || $request->input('qte') === '') {
            $request->merge(['qte' => 1]);
        }

    $request->validate([
        'designation' => 'required',
        'qte' => 'required|numeric|min:0',
        'materiaux' => 'required|numeric',
        'taux_mo' => 'nullable|numeric',
        'taux_mat' => 'nullable|numeric',
        'taux_fc' => 'nullable|numeric',
        'taux_fg' => 'nullable|numeric',
        'taux_benefice' => 'nullable|numeric',
        'unite' => 'required',
        'id_rubrique' => 'required|exists:rubriques,id'
    ]);

    $redirectRoute = $request->input('redirect_to', 'bpu.index');

    /**
     * Règle métier :
     * - Depuis la page "BPU Utilitaires" (/bpu/until), toute création doit être utilitaire.
     *   Donc contrat_id = NULL, même si un contrat est encore en session.
     * - Sinon, en mode contrat : on accepte contrat_id du formulaire, puis fallback session.
     */
    if ($redirectRoute === 'bpu.indexuntil') {
        $contratId = null;
    } else {
        $contratId = $request->filled('contrat_id') ? $request->input('contrat_id') : session('contrat_id');
        $contratId = $contratId ?: null;
    }

    try {
        // Création du BPU avec les données de base et les taux
        $bpu = Bpu::create([
            'designation' => $request->designation,
            'qte' => $request->qte,
            'materiaux' => $request->materiaux,
            'taux_mo' => $request->taux_mo ?? 0,
            'taux_mat' => $request->taux_mat ?? 0,
            'taux_fc' => $request->taux_fc ?? 0,
            'taux_fg' => $request->taux_fg ?? 0,
            'taux_benefice' => $request->taux_benefice ?? 0,
            'unite' => $request->unite,
            'id_rubrique' => $request->id_rubrique,
            'contrat_id' => $contratId,
        ]);
        
        // La méthode updateDerivedValues s'occupe de tous les calculs selon les formules BPU
        $bpu->updateDerivedValues();

        return redirect()->route($redirectRoute)->with('success', 'BPU ajouté avec succès.');
    } catch (\Throwable $e) {
        return redirect()->route($redirectRoute)->with('error', 'Erreur ajout BPU : ' . $e->getMessage());
    }
}

public function update(Request $request, $id)
{
    $bpu = Bpu::findOrFail($id);

    // Validation des données
    $request->validate([
        'designation' => 'required',
        'qte' => 'required|numeric|min:0',
        'materiaux' => 'required|numeric',
        'taux_mo' => 'nullable|numeric',
        'taux_mat' => 'nullable|numeric',
        'taux_fc' => 'nullable|numeric',
        'taux_fg' => 'nullable|numeric',
        'taux_benefice' => 'nullable|numeric',
        'unite' => 'required',
    ]);

    // Mise à jour des données de base et des taux
    $bpu->update([
        'designation' => $request->designation,
        'qte' => $request->input('qte', $bpu->qte ?? 0),
        'materiaux' => $request->materiaux,
        'taux_mo' => $request->taux_mo ?? 0,
        'taux_mat' => $request->taux_mat ?? 0,
        'taux_fc' => $request->taux_fc ?? 0,
        'taux_fg' => $request->taux_fg ?? 0,
        'taux_benefice' => $request->taux_benefice ?? 0,
        'unite' => $request->unite,
    ]);
    
    // La méthode updateDerivedValues s'occupe de tous les calculs selon les formules BPU
    $bpu->updateDerivedValues();

    // Redirection intelligente selon la page d'origine
    $redirectRoute = $request->input('redirect_to', 'bpu.index');
    return redirect()->route($redirectRoute)->with('success', 'BPU mis à jour avec succès.');
}
    public function destroy(Request $request, Bpu $bpu)
    {
        $bpu->delete();

        // Redirection intelligente selon la page d'origine
        $redirectRoute = $request->input('redirect_to', 'bpu.index');
        return redirect()->route($redirectRoute)->with('success', 'BPU supprimé avec succès.');
    }

    /**
     * Dupliquer les BPU sélectionnés vers les utilitaires
     */
    public function duplicate(Request $request)
    {
        try {
            $bpuIds = $request->input('bpu_ids', []);
            
            if (empty($bpuIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun BPU sélectionné pour la duplication.'
                ]);
            }

            $duplicatedCount = 0;
            
            foreach ($bpuIds as $bpuId) {
                $originalBpu = Bpu::find($bpuId);
                
                if ($originalBpu && $originalBpu->contrat_id) {
                    // Créer une copie du BPU sans contrat_id (pour les utilitaires)
                    $duplicatedBpu = $originalBpu->replicate();
                    $duplicatedBpu->contrat_id = null;
                    $duplicatedBpu->save();
                    
                    $duplicatedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'duplicated_count' => $duplicatedCount,
                'message' => "$duplicatedCount BPU ont été dupliqués avec succès vers les utilitaires."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la duplication: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Copier les BPU utilitaires sélectionnés vers le contrat
     */
    public function copyToContract(Request $request)
    {
        try {
            $bpuIds = $request->input('bpu_ids', []);
            $contratId = $request->input('contrat_id');
            
            if (empty($bpuIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun BPU sélectionné pour la copie.'
                ]);
            }

            if (!$contratId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID du contrat manquant.'
                ]);
            }

            $copiedCount = 0;
            $categorieMapping = [];
            $sousCategorieMapping = [];
            $rubriqueMapping = [];
            
            foreach ($bpuIds as $bpuId) {
                $originalBpu = Bpu::with('rubrique.sousCategorie.categorie')->find($bpuId);
                
                if ($originalBpu && !$originalBpu->contrat_id && $originalBpu->rubrique) {
                    $originalRubrique = $originalBpu->rubrique;
                    $originalSousCategorie = $originalRubrique->sousCategorie;
                    $originalCategorie = $originalSousCategorie->categorie;
                    
                    // 1. Dupliquer ou récupérer la catégorie pour le contrat
                    $categorieKey = $originalCategorie->id;
                    if (!isset($categorieMapping[$categorieKey])) {
                        $existingCategorie = \App\Models\CategorieRubrique::where('contrat_id', $contratId)
                            ->where('nom', $originalCategorie->nom)
                            ->where('type', $originalCategorie->type)
                            ->first();
                        
                        if (!$existingCategorie) {
                            $newCategorie = $originalCategorie->replicate();
                            $newCategorie->contrat_id = $contratId;
                            $newCategorie->save();
                            $categorieMapping[$categorieKey] = $newCategorie->id;
                        } else {
                            $categorieMapping[$categorieKey] = $existingCategorie->id;
                        }
                    }
                    
                    // 2. Dupliquer ou récupérer la sous-catégorie pour le contrat
                    $sousCategorieKey = $originalSousCategorie->id;
                    if (!isset($sousCategorieMapping[$sousCategorieKey])) {
                        $existingSousCategorie = \App\Models\SousCategorieRubrique::where('contrat_id', $contratId)
                            ->where('nom', $originalSousCategorie->nom)
                            ->where('type', $originalSousCategorie->type)
                            ->where('id_session', $categorieMapping[$categorieKey])
                            ->first();
                        
                        if (!$existingSousCategorie) {
                            $newSousCategorie = $originalSousCategorie->replicate();
                            $newSousCategorie->contrat_id = $contratId;
                            $newSousCategorie->id_session = $categorieMapping[$categorieKey];
                            $newSousCategorie->save();
                            $sousCategorieMapping[$sousCategorieKey] = $newSousCategorie->id;
                        } else {
                            $sousCategorieMapping[$sousCategorieKey] = $existingSousCategorie->id;
                        }
                    }
                    
                    // 3. Dupliquer ou récupérer la rubrique pour le contrat
                    $rubriqueKey = $originalRubrique->id;
                    if (!isset($rubriqueMapping[$rubriqueKey])) {
                        $existingRubrique = \App\Models\Rubrique::where('contrat_id', $contratId)
                            ->where('nom', $originalRubrique->nom)
                            ->where('type', $originalRubrique->type)
                            ->where('id_soussession', $sousCategorieMapping[$sousCategorieKey])
                            ->first();
                        
                        if (!$existingRubrique) {
                            $newRubrique = $originalRubrique->replicate();
                            $newRubrique->contrat_id = $contratId;
                            $newRubrique->id_soussession = $sousCategorieMapping[$sousCategorieKey];
                            $newRubrique->save();
                            $rubriqueMapping[$rubriqueKey] = $newRubrique->id;
                        } else {
                            $rubriqueMapping[$rubriqueKey] = $existingRubrique->id;
                        }
                    }
                    
                    // 4. Vérifier si ce BPU exact n'existe pas déjà dans le contrat
                    $existingBpu = Bpu::where('contrat_id', $contratId)
                        ->where('designation', $originalBpu->designation)
                        ->where('id_rubrique', $rubriqueMapping[$rubriqueKey])
                        ->where('qte', $originalBpu->qte)
                        ->where('materiaux', $originalBpu->materiaux)
                        ->where('main_oeuvre', $originalBpu->main_oeuvre)
                        ->where('materiel', $originalBpu->materiel)
                        ->first();
                    
                    if (!$existingBpu) {
                        // 5. Créer une copie du BPU avec la nouvelle rubrique et contrat_id
                        $copiedBpu = $originalBpu->replicate();
                        $copiedBpu->contrat_id = $contratId;
                        $copiedBpu->id_rubrique = $rubriqueMapping[$rubriqueKey];
                        $copiedBpu->save();
                        
                        $copiedCount++;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'copied_count' => $copiedCount,
                'message' => "$copiedCount BPU ont été copiés avec succès vers le contrat avec leur hiérarchie complète."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la copie: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Trie catégories, sous-catégories et rubriques : « Lot n° X » par X croissant, puis les autres par nom.
     */
    protected function applyBpuCatalogDisplayOrder(Collection $categories): Collection
    {
        return $this->sortNamedBpuLevel($categories)
            ->values()
            ->map(function (CategorieRubrique $categorie) {
                $categorie->setRelation(
                    'sousCategories',
                    $this->sortNamedBpuLevel($categorie->sousCategories)->values()
                );
                foreach ($categorie->sousCategories as $sous) {
                    $sous->setRelation(
                        'rubriques',
                        $this->sortNamedBpuLevel($sous->rubriques)->values()
                    );
                    foreach ($sous->rubriques as $rubrique) {
                        if ($rubrique->relationLoaded('bpus')) {
                            $rubrique->setRelation('bpus', $rubrique->bpus->sortBy('id')->values());
                        }
                    }
                }

                return $categorie;
            });
    }

    /**
     * @param  Collection<int, CategorieRubrique|SousCategorieRubrique|Rubrique>  $items
     * @return Collection<int, CategorieRubrique|SousCategorieRubrique|Rubrique>
     */
    protected function sortNamedBpuLevel(Collection $items): Collection
    {
        return $items->sort(function ($a, $b) {
            $na = (string) ($a->nom ?? '');
            $nb = (string) ($b->nom ?? '');
            $cmp = $this->compareBpuLotNom($na, $nb);
            if ($cmp !== 0) {
                return $cmp;
            }

            return ((int) ($a->id ?? 0)) <=> ((int) ($b->id ?? 0));
        });
    }

    /**
     * Compare deux libellés : lots d’abord (Lot 0 / Lot n° 0 en premier, puis 1, 2…), puis le reste par nom.
     */
    protected function compareBpuLotNom(string $a, string $b): int
    {
        $la = $this->parseLotNumeroFromNom($a);
        $lb = $this->parseLotNumeroFromNom($b);

        if ($la !== null && $lb !== null) {
            return $la <=> $lb;
        }
        if ($la !== null) {
            return -1;
        }
        if ($lb !== null) {
            return 1;
        }

        return strcasecmp(mb_strtolower($a, 'UTF-8'), mb_strtolower($b, 'UTF-8'));
    }

    /**
     * Retourne le numéro X pour un lot (0, 1, 2…), ou null si ce n’est pas un libellé de type « Lot ».
     * « LOT 0 », « Lot 0 », « Lot n° 0 » sont reconnus ; le 0 est trié avant tous les autres lots.
     */
    protected function parseLotNumeroFromNom(string $nom): ?int
    {
        $nom = trim($nom);
        if ($nom === '') {
            return null;
        }
        // Lot n° 1, LOT N° 12, Lot nº 3, etc.
        if (preg_match('/^lot\s+n\s*[°º]?\s*(\d+)/iu', $nom, $m)) {
            return (int) $m[1];
        }
        // LOT 0, Lot 0, Lot 15… (sans « n° »)
        if (preg_match('/^lot\s+(\d+)\b/iu', $nom, $m)) {
            return (int) $m[1];
        }

        return null;
    }
}