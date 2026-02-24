<?php
require_once 'controller/ControleurBase.php';
require_once 'model/Utilisateur.php';

class ControleurProfil extends ControleurBase {

    /**
     * Affiche les informations personnelles de l'utilisateur connecté
     * La modification a été désactivée.
     */
    public function infos() {
        // 1. Vérification connexion
        $sessionVars = $this->requireLogin([]); 
        
        // 2. Récupération de l'utilisateur depuis la BDD
        $userId = $sessionVars['userId'] ?? $_SESSION['user_id'];
        $utilisateur = Utilisateur::getById($userId);

        if (!$utilisateur) {
            header('Location: index.php?controller=auth&action=deconnexion');
            exit;
        }

        // 3. Affichage via render() en mode lecture seule
        $this->render('view/commun/infosPersonnelles.php', [
            'pageTitle' => 'Mes informations',
            'currentPage' => 'infos',
            'utilisateur' => $utilisateur
        ]);
    }

}
?>