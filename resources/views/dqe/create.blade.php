@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Nouveau DQE</h2>
            <h4>Contrat : {{ $contrat->nom_contrat }}</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dqe.index', $contrat->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('dqe.store', $contrat->id) }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reference">Référence</label>
                            <input type="text" class="form-control" id="reference" name="reference" value="{{ old('reference') }}">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">Créer et continuer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection