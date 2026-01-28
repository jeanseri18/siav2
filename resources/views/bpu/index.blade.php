@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

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
    
    @if($contratId)
        <div class="alert alert-info mb-3">
            <strong>Mode Contrat:</strong> Vous visualisez les BPU spécifiques au contrat et les BPU utilitaires.
        </div>
        
        <!-- Navigation entre les sections -->
        <ul class="nav nav-tabs mb-3" id="bpuTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="contrat-tab" data-bs-toggle="tab" data-bs-target="#contrat" type="button" role="tab">BPU Contrat</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="utilitaires-tab" data-bs-toggle="tab" data-bs-target="#utilitaires" type="button" role="tab">BPU Utilitaires</button>
            </li>
        </ul>
    @else
        <h3 class="text-primary mb-3">BPU Utilitaires (Modèles)</h3>
    @endif
    
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

    @if($contratId)
        <div class="tab-content" id="bpuTabsContent">
            <!-- Section BPU Contrat -->
            <div class="tab-pane fade show active" id="contrat" role="tabpanel">
                <h4 class="text-success mb-3">BPU Spécifiques au Contrat (Modifiables)</h4>
                @foreach ($categoriesContrat as $categorie)
                    @include('bpu.partials.categorie-table', ['categorie' => $categorie, 'type' => 'contrat'])
                @endforeach
            </div>
            
            <!-- Section BPU Utilitaires -->
            <div class="tab-pane fade" id="utilitaires" role="tabpanel">
                <h4 class="text-info mb-3">BPU Utilitaires (Lecture seule)</h4>
                @foreach ($categories as $categorie)
                    @include('bpu.partials.categorie-table', ['categorie' => $categorie, 'type' => 'utilitaires'])
                @endforeach
            </div>
        </div>
    @else
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
                                <a href="{{ route('bpus.edit', $bpu->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                                <form action="{{ route('bpus.destroy', $bpu->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="bpu.index">
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
                                <input type="hidden" name="redirect_to" value="bpu.index">

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
    @endif
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

    // Fonctions pour la sélection et duplication des BPU
    function toggleAllBpu(rubriqueId) {
        const selectAllCheckbox = document.getElementById(`selectAll_${rubriqueId}`);
        const bpuCheckboxes = document.querySelectorAll(`input[data-rubrique="${rubriqueId}"].bpu-checkbox`);
        
        bpuCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selectedCheckboxes = document.querySelectorAll('.bpu-checkbox:checked');
        const countElement = document.getElementById('selectedCount');
        if (countElement) {
            countElement.textContent = `${selectedCheckboxes.length} BPU sélectionné(s)`;
        }
    }

    function copySelectedBpuToContract() {
        const selectedCheckboxes = document.querySelectorAll('.bpu-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Veuillez sélectionner au moins un BPU à copier.');
            return;
        }
        
        // Récupérer l'ID du contrat depuis la session Laravel
        const contratId = '{{ session("contrat_id") }}';
        
        if (!contratId || contratId === '') {
            alert('Erreur: ID du contrat non trouvé. Veuillez sélectionner un contrat.');
            return;
        }
        
        if (confirm(`Êtes-vous sûr de vouloir copier ${selectedIds.length} BPU vers le contrat ?`)) {
            fetch('{{ route("bpus.copyToContract") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    bpu_ids: selectedIds,
                    contrat_id: contratId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`${data.copied_count} BPU ont été copiés avec succès vers le contrat.`);
                    location.reload();
                } else {
                    alert('Erreur lors de la copie: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la copie des BPU.');
            });
        }
    }

    // Écouter les changements sur les cases à cocher individuelles
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('bpu-checkbox')) {
            updateSelectedCount();
        }
    });
</script>

<script src="{{ asset('js/bpu-calculator.js') }}"></script>
@endsection