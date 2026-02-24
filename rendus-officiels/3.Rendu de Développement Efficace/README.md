# Algorithmes de Constitution de Groupes

Ce dossier contient les implémentations des algorithmes de constitution automatique de groupes d'étudiants.

## Structure

```
partie algo/
├── AlgoNesrine/     # Algorithmes pour semestres 1 et 2
├── AlgoRached/      # Algorithmes pour semestre 3
├── Pedagogie/       # Classes métier (Matiere, Note)
├── Scolarite/       # Classes métier (Formation, Groupe, Parcours, etc.)
├── Sondage/         # Classes métier (Sondage, Reponse)
└── Utilisateur/     # Classes métier (Etudiant, Enseignant, etc.)
```

## Algorithmes de Nesrine (S1-S2)

### 1. ForceBruteBacktracking
Algorithme exhaustif qui teste toutes les combinaisons possibles pour trouver la meilleure répartition.
- ✅ Trouve la solution optimale
- ❌ Temps d'exécution exponentiel (lent pour groupes nombreux)

### 2. GloutonEquilibrage
Algorithme glouton qui répartit les étudiants en équilibrant les groupes.
- Objectif : groupes de tailles similaires
- Rapide mais pas toujours optimal

### 3. GloutonFillesDAbord
Algorithme glouton avec priorité aux étudiantes.
- Distribue d'abord les filles dans les groupes
- Puis complète avec les garçons
- Vise une meilleure mixité

## Algorithmes de Rached (S3)

### 1. ForceBruteBacktrackingS3
Version adaptée pour le semestre 3 avec contraintes spécifiques.
- Prend en compte les redoublants
- Gère les contraintes de parcours
- Optimise selon plusieurs critères simultanés

### 2. GloutonScoreLocalS3
Algorithme glouton basé sur un score local pour chaque étudiant.
- Calcule un score de compatibilité pour chaque groupe
- Place l'étudiant dans le groupe avec le meilleur score
- Rapide et efficace pour grandes promotions

### 3. GloutonRedoublantsEquilibre
Algorithme spécialisé pour gérer les redoublants.
- Distribue équitablement les redoublants entre groupes
- Évite la concentration de redoublants dans un même groupe
- Équilibre ensuite les primo-arrivants

### 4. GloutonMultiCriteresSX
Algorithme multi-critères avancé.
- Optimise simultanément :
  * Équilibre des effectifs
  * Mixité homme/femme
  * Distribution des redoublants
  * Répartition par parcours
- Solution de compromis équilibrée

## Contraintes gérées

### ContraintesGroupes (S1-S2)
- Taille minimale et maximale des groupes
- Mixité homme/femme
- Répartition équitable

### ContraintesGroupesS3 (S3)
- Tout ce qui précède, plus :
- Gestion des redoublants
- Contraintes de parcours
- Binômes imposés
- Exclusions entre étudiants

## Utilisation

Les algorithmes sont intégrés dans l'application Java Desktop via le package `algo/` :

```java
// Exemple d'utilisation
List<Etudiant> etudiants = promotion.getEtudiants();
List<Groupe> groupes = new ArrayList<>();

// Utiliser un algorithme
GloutonScoreLocalS3 algo = new GloutonScoreLocalS3();
algo.repartir(etudiants, groupes, contraintes);
```

## Classes utilitaires

- **GroupeAlgo / GroupeAlgoS3** : Représentation d'un groupe pour les algorithmes
- **GroupingUtils / GroupingUtilsS3** : Méthodes utilitaires (calcul scores, vérification contraintes)
- **Pack** : Structure de données pour optimisations

## Démonstration

- **Demo.java** : Exemples d'utilisation des algorithmes S1-S2
- **DemoS3.java** : Exemples d'utilisation des algorithmes S3

## Choix d'un algorithme

| Critère | Recommandation |
|---------|----------------|
| Petit groupe (<20) | Force brute (solution optimale) |
| Grand groupe (>50) | Glouton Score Local (rapide) |
| Redoublants nombreux | Glouton Redoublants Équilibre |
| Contraintes complexes | Multi-Critères |
| Mixité prioritaire | Glouton Filles d'Abord |

## Performance

- **Force Brute** : O(n!) - Exponentiel
- **Algorithmes Gloutons** : O(n²) - Quadratique
- **Multi-Critères** : O(n² log n) - Quasi-linéaire

Pour une promotion de 100 étudiants :
- Force brute : plusieurs heures
- Glouton : quelques secondes
