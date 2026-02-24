<p align="center">
  <img src="https://readme-typing-svg.demolab.com?font=Fira+Code&size=24&duration=3000&pause=1000&color=E76F00&center=true&vCenter=true&width=700&lines=java-desktop-app;Client+lourd+Java+Swing;Gestion+%26+r%C3%A9partition+de+groupes" alt="Typing SVG" />
</p>

<h1 align="center">🖥️ <span style="color:#E76F00;">java-desktop-app</span></h1>
<p align="center">Application client lourd Java Swing pour la gestion des promotions, étudiants et la constitution de groupes TD/TP.</p>

<p align="center">
  <img src="https://img.shields.io/badge/Lang-Java_17+-007396?style=for-the-badge&logo=java&logoColor=white" alt="Java"/>
  <img src="https://img.shields.io/badge/GUI-Swing-E76F00?style=for-the-badge&logo=java&logoColor=white" alt="Swing"/>
  <img src="https://img.shields.io/badge/Pattern-MVC-purple?style=for-the-badge" alt="MVC"/>
  <img src="https://img.shields.io/badge/JSON-Gson_2.10.1-2E7D32?style=for-the-badge&logo=google&logoColor=white" alt="Gson"/>
  <img src="https://img.shields.io/badge/API-REST_Client-FF6F00?style=for-the-badge&logo=json&logoColor=white" alt="REST"/>
</p>

---

### 🎯 À propos de l'application

🚀 **java-desktop-app** est le client lourd du projet SAE S3, destiné au **responsable de filière**. Il consomme l'API REST du backend `web-api` et permet de :

* 🎓 **Consulter** les promotions, listes d'étudiants et statistiques détaillées
* 👥 **Constituer des groupes** — manuellement (glisser-déposer) ou automatiquement (algorithmes)
* ✏️ **Gérer les étudiants** — ajouter, modifier, supprimer via CRUD complet
* 💾 **Sauvegarder les affectations** directement en base via l'API REST
* 📊 **Visualiser les statistiques** — filles/garçons, redoublants, anglophones, apprentis, covoiturage

> L'application intègre 4 algorithmes de répartition automatique (3 algorithmes S3 + 1 glouton multi-critères générique).

---

### 🛠️ Stack Technique

<div align="center">

**💡 Core**

![Java](https://img.shields.io/badge/Java-JDK_17+-007396?style=for-the-badge&logo=java&logoColor=white)
![Swing](https://img.shields.io/badge/GUI-Swing-E76F00?style=for-the-badge&logo=java&logoColor=white)
![MVC](https://img.shields.io/badge/Architecture-MVC-purple?style=for-the-badge)

**🔌 Communication**

![Gson](https://img.shields.io/badge/Gson-2.10.1-2E7D32?style=for-the-badge&logo=google&logoColor=white)
![HTTP](https://img.shields.io/badge/HTTP-Basic_Auth-FF6F00?style=for-the-badge&logo=curl&logoColor=white)
![REST](https://img.shields.io/badge/API-REST_JSON-3498DB?style=for-the-badge&logo=json&logoColor=white)

</div>

---

### 🚀 Fonctionnalités Clés

<div align="center">

### 🔐 **Authentification**
> *Double niveau de sécurité*

</div>

- **HTTP Basic Auth** (technique) : identifiants serveur configurés dans `Config.java`, envoyés à chaque requête.
- **Login applicatif** (fonctionnel) : l'utilisateur saisit son login/mot de passe, vérifié côté backend. Seul le rôle `responsable_filiere` accède au dashboard complet.
- **Gestion de session** : le cookie `PHPSESSID` est capturé au login et renvoyé à chaque requête.

---

<div align="center">

### 📋 **Gestion des promotions & étudiants**
> *Consultation et CRUD complet*

</div>

- **Promotions** : liste complète (année, semestre, parcours, effectif, nombre de groupes).
- **Détail promotion** : tableau des étudiants avec genre, bac, groupe, statuts (redoublant, anglophone, apprenti).
- **Statistiques** : `nbFilles`, `nbGarçons`, `nbRedoublants`, `nbAnglophones`, `nbApprentis`, `nbAvecCovoiturage`, `nbSansGroupe`.
- **CRUD étudiant** : ajout (formulaire modal), modification, suppression avec confirmation.

---

<div align="center">

### 🧮 **Constitution de groupes**
> *Mode automatique ou manuel*

</div>

**Mode automatique** — L'utilisateur configure les paramètres (taille min/max/cible via spinners) et choisit un algorithme :

| Algorithme | Stratégie |
| :--- | :--- |
| `GloutonRedoublantsEquilibre` | Anglais → groupe 1, puis équilibrage redoublants, puis tailles |
| `GloutonScoreLocalS3` | Placement unitaire minimisant le coût local (taille + redoublants) |
| `ForceBruteBacktrackingS3` | Backtracking exhaustif avec limite de nœuds |
| `GloutonMultiCriteresSX` | **Glouton générique** (S3/S4/S5+) — packs covoiturage, regroupement anglais/redoublants, coût `taille + 2×filles` |

**Mode manuel** — Interface de glisser-déposer pour affecter individuellement chaque étudiant.

---

### 🏗️ Architecture du projet

Le projet suit le pattern **Modèle-Vue-Contrôleur (MVC)** :

```
src/
├── 🐹 Main.java                             # Point d'entrée → lance VueConnexion
├── 📁 api/
│   └── 🐹 ApiClient.java                    # Singleton — 10 méthodes REST, Basic Auth, cookie session
├── 📁 controleur/
│   ├── 🐹 ControleurAuth.java               # login(), logout(), gestion rôles
│   ├── 🐹 ControleurGroupe.java             # affectations, statistiques groupes, save
│   └── 🐹 ControleurPromotion.java          # promotions, étudiants, statistiques promo
├── 📁 modele/
│   ├── 🐹 Etudiant.java                     # id, nom, genre, groupe, statuts (redoublant, anglophone, apprenti)
│   ├── 🐹 Groupe.java                       # id, nom, effectif, etudiants, stats
│   ├── 🐹 Promotion.java                    # id composite (année|semestre|parcours), libellé
│   └── 🐹 Utilisateur.java                  # id, nom, prenom, login, email, genre
├── 📁 vue/
│   ├── 🐹 VueConnexion.java                 # Écran de login
│   ├── 🐹 VueDashboard.java                 # Dashboard avec sidebar navigation
│   ├── 🐹 VuePromotions.java                # Table des promotions
│   ├── 🐹 VueDetailsPromotion.java          # Détails étudiants d'une promo
│   ├── 🐹 VueConstitutionGroupes.java       # Sélecteur promo + choix mode (auto/manuel)
│   ├── 🐹 VueRepartitionAutomatique.java    # Config algo (spinners) + résultats
│   ├── 🐹 VueRepartitionManuelle.java       # Mode manuel simplifié
│   ├── 🐹 VueRepartitionManuelleComplete.java # Mode manuel complet (drag & drop)
│   ├── 🐹 VueGestionEtudiants.java          # CRUD étudiants (table + actions)
│   ├── 🐹 VueMesInformations.java           # Infos utilisateur connecté
│   └── 🐹 DialogueAjoutEtudiant.java        # Modal ajout/modification étudiant
├── 📁 algo/                                  # Algorithmes embarqués (S3 + multi-critères)
│   ├── 🐹 GloutonRedoublantsEquilibre.java
│   ├── 🐹 GloutonScoreLocalS3.java
│   ├── 🐹 ForceBruteBacktrackingS3.java
│   ├── 🐹 GloutonMultiCriteresSX.java       # ← UNIQUE : glouton générique multi-semestres
│   ├── 🐹 ContraintesGroupesS3.java
│   ├── 🐹 GroupeAlgoS3.java
│   ├── 🐹 GroupingUtilsS3.java
│   ├── 🐹 Pack.java
│   └── 🐹 DemoS3.java
└── 📁 utils/
    ├── 🐹 Config.java                       # API_URL, auth, couleurs, tailles par défaut
    ├── 🐹 SessionManager.java               # Singleton session (user, role, cookie)
    ├── 🐹 ModernTextField.java              # Composant UI personnalisé
    ├── 🐹 RoundedButton.java                # Bouton arrondi
    └── 🐹 RoundedPanel.java                 # Panel arrondi
```

---

### ⚙️ Configuration

#### Fichier `src/utils/Config.java`

| Constante | Valeur par défaut | Description |
| :--- | :--- | :--- |
| `API_URL` | `https://projets.iut-orsay.fr/.../api/` | URL de base de l'API REST |
| `HTTP_AUTH_USER` | *(à configurer)* | Login HTTP Basic Auth (accès serveur) |
| `HTTP_AUTH_PASSWORD` | *(à configurer)* | Mot de passe HTTP Basic Auth |
| `HTTP_TIMEOUT` | `10000` | Timeout requêtes HTTP (ms) |
| `WINDOW_WIDTH` / `HEIGHT` | `1200` × `800` | Taille fenêtre par défaut |
| `DEFAULT_GROUP_MIN` / `MAX` / `TARGET` | `17` / `20` / `18` | Tailles de groupe par défaut |
| Couleurs | `PRIMARY=#2C3E50`, `SECONDARY=#3498DB`, etc. | Thème UI |

> ⚠️ Ne jamais commiter de secrets réels. Utiliser des placeholders dans le dépôt.

---

### 🔌 Endpoints API consommés

`ApiClient.java` (singleton) centralise 10 appels REST vers `web-api` :

| Méthode | Endpoint | HTTP | Retour |
| :--- | :--- | :--- | :--- |
| `login()` | `?endpoint=login` | POST | `Utilisateur` |
| `logout()` | `?endpoint=logout` | POST | — |
| `getPromotions()` | `?endpoint=promotions` | GET | `List<Promotion>` |
| `getEtudiants()` | `?endpoint=etudiants&promotion={id}` | GET | `List<Etudiant>` |
| `getGroupes()` | `?endpoint=groupes&promotion={id}` | GET | `List<Groupe>` |
| `saveAffectations()` | `?endpoint=affectations` | POST | `boolean` |
| `ajouterEtudiant()` | `?endpoint=ajouter_etudiant` | POST | `Etudiant` |
| `modifierEtudiant()` | `?endpoint=modifier_etudiant` | POST | `Etudiant` |
| `supprimerEtudiant()` | `?endpoint=supprimer_etudiant` | POST | — |

---

### 💻 Installation & Démarrage

#### 1. Prérequis
* **Java JDK 17** ou supérieur installé.
* **Backend `web-api` opérationnel** et accessible à l'URL configurée.
* Dépendance : `lib/gson-2.10.1.jar` (incluse dans le dépôt).

#### 2. Configuration
Éditer `src/utils/Config.java` — renseigner `API_URL`, `HTTP_AUTH_USER`, `HTTP_AUTH_PASSWORD`.

#### 3. Compilation
```powershell
cd java-desktop-app
javac -encoding UTF-8 -cp "lib/gson-2.10.1.jar" -d bin (Get-ChildItem -Path src -Recurse -Filter *.java | ForEach-Object { $_.FullName })
```

#### 4. Lancement
```powershell
java -cp "bin;lib/gson-2.10.1.jar" Main
```

L'application va :
1. Afficher l'écran de connexion.
2. Après login (`responsable_filiere`), ouvrir le dashboard principal.
3. Permettre la navigation entre promotions, groupes, gestion étudiants.

---

### 🎮 Utilisation

| Écran | Action |
| :--- | :--- |
| **Connexion** | Saisir login/mot de passe (rôle `responsable_filiere`) |
| **Dashboard** | Navigation via sidebar : promotions, groupes, étudiants, profil |
| **Promotions** | Consulter la liste, cliquer pour voir le détail |
| **Constitution groupes** | Choisir une promo → mode automatique ou manuel |
| **Répartition auto** | Configurer min/max/cible → choisir un algorithme → visualiser le résultat |
| **Répartition manuelle** | Glisser-déposer les étudiants dans les groupes |
| **Gestion étudiants** | Ajouter / modifier / supprimer via le tableau + formulaire modal |

---

### 🔧 Dépannage

| Problème | Solution |
| :--- | :--- |
| Connexion refusée | Vérifier `HTTP_AUTH_USER`/`HTTP_AUTH_PASSWORD` dans `Config.java` et l'accessibilité de `API_URL` |
| Échec login applicatif | Vérifier les identifiants utilisateur et que le rôle renvoyé est bien `responsable_filiere` |
| Erreur JSON / réponse non-JSON | Vérifier que l'URL pointe vers l'API (pas vers une page HTML d'erreur Apache) |
| Affectations non sauvegardées | Valider l'endpoint `affectations` côté serveur et les droits du compte |

---

### 👥 Équipe

| Membre | Rôle |
| :--- | :--- |
| **CHARLES Nesrine** | Développeur |
| **DAHMANI Rached** | Développeur |
| **BOUZLAFA Selman** | Développeur |

---

### 📄 Licence

<div align="center">

Projet réalisé dans le cadre de la **SAE S3 — Constitution de Groupes** (BUT Informatique).
Usage académique uniquement.

</div>

---

<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:2C3E50,100:E76F00&height=120&section=footer&text=java-desktop-app%20|%20Client%20Swing%20MVC&fontColor=ffffff&fontSize=16&animation=fadeIn" />
</p>
