<p align="center">
  <img src="https://readme-typing-svg.demolab.com?font=Fira+Code&size=24&duration=3000&pause=1000&color=9C27B0&center=true&vCenter=true&width=700&lines=algo-playground;Prototypage+d'algorithmes+de+r%C3%A9partition;Gloutons+%2B+Force+Brute+%2B+Backtracking" alt="Typing SVG" />
</p>

<h1 align="center">🧮 <span style="color:#9C27B0;">algo-playground</span></h1>
<p align="center">Module Java autonome de prototypage et benchmark des stratégies de répartition d'étudiants en groupes sous contraintes.</p>

<p align="center">
  <img src="https://img.shields.io/badge/Lang-Java_17+-007396?style=for-the-badge&logo=java&logoColor=white" alt="Java"/>
  <img src="https://img.shields.io/badge/Mode-CLI_(aucune_GUI)-455A64?style=for-the-badge&logo=windowsterminal&logoColor=white" alt="CLI"/>
  <img src="https://img.shields.io/badge/Deps-Aucune-27AE60?style=for-the-badge" alt="Deps"/>
  <img src="https://img.shields.io/badge/Algo-9_strat%C3%A9gies-9C27B0?style=for-the-badge&logo=thealgorithms&logoColor=white" alt="Algo"/>
</p>

---

### 🎯 À propos du module

🚀 **algo-playground** est l'espace de prototypage algorithmique du projet SAE S3. Chaque membre de l'équipe y a développé **3 stratégies de répartition** ciblant un semestre spécifique, avec ses propres contraintes métier.

📊 **Objectif** : Comparer les performances (score d'équilibrage + temps d'exécution) de 9 algorithmes sur des jeux de données générés aléatoirement, afin de retenir les meilleurs pour l'intégration dans le client desktop.

> Ce module n'a aucune dépendance réseau, aucune base de données et aucune interface graphique. Il s'exécute purement en ligne de commande.

---

### 🛠️ Stack Technique

<div align="center">

**Core**

![Java](https://img.shields.io/badge/Java-JDK_17+-007396?style=for-the-badge&logo=java&logoColor=white)
![Collections](https://img.shields.io/badge/Lib-Java_Collections-E76F00?style=for-the-badge&logo=java&logoColor=white)

**Paradigmes**

![OOP](https://img.shields.io/badge/Architecture-OOP+Interface-1E88E5?style=for-the-badge)
![Backtracking](https://img.shields.io/badge/Algo-Backtracking-9C27B0?style=for-the-badge)
![Greedy](https://img.shields.io/badge/Algo-Glouton-FF6F00?style=for-the-badge)

</div>

---

### 🚀 Modules algorithmiques

Trois packages indépendants, un par membre de l'équipe, chacun ciblant un semestre :

<div align="center">

### 📦 AlgoNesrine — Semestre 1
> *Mixité filles/garçons + covoiturage*

</div>

- **Contraintes S1** : `tailleMin=17`, `tailleMax=20`, `tailleCible=18`, `minFilles=6`, `covoitMin=2`, `covoitMax=3`
- **Spécificités** : Packs de covoiturage indivisibles (2-3 étudiants), seuil minimum de filles par groupe avec fallback parité
- **Score** : `Σ(écart_taille² + écart_filles² + 0.25×écart_cible²)` → **0 = parfait**

| Algorithme | Stratégie | Complexité |
| :--- | :--- | :--- |
| `GloutonEquilibrage` | Place chaque pack dans le groupe le moins rempli qui respecte les contraintes | Rapide |
| `GloutonFillesDAbord` | Phase 1 : packs riches en filles. Phase 2 : remplissage. Phase 3 : correction parité | Rapide |
| `ForceBruteBacktracking` | Backtracking récursif sur les packs, limite 2M nœuds, pruning `faisable()` | Coûteux |

---

<div align="center">

### 📦 AlgoSelman — Semestre 2
> *Équilibrage apprentis + filles + covoiturage*

</div>

- **Contraintes S2** : `tailleMin=17`, `tailleMax=20`, `tailleCible=18`, `minApprentisParGroupe=1`, `covoitMin=2`, `covoitMax=3`
- **Spécificités** : Chaque groupe doit contenir au moins 1 apprenti, packs triés par taille/apprentis/filles
- **Score** : `(maxApprentis - minApprentis) + (maxFilles - minFilles)` → **0 = parfait**

| Algorithme | Stratégie | Complexité |
| :--- | :--- | :--- |
| `GloutonPrioriteSelman` | Round-robin : 1) packs avec apprentis, 2) avec filles, 3) le moins rempli | Rapide |
| `GloutonScoreSelman` | Simule chaque placement, choisit celui qui minimise le score global | Rapide |
| `ForceBruteSelman` | Backtracking sur les packs, limite 100K nœuds | Coûteux |

---

<div align="center">

### 📦 AlgoRached — Semestre 3
> *Regroupement anglais + équilibrage redoublants*

</div>

- **Contraintes S3** : `tailleMin=17`, `tailleMax=20`, `tailleCible=18`, `poidsRedoublants=5.0`
- **Spécificités** : Tous les étudiants avec option anglais dans **un seul groupe**, pondération forte des redoublants
- **Score** : `Σ(écart_taille_cible² + 5.0×écart_redoublants²)` → **0 = parfait**

| Algorithme | Stratégie | Complexité |
| :--- | :--- | :--- |
| `GloutonRedoublantsEquilibre` | Anglais → groupe 1, puis équilibrage redoublants, puis tailles, correction min | Rapide |
| `GloutonScoreLocalS3` | Force anglais → groupe 1, place chaque étudiant (redoublants d'abord) en minimisant le coût local | Rapide |
| `ForceBruteBacktrackingS3` | Force anglais → groupe 1, backtrack le reste avec limite de nœuds | Coûteux |

---

### 🧱 Modèles de données partagés

Quatre packages fournissent les classes métier réutilisées par tous les algorithmes :

| Package | Classes | Champs clés |
| :--- | :--- | :--- |
| `Utilisateur/` | `Etudiant`, `Utilisateur`, `Enseignant`, `Role` | genre, estApprenti, estRedoublant, estAnglophone, idCovoiturage |
| `Scolarite/` | `Groupe`, `Formation`, `Parcours`, `TypeBac`, `MentionBac` | lettre, etudiants, effectif, nbFilles, nbRedoublants, nbApprentis |
| `Pedagogie/` | `Matiere`, `Note` | valeur, commentaire |
| `Sondage/` | `Sondage`, `Reponse` | — |

---

### 🧩 Architecture du module

```
algo-playground/
├── 📁 AlgoNesrine/                 # Algorithmes Semestre 1
│   ├── ContraintesGroupes.java         # Paramètres S1 (factory method s1())
│   ├── GroupeAlgo.java                 # Interface commune generer()
│   ├── GroupingUtils.java              # Score, vérification, packs, mixité
│   ├── Pack.java                       # Pack de covoiturage (id, membres, nbFilles)
│   ├── GloutonEquilibrage.java         # Glouton least-filled
│   ├── GloutonFillesDAbord.java        # Glouton girls-first
│   ├── ForceBruteBacktracking.java     # Backtracking 2M nœuds
│   └── Demo.java                       # 54 étudiants fictifs, benchmark 3 algos
├── 📁 AlgoRached/                  # Algorithmes Semestre 3
│   ├── ContraintesGroupesS3.java       # Paramètres S3 + poidsRedoublants
│   ├── GroupeAlgoS3.java               # Interface commune
│   ├── GroupingUtilsS3.java            # Score, vérification, tri
│   ├── GloutonRedoublantsEquilibre.java
│   ├── GloutonScoreLocalS3.java
│   ├── ForceBruteBacktrackingS3.java
│   └── DemoS3.java                     # 54 étudiants, benchmark
├── 📁 AlgoSelman/                  # Algorithmes Semestre 2
│   ├── ContraintesGroupesSelman.java   # Paramètres S2 + minApprentis
│   ├── GroupeAlgoSelman.java           # Interface commune
│   ├── GroupingUtilsSelman.java        # Score=écart apprentis+filles
│   ├── Pack.java                       # Pack (nbFilles, nbApprentis)
│   ├── GloutonPrioriteSelman.java      # Round-robin par priorité
│   ├── GloutonScoreSelman.java         # Best-fit par score global
│   ├── ForceBruteSelman.java           # Backtracking 100K nœuds
│   └── DemoSelman.java                 # 105 étudiants, benchmark comparatif
├── 📁 Utilisateur/                 # Modèles utilisateur
├── 📁 Scolarite/                   # Modèles scolarité
├── 📁 Pedagogie/                   # Modèles pédagogie
├── 📁 Sondage/                     # Modèles sondage
└── README.md
```

---

### ⚙️ Interface commune

Chaque package expose une interface `GroupeAlgo*` avec la même signature :

```java
public interface GroupeAlgoS3 {
    List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesS3 contraintes);
}
```

Les utilitaires `GroupingUtils*` fournissent les primitives partagées :
- `choisirNombreDeGroupes()` — Calcule le nombre optimal de groupes selon tailleCible
- `construirePacks()` — Regroupe les étudiants en covoiturage en packs indivisibles
- `verifierSolution()` — Valide que la solution respecte toutes les contraintes
- `score()` / `calculerScore()` — Évalue la qualité d'une répartition (0 = parfait)

---

### 💻 Installation & Démarrage

#### 1. Prérequis
* **Java JDK 17** ou supérieur installé (`javac` et `java` dans le `PATH`).

#### 2. Compilation

Depuis la racine `algo-playground/` :

```powershell
Get-ChildItem -Recurse -Filter *.java |
  ForEach-Object { $_.FullName } |
  Set-Content -Encoding Ascii .\sources.txt

javac -encoding UTF-8 @sources.txt
```

#### 3. Exécution des démonstrations

```powershell
java AlgoNesrine.Demo          # S1 : 54 étudiants, 3 algos
java AlgoSelman.DemoSelman     # S2 : 105 étudiants, 40 pour brute force, benchmark N jeux
java AlgoRached.DemoS3         # S3 : 54 étudiants, redoublants + anglophones
```

### Exemple de sortie

Les démos génèrent des jeux de données aléatoires, exécutent les 3 algorithmes et comparent :
- **Score** de chaque solution (`0 = parfait`)
- **Temps d'exécution** (ms)
- **Nombre de victoires** par algorithme sur N itérations
- **Détail des groupes** : effectif, filles, apprentis, redoublants par groupe

---

### 📊 Récapitulatif comparatif

| Module | Semestre | Score | Cible optimale | Jeu de test |
| :--- | :--- | :--- | :--- | :--- |
| AlgoNesrine | S1 | `Σ(taille² + filles² + 0.25×cible²)` | 0 | 54 étudiants, 18 filles |
| AlgoSelman | S2 | `(maxApp-minApp) + (maxFil-minFil)` | 0 | 105 étudiants, 25% filles, 15% apprentis |
| AlgoRached | S3 | `Σ(taille² + 5.0×redoublants²)` | 0 | 54 étudiants, anglais/redoublants |

---

### 👥 Auteurs

| Membre | Package |
| :--- | :--- |
| **CHARLES Nesrine** | AlgoNesrine (S1) |
| **DAHMANI Rached** | AlgoRached (S3) |
| **BOUZLAFA Selman** | AlgoSelman (S2) |

---

### 📄 Licence

<div align="center">

Projet réalisé dans le cadre de la **SAE S3 — Constitution de Groupes** (BUT Informatique).
Usage académique uniquement.

</div>

---

<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:4A148C,100:9C27B0&height=120&section=footer&text=algo-playground%20|%20Algorithmes%20de%20R%C3%A9partition&fontColor=ffffff&fontSize=16&animation=fadeIn" />
</p>
