<?php
require_once 'model/Utilisateur.php';
require_once 'model/Etudiant.php';
require_once 'model/Enseignant.php';

class ControleurAuth {

    public function connexion() {
        require_once 'view/auth/connexion.php';
    }

    public function traiterConnexion() {
        $login = $_POST['identifiant'] ?? '';
        $mdp = $_POST['motdepasse'] ?? '';

        if (!$login || !$mdp) $this->redirectError("Identifiants manquants.");

        $user = Utilisateur::getByLogin($login);
        if (!$user || !password_verify($mdp, $user->get('mdp_hash_utilisateur'))) {
            $this->redirectError("Identifiants incorrects.");
        }

        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $_SESSION['is_connected'] = true;
        $_SESSION['user_id'] = $user->get('id_utilisateur');
        $_SESSION['user_login'] = $user->get('login_utilisateur');
        $_SESSION['user_prenom'] = $_SESSION['user_firstname'] = $user->get('prenom_utilisateur');
        $_SESSION['user_nom'] = $_SESSION['user_lastname'] = $user->get('nom_utilisateur');

        $role = '';
        $redirect = '';

        if ($ens = Enseignant::getByIdUtilisateur($user->get('id_utilisateur'))) {
            $roleId = $ens->get('id_role');
            
            // Mapping des rôles vers les contrôleurs
            if (in_array($roleId, ['RESP_FIL', 'RESP_PED', '2'])) {
                $role = 'responsable_filiere';
                $redirect = 'responsableFiliere';
            } elseif (in_array($roleId, ['RESP_FORM', '1'])) {
                $role = 'responsable_formation';
                $redirect = 'responsableFiliere'; 
            } else {
                $role = 'enseignant';
                $redirect = 'enseignant';
            }
        } 
        // Test Etudiant
        elseif ($etu = Etudiant::getByIdUtilisateur($user->get('id_utilisateur'))) {
            $role = 'etudiant';
            $redirect = 'etudiant';
            $_SESSION['id_etudiant'] = $etu->get('id_etudiant');
        }

        if (!$role) {
            session_destroy();
            $this->redirectError("Compte valide mais aucun rôle associé.");
        }

        $_SESSION['role'] = $_SESSION['user_role'] = $role; 

        header('Location: index.php?controller=' . $redirect . '&action=dashboard');
        exit;
    }

    public function deconnexion() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: index.php');
        exit;
    }
    private function redirectError($msg) {
        header('Location: index.php?controller=auth&action=connexion&error=1&msg=' . urlencode($msg));
        exit;
    }
}
?>