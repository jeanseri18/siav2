@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card text-white" style="background:#033765; padding: 20px; margin-bottom: 20px;">
        <font style="font-size: 30px;">Gestion des Bons de Commande</font>
        <div class="row">
            <div class="col-md-4" style="padding:5px">
                <a href="{{ route('bon-commandes.index') }}" class="btn btn-sm" 
                    style="background:#0A8CFF; padding: 5px 10px; color:white; width: 200px;">Liste des Bons</a>
            </div>
            <div class="col-md-4" style="padding:5px">
                <a href="{{ route('bon-commandes.create') }}" class="btn btn-sm" 
                    style="background:#0A8CFF; padding: 5px 10px; color:white; width: 200px;">Nouveau Bon</a>
            </div>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <h3>Liste des Bons de Commande</h3>
            
            <table id="bonCommandeTable" class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Fournisseur</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bonCommandes as $bonCommande)
                        <tr>
                            <td>{{ $bonCommande->reference }}</td>
                            <td>{{ $bonCommande->date_commande->format('d/m/Y') }}</td>
                            <td>{{ $bonCommande->fournisseur ? $bonCommande->fournisseur->nom_raison_sociale : 'N/A' }}</td>
                            <td>{{ number_format($bonCommande->montant_total, 0, ',', ' ') }} CFA</td>
                            <td>
                                @if($bonCommande->statut == 'en attente')
                                    <span class="badge bg-warning">En attente</span>
                                @elseif($bonCommande->statut == 'confirmée')
                                    <span class="badge bg-info">Confirmée</span>
                                @elseif($bonCommande->statut == 'livrée')
                                    <span class="badge bg-success">Livrée</span>
                                @elseif($bonCommande->statut == 'annulée')
                                    <span class="badge bg-danger">Annulée</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('bon-commandes.show', $bonCommande) }}" class="btn btn-info btn-sm">Voir</a>
                                @if($bonCommande->statut == 'en attente')
                                    <a href="{{ route('bon-commandes.edit', $bonCommande) }}" class="btn btn-warning btn-sm">Modifier</a>
                                    <form action="{{ route('bon-commandes.destroy', $bonCommande) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bon de commande?')">Supprimer</button>
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
        $('#bonCommandeTable').DataTable({
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