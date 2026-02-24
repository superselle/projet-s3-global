<?php
require_once 'controller/ControleurBase.php';
require_once 'model/Promotion.php';
require_once 'model/Etudiant.php';
require_once 'model/Groupe.php';

class ControleurPromotions extends ControleurBase {

    public function promotions() {
        $this->requireLogin(['enseignant', 'responsable_filiere', 'responsable_formation']);
        $promotions = Promotion::getAll();
        $this->render('view/promotions/promotions.php', [
            'pageTitle' => 'Consulter promotions',
            'currentPage' => 'promotions',
            'promotions' => $promotions,
            'titre' => 'Annuaire des Promotions',
            'soustitre' => 'Sélectionnez une promotion pour consulter les détails.'
        ]);
    }

    // 2. Détail d'une promo (Liste des groupes OU Liste des étudiants d'un groupe)
    public function detailPromotion() {
        $session = $this->requireLogin(['enseignant', 'responsable_filiere', 'responsable_formation', 'etudiant']);
        $userRole = $session['userRole'];

        $idPromo = $_GET['id'] ?? '';
        if (!$idPromo) $this->redirectBack();

        $promotion = Promotion::getById($idPromo);
        if (!$promotion) $this->redirectBack();

        // Vérification de la publication des groupes
        $groupesPublies = Promotion::areGroupesPublies($promotion->get('id'));

        // Protection Étudiant : Si non publié, on bloque
        if ($userRole === 'etudiant' && !$groupesPublies) {
             echo "<div class='content-wrapper' style='padding:20px;'><div class='alert alert-info'>La constitution des groupes est en cours. Ils seront bientôt visibles.</div></div>";
             return;
        }

        // Récupération de tous les groupes
        $groupes = Groupe::getByPromotion($promotion->get('id'));

        // --- GESTION DU CLIC SUR UN GROUPE (Affichage des étudiants) ---
        $idGroupe = isset($_GET['groupe']) ? (int)$_GET['groupe'] : 0;
        
        if ($idGroupe > 0) {
            $groupe = Groupe::getById($idGroupe);
            
            // Sécurité : On vérifie que le groupe existe et appartient à la promo
            // IMPORTANT : Utilisation de ->get() car attributs privés
            if ($groupe && 
               ($groupe->get('annee_scolaire') == $promotion->get('annee_scolaire') && 
                $groupe->get('id_parcours') == $promotion->get('id_parcours'))) {

                // Récupération propre des étudiants via le modèle Groupe (optimisation)
                // Si cette méthode n'existe pas dans ton modèle Groupe, on garde ta logique de filtre :
                $etudiants = Etudiant::getListePedagogiqueByGroupe($idGroupe);
                // Si getListePedagogiqueByGroupe renvoie des tableaux, c'est bon pour la vue. 
                // Si tu veux des objets :
                // $tous = Etudiant::getAllByPromo($promotion->get('annee_scolaire'), $promotion->get('id_parcours'));
                // $etudiants = array_filter($tous, fn($e) => $e->get('id_groupe') == $idGroupe);
                
                $this->render('view/promotions/detailPromotion.php', [
                    'pageTitle' => "Groupe " . $groupe->get('nom_groupe'),
                    'promotion' => $promotion,
                    'groupe' => $groupe,
                    'etudiants' => $etudiants,
                    'groupes' => $groupes // Pour le menu latéral ou navigation
                ]);
                return;
            }
        }
        
        // --- PAR DÉFAUT : Liste des groupes ---
        $this->render('view/promotions/groupesPromotion.php', [
            'pageTitle' => $promotion->getLabel(),
            'promotion' => $promotion,
            'groupes' => $groupes,
            'areGroupesPublies' => $groupesPublies
        ]);
    }

    // 3. Action du bouton "Publier / Dépublier"
    public function setPublicationGroupes() {
        $this->requireLogin(['responsable_filiere', 'responsable_formation']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPromo = $_POST['id'] ?? '';
            $etat = $_POST['published'] ?? 0;
            
            if ($idPromo) {
                Promotion::setPublication($idPromo, $etat);
            }
            
            header('Location: index.php?controller=promotions&action=detailPromotion&id=' . urlencode($idPromo));
            exit;
        }
        $this->redirectBack();
    }

    // 4. Ma Promotion (Raccourci Étudiant)
    public function maPromotion() {
        $session = $this->requireLogin(['etudiant']);
        
        // On récupère l'étudiant connecté
        $etu = Etudiant::getByIdUtilisateur($session['userId']);
        
        if ($etu && $etu->get('id_groupe')) {
            $grp = Groupe::getById($etu->get('id_groupe'));
            if ($grp) {
                // Construction ID composite
                $idPromo = $grp->get('annee_scolaire') . '|' . $grp->get('semestre') . '|' . $grp->get('id_parcours');
                
                // Redirection vers la promotion (sans paramètre groupe pour voir toute la promotion)
                header('Location: index.php?controller=promotions&action=detailPromotion&id=' . urlencode($idPromo));
                exit;
            }
        }
        
        // Si pas de groupe
        echo "<div class='content-wrapper' style='padding:20px;'><div class='alert alert-warning'>Vous n'êtes affecté à aucun groupe pour le moment.</div></div>";
    }

    public function monGroupe() {
        $session = $this->requireLogin(['etudiant']);
        
        // On récupère l'étudiant connecté
        $etu = Etudiant::getByIdUtilisateur($session['userId']);
        
        if ($etu && $etu->get('id_groupe')) {
            $grp = Groupe::getById($etu->get('id_groupe'));
            if ($grp) {
                // Construction ID composite
                $idPromo = $grp->get('annee_scolaire') . '|' . $grp->get('semestre') . '|' . $grp->get('id_parcours');
                $idGroupe = $etu->get('id_groupe');
                
                // Redirection vers le groupe spécifique
                header('Location: index.php?controller=promotions&action=detailPromotion&id=' . urlencode($idPromo) . '&groupe=' . $idGroupe);
                exit;
            }
        }
        
        // Si pas de groupe
        echo "<div class='content-wrapper' style='padding:20px;'><div class='alert alert-warning'>Vous n'êtes affecté à aucun groupe pour le moment.</div></div>";
    }
    
    private function redirectBack() {
        header('Location: index.php?controller=promotions&action=promotions');
        exit;
    }
}
?>