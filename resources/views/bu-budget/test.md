**Analyse de l'Excel : définition des champs des tables et formules automatiques**

L’Excel est structuré en **9 feuilles** (onglets). Chaque feuille correspond à une **table** avec une disposition fixe (lignes et colonnes prédéfinies, pas de lignes dynamiques).

Les champs sont de deux types :

- **Inputs** (saisis manuellement par l’utilisateur)
- **Outputs** (calculés automatiquement et non éditables)

Voici le détail par feuille :

### 1. Hypotheses (table d’entrée principale)

**Colonnes** : Références | Paramètres | Valeurs\
**Champs (exemple inputs)** :

- Construction bâtiment → Nombre de chantiers bâtiment (int)
- Construction bâtiment → Montant moyen chantier bâtiment (FCFA) (numérique)
- VRD / Voirie → Nombre de chantiers VRD (int)
- VRD / Voirie → Montant moyen chantier VRD (FCFA)
- Petits travaux → Nombre petits travaux (int)
- Petits travaux → Montant moyen petit travaux (FCFA)

Aucune formule. Ces valeurs sont la source de tous les calculs suivants.

### 2. Chiffre\_Affaires (table générée)

**Colonnes** : N° Ligne | Type de travaux | Nombre | Montant unitaire (FCFA) | Montant annuel (FCFA)\
**Lignes fixes** (lignes  saisi+ total) :

exemple:

- Ligne 1 : Construction bâtiment
- Ligne 2 : VRD / Voirie
- Ligne 3 : Petits travaux
- Ligne Total CA

**Formules automatiques** :

- Montant unitaire = Valeur du paramètre correspondant dans Hypotheses
- Montant annuel = Nombre × Montant unitaire\
  (ex. : ( 3 \times 35,000,000 = 105,000,000 ))
- Total CA = (\sum) Montant annuel des 3 lignes\
  (dans l’exemple : 150 000 000 FCFA)

### 3. Cout\_Chantiers (table d’entrée coûts directs chantiers)

**Colonnes** : N° Ligne | Poste | Montant annuel (FCFA)\
**Postes fixes**  : exemple(Matériaux, Matériels, Main d’œuvre chantier, Location engins, Transport matériaux, Carburant)\
**Formule** :\
Total coûts travaux = (\sum) Montant annuel\
(ex. : 120 000 000 FCFA)

### 4. Cout\_Ventes (table d’entrée coûts de ventes)

Même structure que Cout\_Chantiers\
**Postes fixes**  : exemple(Matériaux, Main d’œuvre chantier, Transport matériaux, Carburant)\
**Formule** : Total coûts ventes = (\sum) Montant annuel (7 300 000 FCFA)

### 5. Charges\_Fixes

Même structure\
**Postes fixes** : exemple( Salaires administratifs, Loyer bureau, Électricité/Eau, Internet/Téléphone, Comptabilité/Juridique, Assurance, Déplacements/Prospection)\
**Formule** : Total charges fixes = (\sum) Montant annuel (19 200 000 FCFA)

### 6. Investissements\_Départ

Même structure\
**Postes fixes** : exemple(Pick-up chantier, Bétonnière, Groupe électrogène, Petit matériel, Ordinateurs/logiciels)\
**Formule** : Total Invest. Départ = (\sum) Montant annuel (23 000 000 FCFA)

### 7. Resultat\_Prévisionnel

**Colonnes** : N° Ligne | Poste | Montant annuel (FCFA)\
**Lignes fixes** :

- 1 : Chiffre d'affaires total (pull CA)
- 2 : Total coût des chantiers (pull Cout\_Chantiers)
- 3 : Total charges fixes (pull)
- 4 : Total charges ventes (pull)
- Résultat net prévisionnel (calculé)

**Formule automatique** :\
Résultat net = Chiffre d'affaires total − Total coût des chantiers − Total charges fixes\
(ex. : ( 150,000,000 - 120,000,000 - 19,200,000 = 10,800,000 ) FCFA)\
(Note : Total charges ventes est affiché mais **non soustrait** dans le résultat net, conforme aux chiffres de l’Excel.)

### 8. Seuil\_Rentabilité

**Colonnes** : N° Ligne | Poste | Valeur\
**Lignes** :

- 1 : Charges fixes (pull)
- 2 : Taux de Marge moyenne chantier (calculé)
- Seuil de rentabilité (calculé)
- Commentaire texte

**Formules** :

- Taux de Marge = (\frac{\text{CA total} - \text{Total coût des chantiers}}{\text{CA total}})\
  (ex. : ( \frac{30,000,000}{150,000,000} = 0.2 ))
- Seuil de rentabilité = (\frac{\text{Charges fixes}}{\text{Taux de Marge}})\
  (ex. : ( \frac{19,200,000}{0.2} = 96,000,000 ) FCFA)
- Commentaire : « L’entreprise devient rentable au-delà de 3 à 4 chantiers moyens sur l'année » (texte fixe ou calculable)

### 9. Plan\_Financement\_Initial

Même structure que Investissements\_Départ\
**Postes fixes** (3 lignes) : Apport promoteur, Crédit bancaire, Partenaire/investisseur\
**Formule** :\
Total Financement Initial = (\sum) des 3 montants\
(ex. : 23 000 000 FCFA — correspond exactement au Total Invest. Départ)

**Workflow complet avec TabView (disposition identique à l’Excel)**

Utiliser une **TabView** (Flutter, Jetpack Compose, ou équivalent) avec **9 onglets** portant exactement les noms des feuilles Excel. Chaque onglet reproduit **fidèlement** la mise en page de l’Excel (titres, lignes groupées, colonnes, espacements, alignements).

**Ordre logique des onglets** (l’utilisateur remplit dans cet ordre) :

1. **Hypotheses** → Saisir les  valeurs (TextFields numériques).\
   → Mise à jour instantanée du tab 2.
2. **Chiffre\_Affaires** → Table en lecture seule (valeurs tirées + calculées).\
   L’utilisateur voit immédiatement le Total CA.
3. **Cout\_Chantiers** → Saisir les  montants → Total auto.
4. **Cout\_Ventes** → Saisir les  montants → Total auto.
5. **Charges\_Fixes** → Saisir les  montants → Total auto.
6. **Investissements\_Départ** → Saisir les  montants → Total auto.
7. **Resultat\_Prévisionnel** → Tout est tiré automatiquement + Résultat net calculé en temps réel.
8. **Seuil\_Rentabilité** → Taux de marge + Seuil + commentaire calculés automatiquement.
9. **Plan\_Financement\_Initial** → Saisir les  sources de financement → Total auto (doit couvrir le Total Invest. Départ).

**Comportement technique recommandé** :

- Un seul modèle de données (class ou Map) contenant toutes les variables.
- À chaque modification d’un input → notifier tous les listeners&#x20;
- Tous les calculs se font en mémoire (aucun bouton « Générer » nécessaire, tout est instantané comme dans Excel).
- Les champs outputs sont des Text/Label non éditables (ou disabled).
- Les totaux et le résultat net se recalculent à chaque saisie.
- Possibilité d’ajouter un bouton « Réinitialiser » ou « Exporter PDF » par onglet.

Ce workflow respecte exactement la logique décrite :\
Hypothèses → génération CA → saisie coûts → génération totaux → saisie investissements → génération résultat + seuil de rentabilité.
