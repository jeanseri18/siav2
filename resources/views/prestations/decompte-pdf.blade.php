<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décompte - {{ $decompte->titre }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 120px;
            max-height: 80px;
        }
        .company-info {
            text-align: right;
        }
        .company-info p {
            margin: 5px 0;
            line-height: 1.6;
        }
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            text-align: center;
        }
        .document-details {
            margin-bottom: 30px;
        }
        .document-details table {
            width: 100%;
        }
        .document-details td {
            padding: 5px 0;
        }
        .prestation-info {
            float: left;
            width: 50%;
        }
        .decompte-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .clear {
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            float: right;
            width: 40%;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 5px;
        }
        .totals td:last-child {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .total-highlight {
            font-size: 16px;
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #007bff;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print:hover {
            background-color: #0056b3;
        }
        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">
     Imprimer
    </button>

    <div class="header">
        <div>
            @if(auth()->user() && auth()->user()->bus && auth()->user()->bus->logo)
                <img src="{{ public_path('storage/' . auth()->user()->bus->logo) }}" alt="Logo" class="logo">
            @else
                <h2 style="margin: 0; color: #007bff;">{{ auth()->user()->bus->nom ?? 'SIA' }}</h2>
            @endif
        </div>
        <div class="company-info">
            @if($prestation->artisan)
                {{-- Informations Artisan --}}
                <p><strong>{{ $prestation->artisan->nom }} {{ $prestation->artisan->prenoms }}</strong></p>
                <p>{{ $prestation->artisan->adresse ?? 'Adresse non renseignée' }}</p>
                <p>Tel: {{ $prestation->artisan->telephone ?? 'N/A' }} 
                @if($prestation->artisan->email)
                    | Email: {{ $prestation->artisan->email }}
                @endif
                </p>
                @if($prestation->artisan->specialite)
                    <p>Spécialité: {{ $prestation->artisan->specialite }}</p>
                @endif
            @elseif($prestation->fournisseur)
                {{-- Informations Fournisseur --}}
                <p><strong>{{ $prestation->fournisseur->nom_raison_sociale }}</strong></p>
                <p>{{ $prestation->fournisseur->adresse ?? 'Adresse non renseignée' }}</p>
                <p>Tel: {{ $prestation->fournisseur->telephone ?? 'N/A' }} 
                @if($prestation->fournisseur->email)
                    | Email: {{ $prestation->fournisseur->email }}
                @endif
                </p>
                @if($prestation->fournisseur->rccm || $prestation->fournisseur->ifu)
                    <p>
                        @if($prestation->fournisseur->rccm)
                            RCCM: {{ $prestation->fournisseur->rccm }}
                        @endif
                        @if($prestation->fournisseur->ifu)
                            | IFU: {{ $prestation->fournisseur->ifu }}
                        @endif
                    </p>
                @endif
            @else
                {{-- Aucun prestataire affecté --}}
                <p><strong>Prestataire</strong></p>
                <p>Non affecté</p>
                <p>Tel: N/A | Email: N/A</p>
            @endif
        </div>
    </div>

    <div class="document-title">DÉCOMPTE DE PRESTATION</div>

    <div class="document-details">
        <div class="prestation-info">
            <h3 style="color: #007bff;">Informations Prestation</h3>
            <table style="border: none;">
                <tr>
                    <td style="border: none;"><strong>Titre:</strong></td>
                    <td style="border: none;">{{ $prestation->prestation_titre }}</td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Contrat:</strong></td>
                    <td style="border: none;">{{ $prestation->contrat->nom_contrat ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Réf. Contrat:</strong></td>
                    <td style="border: none;">{{ $prestation->contrat->ref_contrat ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Prestataire:</strong></td>
                    <td style="border: none;">
                        @if($prestation->artisan)
                            {{ $prestation->artisan->nom }} {{ $prestation->artisan->prenoms }}
                        @elseif($prestation->fournisseur)
                            {{ $prestation->fournisseur->nom_raison_sociale }}
                        @else
                            Non affecté
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="decompte-info">
            <h3 style="color: #007bff;">Informations Décompte</h3>
            <table style="border: none;">
                <tr>
                    <td style="border: none;"><strong>Titre:</strong></td>
                    <td style="border: none;">{{ $decompte->titre }}</td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Date d'émission:</strong></td>
                    <td style="border: none;">{{ $decompte->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Pourcentage:</strong></td>
                    <td style="border: none;">
                        <span class="badge badge-info">{{ number_format($decompte->pourcentage, 2) }}%</span>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Montant:</strong></td>
                    <td style="border: none;">
                        <strong style="font-size: 16px; color: #007bff;">
                            {{ number_format($decompte->montant, 2, ',', ' ') }} FCFA
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="clear"></div>
    </div>

    <!-- Détails des lignes du décompte -->
    <div class="section-title">DÉTAILS DES LIGNES DE PRESTATION</div>

    @php
        // Récupérer les lignes de prestation qui ont été payées dans ce décompte
        // On va afficher toutes les lignes de la prestation avec leur état
        $lignes = \App\Models\LignePrestation::where('id_prestation', $prestation->id)
            ->with(['rubrique.sousCategorie.categorie'])
            ->get();
    @endphp

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">N°</th>
                <th style="width: 30%;">Désignation</th>
                <th style="width: 10%;">Unité</th>
                <th style="width: 10%;">Quantité</th>
                <th style="width: 12%;">Coût unitaire</th>
                <th style="width: 12%;">Montant total</th>
                <th style="width: 10%;">Taux avanc.</th>
                <th style="width: 11%;">Montant payé</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalMontant = 0;
                $totalPaye = 0;
            @endphp
            
            @foreach($lignes as $index => $ligne)
                @php
                    $totalMontant += $ligne->montant;
                    $totalPaye += $ligne->montant_paye;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $ligne->designation }}</td>
                    <td class="text-center">{{ $ligne->unite }}</td>
                    <td class="text-end">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($ligne->cout_unitaire, 2, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($ligne->montant, 2, ',', ' ') }}</td>
                    <td class="text-center">
                        <span class="badge badge-info">{{ number_format($ligne->taux_avancement, 2) }}%</span>
                    </td>
                    <td class="text-end">{{ number_format($ligne->montant_paye, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
            
            <!-- Total -->
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="5" class="text-end">TOTAL</td>
                <td class="text-end">{{ number_format($totalMontant, 2, ',', ' ') }}</td>
                <td></td>
                <td class="text-end" style="color: #007bff;">{{ number_format($totalPaye, 2, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Récapitulatif -->
    <div class="totals">
        <table>
            <tr>
                <td><strong>Montant total prestation</strong></td>
                <td>{{ number_format($totalMontant, 2, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Total déjà payé</strong></td>
                <td>{{ number_format($totalPaye, 2, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Reste à payer</strong></td>
                <td>{{ number_format($totalMontant - $totalPaye, 2, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="total-highlight">
                <td><strong>CE DÉCOMPTE</strong></td>
                <td>{{ number_format($decompte->montant, 2, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td><strong>Taux d'avancement global</strong></td>
                <td>
                    <span class="badge badge-success">{{ number_format($prestation->taux_avancement ?? 0, 2) }}%</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>

    <!-- Informations supplémentaires -->
    <div class="section-title">INFORMATIONS COMPLÉMENTAIRES</div>
    <table style="border: none;">
        <tr>
            <td style="border: none; width: 30%;"><strong>Corps de métier:</strong></td>
            <td style="border: none;">{{ $prestation->corpMetier->nom ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Statut prestation:</strong></td>
            <td style="border: none;">
                <span class="badge 
                    @if($prestation->statut == 'En cours') badge-info
                    @else badge-success
                    @endif">
                    {{ $prestation->statut }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="border: none;"><strong>Date création prestation:</strong></td>
            <td style="border: none;">{{ $prestation->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p><strong>Conditions de paiement :</strong><br>
        Paiement par virement bancaire : Banque: XXXXX | N° de compte: XXXXXXXXXX</p>
        <p>Ce décompte est émis dans le cadre du suivi de l'avancement des travaux.<br>
        Pour toute question concernant ce décompte, veuillez contacter notre service comptabilité.</p>
        <p><strong>Document généré le {{ date('d/m/Y à H:i') }}</strong></p>
    </div>
</body>
</html>
