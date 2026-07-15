<div class="bpu-categorie-wrap">
<table width="100%" class="text-center mt-4 bpu-categorie-table" border="1" bordercolor="black">
    <tr bgcolor="{{ $type === 'contrat' ? '#033d71' : '#033d71' }}" height="40px">
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
                    @if(auth()->user()->hasPermission('bpus.edit'))
                    <div class="col">
                        <!-- Boutons Modifier et Supprimer -->
                        <button class="btn btn-primary btn-sm form-control" onclick="editCategorie('{{ $categorie->id }}', '{{ $categorie->nom }}')">Modifier</button>
                    </div>
                    @endif
                    @if(auth()->user()->hasPermission('bpus.destroy'))
                    <div class="col">
                        <form action="{{ route('categoriesbpu.destroy', $categorie->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm form-control">Supprimer</button>
                        </form>
                    </div>
                    @endif
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
        @if(auth()->user()->hasPermission('bpus.create'))
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
    @endif

    @foreach ($categorie->sousCategories as $sousCategorie)
        <tr bgcolor="{{ $type === 'contrat' ? '#033d71' : '#1F384C' }}" class="text-white" height="40px">
            <td colspan="{{ $type === 'utilitaires' ? '17' : '16' }}">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="text-start text-uppercase">{{ $sousCategorie->nom }}</h5>
                    </div>
                    @if($type === 'contrat')
                        @if(auth()->user()->hasPermission('bpus.edit'))
                        <div class="col-md-2">
                            <button class="btn btn-warning form-control btn-sm" onclick="editSousCategorie('{{ $sousCategorie->id }}', '{{ $sousCategorie->nom }}')">Modifier</button>
                        </div>
                        @endif
                        @if(auth()->user()->hasPermission('bpus.destroy'))
                        <div class="col-md-2">
                            <form action="{{ route('souscategoriesbpu.destroy', $sousCategorie->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn form-control btn-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                        @endif
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
            @if(auth()->user()->hasPermission('bpus.create'))
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
        @endif

        @foreach ($sousCategorie->rubriques as $rubrique)
            @php
                $bpuGroupId = 'g-' . $categorie->id . '-' . $sousCategorie->id . '-' . $rubrique->id;
            @endphp
            <tr bgcolor="{{ $type === 'contrat' ? '#004085' : '#3A6B8C' }}" class="text-white" height="40px" data-bpu-group="{{ $bpuGroupId }}">
                <td colspan="{{ $type === 'utilitaires' ? '17' : '16' }}">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-start text-uppercase">{{ $rubrique->nom }}</h6>
                        </div>
                        @if($type === 'contrat')
                            @if(auth()->user()->hasPermission('bpus.edit'))
                            <div class="col-md-2">
                                <button class="btn btn-warning form-control btn-sm" onclick="editRubrique('{{ $rubrique->id }}', '{{ $rubrique->nom }}')">Modifier</button>
                            </div>
                            @endif
                            @if(auth()->user()->hasPermission('bpus.destroy'))
                            <div class="col-md-2">
                                <form action="{{ route('rubriques.destroy', $rubrique->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn form-control btn-danger btn-sm">Supprimer</button>
                                </form>
                            </div>
                            @endif
                        @else
                            <div class="col-md-4">
                                <span class="text-white"><em>Lecture seule</em></span>
                            </div>
                        @endif
                    </div>
                </td>
            </tr>

            <tr data-bpu-group="{{ $bpuGroupId }}">
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
                <tr class="{{ $type === 'contrat' ? 'table-success' : 'table-info' }}" data-bpu-group="{{ $bpuGroupId }}" data-bpu-designation-haystack="{{ e(mb_strtolower(trim((string) ($bpu->designation ?? '')), 'UTF-8')) }}">
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
                            @if(auth()->user()->hasPermission('bpus.edit'))
                            <a href="{{ route('bpus.edit', $bpu->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                            @endif
                            @if(auth()->user()->hasPermission('bpus.destroy'))
                            <form action="{{ route('bpus.destroy', $bpu->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="redirect_to" value="bpu.index">
                                <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                            @endif
                        @else
                            <span class="text-muted"><em>Lecture seule</em></span>
                        @endif
                    </td>
                </tr>
            @endforeach

            {{-- BPU contrat : formulaire d’ajout de ligne masqué (lignes via copie utilitaire / import). --}}
        @endforeach
    @endforeach
</table>

@if($type === 'utilitaires')
    <div class="mt-3 mb-3">
        <button type="button" class="btn btn-success" onclick="copySelectedBpuToContract()">Copier les BPU sélectionnés vers le contrat</button>
        <span id="selectedCount" class="ms-3 text-muted">0 BPU sélectionné(s)</span>
    </div>
@endif

</div>
