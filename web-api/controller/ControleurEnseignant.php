<?php

require_once 'controller/ControleurBase.php';
require_once 'model/Enseignant.php';

class ControleurEnseignant extends ControleurBase {
    
    /**
     * Page d'accueil de l'enseignant
     */
    public function dashboard() {
        $session = $this->requireLogin(['enseignant']);
        
        // Récupérer l'enseignant lié
        $enseignant = Enseignant::getByIdUtilisateur($session['userId']);
        
        $this->render('view/commun/dashboard.php', [
            'pageTitle' => 'Tableau de bord',
            'currentPage' => 'dashboard',
            'enseignant' => $enseignant
        ]);
    }
    
    public function promotions() {
        $this->requireLogin(['enseignant', 'responsable_filiere', 'responsable_formation']);
        header('Location: ' . BASE_URL . '?controller=promotions&action=promotions');
        exit;
    }
    
    public function detailPromotion() {
        $this->requireLogin(['enseignant', 'responsable_filiere', 'responsable_formation']);

        $idPromotion = isset($_GET['id']) ? $_GET['id'] : '';
        if ($idPromotion === '') {
            header('Location: ' . BASE_URL . '?controller=promotions&action=promotions');
            exit;
        }

        $idGroupe = isset($_GET['groupe']) ? $_GET['groupe'] : 0;

        $url = BASE_URL . '?controller=promotions&action=detailPromotion&id=' . urlencode($idPromotion);
        if ($idGroupe > 0) {
            $url .= '&groupe=' . $idGroupe;
        }

        header('Location: ' . $url);
        exit;
    }
}

