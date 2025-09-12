<table width="100%" class="text-center mt-4" border="1" bordercolor="black">
    <tr bgcolor="{{ $type === 'contrat' ? '#28a745' : '#5EB3F6' }}" height="40px">
        <td colspan="12">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="text-start text-uppercase text-white">
                        {{ $categorie->nom }}
                        @if($type === 'contrat')
                            <span class="badge bg-light text-dark ms-2">Contrat</span>
                        @else
                            <span class="badge bg-light text-dark ms-2">Utilitaires</span>
                        @endif
                    </h4>
                </div>
                @if($type === 'contrat')
                    <div class="col">
                        <!-- Boutons Modifier et Supprimer -->
                        <button class="btn btn-primary btn-sm form-control" onclick="editCategorie('{{ $categorie->id }}', '{{ $categorie->nom }}')">Modifier</button>
                    </div>
                    <div class="col">
                        <form action="{{ route('categoriesbpu.destroy', $categorie->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm form-control">Supprimer</button>
                        </form>
                    </div>
                @else
                    <div class="col-md-4">
                        <span class="text-white"><em>Lecture seule - Modifiez depuis les utilitaires</em></span>
                    </div>
                @endif
            </div>
        </td>
    </tr>

    @if($type === 'contrat')
        <!-- Formulaire d'ajout de sous-catégorie -->
        <tr>
            <td colspan="12">
                <form action="{{ route('souscategoriesbpu.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <input type="hidden" name="id_session" value="{{ $categorie->id }}">
                            <input type="text" name="nom" class="form-control" placeholder="Nouvelle sous-catégorie" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-sm mt-2">Ajouter Sous-Catégorie</button>
                        </div>
                    </div>
                </form>
            </td>
        </tr>
    @endif

    @foreach ($categorie->sousCategories as $sousCategorie)
        <tr bgcolor="{{ $type === 'contrat' ? '#155724' : '#1F384C' }}" class="text-white" height="40px">
            <td colspan="12">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="text-start text-uppercase">{{ $sousCategorie->nom }}</h5>
                    </div>
                    @if($type === 'contrat')
                        <div class="col-md-2">
                            <button class="btn btn-warning form-control btn-sm" onclick="editSousCategorie('{{ $sousCategorie->id }}', '{{ $sousCategorie->nom }}')">Modifier</button>
                        </div>
                        <div class="col-md-2">
                            <form action="{{ route('souscategoriesbpu.destroy', $sousCategorie->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn form-control btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    @else
                        <div class="col-md-4">
                            <span class="text-white"><em>Lecture seule</em></span>
                        </div>
                    @endif
                </div>
            </td>
        </tr>

        @if($type === 'contrat')
            <!-- Formulaire d'ajout de rubrique -->
            <tr>
                <td colspan="12">
                    <form action="{{ route('rubriques.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-10">
                                <input type="hidden" name="id_soussession" value="{{ $sousCategorie->id }}">
                                <input type="text" name="nom" class="form-control" placeholder="Nouvelle rubrique" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info btn-sm mt-2">Ajouter Rubrique</button>
                            </div>
                        </div>
                    </form>
                </td>
            </tr>
        @endif

        @foreach ($sousCategorie->rubriques as $rubrique)
            <tr bgcolor="{{ $type === 'contrat' ? '#0f5132' : '#3A6B8C' }}" class="text-white" height="40px">
                <td colspan="12">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-start text-uppercase">{{ $rubrique->nom }}</h6>
                        </div>
                        @if($type === 'contrat')
                            <div class="col-md-2">
                                <button class="btn btn-warning form-control btn-sm" onclick="editRubrique('{{ $rubrique->id }}', '{{ $rubrique->nom }}')">Modifier</button>
                            </div>
                            <div class="col-md-2">
                                <form action="{{ route('rubriques.destroy', $rubrique->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn form-control btn-danger btn-sm">Supprimer</button>
                                </form>
                            </div>
                        @else
                            <div class="col-md-4">
                                <span class="text-white"><em>Lecture seule</em></span>
                            </div>
                        @endif
                    </div>
                </td>
            </tr>

            <tr>
                @if($type === 'utilitaires')
                    <td>
                        <input type="checkbox" id="selectAll_{{ $rubrique->id }}" onchange="toggleAllBpu({{ $rubrique->id }})">
                        <label for="selectAll_{{ $rubrique->id }}">Tout</label>
                    </td>
                @endif
                <td>Désignation</td>
                <td>Quantité</td>
                <td>Matériaux</td>
                <td>Unité</td>
                <td>Main d'oeuvre</td>
                <td>Matériel</td>
                <td>Déboursé sec</td>
                <td>Frais de chantier</td>
                <td>Frais généraux</td>
                <td>Bénéfice</td>
                <td>Prix HT</td>
                <td>Action</td>
            </tr>
            
            @foreach ($rubrique->bpus as $bpu)
                <tr class="{{ $type === 'contrat' ? 'table-success' : 'table-info' }}">
                    @if($type === 'utilitaires')
                        <td>
                            <input type="checkbox" class="bpu-checkbox" data-rubrique="{{ $rubrique->id }}" value="{{ $bpu->id }}">
                        </td>
                    @endif
                    <td>{{ $bpu->designation }}</td>
                    <td>{{ $bpu->qte }}</td>
                    <td>{{ $bpu->materiaux }}</td>
                    <td>{{ $bpu->unite }}</td>
                    <td>{{ $bpu->main_oeuvre }}</td>
                    <td>{{ $bpu->materiel }}</td>
                    <td>{{ $bpu->debourse_sec }}</td>
                    <td>{{ $bpu->frais_chantier }}</td>
                    <td>{{ $bpu->frais_general }}</td>
                    <td>{{ $bpu->marge_nette }}</td>
                    <td>{{ $bpu->pu_ht }}</td>
                    <td>
                        @if($type === 'contrat')
                            <a href="{{ route('bpus.edit', $bpu->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="{{ route('bpus.destroy', $bpu->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        @else
                            <span class="text-muted"><em>Lecture seule</em></span>
                        @endif
                    </td>
                </tr>
            @endforeach

            @if($type === 'contrat')
                <tr>
                    <td colspan="12">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('bpus.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_rubrique" value="{{ $rubrique->id }}">
                            <input type="hidden" name="contrat_id" value="{{ $contratId ?? '' }}">

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="designation" class="form-label">Désignation</label>
                                    <input type="text" name="designation" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="qte" class="form-label">Quantité</label>
                                    <input type="number" step="0.01" name="qte" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="materiaux" class="form-label">Matériaux</label>
                                    <input type="number" step="0.01" name="materiaux" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="main_oeuvre" class="form-label">Main d'œuvre</label>
                                    <input type="number" step="0.01" name="main_oeuvre" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="materiel" class="form-label">Matériel</label>
                                    <input type="number" step="0.01" name="materiel" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="unite" class="form-label">Unité</label>
                                    <select name="unite" class="form-control" required>
                                        <option value="">Choisir</option>
                                        @foreach ($uniteMesures as $uniteMesure)
                                            <option value="{{ $uniteMesure->ref }}">{{ $uniteMesure->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Ajouter une ligne</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
</table>

@if($type === 'utilitaires')
    <div class="mt-3 mb-3">
        <button type="button" class="btn btn-success" onclick="copySelectedBpuToContract()">Copier les BPU sélectionnés vers le contrat</button>
        <span id="selectedCount" class="ms-3 text-muted">0 BPU sélectionné(s)</span>
    </div>
@endif

<br>