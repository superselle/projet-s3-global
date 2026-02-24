-- NETTOYAGE PREALABLE 
BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE MATIERE_GROUPE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE ENSEIGNANT_MATIERE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE ETUDIANT_REPONSE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE NOTE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE ETUDIANT CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE GROUPE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE ENSEIGNANT CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE TYPE_BAC CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE ROLE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE MENTION_BAC CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE REPONSE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE MATIERE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE PARCOURS CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE SONDAGE CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE FORMATION CASCADE CONSTRAINTS';
   EXECUTE IMMEDIATE 'DROP TABLE UTILISATEUR CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL; -- Ignore les erreurs si les tables n'existent pas encore
END;
/

-- UTILISATEUR
CREATE TABLE UTILISATEUR(
   id_utilisateur NUMBER(10),
   prenom_utilisateur VARCHAR2(50) NOT NULL,
   nom_utilisateur VARCHAR2(50) NOT NULL,
   mail_utilisateur VARCHAR2(100) NOT NULL,
   tel_utilisateur VARCHAR2(15),
   adresse_utilisateur VARCHAR2(200),
   genre_utilisateur VARCHAR2(20),
   statut_utilisateur VARCHAR2(20),         -- Ex: 'ETUDIANT', 'ENSEIGNANT'
   date_naissance DATE,
   login_utilisateur VARCHAR2(50) NOT NULL,
   mdp_hash_utilisateur VARCHAR2(255) NOT NULL, 
   PRIMARY KEY(id_utilisateur)
);

-- FORMATION
CREATE TABLE FORMATION(
   id_formation VARCHAR2(10), 
   nom_formation VARCHAR2(100), -- Ex: 'Informatique'
   PRIMARY KEY(id_formation)
);

-- SONDAGE
CREATE TABLE SONDAGE(
   id_sondage NUMBER(10),
   nom_sondage VARCHAR2(100),
   contenu_sondage VARCHAR2(4000),
   PRIMARY KEY(id_sondage)
);

-- PARCOURS
CREATE TABLE PARCOURS(
   id_parcours VARCHAR2(10),
   initiale_parcours VARCHAR2(5), -- ex: 'A'
   nom_parcours VARCHAR2(100),
   type_parcours VARCHAR2(10), -- Ex: 'Initial' ou 'Apprenti'
   id_formation VARCHAR2(10) NOT NULL,
   PRIMARY KEY(id_parcours),
   FOREIGN KEY(id_formation) REFERENCES FORMATION(id_formation)
);

-- MATIERE
CREATE TABLE MATIERE(
   id_matiere VARCHAR2(20), -- Ex: 'R3.07'
   lettre_matiere CHAR(1),  -- Ex: 'A'
   numero_matiere VARCHAR2(10),
   nom_matiere VARCHAR2(100),
   PRIMARY KEY(id_matiere)
);

-- REPONSE
CREATE TABLE REPONSE(
   id_reponse NUMBER(10),
   contenu_reponse VARCHAR2(500), -- Réponses parfois longues
   id_sondage NUMBER(10) NOT NULL,
   PRIMARY KEY(id_reponse),
   FOREIGN KEY(id_sondage) REFERENCES SONDAGE(id_sondage)
);

-- MENTION_BAC
CREATE TABLE MENTION_BAC(
   id_mention VARCHAR2(10), 
   libelle_mention VARCHAR2(50), -- Ex: 'Très Bien"
   PRIMARY KEY(id_mention)
);

-- ROLE
CREATE TABLE ROLE(
   id_role VARCHAR2(20),
   libelle_role VARCHAR2(50),
   description VARCHAR2(255),
   PRIMARY KEY(id_role)
);

-- TYPE_BAC
CREATE TABLE TYPE_BAC(
   id_type VARCHAR2(10),
   libelle_type VARCHAR2(100), -- Ex: "Général"
   PRIMARY KEY(id_type)
);

-- ENSEIGNANT (Hérite de Utilisateur)
CREATE TABLE ENSEIGNANT(
   id_enseignant NUMBER(10),
   id_role VARCHAR2(20),
   id_utilisateur NUMBER(10) NOT NULL,
   PRIMARY KEY(id_enseignant),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_role) REFERENCES ROLE(id_role),
   FOREIGN KEY(id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
);

-- GROUPE
CREATE TABLE GROUPE(
   id_groupe VARCHAR2(20), -- Ex: '3D'
   lettre_groupe CHAR(1), -- Ex; 'D'
   chiffre_semestre NUMBER(1), -- Ex: 3
   effectif NUMBER(3),         -- Ex: 26
   annee_promo NUMBER(4),      -- Ex: 2025-26
   id_parcours VARCHAR2(10) NOT NULL,
   PRIMARY KEY(id_groupe),
   FOREIGN KEY(id_parcours) REFERENCES PARCOURS(id_parcours)
);

-- ETUDIANT (Hérite de Utilisateur)
CREATE TABLE ETUDIANT(
   id_etudiant NUMBER(10),
   id_covoiturage NUMBER(10), -- Nullable car pas forcément de groupe covoit
   est_redoublant NUMBER(1) DEFAULT 0 CHECK (est_redoublant IN (0, 1)),
   est_anglophone NUMBER(1) DEFAULT 0 CHECK (est_anglophone IN (0, 1)),
   est_apprenti NUMBER(1) DEFAULT 0 CHECK (est_apprenti IN (0, 1)),
   id_type VARCHAR2(10) NOT NULL,
   id_mention VARCHAR2(10) NOT NULL,
   id_parcours VARCHAR2(10) NOT NULL,
   id_groupe VARCHAR2(20), -- Nullable car affecté plus tard
   id_utilisateur NUMBER(10) NOT NULL,
   PRIMARY KEY(id_etudiant),
   UNIQUE(id_utilisateur),
   FOREIGN KEY(id_type) REFERENCES TYPE_BAC(id_type),
   FOREIGN KEY(id_mention) REFERENCES MENTION_BAC(id_mention),
   FOREIGN KEY(id_parcours) REFERENCES PARCOURS(id_parcours),
   FOREIGN KEY(id_groupe) REFERENCES GROUPE(id_groupe),
   FOREIGN KEY(id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur)
);

-- NOTE
CREATE TABLE NOTE(
   id_note NUMBER(10),
   valeur_note NUMBER(4,2) NOT NULL, -- Permet 14.50, 20.00...
   commentaire_note VARCHAR2(255),
   id_etudiant NUMBER(10) NOT NULL,
   id_matiere VARCHAR2(20) NOT NULL,
   PRIMARY KEY(id_note),
   FOREIGN KEY(id_etudiant) REFERENCES ETUDIANT(id_etudiant),
   FOREIGN KEY(id_matiere) REFERENCES MATIERE(id_matiere),
   CONSTRAINT chk_note_valide CHECK (valeur_note BETWEEN 0 AND 20)
);

-- ASSOCIATIONS (Tables d'association n,n)
CREATE TABLE ETUDIANT_REPONSE(
   id_etudiant NUMBER(10),
   id_reponse NUMBER(10),
   PRIMARY KEY(id_etudiant, id_reponse),
   FOREIGN KEY(id_etudiant) REFERENCES ETUDIANT(id_etudiant),
   FOREIGN KEY(id_reponse) REFERENCES REPONSE(id_reponse)
);

CREATE TABLE ENSEIGNANT_MATIERE(
   id_enseignant NUMBER(10),
   id_matiere VARCHAR2(20),
   PRIMARY KEY(id_enseignant, id_matiere),
   FOREIGN KEY(id_enseignant) REFERENCES ENSEIGNANT(id_enseignant),
   FOREIGN KEY(id_matiere) REFERENCES MATIERE(id_matiere)
);

CREATE TABLE MATIERE_GROUPE(
   id_groupe VARCHAR2(20),
   id_matiere VARCHAR2(20),
   PRIMARY KEY(id_groupe, id_matiere),
   FOREIGN KEY(id_groupe) REFERENCES GROUPE(id_groupe),
   FOREIGN KEY(id_matiere) REFERENCES MATIERE(id_matiere)
);



/* =========
    LES VUES 
    ========= */
     
/*
    Utilisée pour "Mes informations" et "Liste des étudiants"
    Scénarios : 1 (Étudiant) et 10 (Enseignant)
    Objectif : Avoir toutes les infos (Nom, Prénom, Bac, Groupe, Parcours) sans faire 5 jointures à chaque fois
*/
CREATE OR REPLACE VIEW V_INFO_COMPLETE_ETUDIANT AS
SELECT 
     e.id_etudiant,
     u.nom_utilisateur,
     u.prenom_utilisateur,
     u.mail_utilisateur,
     u.genre_utilisateur,
     u.statut_utilisateur,
     tb.libelle_type AS type_bac,
     mb.libelle_mention AS mention_bac,
     p.nom_parcours,
     e.est_redoublant,
     e.est_apprenti,
     g.id_groupe,
     g.lettre_groupe
FROM ETUDIANT e
JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur
JOIN TYPE_BAC tb ON e.id_type = tb.id_type
LEFT JOIN MENTION_BAC mb ON e.id_mention = mb.id_mention
JOIN PARCOURS p ON e.id_parcours = p.id_parcours
LEFT JOIN GROUPE g ON e.id_groupe = g.id_groupe;


/*
    Utilisée pour "Constitution des groupes"
    Scénarios : 7 (Répartition automatique)
    Objectif : Visualiser en temps réel si les contraintes (Mixité, Effectif, Redoublant....) sont respectées
*/
CREATE OR REPLACE VIEW V_DASHBOARD_GROUPES AS
SELECT 
     g.id_groupe,
     g.lettre_groupe,
     g.effectif AS capacite_max,
     COUNT(e.id_etudiant) AS nb_inscrits,
     -- Calcul du nombre de filles pour la contrainte de mixité 
     SUM(CASE WHEN u.genre_utilisateur = 'F' THEN 1 ELSE 0 END) AS nb_femmes,
     -- Calcul du nombre de redoublants pour l'homogénéité
     SUM(CASE WHEN e.est_redoublant = 1 THEN 1 ELSE 0 END) AS nb_redoublants,
     -- Calcul du nombre d'apprentis
     SUM(CASE WHEN e.est_apprenti = 1 THEN 1 ELSE 0 END) AS nb_apprentis
FROM GROUPE g
LEFT JOIN ETUDIANT e ON g.id_groupe = e.id_groupe
LEFT JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur
GROUP BY g.id_groupe, g.lettre_groupe, g.effectif;


/*
    Algorithme de répartition.
    Scénarios : 7 (Règle "Équilibrer les niveaux académiques" )
    Objectif : Calculer la moyenne générale de chaque étudiant pour pouvoir les trier par niveau
*/
CREATE OR REPLACE VIEW V_MOYENNE_ETUDIANT AS
SELECT 
     e.id_etudiant,
     u.nom_utilisateur,
     u.prenom_utilisateur,
     AVG(n.valeur_note) AS moyenne_generale
FROM ETUDIANT e
JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur
JOIN NOTE n ON e.id_etudiant = n.id_etudiant
GROUP BY e.id_etudiant, u.nom_utilisateur, u.prenom_utilisateur;


/*
    Aide à la décision manuelle.
    Scénarios : 3 (Réponse sondage) et 7 (Création groupe)
    Objectif : Voir qui a répondu quoi pour regrouper par choixx (exemple : "Options" ou "Covoiturage") 
*/
CREATE OR REPLACE VIEW V_RESULTATS_SONDAGE AS
SELECT 
     s.nom_sondage,
     r.contenu_reponse,
     u.nom_utilisateur,
     u.prenom_utilisateur,
     e.id_etudiant
FROM SONDAGE s
JOIN REPONSE r ON s.id_sondage = r.id_sondage
JOIN ETUDIANT_REPONSE er ON r.id_reponse = er.id_reponse
JOIN ETUDIANT e ON er.id_etudiant = e.id_etudiant
JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur;


/* =========
    LES REQUETES
    ========= */

/* 
    Liste des étudiants sans groupe
    Fonctionnalité : Répartition manuelle ou Initialisation de l'algo
    Maquette : "Groupes à constituer" 
*/
SELECT id_etudiant, nom_utilisateur, prenom_utilisateur, nom_parcours 
FROM V_INFO_COMPLETE_ETUDIANT
WHERE id_groupe IS NULL
ORDER BY nom_utilisateur;


/* 
    Vérification de l'équilibre des niveaux académiques par groupe
    Fonctionnalité : Vérification après répartition automatique 
    Objectif : Comparer la moyenne de chaque groupe pour voir s'ils sont équilibrés
*/
SELECT 
     g.lettre_groupe,
     ROUND(AVG(vme.moyenne_generale), 2) AS moyenne_du_groupe
FROM GROUPE g
JOIN ETUDIANT e ON g.id_groupe = e.id_groupe
JOIN V_MOYENNE_ETUDIANT vme ON e.id_etudiant = vme.id_etudiant
GROUP BY g.lettre_groupe
ORDER BY moyenne_du_groupe DESC;


/* D
    étection des groupes non mixtes
    Fonctionnalité : Contrainte de mixité 
    Objectif : Identifier les groupes qui n'ont aucune femme ou aucun homme.
*/
SELECT id_groupe, lettre_groupe, nb_femmes, (nb_inscrits - nb_femmes) as nb_hommes
FROM V_DASHBOARD_GROUPES
WHERE nb_femmes = 0 
    OR (nb_inscrits - nb_femmes) = 0;


/* 
    Récupérer les étudiants ayant choisi une option spécifique pour les mettre ensemble
    Fonctionnalité : Regroupement par critère
    Objectif : Trouver tous les étudiants ayant répondu "IA et Data Mining" au sondage
*/
SELECT e.id_etudiant, u.nom_utilisateur, u.prenom_utilisateur
FROM ETUDIANT e
JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur
JOIN ETUDIANT_REPONSE er ON e.id_etudiant = er.id_etudiant
JOIN REPONSE r ON er.id_reponse = r.id_reponse
WHERE r.contenu_reponse = 'IA et Data Mining'; 

COMMIT;