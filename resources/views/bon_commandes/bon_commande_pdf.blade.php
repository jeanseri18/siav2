<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Commande - {{ $bonCommande->reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 6px; vertical-align: top; }
        .header td { border:none; padding: 8px 0; }
        .logo { width: 90px; }
        .title { font-size: 18px; font-weight: bold; text-align: center; background: #333; color: white; padding: 8px; }
        .strong { font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .border-top { border-top: 2px solid #000; }
        .bg-gray { background-color: #f0f0f0; }
        .underline { text-decoration: underline; }
        .mt20 { margin-top: 20px; }
        .signature { margin-top: 50px; text-align: center; }
    </style>
</head>
<body>

<table class="header">
    <tr>
        <td width="20%" class="text-center">
            @if($configGlobal && $configGlobal->logo)
                <img src="{{ asset('storage/' . $configGlobal->logo) }}" alt="Logo {{ $configGlobal->nom_entreprise ?? 'SIA' }}" class="logo">
            @else
                <img src="https://via.placeholder.com/120x120/003087/ffffff?text=SIA" alt="Logo SIA" class="logo">
            @endif
            <br><strong>{{ $configGlobal->nom_entreprise ?? 'SIA-Sarl' }}</strong>
        </td>
        <td width="40%" class="strong">
            {{ $configGlobal->nom_entreprise ?? 'SOCIETE D\'INGENIERIE EN AFRIQUE' }}<br>
            {{ $configGlobal->localisation ?? 'Bingerville, cité colombe 1, Ilot 20, Lot 134' }}<br>
            {{ $configGlobal->adresse_postale ?? '18 BP 682 Abidjan 18' }}<br>
            @if($configGlobal && $configGlobal->tel1)
                Tél : {{ $configGlobal->tel1 }}<br>
            @endif
            @if($configGlobal && $configGlobal->email)
                Email : {{ $configGlobal->email }}<br>
            @endif
        </td>
        <td width="40%" class="strong text-right">
            {{ $bonCommande->fournisseur ? $bonCommande->fournisseur->nom_raison_sociale : 'FOURNISSEUR' }}<br>
            {{ $bonCommande->fournisseur && $bonCommande->fournisseur->adresse ? $bonCommande->fournisseur->adresse : 'Adresse du fournisseur' }}<br>
            {{ $bonCommande->fournisseur && $bonCommande->fournisseur->telephone ? 'Tél : ' . $bonCommande->fournisseur->telephone : '' }}<br>
            {{ $bonCommande->fournisseur && $bonCommande->fournisseur->email ? 'Email: ' . $bonCommande->fournisseur->email : '' }}
        </td>
    </tr>
</table>

<table class="title">
    <tr>
        <td><strong>DONNEUR D'ORDRE :</strong></td>
        <td><strong>FOURNISSEUR :</strong></td>
    </tr>
</table>

<table>
    <tr>
        <td width="50%">
            <strong>VEUILLEZ LIVRER LES MARCHANDISES A :</strong><br>
            {{ $configGlobal->nom_entreprise ?? 'SIA-Sarl' }}<br>
            {{ $configGlobal->localisation ?? 'Bingerville, cité colombe 1 Ilot 20, lot 134' }} - {{ $configGlobal->adresse_postale ?? '18 BP 682 Abidjan 18' }}<br>
            Horaire d'ouverture : 8:00 – 17:00, du lundi au vendredi
        </td>
        <td width="50%">
            <strong>CONDITION DE LIVRAISON :</strong><br>
            Date du document : {{ $bonCommande->date_commande->format('d/m/Y') }}<br>
            @if($bonCommande->projet)
                Num. du projet : <strong>{{ $bonCommande->projet->nom_projet }}</strong><br>
            @endif
            @if($bonCommande->reference)
                N° Document externe : <strong>{{ $bonCommande->reference }}</strong><br>
            @endif
            Conditions de paiement : <strong>{{ $bonCommande->mode_reglement ? $bonCommande->mode_reglement : 'CASH - Chèque' }}</strong><br>
            Délai : {{ $bonCommande->delai_reglement ? $bonCommande->delai_reglement : 'En référencement au contrat' }}<br>
            Responsable du contrat : -<br>
            Lieu de livraison : <strong>{{ $bonCommande->lieu_livraison ? $bonCommande->lieu_livraison : 'CITE FPI / ANGRE CHATEAU' }}</strong>
        </td>
    </tr>
</table>

<table>
    <tr>
        <td width="50%">
            <strong>VEUILLEZ TRANSMETTRE LA FACTURE A :</strong><br>
            {{ $configGlobal->nom_entreprise ?? 'SIA-Sarl' }}<br>
            {{ $configGlobal->localisation ?? 'Bingerville, cité colombe 1 Ilot 20, lot 134' }} - {{ $configGlobal->adresse_postale ?? '18 BP 682 Abidjan 18' }}<br>
            Horaire d'ouverture : 8:00 – 17:00, du lundi au vendredi
        </td>
        <td width="50%"></td>
    </tr>
</table>

<br>

<table>
    <thead class="bg-gray">
        <tr>
            <th>N° Ligne</th>
            <th>Réf. Article</th>
            <th>Désignation de l'article</th>
            <th>Unité</th>
            <th>Qté</th>
            <th>Prix unitaire HT</th>
            <th>% Remise</th>
            <th>Montant HT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bonCommande->lignes as $index => $ligne)
        <tr>
            <td>{{ sprintf('%03d', $index + 1) }}</td>
            <td>{{ $ligne->article ? $ligne->article->reference : '-' }}</td>
            <td>{{ $ligne->article ? $ligne->article->nom : $ligne->article_id }}</td>
            <td>{{ $ligne->article && $ligne->article->uniteMesure ? $ligne->article->uniteMesure->nom : 'U' }}</td>
            <td>{{ $ligne->quantite }}</td>
            <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }}</td>
            <td>{{ $ligne->remise }}</td>
            <td>{{ number_format($ligne->quantite * $ligne->prix_unitaire * (1 - $ligne->remise / 100), 0, ',', ' ') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="mt20">
    <tr>
        <td width="60%">
            Arrêtée le montant de cette commande à la somme de :<br>
            <strong>{{ ucfirst($bonCommande->montant_total_lettres ?? 'Montant en lettres non défini') }}</strong>
        </td>
        <td width="40%">
            <table width="100%">
                <tr><td>Montant Total Hors Taxe</td><td class="text-right">{{ number_format($bonCommande->montant_total, 0, ',', ' ') }}</td></tr>
                <tr><td>Montant total Remise</td><td class="text-right">-</td></tr>
                <tr><td>Montant Total HT Net</td><td class="text-right">{{ number_format($bonCommande->montant_total, 0, ',', ' ') }}</td></tr>
                <tr class="border-top"><td><strong>TVA 18%</strong></td><td class="text-right"><strong>{{ number_format($bonCommande->montant_total * 0.18, 0, ',', ' ') }}</strong></td></tr>
                <tr class="border-top"><td><strong>Montant TTC</strong></td><td class="text-right"><strong>{{ number_format($bonCommande->montant_total * 1.18, 0, ',', ' ') }}</strong></td></tr>
            </table>
        </td>
    </tr>
</table>

<div class="signature">
    <p>Visa Resp. Financier : {{ $bonCommande->date_commande->format('d/m/Y') }}<br>
    Signature D.G : {{ $bonCommande->date_commande->format('d/m/Y') }}</p>
    <br><br>
    _________________________<br>
    <strong>Le Directeur Général</strong>
</div>

<script>
    window.onload = function() {
        window.print();
    };
</script>

</body>
</html>