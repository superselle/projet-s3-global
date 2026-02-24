<?php
require_once 'controller/ControleurBase.php';
require_once 'model/Enseignant.php';
require_once 'model/Etudiant.php';
require_once 'model/Groupe.php';
require_once 'model/Promotion.php';
require_once 'model/Utilisateur.php';
require_once 'model/Parcours.php';
require_once 'model/TypeBac.php';
require_once 'model/MentionBac.php';

// Chargement conditionnel des modèles optionnels
if (file_exists('model/Contrainte.php')) require_once 'model/Contrainte.php';
if (file_exists('model/Objectif.php')) require_once 'model/Objectif.php';

class ControleurResponsableFiliere extends ControleurBase {
        /**
         * Affiche la vue de répartition manuelle des groupes pour une promotion
         */
        public function repartitionManuelle() {
            $this->checkAuth();
            $idPromo = $_GET['id'] ?? '';
            if (!$idPromo) {
                header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes');
                exit;
            }

            $promotion = Promotion::getById($idPromo);
            if (!$promotion) {
                header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes');
                exit;
            }

            $groupes = Groupe::getByPromotion($promotion->get('id'));
            $semestre = $promotion->get('semestre');
            $studentsAll = Etudiant::getAllByPromoAndSemestre($promotion->get('annee_scolaire'), $promotion->get('id_parcours'), $semestre);
            $studentsByGroup = [];
            $nonAffectes = [];
            foreach ($studentsAll as $etu) {
                $idGroupe = $etu->get('id_groupe');
                if ($idGroupe) {
                    if (!isset($studentsByGroup[$idGroupe])) $studentsByGroup[$idGroupe] = [];
                    $studentsByGroup[$idGroupe][] = $etu;
                } else {
                    $nonAffectes[] = $etu;
                }
            }
            // Objectifs désactivés, simplification demandée
            $objectifs = [];

            $this->render('view/responsableFiliere/repartitionManuelle.php', [
                'promotion' => $promotion,
                'groupes' => $groupes,
                'studentsAll' => $studentsAll,
                'studentsByGroup' => $studentsByGroup,
                'nonAffectes' => $nonAffectes,
                'objectifs' => $objectifs
            ]);
        }
    
    const DEFAULT_SIZE = 18;

    /**
     * Vérifie les droits d'accès
     */
    private function checkAuth() {
        return $this->requireLogin(['responsable_filiere', 'responsable_formation']);
    }

    /**
     * Crée des groupes vides pour la répartition manuelle
     */
    public function creerGroupesManuels() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes');
            exit;
        }
        
        $idPromo = $_POST['id_promo'] ?? '';
        $nbGroupes = (int)($_POST['nb_groupes'] ?? 0);
        $tailleGroupe = (int)($_POST['taille_groupe'] ?? 18);
        $resetGroupes = isset($_POST['reset_groupes']);
        
        if (!$idPromo || $nbGroupes <= 0) {
            header('Location: index.php?controller=responsableFiliere&action=repartitionManuelle&id=' . urlencode($idPromo) . '&error=params');
            exit;
        }
        
        $promo = Promotion::getById($idPromo);
        if (!$promo) {
            header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes&error=promo');
            exit;
        }
        
        try {
            Connexion::pdo()->beginTransaction();
            
            $annee = $promo->get('annee_scolaire');
            $sem = $promo->get('semestre');
            $parc = $promo->get('id_parcours');
            
            // Si reset demandé, supprimer les anciens groupes
            if ($resetGroupes) {
                // D'abord récupérer les IDs des groupes à supprimer
                $sqlGetGroupes = "SELECT id_groupe FROM GROUPE WHERE annee_scolaire = ? AND semestre = ? AND id_parcours = ?";
                $stmtGet = Connexion::pdo()->prepare($sqlGetGroupes);
                $stmtGet->execute([$annee, $sem, $parc]);
                $groupeIds = $stmtGet->fetchAll(PDO::FETCH_COLUMN);
                
                if (!empty($groupeIds)) {
                    // Désaffecter les étudiants de ces groupes spécifiques
                    $placeholders = implode(',', array_fill(0, count($groupeIds), '?'));
                    $sqlReset = "UPDATE ETUDIANT SET id_groupe = NULL WHERE id_groupe IN ($placeholders)";
                    Connexion::pdo()->prepare($sqlReset)->execute($groupeIds);
                    
                    // Supprimer les groupes
                    $sqlDel = "DELETE FROM GROUPE WHERE id_groupe IN ($placeholders)";
                    Connexion::pdo()->prepare($sqlDel)->execute($groupeIds);
                }
            }
            
            // Compter les groupes existants pour déterminer la prochaine lettre
            $sqlCount = "SELECT COUNT(*) FROM GROUPE WHERE annee_scolaire = ? AND semestre = ? AND id_parcours = ?";
            $stmtCount = Connexion::pdo()->prepare($sqlCount);
            $stmtCount->execute([$annee, $sem, $parc]);
            $existingCount = (int)$stmtCount->fetchColumn();
            
            // Créer les nouveaux groupes (avec colonne lettre NOT NULL)
            $sqlInsert = "INSERT INTO GROUPE (lettre, nom_groupe, annee_scolaire, semestre, id_parcours, effectif_max) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = Connexion::pdo()->prepare($sqlInsert);
            
            for ($i = 1; $i <= $nbGroupes; $i++) {
                $lettre = chr(64 + $existingCount + $i); // A, B, C, ... (en tenant compte des existants)
                $nomGroupe = "Groupe " . $lettre;
                $stmt->execute([$lettre, $nomGroupe, $annee, $sem, $parc, $tailleGroupe]);
            }
            
            Connexion::pdo()->commit();
            header('Location: index.php?controller=responsableFiliere&action=repartitionManuelle&id=' . urlencode($idPromo) . '&success=created');
            exit;
            
        } catch (Exception $e) {
            Connexion::pdo()->rollBack();
            header('Location: index.php?controller=responsableFiliere&action=repartitionManuelle&id=' . urlencode($idPromo) . '&error=db');
            exit;
        }
    }
    
    /**
     * Affecte un étudiant à un groupe (répartition manuelle)
     */
    public function affecterEtudiant() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes');
            exit;
        }
        
        $idPromo = $_POST['id_promo'] ?? '';
        $idEtudiant = (int)($_POST['id_etudiant'] ?? 0);
        $idGroupe = (int)($_POST['id_groupe'] ?? 0);
        
        if (!$idPromo || $idEtudiant <= 0) {
            header('Location: index.php?controller=responsableFiliere&action=repartitionManuelle&id=' . urlencode($idPromo) . '&error=params');
            exit;
        }
        
        try {
            // Si id_groupe = 0, désaffecter l'étudiant
            $newGroupe = $idGroupe > 0 ? $idGroupe : null;
            
            $sql = "UPDATE ETUDIANT SET id_groupe = ? WHERE id_etudiant = ?";
            Connexion::pdo()->prepare($sql)->execute([$newGroupe, $idEtudiant]);
            
            header('Location: index.php?controller=responsableFiliere&action=repartitionManuelle&id=' . urlencode($idPromo) . '&success=affected');
            exit;
            
        } catch (Exception $e) {
            header('Location: index.php?controller=responsableFiliere&action=repartitionManuelle&id=' . urlencode($idPromo) . '&error=db');
            exit;
        }
    }
    
    /**
     * Valide et enregistre la constitution des groupes après répartition automatique
     */
    public function validerGroupes() {
        $this->checkAuth();
        
        $idPromo = $_GET['id'] ?? '';
        
        if (!$idPromo) {
            header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes&error=nopromo');
            exit;
        }
        
        // La répartition est déjà enregistrée par genererGroupes()
        // Cette méthode confirme simplement et redirige
        header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes&id=' . urlencode($idPromo) . '&success=validated');
        exit;
    }

    public function dashboard() {
        $session = $this->checkAuth();
        
        // On récupère l'enseignant lié
        $enseignant = Enseignant::getByIdUtilisateur($session['userId']);
        
        $this->render('view/commun/dashboard.php', [
            'pageTitle' => 'Tableau de bord',
            'currentPage' => 'dashboard',
            'enseignant' => $enseignant
        ]);
    }

    // --- GESTION ÉTUDIANTS (CRUD) ---
    
    public function gestionEtudiants() {
        $this->checkAuth();
        $etudiants = Etudiant::getAll(); 
        $this->render('view/commun/tableauGestion.php', [
            'pageTitle' => 'Gestion Étudiants',
            'currentPage' => 'gestionEtudiants',
            'titre' => 'Gestion des étudiants',
            'btnAjoutUrl' => 'index.php?controller=responsableFiliere&action=ajouterEtudiant',
            'btnAjoutTexte' => 'Ajouter un étudiant',
            'items' => $etudiants,
            'msgVide' => 'Aucun étudiant trouvé.',
            'colonnes' => [
                ['type' => 'text', 'key' => 'nom', 'label' => 'Nom', 'class' => 'fw-bold'],
                ['type' => 'text', 'key' => 'prenom', 'label' => 'Prénom'],
                ['type' => 'email', 'key' => 'email', 'label' => 'Email'],
                    ['type' => 'bool', 'key' => 'est_redoublant', 'label' => 'Redoublant', 'class' => 'text-center', 'badgeYes' => 'badge-warning text-dark', 'badgeNo' => 'badge-light text-muted'],
                    ['type' => 'bool', 'key' => 'est_anglophone', 'label' => 'Anglophone', 'class' => 'text-center', 'badgeYes' => 'badge-info text-dark', 'badgeNo' => 'badge-light text-muted'],
                    ['type' => 'bool', 'key' => 'est_apprenti', 'label' => 'Apprenti', 'class' => 'text-center', 'badgeYes' => 'badge-primary', 'badgeNo' => 'badge-light text-muted'],
                ['type' => 'badge', 'key' => 'nom_groupe', 'label' => 'Groupe', 'class' => 'text-center', 'badgeClass' => 'badge-primary'],
                ['type' => 'actions', 'label' => 'Actions', 'class' => 'text-center', 'style' => 'width:150px', 
                    'modifier' => ['url' => 'index.php?controller=responsableFiliere&action=modifierEtudiant&id=', 'param' => 'id_etudiant'],
                    'supprimer' => ['url' => 'index.php?controller=responsableFiliere&action=supprimerEtudiant', 'param' => 'id_etudiant', 'confirm' => 'Êtes-vous sûr de vouloir supprimer cet étudiant ?']]
            ]
        ]);
    }

    public function ajouterEtudiant() {
        $this->checkAuth();
        
        // Affichage du formulaire (GET uniquement)
        $this->render('view/responsableFiliere/formulaireEtudiant.php', [
            'pageTitle' => 'Ajout Étudiant',
            'currentPage' => 'gestionEtudiants',
            'parcours' => Parcours::getAll(),
            'typesBac' => TypeBac::getAll(),
            'mentions' => MentionBac::getAll()
        ]);
    }
    
    public function enregistrerEtudiant() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=responsableFiliere&action=ajouterEtudiant');
            exit;
        }
        
        // Validation des champs obligatoires
        if (empty($_POST['prenom']) || empty($_POST['nom']) || empty($_POST['email']) || empty($_POST['password'])) {
            header('Location: index.php?controller=responsableFiliere&action=ajouterEtudiant&error=missing');
            exit;
        }
        
        try {
            Connexion::pdo()->beginTransaction();
            
            // Création de l'utilisateur
            $idUser = Utilisateur::create(
                $_POST['prenom'], 
                $_POST['nom'], 
                $_POST['email'], 
                $_POST['password'], 
                'ETUDIANT', 
                null
            );
            
            // Gestion des valeurs NULL pour les clés étrangères optionnelles
            $idParcours = !empty($_POST['id_parcours']) ? $_POST['id_parcours'] : null;
            $idType = !empty($_POST['id_type']) ? $_POST['id_type'] : null;
            $idMention = !empty($_POST['id_mention']) ? $_POST['id_mention'] : null;
            
            // Création de l'étudiant
            $sql = "INSERT INTO ETUDIANT (id_utilisateur, id_parcours, id_type, id_mention, semestre, est_redoublant, est_anglophone, est_apprenti) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            Connexion::pdo()->prepare($sql)->execute([
                $idUser, 
                $idParcours, 
                $idType, 
                $idMention,
                intval($_POST['semestre'] ?? 1),
                isset($_POST['est_redoublant']) ? 1 : 0, 
                isset($_POST['est_anglophone']) ? 1 : 0, 
                isset($_POST['est_apprenti']) ? 1 : 0
            ]);
            
            Connexion::pdo()->commit();
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&success=created'); 
            exit;

        } catch (Exception $e) { 
            if (Connexion::pdo()->inTransaction()) {
                Connexion::pdo()->rollBack();
            }
            error_log("Erreur création étudiant : " . $e->getMessage());
            header('Location: index.php?controller=responsableFiliere&action=ajouterEtudiant&error=db&msg=' . urlencode($e->getMessage())); 
            exit;
        }
    }
    
public function modifierEtudiant() {
        $this->checkAuth();
        
        $idEtudiant = $_GET['id'] ?? null;
        if (!$idEtudiant) {
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants'); 
            exit;
        }

        // Récupération de l'étudiant actuel
        $etudiant = Etudiant::getById($idEtudiant);
        if (!$etudiant) {
             header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&error=notfound'); 
             exit;
        }

        // Affichage du formulaire (GET uniquement)
        $this->render('view/responsableFiliere/formulaireEtudiant.php', [
            'pageTitle' => 'Modifier Étudiant',
            'currentPage' => 'gestionEtudiants',
            'etudiant' => $etudiant,
            'parcours' => Parcours::getAll(),
            'typesBac' => TypeBac::getAll(),
            'mentions' => MentionBac::getAll()
        ]);
    }
    
    public function mettreAJourEtudiant() {
        $this->checkAuth();
        
        $idEtudiant = $_GET['id'] ?? null;
        if (!$idEtudiant) {
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&error=missingid');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=responsableFiliere&action=modifierEtudiant&id=' . $idEtudiant);
            exit;
        }
        
        $etudiant = Etudiant::getById($idEtudiant);
        if (!$etudiant) {
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&error=notfound'); 
            exit;
        }
        
        try {
            Connexion::pdo()->beginTransaction();

            $idUtilisateur = $etudiant->get('id_utilisateur');
            $login = empty($_POST['login']) ? null : substr($_POST['login'], 0, 50);

            // Mise à jour table UTILISATEUR
            $sqlUser = "UPDATE UTILISATEUR SET nom_utilisateur = ?, prenom_utilisateur = ?, mail_utilisateur = ?, login_utilisateur = ? WHERE id_utilisateur = ?";
            $paramsUser = [
                $_POST['nom'], 
                $_POST['prenom'], 
                $_POST['email'], 
                $login, 
                $idUtilisateur
            ];
            Connexion::pdo()->prepare($sqlUser)->execute($paramsUser);

            // Gestion Mot de passe
            if (!empty($_POST['password'])) {
                $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sqlMdp = "UPDATE UTILISATEUR SET mdp_hash_utilisateur = ? WHERE id_utilisateur = ?";
                Connexion::pdo()->prepare($sqlMdp)->execute([$hash, $idUtilisateur]);
            }

            // Gestion des valeurs NULL pour les clés étrangères optionnelles
            $idParcours = !empty($_POST['id_parcours']) ? $_POST['id_parcours'] : null;
            $idType = !empty($_POST['id_type']) ? $_POST['id_type'] : null;
            $idMention = !empty($_POST['id_mention']) ? $_POST['id_mention'] : null;

            // Mise à jour table ETUDIANT
            $sqlEtu = "UPDATE ETUDIANT SET id_parcours=?, id_type=?, id_mention=?, semestre=?, est_redoublant=?, est_anglophone=?, est_apprenti=? WHERE id_etudiant=?";
            Connexion::pdo()->prepare($sqlEtu)->execute([
                $idParcours,
                $idType,
                $idMention,
                intval($_POST['semestre'] ?? 1),
                isset($_POST['est_redoublant']) ? 1 : 0,
                isset($_POST['est_anglophone']) ? 1 : 0,
                isset($_POST['est_apprenti']) ? 1 : 0,
                $idEtudiant
            ]);

            Connexion::pdo()->commit();
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&success=updated');
            exit;

        } catch (Exception $e) {
            if (Connexion::pdo()->inTransaction()) {
                Connexion::pdo()->rollBack();
            }
            error_log("Erreur modification étudiant : " . $e->getMessage());
            header('Location: index.php?controller=responsableFiliere&action=modifierEtudiant&id=' . $idEtudiant . '&error=db&msg=' . urlencode($e->getMessage()));
            exit;
        }
    }

public function supprimerEtudiant() {
        $this->checkAuth();
        
        if (!isset($_POST['id'])) {
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&error=noid');
            exit;
        }
        
        $idEtudiant = $_POST['id'];
        $etu = Etudiant::getById($idEtudiant);
        
        if (!$etu) {
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&error=notfound');
            exit;
        }
        
        try {
            Connexion::pdo()->beginTransaction();
            
            $idUtilisateur = $etu->get('id_utilisateur');
            
            // 1. Supprimer les réponses aux sondages (ETUDIANT_REPONSE)
            $sql = "DELETE FROM ETUDIANT_REPONSE WHERE id_etudiant = ?";
            Connexion::pdo()->prepare($sql)->execute([$idEtudiant]);
            
            // 2. Supprimer les notes
            $sql = "DELETE FROM NOTE WHERE id_etudiant = ?";
            Connexion::pdo()->prepare($sql)->execute([$idEtudiant]);
            
            // 3. Supprimer l'étudiant
            $sql = "DELETE FROM ETUDIANT WHERE id_etudiant = ?";
            Connexion::pdo()->prepare($sql)->execute([$idEtudiant]);
            
            // 4. Supprimer l'utilisateur
            $sql = "DELETE FROM UTILISATEUR WHERE id_utilisateur = ?";
            Connexion::pdo()->prepare($sql)->execute([$idUtilisateur]);
            
            Connexion::pdo()->commit();
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&success=deleted');
            exit;
            
        } catch (Exception $e) {
            if (Connexion::pdo()->inTransaction()) {
                Connexion::pdo()->rollBack();
            }
            error_log("Erreur suppression étudiant : " . $e->getMessage());
            header('Location: index.php?controller=responsableFiliere&action=gestionEtudiants&error=delete&msg=' . urlencode($e->getMessage()));
            exit;
        }
    }

    // --- ALGORITHME & GROUPES ---

    public function constitutionGroupes() {
        $this->checkAuth();
        $idPromo = $_GET['id'] ?? '';
        $promo = $idPromo ? Promotion::getById($idPromo) : null;
        $promotions = Promotion::getAll();

        $nbEtudiants = 0;
        $nbGroupes = 0;
        if ($promo) {
            $etudiants = Etudiant::getAllByPromo($promo->get('annee_scolaire'), $promo->get('id_parcours'), $promo->get('semestre'));
            $nbEtudiants = count($etudiants);
            $nbGroupes = ($nbEtudiants > 0) ? ceil($nbEtudiants / self::DEFAULT_SIZE) : 0;
        }
        $this->render('view/responsableFiliere/constitutionGroupes.php', [
            'pageTitle' => 'Constitution Groupes',
            'currentPage' => 'constitutionGroupes',
            'promo' => $promo,
            'promotions' => $promotions,
            'nbEtudiants' => $nbEtudiants,
            'nbGroupes' => $nbGroupes
        ]);
    }

    /**
     * Cœur du système : Génération aléatoire des groupes
     * ATTENTION : Utilise des requêtes SQL directes pour la performance/transaction
     */
    public function genererGroupes() {
        $this->checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $idPromo = $_POST['id_promo'];
        $promo = Promotion::getById($idPromo);
        
        if (!$promo) die("Erreur : Promotion introuvable.");

        // Paramètres
        $nbGroupes = (int)($_POST['nb_groupes'] ?: 4);
        
        // 1. Récupération des étudiants via le modèle (Objets)
        $students = Etudiant::getAllByPromo($promo->get('annee_scolaire'), $promo->get('id_parcours'), $promo->get('semestre'));

        // 2. Algorithme de répartition amélioré avec prise en compte des covoiturages
        $groupes = array_fill(0, $nbGroupes, []);
        $studentsPlaces = []; // IDs étudiants déjà placés
        
        // PHASE 1 : Placer les groupes de covoiturage (choix réciproques prioritaires)
        foreach ($students as $etu) {
            $idEtu = $etu->get('id_etudiant');
            
            // Si déjà placé, passer
            if (in_array($idEtu, $studentsPlaces)) continue;
            
            // Récupérer l'état des binômes
            $etatBinome = Etudiant::getEtatBinome($idEtu);
            
            // Si au moins 1 choix réciproque, créer un mini-groupe
            if (!empty($etatBinome['reciproques'])) {
                $miniGroupe = [$etu];
                $studentsPlaces[] = $idEtu;
                
                // Ajouter les choix réciproques au mini-groupe
                foreach ($etatBinome['reciproques'] as $reciproque) {
                    $idRecip = $reciproque->get('id_etudiant');
                    if (!in_array($idRecip, $studentsPlaces)) {
                        $miniGroupe[] = $reciproque;
                        $studentsPlaces[] = $idRecip;
                    }
                }
                
                // Placer ce mini-groupe dans le groupe le moins rempli
                $groupeMin = 0;
                $tailleMin = count($groupes[0]);
                for ($g = 1; $g < $nbGroupes; $g++) {
                    if (count($groupes[$g]) < $tailleMin) {
                        $tailleMin = count($groupes[$g]);
                        $groupeMin = $g;
                    }
                }
                
                foreach ($miniGroupe as $membre) {
                    $groupes[$groupeMin][] = $membre;
                }
            }
        }
        
        // PHASE 2 : Distribution homogène des étudiants restants
        // Séparation par genre pour équilibrage
        $hommesRestants = [];
        $femmesRestants = [];
        
        foreach ($students as $etu) {
            if (in_array($etu->get('id_etudiant'), $studentsPlaces)) continue;
            
            $genre = $etu->get('genre_utilisateur');
            if ($genre === 'Homme' || $genre === 'M') {
                $hommesRestants[] = $etu;
            } else {
                $femmesRestants[] = $etu;
            }
        }
        
        // Mélanger pour randomiser
        shuffle($hommesRestants);
        shuffle($femmesRestantes);
        
        // Alterner hommes et femmes pour équilibrage
        $restants = [];
        $maxLen = max(count($hommesRestants), count($femmesRestantes));
        for ($i = 0; $i < $maxLen; $i++) {
            if (isset($hommesRestants[$i])) $restants[] = $hommesRestants[$i];
            if (isset($femmesRestantes[$i])) $restants[] = $femmesRestantes[$i];
        }
        
        // Distribution dans les groupes (round-robin)
        foreach ($restants as $etu) {
            // Trouver le groupe le moins rempli
            $groupeMin = 0;
            $tailleMin = count($groupes[0]);
            for ($g = 1; $g < $nbGroupes; $g++) {
                if (count($groupes[$g]) < $tailleMin) {
                    $tailleMin = count($groupes[$g]);
                    $groupeMin = $g;
                }
            }
            $groupes[$groupeMin][] = $etu;
        }

        // 3. Sauvegarde en Base de Données
        try {
            Connexion::pdo()->beginTransaction();
            
            // Extraction des valeurs scalaires via les getters pour le SQL
            $annee = $promo->get('annee_scolaire');
            $sem   = $promo->get('semestre');
            $parc  = $promo->get('id_parcours');
            
            // A. Réinitialisation des étudiants de cette promo (plus de groupe) - EN MASSE
            $sqlResetEtu = "UPDATE ETUDIANT SET id_groupe = NULL 
                           WHERE id_parcours = ? AND semestre = ?";
            Connexion::pdo()->prepare($sqlResetEtu)->execute([$parc, $sem]);
            
            // B. Suppression des anciens groupes de cette promo
            $sqlDel = "DELETE FROM GROUPE WHERE annee_scolaire = ? AND semestre = ? AND id_parcours = ?";
            Connexion::pdo()->prepare($sqlDel)->execute([$annee, $sem, $parc]);

            // C. Création des nouveaux groupes et affectation
            $stmtGrp = Connexion::pdo()->prepare("INSERT INTO GROUPE (nom_groupe, annee_scolaire, semestre, id_parcours) VALUES (?, ?, ?, ?)");
            $stmtEtu = Connexion::pdo()->prepare("UPDATE ETUDIANT SET id_groupe = ? WHERE id_etudiant = ?");

            foreach ($groupes as $idx => $membres) {
                // Nom du groupe : A, B, C...
                $nomGroupe = "Groupe " . chr(65 + $idx); 
                
                $stmtGrp->execute([$nomGroupe, $annee, $sem, $parc]);
                $idGroupe = Connexion::pdo()->lastInsertId();

                foreach ($membres as $etu) {
                    $stmtEtu->execute([$idGroupe, $etu->get('id_etudiant')]);
                }
            }
            
            Connexion::pdo()->commit();
            
            // Redirection vers le résultat avec message de succès
            header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes&id=' . urlencode($idPromo) . '&success=generated');
            exit;

        } catch (Exception $e) {
            if (Connexion::pdo()->inTransaction()) {
                Connexion::pdo()->rollBack();
            }
            error_log("Erreur lors de la sauvegarde des groupes : " . $e->getMessage());
            error_log("Stack trace : " . $e->getTraceAsString());
            header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes&id=' . urlencode($idPromo) . '&error=save&msg=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function resultatRepartition() {
        $this->checkAuth();
        
        // On pourrait récupérer les infos ici pour les passer à la vue
        $idPromo = $_GET['id'] ?? '';
        
        $this->render('view/responsableFiliere/resultatRepartition.php', [
            'pageTitle' => 'Résultat Répartition',
            'currentPage' => 'constitutionGroupes',
            'idPromo' => $idPromo
        ]);
    }

    public function configContraintes() {
        $this->checkAuth();
        
        $idPromo = $_GET['id'] ?? '';
        $promo = $idPromo ? Promotion::getById($idPromo) : null;
        
        if (!$promo) {
            header('Location: index.php?controller=promotions&action=promotions');
            exit;
        }

        // Récupération des données pour la vue
        $groupes = Groupe::getByPromotion($promo->get('id')); // Utilisation du getter ID composite
        $etudiants = Etudiant::getAllByPromo($promo->get('annee_scolaire'), $promo->get('id_parcours'), $promo->get('semestre'));
        
        $contraintes = class_exists('Contrainte') ? Contrainte::listByPromotion($idPromo) : [];
            // Objectifs désactivés, simplification demandée
            $objectifs = [];

        $this->render('view/responsableFiliere/configContraintes.php', [
            'pageTitle' => 'Configuration des Contraintes',
            'currentPage' => 'constitutionGroupes',
            'promo' => $promo,
            'groupes' => $groupes,
            'etudiants' => $etudiants,
            'contraintes' => $contraintes,
            'objectifs' => $objectifs
        ]);
    }
    
    /**
     * Affiche le contrôle qualité des groupes d'une promotion
     */
    public function controleQualite() {
        $this->checkAuth();
        
        $promoId = $_GET['id'] ?? '';
        
        if (empty($promoId)) {
            header('Location: index.php?controller=responsableFiliere&action=constitutionGroupes');
            exit;
        }
        
        // Récupérer les statistiques de tous les groupes
        $statsPromo = Groupe::getStatistiquesPromotion($promoId);
        
        $this->render('view/responsableFiliere/controleQualiteGroupes.php', [
            'pageTitle' => 'Contrôle Qualité des Groupes',
            'currentPage' => 'constitutionGroupes',
            'statsPromo' => $statsPromo,
            'promoId' => $promoId
        ]);
    }
}
?>