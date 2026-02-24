<p align="center">
  <img src="https://readme-typing-svg.demolab.com?font=Fira+Code&size=24&duration=3000&pause=1000&color=3498DB&center=true&vCenter=true&width=700&lines=SAE+S3+%E2%80%94+Constitution+de+Groupes;R%C3%A9partition+automatique+%26+manuelle;Backend+PHP+%2B+Client+Java+%2B+Algorithmes" alt="Typing SVG" />
</p>

<h1 align="center">🎓 <span style="color:#3498DB;">Projet-S3-Global</span></h1>
<p align="center">Système complet de constitution de groupes TD/TP pour le département Informatique de l'IUT d'Orsay.</p>

<p align="center">
  <img src="https://img.shields.io/badge/Status-Livré-27AE60?style=for-the-badge&logo=checkmarx&logoColor=white" alt="Status"/>
  <img src="https://img.shields.io/badge/SAE-S3_2024--2025-3498DB?style=for-the-badge&logo=bookstack&logoColor=white" alt="SAE"/>
  <img src="https://img.shields.io/badge/Backend-PHP_8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/Client-Java_17+Swing-007396?style=for-the-badge&logo=java&logoColor=white" alt="Java"/>
  <img src="https://img.shields.io/badge/BDD-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
</p>

---

### 🎯 À propos du projet

🚀 **Projet-S3-Global** est le livrable de la SAE S3 « Constitution de groupes ». Il permet aux responsables de filière et de formation de **constituer automatiquement ou manuellement des groupes d'étudiants** (TD/TP) en respectant un ensemble de contraintes pédagogiques configurables.

📊 **Pourquoi ce projet ?**
* 🎓 **Contexte** : Chaque semestre, les responsables de filière doivent répartir ~100 étudiants en groupes équilibrés selon des critères multiples.
* ⚙️ **Contraintes gérées** : Taille min/max/cible, mixité filles/garçons, covoiturage (packs indivisibles), équilibrage des apprentis, regroupement des anglophones, répartition des redoublants.
* 🔄 **Double mode** : Répartition automatique (algorithmes gloutons / force brute) ou manuelle (glisser-déposer).
* 🌐 **Interface web** : Consultation des groupes, sondages, choix de binôme, import/export CSV, publication des résultats.

> "De l'inscription à la publication des groupes, tout est automatisé."

---

### 🛠️ Stack Technique

<div align="center">

**💡 Backend & Données**

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![REST](https://img.shields.io/badge/API-REST_JSON-FF6F00?style=for-the-badge&logo=json&logoColor=white)

**🖥️ Client Desktop**

![Java](https://img.shields.io/badge/Java-JDK_17+-007396?style=for-the-badge&logo=java&logoColor=white)
![Swing](https://img.shields.io/badge/GUI-Swing-E76F00?style=for-the-badge&logo=java&logoColor=white)
![Gson](https://img.shields.io/badge/JSON-Gson_2.10.1-2E7D32?style=for-the-badge&logo=google&logoColor=white)

**🧮 Algorithmes**

![Algo](https://img.shields.io/badge/Algo-Glouton+Backtracking-9C27B0?style=for-the-badge&logo=thealgorithms&logoColor=white)
![Java](https://img.shields.io/badge/Java_17-Pur_(aucune_dep.)-007396?style=for-the-badge&logo=java&logoColor=white)

</div>

---

### 🏗️ Architecture du Système

Le projet est composé de **trois sous-systèmes indépendants** qui collaborent :

<div align="center">

```
 ┌──────────────────────┐        HTTP/JSON          ┌──────────────────────┐
 │                      │  ◄─────────────────────►  │                      │
 │  java-desktop-app    │    (10 endpoints REST)    │      web-api         │
 │  Client lourd Java   │                           │  Backend PHP + MVC   │
 │  Swing + Gson        │                           │  Interface web       │
 │  Algorithmes S3      │                           │  Sessions + Rôles    │
 │                      │                           │                      │
 └──────────────────────┘                           └──────────────────────┘
                                                              ▲
                                                              │ SQL (PDO)
                                                              ▼
                                                    ┌──────────────────┐
                                                    │   Base MySQL     │
                                                    │   17 tables      │
                                                    │   4 vues + 5     │
                                                    │   triggers       │
                                                    └──────────────────┘

┌──────────────────────┐
                               │  algo-playground     │← Module autonome (hors réseau)
                               │  Prototypage Java    │     3 packages × 3 algorithmes
                            │  S1 + S2 + S3        │     Benchmark & comparaison
└──────────────────────┘
```

</div>

### Flux de données

1. **`web-api`** expose une API REST JSON (10 endpoints) et une interface web MVC complète. Il gère l'authentification multi-rôles, les promotions, étudiants, groupes, sondages, notes et affectations. Toutes les données persistent dans une base MySQL (17 tables).
2. **`java-desktop-app`** est le client lourd Swing utilisé par le responsable de filière. Il consomme l'API REST de `web-api` pour lire les promotions/étudiants et sauvegarder les affectations. Il embarque les algorithmes S3 + un glouton multi-critères générique (S3/S4/S5+) pour la répartition automatique.
3. **`algo-playground`** est un espace de prototypage algorithmique pur Java, sans dépendance réseau ni BDD. Chaque membre de l'équipe y a implémenté 3 stratégies pour un semestre donné (S1, S2, S3). Les algorithmes S3 validés ici sont intégrés dans `java-desktop-app/src/algo/`.

---

### 📂 Navigation dans le projet

<div align="center">

| Dossier | Description | Documentation |
| :--- | :--- | :--- |
| [`algo-playground/`](algo-playground/) | Algorithmes de répartition — 3 packages × 3 stratégies | [📖 README](algo-playground/README.md) |
| [`java-desktop-app/`](java-desktop-app/) | Application desktop Java Swing (client lourd) | [📖 README](java-desktop-app/README.md) |
| [`web-api/`](web-api/) | Backend PHP MVC + API REST + interface web | [📖 README](web-api/README.md) |
| [`rendus-officiels/`](rendus-officiels/) | Livrables académiques | Voir ci-dessous |

</div>

### 📋 Rendus officiels

| # | Livrable | Contenu | Dossier |
| :--- | :--- | :--- | :--- |
| 1 | Analyse | Rapport d'analyse (PDF) | [`1.Rendu analyse/`](rendus-officiels/1.Rendu%20analyse/) |
| 2 | Base de données | DDL Oracle, procédures/triggers, insertions | [`2.Rendu BD/`](rendus-officiels/2.Rendu%20BD/) |
| 3 | Développement efficace | Documentation des algorithmes | [`3.Rendu de Développement Efficace/`](rendus-officiels/3.Rendu%20de%20Développement%20Efficace/) |
| 4 | Qualité du développement | Rapport COO + diagrammes UML (Visual Paradigm) | [`4.Rendu Qualité du Developpemement (par groupe)/`](rendus-officiels/4.Rendu%20Qualité%20du%20Developpemement%20(par%20groupe)/) |
| 5 | Développement web | Rapport web (PDF) | [`5.Rendu Développement Web/`](rendus-officiels/5.Rendu%20Développement%20Web/) |

---

### ⚡ Démarrage rapide

#### 1. Backend (PHP + MySQL)
```bash
# 1. Créer la base MySQL et importer les scripts SQL
#    info-bd/creation-insert-mysql.txt  →  structure + données
#    info-bd/vue-proc-fonction-mysql.txt  →  vues, triggers, procédures

# 2. Configurer config/connexion.php avec vos accès MySQL

# 3. Hasher les mots de passe des comptes de démonstration
cd web-api
php scripts/seed_utilisateurs_s4.php

# 4. Lancer le serveur
php -S localhost:8000
```

#### 2. Client desktop (Java Swing)
```powershell
cd java-desktop-app

# Configurer src/utils/Config.java → API_URL, HTTP_AUTH_USER, HTTP_AUTH_PASSWORD

# Compiler
javac -encoding UTF-8 -cp "lib/gson-2.10.1.jar" -d bin (Get-ChildItem -Path src -Recurse -Filter *.java | ForEach-Object { $_.FullName })

# Lancer
java -cp "bin;lib/gson-2.10.1.jar" Main
```

#### 3. Algorithmes (standalone)
```powershell
cd algo-playground
Get-ChildItem -Recurse -Filter *.java | ForEach-Object { $_.FullName } | Set-Content -Encoding Ascii .\sources.txt
javac -encoding UTF-8 @sources.txt

# Exécuter les démos
java AlgoNesrine.Demo        # S1
java AlgoSelman.DemoSelman   # S2
java AlgoRached.DemoS3       # S3
```

#### 🔑 Comptes de démonstration

| Rôle | Login | Mot de passe |
| :--- | :--- | :--- |
| Responsable Formation | `respform` | `respform` |
| Responsable Filière | `respfil` | `respfil` |
| Enseignant | `prof` | `prof` |
| Étudiant | `etudiant` | `etudiant` |

> ⚠️ Les mots de passe doivent être hashés en base (`scripts/seed_utilisateurs_s4.php`).

---

### 👥 Équipe de Développement

| Membre | Algorithme dédié |
| :--- | :--- |
| **CHARLES Nesrine** | AlgoNesrine — Répartition Semestre 1 |
| **DAHMANI Rached** | AlgoRached — Répartition Semestre 3 |
| **BOUZLAFA Selman** | AlgoSelman — Répartition Semestre 2 |

---

### 📄 Licence

<div align="center">

Projet réalisé dans le cadre de la **SAE S3 — Constitution de Groupes** (BUT Informatique, IUT d'Orsay).
Usage académique uniquement.

</div>

---

<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:2C3E50,100:3498DB&height=120&section=footer&text=Projet-S3-Global%20|%202025%20|%20SAE%20Constitution%20de%20Groupes&fontColor=ffffff&fontSize=16&animation=fadeIn" />
</p>
