@extends('layouts.app')

@section('content')


<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid black;
        padding: 10px;
    }
    tr {
        border-bottom: 1px solid black;
    }
</style>

<div class="container-fluid" style="font-size: 12px;">
    <h2 class="mb-4">Bordereaux de prix unitaires</h2>
    <button class="btn btn-secondary mb-2" onclick="toggleForm('formCategorie')">Ajouter Catégorie</button>
    <a href="{{ route('bpu.print') }}" class="btn btn-secondary mb-2" target="blank">Afficher le BPU complet</a>
    <a href="{{ route('import.index') }}" class="btn btn-secondary mb-2">Importer un fichier excel</a>

    <!-- Formulaire d'ajout de catégorie -->
    <form id="formCategorie" action="{{ route('categoriesbpu.store') }}" method="POST" style="display: none;">
        @csrf
        <div class="row">
            <div class="col-md-10">
                <div class="mb-3">
                    <input type="text" name="nom" class="form-control" placeholder="Nouvelle catégorie" required>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Ajouter Catégorie</button>
            </div>
        </div>
    </form>

    @foreach ($categories as $categorie)
        <table width="100%" class="text-center mt-4" border="1" bordercolor="black">
            <tr bgcolor="#5EB3F6" height="40px">
                <td colspan="16">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text-start text-uppercase">{{ $categorie->nom }}</h4>
                        </div>
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
                    </div>
                </td>
            </tr>

            <!-- Formulaire d'ajout de sous-catégorie -->
            <tr>
                <td colspan="16">
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

            @foreach ($categorie->sousCategories as $sousCategorie)
                <tr bgcolor="#1F384C" class="text-white" height="40px">
                    <td colspan="16">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="text-start text-uppercase">{{ $sousCategorie->nom }}</h5>
                            </div>
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
                        </div>
                    </td>
                </tr>

                <!-- Formulaire d'ajout de rubrique -->
                <tr>
                    <td colspan="16">
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

                @foreach ($sousCategorie->rubriques as $rubrique)
                    <tr bgcolor="#3A6B8C" class="text-white" height="40px">
                        <td colspan="16">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="text-start text-uppercase">{{ $rubrique->nom }}</h6>
                                </div>
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
                            </div>
                        </td>
                    </tr>

                    <tr>
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
                        <tr>
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
                                <a href="{{ route('bpus.edit', $bpu->id) }}?from=until" class="btn btn-warning btn-sm">Modifier</a>
                                <form action="{{ route('bpus.destroy', $bpu->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="bpu.indexuntil">
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

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
                                <input type="hidden" name="redirect_to" value="bpu.indexuntil">

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
                                <!-- <button type="submit" class="btn btn-primary btn-sm" style="height: 35px; font-size: 12px; padding: 0 8px;">Ajouter</button> -->
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
                            <button type="submit" class="btn btn-primary">Ajouter une ligne</button>
                        </div>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </table>
        <br>
    @endforeach
</div>

<script>
    function toggleForm(id) {
        var form = document.getElementById(id);
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    function editCategorie(id, nom) {
        let newName = prompt("Modifier la catégorie :", nom);
        if (newName) {
            fetch(`{{ url('/categoriesbpu') }}/${id}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ nom: newName })
            }).then(() => location.reload());
        }
    }

    function editSousCategorie(id, nom) {
        let newName = prompt("Modifier la sous-catégorie :", nom);
        if (newName) {
            fetch(`{{ url('/souscategoriesbpu') }}/${id}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ nom: newName })
            }).then(() => location.reload());
        }
    }

    function editRubrique(id, nom) {
        let newName = prompt("Modifier la rubrique :", nom);
        if (newName) {
            fetch(`{{ url('/rubriques') }}/${id}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ nom: newName })
            }).then(() => location.reload());
        }
    }
</script>

<script src="{{ asset('js/bpu-calculator.js') }}"></script>
@endsection