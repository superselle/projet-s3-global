<?php

require_once 'controller/ControleurBase.php';

class ControleurResponsableFormation extends ControleurBase {

    private function checkAuth() {
        return $this->requireLogin(['responsable_formation']);
    }

    private function resolveIdRoleFromKey($roleKey) {
        if ($roleKey === 'responsable_formation') return 'RESP_FORM';
        if ($roleKey === 'responsable_filiere') return 'RESP_PED';
        return 'ENS'; // Par défaut
    }

    private function mapRoleIdToKey($idRole) {
        if ($idRole === 'RESP_FORM') return 'responsable_formation';
        if ($idRole === 'RESP_PED') return 'responsable_filiere';
        return 'enseignant';
    }
    
    public function dashboard() {
        $this->requireLogin(['responsable_formation']);
        $userRole = 'responsable_formation';
        $showSidebar = true;
        // On récupère le nom pour l'affichage (si pas déjà en session)
        $userName = (isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : '') . ' ' . (isset($_SESSION['user_nom']) ? $_SESSION['user_nom'] : '');
        require_once 'view/commun/dashboard.php';
    }
    
    public function gestionEnseignants() {
        $this->requireLogin(['responsable_formation']);
        require_once 'model/Enseignant.php';
        $enseignants = Enseignant::getListeGestion();
        $this->render('view/commun/tableauGestion.php', [
            'pageTitle' => 'Gestion des enseignants',
            'currentPage' => 'gestionEnseignants',
            'showSidebar' => true,
            'titre' => 'Liste des enseignants enregistrés',
            'btnAjoutUrl' => 'index.php?controller=responsableFormation&action=ajouterEnseignant',
            'btnAjoutTexte' => 'Ajouter un enseignant',
            'items' => $enseignants,
            'msgVide' => 'Aucun enseignant trouvé.',
            'colonnes' => [
                ['type' => 'text', 'key' => 'nom', 'label' => 'Nom'],
                ['type' => 'text', 'key' => 'prenom', 'label' => 'Prénom'],
                ['type' => 'email', 'key' => 'email', 'label' => 'Email'],
                ['type' => 'badge', 'key' => 'libelle_role', 'label' => 'Rôle', 'badgeClass' => 'badge-secondary'],
                ['type' => 'actions', 'label' => 'Actions',
                    'modifier' => ['url' => 'index.php?controller=responsableFormation&action=modifierEnseignant&id=', 'param' => 'id_utilisateur'],
                    'supprimer' => ['url' => 'index.php?controller=responsableFormation&action=supprimerEnseignant', 'param' => 'id_utilisateur', 'confirm' => 'Êtes-vous sûr de vouloir supprimer cet enseignant ?']]
            ]
        ]);
    }
    
    // --- FACTORISATION : Action Ajouter ---
    public function ajouterEnseignant() {
        $this->requireLogin(['responsable_formation']);
        
        // On appelle la vue UNIQUE sans passer d'objet utilisateur -> Mode Création
        $this->render('view/responsableFormation/formulaireEnseignant.php', [
            'pageTitle' => 'Ajouter un enseignant',
            'currentPage' => 'gestionEnseignants',
            'showSidebar' => true,
            'roleKey' => '' // Valeur par défaut pour éviter les warnings
        ]);
    }
    
    // --- FACTORISATION : Action Modifier ---
    public function modifierEnseignant() {
        $this->requireLogin(['responsable_formation']);
        require_once 'model/Utilisateur.php';
        require_once 'model/Enseignant.php';

        $idUtilisateur = $_GET['id'] ?? '';
        if ($idUtilisateur === '') {
            header('Location: index.php?controller=responsableFormation&action=gestionEnseignants');
            exit;
        }

        $utilisateur = Utilisateur::getById($idUtilisateur);
        $enseignant = Enseignant::getByIdUtilisateur($idUtilisateur);
        
        if (!$utilisateur || !$enseignant) {
            header('Location: index.php?controller=responsableFormation&action=gestionEnseignants&error=notfound');
            exit;
        }

        $idRole = $enseignant->get('id_role');
        $roleKey = $this->mapRoleIdToKey($idRole);

        // On passe l'objet utilisateur ET enseignant (qui a les alias mappés)
        $this->render('view/responsableFormation/formulaireEnseignant.php', [
            'pageTitle' => 'Modifier un enseignant',
            'currentPage' => 'gestionEnseignants',
            'showSidebar' => true,
            'utilisateur' => $utilisateur,
            'enseignant' => $enseignant, // Ajout de l'enseignant avec les alias
            'roleKey' => $roleKey
        ]);
    }

 public function enregistrerEnseignant() {
        $this->requireLogin(['responsable_formation']);
        require_once 'model/Utilisateur.php';
        require_once 'model/Enseignant.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=responsableFormation&action=ajouterEnseignant');
            exit;
        }

        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $roleKey = $_POST['role'] ?? '';
        $motdepasse = $_POST['motdepasse'] ?? '';
        $motdepasseConfirm = $_POST['motdepasse_confirm'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($roleKey)) {
            header('Location: index.php?controller=responsableFormation&action=ajouterEnseignant&error=missing');
            exit;
        }

        if (empty($motdepasse) || $motdepasse !== $motdepasseConfirm) {
            header('Location: index.php?controller=responsableFormation&action=ajouterEnseignant&error=pwd');
            exit;
        }

        if (Utilisateur::getByEmail($email)) {
            header('Location: index.php?controller=responsableFormation&action=ajouterEnseignant&error=email');
            exit;
        }

        // --- CORRECTION 1 : Arguments positionnels ---
        // Signature : ($prenom, $nom, $email, $password, $statut, $tel)
        $idUtilisateur = Utilisateur::create(
            $prenom, 
            $nom, 
            $email, 
            $motdepasse, 
            'ENSEIGNANT', 
            !empty($telephone) ? $telephone : null
        );

        if (!$idUtilisateur) {
             header('Location: index.php?controller=responsableFormation&action=ajouterEnseignant&error=db');
             exit;
        }

        // Création du rôle enseignant
        $idRole = $this->resolveIdRoleFromKey($roleKey);
        $ok = Enseignant::createEnseignant($idUtilisateur, $idRole);
        
        if (!$ok) {
            Utilisateur::delete($idUtilisateur);
            header('Location: index.php?controller=responsableFormation&action=ajouterEnseignant&error=db');
            exit;
        }

        header('Location: index.php?controller=responsableFormation&action=gestionEnseignants&success=1');
        exit;
    }

public function mettreAJourEnseignant() {
        $this->requireLogin(['responsable_formation']);
        require_once 'model/Utilisateur.php';
        require_once 'model/Enseignant.php';

        $idUtilisateur = $_GET['id'] ?? '';
        if (empty($idUtilisateur)) { /* ... redirection erreur ... */ exit; }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { /* ... redirection ... */ exit; }

        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $roleKey = $_POST['role'] ?? '';
        $motdepasse = $_POST['motdepasse'] ?? '';
        $motdepasseConfirm = $_POST['motdepasse_confirm'] ?? '';

        // ... vérifications vide ...

        // Mise à jour simplifiée avec SEULEMENT les champs existants
        Utilisateur::update($idUtilisateur, [
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'tel' => !empty($telephone) ? $telephone : null
        ]);

        // Mise à jour mot de passe si demandé
        if (!empty($motdepasse)) {
            if ($motdepasse !== $motdepasseConfirm) {
                header('Location: index.php?controller=responsableFormation&action=modifierEnseignant&id=' . $idUtilisateur . '&error=pwd');
                exit;
            }
            Utilisateur::updatePassword($idUtilisateur, $motdepasse);
        }

        // Mise à jour Rôle
        $idRole = $this->resolveIdRoleFromKey($roleKey);
        Enseignant::updateRole($idUtilisateur, $idRole);

        header('Location: index.php?controller=responsableFormation&action=gestionEnseignants&updated=1');
        exit;
    }
    
    public function supprimerEnseignant() {
        $this->requireLogin(['responsable_formation']);
        require_once 'model/Utilisateur.php';
        require_once 'model/Enseignant.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=responsableFormation&action=gestionEnseignants');
            exit;
        }

        $idUtilisateur = $_POST['id'] ?? '';
        if ($idUtilisateur) {
            try {
                // Ordre de suppression important
                Enseignant::delete($idUtilisateur);
                Utilisateur::delete($idUtilisateur);
            } catch (Exception $e) {
                header('Location: index.php?controller=responsableFormation&action=gestionEnseignants&error=delete');
                exit;
            }
        }
        header('Location: index.php?controller=responsableFormation&action=gestionEnseignants&deleted=1');
        exit;
    }
    
    public function promotions() {
        $this->requireLogin(['responsable_formation']);
        header('Location: index.php?controller=promotions&action=promotions');
        exit;
    }
}
?>