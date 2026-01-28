@extends('layouts.app')

@section('content')
@include('sublayouts.contrat')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Demandes de Ravitaillement</h3>
                    <a href="{{ route('demandes-ravitaillement.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle Demande
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Objet</th>
                                    <th>Contrat</th>
                                    <th>Projet</th>
                                    <th>Demandeur</th>
                                    <th>Statut</th>
                                    <th>Priorité</th>
                                    <th>Date Demande</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($demandes as $demande)
                                    <tr>
                                        <td>{{ $demande->reference }}</td>
                                        <td>{{ $demande->objet }}</td>
                                        <td>
                                            @if($demande->contrat)
                                                {{ $demande->contrat->reference ?? 'N/A' }}
                                                @if($demande->contrat->client)
                                                    <br><small class="text-muted">{{ $demande->contrat->client->nom }}</small>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($demande->contrat && $demande->contrat->projet)
                                                {{ $demande->contrat->projet->nom }}
                                            @else
                                                Aucun projet
                                            @endif
                                        </td>
                                        <td>
                                            @if($demande->demandeur)
                                                {{ $demande->demandeur->name }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($demande->statut) {
                                                    'en_attente' => 'warning',
                                                    'approuvee' => 'success',
                                                    'rejetee' => 'danger',
                                                    'en_cours' => 'info',
                                                    'livree' => 'primary',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ $demande->statut_label }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $priorityClass = match($demande->priorite) {
                                                    'urgente' => 'danger',
                                                    'haute' => 'warning',
                                                    'normale' => 'info',
                                                    'basse' => 'secondary',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $priorityClass }}">{{ $demande->priorite_label }}</span>
                                        </td>
                                        <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                        id="dropdownMenuButton{{ $demande->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-cog"></i> Actions
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $demande->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('demandes-ravitaillement.show', $demande->id) }}">
                                                            <i class="fas fa-eye me-2"></i> Voir
                                                        </a>
                                                    </li>
                                                    
                                                    @if($demande->statut === 'en_attente')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('demandes-ravitaillement.edit', $demande) }}">
                                                                <i class="fas fa-edit me-2"></i> Modifier
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('demandes-ravitaillement.approuver', $demande) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success" 
                                                                        onclick="return confirm('Êtes-vous sûr de vouloir approuver cette demande ?')">
                                                                    <i class="fas fa-check me-2"></i> Approuver
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('demandes-ravitaillement.rejeter', $demande) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                        onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')">
                                                                    <i class="fas fa-times me-2"></i> Rejeter
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('demandes-ravitaillement.destroy', $demande) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" 
                                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')">
                                                                    <i class="fas fa-trash me-2"></i> Supprimer
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @elseif($demande->statut === 'approuvee')
                                                        <li>
                                                            <form action="{{ route('demandes-ravitaillement.marquer-livree', $demande) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-primary" 
                                                                        onclick="return confirm('Êtes-vous sûr de vouloir marquer cette demande comme livrée ?')">
                                                                    <i class="fas fa-truck me-2"></i> Marquer comme livrée
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                    <td colspan="8" class="text-center">Aucune demande de ravitaillement trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($demandes->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $demandes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush