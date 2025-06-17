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
                <td colspan="11">
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
                <td colspan="11">
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
                    <td colspan="11">
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
                    <td colspan="11">
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
                        <td colspan="11">
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
                        <td>Quantité</td>
                        <td>Matériaux</td>
                        <td>Unité</td>
                        <td>Main d'oeuvre</td>
                        <td>Matériel</td>
                        <td>Déboursé sec</td>
                        <td>Frais de chantier</td>
                        <td>Frais Général</td>
                        <td>Prix HT</td>
                        <td>Action</td>
                    </tr>
                    
                    @foreach ($rubrique->bpus as $bpu)
                        <tr>
                            <td>{{ $bpu->designation }}</td>
                            <td>{{ $bpu->qte }}</td>
                            <td>{{ $bpu->materiaux }}</td>
                            <td>{{ $bpu->unite }}</td>
                            <td>{{ $bpu->main_oeuvre }}</td>
                            <td>{{ $bpu->materiel }}</td>
                            <td>{{ $bpu->debourse_sec }}</td>
                            <td>{{ $bpu->frais_chantier }}</td>
                            <td>{{ $bpu->frais_general }}</td>
                            <td>{{ $bpu->pu_ht }}</td>
                            <td>
                                <a href="{{ route('bpus.edit', $bpu->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                                <form action="{{ route('bpus.destroy', $bpu->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="11">
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
@endsection