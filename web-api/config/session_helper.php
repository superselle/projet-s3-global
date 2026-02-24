<?php
/**
 * Helper pour gérer les variables de session dans les vues
 * Récupère automatiquement les informations de l'utilisateur connecté
 */

// Fonction pour initialiser les variables de session dans les vues
function initSessionVars() {
    // Récupération simple (niveau "cours")
    $isConnected = isset($_SESSION['is_connected']) && $_SESSION['is_connected'];
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
    $userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
    $userFirst = isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : '';
    $userLast = isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : '';

    $userName = trim($userFirst . ' ' . $userLast);
    $userRoleLabel = $userRole;
    
    return [
        'isConnected' => $isConnected,
        'userRole' => $userRole,
        'userId' => $userId,
        'userEmail' => $userEmail,
        'userFirstName' => $userFirst,
        'userLastName' => $userLast,
        'userName' => $userName,
        'userRoleLabel' => $userRoleLabel,
    ];
}
