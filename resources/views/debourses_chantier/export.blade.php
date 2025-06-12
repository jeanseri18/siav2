<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déboursé Chantier - {{ $debourseChantier->contrat->ref_contrat }}</title>
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
            color: #033765;
            margin-bottom: 10px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #033765;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 120px;
            max-height: 80px;
        }
        .company-info {
            text-align: right;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
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
            background-color: #f8f9fa;
            font-weight: bold;
            color: #033765;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
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
            @if($debourseChantier->contrat && $debourseChantier->contrat->bus && $debourseChantier->contrat->bus->logo)
                <img src="{{ public_path('storage/' . $debourseChantier->contrat->bus->logo) }}" alt="Logo" class="logo">
            @endif
        </div>
        <div class="company-info">
            <h1>Déboursé Chantier</h1>
            <h2>{{ $debourseChantier->contrat->ref_contrat }}</h2>
            @if($debourseChantier->contrat && $debourseChantier->contrat->bus)
                <div>{{ $debourseChantier->contrat->bus->nom }}</div>
            @endif
        </div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Référence :</span>
            <span>{{ $debourseChantier->reference }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contrat :</span>
            <span>{{ $debourseChantier->contrat->ref_contrat }} - {{ $debourseChantier->contrat->nom_contrat }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">DQE :</span>
            <span>{{ $debourseChantier->dqe->reference ?? 'Sans référence' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Statut :</span>
            <span>{{ ucfirst($debourseChantier->statut) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de création :</span>
            <span>{{ $debourseChantier->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>Unité</th>
                <th>Matériaux (FCFA)</th>
                <th>Main d'œuvre (FCFA)</th>
                <th>Matériel (FCFA)</th>
                <th>Déboursé Sec (FCFA)</th>
                <th>Déboursé M.O. (FCFA)</th>
                <th>Total (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($debourseChantier->details as $detail)
                <tr>
                    <td>{{ $detail->section }}</td>
                    <td>{{ $detail->designation }}</td>
                    <td class="text-right">{{ number_format($detail->quantite, 2, ',', ' ') }}</td>
                    <td>{{ $detail->unite }}</td>
                    <td class="text-right">{{ number_format($detail->cout_unitaire_materiaux, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($detail->cout_unitaire_main_oeuvre, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($detail->cout_unitaire_materiel, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($detail->debourse_sec ?? 0, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($detail->debourse_main_oeuvre ?? 0, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($detail->montant_total, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="9" class="text-right"><strong>Total Général :</strong></td>
                <td class="text-right"><strong>{{ number_format($debourseChantier->montant_total, 2, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Système de gestion SIA - Déboursé Chantier</p>
    </div>
</body>
</html>