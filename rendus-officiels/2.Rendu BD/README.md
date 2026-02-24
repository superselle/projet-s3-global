    # 📂 Projet SAE S301 - Gestion des Groupes de TD/TP

Ce dossier contient les scripts SQL nécessaires à l'implémentation de la base de données pour l'application de gestion de groupes. Le projet a été conçu pour un environnement **Oracle Database** (PL/SQL).

## 📋 Prérequis Technques

* **SGBD :** Oracle Database.
* **Outils recommandés :** Oracle SQL Developer ou VS Code (avec extension Oracle).
* **Encodage :** Les fichiers sont encodés en UTF-8.

---

## 🚀 Ordre d'Exécution des Scripts

⚠️ **Important :** Il est impératif d'exécuter les 3 fichiers dans l'ordre exact ci-dessous pour respecter les dépendances (Tables > Logique > Données).

1.  **`Creation.sql`** (Création des tables, vues)
    * *Action :* Nettoie l'environnement (DROP), crée toutes les tables (DDL) et installe les vues applicatives (Reporting).
    
2.  **`Fonctions_Procedures_Triggers.sql`** (PL/SQL)
    * *Action :* Initialise les séquences, crée la table technique d'audit (`LOG_MODIF_NOTE`) et compile toutes les fonctions, procédures stockées et triggers.
    * *Pourquoi maintenant ?* Les triggers doivent être actifs **avant** l'insertion des données pour garantir le formatage et les contrôles d'intégrité.

3.  **`Insertion.sql`** (Jeu de données)
    * *Action :* Peuple la base de données avec un jeu de test réaliste (Étudiants, Enseignants, Notes, Groupes) permettant de valider le fonctionnement immédiat de l'application.

---

## 📂 Description Détaillée des Fichiers

| Fichier | Description Technique |
| :--- | :--- |
| **`Creation.sql`** | Ce script gère l'aspect statique et consultatif de la base :<br>• **Nettoyage** : Suppression propre des anciens objets.<br>• **Structure** : Création des tables avec Clés Primaires/Étrangères et contraintes `CHECK`.<br>• **Vues** : Création des interfaces de consultation (`V_INFO_COMPLETE_ETUDIANT`, `V_DASHBOARD_GROUPES`) et des requêtes types. |
| **`Fonctions_Procedures_Triggers.sql`** | Contient toute les fonctionnalité attentendu (PL/SQL) : <br>• **Fonctions** : Calculs (Moyennes, Capacité groupes).<br>• **Procédures** : Actions transactionnelles (Affectation, Ajout de notes).<br>• **Triggers** : Automatismes (Audit, Formatage des noms).<br>• **Séquences** : Générateurs d'ID. |
| **`Insertion.sql`** | Script de population. Il insère des scénarios de test complets (étudiants sans groupes, notes variées, sondages....).

---

## ⚙️ Notes Techniques

* **Affichage des sorties :** Pour visualiser les messages de confirmation des procédures (exemple : `Succès : Étudiant affecté...`), assurez-vous d'activer la sortie serveur :
    ```sql
    SET SERVEROUTPUT ON;
    ```
    *(Dans SQL Developer : Affichage > Sortie SGBD)*.

* **Séquences et IDs :** * Le script `Insertion.sql` utilise des IDs fixes pour garantir la cohérence du jeu de test initial.
    * Les procédures stockées (exemple : `P_AJOUTER_NOTE`) utilisent des séquences Oracle (`SEQ_ID_NOTE`) pour l'ajout de nouvelles données dynamiques.



## 👥 Auteurs

Projet réalisé par :
* **Rached DAHMANI**
* **Nesrine CHARLES**
* **Selman BOUZLAFA**