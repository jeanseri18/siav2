@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card text-white" style="background:#033765; padding: 20px; margin-bottom: 20px;">
        <font style="font-size: 30px;">Gestion des Demandes de Cotation</font>
        <div class="row">
            <div class="col-md-4" style="padding:5px">
                <a href="{{ route('demande-cotations.index') }}" class="btn btn-sm" 
                    style="background:#0A8CFF; padding: 5px 10px; color:white; width: 200px;">Liste des Demandes</a>
            </div>
            <div class="col-md-4" style="padding:5px">
                <a href="{{ route('demande-cotations.create') }}" class="btn btn-sm" 
                    style="background:#0A8CFF; padding: 5px 10px; color:white; width: 200px;">Nouvelle Demande</a>
            </div>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <h3>Liste des Demandes de Cotation</h3>
            
            <table id="demandeTable" class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Date expiration</th>
                        <th>Nb Fournisseurs</th>
                        <th>Demande achat liée</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($demandes as $demande)
                        <tr>
                            <td>{{ $demande->reference }}</td>
                            <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                            <td>{{ $demande->date_expiration->format('d/m/Y') }}</td>
                            <td>{{ $demande->fournisseurs->count() }}</td>
                            <td>
                                @if($demande->demandeAchat)
                                    <a href="{{ route('demande-achats.show', $demande->demandeAchat) }}">
                                        {{ $demande->demandeAchat->reference }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($demande->statut == 'en cours')
                                    <span class="badge bg-warning">En cours</span>
                                @elseif($demande->statut == 'terminée')
                                    <span class="badge bg-success">Terminée</span>
                                @elseif($demande->statut == 'annulée')
                                    <span class="badge bg-danger">Annulée</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('demande-cotations.show', $demande) }}" class="btn btn-info btn-sm">Voir</a>
                                @if($demande->statut == 'en cours')
                                    <a href="{{ route('demande-cotations.edit', $demande) }}" class="btn btn-warning btn-sm">Modifier</a>
                                    <form action="{{ route('demande-cotations.destroy', $demande) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande?')">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .custom-card {
        border: 1px solid #033765;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .custom-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }
    
    .table-bordered th, .table-bordered td {
        border: 1px solid #ddd;
    }
</style>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#demandeTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            }
        });
    });
</script>
@endpush