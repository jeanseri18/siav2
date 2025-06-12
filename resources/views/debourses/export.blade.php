<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $typeLabel }} - {{ $debourse->contrat->ref_contrat }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #033765;
            margin-bottom: 5px;
        }
        h2 {
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #033765;
            padding-bottom: 20px;
        }
        .logo {
            max-width: 120px;
            max-height: 80px;
        }
        .company-info {
            text-align: right;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            @if($debourse->contrat && $debourse->contrat->bus && $debourse->contrat->bus->logo)
                <img src="{{ public_path('storage/' . $debourse->contrat->bus->logo) }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="company-info">
            <h1>{{ $typeLabel }}</h1>
            <h2>Contrat : {{ $debourse->contrat->nom_contrat }}</h2>
            @if($debourse->contrat && $debourse->contrat->bus)
                <div>{{ $debourse->contrat->bus->nom }}</div>
            @endif
        </div>
    </div>
    
    <div class="info">
        <p><strong>Référence contrat :</strong> {{ $debourse->contrat->ref_contrat }}</p>
        <p><strong>DQE de référence :</strong> {{ $debourse->dqe->reference ?? 'Sans référence' }}</p>
        <p><strong>Date de génération :</strong> {{ $debourse->created_at->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>Unité</th>
                @if($debourse->type == 'sec')
                    <th>Matériaux</th>
                    <th>Main d'Œuvre</th>
                    <th>Matériel</th>
                @elseif($debourse->type == 'main_oeuvre')
                    <th>Main d'Œuvre Unitaire</th>
                @else
                    <th>Frais de Chantier Unitaire</th>
                @endif
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($debourse->details as $detail)
                <tr>
                    <td>{{ $detail->dqeLigne->designation }}</td>
                    <td>{{ $detail->dqeLigne->quantite }}</td>
                    <td>{{ $detail->dqeLigne->unite }}</td>
                    @if($debourse->type == 'sec')
                        <td>{{ number_format($detail->dqeLigne->bpu->materiaux, 2, ',', ' ') }}</td>
                        <td>{{ number_format($detail->dqeLigne->bpu->main_oeuvre, 2, ',', ' ') }}</td>
                        <td>{{ number_format($detail->dqeLigne->bpu->materiel, 2, ',', ' ') }}</td>
                    @elseif($debourse->type == 'main_oeuvre')
                        <td>{{ number_format($detail->dqeLigne->bpu->main_oeuvre, 2, ',', ' ') }}</td>
@else
                        <td>{{ number_format($detail->dqeLigne->bpu->frais_chantier, 2, ',', ' ') }}</td>
                    @endif
                    <td>{{ number_format($detail->montant, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Montant total : {{ number_format($debourse->montant_total, 2, ',', ' ') }}</p>
    </div>

    <div class="footer">
        <p>Document généré le {{ date('d/m/Y') }}</p>
        <p>© {{ date('Y') }} - Votre Entreprise</p>
    </div>
</body>
</html>