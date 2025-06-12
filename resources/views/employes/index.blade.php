@extends('layouts.app')

@section('title', 'Liste des Employés')
@section('page-title', 'Liste des Employés')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
<li class="breadcrumb-item active">Employés</li>
@endsection

@section('content')

<div class="app-fade-in">
    <div class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">
                <i class="fas fa-users me-2"></i>Liste des Employés
            </h2>
            <div class="app-card-actions">
                <a href="{{ route('employes.create') }}" class="app-btn app-btn-primary app-btn-icon">
                    <i class="fas fa-plus"></i>
                    Ajouter un employé
                </a>
            </div>
        </div>
        
        <div class="app-card-body">
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
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Nom Complet</th>
                            <th>Email</th>
                            <th>Poste/Rôle</th>
                            <th>Téléphone</th>
                            <th>Date d'embauche</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employes as $employe)
                        <tr>
                            <td>
                                @if($employe->photo)
                                    <img src="{{ asset('storage/' . $employe->photo) }}" alt="Photo" class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="avatar-initials" style="width: 40px; height: 40px; font-size: 14px;">
                                        {{ strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $employe->prenom }} {{ $employe->nom }}</span>
                                    @if($employe->poste)
                                        <small class="text-muted">{{ $employe->poste }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $employe->email }}</td>
                            <td>
                                @php
                                    $roleLabels = [
                                        'chef_projet' => 'Chef de Projet',
                                        'conducteur_travaux' => 'Conducteur de Travaux',
                                        'chef_chantier' => 'Chef de Chantier',
                                        'comptable' => 'Comptable',
                                        'magasinier' => 'Magasinier',
                                        'acheteur' => 'Acheteur',
                                        'controleur_gestion' => 'Contrôleur de Gestion',
                                        'secretaire' => 'Secrétaire',
                                        'chauffeur' => 'Chauffeur',
                                        'gardien' => 'Gardien',
                                        'employe' => 'Employé',
                                        'admin' => 'Administrateur',
                                        'dg' => 'Directeur Général'
                                    ];
                                    $roleClass = match($employe->role) {
                                        'admin', 'dg' => 'danger',
                                        'chef_projet', 'conducteur_travaux' => 'primary',
                                        'chef_chantier', 'comptable' => 'success',
                                        'magasinier', 'acheteur' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $roleClass }}">
                                    {{ $roleLabels[$employe->role] ?? ucfirst($employe->role) }}
                                </span>
                            </td>
                            <td>{{ $employe->telephone ?? '-' }}</td>
                            <td>
                                @if($employe->date_embauche)
                                    {{ $employe->date_embauche->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $employe->anciennete }} an(s)</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $employe->status === 'actif' ? 'success' : 'danger' }}">
                                    {{ ucfirst($employe->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="app-d-flex app-gap-1">
                                    <a href="{{ route('employes.show', $employe) }}" class="app-btn app-btn-info app-btn-sm app-btn-icon" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('employes.edit', $employe) }}" class="app-btn app-btn-warning app-btn-sm app-btn-icon" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($employe->role !== 'admin')
                                    <form action="{{ route('employes.destroy', $employe) }}" method="POST" class="delete-form" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-btn app-btn-danger app-btn-sm app-btn-icon delete-btn" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucun employé trouvé.</p>
                                <a href="{{ route('employes.create') }}" class="app-btn app-btn-primary">
                                    <i class="fas fa-plus me-2"></i>Ajouter le premier employé
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($employes->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $employes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Confirmation de suppression
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir supprimer cet employé ?')) {
                this.closest('form').submit();
            }
        });
    });
</script>
@endpush

@endsection