<?php

require_once 'controller/ControleurBase.php';
require_once 'model/Sondage.php';
require_once 'model/Reponse.php';
require_once 'model/Promotion.php';

class ControleurSondage extends ControleurBase
{
    private function requireSondageManager()
    {
        // Correction suite audit des droits d'accès
        // Seuls les responsables (filière/formation) peuvent gérer les sondages
        // Les enseignants simples ont accès en consultation uniquement (promotions)
        return $this->requireLogin(['responsable_filiere', 'responsable_formation']);
    }

    public function index()
    {
        $session = $this->requireSondageManager();

        $sondages = Sondage::getAll();

        $this->render('view/commun/sondagesGestion.php', [
            'pageTitle' => 'Gestion des sondages',
            'currentPage' => 'sondagesGestion',
            'sondages' => $sondages,
            'success' => isset($_GET['success']) ? $_GET['success'] : null,
            'error' => isset($_GET['error']) ? $_GET['error'] : null,
        ]);
    }

    public function creer()
    {
        $this->requireSondageManager();

        $promotions = Promotion::getAll();

        $this->render('view/commun/sondageCreer.php', [
            'pageTitle' => 'Créer un sondage',
            'currentPage' => 'sondagesGestion',
            'promotions' => $promotions,
            'error' => isset($_GET['error']) ? $_GET['error'] : null,
        ]);
    }

    public function enregistrer()
    {
        $this->requireSondageManager();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?controller=sondage&action=index');
            exit;
        }

        $nom = isset($_POST['nom_sondage']) ? trim($_POST['nom_sondage']) : '';
        $contenu = isset($_POST['contenu_sondage']) ? trim($_POST['contenu_sondage']) : '';
        $reponsesRaw = isset($_POST['reponses']) ? $_POST['reponses'] : '';
        $promoKey = isset($_POST['promotion_id']) ? $_POST['promotion_id'] : '';
        $mode = isset($_POST['mode_sondage']) ? $_POST['mode_sondage'] : 'unique';

        // Décodage de la clé composite (Annee|Semestre|Parcours)
        $parts = explode('|', $promoKey);
        $decodedPromo = null;
        if (count($parts) === 3) {
            $decodedPromo = [
                'annee_scolaire' => $parts[0],
                'semestre' => $parts[1],
                'id_parcours' => $parts[2]
            ];
        }

        if ($nom === '' || $reponsesRaw === '' || !$decodedPromo) {
            header('Location: ' . BASE_URL . '/index.php?controller=sondage&action=creer&error=missing');
            exit;
        }

        $lines = preg_split('/\R/', $reponsesRaw);
        $reponses = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $reponses[] = $line;
            }
        }
        $reponses = array_values(array_unique($reponses));
        
        if (count($reponses) < 2) {
            header('Location: ' . BASE_URL . '/index.php?controller=sondage&action=creer&error=reponses');
            exit;
        }

        try {
            $idSondage = Sondage::create(
                $nom,
                $contenu,
                $decodedPromo['annee_scolaire'],
                $decodedPromo['semestre'],
                $decodedPromo['id_parcours'],
                $mode
            );
            
            foreach ($reponses as $r) {
                Reponse::create($idSondage, $r);
            }
        } catch (Exception $e) { // Changé Throwable en Exception pour compatibilité
            header('Location: ' . BASE_URL . '/index.php?controller=sondage&action=creer&error=db');
            exit;
        }

        header('Location: ' . BASE_URL . '/index.php?controller=sondage&action=index&success=created');
        exit;
    }

    public function resultats()
    {
        $this->requireSondageManager();
        
        // Si tu n'as pas le fichier RepondreSondage.php, cette partie plantera.
        // Assure-toi d'avoir ce fichier modèle pour voir les résultats.
        if (file_exists('model/RepondreSondage.php')) {
            require_once 'model/RepondreSondage.php';
        } else {
             die("Erreur : Le fichier model/RepondreSondage.php est manquant.");
        }

        $idSondage = isset($_GET['id']) ? $_GET['id'] : 0;
        $sondage = $idSondage > 0 ? Sondage::getById($idSondage) : null;
        if (!$sondage) {
            header('Location: ' . BASE_URL . '/index.php?controller=sondage&action=index&error=notfound');
            exit;
        }

        $stats = RepondreSondage::getStatsBySondage($idSondage);
        $lignes = RepondreSondage::getReponsesEtudiantsBySondage($idSondage);

        $this->render('view/commun/sondageResultats.php', [
            'pageTitle' => 'Résultats sondage',
            'currentPage' => 'sondagesGestion',
            'sondage' => $sondage,
            'stats' => $stats,
            'lignes' => $lignes,
        ]);
    }
}
?>