<table width="100%" class="text-center mt-4" border="1" bordercolor="black">
    <tr bgcolor="{{ $type === 'contrat' ? '#007bff' : '#5EB3F6' }}" height="40px">
        <td colspan="{{ $type === 'utilitaires' ? '17' : '16' }}">
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
            <td colspan="{{ $type === 'utilitaires' ? '17' : '16' }}">
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
        <tr bgcolor="{{ $type === 'contrat' ? '#0056b3' : '#1F384C' }}" class="text-white" height="40px">
            <td colspan="{{ $type === 'utilitaires' ? '17' : '16' }}">
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
                <td colspan="{{ $type === 'utilitaires' ? '17' : '16' }}">
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
            <tr bgcolor="{{ $type === 'contrat' ? '#004085' : '#3A6B8C' }}" class="text-white" height="40px">
                <td colspan="{{ $type === 'utilitaires' ? '17' : '16' }}">
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
                <td>Unité</td>
                <td>Matériaux</td>
                <td>Taux MO (%)</td>
                <td>Main d'œuvre</td>
                <td>Taux MAT (%)</td>
                <td>Matériel</td>
                <td>DS</td>
                <td>Taux FC (%)</td>
                <td>FC</td>
                <td>Taux FG (%)</td>
                <td>Frais généraux</td>
                <td>Taux Bénéfice (%)</td>
                <td>Bénéfice</td>
                <td>Prix unitaire HT</td>
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
                    <td>{{ $bpu->unite }}</td>
                    <td>{{ number_format($bpu->materiaux, 2) }}</td>
                    <td>{{ number_format($bpu->taux_mo, 2) }}%</td>
                    <td>{{ number_format($bpu->main_oeuvre, 2) }}</td>
                    <td>{{ number_format($bpu->taux_mat, 2) }}%</td>
                    <td>{{ number_format($bpu->materiel, 2) }}</td>
                    <td>{{ number_format($bpu->debourse_sec, 2) }}</td>
                    <td>{{ number_format($bpu->taux_fc, 2) }}%</td>
                    <td>{{ number_format($bpu->frais_chantier, 2) }}</td>
                    <td>{{ number_format($bpu->taux_fg, 2) }}%</td>
                    <td>{{ number_format($bpu->frais_general, 2) }}</td>
                    <td>{{ number_format($bpu->taux_benefice, 2) }}%</td>
                    <td>{{ number_format($bpu->marge_nette, 2) }}</td>
                    <td>{{ number_format($bpu->pu_ht, 2) }}</td>
                    <td>
                        @if($type === 'contrat')
                            <a href="{{ route('bpus.edit', $bpu->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="{{ route('bpus.destroy', $bpu->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="redirect_to" value="bpu.index">
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
                    <td colspan="16">
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
                            <input type="hidden" name="redirect_to" value="bpu.index">

                            <div class="d-flex gap-3 mb-2 align-items-center" style="font-size: 14px;">
                                <div class="d-flex flex-column">
                                    <small>Désignation</small>
                                    <input type="text" name="designation" class="form-control form-control-sm" placeholder="Désignation" style="width: 220px; font-size: 14px; height: 38px;" required>
                                </div>
                                <div class="d-flex flex-column">
                                    <small>Unité</small>
                                    <select name="unite" class="form-select form-select-sm" style="width: 110px; font-size: 14px; height: 38px;" required>
                                        <option value="" style="font-size: 14px;">Choisir</option>
                                        @foreach ($uniteMesures as $uniteMesure)
                                            <option value="{{ $uniteMesure->ref }}">{{ $uniteMesure->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="d-flex flex-column">
                                    <small>Matériaux</small>
                                    <input type="number" name="materiaux" class="form-control form-control-sm" placeholder="Mat." step="0.01" value="0" style="width: 110px; font-size: 14px; height: 38px;" required>
                                </div>
                                <div class="d-flex flex-column">
                                    <small>T.MO (%)</small>
                                    <input type="number" name="taux_mo" class="form-control form-control-sm" placeholder="T.MO" step="0.01" value="0" style="width: 95px; font-size: 14px; height: 38px;">
                                </div>
                                <div class="d-flex flex-column">
                                    <small>T.MAT (%)</small>
                                    <input type="number" name="taux_mat" class="form-control form-control-sm" placeholder="T.MAT" step="0.01" value="0" style="width: 100px; font-size: 14px; height: 38px;">
                                </div>
                                <div class="d-flex flex-column">
                                    <small>T.FC (%)</small>
                                    <input type="number" name="taux_fc" class="form-control form-control-sm" placeholder="T.FC" step="0.01" value="0" style="width: 95px; font-size: 14px; height: 38px;">
                                </div>
                                <div class="d-flex flex-column">
                                    <small>T.FG (%)</small>
                                    <input type="number" name="taux_fg" class="form-control form-control-sm" placeholder="T.FG" step="0.01" value="0" style="width: 95px; font-size: 14px; height: 38px;">
                                </div>
                                <div class="d-flex flex-column">
                                    <small>T.BEN (%)</small>
                                    <input type="number" name="taux_benefice" class="form-control form-control-sm" placeholder="T.BEN" step="0.01" value="0" style="width: 100px; font-size: 14px; height: 38px;">
                                </div>
                            </div>

                            {{-- Affichage des valeurs calculées en temps réel --}}
                            <div class="mb-2">
                                <small class="text-muted">Aperçu :</small>
                                <div class="d-flex gap-2 align-items-center" style="font-size: 10px; background-color: #f8f9fa; padding: 5px; border-radius: 3px;">
                                    <span>MO: <strong id="main_oeuvre_calc">0.00</strong></span>
                                    <span>MAT: <strong id="materiel_calc">0.00</strong></span>
                                    <span>DS: <strong id="debourse_sec_calc">0.00</strong></span>
                                    <span>FC: <strong id="frais_chantier_calc">0.00</strong></span>
                                    <span>FG: <strong id="frais_generaux_calc">0.00</strong></span>
                                    <span>BEN: <strong id="benefice_calc">0.00</strong></span>
                                    <span class="text-primary">| PU HT: <strong id="pu_ht_calc">0.00</strong></span>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm" style="height: 35px; font-size: 12px; padding: 0 8px;">Ajouter une ligne</button>
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