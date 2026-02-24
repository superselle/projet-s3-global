/* ====================
   SCRIPT DE POPULATION 
   ==================== */

-- 1. INSERTION DES ROLES
INSERT INTO ROLE (id_role, libelle_role, description) VALUES ('RESP_FORM', 'Responsable Formation', 'Droits totaux sur la plateforme');
INSERT INTO ROLE (id_role, libelle_role, description) VALUES ('RESP_ANNEE', 'Responsable Année/Filière', 'Gestion des groupes et importation des notes');
INSERT INTO ROLE (id_role, libelle_role, description) VALUES ('ENS', 'Enseignant', 'Consultation des groupes et infos pédagogiques');

-- 2. INSERTION DE LA FORMATION
INSERT INTO FORMATION (id_formation, nom_formation) VALUES ('BUT_INFO', 'BUT Informatique');

-- 3. INSERTION DES PARCOURS
INSERT INTO PARCOURS (id_parcours, initiale_parcours, nom_parcours, type_parcours, id_formation) VALUES ('P_A', 'A', 'Développement (Réalisation d''app)', 'Initial', 'BUT_INFO');
INSERT INTO PARCOURS (id_parcours, initiale_parcours, nom_parcours, type_parcours, id_formation) VALUES ('P_B', 'B', 'Cybersécurité (Déploiement)', 'Alternance', 'BUT_INFO');
INSERT INTO PARCOURS (id_parcours, initiale_parcours, nom_parcours, type_parcours, id_formation) VALUES ('P_C', 'C', 'Base de Données (Admin données)', 'Initial', 'BUT_INFO');

-- 4. INSERTION DES TYPES DE BAC ET MENTIONS
INSERT INTO TYPE_BAC (id_type, libelle_type) VALUES ('GEN', 'Bac Général');
INSERT INTO TYPE_BAC (id_type, libelle_type) VALUES ('STI2D', 'Bac Techno STI2D');
INSERT INTO TYPE_BAC (id_type, libelle_type) VALUES ('STMG', 'Bac Techno STMG');
INSERT INTO TYPE_BAC (id_type, libelle_type) VALUES ('PRO', 'Bac Pro SN');

INSERT INTO MENTION_BAC (id_mention, libelle_mention) VALUES ('P', 'Passable');
INSERT INTO MENTION_BAC (id_mention, libelle_mention) VALUES ('AB', 'Assez Bien');
INSERT INTO MENTION_BAC (id_mention, libelle_mention) VALUES ('B', 'Bien');
INSERT INTO MENTION_BAC (id_mention, libelle_mention) VALUES ('TB', 'Très Bien');

-- 5. INSERTION DES MATIERES
-- Format ID: R + Semestre + . + Numero (exemple : R3.01)
-- lettre_matiere : 'R' (Ressource) ou 'S' (SAÉ/Projet)
INSERT INTO MATIERE (id_matiere, lettre_matiere, numero_matiere, nom_matiere) VALUES ('R3.01', 'R', '301', 'Développement Web');
INSERT INTO MATIERE (id_matiere, lettre_matiere, numero_matiere, nom_matiere) VALUES ('R3.07', 'R', '307', 'SQL et Base de Données');
INSERT INTO MATIERE (id_matiere, lettre_matiere, numero_matiere, nom_matiere) VALUES ('R3.02', 'R', '302', 'Droit des contrats');
INSERT INTO MATIERE (id_matiere, lettre_matiere, numero_matiere, nom_matiere) VALUES ('R3.04', 'R', '304', 'Qualité de développement');
INSERT INTO MATIERE (id_matiere, lettre_matiere, numero_matiere, nom_matiere) VALUES ('R3.14', 'R', '314', 'Anglais Technique'); -- Anciennement ANG
INSERT INTO MATIERE (id_matiere, lettre_matiere, numero_matiere, nom_matiere) VALUES ('R3.15', 'R', '315', 'Mathématiques - Probabilités'); -- Anciennement MATH
INSERT INTO MATIERE (id_matiere, lettre_matiere, numero_matiere, nom_matiere) VALUES ('S3.01', 'S', '301', 'Développement d''application (SAE)'); -- Ajout d'un projet SAE

-- 6. INSERTION DES SONDAGES ET REPONSES
INSERT INTO SONDAGE (id_sondage, nom_sondage, contenu_sondage) VALUES (1, 'Préférences Options S4', 'Classez vos préférences pour les modules complémentaires du semestre 4.');
INSERT INTO SONDAGE (id_sondage, nom_sondage, contenu_sondage) VALUES (2, 'Besoins en matériel', 'Avez-vous besoin d''un prêt d''ordinateur portable ?');

INSERT INTO REPONSE (id_reponse, contenu_reponse, id_sondage) VALUES (10, 'IA et Data Mining', 1);
INSERT INTO REPONSE (id_reponse, contenu_reponse, id_sondage) VALUES (11, 'Réalité Virtuelle', 1);
INSERT INTO REPONSE (id_reponse, contenu_reponse, id_sondage) VALUES (12, 'Management de projet agile', 1);
INSERT INTO REPONSE (id_reponse, contenu_reponse, id_sondage) VALUES (20, 'Oui', 2);
INSERT INTO REPONSE (id_reponse, contenu_reponse, id_sondage) VALUES (21, 'Non', 2);

-- 7. INSERTION DES UTILISATEURS (ENSEIGNANTS)
INSERT INTO UTILISATEUR VALUES (1, 'Nicolas', 'Ferey', 'nicolas.ferey@univ.fr', '0102030405', 'Orsay', 'H', 'ENS', TO_DATE('1980-05-12', 'YYYY-MM-DD'), 'nferey', 'hash123');
INSERT INTO UTILISATEUR VALUES (2, 'Jean', 'Dupont', 'jean.dupont@univ.fr', '0102030406', 'Paris', 'H', 'ENS', TO_DATE('1975-08-20', 'YYYY-MM-DD'), 'jdupont', 'hash456');
INSERT INTO UTILISATEUR VALUES (3, 'Marie', 'Curie', 'marie.curie@univ.fr', '0102030407', 'Gif', 'F', 'ENS', TO_DATE('1982-11-03', 'YYYY-MM-DD'), 'mcurie', 'hash789');
INSERT INTO UTILISATEUR VALUES (4, 'Alan', 'Turing', 'alan.turing@univ.fr', '0102030408', 'Londres', 'H', 'ENS', TO_DATE('1990-01-15', 'YYYY-MM-DD'), 'aturing', 'hash000');
INSERT INTO UTILISATEUR VALUES (5, 'Ada', 'Lovelace', 'ada.lovelace@univ.fr', '0102030409', 'Paris', 'F', 'ENS', TO_DATE('1985-06-30', 'YYYY-MM-DD'), 'alovelace', 'hash111');

-- 8. INSERTION DES ENSEIGNANTS
INSERT INTO ENSEIGNANT (id_enseignant, id_role, id_utilisateur) VALUES (1, 'RESP_FORM', 1);
INSERT INTO ENSEIGNANT (id_enseignant, id_role, id_utilisateur) VALUES (2, 'RESP_ANNEE', 2);
INSERT INTO ENSEIGNANT (id_enseignant, id_role, id_utilisateur) VALUES (3, 'ENS', 3);
INSERT INTO ENSEIGNANT (id_enseignant, id_role, id_utilisateur) VALUES (4, 'ENS', 4);
INSERT INTO ENSEIGNANT (id_enseignant, id_role, id_utilisateur) VALUES (5, 'ENS', 5);

-- 9. INSERTION DES GROUPES
-- Format ID: Chiffre Semestre + Lettre
INSERT INTO GROUPE VALUES ('3A', 'A', 3, 26, 2024, 'P_A');
INSERT INTO GROUPE VALUES ('3B', 'B', 3, 24, 2024, 'P_B');
INSERT INTO GROUPE VALUES ('3C', 'C', 3, 15, 2024, 'P_C');
INSERT INTO GROUPE VALUES ('3D', 'D', 3, 25, 2024, 'P_A');

-- 10. INSERTION DES UTILISATEURS (ETUDIANTS)
INSERT INTO UTILISATEUR VALUES (10, 'Thomas', 'Anderson', 'neo@etu.univ.fr', '0601010101', 'Zion', 'H', 'ETU', TO_DATE('2004-01-01', 'YYYY-MM-DD'), 'tanderson', 'pass1');
INSERT INTO UTILISATEUR VALUES (11, 'Trinity', 'Moss', 'trinity@etu.univ.fr', '0601010102', 'Zion', 'F', 'ETU', TO_DATE('2004-02-14', 'YYYY-MM-DD'), 'tmoss', 'pass2');
INSERT INTO UTILISATEUR VALUES (12, 'Morpheus', 'Dorne', 'morpheus@etu.univ.fr', '0601010103', 'Nebuchadnezzar', 'H', 'ETU', TO_DATE('2003-05-20', 'YYYY-MM-DD'), 'mdorne', 'pass3');
INSERT INTO UTILISATEUR VALUES (13, 'Luke', 'Skywalker', 'luke@etu.univ.fr', '0601010104', 'Tatooine', 'H', 'ETU', TO_DATE('2004-09-09', 'YYYY-MM-DD'), 'lsky', 'pass4');
INSERT INTO UTILISATEUR VALUES (14, 'Leia', 'Organa', 'leia@etu.univ.fr', '0601010105', 'Alderaan', 'F', 'ETU', TO_DATE('2004-09-09', 'YYYY-MM-DD'), 'lorgana', 'pass5');
INSERT INTO UTILISATEUR VALUES (15, 'Han', 'Solo', 'han@etu.univ.fr', '0601010106', 'Corellia', 'H', 'ETU', TO_DATE('2003-12-01', 'YYYY-MM-DD'), 'hsolo', 'pass6');
INSERT INTO UTILISATEUR VALUES (16, 'Harry', 'Potter', 'harry@etu.univ.fr', '0601010107', 'Londres', 'H', 'ETU', TO_DATE('2004-07-31', 'YYYY-MM-DD'), 'hpotter', 'pass7');
INSERT INTO UTILISATEUR VALUES (17, 'Hermione', 'Granger', 'hermione@etu.univ.fr', '0601010108', 'Londres', 'F', 'ETU', TO_DATE('2003-09-19', 'YYYY-MM-DD'), 'hgranger', 'pass8');
INSERT INTO UTILISATEUR VALUES (18, 'Ron', 'Weasley', 'ron@etu.univ.fr', '0601010109', 'Terrier', 'H', 'ETU', TO_DATE('2004-03-01', 'YYYY-MM-DD'), 'rweasley', 'pass9');
INSERT INTO UTILISATEUR VALUES (19, 'Bilbo', 'Sacquet', 'bilbo@etu.univ.fr', '0601010110', 'Comte', 'H', 'ETU', TO_DATE('2002-09-22', 'YYYY-MM-DD'), 'bsacquet', 'pass10');
INSERT INTO UTILISATEUR VALUES (20, 'Frodo', 'Sacquet', 'frodo@etu.univ.fr', '0601010111', 'Comte', 'H', 'ETU', TO_DATE('2004-09-22', 'YYYY-MM-DD'), 'fsacquet', 'pass11');
INSERT INTO UTILISATEUR VALUES (21, 'Sam', 'Gamgie', 'sam@etu.univ.fr', '0601010112', 'Comte', 'H', 'ETU', TO_DATE('2004-05-12', 'YYYY-MM-DD'), 'sgamgie', 'pass12');
INSERT INTO UTILISATEUR VALUES (22, 'Gandalf', 'Gris', 'gandalf@etu.univ.fr', '0601010113', 'Terre du Milieu', 'H', 'ETU', TO_DATE('2000-01-01', 'YYYY-MM-DD'), 'gandalf', 'pass13');
INSERT INTO UTILISATEUR VALUES (23, 'Lara', 'Croft', 'lara@etu.univ.fr', '0601010114', 'Manoir', 'F', 'ETU', TO_DATE('2003-02-14', 'YYYY-MM-DD'), 'lcroft', 'pass14');
INSERT INTO UTILISATEUR VALUES (24, 'Nathan', 'Drake', 'nathan@etu.univ.fr', '0601010115', 'USA', 'H', 'ETU', TO_DATE('2002-08-10', 'YYYY-MM-DD'), 'ndrake', 'pass15');
INSERT INTO UTILISATEUR VALUES (25, 'Ellie', 'Williams', 'ellie@etu.univ.fr', '0601010116', 'Boston', 'F', 'ETU', TO_DATE('2005-01-01', 'YYYY-MM-DD'), 'ewilliams', 'pass16');

-- 11. INSERTION DES ETUDIANTS
INSERT INTO ETUDIANT VALUES (10, 100, 0, 1, 0, 'GEN', 'TB', 'P_A', '3A', 10);
INSERT INTO ETUDIANT VALUES (11, 100, 0, 1, 0, 'GEN', 'B', 'P_A', '3A', 11);
INSERT INTO ETUDIANT VALUES (12, 100, 1, 0, 0, 'STI2D', 'AB', 'P_A', '3A', 12);

INSERT INTO ETUDIANT VALUES (13, NULL, 0, 0, 1, 'GEN', 'TB', 'P_B', '3B', 13);
INSERT INTO ETUDIANT VALUES (14, NULL, 0, 1, 1, 'GEN', 'B', 'P_B', '3B', 14);

INSERT INTO ETUDIANT VALUES (15, NULL, 0, 0, 0, 'STI2D', 'P', 'P_C', '3C', 15);
INSERT INTO ETUDIANT VALUES (16, 101, 0, 1, 0, 'GEN', 'B', 'P_C', '3C', 16);
INSERT INTO ETUDIANT VALUES (17, NULL, 0, 1, 0, 'GEN', 'TB', 'P_C', '3C', 17);
INSERT INTO ETUDIANT VALUES (18, 101, 1, 0, 0, 'PRO', 'AB', 'P_C', '3C', 18);

INSERT INTO ETUDIANT VALUES (19, NULL, 0, 0, 0, 'GEN', 'P', 'P_A', '3D', 19);
INSERT INTO ETUDIANT VALUES (20, NULL, 0, 0, 0, 'GEN', 'AB', 'P_A', '3D', 20);
INSERT INTO ETUDIANT VALUES (21, NULL, 0, 0, 0, 'STMG', 'AB', 'P_A', '3D', 21);
INSERT INTO ETUDIANT VALUES (22, NULL, 0, 1, 0, 'GEN', 'TB', 'P_B', '3B', 22);
INSERT INTO ETUDIANT VALUES (23, NULL, 0, 0, 0, 'GEN', 'B', 'P_B', '3B', 23);
INSERT INTO ETUDIANT VALUES (24, NULL, 0, 0, 0, 'STI2D', 'P', 'P_C', '3C', 24);
INSERT INTO ETUDIANT VALUES (25, NULL, 0, 1, 0, 'GEN', 'B', 'P_A', '3A', 25);

-- 12. INSERTION DES NOTES
-- R3.01 (Web), R3.07 (SQL), R3.14 (Anglais), R3.15 (Maths)
INSERT INTO NOTE VALUES (1, 15.5, 'Bon travail', 10, 'R3.07');
INSERT INTO NOTE VALUES (2, 18.0, 'Excellent', 10, 'R3.01');
INSERT INTO NOTE VALUES (3, 14.0, '', 10, 'R3.14');
INSERT INTO NOTE VALUES (4, 08.5, 'Attention', 12, 'R3.07');
INSERT INTO NOTE VALUES (5, 10.0, 'Juste', 12, 'R3.01');
INSERT INTO NOTE VALUES (6, 19.5, 'Parfait', 17, 'R3.07');
INSERT INTO NOTE VALUES (7, 16.0, '', 17, 'R3.15');
INSERT INTO NOTE VALUES (8, 12.0, '', 11, 'R3.07');
INSERT INTO NOTE VALUES (9, 11.5, '', 13, 'R3.01');
INSERT INTO NOTE VALUES (10, 13.0, '', 14, 'R3.02');
INSERT INTO NOTE VALUES (11, 09.0, 'A revoir', 15, 'R3.15');
INSERT INTO NOTE VALUES (12, 17.0, 'Very good', 16, 'R3.14');
INSERT INTO NOTE VALUES (13, 05.0, 'Absent', 19, 'R3.07');
INSERT INTO NOTE VALUES (14, 14.5, '', 20, 'R3.04');
-- Note sur le projet SAE
INSERT INTO NOTE VALUES (15, 16.0, 'Bonne soutenance', 10, 'S3.01');

-- 13. INSERTION DANS LES TABLES D'ASSOCIATION
-- Table ETUDIANT_REPONSE
INSERT INTO ETUDIANT_REPONSE VALUES (10, 10);
INSERT INTO ETUDIANT_REPONSE VALUES (11, 11);
INSERT INTO ETUDIANT_REPONSE VALUES (12, 20);

-- Table ENSEIGNANT_MATIERE
INSERT INTO ENSEIGNANT_MATIERE VALUES (1, 'R3.07');
INSERT INTO ENSEIGNANT_MATIERE VALUES (1, 'R3.01');
INSERT INTO ENSEIGNANT_MATIERE VALUES (3, 'R3.15');

-- Table MATIERE_GROUPE
INSERT INTO MATIERE_GROUPE VALUES ('3A', 'R3.07');
INSERT INTO MATIERE_GROUPE VALUES ('3A', 'R3.01');
INSERT INTO MATIERE_GROUPE VALUES ('3B', 'R3.04');
INSERT INTO MATIERE_GROUPE VALUES ('3C', 'R3.07');

COMMIT;