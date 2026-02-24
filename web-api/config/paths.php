<?php
// Configuration simple du chemin de base (niveau "cours")
// Exemple attendu: /WEB/sae-s301-groupes

$scriptPath = '';
if (isset($_SERVER['SCRIPT_NAME'])) {
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
}

if ($scriptPath === '/' || $scriptPath === '\\') {
    $scriptPath = '';
}

define('BASE_URL', $scriptPath);
