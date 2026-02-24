SET SERVEROUTPUT ON;

/* ==============
    LES FONCTIONS 
    ============= */
 
/* 
    Calculer la moyenne générale d'un étudiant
    Objectif : Servira pour l'algorithme de répartition par niveau académique
    Retourne : La moyenne sur 20 ou -1 si aucune note
*/
CREATE OR REPLACE FUNCTION F_CALCUL_MOYENNE(p_id_etudiant IN NUMBER) 
RETURN NUMBER 
IS
     v_moyenne NUMBER(4,2);
     v_count NUMBER;
BEGIN
     -- Vérifier si l'étudiant a des notes
     SELECT COUNT(*) INTO v_count FROM NOTE WHERE id_etudiant = p_id_etudiant;
     
     IF v_count = 0 THEN
          RETURN -1; -- "Pas de note"
     ELSE
          SELECT AVG(valeur_note) INTO v_moyenne 
          FROM NOTE 
          WHERE id_etudiant = p_id_etudiant;
          RETURN ROUND(v_moyenne, 2);
     END IF;
END F_CALCUL_MOYENNE;
/

/*
    Vérifier si un groupe a atteint sa capacité maximale
    Objectif : Empêcher la surcharge des groupes lors de la répartition
    Retourne : 1 vrai donc plein ou 0 faux donc place dispo
*/
CREATE OR REPLACE FUNCTION F_EST_GROUPE_COMPLET(p_id_groupe IN VARCHAR2) 
RETURN NUMBER 
IS
     v_nb_inscrits NUMBER;
     v_capacite NUMBER;
BEGIN
     SELECT effectif INTO v_capacite FROM GROUPE WHERE id_groupe = p_id_groupe;
     
     SELECT COUNT(*) INTO v_nb_inscrits FROM ETUDIANT WHERE id_groupe = p_id_groupe;
     
     IF v_nb_inscrits >= v_capacite THEN
          RETURN 1; -- groupe plein
     ELSE
          RETURN 0; -- il reste de la place
     END IF;
EXCEPTION
     WHEN NO_DATA_FOUND THEN
          RETURN 1; -- bloque par securite si le groupe n'existe pas
END F_EST_GROUPE_COMPLET;
/

/* 
    Calculer le taux de féminisation d'un groupe
    Objectif : Aider à respecter la contrainte de mixité
    Retourne : Le pourcentage de femmes dans le groupe
*/
CREATE OR REPLACE FUNCTION F_TAUX_FEMMES_GROUPE(p_id_groupe IN VARCHAR2) 
RETURN NUMBER 
IS
     v_total NUMBER;
     v_nb_femmes NUMBER;
BEGIN
     SELECT COUNT(*) INTO v_total FROM ETUDIANT WHERE id_groupe = p_id_groupe;
     
     IF v_total = 0 THEN
          RETURN 0;
     END IF;
     
     SELECT COUNT(*) INTO v_nb_femmes 
     FROM ETUDIANT e
     JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur
     WHERE e.id_groupe = p_id_groupe AND u.genre_utilisateur = 'F';
     
     RETURN ROUND((v_nb_femmes / v_total) * 100, 2);
END F_TAUX_FEMMES_GROUPE;
/


/* ===============
    LES PROCEDURES
    ============== */

/* 
    Affectation manuelle sécurisée
    Objectif : Ajouter un étudiant à un groupe en vérifiant les contraintes
    Lève une erreur si l'action est impossible
*/
CREATE OR REPLACE PROCEDURE P_AFFECTER_ETUDIANT(
     p_id_etudiant IN NUMBER, 
     p_id_groupe IN VARCHAR2
) 
IS
     v_est_complet NUMBER;
BEGIN
     v_est_complet := F_EST_GROUPE_COMPLET(p_id_groupe);
     
     IF v_est_complet = 1 THEN
          RAISE_APPLICATION_ERROR(-20001, 'Erreur : Le groupe ' || p_id_groupe || ' est complet.');
     END IF;
     
     UPDATE ETUDIANT 
     SET id_groupe = p_id_groupe 
     WHERE id_etudiant = p_id_etudiant;
     
     IF SQL%ROWCOUNT = 0 THEN
          RAISE_APPLICATION_ERROR(-20002, 'Erreur : Étudiant introuvable.');
     END IF;
     
     COMMIT;
     DBMS_OUTPUT.PUT_LINE('Succès : Étudiant ' || p_id_etudiant || ' affecté au groupe ' || p_id_groupe);
END P_AFFECTER_ETUDIANT;
/

/*
    Réinitialisation des groupes d'un parcours
    Objectif : Permet de remettre à zéro la répartition avant de relancer un algo automatique
    Correspond au besoin d'ajustements fréquents
*/
CREATE OR REPLACE PROCEDURE P_RESET_GROUPES_PARCOURS(p_id_parcours IN VARCHAR2) 
IS
BEGIN
     UPDATE ETUDIANT 
     SET id_groupe = NULL 
     WHERE id_parcours = p_id_parcours;
     
     COMMIT;
     DBMS_OUTPUT.PUT_LINE('Les groupes du parcours ' || p_id_parcours || ' ont été réinitialisés.');
END P_RESET_GROUPES_PARCOURS;
/

/*
    Importation unitaire de note (Simulation d'un imporrt CSV)
    Objectif : Facilite l'insertion des notes importées depuis un fichier
    Gère la création automatique si la note n'existe pas
*/

-- Pré-requis : 
CREATE SEQUENCE SEQ_ID_NOTE
START WITH 1
INCREMENT BY 1;

CREATE OR REPLACE PROCEDURE P_AJOUTER_NOTE(
     p_id_etudiant IN NUMBER,
     p_id_matiere IN VARCHAR2,
     p_valeur IN NUMBER,
     p_commentaire IN VARCHAR2
)
IS
BEGIN
     INSERT INTO NOTE (id_note, valeur_note, commentaire_note, id_etudiant, id_matiere)
     VALUES (SEQ_ID_NOTE.NEXTVAL, p_valeur, p_commentaire, p_id_etudiant, p_id_matiere); 
     -- Note: Suppose l'existence d'une séquence SEQ_ID_NOTE
     
     COMMIT;
EXCEPTION
     WHEN DUP_VAL_ON_INDEX THEN
          DBMS_OUTPUT.PUT_LINE('Erreur : Une note existe déjà pour cette matière/étudiant.');
END P_AJOUTER_NOTE;
/


/* ==============
    LES TRIGGERS 
    ============= */

/*
    Formatage automatique des données utilisateur
    Objectif : Garantir la propreté des données (Nom en maj, email en minuscule) à l'inscription
    Type : BEFORE INSERT OR UPDATE
*/
CREATE OR REPLACE TRIGGER TRG_FORMAT_UTILISATEUR
BEFORE INSERT OR UPDATE ON UTILISATEUR
FOR EACH ROW
BEGIN
     :NEW.nom_utilisateur := UPPER(:NEW.nom_utilisateur);
     :NEW.prenom_utilisateur := INITCAP(:NEW.prenom_utilisateur);
     :NEW.mail_utilisateur := LOWER(:NEW.mail_utilisateur);
END;
/

/* 
    Vérification de cohérence Parcours / Groupe
    Objectif : Un étudiant inscrit en parcours A ne doit pas être mis dans un groupe du parcours B
    Type : BEFORE UPDATE
*/
CREATE OR REPLACE TRIGGER TRG_CHECK_COHERENCE_PARCOURS
BEFORE UPDATE OF id_groupe ON ETUDIANT
FOR EACH ROW
WHEN (NEW.id_groupe IS NOT NULL) -- que si on affecte un groupe
DECLARE
     v_parcours_groupe VARCHAR2(10);
BEGIN
     SELECT id_parcours INTO v_parcours_groupe 
     FROM GROUPE 
     WHERE id_groupe = :NEW.id_groupe;
     
     IF v_parcours_groupe != :NEW.id_parcours THEN
          RAISE_APPLICATION_ERROR(-20003, 
                'Incohérence : Ce groupe appartient au parcours ' || v_parcours_groupe || 
                ' alors que l''étudiant est inscrit en ' || :NEW.id_parcours);
     END IF;
END;
/

/*
    Audit des modifications de notes
    Objectif : Garder une trace si un enseignant modifie une note
    Nécessite une table TABLE_LOG_NOTE
*/
-- Pré-requis : 
-- Création de la table LOG
CREATE TABLE LOG_MODIF_NOTE (
    id_log NUMBER(10),
    ancien_valeur NUMBER(4,2),
    nouvelle_valeur NUMBER(4,2),
    date_modif DATE DEFAULT SYSDATE,
    user_modif VARCHAR2(50),
    id_note_concernee NUMBER(10),
    PRIMARY KEY(id_log)
);

-- Création de la séquence pour générer les ID automatiquemnt 
CREATE SEQUENCE SEQ_LOG_NOTE
START WITH 1
INCREMENT BY 1;

CREATE OR REPLACE TRIGGER TRG_AUDIT_NOTE
AFTER UPDATE OF valeur_note ON NOTE
FOR EACH ROW
BEGIN
     INSERT INTO LOG_MODIF_NOTE (id_log, ancien_valeur, nouvelle_valeur, date_modif, user_modif)
     VALUES (
          SEQ_LOG_NOTE.NEXTVAL,
          :OLD.valeur_note,
          :NEW.valeur_note,
          SYSDATE,
          rdahma1 -- L'utilisateur Oracle qui fait la modif
     );
END;
/