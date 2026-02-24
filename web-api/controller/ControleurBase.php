<?php
/**
 * Classe de base pour tous les contrôleurs
 * Fournit des méthodes communes pour initialiser les vues
 */

class ControleurBase {
    
    /**
     * Initialise les variables communes pour les vues
     */
    protected function initViewVars($additionalVars = []) {
        // On vérifie si les fichiers existent avant inclusion pour éviter les erreurs fatales
        if (file_exists('config/paths.php')) require_once 'config/paths.php';
        if (file_exists('config/session_helper.php')) require_once 'config/session_helper.php';
        
        // Si la fonction n'existe pas (cas où session_helper n'est pas chargé), on simule
        if (function_exists('initSessionVars')) {
            $sessionVars = initSessionVars();
        } else {
            // Fallback au cas où
            if (session_status() === PHP_SESSION_NONE) session_start();
            $sessionVars = [
                'isConnected' => $_SESSION['is_connected'] ?? false,
                'userRole' => $_SESSION['role'] ?? '', // Compatible avec ton nouveau système
                'userId' => $_SESSION['user_id'] ?? null,
                'userEmail' => $_SESSION['user_email'] ?? '', // Attention aux noms de clés session
                'userFirstName' => $_SESSION['user_prenom'] ?? '',
                'userLastName' => $_SESSION['user_nom'] ?? '',
                'userName' => ($_SESSION['user_prenom'] ?? '') . ' ' . ($_SESSION['user_nom'] ?? ''),
                'userRoleLabel' => $_SESSION['role'] ?? '',
            ];
        }
        
        // Variables de base
        $vars = [
            'isConnected' => $sessionVars['isConnected'],
            'userRole' => $sessionVars['userRole'],
            'userId' => $sessionVars['userId'],
            'userEmail' => $sessionVars['userEmail'],
            'userFirstName' => $sessionVars['userFirstName'],
            'userLastName' => $sessionVars['userLastName'],
            'userName' => $sessionVars['userName'],
            'userRoleLabel' => $sessionVars['userRoleLabel'],
            'showSidebar' => true,
            'baseUrl' => defined('BASE_URL') ? BASE_URL : 'index.php', // Sécurité si BASE_URL manquant
        ];
        
        return array_merge($vars, $additionalVars);
    }
    
    /**
     * Affiche une vue de manière intelligente
     * * @param string $viewPath Chemin (ex: 'etudiant/notes' OU 'view/etudiant/notes.php')
     * @param array $vars Données à passer à la vue
     */
    protected function render($viewPath, $vars = []) {
        $allVars = $this->initViewVars($vars);
        extract($allVars);
        
        // 1. Nettoyage et construction du chemin
        // Si on n'a pas mis 'view/' au début, on l'ajoute
        if (strpos($viewPath, 'view/') !== 0) {
            $viewPath = 'view/' . $viewPath;
        }
        
        // Si on n'a pas mis '.php' à la fin, on l'ajoute
        if (substr($viewPath, -4) !== '.php') {
            $viewPath .= '.php';
        }

        // 2. Inclusion sécurisée
        if (file_exists($viewPath)) {
            require $viewPath; // require simple permet de réutiliser des vues si besoin
        } else {
            // Message d'erreur clair pour le développeur
            die("Erreur ControleurBase : Impossible de trouver la vue <strong>$viewPath</strong>");
        }
    }

    /**
     * Vérifie connexion et rôles
     */
    protected function requireLogin($allowedRoles = []) {
        if (file_exists('config/paths.php')) require_once 'config/paths.php';
        if (file_exists('config/session_helper.php')) require_once 'config/session_helper.php';
        
        // Utilisation de la session standard si le helper échoue ou pour uniformiser
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $isConnected = $_SESSION['is_connected'] ?? false;
        // On récupère le rôle via la clé standardisée 'role' (celle définie dans ControleurAuth)
        $userRole = $_SESSION['role'] ?? $_SESSION['user_role'] ?? ''; 

        if (!$isConnected) {
            header('Location: index.php?controller=auth&action=connexion');
            exit;
        }

        if (!empty($allowedRoles)) {
            // Conversion en tableau si string unique
            if (!is_array($allowedRoles)) $allowedRoles = [$allowedRoles];
            
            if (!in_array($userRole, $allowedRoles)) {
                // Redirection accès interdit
                 header('Location: index.php?controller=auth&action=connexion&error=1&msg=Acces+interdit');
                exit;
            }
        }

        // On retourne les infos utiles (compatible avec ton ancien code)
        return [
            'isConnected' => $isConnected,
            'userRole' => $userRole,
            'userId' => $_SESSION['user_id'] ?? null
        ];
    }
}
?>