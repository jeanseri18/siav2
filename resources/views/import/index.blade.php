@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Importer un fichier Excel</h3>
                </div>
                <div class="card-body">
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

                    <form action="{{ route('import.create') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="file">Choisir un fichier Excel</label>
                            <input type="file" name="file" class="form-control mt-2" required>
                            <small class="form-text text-muted">
                                Formats acceptés: .xlsx, .xls, .csv
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <h5>Structure du fichier:</h5>
                            <p>Votre fichier Excel doit suivre cette structure:</p>
                            <ul>
                                <li>Colonne A: Code ou identifiant</li>
                                <li>Colonne B: Désignation</li>
                                <li>Colonne C: Unité</li>
                                <li>Colonne D: Matériaux</li>
                                <li>Colonne E: Main d'œuvre</li>
                                <li>Colonne F: Matériel</li>
                            </ul>
                            <p>Pour indiquer la hiérarchie, utilisez:</p>
                            <ul>
                                <li>"categorie" en colonne A pour une nouvelle catégorie</li>
                                <li>"souscategorie" en colonne A pour une nouvelle sous-catégorie</li>
                                <li>"rubrique" en colonne A pour une nouvelle rubrique</li>
                                <li>"bpu" en colonne A pour une ligne de BPU</li>
                            </ul>
                            <p>Exemple:</p>
                            <pre>
categorie      GROS ŒUVRE
souscategorie  FOUILLES
rubrique       FOUILLES EN MASSE
bpu            Fouille en masse, par des moyens mécaniques     m3     1000    300     200
bpu            Evacuation des déblais                          m3     500     150     100
                            </pre>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bpu.index') }}" class="btn btn-secondary">Retour</a>
                            <button type="submit" class="btn btn-primary">Importer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection