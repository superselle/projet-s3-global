# Package `Algorithmique2` (S3) — constitution de groupes avec options anglais + redoublants

Ce package reprend la même logique que `Algorithmique`, mais pour les **groupes de S3**.

## Règles (contraintes)
- **Taille** : chaque groupe doit contenir **entre 17 et 20** étudiants.
- **Option anglais** : tous les étudiants ayant l’option anglais doivent être dans **un seul et même groupe**
  (ce groupe peut contenir des étudiants non-anglais).

## Optimisation
- Répartir les **redoublants** le plus équitablement possible entre les groupes.

> Dans le modèle fourni, il n’existe pas de champ explicite `optionAnglais`.
> On utilise donc le booléen existant `Etudiant.estAnglophone` via l’alias :
> `Etudiant.aOptionAnglais()` / `Etudiant.setOptionAnglais(...)`.

## Fichiers principaux

- `ContraintesGroupesS3` : paramètres (tailleMin/tailleMax/tailleCible, poids redoublants)
- `GroupeAlgoS3` : interface commune
- `GroupingUtilsS3` : utilitaires (choix du nombre de groupes, vérifs, score)
- 2 gloutons :
  - `GloutonRedoublantsEquilibre` : anglais d'abord, puis redoublants équilibrés, puis tailles
  - `GloutonScoreLocalS3` : placement étudiant par étudiant avec coût local (taille + redoublants)
- 1 force brute :
  - `ForceBruteBacktrackingS3` : backtracking exhaustif (avec limite `maxNoeuds`)
- `DemoS3` : exemple console

## Utilisation

```java
ContraintesGroupesS3 c = ContraintesGroupesS3.s3();

GroupeAlgoS3 algo1 = new GloutonRedoublantsEquilibre();
List<Groupe> g1 = algo1.generer(etudiants, c);

GroupeAlgoS3 algo2 = new GloutonScoreLocalS3();
List<Groupe> g2 = algo2.generer(etudiants, c);

GroupeAlgoS3 bf = new ForceBruteBacktrackingS3(1_000_000);
List<Groupe> g3 = bf.generer(etudiants, c);
```

`GroupingUtilsS3.verifierSolution(...)` est appelé automatiquement dans les algorithmes.
