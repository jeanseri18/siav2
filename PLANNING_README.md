# Système de Planning - Diagramme de Gantt

## Vue d'ensemble

Le système de planning permet de visualiser et gérer le calendrier des travaux sous forme de diagramme de Gantt, similaire à Microsoft Project ou Excel.

## Structure de la base de données

### Table `plannings`
- `id` : Identifiant unique
- `id_contrat` : Référence au contrat
- `id_souscategorie` : Référence à la sous-catégorie DQE (optionnel)
- `nom_tache_planning` : Nom de la tâche
- `date_debut` : Date de début de la tâche
- `date_fin` : Date de fin de la tâche
- `statut` : État de la tâche
- `created_at`, `updated_at` : Timestamps

## Statuts des tâches

1. **Non démarré** (`non_demarre`) - Couleur : Gris (#6c757d)
2. **En cours** (`en_cours`) - Couleur : Bleu clair (#0dcaf0)
3. **En retard** (`retard`) - Couleur : Rouge (#dc3545)
4. **Terminé** (`termine`) - Couleur : Vert (#198754)

## Fonctionnalités

### 1. Affichage Gantt

- **À gauche** : Liste des tâches organisées par catégories/sous-catégories DQE
- **À droite** : Calendrier avec :
  - En-tête : Mois
  - Sous-en-tête : Semaines (S1, S2, S3, etc.) et jours
  - Week-ends colorés en rose
  - Barres colorées représentant les tâches

### 2. Hiérarchie

Le planning affiche automatiquement :
- **Catégories DQE** (en bleu foncé)
- **Sous-catégories DQE** (en gris)
- **Tâches de planning** (en blanc avec barres colorées)

Seules les catégories des DQE **validés** ou **approuvés** sont affichées.

### 3. Ajout de tâches

Deux méthodes pour ajouter une tâche :

#### Méthode 1 : Bouton global
1. Cliquer sur "Ajouter une tâche" en haut
2. Remplir :
   - Nom de la tâche
   - Sous-catégorie (optionnel)
   - Date début
   - Date fin
   - Statut
3. Cliquer sur "Enregistrer"

#### Méthode 2 : Depuis une sous-catégorie
1. Cliquer sur "Ajouter" dans une ligne de sous-catégorie
2. La sous-catégorie est pré-sélectionnée
3. Remplir les informations
4. Enregistrer

### 4. Visualisation

Les tâches apparaissent sous forme de **barres horizontales** :
- **Longueur** : Correspond à la durée (date début → date fin)
- **Couleur** : Indique le statut
- **Position** : Placée sur les jours correspondants du calendrier

### 5. Calculs automatiques

- **Durée** : Calculée automatiquement (date fin - date début + 1 jour)
- **Période d'affichage** : Du début du mois actuel à 3 mois

## Utilisation

### Accès au module
1. Sélectionnez un contrat depuis le menu principal
2. Cliquez sur l'onglet **"Planning"** dans la barre de navigation du contrat

### Prérequis
- Le contrat doit avoir au moins un **DQE validé ou approuvé**
- Sans DQE validé, un message s'affiche : "Aucune catégorie DQE validée"

### Exemple de flux de travail

```
1. Valider un DQE pour le contrat
2. Accéder au Planning
3. Les catégories/sous-catégories DQE s'affichent automatiquement
4. Ajouter des tâches :
   - "Installation de chantier" : 23/12/2025 → 25/12/2025
   - "Plan d'exécution" : 24/12/2025 → 26/12/2025
5. Les barres apparaissent dans le calendrier
6. Modifier le statut si nécessaire (future fonctionnalité)
```

## Routes API

### Plannings
- `GET /planning` - Afficher le planning
- `POST /planning` - Créer une tâche
- `PUT /planning/{planning}` - Modifier une tâche
- `DELETE /planning/{planning}` - Supprimer une tâche
- `POST /planning/{planning}/update-field` - Mise à jour rapide d'un champ

## Modèles Eloquent

- `App\Models\Planning`
- `App\Models\CategorieRubrique`
- `App\Models\SousCategorieRubrique`
- `App\Models\DQE`

## Contrôleur

`App\Http\Controllers\PlanningController` - Gère toutes les opérations du planning.

## Vues

- `resources/views/planning/index.blade.php` - Vue principale du planning Gantt

## Affichage du calendrier

### Structure
```
┌─────────────┬────────┬────────┬───────────────────────────────────────┐
│ TÂCHE       │ DÉBUT  │ FIN    │     DÉCEMBRE          │    JANVIER    │
├─────────────┼────────┼────────┼───────────────────────┼───────────────┤
│             │        │        │ S1  │ S2  │ S3  │ S4  │ S1  │ S2  │..│
│             │        │        │ 1 2│3 4│5 6│7 8│9 10│11 12│13 14│... │
├─────────────┼────────┼────────┼────┼────┼────┼────┼────┼─────┼─────┤
│ CATÉGORIE 1                                                          │
├─────────────┼────────┼────────┼────┼────┼────┼────┼────┼─────┼─────┤
│  Sous-cat 1 │ [Bouton Ajouter]                                      │
├─────────────┼────────┼────────┼────┼────┼────┼────┼────┼─────┼─────┤
│    Tâche 1  │23/12/25│25/12/25│████████████│    │    │    │        │
└─────────────┴────────┴────────┴────┴────┴────┴────┴────┴─────┴─────┘
```

## Légende des couleurs

- 🔵 **Bleu foncé** : En-tête catégorie DQE
- ⬜ **Gris** : Sous-catégorie DQE
- 🟦 **Bleu** : Mois (en-tête)
- ⬛ **Gris foncé** : Colonnes DÉBUT/FIN
- 🟥 **Rose** : Week-ends
- 🟩 **Vert** : Tâche terminée
- 🟦 **Bleu clair** : Tâche en cours
- 🟥 **Rouge** : Tâche en retard
- ⬜ **Gris** : Tâche non démarrée

## Améliorations futures

- ✅ Export PDF du planning
- ✅ Export Excel
- ✅ Drag & Drop des barres pour modifier les dates
- ✅ Édition en ligne des tâches
- ✅ Dépendances entre tâches
- ✅ Affectation de ressources
- ✅ Gestion des jalons (milestones)
- ✅ Vue mensuelle / hebdomadaire
- ✅ Filtres par statut
- ✅ Zoom calendrier

## Notes techniques

### Calcul des semaines
Le système génère automatiquement les semaines du début du mois actuel jusqu'à 3 mois dans le futur.

### Rowspan des barres
Les barres de tâches s'étendent sur plusieurs cellules selon leur durée. La première cellule contient la barre avec arrondi, les cellules suivantes prolongent la barre.

### Performance
- Optimisé pour afficher jusqu'à 100 tâches simultanément
- Scroll horizontal pour les longues périodes
- Sticky columns pour les en-têtes

## Dépendances

- Laravel 10+
- Bootstrap 5
- Font Awesome
- Carbon (gestion des dates)

## Middleware

Toutes les routes du planning sont protégées par le middleware `auth`.
