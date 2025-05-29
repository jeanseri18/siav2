<!-- resources/views/ventes/report_pdf.blade.php -->
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Rapport des Ventes</h1>
    <table>
        <thead>
            <tr>
                <th>ID Vente</th>
                <th>Client</th>
                <th>Date</th>
                <th>Total</th>
                <th>Articles</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventes as $vente)
                <tr>
                    <td>{{ $vente->id }}</td>
                    <td>{{ $vente->client->prenoms }}</td>
                    <td>{{ $vente->created_at->format('d-m-Y') }}</td>
                    <td>{{ $vente->total }} FCFA</td>
                    <td>
                        <ul>
                            @foreach($vente->articles as $article)
                                <li>{{ $article->nom }} ({{ $article->pivot->quantite }})</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
