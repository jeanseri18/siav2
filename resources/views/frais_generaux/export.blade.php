<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frais Généraux - {{ $fraisGeneral->contrat->ref_contrat }}</title>
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
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }
      .calculation {
            margin-top: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
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
            @if($fraisGeneral->contrat && $fraisGeneral->contrat->bus && $fraisGeneral->contrat->bus->logo)
                <img src="{{ public_path('storage/' . $fraisGeneral->contrat->bus->logo) }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="company-info">
            <h1>Frais Généraux</h1>
            <h2>{{ $fraisGeneral->contrat->ref_contrat }}</h2>
            @if($fraisGeneral->contrat && $fraisGeneral->contrat->bus)
                <div>{{ $fraisGeneral->contrat->bus->nom }}</div>
            @endif
        </div>
    </div>
    
    <div class="info">
        <p><strong>Référence contrat :</strong> {{ $fraisGeneral->contrat->ref_contrat }}</p>
        <p><strong>Client :</strong> {{ $fraisGeneral->contrat->client->nom ?? 'Non défini' }}</p>
<p><strong>Date de calcul :</strong> 
    @if($fraisGeneral->date_calcul instanceof \Carbon\Carbon)
        {{ $fraisGeneral->date_calcul->format('d/m/Y') }}
    @else
        {{ date('d/m/Y', strtotime($fraisGeneral->date_calcul)) }}
    @endif
</p>        <p><strong>Statut :</strong> {{ ucfirst($fraisGeneral->statut) }}</p>
    </div>

    <div class="calculation">
        <h3>Détails du calcul</h3>
        <table>
            <tr>
                <td><strong>Montant de base</strong></td>
                <td>{{ number_format($fraisGeneral->montant_base, 2, ',', ' ') }}</td>
            </tr>
            <tr>
                <td><strong>Pourcentage appliqué</strong></td>
                <td>{{ $fraisGeneral->pourcentage }}%</td>
            </tr>
            <tr>
                <td><strong>Formule</strong></td>
                <td>{{ number_format($fraisGeneral->montant_base, 2, ',', ' ') }} × {{ $fraisGeneral->pourcentage }}%</td>
            </tr>
            <tr>
                <td><strong>Montant total des frais généraux</strong></td>
                <td>{{ number_format($fraisGeneral->montant_total, 2, ',', ' ') }}</td>
            </tr>
        </table>
    </div>

    @if($fraisGeneral->description)
    <div>
        <h3>Description / Notes</h3>
        <p>{{ $fraisGeneral->description }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Document généré le {{ date('d/m/Y') }}</p>
        <p>© {{ date('Y') }} - Votre Entreprise</p>
    </div>
</body>
</html>