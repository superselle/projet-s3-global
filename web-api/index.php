<?php
// Point d'entrée unique (Routeur)
session_start();
require_once 'config/paths.php';
require_once 'config/connexion.php';

// Connexion BDD immédiate (Slide 32)
Connexion::connect();

// Routage simple
$ctrl = isset($_GET['controller']) ? ucfirst($_GET['controller']) : 'Auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'connexion';

// Construction du nom de classe (ex: ControleurAuth)
$ctrlClass = 'Controleur' . $ctrl;
$ctrlFile = 'controller/' . $ctrlClass . '.php';

// Inclusion et appel dynamique
if (file_exists($ctrlFile)) {
    require_once $ctrlFile;
    if (class_exists($ctrlClass)) {
        $objet = new $ctrlClass();
        if (method_exists($objet, $action)) {
            $objet->$action();
            exit;
        }
    }
}

// Fallback si route introuvable
header('Location: index.php?controller=auth&action=connexion');
?>