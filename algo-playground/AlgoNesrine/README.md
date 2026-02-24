# Package `Algorithmique` (S1) — constitution de groupes mixtes

Ce package contient **3 algorithmes** (2 gloutons + 1 force brute) pour générer des groupes d'étudiants de **S1** en respectant des **contraintes** (règles) et un **objectif d’optimisation**.

> Les classes du projet fournies dans le zip étaient un export UML avec surtout des attributs.
> Pour rendre les algorithmes exécutables, quelques **méthodes utilitaires** ont été ajoutées dans
> `Utilisateur.Etudiant`, `Utilisateur.Utilisateur` et `Scolarite.Groupe`.

---

## 1) Données d'entrée / modèles utilisés

### `Utilisateur.Etudiant`
Les algorithmes consomment une liste de `Etudiant` et utilisent principalement :
- `char getGenre()` (ex: 'F' / 'M')
- `boolean isFille()` (helper)
- `int getIdCovoiturage()` (0 = pas de covoiturage, sinon identifiant commun)

### `Scolarite.Groupe`
Un `Groupe` contient une liste d’étudiants. Les algorithmes utilisent :
- `void ajouterEtudiant(Etudiant e)` / `void ajouterEtudiants(List<Etudiant>)`
- `int getEffectif()`
- `int getNbFilles()`, `int getNbGarcons()`

---

## 2) Contraintes S1 gérées

Les contraintes sont regroupées dans `ContraintesGroupes` :

- **Taille des groupes** : `tailleMin..tailleMax` (par défaut **17..20**)
- **Mixité** :
  - règle principale : `minFilles = 6` filles / groupe
  - alternative : `minFillesAlternative = 4` **et** `fillesPairSiAlternative = true`
- **Covoiturage** : les étudiants ayant le même `idCovoiturage` doivent rester ensemble
  - taille de pack autorisée : `covoitMin..covoitMax` (par défaut **2..3**)

La méthode `ContraintesGroupes.s1()` fournit une configuration standard S1.

---

## 3) Structure interne : la notion de `Pack`

Pour gérer le covoiturage, on regroupe les étudiants en **packs indivisibles** (`Pack`) :

- un pack = { 1 étudiant solo } OU { un covoiturage de 2 à 3 }
- chaque pack mémorise :
  - `taille`
  - `nbFilles`
  - `List<Etudiant> membres`

**Important** : un pack covoiturage ne doit **jamais** être séparé.

La construction/validation des packs est faite par :
- `GroupingUtils.construirePacksCovoiturage(...)`

---

## 4) Outils partagés (`GroupingUtils`)

`GroupingUtils` centralise des fonctions communes :
- `choisirNombreDeGroupes(N, contraintes)` : choisit un nombre de groupes `k` tel que
  `k*tailleMin <= N <= k*tailleMax` et dont la moyenne est la plus proche de `tailleCible`.
- `initialiserGroupes(k)` : crée `k` groupes vides.
- `verifierSolution(groupes, contraintes)` : vérifie tailles + mixité.
- `score(groupes, contraintes)` : score d'optimisation (plus petit = meilleur) basé sur :
  - écart à la taille cible
  - écart de répartition des filles (équilibrage)

Toutes les erreurs "pas de solution" / "données invalides" utilisent :
- `GroupingUtils.GroupingException`

---

## 5) Algorithmes disponibles

Tous les algorithmes implémentent l'interface :

```java
public interface GroupeAlgo {
    List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupes c);
}
```

### A) `GloutonEquilibrage`
**Idée** : placer chaque pack (covoit/solo), du plus gros au plus petit, dans le groupe
le **moins rempli** qui reste **faisable** pour atteindre la mixité.

- approche proche d’un bin-packing « best-fit »
- rapide, robuste, mais pas garanti optimal

Complexité : ~ `O(P * k)` où `P` = nombre de packs.

### B) `GloutonFillesDAbord`
**Idée** :
1) pousser d'abord des packs riches en filles pour satisfaire `minFilles`,
2) puis remplir en équilibrant les tailles,
3) correction légère si on est en alternative « 4 filles + parité ».

Complexité : ~ `O(P * k)`.

### C) `ForceBruteBacktracking`
**Idée** : exploration exhaustive (backtracking) des placements de packs dans les groupes.

- garantit la meilleure solution selon `score(...)` **si** on laisse terminer la recherche
- utilise des **élagages (pruning)** de faisabilité
- possède une limite `maxNoeuds` pour éviter l’explosion combinatoire

Complexité : exponentielle dans le pire cas.

---

## 6) Exemple d'utilisation

```java
ContraintesGroupes c = ContraintesGroupes.s1();

GroupeAlgo algo = new GloutonEquilibrage();
List<Groupe> groupes = algo.generer(etudiants, c);

GroupingUtils.verifierSolution(groupes, c);
System.out.println("Score: " + GroupingUtils.score(groupes, c));
```

Une classe `Demo` est fournie pour illustrer un appel simple en console.

---

## 7) Limites / hypothèses

- Si la contrainte « 6 filles / groupe » est impossible au vu du total de filles,
  les algorithmes basculent vers l’alternative (si activée) ou lèvent une exception.
- Les covoiturages doivent former des packs de taille 2..3 exactement (sinon exception).
