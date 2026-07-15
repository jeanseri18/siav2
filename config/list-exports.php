<?php

use App\Models\Banque;
use App\Models\BU;
use App\Models\Categorie;
use App\Models\Commune;
use App\Models\ConfigGlobal;
use App\Models\CorpMetier;
use App\Models\ModePaiement;
use App\Models\Monnaie;
use App\Models\Pays;
use App\Models\Quartier;
use App\Models\Reference;
use App\Models\RegimeImposition;
use App\Models\Secteur;
use App\Models\SecteurActivite;
use App\Models\SousCategorie;
use App\Models\Tache;
use App\Models\TypeTravaux;
use App\Models\UniteMesure;
use App\Models\User;
use App\Models\Ville;

return [
    'categories' => [
        'title' => 'Liste des catégories',
        'model' => Categorie::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'sous_categories' => [
        'title' => 'Liste des sous-catégories',
        'model' => SousCategorie::class,
        'with' => ['categorie'],
        'order' => ['nom', 'asc'],
        'headers' => ['Nom', 'Catégorie'],
        'columns' => ['nom', 'categorie.nom'],
    ],
    'unite_mesures' => [
        'title' => 'Liste des unités de mesure',
        'model' => UniteMesure::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Réf.', 'Nom'],
        'columns' => ['ref', 'nom'],
    ],
    'corpmetiers' => [
        'title' => 'Liste des corps de métier',
        'model' => CorpMetier::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'type_travaux' => [
        'title' => 'Liste des types de travaux',
        'model' => TypeTravaux::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'monnaies' => [
        'title' => 'Liste des monnaies',
        'model' => Monnaie::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Sigle', 'Nom'],
        'columns' => ['sigle', 'nom'],
    ],
    'banques' => [
        'title' => 'Liste des banques',
        'model' => Banque::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom', 'Code banque'],
        'columns' => ['nom', 'code_banque'],
    ],
    'modes_de_paiement' => [
        'title' => 'Liste des modes de paiement',
        'model' => ModePaiement::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'regime_impositions' => [
        'title' => 'Liste des régimes d\'imposition',
        'model' => RegimeImposition::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'secteur_activites' => [
        'title' => 'Liste des secteurs d\'activité',
        'model' => SecteurActivite::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'pays' => [
        'title' => 'Liste des pays',
        'model' => Pays::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'villes' => [
        'title' => 'Liste des villes',
        'model' => Ville::class,
        'with' => ['pays'],
        'order' => ['nom', 'asc'],
        'headers' => ['Nom', 'Pays'],
        'columns' => ['nom', 'pays.nom'],
    ],
    'communes' => [
        'title' => 'Liste des communes',
        'model' => Commune::class,
        'with' => ['ville'],
        'order' => ['nom', 'asc'],
        'headers' => ['Nom', 'Ville'],
        'columns' => ['nom', 'ville.nom'],
    ],
    'quartiers' => [
        'title' => 'Liste des quartiers',
        'model' => Quartier::class,
        'with' => ['commune'],
        'order' => ['nom', 'asc'],
        'headers' => ['Nom', 'Commune'],
        'columns' => ['nom', 'commune.nom'],
    ],
    'secteurs' => [
        'title' => 'Liste des secteurs',
        'model' => Secteur::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom'],
        'columns' => ['nom'],
    ],
    'references' => [
        'title' => 'Liste des références',
        'model' => Reference::class,
        'order' => ['ref', 'asc'],
        'headers' => ['Réf.', 'Nom'],
        'columns' => ['ref', 'nom'],
    ],
    'taches' => [
        'title' => 'Liste des tâches',
        'model' => Tache::class,
        'order' => ['description', 'asc'],
        'headers' => ['Description', 'Date début', 'Date fin'],
        'columns' => ['description', 'date_debut', 'date_fin'],
    ],
    'users' => [
        'title' => 'Liste des utilisateurs',
        'model' => User::class,
        'order' => ['nom', 'asc'],
        'headers' => ['Nom', 'Email', 'Rôle'],
        'columns' => ['nom', 'email', 'role'],
    ],
    'bu' => [
        'title' => 'Liste des Business Units',
        'model' => BU::class,
        'with' => ['secteur'],
        'order' => ['nom', 'asc'],
        'headers' => ['Nom', 'Secteur', 'Statut'],
        'columns' => ['nom', 'secteur.nom', 'statut'],
    ],
    'config_global' => [
        'title' => 'Liste des configurations globales',
        'model' => ConfigGlobal::class,
        'with' => ['businessUnit'],
        'order' => ['nom_entreprise', 'asc'],
        'headers' => ['Entreprise', 'BU', 'Email'],
        'columns' => ['nom_entreprise', 'businessUnit.nom', 'email'],
    ],
];
