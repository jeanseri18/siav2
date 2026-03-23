# Système de Suivi des Tâches

## Vue d'ensemble

Le système de suivi des tâches permet de gérer et suivre l'avancement des travaux sur un contrat de manière hiérarchique.

## Structure Hiérarchique

1. **Lot** - Niveau le plus élevé de la hiérarchie
2. **Niveau** - Subdivision d'un lot (ex: Étage, Zone)
3. **Localisation** - Emplacement spécifique dans un niveau
4. **Corps de métier** - Type de travaux (Maçonnerie, Plomberie, etc.)
5. **Tâche** - Tâche spécifique à réaliser

### Relations

- Un **lot** peut avoir plusieurs **niveaux**
- Un **niveau** peut avoir plusieurs **localisations**
- Une **localisation** peut avoir plusieurs **corps de métiers**
- Un **corps de métier** peut avoir plusieurs **tâches**

## Tables de la Base de Données

### Table `lots`
- `id` : Identifiant unique
- `titre` : Nom du lot
- `id_contrat` : Référence au contrat
- `created_at`, `updated_at` : Timestamps

### Table `niveaux`
- `id` : Identifiant unique
- `id_lot` : Référence au lot parent
- `titre_niveau` : Nom du niveau
- `id_contrat` : Référence au contrat
- `created_at`, `updated_at` : Timestamps

### Table `localisations`
- `id` : Identifiant unique
- `titre_localisation` : Nom de la localisation
- `id_niveau` : Référence au niveau parent
- `id_contrat` : Référence au contrat
- `created_at`, `updated_at` : Timestamps

### Table `corps_de_metiers`
- `id` : Identifiant unique
- `id_localisation` : Référence à la localisation parent
- `nom_corpsdemetier` : Nom du corps de métier
- `id_contrat` : Référence au contrat
- `created_at`, `updated_at` : Timestamps

### Table `taches`
- `id` : Identifiant unique
- `id_corps_de_metier` : Référence au corps de métier parent
- `description` : Description détaillée de la tâche
- `date_debut` : Date de début prévue
- `date_fin` : Date de fin prévue
- `nbre_jr_previsionnelle` : Nombre de jours prévisionnels
- `nbre_de_jr_realise` : Nombre de jours réalisés (mis à jour automatiquement)
- `progression` : Pourcentage d'avancement (0-100)
- `statut` : État de la tâche
- `id_contrat` : Référence au contrat
- `image` : Chemin vers l'image de la tâche
- `created_at`, `updated_at` : Timestamps

## Statuts des Tâches

1. **Non débuté** (`non_debute`) - La tâche n'a pas encore commencé
2. **En cours** (`en_cours`) - La tâche est en cours de réalisation
3. **Suspendu** (`suspendu`) - La tâche est temporairement arrêtée
4. **Réceptionné** (`receptionne`) - La tâche est terminée et réceptionnée
5. **Terminé** (`termine`) - La tâche est complètement terminée

## Fonctionnalités Automatiques

### Calcul automatique des jours réalisés
Lorsque la progression d'une tâche atteint **100%**, le nombre de jours réalisés est automatiquement calculé en fonction des dates de début et de fin.

## Utilisation

### Accès au module
1. Sélectionnez un contrat depuis le menu principal
2. Cliquez sur l'onglet **"Suivie des tâches"** dans la barre de navigation du contrat

### Ajouter des éléments

#### 1. Ajouter un Lot
- Cliquez sur le bouton "Ajouter Lot"
- Saisissez le titre du lot (ex: "ENSEMBLE", "TERRASSE", "CUISINE")
- Validez

#### 2. Ajouter un Niveau
- Cliquez sur le bouton "Ajouter Niveau"
- Sélectionnez le lot parent
- Saisissez le titre du niveau (ex: "INSTAL CHANTIER", "CO_Fondation")
- Validez

#### 3. Ajouter une Localisation
- Cliquez sur le bouton "Ajouter Localisation"
- Sélectionnez le lot puis le niveau
- Saisissez le titre de la localisation (ex: "Ensemble", "Fouille")
- Validez

#### 4. Ajouter un Corps de Métier
- Cliquez sur le bouton "Ajouter Corps de Métier"
- Sélectionnez le lot, niveau et localisation
- Saisissez le nom du corps de métier (ex: "Cuisine africaine", "Maçonnerie", "Ferraillage")
- Validez

#### 5. Ajouter une Tâche
- Cliquez sur le bouton "Ajouter Tâche"
- Remplissez tous les champs :
  - Sélectionnez la hiérarchie (lot > niveau > localisation > corps de métier)
  - Description des travaux
  - Date de début et de fin
  - Nombre de jours prévisionnels
  - Progression (%)
  - Statut
  - Image (optionnel)
- Validez

### Modifier une Tâche
- Cliquez sur le bouton "Modifier" (icône crayon) à côté de la tâche
- Modifiez les informations souhaitées
- Validez

### Supprimer une Tâche
- Cliquez sur le bouton "Supprimer" (icône poubelle) à côté de la tâche
- Confirmez la suppression

### Visualiser une Image
- Cliquez sur l'image miniature dans le tableau
- L'image s'affichera en grand dans une fenêtre modale

## Routes API

### Lots
- `POST /taches/lots` - Créer un lot

### Niveaux
- `POST /taches/niveaux` - Créer un niveau
- `GET /taches/niveaux/{lotId}` - Obtenir les niveaux d'un lot

### Localisations
- `POST /taches/localisations` - Créer une localisation
- `GET /taches/localisations/{niveauId}` - Obtenir les localisations d'un niveau

### Corps de Métier
- `POST /taches/corps-de-metiers` - Créer un corps de métier
- `GET /taches/corps-de-metiers/{localisationId}` - Obtenir les corps de métiers d'une localisation

### Tâches
- `GET /taches` - Afficher toutes les tâches
- `POST /taches` - Créer une tâche
- `PUT /taches/{tache}` - Modifier une tâche
- `DELETE /taches/{tache}` - Supprimer une tâche

## Affichage

Le tableau de suivi affiche toutes les informations dans une vue hiérarchique avec :
- Lot
- Niveau
- Localisation
- Corps de métier
- Description des travaux
- Nombre de jours prévisionnels
- Date de début
- Date de fin
- Nombre de jours réalisés
- Progression (barre de progression visuelle)
- Statut (badge coloré)
- Image (miniature cliquable)
- Actions (modifier/supprimer)

## Codes Couleur des Statuts

- **Non débuté** : Gris (#6c757d)
- **En cours** : Jaune/Orange (#ffc107)
- **Suspendu** : Rouge (#dc3545)
- **Réceptionné** : Bleu (#17a2b8)
- **Terminé** : Vert (#28a745)

## Stockage des Images

Les images sont stockées dans le dossier `storage/app/public/taches/` et sont accessibles via le lien symbolique `public/storage/`.

## Modèles Eloquent

- `App\Models\Lot`
- `App\Models\Niveau`
- `App\Models\Localisation`
- `App\Models\CorpsDeMetier`
- `App\Models\Tache`

## Contrôleur

`App\Http\Controllers\TacheController` - Gère toutes les opérations CRUD pour le système de suivi des tâches.

## Vues

- `resources/views/taches/index.blade.php` - Vue principale du suivi des tâches
- `resources/views/planning/index.blade.php` - Vue du planning (en développement)

## Notes Importantes

1. Toutes les données sont liées à un contrat via `id_contrat`
2. Les suppressions en cascade sont activées (supprimer un lot supprime tous ses enfants)
3. Les dates sont au format `Y-m-d` (YYYY-MM-DD)
4. La progression est un nombre décimal entre 0 et 100
5. Les images sont optionnelles mais recommandées pour le suivi visuel
6. Le middleware `auth` est requis pour accéder à toutes les routes

## Améliorations Futures

- Export PDF du suivi des tâches
- Vue Gantt pour le planning
- Notifications automatiques pour les tâches en retard
- Tableau de bord avec statistiques d'avancement
- Import/Export Excel
- Gestion des ressources (personnel, matériel)
- Historique des modifications
