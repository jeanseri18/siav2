@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card text-white" style="background:#033765; padding: 20px; margin-bottom: 20px;">
        <font style="font-size: 30px;">Détails du Bon de Commande</font>
        <div class="row">
            <div class="col-md-4" style="padding:5px">
                <a href="{{ route('bon-commandes.index') }}" class="btn btn-sm" 
                    style="background:#0A8CFF; padding: 5px 10px; color:white; width: 200px;">Retour à la liste</a>
            </div>
            <div class="col-md-4" style="padding:5px">
                <a href="javascript:window.print()" class="btn btn-sm" 
                    style="background:#0A8CFF; padding: 5px 10px; color:white; width: 200px;">Imprimer</a>
            </div>
        </div>
    </div>

    <div class="card custom-card" id="printable-content">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <h3>BON DE COMMANDE</h3>
                    <h4>{{ $bonCommande->reference }}</h4>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th>Référence</th>
                            <td>{{ $bonCommande->reference }}</td>
                        </tr>
                        <tr>
                            <th>Date de commande</th>
                            <td>{{ $bonCommande->date_commande->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Date de livraison prévue</th>
                            <td>{{ $bonCommande->date_livraison_prevue ? $bonCommande->date_livraison_prevue->format('d/m/Y') : 'Non spécifiée' }}</td>
                        </tr>
                        <tr>
                            <th>Fournisseur</th>
                            <td>{{ $bonCommande->fournisseur ? $bonCommande->fournisseur->nom_raison_sociale : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Conditions de paiement</th>
                            <td>{{ $bonCommande->conditions_paiement ?: 'Non spécifiées' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th>Statut</th>
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
                        </tr>
                        <tr>
                            <th>Demande d'appro liée</th>
                            <td>
                                @if($bonCommande->demandeApprovisionnement)
                                    <a href="{{ route('demande-approvisionnements.show', $bonCommande->demandeApprovisionnement) }}">
                                        {{ $bonCommande->demandeApprovisionnement->reference }}
                                    </a>
                                @else
                                    Aucune
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Demande d'achat liée</th>
                            <td>
                                @if($bonCommande->demandeAchat)
                                    <a href="{{ route('demande-achats.show', $bonCommande->demandeAchat) }}">
                                        {{ $bonCommande->demandeAchat->reference }}
                                    </a>
                                @else
                                    Aucune
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Créé par</th>
                            <td>{{ $bonCommande->user ? $bonCommande->user->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Créé le</th>
                            <td>{{ $bonCommande->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($bonCommande->notes)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Notes</strong>
                            </div>
                            <div class="card-body">
                                {{ $bonCommande->notes }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4>Articles commandés</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Désignation</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Montant</th>
                                    @if($bonCommande->statut == 'livrée')
                                        <th>Quantité livrée</th>
                                    @endif
                                    <th>Commentaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bonCommande->lignes as $ligne)
                                    <tr>
                                        <td>{{ $ligne->article->reference }}</td>
                                        <td>{{ $ligne->article->nom }}</td>
                                        <td>{{ $ligne->quantite }} {{ $ligne->article->unite_mesure }}</td>
                                        <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} CFA</td>
                                        <td>{{ number_format($ligne->quantite * $ligne->prix_unitaire, 0, ',', ' ') }} CFA</td>
                                        @if($bonCommande->statut == 'livrée')
                                            <td>{{ $ligne->quantite_livree }} {{ $ligne->article->unite_mesure }}</td>
                                        @endif
                                        <td>{{ $ligne->commentaire }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="{{ $bonCommande->statut == 'livrée' ? '6' : '5' }}" class="text-end">Total</th>
                                    <th>{{ number_format($bonCommande->montant_total, 0, ',', ' ') }} CFA</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4 no-print">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between">
                        <div>
                            @if($bonCommande->statut == 'en attente')
                                <a href="{{ route('bon-commandes.edit', $bonCommande) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            @endif
                        </div>
                        <div>
                            @if($bonCommande->statut == 'en attente')
                                <form action="{{ route('bon-commandes.confirm', $bonCommande) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Confirmer
                                    </button>
                                </form>
                            @endif
                            
                            @if($bonCommande->statut == 'confirmée')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#livrerModal">
                                    <i class="fas fa-truck"></i> Marquer comme livré
                                </button>
                            @endif
                            
                            @if($bonCommande->statut != 'livrée' && $bonCommande->statut != 'annulée')
                                <form action="{{ route('bon-commandes.cancel', $bonCommande) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce bon de commande?')">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce bon de commande ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('bon-commandes.destroy', $bonCommande) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de livraison -->
<div class="modal fade" id="livrerModal" tabindex="-1" aria-labelledby="livrerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="livrerModalLabel">Marquer comme livré</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bon-commandes.livrer', $bonCommande) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Veuillez spécifier les quantités livrées pour chaque article :</p>
                    
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Désignation</th>
                                <th>Quantité commandée</th>
                                <th>Quantité livrée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bonCommande->lignes as $index => $ligne)
                                <tr>
                                    <td>{{ $ligne->article->reference }}</td>
                                    <td>{{ $ligne->article->nom }}</td>
                                    <td>{{ $ligne->quantite }} {{ $ligne->article->unite_mesure }}</td>
                                    <td>
                                        <input type="number" name="quantite_livree[{{ $index }}]" class="form-control" 
                                            min="0" max="{{ $ligne->quantite }}" value="{{ $ligne->quantite }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Confirmer la livraison</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-card {
        border: 1px solid #033765;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            padding: 0;
            margin: 0;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            padding: 0;
            margin: 0;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .table {
            width: 100%;
        }
        
        .badge {
            border: 1px solid #000;
            color: #000 !important;
            background-color: transparent !important;
        }
    }
</style>
@endsection