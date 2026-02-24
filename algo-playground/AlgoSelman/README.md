# Package `AlgoSelman` — Équilibrage S2, Mixité et Futurs Apprentis

Ce package contient **3 algorithmes** (2 gloutons + 1 force brute) pour générer des groupes d'étudiants en respectant des contraintes.

> **Responsable : Selman**

---

## 1) Mode de création

**Scénario** : Constitution de groupes S2 en équilibrant la répartition des étudiants en attente d'apprentissage et la mixité.

---

## 2) Contraintes respectées

| Contrainte | Valeur par défaut |
|------------|-------------------|
| Taille des groupes | 17 à 20 étudiants |
| Apprentis par groupe | minimum 1 |
| Covoiturage | 2 à 3 personnes, jamais séparées |

Les contraintes sont configurables via `ContraintesGroupesSelman`.

---

## 3) Critère d'optimisation et score

**Objectif** : Équilibrer la répartition des apprentis ET des filles entre les groupes.

**Score à minimiser** :
```
Score = EcartApprentis + EcartFilles
```

- `EcartApprentis` = max - min du nombre d'apprentis par groupe
- `EcartFilles` = max - min du nombre de filles par groupe

Un score de **0** = répartition parfaitement équilibrée.

---

## 4) Algorithmes implémentés

Tous implémentent l'interface `GroupeAlgoSelman` :

```java
List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesSelman c);
```

### A) GloutonPrioriteSelman (Glouton 1)

**Stratégie** : Distribution Round-Robin par priorité
1. D'abord les packs contenant des apprentis
2. Puis les packs contenant des filles
3. Enfin les autres (dans le groupe le moins rempli)

**Avantage** : Rapide

### B) GloutonScoreSelman (Glouton 2)

**Stratégie** : Best Fit - minimiser le score à chaque étape
- Pour chaque pack, tester tous les groupes
- Choisir celui qui minimise le score global

**Avantage** : Meilleurs résultats en moyenne

### C) ForceBruteSelman (Force Brute)

**Stratégie** : Backtracking
- Explorer toutes les combinaisons possibles
- Garder la meilleure solution (score minimal)

**Avantage** : Solution optimale  
**Limite** : Utilisable seulement sur petites entrées (< 30 étudiants)

---

## 5) Structure des données

### Pack
Groupe indivisible d'étudiants (covoiturage ou solo) :
- `taille` : nombre de membres
- `nbFilles`, `nbApprentis` : statistiques
- `membres` : liste des étudiants

### Utilitaires (GroupingUtilsSelman)
- `construirePacks()` : crée les packs depuis les étudiants
- `choisirNombreDeGroupes()` : calcule k optimal
- `verifierSolution()` : vérifie les contraintes
- `calculerScore()` : calcule le score

---

## 6) Gestion des erreurs

Les algorithmes lancent une exception si :
- Liste d'étudiants vide → `IllegalArgumentException`
- Aucune solution trouvée → `IllegalStateException`

---

## 7) Résultats des tests aléatoires

Tests avec `DemoSelman.java` sur 20 jeux de 100 étudiants :

| Algorithme | Score moyen | Victoires |
|------------|-------------|-----------|
| Glouton Priorité | ~4-5 | ~8/20 |
| Glouton Score | ~3-4 | ~12/20 |

**Conclusion** : Le Glouton Score donne généralement de meilleurs résultats.

---

## 8) Fichiers du package

| Fichier | Description |
|---------|-------------|
| `ContraintesGroupesSelman.java` | Configuration des contraintes |
| `GroupeAlgoSelman.java` | Interface commune |
| `GroupingUtilsSelman.java` | Utilitaires partagés |
| `Pack.java` | Structure indivisible |
| `GloutonPrioriteSelman.java` | Glouton 1 (Round-Robin) |
| `GloutonScoreSelman.java` | Glouton 2 (Best Fit) |
| `ForceBruteSelman.java` | Force Brute (Backtracking) |
| `DemoSelman.java` | Tests et comparaison |

---

## 9) Utilisation

```java
ContraintesGroupesSelman c = ContraintesGroupesSelman.s2();

// Glouton 1
List<Groupe> g1 = new GloutonPrioriteSelman().generer(etudiants, c);

// Glouton 2
List<Groupe> g2 = new GloutonScoreSelman().generer(etudiants, c);

// Force Brute (petites entrées)
List<Groupe> g3 = new ForceBruteSelman().generer(miniPromo, c);

// Vérification
double score = GroupingUtilsSelman.calculerScore(g1);
```

---

**Auteur** : Selman  
**Date** : Janvier 2026
