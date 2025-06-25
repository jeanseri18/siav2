# Intégration DQE - Contrat : Mise à jour automatique du montant

## Vue d'ensemble

Cette fonctionnalité permet la mise à jour automatique du montant d'un contrat lorsqu'un DQE (Détail Quantitatif Estimatif) est validé.

## Fonctionnement

### 1. Processus de validation du DQE

Lorsqu'un DQE passe du statut "brouillon" ou "archivé" au statut "validé" :

1. Le système vérifie les permissions de l'utilisateur
2. Le DQE est mis à jour avec le nouveau statut
3. **Automatiquement**, le montant du contrat associé est mis à jour avec le montant TTC du DQE
4. Une notification confirme la mise à jour

### 2. Règles de mise à jour

- **Déclencheur** : Changement de statut vers "validé"
- **Source** : Montant TTC du DQE (`montant_total_ttc`)
- **Cible** : Champ `montant` du contrat associé
- **Condition** : Le DQE doit avoir un montant TTC > 0

### 3. Permissions requises

Seuls les utilisateurs avec les rôles suivants peuvent valider un DQE :
- `chef_projet`
- `conducteur_travaux`
- `admin`
- `dg`

## Implémentation technique

### Fichiers modifiés

1. **`app/Http/Controllers/DQEController.php`**
   - Méthode `update()` : Ajout de la logique de mise à jour du contrat
   - Méthode `updateContratMontant()` : Gestion de la mise à jour

2. **`app/Models/Contrat.php`**
   - Méthode `updateMontantFromDQE()` : Mise à jour basée sur le dernier DQE validé
   - Méthode `getLastValidatedDQE()` : Récupération du dernier DQE validé

3. **`resources/views/contrats/create.blade.php`** et **`edit.blade.php`**
   - Champ montant rendu optionnel
   - Indication que le montant sera mis à jour après validation du DQE

### Code clé

```php
// Dans DQEController::update()
if ($request->statut === 'validé' && $ancienStatut !== 'validé') {
    $contratUpdated = $this->updateContratMontant($dqe);
}

// Dans Contrat::updateMontantFromDQE()
public function updateMontantFromDQE()
{
    $lastDqe = $this->getLastValidatedDQE();
    
    if ($lastDqe && $lastDqe->montant_total_ttc > 0) {
        $this->update(['montant' => $lastDqe->montant_total_ttc]);
        return true;
    }
    
    return false;
}
```

## Workflow utilisateur

1. **Création du contrat** : L'utilisateur peut créer un contrat sans spécifier le montant
2. **Création du DQE** : Un DQE est créé et associé au contrat
3. **Saisie des lignes DQE** : Les détails quantitatifs sont saisis
4. **Calcul automatique** : Le montant total TTC du DQE est calculé automatiquement
5. **Validation du DQE** : Un utilisateur autorisé valide le DQE
6. **Mise à jour automatique** : Le montant du contrat est automatiquement mis à jour

## Notifications et logs

- **Interface utilisateur** : Message de succès indiquant la mise à jour du montant
- **Logs système** : Enregistrement de la mise à jour avec détails

## Tests

Des tests automatisés sont disponibles dans :
- `tests/Feature/DQEContratMontantUpdateTest.php`

Tests couverts :
- Mise à jour du montant lors de la validation
- Pas de mise à jour si le statut ne change pas
- Vérification des permissions

## Avantages

1. **Cohérence** : Le montant du contrat reflète toujours le dernier DQE validé
2. **Automatisation** : Réduction des erreurs manuelles
3. **Traçabilité** : Logs détaillés des modifications
4. **Flexibilité** : Possibilité de créer des contrats sans montant initial

## Considérations

- Si plusieurs DQE sont validés, seul le dernier (par date de mise à jour) sera pris en compte
- La TVA est automatiquement incluse (18% par défaut)
- Les utilisateurs peuvent toujours modifier manuellement le montant du contrat si nécessaire