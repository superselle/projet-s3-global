<p align="center">
  <img src="https://readme-typing-svg.demolab.com?font=Fira+Code&size=24&duration=3000&pause=1000&color=777BB4&center=true&vCenter=true&width=700&lines=web-api;Backend+PHP+MVC+%2B+API+REST;Gestion+compl%C3%A8te+%C3%A9tudiants+%26+groupes" alt="Typing SVG" />
</p>

<h1 align="center">🌐 <span style="color:#777BB4;">web-api</span></h1>
<p align="center">Backend PHP MVC avec interface web complète et API REST JSON pour la gestion des promotions, étudiants, groupes et sondages.</p>

<p align="center">
  <img src="https://img.shields.io/badge/Lang-PHP_8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/BDD-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
  <img src="https://img.shields.io/badge/Pattern-MVC-purple?style=for-the-badge" alt="MVC"/>
  <img src="https://img.shields.io/badge/API-REST_JSON-FF6F00?style=for-the-badge&logo=json&logoColor=white" alt="REST"/>
  <img src="https://img.shields.io/badge/Auth-Sessions+Roles-E74C3C?style=for-the-badge&logo=lock&logoColor=white" alt="Auth"/>
</p>

---

### 🎯 À propos du module

🚀 **web-api** est le cœur du système. Il remplit **deux rôles** :

1. **Interface web MVC complète** — application web pour tous les acteurs (étudiants, enseignants, responsables). Gestion intégrale : promotions, groupes, étudiants, sondages, notes, binômes, import/export CSV.
2. **API REST JSON** — 10 endpoints consommés par le client desktop `java-desktop-app` pour la constitution de groupes, le CRUD étudiants et la sauvegarde d'affectations.

📊 **Fonctionnalités principales** :
* 🔐 **Authentification multi-rôles** : étudiant, enseignant, responsable filière, responsable formation
* 🎓 **Gestion complète** : promotions, groupes, étudiants, enseignants
* 📊 **Sondages** : création, réponses (mode unique/classement), statistiques
* 🤝 **Choix de binôme** : jusqu'à 3 choix classés par étudiant
* 📥 **Import/Export CSV** : notes et listes pédagogiques
* 📢 **Publication** : contrôle de la visibilité des groupes pour les étudiants

> Base de données MySQL — 17 tables, 4 vues, 1 fonction, 1 procédure, 5 triggers.

---

### 🛠️ Stack Technique

<div align="center">

**💡 Backend**

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PDO](https://img.shields.io/badge/PDO-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Sessions](https://img.shields.io/badge/Auth-Sessions_PHP-E74C3C?style=for-the-badge&logo=php&logoColor=white)

**🎨 Frontend Web**

![HTML](https://img.shields.io/badge/HTML-5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS-Custom-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![PHP Views](https://img.shields.io/badge/Views-PHP_Templates-777BB4?style=for-the-badge)

</div>

---

### 🚀 Fonctionnalités par rôle

<div align="center">

### 🎓 **Étudiant**
> *Consultation et interactions*

</div>

- Consultation de sa promotion, ses notes (avec moyennes et stats)
- Réponse aux sondages (mode unique ou classement)
- Choix de binôme (jusqu'à 3 choix classés)
- Consultation de son groupe (si publié)

---

<div align="center">

### 👨‍🏫 **Enseignant**
> *Lecture seule*

</div>

- Consultation des promotions et des groupes

---

<div align="center">

### 📋 **Responsable de Filière**
> *Gestion complète*

</div>

- **Étudiants** : CRUD complet (ajout, modification, suppression avec cascade)
- **Groupes** : constitution automatique (covoiturage + mixité) ou manuelle, publication/dépublication
- **Sondages** : création, consultation des résultats avec statistiques
- **CSV** : import de notes, export de listes (minimal ou complet)
- **Qualité** : contrôle qualité des groupes (statistiques de conformité)
- **Contraintes** : configuration des contraintes et objectifs par promotion

---

<div align="center">

### 🏛️ **Responsable de Formation**
> *Administration étendue*

</div>

- Tout ce que fait le responsable filière
- **Enseignants** : CRUD complet (ajout, modification, suppression, attribution de rôle)

---

### 🏗️ Architecture du projet

```
web-api/
├── 🐹 index.php                          # Routeur MVC : ?controller=X&action=Y
├── 📁 api/
│   ├── 🐹 index.php                      # Routeur API REST : ?endpoint=X (CORS + JSON)
│   ├── 🐹 test_endpoints.php             # Page diagnostic (vérifie les 10 endpoints)
│   └── 📁 endpoints/                     # 10 endpoints REST (1 fichier = 1 endpoint)
│       ├── login.php, logout.php
│       ├── promotions.php, etudiants.php, groupes.php
│       ├── statistiques.php, affectations.php
│       └── ajouter_etudiant.php, modifier_etudiant.php, supprimer_etudiant.php
├── 📁 config/
│   ├── 🐹 connexion.php                  # PDO MySQL (hostname, database, login, password)
│   ├── 🐹 paths.php                      # Détection BASE_URL automatique
│   └── 🐹 session_helper.php             # Variables de session pour les vues
├── 📁 controller/                        # 10 contrôleurs MVC
│   ├── 🐹 ControleurBase.php             # render(), requireLogin(), initViewVars()
│   ├── 🐹 ControleurAuth.php             # connexion, traiterConnexion, deconnexion
│   ├── 🐹 ControleurEtudiant.php         # dashboard, notes, sondages, binome
│   ├── 🐹 ControleurEnseignant.php       # dashboard enseignant
│   ├── 🐹 ControleurPromotions.php       # promotions, détails, publication, maPromotion
│   ├── 🐹 ControleurResponsableFiliere.php # (723 lignes) CRUD étudiants, groupes, CSV, sondages
│   ├── 🐹 ControleurResponsableFormation.php # CRUD enseignants
│   ├── 🐹 ControleurSondage.php          # Création sondages, résultats
│   ├── 🐹 ControleurCsv.php             # Import notes CSV, export promotion CSV
│   └── 🐹 ControleurProfil.php           # Infos personnelles (lecture seule)
├── 📁 model/                             # 15 modèles PDO
│   ├── Utilisateur.php, Etudiant.php, Enseignant.php
│   ├── Groupe.php, Promotion.php, Formation.php, Parcours.php
│   ├── Sondage.php, Reponse.php, RepondreSondage.php
│   ├── Note.php, Matiere.php
│   ├── TypeBac.php, MentionBac.php
│   ├── Commentaire.php, Contrainte.php, Objectif.php
│   └── ...
├── 📁 view/                              # Vues PHP organisées par domaine
│   ├── 📁 auth/                          # connexion.php
│   ├── 📁 commun/                        # header, footer, navbar, components, dashboard
│   ├── 📁 etudiant/                      # notes, sondages, binome
│   ├── 📁 promotions/                    # liste, détails, groupes
│   ├── 📁 responsableFiliere/            # formulaires, répartition, qualité
│   └── 📁 responsableFormation/          # formulaire enseignant
├── 📁 info-bd/                           # Scripts SQL + données de référence
├── 📁 scripts/                           # Utilitaires (seed, migrations, génération)
└── 📁 public/
    └── 📁 css/                           # Feuilles de style
```

---

### 🗄️ Base de données MySQL

#### Tables principales (17)

| Table | Description | Colonnes clés |
| :--- | :--- | :--- |
| `UTILISATEUR` | Tous les comptes (PK auto) | nom, prenom, mail, genre, login, mdp_hash |
| `ETUDIANT` | Étudiant (FK → UTILISATEUR) | est_redoublant, est_anglophone, est_apprenti, id_groupe, id_covoiturage |
| `ENSEIGNANT` | Enseignant (FK → UTILISATEUR, ROLE) | id_role |
| `GROUPE` | Groupe TD/TP | semestre, lettre, nom_groupe, annee_scolaire, effectif_max, id_parcours |
| `FORMATION` | Formation (ex: BUT_INFO) | id_formation, nom_formation |
| `PARCOURS` | Parcours (P_A, P_B, P_C, P_GEN) | initiale, nom, type, id_formation |
| `SONDAGE` | Sondage configurable | nom, contenu, mode (unique/classement), annee, semestre |
| `REPONSE` | Choix possibles d'un sondage | libelle, id_sondage |
| `ETUDIANT_REPONSE` | Réponses classées des étudiants | id_etudiant, id_sondage, rang, id_reponse |
| `NOTE` | Notes (0-20) | valeur DECIMAL(4,2), id_etudiant, id_matiere |
| `MATIERE` | Matières par semestre | code, nom, type (RESSOURCE/SAE/STAGE) |
| `COVOITURAGE` | Liens de covoiturage | places_max, est_ouvert |
| `TYPE_BAC` | Types de bac (GEN, TECH, PRO, AUTRE) | libelle |
| `MENTION_BAC` | Mentions (PASS, AB, B, TB) | libelle |
| `ROLE` | Rôles systèmes | RESP_FORM, RESP_PED, ENS |
| `CHOIX_BINOME` | Choix binôme (3 max) | *(ajouté par migration)* |
| `COMMENTAIRE_GROUPE` | Commentaires enseignants | id_promotion, id_groupe, commentaire |

#### Objets SQL avancés

| Type | Nom | Description |
| :--- | :--- | :--- |
| Vue | `V_INFOS_ETUDIANT` | Profil complet étudiant (8 jointures) |
| Vue | `V_GESTION_ENSEIGNANTS` | RH enseignants |
| Vue | `V_LISTE_PEDAGOGIQUE` | Liste de classe simplifiée |
| Fonction | `FNC_MOYENNE_ETUDIANT(id)` | Moyenne des notes |
| Procédure | `PRC_RESET_GROUPES_PROMO(annee)` | RAZ des affectations de groupes |
| Trigger | `TRG_DATA_UTILISATEUR_INSERT/UPDATE` | Normalisation nom/prénom, validation email + date |
| Trigger | `TRG_CHECK_CAP_GROUPE_INSERT/UPDATE` | Vérification capacité max du groupe |
| Trigger | `TRG_AUDIT_SUPPRESSION` | Log de suppression dans `LOG_OPERATIONS` |

---

### 🔌 Endpoints API REST

Tous accessibles via `api/index.php?endpoint=...` — réponses JSON.

| # | Endpoint | Méthode | Auth requise | Description |
| :--- | :--- | :--- | :--- | :--- |
| 1 | `login` | POST | Non | Authentifie (login ou email) + `password_verify`, retourne user + rôle + session |
| 2 | `logout` | POST | Session | Détruit la session |
| 3 | `promotions` | GET | Enseignant+ | Liste promotions avec `nbEtudiants` et `nbGroupes` |
| 4 | `etudiants` | GET | Session | Étudiants d'une promotion (`&promotion=ANNEE|SEM|PARCOURS`) |
| 5 | `groupes` | GET | Session | Groupes d'une promotion |
| 6 | `statistiques` | GET | Session | Stats agrégées (filles, garçons, redoublants, anglophones, apprentis) |
| 7 | `affectations` | POST | Session | Sauvegarde `[{idEtudiant, idGroupe}, ...]` en transaction |
| 8 | `ajouter_etudiant` | POST | Session | Crée UTILISATEUR + ETUDIANT en transaction |
| 9 | `modifier_etudiant` | POST | Session | Met à jour UTILISATEUR + ETUDIANT |
| 10 | `supprimer_etudiant` | POST | Session | Suppression cascade : réponses → notes → étudiant → utilisateur |

#### Exemples `curl`

```bash
# Connexion
curl -X POST "http://localhost:8000/api/index.php?endpoint=login" \
  -H "Content-Type: application/json" \
  -d '{"login":"respfil","password":"respfil"}' \
  -c cookies.txt

# Liste des promotions
curl "http://localhost:8000/api/index.php?endpoint=promotions" \
  -b cookies.txt

# Étudiants d'une promotion
curl "http://localhost:8000/api/index.php?endpoint=etudiants&promotion=2024-2025|3|P_A" \
  -b cookies.txt

# Sauvegarder des affectations
curl -X POST "http://localhost:8000/api/index.php?endpoint=affectations" \
  -H "Content-Type: application/json" \
  -d '{"affectations":[{"idEtudiant":1,"idGroupe":5},{"idEtudiant":2,"idGroupe":6}]}' \
  -b cookies.txt
```

---

### ⚙️ Configuration

#### Base de données — `config/connexion.php`

```php
static private $hostname = 'localhost';
static private $database = 'NOM_BASE';
static private $login    = 'UTILISATEUR';
static private $password = 'MOT_DE_PASSE';
```

#### Scripts SQL (`info-bd/`)

| Fichier | Contenu |
| :--- | :--- |
| `creation-insert-mysql.txt` | `CREATE TABLE` (17 tables) + insertions de base |
| `insert-donnees-mysql.txt` | Jeu de données complet (rôles, matières, groupes, étudiants) |
| `vue-proc-fonction-mysql.txt` | Vues, fonction, procédure, triggers |
| `etudiant s1-s2-s3.txt` / `s4-etc.txt` | Données étudiants complémentaires |
| `identifiants_utilisateurs.csv` | Export des identifiants |

> ⚠️ Les mots de passe dans les SQL bruts sont en clair. Lancer `scripts/seed_utilisateurs_s4.php` pour les hasher avec `password_hash()` et permettre l'authentification via `password_verify()`.

---

### 💻 Installation & Démarrage

#### 1. Prérequis
* **PHP 8.x** (ou 7.4+) avec extensions `pdo` et `pdo_mysql`.
* **MySQL / MariaDB**.

#### 2. Installation

```bash
# 1. Créer la base MySQL
mysql -u root -p -e "CREATE DATABASE sae_s3;"

# 2. Importer les scripts SQL (dans l'ordre)
mysql -u root -p sae_s3 < info-bd/creation-insert-mysql.txt
mysql -u root -p sae_s3 < info-bd/insert-donnees-mysql.txt
mysql -u root -p sae_s3 < info-bd/vue-proc-fonction-mysql.txt

# 3. Configurer config/connexion.php

# 4. Hasher les mots de passe
php scripts/seed_utilisateurs_s4.php
```

#### 3. Lancement

**Option A — Serveur PHP intégré** :
```bash
cd web-api
php -S localhost:8000
```
- Web MVC : `http://localhost:8000/index.php`
- API REST : `http://localhost:8000/api/index.php?endpoint=login`
- Diagnostic : `http://localhost:8000/api/test_endpoints.php`

**Option B — Apache / XAMPP** : Placer `web-api/` dans `htdocs/`.

#### 🔑 Comptes de démonstration

| Rôle | Login | Mot de passe |
| :--- | :--- | :--- |
| Responsable Formation | `respform` | `respform` |
| Responsable Filière | `respfil` | `respfil` |
| Enseignant | `prof` | `prof` |
| Étudiant | `etudiant` | `etudiant` |

---

### 🔧 Scripts utilitaires (`scripts/`)

| Script | Usage |
| :--- | :--- |
| `seed_utilisateurs_s4.php` | Insère les utilisateurs S4 avec mots de passe hashés |
| `seed_utilisateurs_data.php` | Insère les données utilisateurs avec hash |
| `migrate_binome_3choix.php` | Migration BDD : ajout table `CHOIX_BINOME` (3 choix) |
| `update_db_binome.php` | Mise à jour BDD pour la fonctionnalité binôme |
| `generate_identifiants_utilisateurs.php` | Génère `identifiants_utilisateurs.csv` |
| `generate-s2-student.php` | Génère des données étudiants S2 |

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

Projet réalisé dans le cadre de la **SAE S3 — Constitution de Groupes** (BUT Informatique, IUT d'Orsay).
Usage académique uniquement.

</div>

---

<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:4A148C,100:777BB4&height=120&section=footer&text=web-api%20|%20Backend%20PHP%20MVC%20%2B%20API%20REST&fontColor=ffffff&fontSize=16&animation=fadeIn" />
</p>
