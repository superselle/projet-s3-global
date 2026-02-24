<?php
require_once 'controller/ControleurBase.php';
require_once 'model/Etudiant.php';
require_once 'model/Sondage.php';

class ControleurEtudiant extends ControleurBase {

    /**
     * Page d'accueil de l'étudiant - Dashboard
     */
    public function dashboard() {
        $etudiant = $this->getEtudiantConnecte();
        
        $this->render('view/commun/dashboard.php', [
            'pageTitle' => 'Tableau de bord',
            'currentPage' => 'dashboard',
            'etudiant' => $etudiant
        ]);
    }

    /**
     * Page : Mes résultats
     */
    public function notes() {
        // 1. Récupération de l'étudiant via la méthode helper privée
        $etudiant = $this->getEtudiantConnecte();
        
        // 2. Récupération des notes
        $notes = Etudiant::getNotesAvecStats($etudiant->get('id_etudiant'));
        
        // 3. Affichage avec le CHEMIN COMPLET pour ton ControleurBase
        $this->render('view/etudiant/notes.php', [
            'pageTitle' => 'Mes résultats',
            'currentPage' => 'notes',
            'etudiant' => $etudiant,
            'notes' => $notes
        ]);
    }
    
    /**
     * Page : Liste des sondages
     */
    public function sondages() {
        $this->requireLogin(['etudiant']);
        
        $sondages = Sondage::getAll(); 
        
        $this->render('view/etudiant/sondages.php', [
            'pageTitle' => 'Sondages',
            'currentPage' => 'sondages',
            'sondages' => $sondages
        ]);
    }

    /**
     * Page : Répondre à un sondage
     */
    public function repondreSondage() {
        $etudiant = $this->getEtudiantConnecte();
        $idSondage = $_GET['id'] ?? 0;

        $sondage = Sondage::getById($idSondage);
        if (!$sondage) {
            header('Location: index.php?controller=etudiant&action=sondages');
            exit;
        }

        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ids = $_POST['reponse'] ?? [];
            if (!is_array($ids)) $ids = [$ids];
            
            if (Sondage::sauverReponses($idSondage, $etudiant->get('id_etudiant'), $ids)) {
                $message = "Réponse enregistrée avec succès !";
            } else {
                $message = "Erreur lors de l'enregistrement.";
            }
        }

        $questions = Sondage::getChoix($idSondage);
        $mesReponses = Sondage::getReponsesEtudiant($idSondage, $etudiant->get('id_etudiant'));

        $this->render('view/etudiant/repondreSondage.php', [
            'pageTitle' => 'Répondre au sondage',
            'sondage' => $sondage,
            'questions' => $questions,
            'mesReponses' => $mesReponses,
            'message' => $message
        ]);
    }

    /**
     * Page : Choix du binôme (jusqu'à 3 étudiants pour covoiturage)
     */
    public function binome() {
        $etudiant = $this->getEtudiantConnecte();
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les choix (jusqu'à 3)
            $idsBinomes = [];
            for ($i = 1; $i <= 3; $i++) {
                $key = 'id_binome_' . $i;
                if (isset($_POST[$key]) && !empty($_POST[$key])) {
                    $idBinome = intval($_POST[$key]);
                    // Vérifier que ce n'est pas soi-même et pas de doublon
                    if ($idBinome != $etudiant->get('id_etudiant') && !in_array($idBinome, $idsBinomes)) {
                        $idsBinomes[] = $idBinome;
                    }
                }
            }
            
            // Sauvegarder les choix
            Etudiant::updateBinomes($etudiant->get('id_etudiant'), $idsBinomes);
            
            // Redirection pour éviter resoumission
            header('Location: index.php?controller=etudiant&action=binome');
            exit;
        }

        $etatBinome = Etudiant::getEtatBinome($etudiant->get('id_etudiant'));
        $camarades = Etudiant::getCamarades($etudiant->get('id_parcours'));

        $this->render('view/etudiant/binome.php', [
            'pageTitle' => 'Choix du covoiturage',
            'currentPage' => 'binome',
            'etudiant' => $etudiant,
            'etatBinome' => $etatBinome,
            'camarades' => $camarades
        ]);
    }

    /**
     * Helper privé pour récupérer l'étudiant connecté
     */
    private function getEtudiantConnecte() {
        // Ta fonction requireLogin renvoie les sessionVars, on peut s'en servir si besoin
        $sessionVars = $this->requireLogin(['etudiant']);
        
        // On utilise l'ID utilisateur récupéré par ta méthode requireLogin ou directement $_SESSION
        $userId = $sessionVars['userId'] ?? $_SESSION['user_id'];
        
        $etudiant = Etudiant::getByIdUtilisateur($userId);
        
        if (!$etudiant) {
            header('Location: index.php?controller=auth&action=connexion&error=1&msg=Fiche+etudiant+introuvable');
            exit;
        }
        return $etudiant;
    }
}
?>