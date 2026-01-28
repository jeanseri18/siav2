<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document de Prestation - {{ $prestation->prestation_titre }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
        }
        
        .document-container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .document-header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .document-title {
            color: #0d6efd;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-section h4 {
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
            width: 200px;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #212529;
            flex: 1;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-en-cours {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-terminee {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-annulee {
            background-color: #f8d7da;
            color: #842029;
        }
        
        .table-comptes {
            margin-top: 20px;
        }
        
        .table-comptes th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="btn btn-primary btn-print no-print" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Imprimer
    </button>
    
    <div class="document-container">
        <!-- En-tête du document -->
        <div class="document-header">
            <div class="document-title">DOCUMENT DE PRESTATION</div>
            <div class="text-muted">Référence : PREST-{{ str_pad($prestation->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="text-muted">Date d'édition : {{ date('d/m/Y') }}</div>
        </div>
        
        <!-- Informations générales -->
        <div class="info-section">
            <h4><i class="fas fa-info-circle me-2"></i>Informations générales</h4>
            
            <div class="info-row">
                <div class="info-label">Titre de la prestation :</div>
                <div class="info-value">{{ $prestation->prestation_titre }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Contrat :</div>
                <div class="info-value">
                    {{ $prestation->contrat->nom_contrat ?? 'N/A' }}
                    @if($prestation->contrat)
                        <span class="text-muted">(Ref: {{ $prestation->contrat->ref_contrat }})</span>
                    @endif
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Corps de métier :</div>
                <div class="info-value">{{ $prestation->corpMetier->nom ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Statut :</div>
                <div class="info-value">
                    <span class="status-badge 
                        @if($prestation->statut == 'En cours') status-en-cours
                        @elseif($prestation->statut == 'Terminée') status-terminee
                        @else status-annulee
                        @endif">
                        {{ $prestation->statut }}
                    </span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Taux d'avancement :</div>
                <div class="info-value">
                    <strong>{{ $prestation->taux_avancement ?? 0 }}%</strong>
                    <div class="progress mt-2" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $prestation->taux_avancement ?? 0 }}%"
                             aria-valuenow="{{ $prestation->taux_avancement ?? 0 }}" 
                             aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informations du prestataire -->
        <div class="info-section">
            <h4><i class="fas fa-user me-2"></i>Prestataire</h4>
            
            @if($prestation->artisan)
                <div class="info-row">
                    <div class="info-label">Type :</div>
                    <div class="info-value">Artisan</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Nom :</div>
                    <div class="info-value">{{ $prestation->artisan->nom }} {{ $prestation->artisan->prenoms }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Téléphone :</div>
                    <div class="info-value">{{ $prestation->artisan->telephone ?? 'N/A' }}</div>
                </div>
                
                @if($prestation->date_affectation)
                <div class="info-row">
                    <div class="info-label">Date d'affectation :</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($prestation->date_affectation)->format('d/m/Y') }}</div>
                </div>
                @endif
            @elseif($prestation->fournisseur)
                <div class="info-row">
                    <div class="info-label">Type :</div>
                    <div class="info-value">Fournisseur</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Raison sociale :</div>
                    <div class="info-value">{{ $prestation->fournisseur->nom_raison_sociale }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Téléphone :</div>
                    <div class="info-value">{{ $prestation->fournisseur->telephone ?? 'N/A' }}</div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>Aucun prestataire affecté
                </div>
            @endif
        </div>
        
        <!-- Détails de la prestation -->
        <div class="info-section">
            <h4><i class="fas fa-file-alt me-2"></i>Détails de la prestation</h4>
            
            <div class="info-row">
                <div class="info-label">Description :</div>
                <div class="info-value">{{ $prestation->detail }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Montant total :</div>
                <div class="info-value">
                    <strong style="font-size: 18px; color: #0d6efd;">
                        {{ number_format($prestation->montant ?? 0, 0, ',', ' ') }} FCFA
                    </strong>
                </div>
            </div>
        </div>
        
        <!-- Comptes associés -->
        @if($prestation->comptes && $prestation->comptes->count() > 0)
        <div class="info-section">
            <h4><i class="fas fa-calculator me-2"></i>Comptes de prestation</h4>
            
            <table class="table table-bordered table-comptes">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type de compte</th>
                        <th>Description</th>
                        <th class="text-end">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalComptes = 0; @endphp
                    @foreach($prestation->comptes as $compte)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($compte->date_compte)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst(str_replace('_', ' ', $compte->type_compte)) }}
                            </span>
                        </td>
                        <td>{{ $compte->description }}</td>
                        <td class="text-end">{{ number_format($compte->montant, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @php $totalComptes += $compte->montant; @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" class="text-end">Total des comptes :</td>
                        <td class="text-end">{{ number_format($totalComptes, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">Montant restant :</td>
                        <td class="text-end">
                            <strong>{{ number_format(($prestation->montant ?? 0) - $totalComptes, 0, ',', ' ') }} FCFA</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
        
        <!-- Pied de page -->
        <div class="mt-5 pt-4 border-top text-center text-muted">
            <small>Document généré le {{ date('d/m/Y à H:i') }}</small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
